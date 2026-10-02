<?php

namespace App\Repositories\LessonProgress;

use App\Enums\EnrollmentStatus;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class LessonProgressRepository
{
    public function hasActiveEnrollment(User $user, Course $course): bool
    {
        return Enrollment::query()
            ->whereBelongsTo($user)
            ->whereBelongsTo($course)
            ->where('status', EnrollmentStatus::Active)
            ->exists();
    }

    public function lessonWithCourse(Lesson $lesson): Lesson
    {
        return $lesson->loadMissing('chapter.course');
    }

    public function forCourse(User $user, Course $course): Collection
    {
        return LessonProgress::query()
            ->whereBelongsTo($user)
            ->whereHas('lesson.chapter', fn ($query) => $query->where('course_id', $course->id))
            ->with('lesson')
            ->orderBy('lesson_id')
            ->get();
    }

    public function save(User $user, Lesson $lesson, array $data): LessonProgress
    {
        $progress = LessonProgress::query()->firstOrNew([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ]);

        $progress->last_position_seconds = $data['last_position_seconds'];
        $progress->last_watched_at = now();

        if (($data['is_completed'] ?? false) && $progress->completed_at === null) {
            $progress->completed_at = now();
        }

        $progress->save();

        return $progress->load('lesson');
    }
}
