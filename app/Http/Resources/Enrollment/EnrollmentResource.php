<?php
namespace App\Http\Resources\Enrollment;
use App\Http\Resources\Course\CourseResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class EnrollmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->code,
            'status' => $this->status->value,
            'source' => $this->source->value,
            'course' => CourseResource::make($this->whenLoaded('course')),
            'user' => UserResource::make($this->whenLoaded('user')),
            'payment_code' => $this->relationLoaded('payment') ? $this->payment?->code : null,
            'enrolled_at' => $this->enrolled_at,
            'revoked_at' => $this->revoked_at
        ];
    }
}
