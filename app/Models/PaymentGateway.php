<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentGateway extends Model
{
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
        static::saving(function (self $gateway): void {
            $gateway->code = strtolower($gateway->code);
        });
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }
}
