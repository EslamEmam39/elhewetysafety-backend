<?php
namespace App\Services\Enrollment;
use App\Enums\EnrollmentStatus;
use App\Exceptions\Enrollment\EnrollmentAccessDeniedException;
use App\Exceptions\Enrollment\PaidCourseRequiresPaymentException;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Repositories\Enrollment\EnrollmentRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class EnrollmentService
{
    public function __construct(private readonly EnrollmentRepository $repository) {}
    public function enrollFree(User $user, Course $course): Enrollment { return DB::transaction(function() use($user,$course): Enrollment { $course=$this->repository->lockPublishedCourse($course)??abort(404); if((float)$course->price>0){throw new PaidCourseRequiresPaymentException;} $existing=$this->repository->existing($user,$course); if($existing){return $this->repository->findForUser($user,$existing)??abort(404);} return $this->repository->createFree($user,$course,'ENR-'.Str::upper((string)Str::ulid())); }); }
    public function forUser(User $user, int $perPage): LengthAwarePaginator { return $this->repository->paginateForUser($user,$perPage); }
    public function enrollment(User $user, Enrollment $enrollment): Enrollment { return $this->repository->findForUser($user,$enrollment)??abort(404); }
    public function content(User $user, Course $course): Course { return $this->repository->contentFor($user,$course)??throw new EnrollmentAccessDeniedException; }
    public function adminEnrollments(array $filters): LengthAwarePaginator { return $this->repository->paginateAdmin($filters); }
    public function adminEnrollment(Enrollment $enrollment): Enrollment { return $this->repository->loadAdmin($enrollment); }
    public function revoke(Enrollment $enrollment): Enrollment { return $this->repository->setStatus($enrollment,EnrollmentStatus::Revoked); }
    public function activate(Enrollment $enrollment): Enrollment { return $this->repository->setStatus($enrollment,EnrollmentStatus::Active); }
}
