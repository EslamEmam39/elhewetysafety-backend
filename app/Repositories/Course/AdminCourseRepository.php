<?php

namespace App\Repositories\Course;

use App\Models\Chapter;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class AdminCourseRepository
{
    public function findCategoryBySlug(string $slug): ?CourseCategory { return CourseCategory::query()->where('slug', $slug)->where('is_active', true)->first(); }
    public function findUserByUuid(string $uuid): ?User { return User::query()->where('uuid', $uuid)->first(); }
    public function categories(): Collection { return CourseCategory::query()->orderBy('sort_order')->orderBy('id')->get(); }
    public function createCategory(array $data): CourseCategory { return CourseCategory::create($data); }
    public function updateCategory(CourseCategory $category, array $data): CourseCategory { $category->update($data); return $category->refresh(); }
    public function deactivateCategory(CourseCategory $category): CourseCategory { $category->update(['is_active' => false]); return $category->refresh(); }

    public function courses(): Collection { return Course::query()->with(['category','instructor'])->latest('id')->get(); }
    public function loadCourse(Course $course): Course { return $course->load(['category','instructor','chapters.lessons']); }
    public function createCourse(array $data): Course { return Course::create($data)->load(['category','instructor']); }
    public function updateCourse(Course $course, array $data): Course { $course->update($data); return $course->refresh()->load(['category','instructor','chapters.lessons']); }
    public function deleteCourse(Course $course): void { $course->delete(); }

    public function chapters(Course $course): Collection { return $course->chapters()->with('lessons')->get(); }
    public function createChapter(Course $course, array $data): Chapter { return $course->chapters()->create($data); }
    public function updateChapter(Chapter $chapter, array $data): Chapter { $chapter->update($data); return $chapter->refresh()->load('lessons'); }
    public function deleteChapter(Chapter $chapter): void { $chapter->delete(); }

    public function lessons(Chapter $chapter): Collection { return $chapter->lessons()->get(); }
    public function createLesson(Chapter $chapter, array $data): Lesson { return $chapter->lessons()->create($data); }
    public function updateLesson(Lesson $lesson, array $data): Lesson { $lesson->update($data); return $lesson->refresh(); }
    public function deleteLesson(Lesson $lesson): void { $lesson->delete(); }
}
