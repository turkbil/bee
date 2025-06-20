<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\ThemeService;

class CheckThemeStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $themeService = app(ThemeService::class);
            $activeTheme = $themeService->getActiveTheme();
            
            \Log::info('CheckThemeStatus Middleware - Theme Status:', [
                'url' => $request->url(),
                'theme_name' => $activeTheme ? $activeTheme->name : 'null',
                'is_active' => $activeTheme ? $activeTheme->is_active : 'null'
            ]);
            
            // Tema bulunamadı veya aktif değil
            if (!$activeTheme || !$activeTheme->is_active) {
                \Log::warning('CheckThemeStatus - Site offline, redirecting to maintenance page');
                return response()->view('errors.theme-offline', [
                    'message' => 'Site tamamen bakım modunda. Lütfen daha sonra tekrar deneyiniz.'
                ], 503);
            }
            
        } catch (\Exception $e) {
            \Log::error('CheckThemeStatus Error: ' . $e->getMessage());
            return response()->view('errors.theme-offline', [
                'message' => 'Site geçici olarak kullanılamıyor. Lütfen daha sonra tekrar deneyiniz.'
            ], 503);
        }

        return $next($request);
    }
}
