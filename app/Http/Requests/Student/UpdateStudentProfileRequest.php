<?php

namespace App\Http\Requests\Student;

use App\Http\Requests\ApiFormRequest;

class UpdateStudentProfileRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'city_code' => ['sometimes', 'nullable', 'string', 'max:100'],
            'avatar_storage_key' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'job_title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'company' => ['sometimes', 'nullable', 'string', 'max:255'],
            'bio' => ['sometimes', 'nullable', 'string', 'max:5000'],
        ];
    }
}
