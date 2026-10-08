<?php

namespace App\Repositories\Video;

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\User;

class VideoRepository
{
    public function loadLessonContext(Lesson $lesson): Lesson
    {
        return $lesson->loadMissing('chapter.course');
    }

    public function updateVideoMetadata(Lesson $lesson, array $data): Lesson
    {
        $lesson->update($data);

        return $lesson->refresh()->loadMissing('chapter.course');
    }

    public function hasActiveEnrollment(User $user, int $courseId): bool
    {
        return Enrollment::query()
            ->whereBelongsTo($user)
            ->where('course_id', $courseId)
            ->where('status', EnrollmentStatus::Active)
            ->exists();
    }
}
