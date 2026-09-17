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

    public function updatePassword(User $user, string $password): void
    {
        $user->update(['password' => $password]);
    }

    public function loadPublicProfile(User $user): User
    {
        return $user->loadMissing(['roles', 'targetedProfessionalField']);
    }

    public function revokeAllTokens(User $user): void
    {
        $user->tokens()->delete();
    }
}
