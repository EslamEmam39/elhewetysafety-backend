<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Support\Str;

class LoginRequest extends ApiFormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => Str::lower(trim((string) $this->input('email'))),
        ]);
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_uuid' => ['required', 'uuid'],
            'device_name' => ['required', 'string', 'max:100'],
            'device_platform' => ['nullable', 'string', 'max:50'],
        ];
    }
}
