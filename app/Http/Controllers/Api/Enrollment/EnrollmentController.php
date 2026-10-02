<?php
namespace App\Http\Controllers\Api\Enrollment;
use App\Http\Controllers\Controller;
use App\Http\Requests\Enrollment\EnrollCourseRequest;
use App\Http\Requests\Enrollment\EnrollmentIndexRequest;
use App\Http\Resources\Course\CourseResource;
use App\Http\Resources\Enrollment\EnrollmentResource;
use App\Models\Course;
use App\Models\Enrollment;
use App\Services\Enrollment\EnrollmentService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
class EnrollmentController extends Controller
{
    use ApiResponse;
    public function __construct(private readonly EnrollmentService $service) {}
    public function index(EnrollmentIndexRequest $request): JsonResponse { return $this->successResponse(EnrollmentResource::collection($this->service->forUser($request->user(),$request->integer('per_page',15)))); }
    public function store(EnrollCourseRequest $request, Course $course): JsonResponse { return $this->successResponse(EnrollmentResource::make($this->service->enrollFree($request->user(),$course)),'Enrollment completed successfully',201); }
    public function show(Enrollment $enrollment): JsonResponse { return $this->successResponse(EnrollmentResource::make($this->service->enrollment(request()->user(),$enrollment))); }
    public function content(Course $course): JsonResponse { return $this->successResponse(CourseResource::make($this->service->content(request()->user(),$course))); }
}
