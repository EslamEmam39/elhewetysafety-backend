<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\Auth\OtpService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPasswordResetOtp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly int $userId)
    {
        $this->afterCommit();
    }

    public function handle(OtpService $otpService): void
    {
        $user = User::find($this->userId);

        if ($user) {
            $otpService->sendPasswordResetOtp($user->email);
        }
    }
}
