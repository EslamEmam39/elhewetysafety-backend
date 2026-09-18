<?php

namespace App\Http\Requests\Location;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class StoreCountryRequest extends ApiFormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('code')) {
            $this->merge(['code' => strtoupper((string) $this->input('code'))]);
        }
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'size:2', Rule::unique('countries', 'code')],
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'phone_code' => ['required', 'string', 'max:8', 'regex:/^\+[0-9]{1,7}$/'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0', 'max:65535'],
        ];
    }
}
