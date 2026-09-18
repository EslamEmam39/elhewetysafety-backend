<?php

namespace App\Services\User;

use App\Exceptions\Auth\TargetedProfessionalFieldUnavailableException;
use App\Models\User;
use App\Repositories\User\AdminUserRepository;
use App\Repositories\ProfessionalField\TargetedProfessionalFieldRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AdminUserService
{
    public function __construct(
        private readonly AdminUserRepository $userRepository,
        private readonly TargetedProfessionalFieldRepository $fieldRepository
    ) {}

    public function users(array $filters): LengthAwarePaginator
    {
        return $this->userRepository->paginate($filters);
    }

    public function user(User $user): User
    {
        return $this->userRepository->load($user);
    }

    public function update(User $user, array $data): User
    {
        if (array_key_exists('targeted_professional_field_code', $data)) {
            $field = $this->fieldRepository->findActiveByCode($data['targeted_professional_field_code']);

            if (! $field) {
                throw new TargetedProfessionalFieldUnavailableException;
            }

            $data['targeted_professional_field_id'] = $field->id;
            unset($data['targeted_professional_field_code']);
        }

        if (isset($data['email']) && $data['email'] !== $user->email) {
            $data['email_verified_at'] = null;
        }

        return $this->userRepository->update($user, $data);
    }
}
