<?php

namespace App\Repositories;

use App\Models\OtpCode;

class OtpRepository
{
    public function create(array $data): OtpCode
    {
        return OtpCode::create($data);
    }

    public function latestFor(string $email, string $purpose): ?OtpCode
    {
        return OtpCode::query()
            ->where('email', $email)
            ->where('purpose', $purpose)
            ->latest('id')
            ->first();
    }

    public function findActiveForUpdate(string $email, string $purpose): ?OtpCode
    {
        return OtpCode::query()
            ->where('email', $email)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->latest('id')
            ->lockForUpdate()
            ->first();
    }

    public function invalidateActive(string $email, string $purpose): void
    {
        OtpCode::query()
            ->where('email', $email)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->update(['consumed_at' => now()]);
    }

    public function incrementAttempts(OtpCode $otp): int
    {
        $otp->increment('attempts');

        return (int) $otp->refresh()->attempts;
    }

    public function consume(OtpCode $otp): void
    {
        $otp->update(['consumed_at' => now()]);
    }
}
