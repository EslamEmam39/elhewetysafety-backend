<?php

namespace App\Repositories\ProfessionalField;

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

    public function findActiveByCode(string $code): ?TargetedProfessionalField
    {
        return TargetedProfessionalField::query()
            ->where('code', $code)
            ->where('is_active', true)
            ->first();
    }
}
