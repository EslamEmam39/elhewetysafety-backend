<?php

namespace App\Repositories;

use App\Models\TargetedProfessionalField;
use Illuminate\Database\Eloquent\Collection;

class TargetedProfessionalFieldRepository
{
    public function activeFields(): Collection
    {
        return TargetedProfessionalField::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public function findActiveByUuid(string $uuid): ?TargetedProfessionalField
    {
        return TargetedProfessionalField::query()
            ->where('uuid', $uuid)
            ->where('is_active', true)
            ->first();
    }
}