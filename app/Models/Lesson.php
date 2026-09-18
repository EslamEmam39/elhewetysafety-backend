<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lesson extends Model
{
    protected $fillable = ['chapter_id', 'code', 'title', 'sort_order', 'video_s3_key', 'video_hls_manifest_key', 'video_status', 'duration_seconds', 'is_free_preview'];

    protected function casts(): array { return ['is_free_preview' => 'boolean']; }
    public function getRouteKeyName(): string { return 'code'; }
    public function chapter(): BelongsTo { return $this->belongsTo(Chapter::class); }
}
