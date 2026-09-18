<?php

namespace App\Services\Student;

use App\Models\User;
use App\Repositories\Student\AdminStudentRepository;
use App\Services\Location\LocationService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AdminStudentService
{
    public function __construct(
        private readonly AdminStudentRepository $repository,
        private readonly LocationService $locationService
    ) {}

    public function students(array $filters): LengthAwarePaginator
    {
        return $this->repository->paginate($filters);
    }

    public function student(User $user): User
    {
        return $this->repository->load($user);
    }

    public function update(User $user, array $data): User
    {
        $user = $this->repository->load($user);
        $this->locationService->updateStudentProfile($user->student, $data);

        return $this->repository->load($user->refresh());
    }

    public function delete(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $this->repository->deleteProfile($user);
            $user->removeRole('student');
        });
    }
}
