<?php

namespace App\Http\Requests\Certificate;

use App\Http\Requests\ApiFormRequest;

class StoreCertificateRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'user_uuid' => ['required', 'uuid'],
            'course_slug' => ['required', 'string', 'max:255'],
        ];
    }
}
