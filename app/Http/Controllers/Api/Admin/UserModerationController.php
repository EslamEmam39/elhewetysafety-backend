<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authorization\BanUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuthorizationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class UserModerationController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly AuthorizationService $authorizationService
    ) {}

    public function ban(BanUserRequest $request, User $user): JsonResponse
    {
        $user = $this->authorizationService->banUser(
            $request->user(),
            $user,
            $request->validated('ban_reason')
        );

        return $this->successResponse(UserResource::make($user), 'User banned successfully');
    }

    public function unban(User $user): JsonResponse
    {
        $user = $this->authorizationService->unbanUser($user);

        return $this->successResponse(UserResource::make($user), 'User unbanned successfully');
    }
}