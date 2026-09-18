<?php

namespace App\Services\Auth;

use App\Exceptions\Otp\ExpiredOtpException;
use App\Exceptions\Otp\InvalidOtpException;
use App\Exceptions\Otp\OtpResendThrottledException;
use App\Exceptions\Otp\TooManyOtpAttemptsException;
use App\Mail\OtpMail;
use App\Models\OtpCode;
use App\Repositories\Auth\OtpRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    public function __construct(
        private readonly OtpRepository $otpRepository
    ) {
    }

    public function sendEmailVerificationOtp(
        string $email,
        bool $enforceCooldown = true
    ): bool {
        return $this->issue(
            email: $email,
            purpose: OtpCode::PURPOSE_EMAIL_VERIFICATION,
            enforceCooldown: $enforceCooldown
        );
    }

    public function sendPasswordResetOtp(string $email): bool
    {
        return $this->issue(
            email: $email,
            purpose: OtpCode::PURPOSE_PASSWORD_RESET,
            enforceCooldown: false
        );
    }

    public function verify(string $email, string $purpose, string $code): void
    {
        DB::transaction(function () use ($email, $purpose, $code): void {
            $otp = $this->otpRepository->findActiveForUpdate($email, $purpose);

            if (!$otp) {
                throw new InvalidOtpException;
            }

            if ($otp->expires_at->isPast()) {
                $this->otpRepository->consume($otp);
                throw new ExpiredOtpException;
            }

            $maxAttempts = config('otp.max_attempts');

            if ($otp->attempts >= $maxAttempts) {
                $this->otpRepository->consume($otp);
                throw new TooManyOtpAttemptsException;
            }

            if (!Hash::check($code, $otp->code_hash)) {
                $attempts = $this->otpRepository->incrementAttempts($otp);

                if ($attempts >= $maxAttempts) {
                    $this->otpRepository->consume($otp);
                    throw new TooManyOtpAttemptsException;
                }

                throw new InvalidOtpException;
            }

            $this->otpRepository->consume($otp);
        });
    }

    private function issue(
        string $email,
        string $purpose,
        bool $enforceCooldown
    ): bool {
        $latest = $this->otpRepository->latestFor($email, $purpose);
        $resendSeconds = config('otp.resend_seconds');

        if ($latest && $latest->created_at->addSeconds($resendSeconds)->isFuture()) {
            if ($enforceCooldown) {
                $retryAfter = now()->diffInSeconds(
                    $latest->created_at->addSeconds($resendSeconds),
                    false
                );
                throw new OtpResendThrottledException(max(1, $retryAfter));
            }

            return false;
        }

        $code = (string) random_int(100000, 999999);
        $expirationMinutes = config('otp.expiration_minutes');

        DB::transaction(function () use ($email, $purpose, $code, $expirationMinutes): void {
            $this->otpRepository->invalidateActive($email, $purpose);

            $this->otpRepository->create([
                'email' => $email,
                'purpose' => $purpose,
                'code_hash' => Hash::make($code),
                'expires_at' => now()->addMinutes($expirationMinutes),
            ]);
        });

        Mail::to($email)->send(new OtpMail(
            code: $code,
            purpose: $purpose,
            expirationMinutes: $expirationMinutes
        ));

        return true;
    }
}
