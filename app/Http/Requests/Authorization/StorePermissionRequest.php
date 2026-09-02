<?php

namespace App\Http\Requests\Authorization;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class StorePermissionRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('permissions', 'name')->where('guard_name', 'web'),
            ],
        ];
    }
}
