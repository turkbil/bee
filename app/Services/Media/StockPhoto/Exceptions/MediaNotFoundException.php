<?php

namespace App\Services\Media\StockPhoto\Exceptions;

use Exception;

/**
 * Media Not Found Exception
 *
 * Arama sonucu görsel bulunamadığında fırlatılır
 */
class MediaNotFoundException extends Exception
{
    public function __construct(
        string $message = 'No media found matching the search criteria',
        int $code = 404,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Provider ve query bilgisi ile exception oluştur
     */
    public static function forQuery(string $provider, string $query): self
    {
        return new self("No media found on {$provider} for query: {$query}");
    }
}
