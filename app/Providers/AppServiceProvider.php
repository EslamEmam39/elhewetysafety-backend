<?php

namespace App\Providers;

use App\Events\UserRegistered;
use App\Events\UserEmailVerified;
use App\Listeners\QueueEmailVerificationOtp;
use App\Listeners\QueuePasswordResetOtp;
use App\Models\User;
use App\Models\City;
use App\Models\Country;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Chapter;
use App\Models\Lesson;
use App\Observers\UserObserver;
use App\Policies\CityPolicy;
use App\Policies\CountryPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use App\Policies\CoursePolicy;
use App\Policies\CourseCategoryPolicy;
use App\Policies\ChapterPolicy;
use App\Policies\LessonPolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
        User::observe(UserObserver::class);

        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Permission::class, PermissionPolicy::class);
        Gate::policy(Country::class, CountryPolicy::class);
        Gate::policy(City::class, CityPolicy::class);
        Gate::policy(CourseCategory::class, CourseCategoryPolicy::class);
        Gate::policy(Course::class, CoursePolicy::class);
        Gate::policy(Chapter::class, ChapterPolicy::class);
        Gate::policy(Lesson::class, LessonPolicy::class);

        Event::listen(UserRegistered::class, QueueEmailVerificationOtp::class);
        Event::listen(UserEmailVerified::class, QueuePasswordResetOtp::class);
    }
}
