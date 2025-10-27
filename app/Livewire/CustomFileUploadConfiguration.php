<?php

namespace App\Livewire;

use Livewire\Features\SupportFileUploads\FileUploadConfiguration;

class CustomFileUploadConfiguration extends FileUploadConfiguration
{
    /**
     * Override rules to allow root user unlimited upload
     */
    public function rules()
    {
        // Root user (ID: 1) için sınırsız upload
        if (auth()->check() && auth()->user()->id === 1) {
            return ['required', 'file', 'max:' . (1024 * 1024)]; // 1GB max for root
        }

        // Default rules for other users
        return $this->configuredRules() ?: ['required', 'file', 'max:12288']; // 12MB
    }

    /**
     * Get configured rules from config
     */
    protected function configuredRules()
    {
        return config('livewire.temporary_file_upload.rules');
    }
}
