<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authorization\StorePermissionRequest;
use App\Http\Requests\Authorization\UpdatePermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Services\AuthorizationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly AuthorizationService $authorizationService) {}

    public function index(): JsonResponse
    {
        return $this->successResponse(PermissionResource::collection($this->authorizationService->permissions()));
    }

    public function store(StorePermissionRequest $request): JsonResponse
    {
        $permission = $this->authorizationService->createPermission($request->validated());

        return $this->successResponse(PermissionResource::make($permission), 'Permission created successfully', 201);
    }

    public function show(Permission $permission): JsonResponse
    {
        return $this->successResponse(PermissionResource::make($permission));
    }

    public function update(UpdatePermissionRequest $request, Permission $permission): JsonResponse
    {
        $permission = $this->authorizationService->updatePermission($permission, $request->validated());

        return $this->successResponse(PermissionResource::make($permission), 'Permission updated successfully');
    }

    public function destroy(Permission $permission): JsonResponse
    {
        $this->authorizationService->deletePermission($permission);

        return $this->successResponse(message: 'Permission deleted successfully');
    }
}
