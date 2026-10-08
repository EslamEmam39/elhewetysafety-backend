<?php

namespace App\Http\Controllers\Api\Admin\Video;

use App\Http\Controllers\Controller;
use App\Http\Requests\Video\UploadLessonVideoRequest;
use App\Http\Resources\Course\LessonResource;
use App\Models\Lesson;
use App\Services\Video\VideoService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class LessonVideoController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly VideoService $service) {}

    public function store(UploadLessonVideoRequest $request, Lesson $lesson): JsonResponse
    {
        $this->authorize('update', $lesson);

        $lesson = $this->service->upload(
            $lesson,
            $request->file('video'),
            $request->integer('duration_seconds')
        );

        return $this->successResponse(
            LessonResource::make($lesson),
            'Lesson video uploaded successfully'
        );
    }
}
