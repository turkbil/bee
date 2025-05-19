<?php

declare(strict_types=1);

namespace DeepSeek\DeepseekLaravel\Exceptions;

use InvalidArgumentException;

/**
 * @internal
 */
final class ApiKeyIsMissing extends InvalidArgumentException
{
    /**
     * Create a new exception instance.
     */
    public static function create(): self
    {
        return new self(
            'The Deepseek API key is not set. Please ensure `DEEPSEEK_API_KEY` is configured in your .env file.',
        );
    }
}
