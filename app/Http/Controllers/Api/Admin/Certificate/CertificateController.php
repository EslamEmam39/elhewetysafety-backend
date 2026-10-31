<?php

namespace App\Http\Controllers\Api\Admin\Certificate;

use App\Http\Controllers\Controller;
use App\Http\Requests\Certificate\AdminCertificateIndexRequest;
use App\Http\Requests\Certificate\StoreCertificateRequest;
use App\Http\Resources\Certificate\CertificateResource;
use App\Models\Certificate;
use App\Services\Certificate\CertificateService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CertificateController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly CertificateService $service) {}

    public function index(AdminCertificateIndexRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Certificate::class);

        return $this->successResponse(CertificateResource::collection($this->service->adminCertificates($request->validated())));
    }

    public function store(StoreCertificateRequest $request): JsonResponse
    {
        $this->authorize('issue', Certificate::class);

        return $this->successResponse(
            CertificateResource::make($this->service->issue($request->user(), $request->validated())),
            'Certificate issued successfully',
            201
        );
    }

    public function show(Certificate $certificate): JsonResponse
    {
        $this->authorize('view', $certificate);

        return $this->successResponse(CertificateResource::make($this->service->adminCertificate($certificate)));
    }

    public function regeneratePdf(Certificate $certificate): JsonResponse
    {
        $this->authorize('issue', Certificate::class);

        return $this->successResponse(
            CertificateResource::make($this->service->regeneratePdf($certificate)),
            'Certificate PDF regenerated successfully'
        );
    }

    public function download(Certificate $certificate): StreamedResponse
    {
        $this->authorize('view', $certificate);

        return $this->service->download($certificate);
    }
}
