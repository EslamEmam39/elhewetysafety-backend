<?php

namespace App\Http\Controllers\Api\Admin\Location;

use App\Http\Controllers\Controller;
use App\Http\Requests\Location\StoreCityRequest;
use App\Http\Requests\Location\UpdateCityRequest;
use App\Http\Resources\Location\CityResource;
use App\Models\City;
use App\Services\Location\LocationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class CityController extends Controller
{
    use ApiResponse;
    public function __construct(private readonly LocationService $service)
    {
    }
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', City::class);
        return $this->successResponse(CityResource::collection($this->service->cities()));
    }
    public function show(City $city): JsonResponse
    {
        $this->authorize('view', $city);
        return $this->successResponse(CityResource::make($city->load('country')));
    }
    public function store(StoreCityRequest $request): JsonResponse
    {
        $this->authorize('create', City::class);
        return $this->successResponse(CityResource::make($this->service->createCity($request->validated())), 'City created successfully', 201);
    }
    public function update(UpdateCityRequest $request, City $city): JsonResponse
    {
        $this->authorize('update', $city);
        return $this->successResponse(CityResource::make($this->service->updateCity($city, $request->validated())), 'City updated successfully');
    }
    public function destroy(City $city): JsonResponse
    {
        $this->authorize('delete', $city);
        return $this->successResponse(CityResource::make($this->service->deactivateCity($city)), 'City deactivated successfully');
    }
}
