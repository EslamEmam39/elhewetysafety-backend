<?php
namespace App\Models;
use App\Enums\EnrollmentSource;
use App\Enums\EnrollmentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Enrollment extends Model
{
    protected $fillable = [
        'code',
        'user_id',
        'course_id',
        'payment_id',
        'source',
        'status',
        'enrolled_at',
        'revoked_at'
    ];
    protected function casts(): array
    {
        return [
            'source' => EnrollmentSource::class,
            'status' => EnrollmentStatus::class,
            'enrolled_at' => 'datetime',
            'revoked_at' => 'datetime'
        ];
    }
    public function getRouteKeyName(): string
    {
        return 'code';
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
