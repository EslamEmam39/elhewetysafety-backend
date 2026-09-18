<?php

namespace App\Http\Controllers\Api\Admin\Authorization;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authorization\StoreRoleRequest;
use App\Http\Requests\Authorization\UpdateRoleRequest;
use App\Http\Resources\Authorization\RoleResource;
use App\Services\Authorization\AuthorizationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly AuthorizationService $authorizationService) {}

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Role::class);

        return $this->successResponse(RoleResource::collection($this->authorizationService->roles()));
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $this->authorize('create', Role::class);

        $role = $this->authorizationService->createRole($request->validated());

        return $this->successResponse(RoleResource::make($role), 'Role created successfully', 201);
    }

    public function show(Role $role): JsonResponse
    {
        $this->authorize('view', $role);

        return $this->successResponse(RoleResource::make($role->load('permissions')));
    }

    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        $this->authorize('update', $role);

        $role = $this->authorizationService->updateRole($role, $request->validated());

        return $this->successResponse(RoleResource::make($role), 'Role updated successfully');
    }

    public function destroy(Role $role): JsonResponse
    {
        $this->authorize('delete', $role);

        $this->authorizationService->deleteRole($role);

        return $this->successResponse(message: 'Role deleted successfully');
    }
}
