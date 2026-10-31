<?php

namespace App\Http\Requests\Certificate;

use App\Http\Requests\ApiFormRequest;

class AdminCertificateIndexRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'user_uuid' => ['sometimes', 'uuid'],
            'course_slug' => ['sometimes', 'string', 'max:255'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }
}
