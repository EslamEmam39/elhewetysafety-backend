<?php

namespace App\Http\Controllers\Api\Admin\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentGateway\StorePaymentGatewayRequest;
use App\Http\Requests\PaymentGateway\UpdatePaymentGatewayRequest;
use App\Http\Resources\PaymentGateway\PaymentGatewayResource;
use App\Models\PaymentGateway;
use App\Services\PaymentGateway\PaymentGatewayService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class PaymentGatewayController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly PaymentGatewayService $service)
    {
    }

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', PaymentGateway::class);

        return $this->successResponse(
            PaymentGatewayResource::collection($this->service->gateways())
        );
    }

    public function store(StorePaymentGatewayRequest $request): JsonResponse
    {
        $this->authorize('create', PaymentGateway::class);

        return $this->successResponse(
            PaymentGatewayResource::make($this->service->create($request->validated())),
            'Payment gateway created successfully',
            201
        );
    }

    public function show(PaymentGateway $paymentGateway): JsonResponse
    {
        $this->authorize('view', $paymentGateway);

        return $this->successResponse(PaymentGatewayResource::make($paymentGateway));
    }

    public function update(UpdatePaymentGatewayRequest $request, PaymentGateway $paymentGateway): JsonResponse
    {
        $this->authorize('update', $paymentGateway);

        return $this->successResponse(
            PaymentGatewayResource::make($this->service->update($paymentGateway, $request->validated())),
            'Payment gateway updated successfully'
        );
    }

    public function destroy(PaymentGateway $paymentGateway): JsonResponse
    {
        $this->authorize('delete', $paymentGateway);

        return $this->successResponse(
            PaymentGatewayResource::make($this->service->deactivate($paymentGateway)),
            'Payment gateway deactivated successfully'
        );
    }
}
