<?php
namespace App\Models;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
class Payment extends Model
{
    protected $fillable = [
        'code',
        'order_id',
        'payment_gateway_id',
        'amount',
        'currency',
        'status',
        'idempotency_key',
        'provider_reference',
        'failure_reason',
        'paid_at',
        'refunded_at',
        'cash_confirmed_by_user_id'
    ];
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'status' => PaymentStatus::class,
            'paid_at' => 'datetime',
            'refunded_at' => 'datetime'
        ];
    }
    public function getRouteKeyName(): string
    {
        return 'code';
    }
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    public function gateway(): BelongsTo
    {
        return $this->belongsTo(PaymentGateway::class, 'payment_gateway_id');
    }
    public function enrollment(): HasOne
    {
        return $this->hasOne(Enrollment::class);
    }
    public function cashConfirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cash_confirmed_by_user_id');
    }
}
