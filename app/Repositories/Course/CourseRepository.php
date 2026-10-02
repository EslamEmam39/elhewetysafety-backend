<?php

namespace App\Repositories\Course;

use App\Models\Course;
use Illuminate\Database\Eloquent\Collection;
use App\Models\CourseCategory;

class CourseRepository
{
    public function activeCategories(): Collection
    {
        return CourseCategory::query()->where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
    }
    public function publishedCourses(): Collection
    {
        return Course::query()
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->with(['category', 'instructor'])
            ->withCount([
                'lessons',
                'enrollments as students_count' => fn ($query) => $query->where('status', 'active'),
                'visibleReviews as reviews_count',
            ])
            ->withSum('lessons', 'duration_seconds')
            ->withAvg('visibleReviews as average_rating', 'rating')
            ->orderByDesc('published_at')
            ->get();
    }

    public function findPublishedBySlug(string $slug): ?Course
    {
        return Course::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->with(['category', 'instructor'])
            ->withCount([
                'lessons',
                'enrollments as students_count' => fn ($query) => $query->where('status', 'active'),
                'visibleReviews as reviews_count',
            ])
            ->withSum('lessons', 'duration_seconds')
            ->withAvg('visibleReviews as average_rating', 'rating')
            ->first();
    }
}
