<?php

namespace Modules\Studio\App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Throwable;

class WidgetErrorHandlingService
{
    const FALLBACK_CACHE_TTL = 300; // 5 minutes
    const ERROR_THRESHOLD = 5; // Maximum errors before disabling widget
    const ERROR_WINDOW = 3600; // 1 hour window for error counting

    /**
     * Widget render error'unu handle et
     */
    public function handleWidgetError(Throwable $exception, array $context = []): string
    {
        $widgetId = $context['widget_id'] ?? 'unknown';
        $widgetName = $context['widget_name'] ?? 'Unknown Widget';
        $widgetType = $context['widget_type'] ?? 'unknown';

        // Error'u log'la
        $this->logWidgetError($exception, $context);

        // Error count'u artır
        $this->incrementErrorCount($widgetId);

        // Fallback content'i döndür
        return $this->generateErrorFallback($widgetName, $exception, $context);
    }

    /**
     * Widget error'unu detaylı log'la
     */
    public function logWidgetError(Throwable $exception, array $context = []): void
    {
        $errorData = [
            'widget_id' => $context['widget_id'] ?? null,
            'widget_name' => $context['widget_name'] ?? null,
            'widget_type' => $context['widget_type'] ?? null,
            'file_path' => $context['file_path'] ?? null,
            'user_id' => auth()->id(),
            'tenant_id' => $context['tenant_id'] ?? null,
            'error_type' => get_class($exception),
            'error_message' => $exception->getMessage(),
            'error_file' => $exception->getFile(),
            'error_line' => $exception->getLine(),
            'stack_trace' => $exception->getTraceAsString(),
            'request_url' => request()->fullUrl(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString()
        ];

        Log::error('Widget render error', $errorData);

        // Kritik error'ları ayrı olarak log'la
        if ($this->isCriticalError($exception)) {
            Log::critical('Critical widget error detected', $errorData);
            $this->notifyAdministrators($errorData);
        }
    }

    /**
     * Error fallback content'i oluştur
     */
    public function generateErrorFallback(string $widgetName, Throwable $exception, array $context = []): string
    {
        $isDevelopment = config('app.debug', false);
        $widgetId = $context['widget_id'] ?? 'unknown';
        
        // Error count'u kontrol et
        $errorCount = $this->getErrorCount($widgetId);
        $isDisabled = $errorCount >= self::ERROR_THRESHOLD;

        if ($isDisabled) {
            return $this->generateDisabledWidgetFallback($widgetName);
        }

        // Development mode'da detaylı error göster
        if ($isDevelopment) {
            return $this->generateDevelopmentErrorFallback($widgetName, $exception, $context);
        }

        // Production mode'da kullanıcı dostu error göster
        return $this->generateProductionErrorFallback($widgetName, $context);
    }

    /**
     * Development error fallback
     */
    private function generateDevelopmentErrorFallback(string $widgetName, Throwable $exception, array $context): string
    {
        $errorClass = get_class($exception);
        $errorMessage = $exception->getMessage();
        $errorFile = $exception->getFile();
        $errorLine = $exception->getLine();

        return '<div class="widget-error-fallback widget-error-development" style="border: 2px solid #dc3545; background-color: #f8d7da; padding: 15px; margin: 10px 0; border-radius: 5px; color: #721c24;">
            <div style="display: flex; align-items: center; margin-bottom: 10px;">
                <i class="fas fa-exclamation-triangle" style="color: #dc3545; margin-right: 8px; font-size: 18px;"></i>
                <h4 style="margin: 0; font-size: 16px;">Widget Error: ' . htmlspecialchars($widgetName) . '</h4>
            </div>
            <div style="background-color: #ffffff; padding: 10px; border-radius: 3px; font-family: monospace; font-size: 12px;">
                <strong>Error Type:</strong> ' . htmlspecialchars($errorClass) . '<br>
                <strong>Message:</strong> ' . htmlspecialchars($errorMessage) . '<br>
                <strong>File:</strong> ' . htmlspecialchars($errorFile) . '<br>
                <strong>Line:</strong> ' . $errorLine . '<br>
                <strong>Widget ID:</strong> ' . htmlspecialchars($context['widget_id'] ?? 'N/A') . '<br>
                <strong>Widget Type:</strong> ' . htmlspecialchars($context['widget_type'] ?? 'N/A') . '
            </div>
            <div style="margin-top: 10px; font-size: 12px;">
                <strong>Debug Info:</strong> Check the Laravel logs for detailed stack trace.
            </div>
        </div>';
    }

    /**
     * Production error fallback
     */
    private function generateProductionErrorFallback(string $widgetName, array $context): string
    {
        return '<div class="widget-error-fallback widget-error-production" style="border: 1px solid #ffc107; background-color: #fff3cd; padding: 12px; margin: 10px 0; border-radius: 4px; color: #856404; text-align: center;">
            <div style="display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-exclamation-circle" style="color: #ffc107; margin-right: 8px;"></i>
                <span>Bu içerik şu anda yüklenemiyor</span>
            </div>
            <div style="font-size: 12px; margin-top: 5px; opacity: 0.8;">
                Lütfen daha sonra tekrar deneyin
            </div>
        </div>';
    }

    /**
     * Disabled widget fallback
     */
    private function generateDisabledWidgetFallback(string $widgetName): string
    {
        return '<div class="widget-error-fallback widget-disabled" style="border: 1px solid #dc3545; background-color: #f8d7da; padding: 12px; margin: 10px 0; border-radius: 4px; color: #721c24; text-align: center;">
            <div style="display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-ban" style="color: #dc3545; margin-right: 8px;"></i>
                <span>Bu widget geçici olarak devre dışı bırakıldı</span>
            </div>
            <div style="font-size: 12px; margin-top: 5px; opacity: 0.8;">
                Tekrarlanan hatalar nedeniyle güvenlik için kapatıldı
            </div>
        </div>';
    }

    /**
     * Error count'u artır
     */
    private function incrementErrorCount(string $widgetId): void
    {
        $key = "widget_error_count_{$widgetId}";
        $current = Cache::get($key, 0);
        Cache::put($key, $current + 1, self::ERROR_WINDOW);
    }

    /**
     * Error count'u al
     */
    private function getErrorCount(string $widgetId): int
    {
        return Cache::get("widget_error_count_{$widgetId}", 0);
    }

    /**
     * Widget error count'u reset et
     */
    public function resetErrorCount(string $widgetId): void
    {
        Cache::forget("widget_error_count_{$widgetId}");
        Log::info('Widget error count reset', ['widget_id' => $widgetId]);
    }

    /**
     * Kritik error olup olmadığını kontrol et
     */
    private function isCriticalError(Throwable $exception): bool
    {
        $criticalErrors = [
            'PDOException',
            'QueryException',
            'OutOfMemoryError',
            'FatalError',
            'SecurityException'
        ];

        $errorClass = get_class($exception);
        
        foreach ($criticalErrors as $critical) {
            if (str_contains($errorClass, $critical)) {
                return true;
            }
        }

        // Memory related errors
        if (str_contains($exception->getMessage(), 'memory') || 
            str_contains($exception->getMessage(), 'Allowed memory size')) {
            return true;
        }

        return false;
    }

    /**
     * Yöneticileri bilgilendir
     */
    private function notifyAdministrators(array $errorData): void
    {
        // Email notification (implement as needed)
        // Slack notification (implement as needed)
        // Dashboard notification (implement as needed)
        
        Log::info('Administrator notification triggered', [
            'widget_id' => $errorData['widget_id'],
            'error_type' => $errorData['error_type']
        ]);
    }

    /**
     * Widget'ı manuel olarak disable et
     */
    public function disableWidget(string $widgetId, string $reason = 'Manual disable'): void
    {
        $key = "widget_disabled_{$widgetId}";
        Cache::put($key, [
            'reason' => $reason,
            'disabled_at' => now()->toISOString(),
            'disabled_by' => auth()->id()
        ], 86400); // 24 hours

        Log::warning('Widget manually disabled', [
            'widget_id' => $widgetId,
            'reason' => $reason,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Widget'ı enable et
     */
    public function enableWidget(string $widgetId): void
    {
        Cache::forget("widget_disabled_{$widgetId}");
        $this->resetErrorCount($widgetId);

        Log::info('Widget manually enabled', [
            'widget_id' => $widgetId,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Widget disabled olup olmadığını kontrol et
     */
    public function isWidgetDisabled(string $widgetId): bool
    {
        return Cache::has("widget_disabled_{$widgetId}");
    }

    /**
     * Widget health check
     */
    public function checkWidgetHealth(string $widgetId): array
    {
        return [
            'widget_id' => $widgetId,
            'is_disabled' => $this->isWidgetDisabled($widgetId),
            'error_count' => $this->getErrorCount($widgetId),
            'error_threshold' => self::ERROR_THRESHOLD,
            'health_status' => $this->getWidgetHealthStatus($widgetId),
            'last_error_check' => now()->toISOString()
        ];
    }

    /**
     * Widget health status'unu al
     */
    private function getWidgetHealthStatus(string $widgetId): string
    {
        if ($this->isWidgetDisabled($widgetId)) {
            return 'disabled';
        }

        $errorCount = $this->getErrorCount($widgetId);
        
        if ($errorCount >= self::ERROR_THRESHOLD) {
            return 'critical';
        } elseif ($errorCount >= self::ERROR_THRESHOLD * 0.7) {
            return 'warning';
        } elseif ($errorCount > 0) {
            return 'degraded';
        }

        return 'healthy';
    }

    /**
     * Tüm widget'ların health report'u
     */
    public function getWidgetsHealthReport(): array
    {
        // Bu method genişletilecek - şimdilik basic implementasyon
        return [
            'report_generated_at' => now()->toISOString(),
            'total_widgets' => 0, // Widget sayısını al
            'healthy_widgets' => 0,
            'degraded_widgets' => 0,
            'critical_widgets' => 0,
            'disabled_widgets' => 0
        ];
    }
}