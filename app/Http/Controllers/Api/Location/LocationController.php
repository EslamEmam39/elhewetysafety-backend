<?php

namespace App\Http\Controllers\Api\Location;

use App\Http\Controllers\Controller;
use App\Http\Resources\Location\CityResource;
use App\Http\Resources\Location\CountryResource;
use App\Models\Country;
use App\Services\Location\LocationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly LocationService $service) {}

    public function countries(): JsonResponse
    {
        return $this->successResponse(CountryResource::collection($this->service->activeCountries()));
    }

    public function cities(Country $country): JsonResponse
    {
        return $this->successResponse(CityResource::collection($this->service->activeCities($country)));
    }
}
