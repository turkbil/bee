<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Exceptions\Handler;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // InitializeTenancy'yi web middleware grubuna eklemiyoruz
        // Bunun yerine sadece alias olarak tanımlıyoruz
        
        $middleware->alias([
            'tenant' => \App\Http\Middleware\InitializeTenancy::class,
            'root.access' => \App\Http\Middleware\RootAccessMiddleware::class,
            'admin.access' => \App\Http\Middleware\AdminAccessMiddleware::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'module.permission' => \Modules\UserManagement\App\Http\Middleware\ModulePermissionMiddleware::class,
        ]);
                
        // Admin middleware grubu
        $middleware->group('admin', [
            'web',
            'auth',
            'tenant',
            'admin.access',
        ]);
                
        // Module middleware grupları - her modül için yetki kontrolü
        $modules = [];
        if (is_dir(base_path('Modules'))) {
            $modules = array_diff(scandir(base_path('Modules')), ['.', '..']);
        }

        foreach ($modules as $module) {
            $moduleName = strtolower($module);
            $middleware->group('module.' . $moduleName, [
                'web',
                'auth',
                'tenant', // Sadece modül gruplarında tenant middleware'ini kullanıyoruz
                'module.permission:' . $moduleName . ',view'
            ]);
        }

    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (TokenMismatchException $e, $request) {
            return redirect()->route('login');
        });
    })->create();