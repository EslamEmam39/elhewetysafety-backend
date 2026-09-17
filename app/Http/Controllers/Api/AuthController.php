<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
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
        $auth = $this->authService->login($request->validated());

        return $this->successResponse([
            'user' => UserResource::make($auth['user']),
            'token' => $auth['token'],
        ], 'Logged in successfully');
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
}
