<?php

namespace App\Http\Resources\Device;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'platform' => $this->platform,
            'user_agent' => $this->user_agent,
            'ip_address' => $this->ip_address,
            'is_current' => $request->user()?->currentAccessToken()?->device_id === $this->id,
            'last_activity_at' => $this->last_activity_at,
            'revoked_at' => $this->revoked_at,
            'created_at' => $this->created_at,
        ];
    }
}
