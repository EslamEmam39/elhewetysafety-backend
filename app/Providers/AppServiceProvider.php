<?php

namespace App\Providers;

use App\Events\UserRegistered;
use App\Listeners\QueueEmailVerificationOtp;
use App\Models\User;
use App\Models\City;
use App\Models\Country;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Chapter;
use App\Models\Lesson;
use App\Models\PaymentGateway;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Enrollment;
use App\Models\PersonalAccessToken;
use App\Models\Device;
use App\Models\CourseReview;
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
use App\Policies\PaymentGatewayPolicy;
use App\Policies\OrderPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\EnrollmentPolicy;
use App\Policies\DevicePolicy;
use App\Policies\CourseReviewPolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Laravel\Sanctum\Sanctum;

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
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
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
        Gate::policy(PaymentGateway::class, PaymentGatewayPolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(Payment::class, PaymentPolicy::class);
        Gate::policy(Enrollment::class, EnrollmentPolicy::class);
        Gate::policy(Device::class, DevicePolicy::class);
        Gate::policy(CourseReview::class, CourseReviewPolicy::class);

        Event::listen(UserRegistered::class, QueueEmailVerificationOtp::class);
    }
}
