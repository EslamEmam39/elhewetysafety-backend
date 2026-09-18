<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    protected $fillable = ['country_id', 'code', 'name_en', 'name_ar', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
}
