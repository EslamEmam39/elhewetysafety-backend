<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\UpdateStudentProfileRequest;
use App\Http\Resources\Student\StudentProfileResource;
use App\Services\Location\LocationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class StudentProfileController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly LocationService $service) {}

    public function update(UpdateStudentProfileRequest $request): JsonResponse
    {
        $student = $this->service->updateStudentProfile($request->user()->student, $request->validated());
        return $this->successResponse(StudentProfileResource::make($student), 'Student profile updated successfully');
    }
}
