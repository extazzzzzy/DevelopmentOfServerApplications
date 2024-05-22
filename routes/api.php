<?php

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleAndPermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserAndRoleController;
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

            Route::prefix('user_and_role')->group(function () {
                Route::get('', [UserAndRoleController::class, 'getCollectionUsersAndRoles']);

                Route::prefix('user/{user_id}/role')->group(function () {
                    Route::post('', [UserAndRoleController::class, 'createUserAndRole']);
                    Route::get('', [UserAndRoleController::class, 'getCollectionUserAndRoles']);

                    Route::prefix('{role_id}')->group(function () {
                        Route::delete('', [UserAndRoleController::class, 'deleteUserAndRoleHard']);
                        Route::delete('/soft', [UserAndRoleController::class, 'deleteUserAndRoleSoft']);
                        Route::post('/restore', [UserAndRoleController::class, 'restoreSoftDeletedUserAndRole']);
                    });
                });
            });

            Route::prefix('role_and_permission')->group(function () {
                Route::get('', [RoleAndPermissionController::class, 'getCollectionRolesAndPermissions']);

                Route::prefix('role/{role_id}/permission')->group(function () {
                    Route::post('', [RoleAndPermissionController::class, 'createRoleAndPermission']);
                    Route::get('', [RoleAndPermissionController::class, 'getCollectionRoleAndPermissions']);

                    Route::prefix('{permission_id}')->group(function () {
                        Route::delete('', [RoleAndPermissionController::class, 'deleteRoleAndPermissionHard']);
                        Route::delete('/soft', [RoleAndPermissionController::class, 'deleteRoleAndPermissionSoft']);
                        Route::post('/restore', [RoleAndPermissionController::class, 'restoreSoftDeletedRoleAndPermission']);
                    });
                });
            });
        });



    });
});


