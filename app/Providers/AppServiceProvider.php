<?php

namespace App\Providers;

use App\Events\UserRegistered;
use App\Events\UserEmailVerified;
use App\Listeners\QueueEmailVerificationOtp;
use App\Listeners\QueuePasswordResetOtp;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(UserRegistered::class, QueueEmailVerificationOtp::class);
        Event::listen(UserEmailVerified::class, QueuePasswordResetOtp::class);
    }
}
