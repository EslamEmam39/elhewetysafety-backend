<?php

namespace App\Http\Requests\Student;

use App\Http\Requests\ApiFormRequest;

class StudentIndexRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'search' => ['sometimes', 'string', 'max:255'],
            'city_code' => ['sometimes', 'string', 'max:100', 'exists:cities,code'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }
}
