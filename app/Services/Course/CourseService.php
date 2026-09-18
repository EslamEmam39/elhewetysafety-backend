<?php

namespace App\Services\Course;

use App\Models\Course;
use App\Repositories\Course\CourseRepository;
use Illuminate\Database\Eloquent\Collection;

class CourseService
{
    public function __construct(private readonly CourseRepository $courseRepository) {}

    public function publishedCourses(): Collection
    {
        return $this->courseRepository->publishedCourses();
    }

    public function publishedCourse(string $slug): ?Course
    {
        return $this->courseRepository->findPublishedBySlug($slug);
    }

    public function activeCategories(): Collection
    {
        return $this->courseRepository->activeCategories();
    }
}
