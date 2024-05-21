<?php

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::group(['prefix' => '/auth'], function () {
    Route::post('/login', [UserController::class, 'login']);
    Route::middleware('guest:sanctum') -> post('/register', [UserController::class, 'register']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/out', [UserController::class, 'out']);
        Route::get('/me', [UserController::class, 'me']);
        Route::get('/tokens', [UserController::class, 'getTokens']);
        Route::post('/out_all', [UserController::class, 'out_all']);
    });

    Route::prefix('ref')->group(function () {
        Route::prefix('policy')->group(function () {
            Route::prefix('role')->group(function () {
                Route::get('', [RoleController::class, 'getCollectionRoles']);
                Route::post('', [RoleController::class, 'createRole']);

                Route::prefix('{id}')->group(function () {
                    Route::get('', [RoleController::class, 'getRole']);
                    Route::put('', [RoleController::class, 'updateRole']);
                    Route::delete('', [RoleController::class, 'deleteRoleHard']);
                    Route::delete('/soft', [RoleController::class, 'deleteRoleSoft']);
                    Route::post('/restore', [RoleController::class, 'restoreSoftDeletedRole']);
                });
            });


            Route::prefix('permission')->group(function () {
                Route::get('', [PermissionController::class, 'getCollectionPermissions']);
                Route::post('', [PermissionController::class, 'createPermission']);

                Route::prefix('{id}')->group(function () {
                    Route::get('', [PermissionController::class, 'getPermission']);
                    Route::put('', [PermissionController::class, 'updatePermission']);
                    Route::delete('', [PermissionController::class, 'deletePermissionHard']);
                    Route::delete('/soft', [PermissionController::class, 'deletePermissionSoft']);
                    Route::post('/restore', [PermissionController::class, 'restoreSoftDeletedPermission']);
                });
            });
        });

        Route::prefix('user')->group(function () {
            Route::get('', [UserAndRoleController::class, 'getCollectionUsersAndRoles']);

            Route::prefix('{id}/role')->group(function () {
                Route::post('', [UserAndRoleController::class, 'createUserAndRole']);
                Route::get('', [UserAndRoleController::class, 'getCollectionUserAndRoles']);

                Route::prefix('{id}')->group(function () {
                    Route::put('', [UserAndRoleController::class, 'updateUserAndRole']);
                    Route::delete('', [UserAndRoleController::class, 'deleteUserAndRoleHard']);
                    Route::delete('/soft', [UserAndRoleController::class, 'deleteUserAndRoleSoft']);
                    Route::post('/restore', [UserAndRoleController::class, 'restoreSoftDeletedUserAndRole']);
                });
            });
        });

    });
});


