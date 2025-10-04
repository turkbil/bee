<?php
// Modules/UserManagement/routes/admin.php
use Illuminate\Support\Facades\Route;
use Modules\UserManagement\App\Http\Controllers\Admin\UserManagementController;

Route::middleware(['admin', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // User Routes
        Route::prefix('usermanagement')
            ->name('usermanagement.')
            ->group(function () {
                Route::get('/', [UserManagementController::class, 'index'])
                    ->middleware('module.permission:usermanagement,view')
                    ->name('index');

                // Kullanıcı yönetimi - id parametresi opsiyonel
                Route::get('/manage/{id?}', [UserManagementController::class, 'manage'])
                    ->middleware('module.permission:usermanagement,update')
                    ->name('manage');
                
                // Modül bazlı izinler
                Route::get('/module-permissions', [UserManagementController::class, 'modulePermissions'])
                    ->middleware('module.permission:usermanagement,update')
                    ->name('module.permissions');

                // Kullanıcı modül izinleri
                Route::get('/user-module-permissions/{id}', [UserManagementController::class, 'userModulePermissions'])
                    ->middleware('module.permission:usermanagement,update')
                    ->where('id', '[0-9]+')
                    ->name('user.module.permissions');

                // Aktivite log kayıtları
                Route::get('/activity-logs', [UserManagementController::class, 'activityLogs'])
                    ->middleware('module.permission:usermanagement,view')
                    ->name('activity.logs');

                // Kullanıcı aktivite log kayıtları
                Route::get('/user-activity-logs/{id}', [UserManagementController::class, 'userActivityLogs'])
                    ->middleware('module.permission:usermanagement,view')
                    ->where('id', '[0-9]+')
                    ->name('user.activity.logs');
            });

        // Role Routes
        Route::prefix('usermanagement/role')
            ->name('usermanagement.role.')
            ->group(function () {
                Route::get('/', [UserManagementController::class, 'roleIndex'])
                    ->middleware('module.permission:usermanagement,view')
                    ->name('index');

                // Rol yönetimi - id parametresi opsiyonel
                Route::get('/manage/{id?}', [UserManagementController::class, 'roleManage'])
                    ->middleware('module.permission:usermanagement,update')
                    ->name('manage');
            });

        // Permission Routes
        Route::prefix('usermanagement/permission')
            ->name('usermanagement.permission.')
            ->group(function () {
                Route::get('/', [UserManagementController::class, 'permissionIndex'])
                    ->middleware('module.permission:usermanagement,view')
                    ->name('index');

                // İzin yönetimi - id parametresi opsiyonel
                Route::get('/manage/{id?}', [UserManagementController::class, 'permissionManage'])
                    ->middleware('module.permission:usermanagement,update')
                    ->name('manage');
            });
    });