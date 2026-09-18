<?php

namespace App\Repositories\Location;

use App\Models\City;
use App\Models\Country;
use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;

class LocationRepository
{
    public function activeCountries(): Collection
    {
        return Country::query()->where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
    }

    public function activeCities(Country $country): Collection
    {
        return $country->cities()->where('is_active', true)->get();
    }

    public function allCountries(): Collection
    {
        return Country::query()->withCount('cities')->orderBy('sort_order')->orderBy('id')->get();
    }

    public function allCities(): Collection
    {
        return City::query()->with('country')->orderBy('country_id')->orderBy('sort_order')->orderBy('id')->get();
    }

    public function findActiveCityByCode(string $code): ?City
    {
        return City::query()
            ->where('code', $code)
            ->where('is_active', true)
            ->whereHas('country', fn ($query) => $query->where('is_active', true))
            ->first();
    }

    public function findActiveCountryByCode(string $code): ?Country
    {
        return Country::query()->where('code', $code)->where('is_active', true)->first();
    }

    public function createCountry(array $data): Country
    {
        return Country::create($data);
    }

    public function updateCountry(Country $country, array $data): Country
    {
        $country->update($data);
        return $country->refresh()->loadCount('cities');
    }

    public function createCity(array $data): City
    {
        return City::create($data)->load('country');
    }

    public function updateCity(City $city, array $data): City
    {
        $city->update($data);
        return $city->refresh()->load('country');
    }

    public function deactivateCountry(Country $country): Country
    {
        $country->cities()->update(['is_active' => false]);
        $country->update(['is_active' => false]);
        return $country->refresh()->loadCount('cities');
    }

    public function deactivateCity(City $city): City
    {
        $city->update(['is_active' => false]);
        return $city->refresh()->load('country');
    }

    public function updateStudent(Student $student, array $data): Student
    {
        $student->update($data);
        return $student->refresh()->load('city.country');
    }
}
