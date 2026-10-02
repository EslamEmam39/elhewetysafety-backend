<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderIndexRequest;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Resources\Order\OrderResource;
use App\Models\Order;
use App\Services\Order\OrderService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly OrderService $service)
    {
    }

    public function index(OrderIndexRequest $request): JsonResponse
    {
        return $this->successResponse(
            OrderResource::collection($this->service->userOrders($request->user(), $request->validated()))
        );
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        return $this->successResponse(
            OrderResource::make($this->service->create($request->user(), $request->validated('course_slug'))),
            'Order created successfully',
            201
        );
    }

    public function show(Order $order): JsonResponse
    {
        return $this->successResponse(
            OrderResource::make($this->service->userOrder(request()->user(), $order))
        );
    }

    public function destroy(Order $order): JsonResponse
    {
        return $this->successResponse(
            OrderResource::make($this->service->cancel(request()->user(), $order)),
            'Order cancelled successfully'
        );
    }
}
