<?php

namespace Modules\Muzibu\App\Exceptions;

use Exception;

abstract class MuzibuException extends Exception
{
    public function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
        public readonly ?array $context = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    abstract public function getErrorType(): string;

    public function getContext(): array
    {
        return $this->context ?? [];
    }

    public function toArray(): array
    {
        return [
            'type' => $this->getErrorType(),
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'context' => $this->getContext()
        ];
    }
}
