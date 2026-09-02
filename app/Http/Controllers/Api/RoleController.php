<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authorization\StoreRoleRequest;
use App\Http\Requests\Authorization\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Services\AuthorizationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly AuthorizationService $authorizationService) {}

    public function index(): JsonResponse
    {
        return $this->successResponse(RoleResource::collection($this->authorizationService->roles()));
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = $this->authorizationService->createRole($request->validated());

        return $this->successResponse(RoleResource::make($role), 'Role created successfully', 201);
    }

    public function show(Role $role): JsonResponse
    {
        return $this->successResponse(RoleResource::make($role->load('permissions')));
    }

    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        $role = $this->authorizationService->updateRole($role, $request->validated());

        return $this->successResponse(RoleResource::make($role), 'Role updated successfully');
    }

    public function destroy(Role $role): JsonResponse
    {
        $this->authorizationService->deleteRole($role);

        return $this->successResponse(message: 'Role deleted successfully');
    }
}
