<?php

declare(strict_types=1);

namespace Modules\Shop\App\Observers;

use App\Services\TenantCacheService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Shop\App\Exceptions\ShopProtectionException;
use Modules\Shop\App\Exceptions\ShopValidationException;
use Modules\Shop\App\Models\ShopProduct;

class ShopProductObserver
{
    private TenantCacheService $cacheService;

    public function __construct()
    {
        $this->cacheService = app(TenantCacheService::class);
    }

    public function creating(ShopProduct $product): void
    {
        if (empty($product->slug) && !empty($product->title)) {
            $slugs = [];
            foreach ($product->title as $locale => $title) {
                if (!empty($title)) {
                    $slugs[$locale] = Str::slug($title);
                }
            }

            if (!empty($slugs)) {
                $product->slug = $slugs;
            }
        }

        if (!isset($product->is_active)) {
            $product->is_active = true;
        }

        if (!isset($product->currency)) {
            $product->currency = config('shop.defaults.currency', 'TRY');
        }

        Log::info('ShopProduct creating', [
            'title' => $product->title,
            'user_id' => auth()->id(),
        ]);
    }

    public function created(ShopProduct $product): void
    {
        $this->clearProductCaches();

        if (function_exists('log_activity')) {
            log_activity($product, 'oluşturuldu');
        }

        Log::info('ShopProduct created successfully', [
            'product_id' => $product->product_id,
            'title' => $product->title,
            'user_id' => auth()->id(),
        ]);
    }

    public function updating(ShopProduct $product): void
    {
        $dirty = $product->getDirty();

        if (isset($dirty['slug']) && is_array($dirty['slug'])) {
            foreach ($dirty['slug'] as $locale => $slug) {
                if ($this->isSlugTaken($slug, $locale, $product->product_id)) {
                    $dirty['slug'][$locale] = $this->generateUniqueSlug($slug, $locale, $product->product_id);
                }
            }

            $product->slug = $dirty['slug'];
        }

        Log::info('ShopProduct updating', [
            'product_id' => $product->product_id,
            'changed_fields' => array_keys($dirty),
            'user_id' => auth()->id(),
        ]);
    }

    public function updated(ShopProduct $product): void
    {
        $this->clearProductCaches($product->product_id);

        if (function_exists('log_activity')) {
            $changes = $product->getChanges();
            unset($changes['updated_at']);

            if (!empty($changes)) {
                log_activity($product, 'güncellendi', [
                    'changed_fields' => array_keys($changes),
                ]);
            }
        }

        Log::info('ShopProduct updated successfully', [
            'product_id' => $product->product_id,
            'user_id' => auth()->id(),
        ]);
    }

    public function saving(ShopProduct $product): void
    {
        if (is_array($product->title)) {
            $minLength = config('shop.validation.title.min', 3);
            $maxLength = config('shop.validation.title.max', 191);

            foreach ($product->title as $locale => $title) {
                if (!empty($title)) {
                    if (strlen($title) < $minLength) {
                        throw ShopValidationException::titleTooShort($locale, $minLength);
                    }

                    if (strlen($title) > $maxLength) {
                        $product->title[$locale] = mb_substr($title, 0, $maxLength);

                        Log::warning('ShopProduct title auto-trimmed', [
                            'product_id' => $product->product_id,
                            'locale' => $locale,
                            'original_length' => strlen($title),
                            'trimmed_length' => $maxLength,
                        ]);
                    }
                }
            }
        }

        if ($product->price_on_request === false && $product->base_price !== null && $product->compare_at_price !== null) {
            if ((float) $product->compare_at_price <= (float) $product->base_price) {
                $product->compare_at_price = null;

                Log::notice('ShopProduct compare_at_price cleared because it is not higher than base_price', [
                    'product_id' => $product->product_id,
                ]);
            }
        }
    }

    public function saved(ShopProduct $product): void
    {
        Cache::forget("universal_seo_shop_product_{$product->product_id}");

        if (function_exists('responsecache')) {
            responsecache()->forget($product->getUrl());
        }
    }

    public function deleting(ShopProduct $product): bool
    {
        if ($product->is_featured) {
            throw ShopProtectionException::cannotDeleteFeatured($product->product_id);
        }

        Log::info('ShopProduct deleting', [
            'product_id' => $product->product_id,
            'user_id' => auth()->id(),
        ]);

        return true;
    }

    public function deleted(ShopProduct $product): void
    {
        $this->clearProductCaches($product->product_id);

        if (function_exists('log_activity')) {
            log_activity($product, 'silindi');
        }

        Log::info('ShopProduct deleted successfully', [
            'product_id' => $product->product_id,
            'user_id' => auth()->id(),
        ]);
    }

    private function isSlugTaken(string $slug, string $locale, int $ignoreId = 0): bool
    {
        return ShopProduct::where('product_id', '<>', $ignoreId)
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

    private function clearProductCaches(?int $productId = null): void
    {
        $this->cacheService->flushByPrefix('shop_products');

        if ($productId !== null) {
            Cache::forget("shop_product_detail_{$productId}");
        }

        // Sitemap XML cache temizle (yeni ürün için)
        $tenantId = tenant()?->id ?? 'central';
        Cache::forget("sitemap_xml_{$tenantId}");
    }
}
