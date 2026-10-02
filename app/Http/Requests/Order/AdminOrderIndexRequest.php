<?php

namespace App\Http\Requests\Order;

class AdminOrderIndexRequest extends OrderIndexRequest
{
    public function rules(): array
    {
        return parent::rules() + [
            'user_uuid' => ['sometimes', 'uuid'],
            'course_slug' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
