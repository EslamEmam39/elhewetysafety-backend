<?php

namespace App\Http\Controllers\Api\Admin\Device;

use App\Http\Controllers\Controller;
use App\Http\Requests\Device\DeviceIndexRequest;
use App\Http\Requests\Device\RevokeDeviceRequest;
use App\Http\Resources\Device\DeviceResource;
use App\Models\Device;
use App\Models\User;
use App\Services\Device\DeviceService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class DeviceController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly DeviceService $service) {}

    public function index(DeviceIndexRequest $request, User $user): JsonResponse
    {
        $this->authorize('viewAny', Device::class);

        return $this->successResponse(DeviceResource::collection($this->service->forAdmin($user)));
    }

    public function revoke(RevokeDeviceRequest $request, Device $device): JsonResponse
    {
        $this->authorize('manage', $device);

        return $this->successResponse(
            DeviceResource::make($this->service->revokeAny($device)),
            'Device revoked successfully'
        );
    }
}
