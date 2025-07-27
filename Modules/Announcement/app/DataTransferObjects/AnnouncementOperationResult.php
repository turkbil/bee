<?php

declare(strict_types=1);

namespace Modules\Announcement\App\DataTransferObjects;

use Modules\Announcement\App\Models\Announcement;

readonly class AnnouncementOperationResult
{
    public function __construct(
        public bool $success,
        public string $message,
        public string $type = 'success',
        public ?Announcement $data = null,
        public ?array $meta = null
    ) {}
    
    public static function success(string $message, ?Announcement $data = null, ?array $meta = null): self
    {
        return new self(
            success: true,
            message: $message,
            type: 'success',
            data: $data,
            meta: $meta
        );
    }
    
    public static function error(string $message, ?array $meta = null): self
    {
        return new self(
            success: false,
            message: $message,
            type: 'error',
            meta: $meta
        );
    }
    
    public static function warning(string $message, ?Announcement $data = null, ?array $meta = null): self
    {
        return new self(
            success: false,
            message: $message,
            type: 'warning',
            data: $data,
            meta: $meta
        );
    }
    
    public function hasData(): bool
    {
        return !is_null($this->data);
    }
    
    public function hasMeta(): bool
    {
        return !is_null($this->meta) && !empty($this->meta);
    }
}