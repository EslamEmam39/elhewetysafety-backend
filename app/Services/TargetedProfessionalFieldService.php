<?php

namespace App\Services;

use App\Repositories\TargetedProfessionalFieldRepository;
use Illuminate\Database\Eloquent\Collection;

class TargetedProfessionalFieldService
{
    public function __construct(
        private readonly TargetedProfessionalFieldRepository $repository
    ) {}

    public function activeFields(): Collection
    {
        return $this->repository->activeFields();
    }
}