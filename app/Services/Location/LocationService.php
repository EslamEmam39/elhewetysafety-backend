<?php

namespace App\Services\Location;

use App\Exceptions\Location\LocationUnavailableException;
use App\Models\City;
use App\Models\Country;
use App\Models\Student;
use App\Repositories\Location\LocationRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class LocationService
{
    public function __construct(private readonly LocationRepository $repository)
    {
    }

    public function activeCountries(): Collection
    {
        return $this->repository->activeCountries();
    }
    public function activeCities(Country $country): Collection
    {
        if (!$country->is_active) {
            throw new LocationUnavailableException('country_code');
        }
        return $this->repository->activeCities($country);
    }
    public function countries(): Collection
    {
        return $this->repository->allCountries();
    }
    public function cities(): Collection
    {
        return $this->repository->allCities();
    }
    public function createCountry(array $data): Country
    {
        return $this->repository->createCountry($data);
    }
    public function updateCountry(Country $country, array $data): Country
    {
        return $this->repository->updateCountry($country, $data);
    }
    public function deactivateCountry(Country $country): Country
    {
        return DB::transaction(fn() => $this->repository->deactivateCountry($country));
    }

    public function createCity(array $data): City
    {
        $country = $this->activeCountry($data['country_code']);
        unset($data['country_code']);
        return $this->repository->createCity($data + ['country_id' => $country->id]);
    }

    public function updateCity(City $city, array $data): City
    {
        if (isset($data['country_code'])) {
            $country = $this->activeCountry($data['country_code']);
            $data['country_id'] = $country->id;
            unset($data['country_code']);
        }
        return $this->repository->updateCity($city, $data);
    }

    public function deactivateCity(City $city): City
    {
        return $this->repository->deactivateCity($city);
    }

    public function updateStudentProfile(Student $student, array $data): Student
    {
        if (array_key_exists('city_code', $data)) {
            $data['city_id'] = $data['city_code'] === null
                ? null
                : ($this->repository->findActiveCityByCode($data['city_code'])?->id
                    ?? throw new LocationUnavailableException);
            unset($data['city_code']);
        }
        return $this->repository->updateStudent($student, $data);
    }

    private function activeCountry(string $code): Country
    {
        return $this->repository->findActiveCountryByCode($code)
            ?? throw new LocationUnavailableException('country_code');
    }
}
