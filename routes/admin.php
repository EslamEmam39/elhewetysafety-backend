<?php

use App\Http\Controllers\Api\Admin\AuthorizationAssignmentController;
use App\Http\Controllers\Api\Admin\PermissionController;
use App\Http\Controllers\Api\Admin\RoleController;
use App\Http\Controllers\Api\Admin\UserModerationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'not_banned', 'role:admin'])
    ->prefix('admin')
    ->group(function () {
        Route::apiResource('roles', RoleController::class);
        Route::apiResource('permissions', PermissionController::class);

        Route::put('/roles/{role}/permissions', [AuthorizationAssignmentController::class, 'syncRolePermissions']);
        Route::put('/users/{user}/roles', [AuthorizationAssignmentController::class, 'syncUserRoles']);

        Route::put('/users/{user:uuid}/ban', [UserModerationController::class, 'ban']);
        Route::delete('/users/{user:uuid}/ban', [UserModerationController::class, 'unban']);
    });