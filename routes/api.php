<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AuthorizationAssignmentController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [PasswordResetController::class, 'forgot'])
    ->middleware('throttle:6,1');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])
    ->middleware('throttle:6,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/email/verification/resend', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:3,1');
    Route::post('/email/verification/verify', [EmailVerificationController::class, 'verify'])
        ->middleware('throttle:6,1');

    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::apiResource('roles', RoleController::class);
        Route::apiResource('permissions', PermissionController::class);
        Route::put('/roles/{role}/permissions', [AuthorizationAssignmentController::class, 'syncRolePermissions']);
        Route::put('/users/{user}/roles', [AuthorizationAssignmentController::class, 'syncUserRoles']);
    });
});
