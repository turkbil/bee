<?php

namespace Modules\Studio\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Studio\App\Services\WidgetSecurityService;
use Modules\WidgetManagement\App\Models\Widget;
use Symfony\Component\HttpFoundation\Response;

class WidgetPermissionMiddleware
{
    protected WidgetSecurityService $widgetSecurity;

    public function __construct(WidgetSecurityService $widgetSecurity)
    {
        $this->widgetSecurity = $widgetSecurity;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $action = 'view'): Response
    {
        try {
            // Widget ID'sini request'ten al
            $widgetId = $this->extractWidgetId($request);
            
            if (!$widgetId) {
                Log::warning('Widget permission check failed: Widget ID not found', [
                    'url' => $request->fullUrl(),
                    'user_id' => auth()->id()
                ]);
                return $this->unauthorizedResponse('Widget ID not found');
            }

            // User authentication kontrolü
            if (!auth()->check()) {
                Log::warning('Widget access attempt without authentication', [
                    'widget_id' => $widgetId,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
                return $this->unauthorizedResponse('Authentication required');
            }

            // Widget'ın var olup olmadığını kontrol et
            if (!$this->widgetExists($widgetId)) {
                Log::warning('Widget permission check failed: Widget not found', [
                    'widget_id' => $widgetId,
                    'user_id' => auth()->id()
                ]);
                return $this->notFoundResponse('Widget not found');
            }

            // Permission kontrolü
            if (!$this->widgetSecurity->checkWidgetPermission($widgetId, $action)) {
                Log::warning('Widget permission denied', [
                    'widget_id' => $widgetId,
                    'user_id' => auth()->id(),
                    'action' => $action,
                    'ip' => $request->ip()
                ]);
                return $this->forbiddenResponse('Insufficient permissions for widget access');
            }

            // Widget aktif mi kontrol et
            if (!$this->isWidgetActive($widgetId)) {
                Log::info('Inactive widget access attempt', [
                    'widget_id' => $widgetId,
                    'user_id' => auth()->id()
                ]);
                return $this->notFoundResponse('Widget not available');
            }

            // Rate limiting kontrolü
            if (!$this->checkRateLimit($request, $widgetId)) {
                Log::warning('Widget rate limit exceeded', [
                    'widget_id' => $widgetId,
                    'user_id' => auth()->id(),
                    'ip' => $request->ip()
                ]);
                return $this->rateLimitResponse('Rate limit exceeded');
            }

            // Request'e widget bilgilerini ekle
            $request->merge([
                'validated_widget_id' => $widgetId,
                'widget_action' => $action
            ]);

            Log::debug('Widget permission granted', [
                'widget_id' => $widgetId,
                'user_id' => auth()->id(),
                'action' => $action
            ]);

            return $next($request);

        } catch (\Exception $e) {
            Log::error('Widget permission middleware error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => auth()->id()
            ]);

            return $this->errorResponse('Permission check failed');
        }
    }

    /**
     * Request'ten widget ID'sini çıkar
     */
    private function extractWidgetId(Request $request): ?int
    {
        // Route parameter'dan al
        if ($request->route('widget_id')) {
            return (int) $request->route('widget_id');
        }

        if ($request->route('widgetId')) {
            return (int) $request->route('widgetId');
        }

        if ($request->route('id')) {
            return (int) $request->route('id');
        }

        // Request parameter'dan al
        if ($request->has('widget_id')) {
            return (int) $request->input('widget_id');
        }

        // POST body'den al
        if ($request->isMethod('post') && $request->has('widget_id')) {
            return (int) $request->input('widget_id');
        }

        // JSON body'den al
        if ($request->isJson()) {
            $json = $request->json();
            if ($json->has('widget_id')) {
                return (int) $json->get('widget_id');
            }
        }

        return null;
    }

    /**
     * Widget'ın var olup olmadığını kontrol et
     */
    private function widgetExists(int $widgetId): bool
    {
        try {
            return Widget::where('id', $widgetId)->exists();
        } catch (\Exception $e) {
            Log::error('Widget existence check failed', [
                'widget_id' => $widgetId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Widget'ın aktif olup olmadığını kontrol et
     */
    private function isWidgetActive(int $widgetId): bool
    {
        try {
            return Widget::where('id', $widgetId)
                ->where('is_active', true)
                ->exists();
        } catch (\Exception $e) {
            Log::error('Widget active check failed', [
                'widget_id' => $widgetId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Rate limiting kontrolü
     */
    private function checkRateLimit(Request $request, int $widgetId): bool
    {
        $key = 'widget_rate_limit:' . $widgetId . ':' . $request->ip();
        $maxAttempts = 60; // 60 requests per minute
        $decayMinutes = 1;

        $attempts = cache()->get($key, 0);
        
        if ($attempts >= $maxAttempts) {
            return false;
        }

        cache()->put($key, $attempts + 1, now()->addMinutes($decayMinutes));
        return true;
    }

    /**
     * Unauthorized response
     */
    private function unauthorizedResponse(string $message): Response
    {
        if (request()->expectsJson()) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => $message
            ], 401);
        }

        return response()->view('errors.401', ['message' => $message], 401);
    }

    /**
     * Forbidden response
     */
    private function forbiddenResponse(string $message): Response
    {
        if (request()->expectsJson()) {
            return response()->json([
                'error' => 'Forbidden',
                'message' => $message
            ], 403);
        }

        return response()->view('errors.403', ['message' => $message], 403);
    }

    /**
     * Not found response
     */
    private function notFoundResponse(string $message): Response
    {
        if (request()->expectsJson()) {
            return response()->json([
                'error' => 'Not Found',
                'message' => $message
            ], 404);
        }

        return response()->view('errors.404', ['message' => $message], 404);
    }

    /**
     * Rate limit response
     */
    private function rateLimitResponse(string $message): Response
    {
        if (request()->expectsJson()) {
            return response()->json([
                'error' => 'Rate Limit Exceeded',
                'message' => $message
            ], 429);
        }

        return response()->view('errors.429', ['message' => $message], 429);
    }

    /**
     * Error response
     */
    private function errorResponse(string $message): Response
    {
        if (request()->expectsJson()) {
            return response()->json([
                'error' => 'Internal Error',
                'message' => $message
            ], 500);
        }

        return response()->view('errors.500', ['message' => $message], 500);
    }
}