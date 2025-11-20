<?php

namespace Modules\Blog\app\Services;

/**
 * Fallback Blog Product Injector
 *
 * This is a fallback service for tenants that don't have product injection.
 * Tenant-specific injectors should be placed in Services/Tenants/{tenant_id}-BlogProductInjector.php
 */
class BlogProductInjector
{
    /**
     * Fallback: Return content as-is (no product injection)
     */
    public function injectProducts(string $content, $blog): string
    {
        // No product injection for this tenant
        return $content;
    }
}
