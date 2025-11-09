<?php

declare(strict_types=1);

namespace Modules\Payment\App\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use App\Services\TenantCacheService;
use App\Services\TenantLanguageProvider;
use Modules\Payment\App\Contracts\PaymentRepositoryInterface;
use Modules\Payment\App\Models\Payment;
use Modules\Payment\App\Enums\CacheStrategy;

readonly class PaymentRepository implements PaymentRepositoryInterface
{
    private readonly string $cachePrefix;
    private readonly int $cacheTtl;
    private readonly TenantCacheService $cache;

    public function __construct(
        private Payment $model
    ) {
        $this->cachePrefix = TenantCacheService::PREFIX_PAYMENT;
        $this->cacheTtl = (int) config('modules.cache.ttl.list', 3600);
        $this->cache = app(TenantCacheService::class);
    }

    public function findById(int $id): ?Payment
    {
        $strategy = CacheStrategy::fromRequest();

        if (!$strategy->shouldCache()) {
            return $this->model->where('payment_id', $id)->first();
        }

        $cacheKey = $this->getCacheKey("find_by_id.{$id}");

        return $this->cache->remember(
            $this->cachePrefix,
            "find_by_id.{$id}",
            $strategy->getCacheTtl(),
            fn() => $this->model->where('payment_id', $id)->first()
        );
    }

    public function findByIdWithSeo(int $id): ?Payment
    {
        $strategy = CacheStrategy::fromRequest();

        // Admin panelinde cache kullanma
        if ($strategy === CacheStrategy::ADMIN_FRESH) {
            return $this->model->with('seoSetting')->where('payment_id', $id)->first();
        }

        $cacheKey = $this->getCacheKey("find_by_id_with_seo.{$id}");

        return $this->cache->remember(
            $this->cachePrefix,
            "find_by_id_with_seo.{$id}",
            $strategy->getCacheTtl(),
            fn() => $this->model->with('seoSetting')->where('payment_id', $id)->first()
        );
    }

    public function findBySlug(string $slug, string $locale = 'tr'): ?Payment
    {
        $strategy = CacheStrategy::PUBLIC_CACHED; // Always cache for SEO
        $cacheKey = $this->getCacheKey("find_by_slug.{$slug}.{$locale}");

        return Cache::tags($this->getCacheTags())
            ->remember(
                $cacheKey,
                $strategy->getCacheTtl(),
                fn() =>
                $this->model->where(function ($query) use ($slug, $locale) {
                    $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) = ?", [$slug])
                        ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.tr')) = ?", [$slug]);
                })->active()->first()
            );
    }

    public function getActive(): Collection
    {
        $strategy = CacheStrategy::fromRequest();

        if (!$strategy->shouldCache()) {
            return $this->model->active()->orderBy('payment_id', 'desc')->get();
        }

        $cacheKey = $this->getCacheKey('active_payments');

        return Cache::tags($this->getCacheTags())
            ->remember(
                $cacheKey,
                $strategy->getCacheTtl(),
                fn() =>
                $this->model->active()->orderBy('payment_id', 'desc')->get()
            );
    }

    public function getPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        // Search filter
        if (!empty($filters['search'])) {
            $searchTerm = '%' . $filters['search'] . '%';
            $locales = $filters['locales'] ?? TenantLanguageProvider::getActiveLanguageCodes();

            $query->where(function ($subQuery) use ($searchTerm, $locales) {
                foreach ($locales as $locale) {
                    $subQuery->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.{$locale}')) COLLATE utf8mb4_unicode_ci LIKE ?", [$searchTerm])
                        ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.{$locale}')) COLLATE utf8mb4_unicode_ci LIKE ?", [$searchTerm]);
                }
            });
        }

        // Status filter
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        // Sorting
        $sortField = $filters['sortField'] ?? 'payment_id';
        $sortDirection = $filters['sortDirection'] ?? 'desc';

        if ($sortField === 'title') {
            $locale = $filters['currentLocale'] ?? 'tr';
            $query->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.{$locale}')) {$sortDirection}");
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        // 🚀 PERFORMANCE FIX: Eager loading ile N+1 query sorununu çöz
        return $query->with(['seoSetting'])->paginate($perPage);
    }

    public function search(string $term, array $locales = []): Collection
    {
        if (empty($locales)) {
            $locales = TenantLanguageProvider::getActiveLanguageCodes();
        }

        $searchTerm = '%' . $term . '%';

        return $this->model->where(function ($query) use ($searchTerm, $locales) {
            foreach ($locales as $locale) {
                $query->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(title, '$.{$locale}')) COLLATE utf8mb4_unicode_ci LIKE ?", [$searchTerm])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(body, '$.{$locale}')) COLLATE utf8mb4_unicode_ci LIKE ?", [$searchTerm]);
            }
        })->active()->get();
    }

    public function create(array $data): Payment
    {
        $payment = $this->model->create($data);
        $this->clearCache();

        return $payment;
    }

    public function update(int $id, array $data): bool
    {
        $result = $this->model->where('payment_id', $id)->update($data);

        if ($result) {
            $this->clearCache();
        }

        return (bool) $result;
    }

    public function delete(int $id): bool
    {
        $result = $this->model->where('payment_id', $id)->delete();

        if ($result) {
            $this->clearCache();
        }

        return (bool) $result;
    }

    public function toggleActive(int $id): bool
    {
        // 🚨 PERFORMANCE FIX: Tek sorguda toggle yap, gereksiz findById kaldır
        $payment = $this->model->where('payment_id', $id)->first(['payment_id', 'is_active']);

        if (!$payment) {
            return false;
        }

        $result = $this->model->where('payment_id', $id)->update(['is_active' => !$payment->is_active]);

        if ($result) {
            $this->clearCache();
        }

        return (bool) $result;
    }

    public function bulkDelete(array $ids): int
    {
        $count = $this->model->whereIn('payment_id', $ids)->delete();

        if ($count > 0) {
            $this->clearCache();
        }

        return $count;
    }

    public function bulkToggleActive(array $ids): int
    {
        if (empty($ids)) {
            return 0;
        }

        // Önce mevcut durumları al
        $payments = $this->model->whereIn('payment_id', $ids)->get(['payment_id', 'is_active']);
        $count = 0;

        foreach ($payments as $payment) {
            $this->model->where('payment_id', $payment->payment_id)
                ->update(['is_active' => !$payment->is_active]);
            $count++;
        }

        if ($count > 0) {
            $this->clearCache();
        }

        return $count;
    }

    public function updateSeoField(int $id, string $locale, string $field, mixed $value): bool
    {
        $payment = $this->model->where('payment_id', $id)->first(['payment_id', 'seo']);

        if (!$payment) {
            return false;
        }

        $seo = $payment->seo ?? [];
        $seo[$locale][$field] = $value;

        $result = $this->model->where('payment_id', $id)->update(['seo' => $seo]);

        if ($result) {
            $this->clearCache();
        }

        return (bool) $result;
    }

    public function clearCache(): void
    {
        $this->cache->flushByPrefix($this->cachePrefix);
    }

    protected function getCacheKey(string $key): string
    {
        return $this->cache->key($this->cachePrefix, $key);
    }

    protected function getCacheTags(): array
    {
        return $this->cache->tags([$this->cachePrefix]);
    }
}
