<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'username' => $this->username,
            'phone' => $this->phone,
            'full_name_en' => $this->full_name_en,
            'full_name_ar' => $this->full_name_ar,
            'email' => $this->email,
            'is_banned' => $this->is_banned,
            'ban_reason' => $this->ban_reason,
            'targeted_professional_field' => TargetedProfessionalFieldResource::make(
                $this->whenLoaded('targetedProfessionalField')
            ),
            'email_verified_at' => $this->email_verified_at,
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'created_at' => $this->created_at,
        ];
    }
}
