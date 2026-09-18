<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResendEmailVerificationOtpRequest;
use App\Http\Requests\Auth\VerifyEmailOtpRequest;
use App\Services\Auth\AuthService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class EmailVerificationController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function resend(ResendEmailVerificationOtpRequest $request): JsonResponse
    {
        $this->authService->resendEmailVerificationOtp($request->validated('email'));

        return $this->successResponse(message: 'Verification code sent successfully');
    }

    public function verify(VerifyEmailOtpRequest $request): JsonResponse
    {
        $this->authService->verifyEmail(
            $request->validated('email'),
            $request->validated('otp')
        );

        return $this->successResponse(message: 'Email verified successfully');
    }
}
