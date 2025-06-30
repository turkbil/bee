<?php

namespace Modules\Portfolio\App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Modules\Portfolio\App\Contracts\PortfolioRepositoryInterface;
use Modules\Portfolio\App\Models\Portfolio;
use App\Services\TenantCacheManager;

class PortfolioRepository implements PortfolioRepositoryInterface
{
    protected TenantCacheManager $cacheManager;
    protected int $cacheMinutes = 60;

    public function __construct(TenantCacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }

    public function findById(int $id, array $with = []): ?Portfolio
    {
        $cacheKey = $this->cacheManager->generateKey('portfolio', $id, $with);
        
        return $this->cacheManager->remember($cacheKey, function () use ($id, $with) {
            $query = Portfolio::query();
            
            if (!empty($with)) {
                $query->with($with);
            }
            
            return $query->find($id);
        }, $this->cacheMinutes);
    }

    public function search(array $filters = []): Collection
    {
        $cacheKey = $this->cacheManager->generateKey('portfolio_search', $filters);
        
        return $this->cacheManager->remember($cacheKey, function () use ($filters) {
            $query = Portfolio::query();

            // Title filtreleme (çok dilli)
            if (!empty($filters['title'])) {
                $locale = app()->getLocale();
                $query->where(function ($q) use ($filters, $locale) {
                    $q->whereRaw("JSON_EXTRACT(title, '$.\"{$locale}\"') LIKE ?", ["%{$filters['title']}%"])
                      ->orWhereRaw("JSON_EXTRACT(title, '$.\"tr\"') LIKE ?", ["%{$filters['title']}%"]);
                });
            }

            // Kategori filtreleme
            if (!empty($filters['category_id'])) {
                $query->where('portfolio_category_id', $filters['category_id']);
            }

            // Durum filtreleme
            if (isset($filters['is_active'])) {
                $query->where('is_active', $filters['is_active']);
            }

            // Tarih filtreleme
            if (!empty($filters['date_from'])) {
                $query->whereDate('created_at', '>=', $filters['date_from']);
            }

            if (!empty($filters['date_to'])) {
                $query->whereDate('created_at', '<=', $filters['date_to']);
            }

            return $query->with('category')->latest()->get();
        }, $this->cacheMinutes);
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Portfolio::query();

        // Filtreleri uygula
        if (!empty($filters['title'])) {
            $locale = app()->getLocale();
            $query->where(function ($q) use ($filters, $locale) {
                $q->whereRaw("JSON_EXTRACT(title, '$.\"{$locale}\"') LIKE ?", ["%{$filters['title']}%"])
                  ->orWhereRaw("JSON_EXTRACT(title, '$.\"tr\"') LIKE ?", ["%{$filters['title']}%"]);
            });
        }

        if (!empty($filters['category_id'])) {
            $query->where('portfolio_category_id', $filters['category_id']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->with('category')->latest()->paginate($perPage);
    }

    public function create(array $data): Portfolio
    {
        $portfolio = Portfolio::create($data);
        
        // Cache temizle
        $this->clearCache();
        
        return $portfolio;
    }

    public function update(int $id, array $data): Portfolio
    {
        $portfolio = Portfolio::findOrFail($id);
        $portfolio->update($data);
        
        // Cache temizle
        $this->clearCache($id);
        
        return $portfolio->fresh();
    }

    public function delete(int $id): bool
    {
        $portfolio = Portfolio::findOrFail($id);
        $result = $portfolio->delete();
        
        // Cache temizle
        $this->clearCache($id);
        
        return $result;
    }

    public function getActive(): Collection
    {
        $cacheKey = $this->cacheManager->generateKey('portfolio_active');
        
        return $this->cacheManager->remember($cacheKey, function () {
            return Portfolio::where('is_active', true)
                ->with('category')
                ->latest()
                ->get();
        }, $this->cacheMinutes);
    }

    public function getByCategory(int $categoryId): Collection
    {
        $cacheKey = $this->cacheManager->generateKey('portfolio_by_category', $categoryId);
        
        return $this->cacheManager->remember($cacheKey, function () use ($categoryId) {
            return Portfolio::where('portfolio_category_id', $categoryId)
                ->where('is_active', true)
                ->with('category')
                ->latest()
                ->get();
        }, $this->cacheMinutes);
    }

    public function getRecent(int $limit = 10): Collection
    {
        $cacheKey = $this->cacheManager->generateKey('portfolio_recent', $limit);
        
        return $this->cacheManager->remember($cacheKey, function () use ($limit) {
            return Portfolio::where('is_active', true)
                ->with('category')
                ->latest()
                ->limit($limit)
                ->get();
        }, $this->cacheMinutes);
    }

    public function updateSeo(int $id, array $seoData): Portfolio
    {
        $portfolio = Portfolio::findOrFail($id);
        $currentSeo = $portfolio->seo ?? [];
        
        // SEO verilerini birleştir
        $newSeo = array_merge($currentSeo, $seoData);
        $portfolio->update(['seo' => $newSeo]);
        
        // Cache temizle
        $this->clearCache($id);
        
        return $portfolio->fresh();
    }

    public function clearCache(int $id = null): void
    {
        if ($id) {
            // Belirli portfolio cache'ini temizle
            $this->cacheManager->forgetPattern("portfolio_{$id}_*");
        } else {
            // Tüm portfolio cache'lerini temizle
            $this->cacheManager->forgetPattern('portfolio_*');
        }
    }
}