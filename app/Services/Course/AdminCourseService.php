<?php

namespace App\Services\Course;

use App\Exceptions\Course\CourseReferenceUnavailableException;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Lesson;
use App\Repositories\Course\AdminCourseRepository;
use Illuminate\Database\Eloquent\Collection;

class AdminCourseService
{
    public function __construct(private readonly AdminCourseRepository $repository)
    {
    }

    public function categories(): Collection
    {
        return $this->repository->categories();
    }
    public function createCategory(array $data): CourseCategory
    {
        return $this->repository->createCategory($data);
    }
    public function updateCategory(CourseCategory $category, array $data): CourseCategory
    {
        return $this->repository->updateCategory($category, $data);
    }
    public function deactivateCategory(CourseCategory $category): CourseCategory
    {
        return $this->repository->deactivateCategory($category);
    }

    public function courses(): Collection
    {
        return $this->repository->courses();
    }
    public function course(Course $course): Course
    {
        return $this->repository->loadCourse($course);
    }
    public function createCourse(array $data): Course
    {
        return $this->repository->createCourse($this->resolveCourseReferences($data));
    }
    public function updateCourse(Course $course, array $data): Course
    {
        return $this->repository->updateCourse($course, $this->resolveCourseReferences($data, $course));
    }
    public function deleteCourse(Course $course): void
    {
        $this->repository->deleteCourse($course);
    }

    public function chapters(Course $course): Collection
    {
        return $this->repository->chapters($course);
    }
    public function createChapter(Course $course, array $data): Chapter
    {
        return $this->repository->createChapter($course, $data);
    }
    public function updateChapter(Chapter $chapter, array $data): Chapter
    {
        return $this->repository->updateChapter($chapter, $data);
    }
    public function deleteChapter(Chapter $chapter): void
    {
        $this->repository->deleteChapter($chapter);
    }

    public function lessons(Chapter $chapter): Collection
    {
        return $this->repository->lessons($chapter);
    }
    public function createLesson(Chapter $chapter, array $data): Lesson
    {
        return $this->repository->createLesson($chapter, $data);
    }
    public function updateLesson(Lesson $lesson, array $data): Lesson
    {
        return $this->repository->updateLesson($lesson, $data);
    }
    public function deleteLesson(Lesson $lesson): void
    {
        $this->repository->deleteLesson($lesson);
    }

    private function resolveCourseReferences(array $data, ?Course $course = null): array
    {
        if (array_key_exists('course_category_slug', $data)) {
            $data['course_category_id'] = $this->repository->findCategoryBySlug($data['course_category_slug'])?->id
                ?? throw new CourseReferenceUnavailableException('course_category_slug');
            unset($data['course_category_slug']);
        }
        if (array_key_exists('instructor_uuid', $data)) {
            $data['instructor_id'] = $data['instructor_uuid']
                ? ($this->repository->findUserByUuid($data['instructor_uuid'])?->id
                    ?? throw new CourseReferenceUnavailableException('instructor_uuid'))
                : null;
            unset($data['instructor_uuid']);
        }
        $status = $data['status'] ?? $course?->status;
        if ($status === 'published' && !$course?->published_at) {
            $data['published_at'] = now();
        }
        if ($status === 'draft') {
            $data['published_at'] = null;
        }
        $data['currency'] = isset($data['currency']) ? strtoupper($data['currency']) : ($course?->currency ?? 'EGP');
        return $data;
    }
}
