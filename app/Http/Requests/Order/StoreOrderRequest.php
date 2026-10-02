<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\ApiFormRequest;

class StoreOrderRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'course_slug' => ['required', 'string', 'max:255'],
        ];
    }
}
