<?php
namespace App\Policies;
use App\Models\Enrollment;
use App\Models\User;
class EnrollmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('enrollments.view');
    }
    public function view(User $user, Enrollment $enrollment): bool
    {
        return $user->checkPermissionTo('enrollments.view');
    }
    public function manage(User $user, Enrollment $enrollment): bool
    {
        return $user->checkPermissionTo('enrollments.manage');
    }
}
