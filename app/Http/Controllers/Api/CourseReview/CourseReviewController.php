<?php
namespace App\Http\Controllers\Api\CourseReview;
use App\Http\Controllers\Controller;
use App\Http\Requests\CourseReview\CourseReviewIndexRequest;
use App\Http\Requests\CourseReview\MyCourseReviewRequest;
use App\Http\Requests\CourseReview\StoreCourseReviewRequest;
use App\Http\Requests\CourseReview\UpdateCourseReviewRequest;
use App\Http\Resources\CourseReview\CourseReviewResource;
use App\Models\Course;
use App\Services\CourseReview\CourseReviewService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
class CourseReviewController extends Controller
{
    use ApiResponse;
    public function __construct(private readonly CourseReviewService $service) {}
    public function index(CourseReviewIndexRequest $request, Course $course): JsonResponse { return $this->successResponse(CourseReviewResource::collection($this->service->publicReviews($course,$request->integer('per_page',15)))); }
    public function mine(MyCourseReviewRequest $request, Course $course): JsonResponse { return $this->successResponse(CourseReviewResource::make($this->service->myReview($request->user(),$course))); }
    public function store(StoreCourseReviewRequest $request, Course $course): JsonResponse { return $this->successResponse(CourseReviewResource::make($this->service->create($request->user(),$course,$request->validated())),'Course review created successfully',201); }
    public function update(UpdateCourseReviewRequest $request, Course $course): JsonResponse { return $this->successResponse(CourseReviewResource::make($this->service->update($request->user(),$course,$request->validated())),'Course review updated successfully'); }
    public function destroy(MyCourseReviewRequest $request, Course $course): JsonResponse { $this->service->delete($request->user(),$course); return $this->successResponse(message:'Course review deleted successfully'); }
}
