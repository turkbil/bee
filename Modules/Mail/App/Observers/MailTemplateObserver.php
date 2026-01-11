<?php

namespace Modules\Mail\App\Observers;

use Modules\Mail\App\Models\MailTemplate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * MailTemplate Model Observer
 *
 * Model lifecycle event'lerini yönetir.
 * Activity logging ve cache temizleme işlemlerini otomatikleştirir.
 */
class MailTemplateObserver
{
    /**
     * Handle the MailTemplate "creating" event.
     */
    public function creating(MailTemplate $template): void
    {
        // Varsayılan değerleri ayarla
        if (!isset($template->is_active)) {
            $template->is_active = true;
        }

        Log::info('MailTemplate creating', [
            'key' => $template->key,
            'name' => $template->name,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the MailTemplate "created" event.
     */
    public function created(MailTemplate $template): void
    {
        $this->clearMailTemplateCaches();

        if (function_exists('log_activity')) {
            log_activity($template, 'oluşturuldu');
        }

        Log::info('MailTemplate created successfully', [
            'id' => $template->id,
            'key' => $template->key,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the MailTemplate "updating" event.
     */
    public function updating(MailTemplate $template): void
    {
        $dirty = $template->getDirty();

        Log::info('MailTemplate updating', [
            'id' => $template->id,
            'key' => $template->key,
            'changed_fields' => array_keys($dirty),
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the MailTemplate "updated" event.
     */
    public function updated(MailTemplate $template): void
    {
        $this->clearMailTemplateCaches($template->id, $template->key);

        if (function_exists('log_activity')) {
            $changes = $template->getChanges();
            unset($changes['updated_at']);

            if (!empty($changes)) {
                $oldName = null;
                if (isset($changes['name'])) {
                    $oldName = $template->getOriginal('name');
                }

                log_activity($template, 'güncellendi', [
                    'changed_fields' => array_keys($changes)
                ], $oldName);
            }
        }

        Log::info('MailTemplate updated successfully', [
            'id' => $template->id,
            'key' => $template->key,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the MailTemplate "saving" event.
     */
    public function saving(MailTemplate $template): void
    {
        // Key validation - must be snake_case
        if (!empty($template->key)) {
            $template->key = strtolower(trim($template->key));
        }

        // Name validation
        if (!empty($template->name)) {
            $minLength = 2;
            $maxLength = 191;

            if (strlen($template->name) < $minLength) {
                throw new \Exception("Şablon adı en az {$minLength} karakter olmalıdır");
            }

            if (strlen($template->name) > $maxLength) {
                $template->name = mb_substr($template->name, 0, $maxLength);

                Log::warning('MailTemplate name auto-trimmed', [
                    'id' => $template->id,
                    'original_length' => strlen($template->name),
                    'trimmed_length' => $maxLength
                ]);
            }
        }
    }

    /**
     * Handle the MailTemplate "saved" event.
     */
    public function saved(MailTemplate $template): void
    {
        // Clear specific key cache
        Cache::forget("mail_template_{$template->key}");

        if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
            \Spatie\ResponseCache\Facades\ResponseCache::clear();
        }
    }

    /**
     * Handle the MailTemplate "deleting" event.
     */
    public function deleting(MailTemplate $template): bool
    {
        Log::info('MailTemplate deleting', [
            'id' => $template->id,
            'key' => $template->key,
            'name' => $template->name,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the MailTemplate "deleted" event.
     */
    public function deleted(MailTemplate $template): void
    {
        $this->clearMailTemplateCaches($template->id, $template->key);

        if (function_exists('log_activity')) {
            log_activity($template, 'silindi', null, $template->name);
        }

        Log::info('MailTemplate deleted successfully', [
            'id' => $template->id,
            'key' => $template->key,
            'name' => $template->name,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Clear mail template related caches
     */
    private function clearMailTemplateCaches(?int $templateId = null, ?string $key = null): void
    {
        Cache::forget('mail_templates_list');
        Cache::forget('mail_templates_active');

        if ($templateId) {
            Cache::forget("mail_template_detail_{$templateId}");
        }

        if ($key) {
            Cache::forget("mail_template_{$key}");
        }

        if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
            Cache::tags(['mail_templates', 'mail'])->flush();
        }

        if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
            \Spatie\ResponseCache\Facades\ResponseCache::clear();
        }
    }
}
