<?php

use App\Http\Controllers\Api\Admin\Authorization\AuthorizationAssignmentController;
use App\Http\Controllers\Api\Admin\Authorization\PermissionController;
use App\Http\Controllers\Api\Admin\Authorization\RoleController;
use App\Http\Controllers\Api\Admin\Course\ChapterController;
use App\Http\Controllers\Api\Admin\Course\CourseCategoryController;
use App\Http\Controllers\Api\Admin\Course\CourseController;
use App\Http\Controllers\Api\Admin\Course\LessonController;
use App\Http\Controllers\Api\Admin\Location\CityController;
use App\Http\Controllers\Api\Admin\Location\CountryController;
use App\Http\Controllers\Api\Admin\Student\StudentController;
use App\Http\Controllers\Api\Admin\User\UserController;
use App\Http\Controllers\Api\Admin\User\UserModerationController;
use App\Http\Controllers\Api\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    // Public admin authentication route.
    Route::post('login', [AuthController::class, 'adminLogin'])->middleware('throttle:6,1');

    // Protected administration routes.
    Route::middleware(['auth:sanctum', 'not_banned', 'role:admin'])->group(function () {
        // Roles and permissions management.
        Route::apiResource('roles', RoleController::class);
        Route::apiResource('permissions', PermissionController::class);
        Route::prefix('roles')->group(function () {
            Route::put('{role}/permissions', [AuthorizationAssignmentController::class, 'syncRolePermissions']);
        });

        // Location reference management.
        Route::apiResource('countries', CountryController::class);
        Route::apiResource('cities', CityController::class);

        // Course catalog management.
        Route::apiResource('course-categories', CourseCategoryController::class);
        Route::apiResource('courses', CourseController::class);
        Route::prefix('courses')->group(function () {
            Route::get('{course:slug}/chapters', [ChapterController::class, 'index']);
            Route::post('{course:slug}/chapters', [ChapterController::class, 'store']);
        });

        // Chapter management.
        Route::prefix('chapters')->group(function () {
            Route::get('{chapter:code}', [ChapterController::class, 'show']);
            Route::patch('{chapter:code}', [ChapterController::class, 'update']);
            Route::delete('{chapter:code}', [ChapterController::class, 'destroy']);
            Route::get('{chapter:code}/lessons', [LessonController::class, 'index']);
            Route::post('{chapter:code}/lessons', [LessonController::class, 'store']);
        });

        // Lesson management.
        Route::prefix('lessons')->group(function () {
            Route::get('{lesson:code}', [LessonController::class, 'show']);
            Route::patch('{lesson:code}', [LessonController::class, 'update']);
            Route::delete('{lesson:code}', [LessonController::class, 'destroy']);
        });

        // Student profile management.
        Route::prefix('students')->group(function () {
            Route::get('/', [StudentController::class, 'index']);
            Route::get('{user:uuid}', [StudentController::class, 'show']);
            Route::patch('{user:uuid}', [StudentController::class, 'update']);
            Route::delete('{user:uuid}', [StudentController::class, 'destroy']);
        });

        // User identity, authorization, and moderation management.
        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index']);
            Route::get('{user:uuid}', [UserController::class, 'show']);
            Route::patch('{user:uuid}', [UserController::class, 'update']);
            Route::put('{user:uuid}/roles', [AuthorizationAssignmentController::class, 'syncUserRoles']);
            Route::put('{user:uuid}/ban', [UserModerationController::class, 'ban']);
            Route::delete('{user:uuid}/ban', [UserModerationController::class, 'unban']);
        });
    });
});
