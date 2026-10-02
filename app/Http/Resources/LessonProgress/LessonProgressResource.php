<?php

namespace App\Http\Resources\LessonProgress;

use App\Http\Resources\Course\LessonResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonProgressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'lesson' => LessonResource::make($this->whenLoaded('lesson')),
            'last_position_seconds' => $this->last_position_seconds,
            'is_completed' => $this->completed_at !== null,
            'last_watched_at' => $this->last_watched_at,
            'completed_at' => $this->completed_at,
        ];
    }
}
