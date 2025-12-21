<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\Auth\DeviceService;

class CheckDeviceLimit
{
    protected DeviceService $deviceService;

    public function __construct(DeviceService $deviceService)
    {
        $this->deviceService = $deviceService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 🔴 DEVRE DIŞI: Bu middleware eskidir ve sessions tablosu kullanır
        // Sistem Redis session kullanıyor, sessions tablosu boş
        // Yeni device limit sistemi: Modules\Muzibu\App\Services\DeviceService (login'de çalışır)
        return $next($request);

        /* ESKI KOD (Redis uyumsuz - sessions tablosu boş)
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Check device limit
        if (!$this->deviceService->checkDeviceLimit($user)) {
            // Redirect to device management page
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Cihaz limitinizi aştınız. Lütfen bir cihazı çıkartın.',
                    'error' => 'device_limit_exceeded',
                    'redirect' => route('profile.devices'),
                ], 403);
            }

            return redirect()
                ->route('profile.devices')
                ->with('error', 'Cihaz limitinizi aştınız. Lütfen bir cihazı çıkartın.');
        }

        return $next($request);
        */
    }
}
