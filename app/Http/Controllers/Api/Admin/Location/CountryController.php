<?php

namespace App\Http\Controllers\Api\Admin\Location;

use App\Http\Controllers\Controller;
use App\Http\Requests\Location\StoreCountryRequest;
use App\Http\Requests\Location\UpdateCountryRequest;
use App\Http\Resources\Location\CountryResource;
use App\Models\Country;
use App\Services\Location\LocationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class CountryController extends Controller
{
    use ApiResponse;
    public function __construct(private readonly LocationService $service)
    {
    }
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Country::class);
        return $this->successResponse(CountryResource::collection($this->service->countries()));
    }
    public function show(Country $country): JsonResponse
    {

        $this->authorize('view', $country);
        return $this->successResponse(CountryResource::make($country->loadCount('cities')));
    }
    public function store(StoreCountryRequest $request): JsonResponse
    {
        $this->authorize('create', Country::class);
        return $this->successResponse(CountryResource::make($this->service->createCountry($request->validated())), 'Country created successfully', 201);
    }
    public function update(UpdateCountryRequest $request, Country $country): JsonResponse
    {
        $this->authorize('update', $country);
        return $this->successResponse(
            CountryResource::make
            (
                $this->service->updateCountry($country, $request->validated())
            ),
            'Country updated successfully'
        );
    }
    public function destroy(Country $country): JsonResponse
    {
        $this->authorize('delete', $country);
        return $this->successResponse(CountryResource::make($this->service->deactivateCountry($country)), 'Country deactivated successfully');
    }
}
