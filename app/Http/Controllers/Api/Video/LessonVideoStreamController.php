<?php

namespace App\Http\Controllers\Api\Video;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Services\Video\VideoStreamService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LessonVideoStreamController extends Controller
{
    public function __construct(private readonly VideoStreamService $service) {}

    public function show(Request $request, Lesson $lesson): Response
    {
        $stream = $this->service->prepare($lesson, $request->header('Range'));
        $headers = [
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'private, no-store',
            'Content-Disposition' => 'inline',
            'Content-Length' => (string) $stream['length'],
            'Content-Type' => $stream['mime_type'],
        ];

        if ($stream['status'] === Response::HTTP_PARTIAL_CONTENT) {
            $headers['Content-Range'] = "bytes {$stream['start']}-{$stream['end']}/{$stream['size']}";
        }

        return response()->stream($stream['output'], $stream['status'], $headers);
    }
}
