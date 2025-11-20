<?php

namespace App\Services\Media\StockPhoto\Exceptions;

use Exception;

/**
 * Provider Not Available Exception
 *
 * Provider kullanılamadığında fırlatılır (API key yok, rate limit, API down vs)
 */
class ProviderNotAvailableException extends Exception
{
    public function __construct(
        string $message = 'Provider is not available',
        int $code = 503,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Provider adı ile exception oluştur
     */
    public static function forProvider(string $provider, ?string $reason = null): self
    {
        $message = "Provider '{$provider}' is not available";

        if ($reason) {
            $message .= ": {$reason}";
        }

        return new self($message);
    }

    /**
     * Rate limit aşıldığında
     */
    public static function rateLimitExceeded(string $provider, ?string $resetAt = null): self
    {
        $message = "Rate limit exceeded for provider '{$provider}'";

        if ($resetAt) {
            $message .= ". Resets at: {$resetAt}";
        }

        return new self($message, 429);
    }

    /**
     * API key eksik
     */
    public static function missingApiKey(string $provider): self
    {
        return new self("API key is missing for provider '{$provider}'", 401);
    }
}
