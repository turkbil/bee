<?php

declare(strict_types=1);

namespace Modules\Shop\App\Observers;

use App\Services\TenantCacheService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Shop\App\Models\ShopCategory;

class ShopCategoryObserver
{
    private TenantCacheService $cacheService;

    public function __construct()
    {
        $this->cacheService = app(TenantCacheService::class);
    }

    public function creating(ShopCategory $category): void
    {
        if (empty($category->slug) && !empty($category->title)) {
            $slugs = [];
            foreach ($category->title as $locale => $title) {
                if (!empty($title)) {
                    $slugs[$locale] = Str::slug($title);
                }
            }

            if (!empty($slugs)) {
                $category->slug = $slugs;
            }
        }

        if (!isset($category->is_active)) {
            $category->is_active = true;
        }

        if (!isset($category->sort_order)) {
            $category->sort_order = 0;
        }

        Log::info('ShopCategory creating', [
            'title' => $category->title,
            'user_id' => auth()->id(),
        ]);
    }

    public function created(ShopCategory $category): void
    {
        $this->clearCategoryCaches();

        if (function_exists('log_activity')) {
            log_activity($category, 'oluşturuldu');
        }

        Log::info('ShopCategory created successfully', [
            'category_id' => $category->category_id,
            'title' => $category->title,
            'user_id' => auth()->id(),
        ]);
    }

    public function updating(ShopCategory $category): void
    {
        $dirty = $category->getDirty();

        if (isset($dirty['slug']) && is_array($dirty['slug'])) {
            foreach ($dirty['slug'] as $locale => $slug) {
                if ($this->isSlugTaken($slug, $locale, $category->category_id)) {
                    $dirty['slug'][$locale] = $this->generateUniqueSlug($slug, $locale, $category->category_id);
                }
            }

            $category->slug = $dirty['slug'];
        }

        Log::info('ShopCategory updating', [
            'category_id' => $category->category_id,
            'changed_fields' => array_keys($dirty),
            'user_id' => auth()->id(),
        ]);
    }

    public function updated(ShopCategory $category): void
    {
        $this->clearCategoryCaches($category->category_id);

        if (function_exists('log_activity')) {
            $changes = $category->getChanges();
            unset($changes['updated_at']);

            if (!empty($changes)) {
                // Eski başlığı al (title değiştiyse)
                $oldTitle = null;
                if (isset($changes['title'])) {
                    $oldTitle = $category->getOriginal('title');
                }

                log_activity($category, 'güncellendi', [
                    'changed_fields' => array_keys($changes),
                ], $oldTitle);
            }
        }

        Log::info('ShopCategory updated successfully', [
            'category_id' => $category->category_id,
            'user_id' => auth()->id(),
        ]);
    }

    public function saving(ShopCategory $category): void
    {
        if (is_array($category->title)) {
            $minLength = 2;
            $maxLength = 191;

            foreach ($category->title as $locale => $title) {
                if (!empty($title)) {
                    if (strlen($title) < $minLength) {
                        throw new \RuntimeException(
                            __("shop::admin.category_title_too_short", ['min' => $minLength, 'locale' => $locale])
                        );
                    }

                    if (strlen($title) > $maxLength) {
                        $category->title[$locale] = mb_substr($title, 0, $maxLength);

                        Log::warning('ShopCategory title auto-trimmed', [
                            'category_id' => $category->category_id,
                            'locale' => $locale,
                            'original_length' => strlen($title),
                            'trimmed_length' => $maxLength,
                        ]);
                    }
                }
            }
        }
    }

    public function saved(ShopCategory $category): void
    {
        Cache::forget("universal_seo_shop_category_{$category->category_id}");

        if (function_exists('responsecache')) {
            responsecache()->forget(route('shop.category', ['slug' => $category->slug]));
        }
    }

    public function deleting(ShopCategory $category): bool
    {
        if ($category->products()->count() > 0) {
            throw new \RuntimeException(__('shop::admin.category_has_products'));
        }

        Log::info('ShopCategory deleting', [
            'category_id' => $category->category_id,
            'title' => $category->title,
            'user_id' => auth()->id(),
        ]);

        return true;
    }

    public function deleted(ShopCategory $category): void
    {
        $this->clearCategoryCaches($category->category_id);

        if (function_exists('log_activity')) {
            log_activity($category, 'silindi', null, $category->title);
        }

        Log::info('ShopCategory deleted successfully', [
            'category_id' => $category->category_id,
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Handle the ShopCategory "restoring" event.
     */
    public function restoring(ShopCategory $category): void
    {
        Log::info('ShopCategory restoring', [
            'category_id' => $category->category_id,
            'title' => $category->title,
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Handle the ShopCategory "restored" event.
     */
    public function restored(ShopCategory $category): void
    {
        $this->clearCategoryCaches();

        if (function_exists('log_activity')) {
            log_activity($category, 'geri yüklendi');
        }

        Log::info('ShopCategory restored successfully', [
            'category_id' => $category->category_id,
            'title' => $category->title,
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Handle the ShopCategory "forceDeleting" event.
     */
    public function forceDeleting(ShopCategory $category): bool
    {
        Log::warning('ShopCategory force deleting', [
            'category_id' => $category->category_id,
            'title' => $category->title,
            'user_id' => auth()->id(),
        ]);

        return true;
    }

    /**
     * Handle the ShopCategory "forceDeleted" event.
     */
    public function forceDeleted(ShopCategory $category): void
    {
        $this->clearCategoryCaches($category->category_id);

        if (function_exists('log_activity')) {
            log_activity($category, 'kalıcı silindi', null, $category->title);
        }

        Log::warning('ShopCategory force deleted', [
            'category_id' => $category->category_id,
            'title' => $category->title,
            'user_id' => auth()->id(),
        ]);
    }

    private function isSlugTaken(string $slug, string $locale, int $ignoreId = 0): bool
    {
        return ShopCategory::where('category_id', '<>', $ignoreId)
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

    private function clearCategoryCaches(?int $categoryId = null): void
    {
        $this->cacheService->flushByPrefix('shop_categories');

        if ($categoryId !== null) {
            Cache::forget("shop_category_detail_{$categoryId}");
        }
    }
}
