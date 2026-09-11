<?php

namespace App\Listeners;

use App\Events\UserEmailVerified;
use App\Jobs\SendPasswordResetOtp;

class QueuePasswordResetOtp
{
    public function handle(UserEmailVerified $event): void
    {
        SendPasswordResetOtp::dispatch($event->userId);
    }
}
