<?php

namespace App\Http\Requests\Authorization;

use App\Http\Requests\ApiFormRequest;

class BanUserRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'ban_reason' => ['required', 'string', 'min:3', 'max:1000'],
        ];
    }
}