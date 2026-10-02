<?php

namespace App\Http\Controllers\Api\Admin\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\AdminOrderIndexRequest;
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

    public function index(AdminOrderIndexRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Order::class);

        return $this->successResponse(
            OrderResource::collection($this->service->adminOrders($request->validated()))
        );
    }

    public function show(Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        return $this->successResponse(OrderResource::make($this->service->adminOrder($order)));
    }
}
