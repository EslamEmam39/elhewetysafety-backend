<?php

namespace App\Http\Middleware;

use App\Repositories\Device\DeviceRepository;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TouchDeviceActivity
{
    public function __construct(private readonly DeviceRepository $repository) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $token = $request->user()?->currentAccessToken();

        $this->repository->touchFromToken(
            $token?->device_id,
            $request->ip(),
            $request->userAgent()
        );

        return $response;
    }
}
