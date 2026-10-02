<?php

namespace App\Services\Auth;

use App\Exceptions\Auth\EmailAlreadyVerifiedException;
use App\Events\UserRegistered;
use App\Exceptions\Auth\EmailNotVerifiedException;
use App\Exceptions\Auth\InvalidCredentialsException;
use App\Exceptions\Auth\TargetedProfessionalFieldUnavailableException;
use App\Exceptions\Auth\UserBannedException;
use App\Exceptions\Otp\InvalidOtpException;
use App\Models\OtpCode;
use App\Models\User;
use App\Repositories\Auth\AuthRepository;
use App\Repositories\ProfessionalField\TargetedProfessionalFieldRepository;
use App\Repositories\Device\DeviceRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(
        private readonly AuthRepository $authRepository,
        private readonly OtpService $otpService,
        private readonly TargetedProfessionalFieldRepository $targetedProfessionalFieldRepository,
        private readonly DeviceRepository $deviceRepository
    ) {
    }

    public function register(array $data): User
    {
        return DB::transaction(function () use ($data): User {
            $targetedProfessionalField = $this->targetedProfessionalFieldRepository
                ->findActiveByCode($data['targeted_professional_field_code']);

            if (! $targetedProfessionalField) {
                throw new TargetedProfessionalFieldUnavailableException;
            }
            $user = $this->authRepository->createUser([
                'username' => $data['username'],
                'phone' => $data['phone'],
                'full_name_en' => $data['full_name_en'],
                'full_name_ar' => $data['full_name_ar'],
                'targeted_professional_field_id' => $targetedProfessionalField->id,
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $user->assignRole('student');
            $this->authRepository->createStudentProfile($user);

            UserRegistered::dispatch($user->id);

            return $this->authRepository->loadPublicProfile($user);
        });
    }

    public function login(array $credentials): array
    {
        return $this->authenticate($credentials);
    }

    public function loginAdmin(array $credentials): array
    {
        return $this->authenticate($credentials, 'admin');
    }

    private function authenticate(array $credentials, ?string $requiredRole = null): array
    {
        $user = $this->authRepository->findUserByEmail($credentials['email']);

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new InvalidCredentialsException;
        }

        if ($requiredRole !== null && ! $user->hasRole($requiredRole)) {
            throw new InvalidCredentialsException;
        }

        if ($user->is_banned) {
            throw new UserBannedException($user->ban_reason);
        }
        if (!$user->hasVerifiedEmail()) {
            throw new EmailNotVerifiedException;
        }

        return DB::transaction(function () use ($user, $credentials): array {
            $device = $this->deviceRepository->registerForLogin($user, $credentials);
            $auth = $this->createAuthPayload(
                $this->authRepository->loadPublicProfile($user),
                $device->name
            );
            $this->deviceRepository->attachToken($device, $auth['access_token']);
            unset($auth['access_token']);

            return $auth;
        });
    }

    public function logout(User $user): void
    {
        $this->authRepository->deleteCurrentToken($user);
    }

    public function authenticatedUser(User $user): User
    {
        return $this->authRepository->loadPublicProfile($user);
    }

    public function resendEmailVerificationOtp(string $email): void
    {
        $user = $this->authRepository->findUserByEmail($email);

        // Return the same public response for an unknown email address.
        if (!$user) {
            return;
        }

        if ($user->hasVerifiedEmail()) {
            throw new EmailAlreadyVerifiedException;
        }

        $this->otpService->sendEmailVerificationOtp($user->email);
    }

    public function verifyEmail(string $email, string $otp): void
    {
        $user = $this->authRepository->findUserByEmail($email);

        if (!$user) {
            throw new InvalidOtpException;
        }

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

        if (!$user) {
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

    private function createAuthPayload(User $user, string $tokenName): array
    {
        $newToken = $user->createToken($tokenName);

        return [
            'user' => $user,
            'token' => $newToken->plainTextToken,
            'access_token' => $newToken->accessToken,
        ];
    }
}
