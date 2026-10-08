<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\EmailVerificationController;
use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Controllers\Api\Course\CourseCategoryController;
use App\Http\Controllers\Api\Course\CourseController;
use App\Http\Controllers\Api\Location\LocationController;
use App\Http\Controllers\Api\PaymentGateway\PaymentGatewayController;
use App\Http\Controllers\Api\Order\OrderController;
use App\Http\Controllers\Api\Payment\PaymentController;
use App\Http\Controllers\Api\Enrollment\EnrollmentController;
use App\Http\Controllers\Api\Device\DeviceController;
use App\Http\Controllers\Api\LessonProgress\LessonProgressController;
use App\Http\Controllers\Api\CourseReview\CourseReviewController;
use App\Http\Controllers\Api\ProfessionalField\TargetedProfessionalFieldController;
use App\Http\Controllers\Api\Student\StudentProfileController;
use App\Http\Controllers\Api\Video\LessonPlaybackController;
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

// Authenticated student order routes.
Route::middleware(['auth:sanctum', 'not_banned', 'role:student'])
    ->prefix('orders')
    ->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::post('/', [OrderController::class, 'store']);
        Route::get('{order:code}', [OrderController::class, 'show']);
        Route::delete('{order:code}', [OrderController::class, 'destroy']);
        Route::get('{order:code}/payments', [PaymentController::class, 'index']);
        Route::post('{order:code}/payments', [PaymentController::class, 'store']);
    });
Route::middleware(['auth:sanctum','not_banned','role:student'])->get('payments/{payment:code}',[PaymentController::class,'show']);

// Authenticated student enrollment and protected course content routes.
Route::middleware(['auth:sanctum', 'not_banned', 'role:student'])->group(function () {
    Route::get('enrollments', [EnrollmentController::class, 'index']);
    Route::get('enrollments/{enrollment:code}', [EnrollmentController::class, 'show']);
    Route::post('courses/{course:slug}/enroll', [EnrollmentController::class, 'store']);
    Route::get('courses/{course:slug}/content', [EnrollmentController::class, 'content']);
    Route::get('courses/{course:slug}/progress', [LessonProgressController::class, 'index']);
    Route::put('lessons/{lesson:code}/progress', [LessonProgressController::class, 'update']);
    Route::get('lessons/{lesson:code}/playback', [LessonPlaybackController::class, 'show']);
    Route::get('courses/{course:slug}/reviews/me', [CourseReviewController::class, 'mine']);
    Route::post('courses/{course:slug}/reviews', [CourseReviewController::class, 'store']);
    Route::patch('courses/{course:slug}/reviews/me', [CourseReviewController::class, 'update']);
    Route::delete('courses/{course:slug}/reviews/me', [CourseReviewController::class, 'destroy']);
});

// Authenticated user device management routes.
Route::middleware(['auth:sanctum', 'not_banned'])->prefix('devices')->group(function () {
    Route::get('/', [DeviceController::class, 'index']);
    Route::delete('{device:uuid}', [DeviceController::class, 'destroy']);
});

// Public course catalog routes.
Route::prefix('courses')->group(function () {
    Route::get('/', [CourseController::class, 'index']);
    Route::get('{course:slug}/reviews', [CourseReviewController::class, 'index']);
    Route::get('{slug}', [CourseController::class, 'show']);
});

// Public registration reference routes.
Route::get('targeted-professional-fields', [TargetedProfessionalFieldController::class, 'index']);
Route::get('course-categories', [CourseCategoryController::class, 'index']);
Route::get('payment-gateways', [PaymentGatewayController::class, 'index']);

// Public location reference routes.
Route::prefix('countries')->group(function () {
    Route::get('/', [LocationController::class, 'countries']);
    Route::get('{country:code}/cities', [LocationController::class, 'cities']);
});

// Admin-only routes.
require __DIR__ . '/admin.php';
