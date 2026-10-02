<?php

namespace App\Services\CourseReview;

use App\Exceptions\CourseReview\CourseAlreadyReviewedException;
use App\Exceptions\Enrollment\EnrollmentAccessDeniedException;
use App\Models\Course;
use App\Models\CourseReview;
use App\Models\User;
use App\Repositories\CourseReview\CourseReviewRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CourseReviewService
{
    public function __construct(private readonly CourseReviewRepository $repository) {}

    public function publicReviews(Course $course, int $perPage): LengthAwarePaginator
    {
        abort_unless($this->repository->isPublished($course), 404);

        return $this->repository->visibleForCourse($course, $perPage);
    }

    public function myReview(User $user, Course $course): CourseReview
    {
        return $this->repository->owned($user, $course) ?? abort(404);
    }

    public function create(User $user, Course $course, array $data): CourseReview
    {
        return DB::transaction(function () use ($user, $course, $data): CourseReview {
            $this->ensureCanReview($user, $course);

            if ($this->repository->owned($user, $course)) {
                throw new CourseAlreadyReviewedException;
            }

            return $this->repository->create(
                $user,
                $course,
                $data,
                'REV-'.Str::upper((string) Str::ulid())
            );
        });
    }

    public function update(User $user, Course $course, array $data): CourseReview
    {
        $this->ensureCanReview($user, $course);
        $review = $this->repository->owned($user, $course) ?? abort(404);

        return $this->repository->update($review, $data);
    }

    public function delete(User $user, Course $course): void
    {
        $review = $this->repository->owned($user, $course) ?? abort(404);
        $this->repository->delete($review);
    }

    public function adminReviews(array $filters): LengthAwarePaginator
    {
        return $this->repository->paginateAdmin($filters);
    }

    public function adminReview(CourseReview $review): CourseReview
    {
        return $this->repository->loadAdmin($review);
    }

    public function hide(CourseReview $review): CourseReview
    {
        return $this->repository->setHidden($review, true);
    }

    public function show(CourseReview $review): CourseReview
    {
        return $this->repository->setHidden($review, false);
    }

    private function ensureCanReview(User $user, Course $course): void
    {
        abort_unless($this->repository->isPublished($course), 404);

        if (! $this->repository->hasActiveEnrollment($user, $course)) {
            throw new EnrollmentAccessDeniedException;
        }
    }
}
