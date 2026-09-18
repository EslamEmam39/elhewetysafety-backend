<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $administrator): bool
    {
        return $administrator->checkPermissionTo('users.view');
    }

    public function view(User $administrator, User $user): bool
    {
        return $administrator->checkPermissionTo('users.view');
    }

    public function update(User $administrator, User $user): bool
    {
        return $administrator->checkPermissionTo('users.update');
    }

    public function ban(User $administrator, User $user): bool
    {
        return $administrator->checkPermissionTo('users.ban');
    }

    public function unban(User $administrator, User $user): bool
    {
        return $administrator->checkPermissionTo('users.ban');
    }

    public function assignRoles(User $administrator, User $user): bool
    {
        return $administrator->checkPermissionTo('users.assign-roles');
    }

    public function viewStudents(User $administrator): bool
    {
        return $administrator->checkPermissionTo('students.view');
    }

    public function viewStudent(User $administrator, User $user): bool
    {
        return $administrator->checkPermissionTo('students.view');
    }

    public function updateStudent(User $administrator, User $user): bool
    {
        return $administrator->checkPermissionTo('students.update');
    }

    public function deleteStudent(User $administrator, User $user): bool
    {
        return $administrator->checkPermissionTo('students.delete');
    }
}
