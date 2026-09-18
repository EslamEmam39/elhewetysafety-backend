<?php

namespace App\Http\Resources\Course;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->code,
            'title' => $this->title,
            'sort_order' => $this->sort_order,
            'duration_seconds' => $this->duration_seconds,
            'video_status' => $this->video_status,
            'is_free_preview' => $this->is_free_preview,
        ];
    }
}
