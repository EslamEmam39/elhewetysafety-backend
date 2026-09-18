<?php

namespace App\Http\Resources\Course;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'currency' => $this->currency,
            'level' => $this->level,
            'thumbnail_storage_key' => $this->thumbnail_storage_key,
            'status' => $this->status,
            'published_at' => $this->published_at,
            'category' => CourseCategoryResource::make($this->whenLoaded('category')),
            'instructor' => InstructorResource::make($this->whenLoaded('instructor')),
            'chapters' => ChapterResource::collection($this->whenLoaded('chapters')),
        ];
    }
}
