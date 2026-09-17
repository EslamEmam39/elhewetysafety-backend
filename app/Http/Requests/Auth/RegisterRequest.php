<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Support\Str;

class RegisterRequest extends ApiFormRequest
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
            'username' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[A-Za-z0-9_]+$/', 'unique:users,username'],
            'phone' => ['required', 'string', 'max:32', 'unique:users,phone'],
            'full_name_en' => ['required', 'string', 'max:255'],
            'full_name_ar' => ['required', 'string', 'max:255'],
            'targeted_professional_field_uuid' => ['required', 'uuid', 'exists:targeted_professional_fields,uuid'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
