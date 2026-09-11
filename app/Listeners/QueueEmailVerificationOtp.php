<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Jobs\SendEmailVerificationOtp;

class QueueEmailVerificationOtp
{
    public function handle(UserRegistered $event): void
    {
        SendEmailVerificationOtp::dispatch($event->userId);
    }
}
