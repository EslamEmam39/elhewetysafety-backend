<?php

namespace App\Http\Controllers\Api\LessonProgress;

use App\Http\Controllers\Controller;
use App\Http\Requests\LessonProgress\LessonProgressIndexRequest;
use App\Http\Requests\LessonProgress\UpdateLessonProgressRequest;
use App\Http\Resources\LessonProgress\LessonProgressResource;
use App\Models\Course;
use App\Models\Lesson;
use App\Services\LessonProgress\LessonProgressService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class LessonProgressController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly LessonProgressService $service) {}

    public function index(LessonProgressIndexRequest $request, Course $course): JsonResponse
    {
        return $this->successResponse(
            LessonProgressResource::collection($this->service->forCourse($request->user(), $course))
        );
    }

    public function update(UpdateLessonProgressRequest $request, Lesson $lesson): JsonResponse
    {
        return $this->successResponse(
            LessonProgressResource::make(
                $this->service->update($request->user(), $lesson, $request->validated())
            ),
            'Lesson progress updated successfully'
        );
    }
}
