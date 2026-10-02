<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->code,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'status' => $this->status->value,
            'course' => OrderCourseResource::make($this->whenLoaded('course')),
            'user' => UserResource::make($this->whenLoaded('user')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
