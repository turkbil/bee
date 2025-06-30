<?php

namespace Modules\Announcement\App\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Announcement\App\Contracts\AnnouncementRepositoryInterface;
use Modules\Announcement\App\Models\Announcement;

class AnnouncementService
{
    protected AnnouncementRepositoryInterface $announcementRepository;

    public function __construct(AnnouncementRepositoryInterface $announcementRepository)
    {
        $this->announcementRepository = $announcementRepository;
    }

    /**
     * Duyuru oluştur
     */
    public function create(array $data): Announcement
    {
        try {
            DB::beginTransaction();
            
            // Slug oluştur
            $data = $this->prepareSlugs($data);
            
            // Meta description hazırla
            $data = $this->prepareMetaDescription($data);
            
            // Duyuru oluştur
            $announcement = $this->announcementRepository->create($data);
            
            // Log kaydı
            Log::info('Duyuru oluşturuldu', [
                'announcement_id' => $announcement->announcement_id,
                'title' => $announcement->title,
                'tenant_id' => tenant('id')
            ]);
            
            // Activity log
            if (function_exists('log_activity')) {
                log_activity($announcement, 'oluşturuldu');
            }
            
            DB::commit();
            return $announcement;
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Duyuru oluşturma hatası', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            
            throw $e;
        }
    }

    /**
     * Duyuru güncelle
     */
    public function update(int $id, array $data): Announcement
    {
        try {
            DB::beginTransaction();
            
            // Mevcut duyuru
            $existingAnnouncement = $this->announcementRepository->findById($id);
            if (!$existingAnnouncement) {
                throw new \Exception('Duyuru bulunamadı');
            }
            
            // Slug oluştur
            $data = $this->prepareSlugs($data, $id);
            
            // Meta description hazırla
            $data = $this->prepareMetaDescription($data);
            
            // Duyuru güncelle
            $announcement = $this->announcementRepository->update($id, $data);
            
            // Log kaydı
            Log::info('Duyuru güncellendi', [
                'announcement_id' => $announcement->announcement_id,
                'title' => $announcement->title,
                'tenant_id' => tenant('id')
            ]);
            
            // Activity log
            if (function_exists('log_activity')) {
                log_activity($announcement, 'güncellendi');
            }
            
            DB::commit();
            return $announcement;
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Duyuru güncelleme hatası', [
                'announcement_id' => $id,
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            
            throw $e;
        }
    }

    /**
     * Duyuru sil
     */
    public function delete(int $id): bool
    {
        try {
            DB::beginTransaction();
            
            $announcement = $this->announcementRepository->findById($id);
            if (!$announcement) {
                throw new \Exception('Duyuru bulunamadı');
            }
            
            // Activity log
            if (function_exists('log_activity')) {
                log_activity($announcement, 'silindi');
            }
            
            $result = $this->announcementRepository->delete($id);
            
            Log::info('Duyuru silindi', [
                'announcement_id' => $id,
                'title' => $announcement->title,
                'tenant_id' => tenant('id')
            ]);
            
            DB::commit();
            return $result;
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Duyuru silme hatası', [
                'announcement_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Duyuru listesi (sayfalanmış)
     */
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->announcementRepository->paginate($filters, $perPage);
    }

    /**
     * Duyuru arama
     */
    public function search(array $filters = []): Collection
    {
        return $this->announcementRepository->search($filters);
    }

    /**
     * Aktif duyuruları getir
     */
    public function getActive(): Collection
    {
        return $this->announcementRepository->getActive();
    }

    /**
     * Son duyuruları getir
     */
    public function getRecent(int $limit = 10): Collection
    {
        return $this->announcementRepository->getRecent($limit);
    }

    /**
     * Popüler duyuruları getir
     */
    public function getPopular(int $limit = 10): Collection
    {
        return $this->announcementRepository->getPopular($limit);
    }

    /**
     * Duyuru detayı getir
     */
    public function getById(int $id, array $with = []): ?Announcement
    {
        return $this->announcementRepository->findById($id, $with);
    }

    /**
     * Slug ile duyuru getir
     */
    public function getBySlug(string $slug): ?Announcement
    {
        return $this->announcementRepository->findBySlug($slug);
    }

    /**
     * SEO verilerini güncelle
     */
    public function updateSeo(int $id, array $seoData): Announcement
    {
        return $this->announcementRepository->updateSeo($id, $seoData);
    }

    /**
     * Cache temizle
     */
    public function clearCache(int $id = null): void
    {
        $this->announcementRepository->clearCache($id);
    }

    /**
     * Slug hazırla
     */
    protected function prepareSlugs(array $data, int $announcementId = null): array
    {
        if (isset($data['title']) && is_array($data['title'])) {
            $slugs = [];
            
            foreach ($data['title'] as $locale => $title) {
                if (!empty($title)) {
                    $baseSlug = isset($data['slug'][$locale]) && !empty($data['slug'][$locale]) 
                        ? $data['slug'][$locale] 
                        : Str::slug($title);
                    
                    // Unique slug kontrolü
                    $slugs[$locale] = $this->makeUniqueSlug($baseSlug, $announcementId);
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
    protected function makeUniqueSlug(string $slug, int $announcementId = null): string
    {
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug, $announcementId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    /**
     * Slug varlık kontrolü
     */
    protected function slugExists(string $slug, int $announcementId = null): bool
    {
        $query = Announcement::whereRaw("JSON_EXTRACT(slug, '$.\"" . app()->getLocale() . "\"') = ?", [$slug])
            ->orWhereRaw("JSON_EXTRACT(slug, '$.\"tr\"') = ?", [$slug]);
        
        if ($announcementId) {
            $query->where('announcement_id', '!=', $announcementId);
        }
        
        return $query->exists();
    }
}