<?php

namespace App\Repositories\CourseReview;

use App\Enums\EnrollmentStatus;
use App\Models\Course;
use App\Models\CourseReview;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CourseReviewRepository
{
    public function isPublished(Course $course): bool
    {
        return $course->status === 'published'
            && $course->published_at !== null
            && $course->published_at->isPast();
    }

    public function hasActiveEnrollment(User $user, Course $course): bool
    {
        return Enrollment::query()
            ->whereBelongsTo($user)
            ->whereBelongsTo($course)
            ->where('status', EnrollmentStatus::Active)
            ->exists();
    }

    public function visibleForCourse(Course $course, int $perPage): LengthAwarePaginator
    {
        return CourseReview::query()
            ->whereBelongsTo($course)
            ->whereNull('hidden_at')
            ->with('user.student')
            ->latest('id')
            ->paginate($perPage);
    }

    public function owned(User $user, Course $course): ?CourseReview
    {
        return CourseReview::query()
            ->whereBelongsTo($user)
            ->whereBelongsTo($course)
            ->with(['user.student', 'course'])
            ->first();
    }

    public function create(User $user, Course $course, array $data, string $code): CourseReview
    {
        return CourseReview::create([
            'code' => $code,
            'user_id' => $user->id,
            'course_id' => $course->id,
            'rating' => $data['rating'],
            'review_text' => $data['review_text'] ?? null,
        ])->load(['user.student', 'course']);
    }

    public function update(CourseReview $review, array $data): CourseReview
    {
        $review->update($data);

        return $review->refresh()->load(['user.student', 'course']);
    }

    public function delete(CourseReview $review): void
    {
        $review->delete();
    }

    public function paginateAdmin(array $filters): LengthAwarePaginator
    {
        return CourseReview::query()
            ->with(['user.student', 'course'])
            ->when($filters['visibility'] ?? null, function ($query, string $visibility) {
                $visibility === 'visible' ? $query->whereNull('hidden_at') : $query->whereNotNull('hidden_at');
            })
            ->when($filters['course_slug'] ?? null, fn ($query, $slug) => $query->whereHas('course', fn ($course) => $course->where('slug', $slug)))
            ->when($filters['rating'] ?? null, fn ($query, $rating) => $query->where('rating', $rating))
            ->latest('id')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function loadAdmin(CourseReview $review): CourseReview
    {
        return $review->loadMissing(['user.student', 'course']);
    }

    public function setHidden(CourseReview $review, bool $hidden): CourseReview
    {
        $review->update(['hidden_at' => $hidden ? now() : null]);

        return $this->loadAdmin($review->refresh());
    }
}
