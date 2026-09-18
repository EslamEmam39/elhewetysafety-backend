<?php

namespace App\Http\Requests\User;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends ApiFormRequest
{
    protected function prepareForValidation(): void
    {
        $data = [];

        if ($this->has('email')) {
            $data['email'] = Str::lower(trim((string) $this->input('email')));
        }

        if ($this->has('username')) {
            $data['username'] = trim((string) $this->input('username'));
        }

        $this->merge($data);
    }

    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'username' => ['sometimes', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user?->id)],
            'phone' => ['sometimes', 'string', 'max:32', Rule::unique('users', 'phone')->ignore($user?->id)],
            'full_name_en' => ['sometimes', 'string', 'max:255'],
            'full_name_ar' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'targeted_professional_field_code' => ['sometimes', 'string', 'max:50'],
        ];
    }
}
