<?php

namespace App\Http\Controllers\Api\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authorization\BanUserRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Services\Authorization\AuthorizationService;
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
        $this->authorize('ban', $user);

        $user = $this->authorizationService->banUser(
            $request->user(),
            $user,
            $request->validated('ban_reason')
        );

        return $this->successResponse(UserResource::make($user), 'User banned successfully');
    }

    public function unban(User $user): JsonResponse
    {
        $this->authorize('unban', $user);

        $user = $this->authorizationService->unbanUser($user);

        return $this->successResponse(UserResource::make($user), 'User unbanned successfully');
    }
}
