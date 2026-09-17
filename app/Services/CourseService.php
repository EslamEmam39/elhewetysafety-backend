<?php

namespace App\Services;

use App\Models\Course;
use App\Repositories\CourseRepository;
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
}
