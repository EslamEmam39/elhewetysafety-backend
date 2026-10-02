<?php
namespace App\Http\Controllers\Api\Admin\Enrollment;
use App\Http\Controllers\Controller;
use App\Http\Requests\Enrollment\AdminEnrollmentIndexRequest;
use App\Http\Requests\Enrollment\ManageEnrollmentRequest;
use App\Http\Resources\Enrollment\EnrollmentResource;
use App\Models\Enrollment;
use App\Services\Enrollment\EnrollmentService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
class EnrollmentController extends Controller
{
    use ApiResponse;
    public function __construct(private readonly EnrollmentService $service) {}
    public function index(AdminEnrollmentIndexRequest $request): JsonResponse { $this->authorize('viewAny',Enrollment::class); return $this->successResponse(EnrollmentResource::collection($this->service->adminEnrollments($request->validated()))); }
    public function show(Enrollment $enrollment): JsonResponse { $this->authorize('view',$enrollment); return $this->successResponse(EnrollmentResource::make($this->service->adminEnrollment($enrollment))); }
    public function revoke(ManageEnrollmentRequest $request, Enrollment $enrollment): JsonResponse { $this->authorize('manage',$enrollment); return $this->successResponse(EnrollmentResource::make($this->service->revoke($enrollment)),'Enrollment revoked successfully'); }
    public function activate(ManageEnrollmentRequest $request, Enrollment $enrollment): JsonResponse { $this->authorize('manage',$enrollment); return $this->successResponse(EnrollmentResource::make($this->service->activate($enrollment)),'Enrollment activated successfully'); }
}
