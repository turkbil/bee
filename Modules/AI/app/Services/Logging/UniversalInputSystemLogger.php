<?php

declare(strict_types=1);

namespace Modules\AI\App\Services\Logging;

use Illuminate\Support\Facades\Log;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\JsonFormatter;
use Monolog\Formatter\LineFormatter;
use Psr\Log\LoggerInterface;
use Carbon\Carbon;

/**
 * Universal Input System V3 Professional - Custom Logger Service
 * Advanced logging functionality for the Universal Input System
 * 
 * @package Modules\AI\Services\Logging
 * @version 3.0.0
 * @author AI Universal Input System
 */
readonly class UniversalInputSystemLogger implements LoggerInterface
{
    private Logger $logger;
    private array $config;
    private string $environment;

    public function __construct()
    {
        $this->config = config('universal-input-system.logging', []);
        $this->environment = app()->environment();
        $this->logger = $this->createLogger();
    }

    /**
     * Create and configure the logger instance
     */
    private function createLogger(): Logger
    {
        $logger = new Logger('uis');
        
        // Add default handler
        $this->addDefaultHandler($logger);
        
        // Add performance handler
        $this->addPerformanceHandler($logger);
        
        // Add security handler
        $this->addSecurityHandler($logger);
        
        // Add analytics handler
        $this->addAnalyticsHandler($logger);

        return $logger;
    }

    /**
     * Add default logging handler
     */
    private function addDefaultHandler(Logger $logger): void
    {
        $channel = $this->config['channels']['default'] ?? 'single';
        $level = $this->config['levels']['default'] ?? 'info';
        
        if ($channel === 'daily') {
            $handler = new RotatingFileHandler(
                storage_path('logs/uis/uis.log'),
                $this->config['retention']['days'] ?? 30,
                $this->getMonologLevel($level)
            );
        } else {
            $handler = new StreamHandler(
                storage_path('logs/uis/uis.log'),
                $this->getMonologLevel($level)
            );
        }

        $this->configureHandler($handler, 'default');
        $logger->pushHandler($handler);
    }

    /**
     * Add performance logging handler
     */
    private function addPerformanceHandler(Logger $logger): void
    {
        $channel = $this->config['channels']['performance'] ?? 'daily';
        $level = $this->config['levels']['performance'] ?? 'info';
        
        if ($channel === 'daily') {
            $handler = new RotatingFileHandler(
                storage_path('logs/uis/performance.log'),
                $this->config['retention']['days'] ?? 30,
                $this->getMonologLevel($level)
            );
        } else {
            $handler = new StreamHandler(
                storage_path('logs/uis/performance.log'),
                $this->getMonologLevel($level)
            );
        }

        $this->configureHandler($handler, 'performance');
        $logger->pushHandler($handler);
    }

    /**
     * Add security logging handler
     */
    private function addSecurityHandler(Logger $logger): void
    {
        $channel = $this->config['channels']['security'] ?? 'daily';
        $level = $this->config['levels']['security'] ?? 'warning';
        
        $handler = new RotatingFileHandler(
            storage_path('logs/uis/security.log'),
            365, // Keep security logs for 1 year
            $this->getMonologLevel($level)
        );

        $this->configureHandler($handler, 'security');
        $logger->pushHandler($handler);
    }

    /**
     * Add analytics logging handler
     */
    private function addAnalyticsHandler(Logger $logger): void
    {
        $channel = $this->config['channels']['analytics'] ?? 'daily';
        $level = $this->config['levels']['analytics'] ?? 'info';
        
        $handler = new RotatingFileHandler(
            storage_path('logs/uis/analytics.log'),
            $this->config['retention']['days'] ?? 30,
            $this->getMonologLevel($level)
        );

        $this->configureHandler($handler, 'analytics');
        $logger->pushHandler($handler);
    }

    /**
     * Configure handler with formatter and options
     */
    private function configureHandler($handler, string $type): void
    {
        $structuredLogging = $this->config['format']['structured_logging'] ?? true;
        
        if ($structuredLogging) {
            $formatter = new JsonFormatter();
        } else {
            $format = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";
            $formatter = new LineFormatter($format, null, false, true);
        }
        
        $handler->setFormatter($formatter);
        
        // Set permissions for log files
        if (method_exists($handler, 'getUrl')) {
            $logFile = $handler->getUrl();
            if (file_exists($logFile)) {
                chmod($logFile, 0640);
            }
        }
    }

    /**
     * Convert string log level to Monolog level constant
     */
    private function getMonologLevel(string $level): int
    {
        return match(strtolower($level)) {
            'debug' => Logger::DEBUG,
            'info' => Logger::INFO,
            'notice' => Logger::NOTICE,
            'warning' => Logger::WARNING,
            'error' => Logger::ERROR,
            'critical' => Logger::CRITICAL,
            'alert' => Logger::ALERT,
            'emergency' => Logger::EMERGENCY,
            default => Logger::INFO,
        };
    }

    /**
     * Log form processing events
     */
    public function logFormProcessing(
        string $event,
        int $featureId,
        array $context = [],
        string $level = 'info'
    ): void {
        $this->log($level, "Form Processing: {$event}", array_merge($context, [
            'event_type' => 'form_processing',
            'feature_id' => $featureId,
            'timestamp' => now()->toISOString(),
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
            'request_id' => request()->header('X-Request-ID') ?? uniqid(),
        ]));
    }

    /**
     * Log performance metrics
     */
    public function logPerformance(
        string $operation,
        float $executionTime,
        array $metrics = [],
        string $level = 'info'
    ): void {
        $this->log($level, "Performance: {$operation}", array_merge($metrics, [
            'event_type' => 'performance',
            'operation' => $operation,
            'execution_time_ms' => $executionTime * 1000,
            'memory_usage_mb' => memory_get_usage(true) / 1024 / 1024,
            'memory_peak_mb' => memory_get_peak_usage(true) / 1024 / 1024,
            'timestamp' => now()->toISOString(),
        ]));
    }

    /**
     * Log security events
     */
    public function logSecurity(
        string $event,
        array $context = [],
        string $level = 'warning'
    ): void {
        $this->log($level, "Security: {$event}", array_merge($context, [
            'event_type' => 'security',
            'security_event' => $event,
            'timestamp' => now()->toISOString(),
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => session()->getId(),
            'request_url' => request()->fullUrl(),
            'request_method' => request()->method(),
        ]));
    }

    /**
     * Log analytics events
     */
    public function logAnalytics(
        string $event,
        array $data = [],
        string $level = 'info'
    ): void {
        $this->log($level, "Analytics: {$event}", array_merge($data, [
            'event_type' => 'analytics',
            'analytics_event' => $event,
            'timestamp' => now()->toISOString(),
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
            'request_id' => request()->header('X-Request-ID') ?? uniqid(),
        ]));
    }

    /**
     * Log bulk operation events
     */
    public function logBulkOperation(
        string $operationId,
        string $status,
        array $metrics = [],
        string $level = 'info'
    ): void {
        $this->log($level, "Bulk Operation: {$status}", array_merge($metrics, [
            'event_type' => 'bulk_operation',
            'operation_id' => $operationId,
            'status' => $status,
            'timestamp' => now()->toISOString(),
            'user_id' => auth()->id(),
        ]));
    }

    /**
     * Log AI processing events
     */
    public function logAIProcessing(
        int $featureId,
        string $status,
        array $metrics = [],
        string $level = 'info'
    ): void {
        $this->log($level, "AI Processing: {$status}", array_merge($metrics, [
            'event_type' => 'ai_processing',
            'feature_id' => $featureId,
            'status' => $status,
            'timestamp' => now()->toISOString(),
            'user_id' => auth()->id(),
        ]));
    }

    /**
     * Log context processing events
     */
    public function logContext(
        string $event,
        array $contextData = [],
        string $level = 'info'
    ): void {
        $this->log($level, "Context: {$event}", array_merge($contextData, [
            'event_type' => 'context_processing',
            'context_event' => $event,
            'timestamp' => now()->toISOString(),
            'user_id' => auth()->id(),
        ]));
    }

    /**
     * Log validation events
     */
    public function logValidation(
        string $event,
        array $validationData = [],
        string $level = 'info'
    ): void {
        $this->log($level, "Validation: {$event}", array_merge($validationData, [
            'event_type' => 'validation',
            'validation_event' => $event,
            'timestamp' => now()->toISOString(),
        ]));
    }

    /**
     * Log file operation events
     */
    public function logFileOperation(
        string $operation,
        string $filename,
        array $fileInfo = [],
        string $level = 'info'
    ): void {
        $this->log($level, "File Operation: {$operation}", array_merge($fileInfo, [
            'event_type' => 'file_operation',
            'operation' => $operation,
            'filename' => $filename,
            'timestamp' => now()->toISOString(),
            'user_id' => auth()->id(),
        ]));
    }

    /**
     * Create structured log entry
     */
    private function createLogEntry(string $level, string $message, array $context = []): array
    {
        $baseContext = [
            'environment' => $this->environment,
            'uis_version' => config('universal-input-system.version', '3.0.0'),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
        ];

        if ($this->config['format']['include_context'] ?? true) {
            $context = array_merge($baseContext, $context);
        }

        return [
            'level' => $level,
            'message' => $message,
            'context' => $context,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get log file paths for cleanup and maintenance
     */
    public function getLogPaths(): array
    {
        return [
            'default' => storage_path('logs/uis/uis.log'),
            'performance' => storage_path('logs/uis/performance.log'),
            'security' => storage_path('logs/uis/security.log'),
            'analytics' => storage_path('logs/uis/analytics.log'),
        ];
    }

    /**
     * Clean up old log files
     */
    public function cleanupOldLogs(): int
    {
        $retentionDays = $this->config['retention']['days'] ?? 30;
        $maxFiles = $this->config['retention']['max_files'] ?? 10;
        $compressOld = $this->config['retention']['compress_old'] ?? true;
        
        $cleanedFiles = 0;
        $logPaths = $this->getLogPaths();
        
        foreach ($logPaths as $type => $basePath) {
            $directory = dirname($basePath);
            $filename = pathinfo($basePath, PATHINFO_FILENAME);
            
            if (!is_dir($directory)) {
                continue;
            }
            
            $files = glob($directory . '/' . $filename . '-*');
            $files = array_filter($files, 'is_file');
            
            // Sort files by modification time (oldest first)
            usort($files, function($a, $b) {
                return filemtime($a) - filemtime($b);
            });
            
            foreach ($files as $file) {
                $fileAge = (time() - filemtime($file)) / (24 * 60 * 60); // days
                
                if ($fileAge > $retentionDays || count($files) > $maxFiles) {
                    if ($compressOld && !str_ends_with($file, '.gz')) {
                        // Compress before deletion
                        $this->compressLogFile($file);
                    }
                    
                    if (unlink($file)) {
                        $cleanedFiles++;
                    }
                }
            }
        }
        
        return $cleanedFiles;
    }

    /**
     * Compress log file
     */
    private function compressLogFile(string $filePath): bool
    {
        try {
            $compressedPath = $filePath . '.gz';
            
            if (function_exists('gzencode')) {
                $data = file_get_contents($filePath);
                $compressed = gzencode($data, 9);
                return file_put_contents($compressedPath, $compressed) !== false;
            }
            
            return false;
        } catch (\Exception $e) {
            $this->error('Failed to compress log file', [
                'file' => $filePath,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get log statistics
     */
    public function getLogStatistics(): array
    {
        $stats = [];
        $logPaths = $this->getLogPaths();
        
        foreach ($logPaths as $type => $path) {
            $directory = dirname($path);
            $filename = pathinfo($path, PATHINFO_FILENAME);
            
            $files = glob($directory . '/' . $filename . '*');
            $totalSize = 0;
            $fileCount = 0;
            
            foreach ($files as $file) {
                if (is_file($file)) {
                    $totalSize += filesize($file);
                    $fileCount++;
                }
            }
            
            $stats[$type] = [
                'file_count' => $fileCount,
                'total_size_mb' => round($totalSize / 1024 / 1024, 2),
                'latest_file' => $path,
                'latest_modified' => file_exists($path) ? date('Y-m-d H:i:s', filemtime($path)) : null,
            ];
        }
        
        return $stats;
    }

    // PSR-3 LoggerInterface implementation
    
    public function emergency($message, array $context = []): void
    {
        $this->logger->emergency($message, $this->createLogEntry('emergency', $message, $context));
    }

    public function alert($message, array $context = []): void
    {
        $this->logger->alert($message, $this->createLogEntry('alert', $message, $context));
    }

    public function critical($message, array $context = []): void
    {
        $this->logger->critical($message, $this->createLogEntry('critical', $message, $context));
    }

    public function error($message, array $context = []): void
    {
        $this->logger->error($message, $this->createLogEntry('error', $message, $context));
    }

    public function warning($message, array $context = []): void
    {
        $this->logger->warning($message, $this->createLogEntry('warning', $message, $context));
    }

    public function notice($message, array $context = []): void
    {
        $this->logger->notice($message, $this->createLogEntry('notice', $message, $context));
    }

    public function info($message, array $context = []): void
    {
        $this->logger->info($message, $this->createLogEntry('info', $message, $context));
    }

    public function debug($message, array $context = []): void
    {
        $this->logger->debug($message, $this->createLogEntry('debug', $message, $context));
    }

    public function log($level, $message, array $context = []): void
    {
        $this->logger->log($level, $message, $this->createLogEntry($level, $message, $context));
    }
}