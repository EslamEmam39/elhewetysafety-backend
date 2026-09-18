<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissionNames = [
            'users.view',
            'users.update',
            'users.ban',
            'users.assign-roles',
            'students.view',
            'students.update',
            'students.delete',
            'roles.view',
            'roles.manage',
            'roles.assign-permissions',
            'permissions.view',
            'permissions.manage',
            'locations.view',
            'locations.manage',
            'course-categories.view',
            'course-categories.manage',
            'courses.view',
            'courses.manage',
            'chapters.manage',
            'lessons.manage',
        ];

        $permissions = collect($permissionNames)
            ->map(fn (string $name) => Permission::findOrCreate($name, 'web'));

        $admin = Role::findOrCreate('admin', 'web');
        $admin->syncPermissions($permissions);
        Role::findOrCreate('student', 'web');

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
