<?php

namespace App\Http\Controllers\Api\Device;

use App\Http\Controllers\Controller;
use App\Http\Requests\Device\DeviceIndexRequest;
use App\Http\Requests\Device\RevokeDeviceRequest;
use App\Http\Resources\Device\DeviceResource;
use App\Models\Device;
use App\Services\Device\DeviceService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class DeviceController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly DeviceService $service) {}

    public function index(DeviceIndexRequest $request): JsonResponse
    {
        return $this->successResponse(DeviceResource::collection($this->service->forUser($request->user())));
    }

    public function destroy(RevokeDeviceRequest $request, Device $device): JsonResponse
    {
        return $this->successResponse(
            DeviceResource::make($this->service->revokeOwned($request->user(), $device)),
            'Device revoked successfully'
        );
    }
}
