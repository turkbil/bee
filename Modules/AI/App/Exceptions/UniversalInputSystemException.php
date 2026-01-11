<?php

declare(strict_types=1);

namespace Modules\AI\app\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Universal Input System V3 Professional - Base Exception Class
 * Comprehensive exception handling for the Universal Input System
 * 
 * @package Modules\AI\Exceptions
 * @version 3.0.0
 * @author AI Universal Input System
 */
class UniversalInputSystemException extends Exception
{
    /**
     * Exception context data
     */
    protected array $context = [];
    
    /**
     * Exception severity level
     */
    protected string $severity = 'error';
    
    /**
     * Whether this exception should be reported
     */
    protected bool $reportable = true;
    
    /**
     * HTTP status code for this exception
     */
    protected int $statusCode = 500;
    
    /**
     * User-friendly error message
     */
    protected string $userMessage;

    /**
     * Create a new Universal Input System exception instance
     *
     * @param string $message Technical error message
     * @param string|null $userMessage User-friendly error message
     * @param array $context Additional context data
     * @param int $code Error code
     * @param Throwable|null $previous Previous exception
     */
    public function __construct(
        string $message = '',
        ?string $userMessage = null,
        array $context = [],
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        
        $this->context = $context;
        $this->userMessage = $userMessage ?? $this->getDefaultUserMessage();
        
        // Auto-generate context if not provided
        if (empty($this->context)) {
            $this->context = $this->generateContextData();
        }
        
        // Log the exception immediately if it's reportable
        if ($this->reportable) {
            $this->report();
        }
    }

    /**
     * Get the exception context data
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Set the exception context data
     */
    public function setContext(array $context): self
    {
        $this->context = array_merge($this->context, $context);
        return $this;
    }

    /**
     * Get the exception severity level
     */
    public function getSeverity(): string
    {
        return $this->severity;
    }

    /**
     * Set the exception severity level
     */
    public function setSeverity(string $severity): self
    {
        $this->severity = $severity;
        return $this;
    }

    /**
     * Check if this exception should be reported
     */
    public function shouldReport(): bool
    {
        return $this->reportable;
    }

    /**
     * Set whether this exception should be reported
     */
    public function setReportable(bool $reportable): self
    {
        $this->reportable = $reportable;
        return $this;
    }

    /**
     * Get the HTTP status code
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Set the HTTP status code
     */
    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * Get the user-friendly error message
     */
    public function getUserMessage(): string
    {
        return $this->userMessage;
    }

    /**
     * Set the user-friendly error message
     */
    public function setUserMessage(string $userMessage): self
    {
        $this->userMessage = $userMessage;
        return $this;
    }

    /**
     * Report the exception to the logging system
     */
    public function report(): void
    {
        if (!$this->reportable) {
            return;
        }

        $logData = [
            'exception' => get_class($this),
            'message' => $this->getMessage(),
            'user_message' => $this->userMessage,
            'code' => $this->getCode(),
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'severity' => $this->severity,
            'context' => $this->context,
            'trace' => $this->getTraceAsString(),
            'request_id' => request()->header('X-Request-ID') ?? uniqid(),
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'timestamp' => now()->toISOString(),
        ];

        // Use different log levels based on severity
        switch ($this->severity) {
            case 'critical':
                Log::critical('UIS Critical Exception: ' . $this->getMessage(), $logData);
                break;
            case 'error':
                Log::error('UIS Exception: ' . $this->getMessage(), $logData);
                break;
            case 'warning':
                Log::warning('UIS Warning: ' . $this->getMessage(), $logData);
                break;
            case 'info':
                Log::info('UIS Info: ' . $this->getMessage(), $logData);
                break;
            default:
                Log::error('UIS Exception: ' . $this->getMessage(), $logData);
        }

        // Send alerts for critical exceptions
        if ($this->severity === 'critical') {
            $this->sendCriticalAlert($logData);
        }
    }

    /**
     * Render the exception as an HTTP response
     */
    public function render(Request $request): JsonResponse
    {
        // Determine if we should show detailed error information
        $showDetails = config('app.debug', false) || 
                      config('universal-input-system.debug', false);

        $response = [
            'success' => false,
            'error' => [
                'message' => $this->userMessage,
                'code' => $this->getCode(),
                'type' => class_basename($this),
            ],
            'meta' => [
                'timestamp' => now()->toISOString(),
                'request_id' => request()->header('X-Request-ID') ?? uniqid(),
            ],
        ];

        // Add detailed information in debug mode
        if ($showDetails) {
            $response['debug'] = [
                'message' => $this->getMessage(),
                'file' => $this->getFile(),
                'line' => $this->getLine(),
                'context' => $this->context,
                'trace' => $this->getTrace(),
            ];
        }

        // Add context data if available
        if (!empty($this->context) && !$showDetails) {
            $response['context'] = array_intersect_key(
                $this->context,
                array_flip(['feature_id', 'operation_id', 'user_input'])
            );
        }

        return response()->json($response, $this->statusCode);
    }

    /**
     * Generate default context data
     */
    protected function generateContextData(): array
    {
        return [
            'exception_class' => get_class($this),
            'timestamp' => now()->toISOString(),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'uis_version' => config('universal-input-system.version', '3.0.0'),
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
        ];
    }

    /**
     * Get default user-friendly message
     */
    protected function getDefaultUserMessage(): string
    {
        return __('ai::errors.general_error', [
            'code' => $this->getCode()
        ]);
    }

    /**
     * Send critical alert notifications
     */
    protected function sendCriticalAlert(array $logData): void
    {
        try {
            // Check if alerts are enabled
            if (!config('universal-input-system.analytics.alerts.enabled', false)) {
                return;
            }

            $alertChannels = config('universal-input-system.analytics.alerts.channels', []);
            
            foreach ($alertChannels as $channel) {
                switch ($channel) {
                    case 'mail':
                        $this->sendMailAlert($logData);
                        break;
                    case 'slack':
                        $this->sendSlackAlert($logData);
                        break;
                    case 'webhook':
                        $this->sendWebhookAlert($logData);
                        break;
                }
            }
        } catch (Throwable $e) {
            // Don't let alert failures break the application
            Log::error('Failed to send UIS critical alert', [
                'alert_error' => $e->getMessage(),
                'original_exception' => $this->getMessage()
            ]);
        }
    }

    /**
     * Send email alert for critical exceptions
     */
    protected function sendMailAlert(array $logData): void
    {
        // Implementation would depend on your mail configuration
        // This is a placeholder for the actual implementation
        Log::info('UIS Mail alert would be sent', $logData);
    }

    /**
     * Send Slack alert for critical exceptions
     */
    protected function sendSlackAlert(array $logData): void
    {
        // Implementation would depend on your Slack integration
        // This is a placeholder for the actual implementation
        Log::info('UIS Slack alert would be sent', $logData);
    }

    /**
     * Send webhook alert for critical exceptions
     */
    protected function sendWebhookAlert(array $logData): void
    {
        // Implementation would depend on your webhook configuration
        // This is a placeholder for the actual implementation
        Log::info('UIS Webhook alert would be sent', $logData);
    }

    /**
     * Create exception with fluent interface
     */
    public static function create(string $message): self
    {
        return new static($message);
    }

    /**
     * Add context data with fluent interface
     */
    public function withContext(array $context): self
    {
        return $this->setContext($context);
    }

    /**
     * Set severity with fluent interface
     */
    public function withSeverity(string $severity): self
    {
        return $this->setSeverity($severity);
    }

    /**
     * Set status code with fluent interface
     */
    public function withStatusCode(int $statusCode): self
    {
        return $this->setStatusCode($statusCode);
    }

    /**
     * Set user message with fluent interface
     */
    public function withUserMessage(string $userMessage): self
    {
        return $this->setUserMessage($userMessage);
    }

    /**
     * Set as non-reportable with fluent interface
     */
    public function dontReport(): self
    {
        return $this->setReportable(false);
    }

    /**
     * Convert exception to array
     */
    public function toArray(): array
    {
        return [
            'exception' => get_class($this),
            'message' => $this->getMessage(),
            'user_message' => $this->userMessage,
            'code' => $this->getCode(),
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'severity' => $this->severity,
            'status_code' => $this->statusCode,
            'context' => $this->context,
            'reportable' => $this->reportable,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Convert exception to JSON
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * String representation of the exception
     */
    public function __toString(): string
    {
        return sprintf(
            "[%s] %s in %s:%d\nContext: %s",
            get_class($this),
            $this->getMessage(),
            $this->getFile(),
            $this->getLine(),
            json_encode($this->context, JSON_PRETTY_PRINT)
        );
    }
}