<?php

namespace App\Http\Resources\Student;

use App\Http\Resources\Location\CityResource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'avatar_storage_key' => $this->avatar_storage_key,
            'job_title' => $this->job_title,
            'company' => $this->company,
            'bio' => $this->bio,
            'city' => CityResource::make($this->whenLoaded('city')),
        ];
    }
}
