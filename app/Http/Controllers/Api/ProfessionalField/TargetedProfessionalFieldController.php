<?php

namespace App\Http\Controllers\Api\ProfessionalField;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfessionalField\TargetedProfessionalFieldResource;
use App\Services\ProfessionalField\TargetedProfessionalFieldService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class TargetedProfessionalFieldController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly TargetedProfessionalFieldService $service
    ) {}

    public function index(): JsonResponse
    {
        return $this->successResponse(
            TargetedProfessionalFieldResource::collection($this->service->activeFields())
        );
    }
}
