<?php

namespace Modules\Shop\App\DataTransferObjects;

readonly class BulkOperationResult
{
    public function __construct(
        public bool $success,
        public string $message,
        public int $affectedCount = 0,
        public int $skippedCount = 0,
        public string $type = 'success',
        public ?array $errors = null
    ) {}

    public static function success(string $message, int $affectedCount, int $skippedCount = 0): self
    {
        return new self(
            success: true,
            message: $message,
            affectedCount: $affectedCount,
            skippedCount: $skippedCount,
            type: 'success'
        );
    }

    public static function partial(string $message, int $affectedCount, int $skippedCount, array $errors = []): self
    {
        return new self(
            success: true,
            message: $message,
            affectedCount: $affectedCount,
            skippedCount: $skippedCount,
            type: 'warning',
            errors: $errors
        );
    }

    public static function failure(string $message, array $errors = []): self
    {
        return new self(
            success: false,
            message: $message,
            type: 'error',
            errors: $errors
        );
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'affected_count' => $this->affectedCount,
            'skipped_count' => $this->skippedCount,
            'type' => $this->type,
            'errors' => $this->errors
        ];
    }
}
