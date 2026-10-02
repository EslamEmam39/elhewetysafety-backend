<?php
namespace App\Http\Controllers\Api\Admin\Payment;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\AdminPaymentIndexRequest;
use App\Http\Resources\Payment\PaymentResource;
use App\Models\Payment;
use App\Services\Payment\PaymentService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
class PaymentController extends Controller
{
    use ApiResponse;
    public function __construct(private readonly PaymentService $service) {}
    public function index(AdminPaymentIndexRequest $request): JsonResponse { $this->authorize('viewAny',Payment::class); return $this->successResponse(PaymentResource::collection($this->service->adminPayments($request->validated()))); }
    public function show(Payment $payment): JsonResponse { $this->authorize('view',$payment); return $this->successResponse(PaymentResource::make($this->service->adminPayment($payment))); }
}
