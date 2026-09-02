<?php

namespace App\Services;

use App\Exceptions\Auth\EmailAlreadyVerifiedException;
use App\Exceptions\Auth\InvalidCredentialsException;
use App\Exceptions\Otp\InvalidOtpException;
use App\Models\OtpCode;
use App\Models\User;
use App\Repositories\AuthRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(
        private readonly AuthRepository $authRepository,
        private readonly OtpService $otpService
    ) {}

    public function register(array $data): array
    {
        $auth = DB::transaction(function () use ($data): array {
            $user = $this->authRepository->createUser([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $user->assignRole('student');
            $this->authRepository->createStudentProfile($user);

            return $this->createAuthPayload($user);
        });

        $this->otpService->sendEmailVerificationOtp(
            $auth['user']->email,
            enforceCooldown: false
        );

        return $auth;
    }

    public function login(array $credentials): array
    {
        $user = $this->authRepository->findUserByEmail($credentials['email']);

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw new InvalidCredentialsException;
        }

        return $this->createAuthPayload($user);
    }

    public function logout(User $user): void
    {
        $this->authRepository->deleteCurrentToken($user);
    }

    public function resendEmailVerificationOtp(User $user): void
    {
        if ($user->hasVerifiedEmail()) {
            throw new EmailAlreadyVerifiedException;
        }

        $this->otpService->sendEmailVerificationOtp($user->email);
    }

    public function verifyEmail(User $user, string $otp): void
    {
        if ($user->hasVerifiedEmail()) {
            throw new EmailAlreadyVerifiedException;
        }

        $this->otpService->verify(
            $user->email,
            OtpCode::PURPOSE_EMAIL_VERIFICATION,
            $otp
        );

        $user->markEmailAsVerified();
    }

    public function sendPasswordResetOtp(string $email): void
    {
        $user = $this->authRepository->findUserByEmail($email);

        if ($user) {
            $this->otpService->sendPasswordResetOtp($user->email);
        }
    }

    public function resetPassword(array $data): void
    {
        $user = $this->authRepository->findUserByEmail($data['email']);

        if (! $user) {
            throw new InvalidOtpException;
        }

        $this->otpService->verify(
            $user->email,
            OtpCode::PURPOSE_PASSWORD_RESET,
            $data['otp']
        );

        DB::transaction(function () use ($user, $data): void {
            $this->authRepository->updatePassword(
                $user,
                Hash::make($data['password'])
            );
            $this->authRepository->revokeAllTokens($user);
        });
    }

    private function createAuthPayload(User $user): array
    {
        return [
            'user' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken,
        ];
    }
}
