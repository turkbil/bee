<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;

class CheckModuleRoutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:module-routes {--module= : Belirli bir modül için filtrele}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tüm modül rotalarını ve middleware\'lerini listeler';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $module = $this->option('module');
        
        // Tüm rotaları al
        $routes = Route::getRoutes();
        
        $rows = [];
        
        foreach ($routes as $route) {
            // Module middleware kontrolü
            $middlewares = $route->middleware();
            $moduleMiddleware = $this->findModuleMiddleware($middlewares);
            
            // Eğer modül filtresi varsa ve eşleşmiyorsa atla
            if ($module && $moduleMiddleware && !str_contains($moduleMiddleware, $module)) {
                continue;
            }
            
            // Rota bilgilerini al
            $methods = implode('|', $route->methods());
            $uri = $route->uri();
            $name = $route->getName() ?: '';
            $action = $route->getActionName();
            
            if (str_starts_with($uri, 'admin/') || str_contains($name, 'admin.')) {
                // Middleware bilgisini oluştur
                $middlewareStr = $moduleMiddleware ?: 'YETKİLENDİRME YOK!';
                
                // Renkler
                $hasPermission = $moduleMiddleware !== null;
                $middlewareStr = $hasPermission 
                    ? "<fg=green>{$middlewareStr}</>" 
                    : "<fg=red>{$middlewareStr}</>";
                
                $rows[] = [$methods, $uri, $name, $middlewareStr, $action];
            }
        }
        
        // Tabloyu göster
        $this->table(
            ['HTTP Metod', 'URL', 'Rota Adı', 'Modül Middleware', 'Controller/Action'],
            $rows
        );
        
        // Özet bilgi
        $totalRoutes = count($rows);
        $protectedRoutes = count(array_filter($rows, function($row) {
            return !str_contains($row[3], 'YETKİLENDİRME YOK');
        }));
        
        $this->info("Toplam Admin Rotası: {$totalRoutes}");
        $this->info("Korunan Rota Sayısı: {$protectedRoutes}");
        $this->info("Korumasız Rota Sayısı: " . ($totalRoutes - $protectedRoutes));
    }
    
    /**
     * Middleware listesinden modül middleware'ini bul
     */
    private function findModuleMiddleware(array $middlewares): ?string
    {
        foreach ($middlewares as $middleware) {
            if (str_starts_with($middleware, 'tenant.module:')) {
                return $middleware;
            }
        }
        
        return null;
    }
}