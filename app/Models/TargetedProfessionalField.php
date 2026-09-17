<?php

namespace App\Models;

use Database\Factories\TargetedProfessionalFieldFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class TargetedProfessionalField extends Model
{
    /** @use HasFactory<TargetedProfessionalFieldFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'name_en',
        'name_ar',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $field): void {
            if (blank($field->getAttribute('uuid'))) {
                $field->setAttribute('uuid', (string) Str::uuid());
            }
        });
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}