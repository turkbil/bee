<?php

namespace Modules\Announcement\App\Exceptions;

class AnnouncementNotFoundException extends AnnouncementException
{
    public function getErrorType(): string
    {
        return 'page_not_found';
    }

    public static function withId(int $id): self
    {
        return new self(
            message: "Announcement with ID {$id} not found",
            context: ['announcement_id' => $id]
        );
    }

    public static function withSlug(string $slug, string $locale = 'tr'): self
    {
        return new self(
            message: "Announcement with slug '{$slug}' not found for locale '{$locale}'",
            context: ['slug' => $slug, 'locale' => $locale]
        );
    }
}
