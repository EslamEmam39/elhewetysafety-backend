<?php
namespace App\Http\Controllers\Api\Payment;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\StorePaymentRequest;
use App\Http\Resources\Payment\PaymentResource;
use App\Models\Order;
use App\Models\Payment;
use App\Services\Payment\PaymentService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
class PaymentController extends Controller
{
    use ApiResponse;
    public function __construct(private readonly PaymentService $service) {}
    public function index(Order $order): JsonResponse { return $this->successResponse(PaymentResource::collection($this->service->attempts(request()->user(),$order))); }
    public function store(StorePaymentRequest $request, Order $order): JsonResponse { return $this->successResponse(PaymentResource::make($this->service->createAttempt($request->user(),$order,$request->validated('gateway_code'))),'Payment attempt created successfully',201); }
    public function show(Payment $payment): JsonResponse { return $this->successResponse(PaymentResource::make($this->service->payment(request()->user(),$payment))); }
}
