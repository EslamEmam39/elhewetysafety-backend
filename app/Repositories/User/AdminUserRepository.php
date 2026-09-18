<?php

namespace App\Repositories\User;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AdminUserRepository
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        return User::query()
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
            ->when(array_key_exists('is_banned', $filters), fn ($query) => $query->where('is_banned', $filters['is_banned']))
            ->when($filters['role'] ?? null, fn ($query, string $role) => $query->role($role))
            ->latest('id')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function load(User $user): User
    {
        return $user->loadMissing(['roles', 'targetedProfessionalField', 'student.city.country']);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user->refresh()->load(['roles', 'targetedProfessionalField', 'student.city.country']);
    }
}
