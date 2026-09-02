<?php

namespace App\Services;

use App\Exceptions\Auth\InvalidCredentialsException;
use App\Models\User;
use App\Repositories\AuthRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthService
{
    public function __construct(
        private readonly AuthRepository $authRepository
    ) {}

    public function register(array $data): array
    {
        return DB::transaction(function () use ($data): array {
            $user = $this->authRepository->createUser([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $user->assignRole('student');
            $this->authRepository->createStudentProfile($user);

            return $this->createAuthPayload($user);
        });
    }

    public function login(array $credentials): array
    {
        $user = $this->authRepository->findUserByEmail($credentials['email']);

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw new InvalidCredentialsException();
        }

        return $this->createAuthPayload($user);
    }

    public function logout(User $user): void
    {
        $this->authRepository->deleteCurrentToken($user);
    }

    private function createAuthPayload(User $user): array
    {
        return [
            'user' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken,
        ];
    }
}
