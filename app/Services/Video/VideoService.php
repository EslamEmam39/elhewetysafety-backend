<?php

namespace App\Services\Video;

use App\Exceptions\Enrollment\EnrollmentAccessDeniedException;
use App\Exceptions\Video\VideoNotReadyException;
use App\Exceptions\Video\VideoStorageException;
use App\Models\Lesson;
use App\Models\User;
use App\Repositories\Video\VideoRepository;
use Carbon\CarbonImmutable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class VideoService
{
    public function __construct(private readonly VideoRepository $repository) {}

    public function upload(Lesson $lesson, UploadedFile $video, int $durationSeconds): Lesson
    {
        $lesson = $this->repository->loadLessonContext($lesson);
        $disk = (string) config('video.disk');
        $extension = $video->guessExtension() ?: $video->getClientOriginalExtension() ?: 'bin';
        $directory = "courses/{$lesson->chapter->course->slug}/lessons/{$lesson->code}";
        $filename = Str::uuid().'.'.Str::lower($extension);
        $storage = Storage::disk($disk);
        $storedKey = $storage->putFileAs($directory, $video, $filename);

        if ($storedKey === false) {
            throw new VideoStorageException('The lesson video could not be uploaded.');
        }

        $oldDisk = $lesson->video_disk;
        $oldKey = $lesson->video_storage_key;

        try {
            $updated = $this->repository->updateVideoMetadata($lesson, [
                'video_disk' => $disk,
                'video_storage_key' => $storedKey,
                'video_mime_type' => $video->getMimeType(),
                'video_size_bytes' => $video->getSize(),
                'video_status' => 'ready',
                'duration_seconds' => $durationSeconds,
                'video_hls_manifest_key' => null,
            ]);
        } catch (Throwable $exception) {
            $storage->delete($storedKey);
            throw $exception;
        }

        if ($oldDisk && $oldKey && ($oldDisk !== $disk || $oldKey !== $storedKey)) {
            Storage::disk($oldDisk)->delete($oldKey);
        }

        return $updated;
    }

    public function playback(User $user, Lesson $lesson): array
    {
        $lesson = $this->repository->loadLessonContext($lesson);
        $course = $lesson->chapter->course;

        if (! $this->repository->hasActiveEnrollment($user, $course->id)) {
            throw new EnrollmentAccessDeniedException;
        }

        if ($lesson->video_status !== 'ready' || ! $lesson->video_storage_key) {
            throw new VideoNotReadyException;
        }

        $disk = $lesson->video_disk ?: (string) config('video.disk');
        $storage = Storage::disk($disk);

        if (! $storage->exists($lesson->video_storage_key)) {
            throw new VideoNotReadyException;
        }

        $expiresAt = CarbonImmutable::now()->addMinutes(
            (int) config('video.playback_url_expiration_minutes')
        );

        try {
            $playbackUrl = $storage->temporaryUrl($lesson->video_storage_key, $expiresAt);
        } catch (Throwable $exception) {
            report($exception);
            throw new VideoStorageException;
        }

        return [
            'lesson' => $lesson,
            'playback_url' => $playbackUrl,
            'expires_at' => $expiresAt,
        ];
    }
}
