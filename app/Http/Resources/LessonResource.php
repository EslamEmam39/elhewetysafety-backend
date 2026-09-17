<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'sort_order' => $this->sort_order,
            'duration_seconds' => $this->duration_seconds,
            'video_status' => $this->video_status,
            'is_free_preview' => $this->is_free_preview,
        ];
    }
}
