<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Predis\Connection\ConnectionException;
use Exception;

class RedisResilienceService
{
    private static $reconnectAttempts = 3;
    private static $reconnectDelay = 1; // seconds
    private static $maxReconnectDelay = 10; // seconds
    private static $lastReconnectTime = null;
    private static $isReconnecting = false;

    /**
     * Execute Redis command with automatic reconnection
     */
    public static function executeWithReconnect(callable $operation, int $maxAttempts = 3)
    {
        $attempt = 0;
        $lastException = null;

        while ($attempt < $maxAttempts) {
            try {
                // Bağlantı sağlığını kontrol et
                if ($attempt > 0) {
                    self::ensureConnection();
                }

                // İşlemi çalıştır
                return $operation();

            } catch (ConnectionException $e) {
                $lastException = $e;
                $attempt++;
                
                Log::warning("Redis connection error, attempt {$attempt}/{$maxAttempts}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                if ($attempt < $maxAttempts) {
                    // Exponential backoff ile yeniden deneme
                    $delay = min(self::$reconnectDelay * pow(2, $attempt - 1), self::$maxReconnectDelay);
                    sleep($delay);
                    
                    // Redis bağlantısını yeniden kur
                    self::forceReconnect();
                }
            } catch (Exception $e) {
                // Diğer hatalar için sadece 1 kez daha dene
                if ($attempt === 0) {
                    $attempt++;
                    Log::warning("Redis operation error, retrying once", [
                        'error' => $e->getMessage()
                    ]);
                    
                    sleep(1);
                    self::ensureConnection();
                    continue;
                }
                
                throw $e;
            }
        }

        // Tüm denemeler başarısız
        Log::error("Redis operation failed after {$maxAttempts} attempts", [
            'last_error' => $lastException ? $lastException->getMessage() : 'Unknown error'
        ]);
        
        throw $lastException ?: new Exception('Redis operation failed after maximum attempts');
    }

    /**
     * Force Redis reconnection
     */
    public static function forceReconnect(): void
    {
        if (self::$isReconnecting) {
            return; // Prevent concurrent reconnection attempts
        }

        self::$isReconnecting = true;
        
        try {
            Log::info('Forcing Redis reconnection...');
            
            // Close existing connections
            self::closeAllConnections();
            
            // Wait a bit before reconnecting
            sleep(1);
            
            // Test new connection
            Redis::ping();
            
            self::$lastReconnectTime = time();
            Log::info('Redis reconnection successful');
            
        } catch (Exception $e) {
            Log::error('Redis reconnection failed', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        } finally {
            self::$isReconnecting = false;
        }
    }

    /**
     * Ensure Redis connection is healthy
     */
    public static function ensureConnection(): bool
    {
        try {
            // Quick health check
            Redis::ping();
            return true;
            
        } catch (Exception $e) {
            Log::warning('Redis health check failed, attempting reconnection', [
                'error' => $e->getMessage()
            ]);
            
            self::forceReconnect();
            return true;
        }
    }

    /**
     * Close all Redis connections
     */
    private static function closeAllConnections(): void
    {
        try {
            // Get all Redis connections and close them
            $connections = ['default', 'cache', 'session', 'queue'];
            
            foreach ($connections as $connection) {
                try {
                    $redis = Redis::connection($connection);
                    if (method_exists($redis, 'disconnect')) {
                        $redis->disconnect();
                    }
                } catch (Exception $e) {
                    // Ignore individual connection close errors
                }
            }
            
            // Clear Redis connection pool
            Redis::purge();
            
        } catch (Exception $e) {
            Log::debug('Error closing Redis connections', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get Redis connection with resilience
     */
    public static function getConnection(string $name = 'default')
    {
        return self::executeWithReconnect(function () use ($name) {
            return Redis::connection($name);
        });
    }

    /**
     * Check if reconnection is needed based on time
     */
    public static function shouldReconnect(): bool
    {
        if (self::$lastReconnectTime === null) {
            return false;
        }
        
        // Reconnect if last attempt was more than 30 seconds ago
        return (time() - self::$lastReconnectTime) > 30;
    }

    /**
     * Health check method for monitoring
     */
    public static function healthCheck(): array
    {
        $status = [
            'redis_status' => 'unknown',
            'last_reconnect' => self::$lastReconnectTime,
            'is_reconnecting' => self::$isReconnecting,
            'connections' => []
        ];

        try {
            // Test default connection
            Redis::ping();
            $status['redis_status'] = 'healthy';
            
            // Test specific connections
            $connections = ['default', 'cache', 'session', 'queue'];
            foreach ($connections as $conn) {
                try {
                    Redis::connection($conn)->ping();
                    $status['connections'][$conn] = 'healthy';
                } catch (Exception $e) {
                    $status['connections'][$conn] = 'error: ' . $e->getMessage();
                }
            }
            
        } catch (Exception $e) {
            $status['redis_status'] = 'error: ' . $e->getMessage();
        }

        return $status;
    }
}