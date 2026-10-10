<?php

namespace App\Http\Resources\Course;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChapterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->code,
            'title' => $this->title,
            'sort_order' => $this->sort_order,
            'course' => CourseReferenceResource::make($this->whenLoaded('course')),
            'lessons' => LessonResource::collection($this->whenLoaded('lessons')),
        ];
    }
}
