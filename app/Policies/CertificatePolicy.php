<?php

namespace App\Policies;

use App\Models\Certificate;
use App\Models\User;

class CertificatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('certificates.view');
    }

    public function view(User $user, Certificate $certificate): bool
    {
        return $user->checkPermissionTo('certificates.view');
    }

    public function issue(User $user): bool
    {
        return $user->checkPermissionTo('certificates.issue');
    }
}
