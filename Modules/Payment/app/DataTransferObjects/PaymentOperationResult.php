<?php

namespace Modules\Payment\App\DataTransferObjects;

use Modules\Payment\App\Models\Payment;

readonly class PaymentOperationResult
{
    public function __construct(
        public bool $success,
        public string $message,
        public string $type = 'success',
        public ?Payment $data = null,
        public ?array $meta = null
    ) {}

    public static function success(string $message, ?Payment $data = null, ?array $meta = null): self
    {
        return new self(
            success: true,
            message: $message,
            type: 'success',
            data: $data,
            meta: $meta
        );
    }

    public static function error(string $message, string $type = 'error', ?array $meta = null): self
    {
        return new self(
            success: false,
            message: $message,
            type: $type,
            meta: $meta
        );
    }

    public static function warning(string $message, ?array $meta = null): self
    {
        return new self(
            success: false,
            message: $message,
            type: 'warning',
            meta: $meta
        );
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'type' => $this->type,
            'data' => $this->data?->toArray(),
            'meta' => $this->meta
        ];
    }
}
