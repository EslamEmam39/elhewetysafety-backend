<?php

namespace App\Repositories;

use App\Models\Student;
use App\Models\User;

class AuthRepository
{
    public function createUser(array $data): User
    {
        return User::create($data);
    }

    public function findUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function createStudentProfile(User $user): Student
    {
        return $user->student()->create();
    }

    public function deleteCurrentToken(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }
}
