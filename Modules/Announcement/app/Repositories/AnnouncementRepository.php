<?php

namespace Modules\Announcement\App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Modules\Announcement\App\Contracts\AnnouncementRepositoryInterface;
use Modules\Announcement\App\Models\Announcement;
use App\Services\TenantCacheManager;

class AnnouncementRepository implements AnnouncementRepositoryInterface
{
    protected TenantCacheManager $cacheManager;
    protected int $cacheMinutes = 60;

    public function __construct(TenantCacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }

    public function findById(int $id, array $with = []): ?Announcement
    {
        $cacheKey = $this->cacheManager->generateKey('announcement', $id, $with);
        
        return $this->cacheManager->remember($cacheKey, function () use ($id, $with) {
            $query = Announcement::query();
            
            if (!empty($with)) {
                $query->with($with);
            }
            
            return $query->find($id);
        }, $this->cacheMinutes);
    }

    public function findBySlug(string $slug): ?Announcement
    {
        $cacheKey = $this->cacheManager->generateKey('announcement_by_slug', $slug);
        
        return $this->cacheManager->remember($cacheKey, function () use ($slug) {
            $locale = app()->getLocale();
            return Announcement::where('is_active', true)
                ->where(function ($query) use ($slug, $locale) {
                    $query->whereRaw("JSON_EXTRACT(slug, '$.\"{$locale}\"') = ?", [$slug])
                          ->orWhereRaw("JSON_EXTRACT(slug, '$.\"tr\"') = ?", [$slug]);
                })
                ->first();
        }, $this->cacheMinutes);
    }

    public function search(array $filters = []): Collection
    {
        $cacheKey = $this->cacheManager->generateKey('announcement_search', $filters);
        
        return $this->cacheManager->remember($cacheKey, function () use ($filters) {
            $query = Announcement::query();

            // Title filtreleme (çok dilli)
            if (!empty($filters['title'])) {
                $locale = app()->getLocale();
                $query->where(function ($q) use ($filters, $locale) {
                    $q->whereRaw("JSON_EXTRACT(title, '$.\"{$locale}\"') LIKE ?", ["%{$filters['title']}%"])
                      ->orWhereRaw("JSON_EXTRACT(title, '$.\"tr\"') LIKE ?", ["%{$filters['title']}%"]);
                });
            }

            // İçerik filtreleme
            if (!empty($filters['body'])) {
                $locale = app()->getLocale();
                $query->where(function ($q) use ($filters, $locale) {
                    $q->whereRaw("JSON_EXTRACT(body, '$.\"{$locale}\"') LIKE ?", ["%{$filters['body']}%"])
                      ->orWhereRaw("JSON_EXTRACT(body, '$.\"tr\"') LIKE ?", ["%{$filters['body']}%"]);
                });
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

            return $query->latest()->get();
        }, $this->cacheMinutes);
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Announcement::query();

        // Arama filtreleme
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $currentLocale = $filters['currentLocale'] ?? 'tr';
            $locales = $filters['locales'] ?? ['tr'];
            
            $query->where(function ($q) use ($search, $currentLocale, $locales) {
                // Title arama
                $q->where(function ($titleQuery) use ($search, $currentLocale, $locales) {
                    $titleQuery->whereRaw("JSON_EXTRACT(title, '$.\"{$currentLocale}\"') LIKE ?", ["%{$search}%"]);
                    foreach ($locales as $locale) {
                        if ($locale !== $currentLocale) {
                            $titleQuery->orWhereRaw("JSON_EXTRACT(title, '$.\"{$locale}\"') LIKE ?", ["%{$search}%"]);
                        }
                    }
                });
                
                // Slug arama
                $q->orWhere(function ($slugQuery) use ($search, $currentLocale, $locales) {
                    $slugQuery->whereRaw("JSON_EXTRACT(slug, '$.\"{$currentLocale}\"') LIKE ?", ["%{$search}%"]);
                    foreach ($locales as $locale) {
                        if ($locale !== $currentLocale) {
                            $slugQuery->orWhereRaw("JSON_EXTRACT(slug, '$.\"{$locale}\"') LIKE ?", ["%{$search}%"]);
                        }
                    }
                });
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Sıralama
        $sortField = $filters['sortField'] ?? 'announcement_id';
        $sortDirection = $filters['sortDirection'] ?? 'desc';
        
        return $query->orderBy($sortField, $sortDirection)->paginate($perPage);
    }

    public function create(array $data): Announcement
    {
        $announcement = Announcement::create($data);
        
        // Cache temizle
        $this->clearCache();
        
        return $announcement;
    }

    public function update(int $id, array $data): Announcement
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->update($data);
        
        // Cache temizle
        $this->clearCache($id);
        
        return $announcement->fresh();
    }

    public function delete(int $id): bool
    {
        $announcement = Announcement::findOrFail($id);
        $result = $announcement->delete();
        
        // Cache temizle
        $this->clearCache($id);
        
        return $result;
    }

    public function getActive(): Collection
    {
        $cacheKey = $this->cacheManager->generateKey('announcement_active');
        
        return $this->cacheManager->remember($cacheKey, function () {
            return Announcement::where('is_active', true)
                ->latest()
                ->get();
        }, $this->cacheMinutes);
    }

    public function getRecent(int $limit = 10): Collection
    {
        $cacheKey = $this->cacheManager->generateKey('announcement_recent', $limit);
        
        return $this->cacheManager->remember($cacheKey, function () use ($limit) {
            return Announcement::where('is_active', true)
                ->latest()
                ->limit($limit)
                ->get();
        }, $this->cacheMinutes);
    }

    public function getPopular(int $limit = 10): Collection
    {
        $cacheKey = $this->cacheManager->generateKey('announcement_popular', $limit);
        
        return $this->cacheManager->remember($cacheKey, function () use ($limit) {
            // Şimdilik created_at'a göre sıralama
            // İleride view count eklenirse oraya göre sıralanabilir
            return Announcement::where('is_active', true)
                ->latest()
                ->limit($limit)
                ->get();
        }, $this->cacheMinutes);
    }

    public function updateSeo(int $id, array $seoData): Announcement
    {
        $announcement = Announcement::findOrFail($id);
        $currentSeo = $announcement->seo ?? [];
        
        // SEO verilerini birleştir
        $newSeo = array_merge($currentSeo, $seoData);
        $announcement->update(['seo' => $newSeo]);
        
        // Cache temizle
        $this->clearCache($id);
        
        return $announcement->fresh();
    }

    public function clearCache(int $id = null): void
    {
        if ($id) {
            // Belirli duyuru cache'ini temizle
            $this->cacheManager->forgetPattern("announcement_{$id}_*");
        } else {
            // Tüm duyuru cache'lerini temizle
            $this->cacheManager->forgetPattern('announcement_*');
        }
    }
}