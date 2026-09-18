<?php

namespace App\Http\Requests\Location;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class StoreCityRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'country_code' => ['required', 'string', 'size:2'],
            'code' => ['required', 'string', 'max:100', Rule::unique('cities', 'code')],
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0', 'max:65535'],
        ];
    }
}
