<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = ['title', 'slug', 'description', 'price', 'currency', 'thumbnail', 'status', 'published_at'];

    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'published_at' => 'datetime'];
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class)->orderBy('sort_order');
    }
}
