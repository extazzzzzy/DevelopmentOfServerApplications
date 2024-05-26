<?php

use App\Http\Controllers\ChangeLogController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleAndPermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserAndRoleController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Lab2

Route::group(['prefix' => '/auth'], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/login/confirm2fa', [AuthController::class, 'confirm2FACode']);
    Route::post('/login/resend2fa', [AuthController::class, 'resendCode']);
    Route::middleware('guest:sanctum') -> post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/out', [AuthController::class, 'out']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::get('/tokens', [AuthController::class, 'getTokens']);
        Route::post('/out_all', [AuthController::class, 'out_all']);
    });

    // Lab3
    Route::prefix('ref')->group(function () {
        Route::get('/story', [ChangeLogController::class, 'getCollectionLogs'])->middleware('check.permission:get-story-collection'); // Lab4
        Route::post('/story/{id}/restore', [ChangeLogController::class, 'restore'])->middleware('check.permission:update-story'); // Lab4

        Route::prefix('policy')->group(function () {
            Route::prefix('role')->group(function () {
                Route::get('', [RoleController::class, 'getCollectionRoles'])->middleware('check.permission:get-list-role');
                Route::post('', [RoleController::class, 'createRole'])->middleware('check.permission:create-role');

                Route::prefix('{id}')->group(function () {
                    Route::get('/story', [ChangeLogController::class, 'getRoleLogs'])->middleware('check.permission:get-story-role'); // Lab4

                    Route::get('', [RoleController::class, 'getRole'])->middleware('check.permission:get-role');
                    Route::put('', [RoleController::class, 'updateRole'])->middleware('check.permission:update-role');
                    Route::delete('', [RoleController::class, 'deleteRoleHard'])->middleware('check.permission:delete-role');
                    Route::delete('/soft', [RoleController::class, 'deleteRoleSoft'])->middleware('check.permission:delete-role');
                    Route::post('/restore', [RoleController::class, 'restoreSoftDeletedRole'])->middleware('check.permission:restore-role');
                });
            });


            Route::prefix('permission')->group(function () {
                Route::get('', [PermissionController::class, 'getCollectionPermissions'])->middleware('check.permission:get-list-permission');
                Route::post('', [PermissionController::class, 'createPermission'])->middleware('check.permission:create-permission');

                Route::prefix('{id}')->group(function () {
                    Route::get('/story', [ChangeLogController::class, 'getPermissionLogs'])->middleware('check.permission:get-story-permission'); // Lab4

                    Route::get('', [PermissionController::class, 'getPermission'])->middleware('check.permission:get-permission');
                    Route::put('', [PermissionController::class, 'updatePermission'])->middleware('check.permission:update-permission');
                    Route::delete('', [PermissionController::class, 'deletePermissionHard'])->middleware('check.permission:delete-permission');
                    Route::delete('/soft', [PermissionController::class, 'deletePermissionSoft'])->middleware('check.permission:delete-permission');
                    Route::post('/restore', [PermissionController::class, 'restoreSoftDeletedPermission'])->middleware('check.permission:restore-permission');
                });
            });

            Route::prefix('user_and_role')->group(function () {
                Route::get('', [UserAndRoleController::class, 'getCollectionUsersAndRoles'])->middleware('check.permission:get-list-user_and_role');

                Route::prefix('user/{user_id}/role')->group(function () {
                    Route::post('', [UserAndRoleController::class, 'createUserAndRole'])->middleware('check.permission:create-user_and_role');
                    Route::get('', [UserAndRoleController::class, 'getCollectionUserAndRoles'])->middleware('check.permission:get-user_and_role');

                    Route::prefix('{role_id}')->group(function () {
                        Route::delete('', [UserAndRoleController::class, 'deleteUserAndRoleHard'])->middleware('check.permission:delete-user_and_role');
                        Route::delete('/soft', [UserAndRoleController::class, 'deleteUserAndRoleSoft'])->middleware('check.permission:delete-user_and_role');
                        Route::post('/restore', [UserAndRoleController::class, 'restoreSoftDeletedUserAndRole'])->middleware('check.permission:restore-user_and_role');
                    });
                });
            });

            Route::prefix('role_and_permission')->group(function () {
                Route::get('', [RoleAndPermissionController::class, 'getCollectionRolesAndPermissions'])->middleware('check.permission:get-list-role_and_permission');

                Route::prefix('role/{role_id}/permission')->group(function () {
                    Route::post('', [RoleAndPermissionController::class, 'createRoleAndPermission'])->middleware('check.permission:create-role_and_permission');
                    Route::get('', [RoleAndPermissionController::class, 'getCollectionRoleAndPermissions'])->middleware('check.permission:get-role_and_permission');

                    Route::prefix('{permission_id}')->group(function () {
                        Route::delete('', [RoleAndPermissionController::class, 'deleteRoleAndPermissionHard'])->middleware('check.permission:delete-role_and_permission');
                        Route::delete('/soft', [RoleAndPermissionController::class, 'deleteRoleAndPermissionSoft'])->middleware('check.permission:delete-role_and_permission');
                        Route::post('/restore', [RoleAndPermissionController::class, 'restoreSoftDeletedRoleAndPermission'])->middleware('check.permission:restore-role_and_permission');;
                    });
                });
            });
        });

        Route::prefix('user')->group(function () {

            Route::post('', [UserController::class, 'createUser'])->middleware('check.permission:create-user');

            Route::prefix('{user_id}')->group(function () {
                Route::get('/story', [ChangeLogController::class, 'getUserLogs'])->middleware('check.permission:get-story-user'); // Lab4

                Route::get('', [UserController::class, 'getUser'])->middleware('check.permission:get-user');
                Route::put('', [UserController::class, 'updateUser'])->middleware('check.permission:update-user');
                Route::delete('', [UserController::class, 'deleteUserHard'])->middleware('check.permission:delete-user');
                Route::delete('/soft', [UserController::class, 'deleteUserSoft'])->middleware('check.permission:delete-user');
                Route::post('/restore', [UserController::class, 'restoreSoftDeletedUser'])->middleware('check.permission:restore-user');
            });
        });

    });
});
Route::get('/ref/user', [UserController::class, 'getCollectionUsers'])->middleware('check.permission:get-list-user'); // Lab4




