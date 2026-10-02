<?php
namespace App\Http\Controllers\Api\Admin\CourseReview;
use App\Http\Controllers\Controller;
use App\Http\Requests\CourseReview\AdminCourseReviewIndexRequest;
use App\Http\Requests\CourseReview\MyCourseReviewRequest;
use App\Http\Resources\CourseReview\CourseReviewResource;
use App\Models\CourseReview;
use App\Services\CourseReview\CourseReviewService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
class CourseReviewController extends Controller
{
    use ApiResponse;
    public function __construct(private readonly CourseReviewService $service) {}
    public function index(AdminCourseReviewIndexRequest $request): JsonResponse { $this->authorize('viewAny',CourseReview::class); return $this->successResponse(CourseReviewResource::collection($this->service->adminReviews($request->validated()))); }
    public function show(CourseReview $courseReview): JsonResponse { $this->authorize('view',$courseReview); return $this->successResponse(CourseReviewResource::make($this->service->adminReview($courseReview))); }
    public function hide(MyCourseReviewRequest $request, CourseReview $courseReview): JsonResponse { $this->authorize('manage',$courseReview); return $this->successResponse(CourseReviewResource::make($this->service->hide($courseReview)),'Course review hidden successfully'); }
    public function publish(MyCourseReviewRequest $request, CourseReview $courseReview): JsonResponse { $this->authorize('manage',$courseReview); return $this->successResponse(CourseReviewResource::make($this->service->show($courseReview)),'Course review published successfully'); }
}
