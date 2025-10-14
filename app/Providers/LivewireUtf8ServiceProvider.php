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
        // Hook into Livewire dehydration process to sanitize UTF-8
        Livewire::listen('component.dehydrate', function ($component) {
            $this->sanitizeComponentProperties($component);
        });
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
