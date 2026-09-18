<?php

namespace App\Http\Resources\ProfessionalField;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TargetedProfessionalFieldResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->code,
            'name_en' => $this->name_en,
            'name_ar' => $this->name_ar,
        ];
    }
}
