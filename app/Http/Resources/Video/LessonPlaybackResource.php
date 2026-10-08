<?php

namespace App\Http\Resources\Video;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonPlaybackResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'lesson_code' => $this->resource['lesson']->code,
            'playback_url' => $this->resource['playback_url'],
            'expires_at' => $this->resource['expires_at']->toIso8601String(),
            'mime_type' => $this->resource['lesson']->video_mime_type,
            'duration_seconds' => $this->resource['lesson']->duration_seconds,
        ];
    }
}
