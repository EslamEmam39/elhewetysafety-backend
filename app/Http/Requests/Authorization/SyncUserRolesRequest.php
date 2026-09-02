<?php

namespace App\Http\Requests\Authorization;

use App\Http\Requests\ApiFormRequest;

class SyncUserRolesRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'role_ids' => ['required', 'array', 'min:1'],
            'role_ids.*' => ['integer', 'distinct', 'exists:roles,id'],
        ];
    }
}
