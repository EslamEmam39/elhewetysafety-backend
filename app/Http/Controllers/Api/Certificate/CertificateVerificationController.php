<?php

namespace App\Http\Controllers\Api\Certificate;

use App\Http\Controllers\Controller;
use App\Http\Resources\Certificate\CertificateResource;
use App\Models\Certificate;
use App\Services\Certificate\CertificateService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CertificateVerificationController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly CertificateService $service) {}

    public function verify(Certificate $certificate): JsonResponse
    {
        return $this->successResponse(CertificateResource::make($this->service->verify($certificate)));
    }

    public function download(Certificate $certificate): StreamedResponse
    {
        return $this->service->download($certificate);
    }
}
