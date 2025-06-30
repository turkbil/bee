<?php

namespace Modules\Portfolio\App\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Portfolio\App\Contracts\PortfolioRepositoryInterface;
use Modules\Portfolio\App\Models\Portfolio;

class PortfolioService
{
    protected PortfolioRepositoryInterface $portfolioRepository;

    public function __construct(PortfolioRepositoryInterface $portfolioRepository)
    {
        $this->portfolioRepository = $portfolioRepository;
    }

    /**
     * Portfolio oluştur
     */
    public function create(array $data): Portfolio
    {
        try {
            DB::beginTransaction();
            
            // Slug oluştur
            $data = $this->prepareSlugs($data);
            
            // Meta description hazırla
            $data = $this->prepareMetaDescription($data);
            
            // Portfolio oluştur
            $portfolio = $this->portfolioRepository->create($data);
            
            // Log kaydı
            Log::info('Portfolio oluşturuldu', [
                'portfolio_id' => $portfolio->portfolio_id,
                'title' => $portfolio->title,
                'tenant_id' => tenant('id')
            ]);
            
            // Activity log
            if (function_exists('log_activity')) {
                log_activity($portfolio, 'oluşturuldu');
            }
            
            DB::commit();
            return $portfolio;
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Portfolio oluşturma hatası', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            
            throw $e;
        }
    }

    /**
     * Portfolio güncelle
     */
    public function update(int $id, array $data): Portfolio
    {
        try {
            DB::beginTransaction();
            
            // Mevcut portfolio
            $existingPortfolio = $this->portfolioRepository->findById($id);
            if (!$existingPortfolio) {
                throw new \Exception('Portfolio bulunamadı');
            }
            
            // Slug oluştur
            $data = $this->prepareSlugs($data, $id);
            
            // Meta description hazırla
            $data = $this->prepareMetaDescription($data);
            
            // Portfolio güncelle
            $portfolio = $this->portfolioRepository->update($id, $data);
            
            // Log kaydı
            Log::info('Portfolio güncellendi', [
                'portfolio_id' => $portfolio->portfolio_id,
                'title' => $portfolio->title,
                'tenant_id' => tenant('id')
            ]);
            
            // Activity log
            if (function_exists('log_activity')) {
                log_activity($portfolio, 'güncellendi');
            }
            
            DB::commit();
            return $portfolio;
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Portfolio güncelleme hatası', [
                'portfolio_id' => $id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            
            throw $e;
        }
    }

    /**
     * Portfolio sil
     */
    public function delete(int $id): bool
    {
        try {
            DB::beginTransaction();
            
            $portfolio = $this->portfolioRepository->findById($id);
            if (!$portfolio) {
                throw new \Exception('Portfolio bulunamadı');
            }
            
            // Activity log
            if (function_exists('log_activity')) {
                log_activity($portfolio, 'silindi');
            }
            
            $result = $this->portfolioRepository->delete($id);
            
            Log::info('Portfolio silindi', [
                'portfolio_id' => $id,
                'title' => $portfolio->title,
                'tenant_id' => tenant('id')
            ]);
            
            DB::commit();
            return $result;
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Portfolio silme hatası', [
                'portfolio_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Portfolio listesi (sayfalanmış)
     */
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->portfolioRepository->paginate($filters, $perPage);
    }

    /**
     * Portfolio arama
     */
    public function search(array $filters = []): Collection
    {
        return $this->portfolioRepository->search($filters);
    }

    /**
     * Aktif portfolioları getir
     */
    public function getActive(): Collection
    {
        return $this->portfolioRepository->getActive();
    }

    /**
     * Kategoriye göre portfolioları getir
     */
    public function getByCategory(int $categoryId): Collection
    {
        return $this->portfolioRepository->getByCategory($categoryId);
    }

    /**
     * Son portfolioları getir
     */
    public function getRecent(int $limit = 10): Collection
    {
        return $this->portfolioRepository->getRecent($limit);
    }

    /**
     * Portfolio detayı getir
     */
    public function getById(int $id, array $with = []): ?Portfolio
    {
        return $this->portfolioRepository->findById($id, $with);
    }

    /**
     * SEO verilerini güncelle
     */
    public function updateSeo(int $id, array $seoData): Portfolio
    {
        return $this->portfolioRepository->updateSeo($id, $seoData);
    }

    /**
     * Cache temizle
     */
    public function clearCache(int $id = null): void
    {
        $this->portfolioRepository->clearCache($id);
    }

    /**
     * Slug hazırla
     */
    protected function prepareSlugs(array $data, int $portfolioId = null): array
    {
        if (isset($data['title']) && is_array($data['title'])) {
            $slugs = [];
            
            foreach ($data['title'] as $locale => $title) {
                if (!empty($title)) {
                    $baseSlug = isset($data['slug'][$locale]) && !empty($data['slug'][$locale]) 
                        ? $data['slug'][$locale] 
                        : Str::slug($title);
                    
                    // Unique slug kontrolü
                    $slugs[$locale] = $this->makeUniqueSlug($baseSlug, $portfolioId);
                }
            }
            
            $data['slug'] = $slugs;
        }
        
        return $data;
    }

    /**
     * Meta description hazırla
     */
    protected function prepareMetaDescription(array $data): array
    {
        if (isset($data['body']) && is_array($data['body'])) {
            $metadescs = $data['metadesc'] ?? [];
            
            foreach ($data['body'] as $locale => $body) {
                if (empty($metadescs[$locale]) && !empty($body)) {
                    $metadescs[$locale] = Str::limit(strip_tags($body), 155, '');
                }
            }
            
            $data['metadesc'] = $metadescs;
        }
        
        return $data;
    }

    /**
     * Unique slug oluştur
     */
    protected function makeUniqueSlug(string $slug, int $portfolioId = null): string
    {
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug, $portfolioId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    /**
     * Slug varlık kontrolü
     */
    protected function slugExists(string $slug, int $portfolioId = null): bool
    {
        $query = Portfolio::whereRaw("JSON_EXTRACT(slug, '$.\"" . app()->getLocale() . "\"') = ?", [$slug])
            ->orWhereRaw("JSON_EXTRACT(slug, '$.\"tr\"') = ?", [$slug]);
        
        if ($portfolioId) {
            $query->where('portfolio_id', '!=', $portfolioId);
        }
        
        return $query->exists();
    }
}