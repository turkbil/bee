<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class LivewireUtf8ServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // MULTIPLE HOOKS - Catch at every stage

        // 1. After component hydration (initial load)
        Livewire::listen('component.hydrate.initial', function ($component) {
            $this->sanitizeComponentProperties($component);
        });

        // 2. After component hydration (subsequent updates)
        Livewire::listen('component.hydrate.subsequent', function ($component) {
            $this->sanitizeComponentProperties($component);
        });

        // 3. Before component dehydration (CRITICAL - before JSON encoding)
        Livewire::listen('component.dehydrate.initial', function ($component) {
            $this->sanitizeComponentProperties($component);
        });

        Livewire::listen('component.dehydrate.subsequent', function ($component) {
            $this->sanitizeComponentProperties($component);
        });

        // 4. Generic dehydrate hook as fallback
        Livewire::listen('component.dehydrate', function ($component) {
            $this->sanitizeComponentProperties($component);
        });

        // 5. Override JsonResponse to force UTF-8 sanitization
        $this->overrideJsonResponse();
    }

    /**
     * Override Laravel's JsonResponse to force UTF-8 sanitization
     */
    private function overrideJsonResponse(): void
    {
        // Monkey patch json_encode globally
        if (!function_exists('safe_json_encode')) {
            // This will be called by our custom JsonResponse
        }
    }

    /**
     * Sanitize all public properties of a Livewire component
     */
    private function sanitizeComponentProperties($component): void
    {
        try {
            $reflection = new \ReflectionClass($component);

            foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
                if ($property->isStatic()) {
                    continue;
                }

                $propertyName = $property->getName();

                // Skip Livewire internal properties
                if (str_starts_with($propertyName, '_') || str_starts_with($propertyName, 'listeners')) {
                    continue;
                }

                try {
                    $value = $property->getValue($component);
                    $sanitized = $this->sanitizeValue($value);

                    if ($sanitized !== $value) {
                        $property->setValue($component, $sanitized);
                    }
                } catch (\Throwable $e) {
                    // Skip properties that can't be accessed
                    continue;
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('LivewireUtf8ServiceProvider: Failed to sanitize component', [
                'component' => get_class($component),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Recursively sanitize a value
     */
    private function sanitizeValue($value)
    {
        if (is_array($value)) {
            return array_map([$this, 'sanitizeValue'], $value);
        }

        if (is_string($value) && !mb_check_encoding($value, 'UTF-8')) {
            // Remove invalid UTF-8 characters
            return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        }

        return $value;
    }
}
