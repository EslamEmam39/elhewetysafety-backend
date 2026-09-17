<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Str;

class UserObserver
{
    public function creating(User $user): void
    {
        $this->assignUuidIfMissing($user);
    }

    public function saving(User $user): void
    {
        $this->assignUuidIfMissing($user);
    }

    private function assignUuidIfMissing(User $user): void
    {
        if (blank($user->getAttribute('uuid'))) {
            $user->setAttribute('uuid', (string) Str::uuid());
        }
    }
}