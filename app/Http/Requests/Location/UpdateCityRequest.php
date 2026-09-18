<?php

namespace App\Http\Requests\Location;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class UpdateCityRequest extends ApiFormRequest
{
    public function rules(): array
    {
        $city = $this->route('city');

        return [
            'country_code' => ['sometimes', 'string', 'size:2'],
            'code' => ['sometimes', 'string', 'max:100', Rule::unique('cities', 'code')->ignore($city?->id)],
            'name_en' => ['sometimes', 'string', 'max:255'],
            'name_ar' => ['sometimes', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0', 'max:65535'],
        ];
    }
}
