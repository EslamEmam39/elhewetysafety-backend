<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Course extends Model
{
    protected $fillable = ['course_category_id', 'instructor_id', 'title', 'slug', 'description', 'price', 'currency', 'level', 'thumbnail_storage_key', 'status', 'published_at'];

    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'published_at' => 'datetime'];
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail_storage_key
            ? Storage::disk((string) config('course.thumbnail_disk'))->url($this->thumbnail_storage_key)
            : null;
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class)->orderBy('sort_order');
    }

    public function getRouteKeyName(): string { return 'slug'; }
    public function category(): BelongsTo { return $this->belongsTo(CourseCategory::class, 'course_category_id'); }
    public function instructor(): BelongsTo { return $this->belongsTo(User::class, 'instructor_id'); }
    public function orders(): HasMany { return $this->hasMany(Order::class); }
    public function enrollments(): HasMany { return $this->hasMany(Enrollment::class); }
    public function lessons(): HasManyThrough { return $this->hasManyThrough(Lesson::class, Chapter::class); }
    public function reviews(): HasMany { return $this->hasMany(CourseReview::class); }
    public function visibleReviews(): HasMany { return $this->reviews()->whereNull('hidden_at'); }
}
