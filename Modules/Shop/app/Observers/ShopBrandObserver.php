<?php

declare(strict_types=1);

namespace Modules\Shop\App\Observers;

use App\Services\TenantCacheService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Shop\App\Models\ShopBrand;

class ShopBrandObserver
{
    private TenantCacheService $cacheService;

    public function __construct()
    {
        $this->cacheService = app(TenantCacheService::class);
    }

    public function creating(ShopBrand $brand): void
    {
        if (empty($brand->slug) && !empty($brand->title)) {
            $slugs = [];
            foreach ($brand->title as $locale => $title) {
                if (!empty($title)) {
                    $slugs[$locale] = Str::slug($title);
                }
            }

            if (!empty($slugs)) {
                $brand->slug = $slugs;
            }
        }

        if (!isset($brand->is_active)) {
            $brand->is_active = true;
        }

        if (!isset($brand->sort_order)) {
            $brand->sort_order = 0;
        }

        Log::info('ShopBrand creating', [
            'title' => $brand->title,
            'user_id' => auth()->id(),
        ]);
    }

    public function created(ShopBrand $brand): void
    {
        $this->clearBrandCaches();

        if (function_exists('log_activity')) {
            log_activity($brand, 'oluşturuldu');
        }

        Log::info('ShopBrand created successfully', [
            'brand_id' => $brand->brand_id,
            'title' => $brand->title,
            'user_id' => auth()->id(),
        ]);
    }

    public function updating(ShopBrand $brand): void
    {
        $dirty = $brand->getDirty();

        if (isset($dirty['slug']) && is_array($dirty['slug'])) {
            foreach ($dirty['slug'] as $locale => $slug) {
                if ($this->isSlugTaken($slug, $locale, $brand->brand_id)) {
                    $dirty['slug'][$locale] = $this->generateUniqueSlug($slug, $locale, $brand->brand_id);
                }
            }

            $brand->slug = $dirty['slug'];
        }

        Log::info('ShopBrand updating', [
            'brand_id' => $brand->brand_id,
            'changed_fields' => array_keys($dirty),
            'user_id' => auth()->id(),
        ]);
    }

    public function updated(ShopBrand $brand): void
    {
        $this->clearBrandCaches($brand->brand_id);

        if (function_exists('log_activity')) {
            $changes = $brand->getChanges();
            unset($changes['updated_at']);

            if (!empty($changes)) {
                // Eski başlığı al (title değiştiyse)
                $oldTitle = null;
                if (isset($changes['title'])) {
                    $oldTitle = $brand->getOriginal('title');
                }

                log_activity($brand, 'güncellendi', [
                    'changed_fields' => array_keys($changes),
                ], $oldTitle);
            }
        }

        Log::info('ShopBrand updated successfully', [
            'brand_id' => $brand->brand_id,
            'user_id' => auth()->id(),
        ]);
    }

    public function saving(ShopBrand $brand): void
    {
        if (is_array($brand->title)) {
            $minLength = 2;
            $maxLength = 191;

            foreach ($brand->title as $locale => $title) {
                if (!empty($title)) {
                    if (strlen($title) < $minLength) {
                        throw new \RuntimeException(
                            __("shop::admin.brand_title_too_short", ['min' => $minLength, 'locale' => $locale])
                        );
                    }

                    if (strlen($title) > $maxLength) {
                        $brand->title[$locale] = mb_substr($title, 0, $maxLength);

                        Log::warning('ShopBrand title auto-trimmed', [
                            'brand_id' => $brand->brand_id,
                            'locale' => $locale,
                            'original_length' => strlen($title),
                            'trimmed_length' => $maxLength,
                        ]);
                    }
                }
            }
        }
    }

    public function saved(ShopBrand $brand): void
    {
        Cache::forget("universal_seo_shop_brand_{$brand->brand_id}");

        if (function_exists('responsecache')) {
            responsecache()->forget(route('shop.brand', ['slug' => $brand->slug]));
        }
    }

    public function deleting(ShopBrand $brand): bool
    {
        if ($brand->products()->count() > 0) {
            throw new \RuntimeException(__('shop::admin.brand_has_products'));
        }

        Log::info('ShopBrand deleting', [
            'brand_id' => $brand->brand_id,
            'title' => $brand->title,
            'user_id' => auth()->id(),
        ]);

        return true;
    }

    public function deleted(ShopBrand $brand): void
    {
        $this->clearBrandCaches($brand->brand_id);

        if (function_exists('log_activity')) {
            log_activity($brand, 'silindi', null, $brand->title);
        }

        Log::info('ShopBrand deleted successfully', [
            'brand_id' => $brand->brand_id,
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Handle the ShopBrand "restoring" event.
     */
    public function restoring(ShopBrand $brand): void
    {
        Log::info('ShopBrand restoring', [
            'brand_id' => $brand->brand_id,
            'title' => $brand->title,
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Handle the ShopBrand "restored" event.
     */
    public function restored(ShopBrand $brand): void
    {
        $this->clearBrandCaches();

        if (function_exists('log_activity')) {
            log_activity($brand, 'geri yüklendi');
        }

        Log::info('ShopBrand restored successfully', [
            'brand_id' => $brand->brand_id,
            'title' => $brand->title,
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Handle the ShopBrand "forceDeleting" event.
     */
    public function forceDeleting(ShopBrand $brand): bool
    {
        Log::warning('ShopBrand force deleting', [
            'brand_id' => $brand->brand_id,
            'title' => $brand->title,
            'user_id' => auth()->id(),
        ]);

        return true;
    }

    /**
     * Handle the ShopBrand "forceDeleted" event.
     */
    public function forceDeleted(ShopBrand $brand): void
    {
        $this->clearBrandCaches($brand->brand_id);

        if (function_exists('log_activity')) {
            log_activity($brand, 'kalıcı silindi', null, $brand->title);
        }

        Log::warning('ShopBrand force deleted', [
            'brand_id' => $brand->brand_id,
            'title' => $brand->title,
            'user_id' => auth()->id(),
        ]);
    }

    private function isSlugTaken(string $slug, string $locale, int $ignoreId = 0): bool
    {
        return ShopBrand::where('brand_id', '<>', $ignoreId)
            ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.\"{$locale}\"')) = ?", [$slug])
            ->exists();
    }

    private function generateUniqueSlug(string $slug, string $locale, int $ignoreId = 0): string
    {
        $original = $slug;
        $suffix = 1;

        while ($this->isSlugTaken($slug, $locale, $ignoreId)) {
            $suffix++;
            $slug = "{$original}-{$suffix}";
        }

        return $slug;
    }

    private function clearBrandCaches(?int $brandId = null): void
    {
        $this->cacheService->flushByPrefix('shop_brands');

        if ($brandId !== null) {
            Cache::forget("shop_brand_detail_{$brandId}");
        }
    }
}
