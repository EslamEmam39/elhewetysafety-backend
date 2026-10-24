<?php
namespace App\Http\Controllers\Api\Admin\Course;
use App\Http\Controllers\Controller;
use App\Http\Requests\Course\StoreCourseRequest;
use App\Http\Requests\Course\UploadCourseThumbnailRequest;
use App\Http\Requests\Course\UpdateCourseRequest;
use App\Http\Resources\Course\CourseResource;
use App\Models\Course;
use App\Services\Course\AdminCourseService;
use App\Services\Course\CourseThumbnailService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
class CourseController extends Controller
{
    use ApiResponse;
    public function __construct(
        private readonly AdminCourseService $service,
        private readonly CourseThumbnailService $thumbnailService,
    )
    {
    }
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Course::class);
        return $this->successResponse(CourseResource::collection($this->service->courses()));
    }
    public function store(StoreCourseRequest $request): JsonResponse
    {
        $this->authorize('create', Course::class);
        return $this->successResponse(CourseResource::make($this->service->createCourse($request->validated())), 'Course created successfully', 201);
    }
    public function show(Course $course): JsonResponse
    {
        $this->authorize('view', $course);
        return $this->successResponse(CourseResource::make($this->service->course($course)));
    }
    public function update(UpdateCourseRequest $request, Course $course): JsonResponse
    {
        $this->authorize('update', $course);
        return $this->successResponse(CourseResource::make($this->service->updateCourse($course, $request->validated())), 'Course updated successfully');
    }

    public function uploadThumbnail(UploadCourseThumbnailRequest $request, Course $course): JsonResponse
    {
        $this->authorize('update', $course);

        return $this->successResponse(
            CourseResource::make($this->thumbnailService->upload($course, $request->file('thumbnail'))),
            'Course thumbnail uploaded successfully'
        );
    }
    public function destroy(Course $course): JsonResponse
    {
        $this->authorize('delete', $course);
        $this->service->deleteCourse($course);
        $this->thumbnailService->delete($course);
        return $this->successResponse(message: 'Course deleted successfully');
    }
}
