<?php
namespace App\Repositories\Enrollment;
use App\Enums\EnrollmentSource;
use App\Enums\EnrollmentStatus;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
class EnrollmentRepository
{
    public function lockPublishedCourse(Course $course): ?Course
    {
        return Course::query()->whereKey($course->id)->where('status', 'published')->whereNotNull('published_at')->where('published_at', '<=', now())->lockForUpdate()->first();
    }
    public function findForUser(User $user, Enrollment $enrollment): ?Enrollment
    {
        return Enrollment::query()->whereKey($enrollment->id)->whereBelongsTo($user)->with(['course.category', 'course.instructor', 'payment'])->first();
    }
    public function existing(User $user, Course $course): ?Enrollment
    {
        return Enrollment::query()->whereBelongsTo($user)->whereBelongsTo($course)->lockForUpdate()->first();
    }
    public function createFree(User $user, Course $course, string $code): Enrollment
    {
        return Enrollment::create(['code' => $code, 'user_id' => $user->id, 'course_id' => $course->id, 'source' => EnrollmentSource::Free, 'status' => EnrollmentStatus::Active, 'enrolled_at' => now()])->load(['course.category', 'course.instructor']);
    }
    public function paginateForUser(User $user, int $perPage): LengthAwarePaginator
    {
        return Enrollment::query()->whereBelongsTo($user)->with(['course.category', 'course.instructor', 'payment'])->latest('id')->paginate($perPage);
    }
    public function contentFor(User $user, Course $course): ?Course
    {
        $active = Enrollment::query()->whereBelongsTo($user)->whereBelongsTo($course)->where('status', EnrollmentStatus::Active)->exists();
        return $active ? Course::query()->whereKey($course->id)->with(['category', 'instructor', 'chapters.lessons'])->first() : null;
    }
    public function paginateAdmin(array $filters): LengthAwarePaginator
    {
        return Enrollment::query()->with(['user.roles', 'course.category', 'course.instructor', 'payment'])->when($filters['status'] ?? null, fn($q, $v) => $q->where('status', $v))->when($filters['source'] ?? null, fn($q, $v) => $q->where('source', $v))->when($filters['user_uuid'] ?? null, fn($q, $v) => $q->whereHas('user', fn($q) => $q->where('uuid', $v)))->when($filters['course_slug'] ?? null, fn($q, $v) => $q->whereHas('course', fn($q) => $q->where('slug', $v)))->latest('id')->paginate($filters['per_page'] ?? 15);
    }
    public function loadAdmin(Enrollment $enrollment): Enrollment
    {
        return $enrollment->loadMissing(['user.roles', 'course.category', 'course.instructor', 'payment']);
    }
    public function setStatus(Enrollment $enrollment, EnrollmentStatus $status): Enrollment
    {
        $enrollment->update(['status' => $status, 'revoked_at' => $status === EnrollmentStatus::Revoked ? now() : null]);
        return $this->loadAdmin($enrollment->refresh());
    }
}
