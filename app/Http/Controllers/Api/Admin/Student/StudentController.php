<?php

namespace App\Http\Controllers\Api\Admin\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StudentIndexRequest;
use App\Http\Requests\Student\UpdateStudentProfileRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Services\Student\AdminStudentService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class StudentController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly AdminStudentService $service) {}

    public function index(StudentIndexRequest $request): JsonResponse
    {
        $this->authorize('viewStudents', User::class);

        return $this->successResponse(UserResource::collection($this->service->students($request->validated())));
    }

    public function show(User $user): JsonResponse
    {
        $this->authorize('viewStudent', $user);

        return $this->successResponse(UserResource::make($this->service->student($user)));
    }

    public function update(UpdateStudentProfileRequest $request, User $user): JsonResponse
    {
        $this->authorize('updateStudent', $user);

        return $this->successResponse(
            UserResource::make($this->service->update($user, $request->validated())),
            'Student profile updated successfully'
        );
    }

    public function destroy(User $user): JsonResponse
    {
        $this->authorize('deleteStudent', $user);
        $this->service->delete($user);

        return $this->successResponse(message: 'Student profile deleted successfully');
    }
}
