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
            'thumbnail_url' => $this->thumbnail_url,
            'status' => $this->status,
            'published_at' => $this->published_at,
            'lessons_count' => $this->whenHas('lessons_count', (int) $this->lessons_count),
            'students_count' => $this->whenHas('students_count', (int) $this->students_count),
            'duration_seconds' => $this->whenHas('lessons_sum_duration_seconds', (int) $this->lessons_sum_duration_seconds),
            'average_rating' => $this->whenHas('average_rating', $this->average_rating === null ? null : round((float) $this->average_rating, 1)),
            'reviews_count' => $this->whenHas('reviews_count', (int) $this->reviews_count),
            'category' => CourseCategoryResource::make($this->whenLoaded('category')),
            'instructor' => InstructorResource::make($this->whenLoaded('instructor')),
            'chapters' => ChapterResource::collection($this->whenLoaded('chapters')),
        ];
    }
}
