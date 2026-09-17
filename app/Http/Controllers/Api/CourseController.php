<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Services\CourseService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class CourseController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly CourseService $courseService) {}

    public function index(): JsonResponse
    {
        return $this->successResponse(CourseResource::collection($this->courseService->publishedCourses()));
    }

    public function show(string $slug): JsonResponse
    {
        $course = $this->courseService->publishedCourse($slug);

        abort_unless($course, 404);

        return $this->successResponse(CourseResource::make($course));
    }
}
