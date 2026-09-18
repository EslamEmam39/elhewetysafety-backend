<?php

namespace App\Http\Controllers\Api\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\UserIndexRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Services\User\AdminUserService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly AdminUserService $service) {}

    public function index(UserIndexRequest $request): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        return $this->successResponse(UserResource::collection($this->service->users($request->validated())));
    }

    public function show(User $user): JsonResponse
    {
        $this->authorize('view', $user);

        return $this->successResponse(UserResource::make($this->service->user($user)));
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $this->authorize('update', $user);

        return $this->successResponse(
            UserResource::make($this->service->update($user, $request->validated())),
            'User updated successfully'
        );
    }
}
