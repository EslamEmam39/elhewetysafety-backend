<?php
namespace App\Http\Controllers\Api\Admin\Course;
use App\Http\Controllers\Controller;
use App\Http\Requests\Course\StoreCourseCategoryRequest;
use App\Http\Requests\Course\UpdateCourseCategoryRequest;
use App\Http\Resources\Course\CourseCategoryResource;
use App\Models\CourseCategory;
use App\Services\Course\AdminCourseService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
class CourseCategoryController extends Controller
{
    use ApiResponse;
    public function __construct(private readonly AdminCourseService $service)
    {
    }
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', CourseCategory::class);
        return $this->successResponse(CourseCategoryResource::collection($this->service->categories()));
    }
    public function store(StoreCourseCategoryRequest $request): JsonResponse
    {
        $this->authorize('create', CourseCategory::class);
        return $this->successResponse(CourseCategoryResource::make($this->service->createCategory($request->validated())), 'Course category created successfully', 201);
    }
    public function show(CourseCategory $courseCategory): JsonResponse
    {
        $this->authorize('view', $courseCategory);
        return $this->successResponse(CourseCategoryResource::make($courseCategory));
    }
    public function update(UpdateCourseCategoryRequest $request, CourseCategory $courseCategory): JsonResponse
    {
        $this->authorize('update', $courseCategory);
        return $this->successResponse(CourseCategoryResource::make($this->service->updateCategory($courseCategory, $request->validated())), 'Course category updated successfully');
    }
    public function destroy(CourseCategory $courseCategory): JsonResponse
    {
        $this->authorize('delete', $courseCategory);
        return $this->successResponse(CourseCategoryResource::make($this->service->deactivateCategory($courseCategory)), 'Course category deactivated successfully');
    }
}
