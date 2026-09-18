<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\EmailVerificationController;
use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Controllers\Api\Course\CourseCategoryController;
use App\Http\Controllers\Api\Course\CourseController;
use App\Http\Controllers\Api\Location\LocationController;
use App\Http\Controllers\Api\ProfessionalField\TargetedProfessionalFieldController;
use App\Http\Controllers\Api\Student\StudentProfileController;
use Illuminate\Support\Facades\Route;

// Authentication and account routes.
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::post('email/verification/resend', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:3,1');
    Route::post('email/verification/verify', [EmailVerificationController::class, 'verify'])
        ->middleware('throttle:6,1');

    Route::post('forgot-password', [PasswordResetController::class, 'forgot'])
        ->middleware('throttle:6,1');
    Route::post('reset-password', [PasswordResetController::class, 'reset'])
        ->middleware('throttle:6,1');

    Route::middleware(['auth:sanctum', 'not_banned'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::patch('profile', [StudentProfileController::class, 'update'])
            ->middleware('role:student');
    });
});

// Public course catalog routes.
Route::prefix('courses')->group(function () {
    Route::get('/', [CourseController::class, 'index']);
    Route::get('{slug}', [CourseController::class, 'show']);
});

// Public registration reference routes.
Route::get('targeted-professional-fields', [TargetedProfessionalFieldController::class, 'index']);
Route::get('course-categories', [CourseCategoryController::class, 'index']);

// Public location reference routes.
Route::prefix('countries')->group(function () {
    Route::get('/', [LocationController::class, 'countries']);
    Route::get('{country:code}/cities', [LocationController::class, 'cities']);
});

// Admin-only routes.
require __DIR__ . '/admin.php';
