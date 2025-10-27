<?php

if (!function_exists('livewire_upload_max_size')) {
    /**
     * Get Livewire upload max size based on user
     * Root user (ID: 1) gets unlimited upload
     */
    function livewire_upload_max_size(): int
    {
        // Root user (ID: 1) için 1GB
        if (auth()->check() && auth()->user()->id === 1) {
            return 1024 * 1024; // 1GB in KB
        }

        // Diğerleri için 12MB (Livewire default)
        return 12288; // 12MB in KB
    }
}

if (!function_exists('livewire_upload_rules')) {
    /**
     * Get Livewire upload validation rules
     */
    function livewire_upload_rules(): array
    {
        $maxSize = livewire_upload_max_size();

        return ['required', 'file', 'max:' . $maxSize];
    }
}
