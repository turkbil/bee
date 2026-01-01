<?php

namespace Modules\Subscription\App\Observers;

use Modules\Subscription\App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * SubscriptionPlan Model Observer
 *
 * Model lifecycle event'lerini yönetir.
 * Activity logging, cache temizleme ve validasyon işlemlerini otomatikleştirir.
 */
class SubscriptionPlanObserver
{
    /**
     * Handle the SubscriptionPlan "creating" event.
     */
    public function creating(SubscriptionPlan $plan): void
    {
        // Slug yoksa title'dan otomatik oluştur
        if (empty($plan->slug) && !empty($plan->title)) {
            $title = is_array($plan->title) ? ($plan->title['tr'] ?? $plan->title['en'] ?? reset($plan->title)) : $plan->title;
            if (!empty($title)) {
                $plan->slug = Str::slug($title);
            }
        }

        // Varsayılan değerleri ayarla
        if (!isset($plan->is_active)) {
            $plan->is_active = true;
        }

        if (!isset($plan->is_public)) {
            $plan->is_public = true;
        }

        if (!isset($plan->is_featured)) {
            $plan->is_featured = false;
        }

        if (!isset($plan->is_trial)) {
            $plan->is_trial = false;
        }

        if (!isset($plan->sort_order)) {
            $maxOrder = SubscriptionPlan::max('sort_order') ?? 0;
            $plan->sort_order = $maxOrder + 1;
        }

        Log::info('SubscriptionPlan creating', [
            'title' => $plan->title,
            'slug' => $plan->slug,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the SubscriptionPlan "created" event.
     */
    public function created(SubscriptionPlan $plan): void
    {
        $this->clearSubscriptionPlanCaches();

        if (function_exists('log_activity')) {
            log_activity($plan, 'oluşturuldu');
        }

        Log::info('SubscriptionPlan created successfully', [
            'subscription_plan_id' => $plan->subscription_plan_id,
            'title' => $plan->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the SubscriptionPlan "updating" event.
     */
    public function updating(SubscriptionPlan $plan): void
    {
        $dirty = $plan->getDirty();

        // Slug değiştiyse uniqueness kontrolü
        if (isset($dirty['slug'])) {
            if ($this->isSlugTaken($dirty['slug'], $plan->subscription_plan_id)) {
                $plan->slug = $this->generateUniqueSlug($dirty['slug'], $plan->subscription_plan_id);
            }
        }

        Log::info('SubscriptionPlan updating', [
            'subscription_plan_id' => $plan->subscription_plan_id,
            'changed_fields' => array_keys($dirty),
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the SubscriptionPlan "updated" event.
     */
    public function updated(SubscriptionPlan $plan): void
    {
        $this->clearSubscriptionPlanCaches($plan->subscription_plan_id);

        if (function_exists('log_activity')) {
            $changes = $plan->getChanges();
            unset($changes['updated_at']);

            if (!empty($changes)) {
                $oldTitle = null;
                if (isset($changes['title'])) {
                    $oldTitle = $plan->getOriginal('title');
                }

                log_activity($plan, 'güncellendi', [
                    'changed_fields' => array_keys($changes)
                ], $oldTitle);
            }
        }

        Log::info('SubscriptionPlan updated successfully', [
            'subscription_plan_id' => $plan->subscription_plan_id,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the SubscriptionPlan "saving" event.
     */
    public function saving(SubscriptionPlan $plan): void
    {
        // Title validation
        if (is_array($plan->title)) {
            foreach ($plan->title as $locale => $title) {
                $minLength = 2;
                $maxLength = 191;

                if (!empty($title)) {
                    if (strlen($title) < $minLength) {
                        throw new \Exception("Plan adı en az {$minLength} karakter olmalıdır ({$locale})");
                    }

                    if (strlen($title) > $maxLength) {
                        $plan->title[$locale] = mb_substr($title, 0, $maxLength);

                        Log::warning('SubscriptionPlan title auto-trimmed', [
                            'subscription_plan_id' => $plan->subscription_plan_id,
                            'locale' => $locale,
                            'original_length' => strlen($title),
                            'trimmed_length' => $maxLength
                        ]);
                    }
                }
            }
        }

        // Tax rate validation
        if (isset($plan->tax_rate) && ($plan->tax_rate < 0 || $plan->tax_rate > 100)) {
            throw new \Exception("Vergi oranı 0-100 arasında olmalıdır");
        }
    }

    /**
     * Handle the SubscriptionPlan "saved" event.
     */
    public function saved(SubscriptionPlan $plan): void
    {
        if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
            \Spatie\ResponseCache\Facades\ResponseCache::clear();
        }
    }

    /**
     * Handle the SubscriptionPlan "deleting" event.
     */
    public function deleting(SubscriptionPlan $plan): bool
    {
        // Aktif abonelikleri olan planlar için uyarı
        $activeSubscribers = $plan->subscriptions()->where('status', 'active')->count();
        if ($activeSubscribers > 0) {
            Log::warning('Deleting subscription plan with active subscribers', [
                'subscription_plan_id' => $plan->subscription_plan_id,
                'active_subscribers' => $activeSubscribers,
                'user_id' => auth()->id()
            ]);
        }

        Log::info('SubscriptionPlan deleting', [
            'subscription_plan_id' => $plan->subscription_plan_id,
            'title' => $plan->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the SubscriptionPlan "deleted" event.
     */
    public function deleted(SubscriptionPlan $plan): void
    {
        $this->clearSubscriptionPlanCaches($plan->subscription_plan_id);

        if (function_exists('log_activity')) {
            log_activity($plan, 'silindi', null, $plan->title);
        }

        Log::info('SubscriptionPlan deleted successfully', [
            'subscription_plan_id' => $plan->subscription_plan_id,
            'title' => $plan->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the SubscriptionPlan "restoring" event.
     */
    public function restoring(SubscriptionPlan $plan): void
    {
        Log::info('SubscriptionPlan restoring', [
            'subscription_plan_id' => $plan->subscription_plan_id,
            'title' => $plan->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the SubscriptionPlan "restored" event.
     */
    public function restored(SubscriptionPlan $plan): void
    {
        $this->clearSubscriptionPlanCaches();

        if (function_exists('log_activity')) {
            log_activity($plan, 'geri yüklendi');
        }

        Log::info('SubscriptionPlan restored successfully', [
            'subscription_plan_id' => $plan->subscription_plan_id,
            'title' => $plan->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Handle the SubscriptionPlan "forceDeleting" event.
     */
    public function forceDeleting(SubscriptionPlan $plan): bool
    {
        Log::warning('SubscriptionPlan force deleting', [
            'subscription_plan_id' => $plan->subscription_plan_id,
            'title' => $plan->title,
            'user_id' => auth()->id()
        ]);

        return true;
    }

    /**
     * Handle the SubscriptionPlan "forceDeleted" event.
     */
    public function forceDeleted(SubscriptionPlan $plan): void
    {
        $this->clearSubscriptionPlanCaches($plan->subscription_plan_id);

        if (function_exists('log_activity')) {
            log_activity($plan, 'kalıcı silindi', null, $plan->title);
        }

        Log::warning('SubscriptionPlan force deleted', [
            'subscription_plan_id' => $plan->subscription_plan_id,
            'title' => $plan->title,
            'user_id' => auth()->id()
        ]);
    }

    /**
     * Clear subscription plan related caches
     */
    private function clearSubscriptionPlanCaches(?int $planId = null): void
    {
        Cache::forget('subscription_plans_list');
        Cache::forget('subscription_plans_active');
        Cache::forget('subscription_plans_public');
        Cache::forget('subscription_plans_featured');

        if ($planId) {
            Cache::forget("subscription_plan_detail_{$planId}");
        }

        if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
            Cache::tags(['subscription_plans', 'subscriptions'])->flush();
        }

        if (class_exists('\Spatie\ResponseCache\Facades\ResponseCache')) {
            \Spatie\ResponseCache\Facades\ResponseCache::clear();
        }
    }

    /**
     * Check if slug is taken
     */
    private function isSlugTaken(string $slug, ?int $excludeId = null): bool
    {
        $query = SubscriptionPlan::where('slug', $slug);

        if ($excludeId) {
            $query->where('subscription_plan_id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Generate unique slug
     */
    private function generateUniqueSlug(string $baseSlug, ?int $excludeId = null): string
    {
        $slug = $baseSlug;
        $counter = 1;

        while ($this->isSlugTaken($slug, $excludeId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
