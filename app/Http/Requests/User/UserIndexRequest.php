<?php

namespace App\Http\Requests\User;

use App\Http\Requests\ApiFormRequest;

class UserIndexRequest extends ApiFormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('is_banned')) {
            $this->merge(['is_banned' => filter_var($this->input('is_banned'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)]);
        }
    }

    public function rules(): array
    {
        return [
            'search' => ['sometimes', 'string', 'max:255'],
            'is_banned' => ['sometimes', 'boolean'],
            'role' => ['sometimes', 'string', 'exists:roles,name'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }
}
