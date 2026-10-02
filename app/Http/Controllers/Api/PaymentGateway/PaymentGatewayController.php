<?php

namespace App\Http\Controllers\Api\PaymentGateway;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentGateway\PaymentGatewayResource;
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
        return $this->successResponse(
            PaymentGatewayResource::collection($this->service->activeGateways())
        );
    }
}
