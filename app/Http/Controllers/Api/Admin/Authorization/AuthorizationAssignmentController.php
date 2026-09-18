<?php

namespace App\Http\Controllers\Api\Admin\Authorization;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authorization\SyncRolePermissionsRequest;
use App\Http\Requests\Authorization\SyncUserRolesRequest;
use App\Http\Resources\Authorization\RoleResource;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Services\Authorization\AuthorizationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;

class AuthorizationAssignmentController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly AuthorizationService $authorizationService) {}

    public function syncRolePermissions(SyncRolePermissionsRequest $request, Role $role): JsonResponse
    {
        $this->authorize('syncPermissions', $role);

        $role = $this->authorizationService->syncRolePermissions($role, $request->validated('permission_ids'));

        return $this->successResponse(RoleResource::make($role), 'Role permissions updated successfully');
    }

    public function syncUserRoles(SyncUserRolesRequest $request, User $user): JsonResponse
    {
        $this->authorize('assignRoles', $user);

        $user = $this->authorizationService->syncUserRoles($user, $request->validated('role_ids'));

        return $this->successResponse(UserResource::make($user), 'User roles updated successfully');
    }
}
