<?php

namespace App\Services\Video;

use App\Exceptions\Video\VideoNotReadyException;
use App\Models\Lesson;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class VideoStreamService
{
    /** @return array{start: int, end: int, size: int, length: int, status: int, mime_type: string, output: callable(): void} */
    public function prepare(Lesson $lesson, ?string $range): array
    {
        $disk = $lesson->video_disk ?: (string) config('video.disk');
        $key = $lesson->video_storage_key;
        $storage = Storage::disk($disk);

        if ($lesson->video_status !== 'ready' || ! $key || ! $storage->exists($key)) {
            throw new VideoNotReadyException;
        }

        $size = $storage->size($key);
        [$start, $end, $status] = $this->resolveRange($range, $size);
        $length = $end - $start + 1;

        return [
            'start' => $start,
            'end' => $end,
            'size' => $size,
            'length' => $length,
            'status' => $status,
            'mime_type' => $lesson->video_mime_type ?: 'application/octet-stream',
            'output' => function () use ($storage, $key, $start, $length): void {
                $stream = $storage->readStream($key);
                if ($stream === false) {
                    return;
                }

                try {
                    fseek($stream, $start);
                    $remaining = $length;
                    while ($remaining > 0 && ! feof($stream)) {
                        $chunk = fread($stream, min(8192, $remaining));
                        if ($chunk === false || $chunk === '') {
                            break;
                        }
                        echo $chunk;
                        $remaining -= strlen($chunk);
                    }
                } finally {
                    fclose($stream);
                }
            },
        ];
    }

    /** @return array{int, int, int} */
    private function resolveRange(?string $range, int $size): array
    {
        if (! $range || ! preg_match('/^bytes=(\d*)-(\d*)$/', trim($range), $matches)) {
            return [0, $size - 1, Response::HTTP_OK];
        }

        if ($matches[1] === '' && $matches[2] !== '') {
            $suffixLength = min((int) $matches[2], $size);
            return [$size - $suffixLength, $size - 1, Response::HTTP_PARTIAL_CONTENT];
        }

        $start = (int) $matches[1];
        $end = $matches[2] === '' ? $size - 1 : min((int) $matches[2], $size - 1);

        if ($start >= $size || $start > $end) {
            abort(Response::HTTP_REQUESTED_RANGE_NOT_SATISFIABLE, '', ['Content-Range' => "bytes */{$size}"]);
        }

        return [$start, $end, Response::HTTP_PARTIAL_CONTENT];
    }
}
