<?php

namespace App\Repositories\Student;

use App\Models\Student;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AdminStudentRepository
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        return User::query()
            ->whereHas('student')
            ->with(['roles', 'targetedProfessionalField', 'student.city.country'])
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $term = '%'.$search.'%';
                $query->where(function ($query) use ($term): void {
                    $query->where('username', 'ilike', $term)
                        ->orWhere('email', 'ilike', $term)
                        ->orWhere('phone', 'ilike', $term)
                        ->orWhere('full_name_en', 'ilike', $term)
                        ->orWhere('full_name_ar', 'ilike', $term);
                });
            })
            ->when($filters['city_code'] ?? null, fn ($query, string $code) => $query
                ->whereHas('student.city', fn ($query) => $query->where('code', $code)))
            ->latest('users.id')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function load(User $user): User
    {
        $user->loadMissing(['roles', 'targetedProfessionalField', 'student.city.country']);

        if (! $user->student) {
            throw (new ModelNotFoundException)->setModel(Student::class);
        }

        return $user;
    }

    public function deleteProfile(User $user): void
    {
        $this->load($user)->student->delete();
    }
}
