<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AuthorizationRepository
{
    public function allRoles(): Collection
    {
        return Role::query()->with('permissions')->orderBy('name')->get();
    }

    public function createRole(string $name): Role
    {
        return Role::create(['name' => $name, 'guard_name' => 'web']);
    }

    public function updateRole(Role $role, string $name): Role
    {
        $role->update(['name' => $name]);

        return $role->refresh()->load('permissions');
    }

    public function deleteRole(Role $role): void
    {
        $role->delete();
    }

    public function allPermissions(): Collection
    {
        return Permission::query()->orderBy('name')->get();
    }

    public function createPermission(string $name): Permission
    {
        return Permission::create(['name' => $name, 'guard_name' => 'web']);
    }

    public function updatePermission(Permission $permission, string $name): Permission
    {
        $permission->update(['name' => $name]);

        return $permission->refresh();
    }

    public function deletePermission(Permission $permission): void
    {
        $permission->delete();
    }

    public function syncRolePermissions(Role $role, array $permissionIds): Role
    {
        $permissions = Permission::query()->whereIn('id', $permissionIds)->get();
        $role->syncPermissions($permissions);

        return $role->refresh()->load('permissions');
    }

    public function banUser(User $user, string $reason): User
    {
        $user->update([
            'is_banned' => true,
            'ban_reason' => $reason,
        ]);

        return $user->refresh()->load(['roles', 'targetedProfessionalField']);
    }

    public function unbanUser(User $user): User
    {
        $user->update([
            'is_banned' => false,
            'ban_reason' => null,
        ]);

        return $user->refresh()->load(['roles', 'targetedProfessionalField']);
    }

    public function syncUserRoles(User $user, array $roleIds): User
    {
        $roles = Role::query()->whereIn('id', $roleIds)->get();
        $user->syncRoles($roles);

        return $user->refresh()->load('roles');
    }
}
