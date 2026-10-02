<?php

namespace App\Http\Requests\Order;

use App\Enums\OrderStatus;
use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;

class OrderIndexRequest extends ApiFormRequest
{
    public function rules(): array
    {
        return [
            'status' => ['sometimes', 'string', Rule::enum(OrderStatus::class)],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }
}
