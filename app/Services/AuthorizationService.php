<?php

namespace App\Services;

use App\Exceptions\Authorization\CannotBanSelfException;
use App\Exceptions\Authorization\ProtectedSystemRoleException;
use App\Models\User;
use App\Repositories\AuthorizationRepository;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AuthorizationService
{
    private const SYSTEM_ROLES = ['admin', 'student'];

    public function __construct(
        private readonly AuthorizationRepository $authorizationRepository
    ) {}

    public function roles(): Collection
    {
        return $this->authorizationRepository->allRoles();
    }

    public function createRole(array $data): Role
    {
        return $this->authorizationRepository->createRole($data['name']);
    }

    public function updateRole(Role $role, array $data): Role
    {
        if ($this->isSystemRole($role) && $data['name'] !== $role->name) {
            throw new ProtectedSystemRoleException('renamed');
        }

        return $this->authorizationRepository->updateRole($role, $data['name']);
    }

    public function deleteRole(Role $role): void
    {
        if ($this->isSystemRole($role)) {
            throw new ProtectedSystemRoleException('deleted');
        }

        $this->authorizationRepository->deleteRole($role);
    }

    public function permissions(): Collection
    {
        return $this->authorizationRepository->allPermissions();
    }

    public function createPermission(array $data): Permission
    {
        return $this->authorizationRepository->createPermission($data['name']);
    }

    public function updatePermission(Permission $permission, array $data): Permission
    {
        return $this->authorizationRepository->updatePermission($permission, $data['name']);
    }

    public function deletePermission(Permission $permission): void
    {
        $this->authorizationRepository->deletePermission($permission);
    }

    public function syncRolePermissions(Role $role, array $permissionIds): Role
    {
        return $this->authorizationRepository->syncRolePermissions($role, $permissionIds);
    }

    public function syncUserRoles(User $user, array $roleIds): User
    {
        return $this->authorizationRepository->syncUserRoles($user, $roleIds);
    }

    public function banUser(User $administrator, User $user, string $reason): User
    {
        if ($administrator->is($user)) {
            throw new CannotBanSelfException;
        }

        return $this->authorizationRepository->banUser($user, $reason);
    }

    public function unbanUser(User $user): User
    {
        return $this->authorizationRepository->unbanUser($user);
    }

    private function isSystemRole(Role $role): bool
    {
        return in_array($role->name, self::SYSTEM_ROLES, true);
    }
}
