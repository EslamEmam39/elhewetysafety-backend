<?php

namespace App\Services\LessonProgress;

use App\Exceptions\Enrollment\EnrollmentAccessDeniedException;
use App\Exceptions\LessonProgress\PositionExceedsLessonDurationException;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use App\Repositories\LessonProgress\LessonProgressRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class LessonProgressService
{
    public function __construct(private readonly LessonProgressRepository $repository) {}

    public function forCourse(User $user, Course $course): Collection
    {
        $this->ensureAccess($user, $course);

        return $this->repository->forCourse($user, $course);
    }

    public function update(User $user, Lesson $lesson, array $data): LessonProgress
    {
        return DB::transaction(function () use ($user, $lesson, $data): LessonProgress {
            $lesson = $this->repository->lessonWithCourse($lesson);
            $this->ensureAccess($user, $lesson->chapter->course);

            if ($lesson->duration_seconds !== null
                && $data['last_position_seconds'] > $lesson->duration_seconds) {
                throw new PositionExceedsLessonDurationException;
            }

            return $this->repository->save($user, $lesson, $data);
        });
    }

    private function ensureAccess(User $user, Course $course): void
    {
        if (! $this->repository->hasActiveEnrollment($user, $course)) {
            throw new EnrollmentAccessDeniedException;
        }
    }
}
