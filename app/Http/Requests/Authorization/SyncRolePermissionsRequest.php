<?php

namespace App\Http\Requests\Authorization;

use App\Http\Requests\ApiFormRequest;

class SyncRolePermissionsRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'permission_ids' => ['present', 'array'],
            'permission_ids.*' => ['integer', 'distinct', 'exists:permissions,id'],
        ];
    }
}
