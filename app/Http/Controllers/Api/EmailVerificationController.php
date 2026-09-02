<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmailVerification\VerifyEmailOtpRequest;
use App\Services\AuthService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function resend(Request $request): JsonResponse
    {
        $this->authService->resendEmailVerificationOtp($request->user());

        return $this->successResponse(message: 'Verification code sent successfully');
    }

    public function verify(VerifyEmailOtpRequest $request): JsonResponse
    {
        $this->authService->verifyEmail(
            $request->user(),
            $request->validated('otp')
        );

        return $this->successResponse(message: 'Email verified successfully');
    }
}
