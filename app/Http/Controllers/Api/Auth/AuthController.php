<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\User\UserResource;
use App\Services\Auth\AuthService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authService->register($request->validated());

        return $this->successResponse([
            'user' => UserResource::make($user),
        ], 'Account created successfully', 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $auth = $this->authService->login($this->loginData($request));

        return $this->successResponse([
            'user' => UserResource::make($auth['user']),
            'token' => $auth['token'],
        ], 'Logged in successfully');
    }

    public function adminLogin(LoginRequest $request): JsonResponse
    {
        $auth = $this->authService->loginAdmin($this->loginData($request));

        return $this->successResponse([
            'user' => UserResource::make($auth['user']),
            'token' => $auth['token'],
        ], 'Admin logged in successfully');
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return $this->successResponse(
            message: 'Logged out successfully'
        );
    }

    public function me(Request $request): JsonResponse
    {
        return $this->successResponse([
            'user' => UserResource::make($this->authService->authenticatedUser($request->user())),
        ], 'Authenticated user retrieved successfully');
    }

    private function loginData(LoginRequest $request): array
    {
        return $request->validated() + [
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ];
    }
}
