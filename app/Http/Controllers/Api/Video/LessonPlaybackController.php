<?php

namespace App\Http\Controllers\Api\Video;

use App\Http\Controllers\Controller;
use App\Http\Resources\Video\LessonPlaybackResource;
use App\Models\Lesson;
use App\Services\Video\VideoService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LessonPlaybackController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly VideoService $service) {}

    public function show(Request $request, Lesson $lesson): JsonResponse
    {
        return $this->successResponse(
            LessonPlaybackResource::make($this->service->playback($request->user(), $lesson)),
            'Lesson playback authorized successfully'
        );
    }
}
