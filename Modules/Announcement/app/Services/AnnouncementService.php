<?php

declare(strict_types=1);

namespace Modules\Announcement\App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\{DB, Log};
use Modules\Announcement\App\Contracts\AnnouncementRepositoryInterface;
use App\Contracts\GlobalSeoRepositoryInterface;
use App\Services\GlobalTabService;
use Modules\Announcement\App\Models\Announcement;
use Modules\Announcement\App\DataTransferObjects\AnnouncementOperationResult;
use Modules\Announcement\App\Exceptions\{AnnouncementNotFoundException, AnnouncementCreationException};
use Throwable;

readonly class AnnouncementService
{
    public function __construct(
        private AnnouncementRepositoryInterface $announcementRepository,
        private GlobalSeoRepositoryInterface $seoRepository
    ) {}
    
    public function getAnnouncement(int $id): Announcement
    {
        return $this->announcementRepository->findById($id) 
            ?? throw AnnouncementNotFoundException::withId($id);
    }
    
    public function getAnnouncementBySlug(string $slug, string $locale = 'tr'): Announcement
    {
        return $this->announcementRepository->findBySlug($slug, $locale)
            ?? throw AnnouncementNotFoundException::withSlug($slug, $locale);
    }
    
    public function getActiveAnnouncements(): Collection
    {
        return $this->announcementRepository->getActive();
    }
    
    public function getPaginatedAnnouncements(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->announcementRepository->paginate($filters, $perPage);
    }
    
    public function searchAnnouncements(string $term, array $locales = []): Collection
    {
        return $this->announcementRepository->search($term, $locales);
    }
    
    public function createAnnouncement(array $data): AnnouncementOperationResult
    {
        try {
            // Slug otomatik oluşturma
            if (isset($data['title']) && is_array($data['title'])) {
                $data['slug'] = $this->generateSlugsFromTitles($data['title']);
            }
            
            // SEO verileri hazırlama
            if (isset($data['seo']) && is_array($data['seo'])) {
                $data['seo'] = $this->prepareSeoData($data['seo']);
            }
            
            $announcement = $this->announcementRepository->create($data);
            
            Log::info('Announcement created', [
                'announcement_id' => $announcement->announcement_id,
                'title' => $announcement->title,
                'user_id' => auth()->id()
            ]);
            
            return AnnouncementOperationResult::success(
                message: __('announcement::admin.announcement_created_successfully'),
                data: $announcement
            );
            
        } catch (Throwable $e) {
            Log::error('Announcement creation failed', [
                'error' => $e->getMessage(),
                'data' => $data,
                'user_id' => auth()->id()
            ]);
            
            throw AnnouncementCreationException::withDatabaseError($e->getMessage());
        }
    }
    
    public function updateAnnouncement(int $id, array $data): AnnouncementOperationResult
    {
        try {
            $announcement = $this->announcementRepository->findById($id)
                ?? throw AnnouncementNotFoundException::withId($id);
            
            // Slug güncelleme
            if (isset($data['title']) && is_array($data['title'])) {
                $data['slug'] = $this->generateSlugsFromTitles($data['title'], $id);
            }
            
            // SEO verileri hazırlama
            if (isset($data['seo']) && is_array($data['seo'])) {
                $data['seo'] = $this->prepareSeoData($data['seo']);
            }
            
            $announcement = $this->announcementRepository->update($id, $data);
            
            Log::info('Announcement updated', [
                'announcement_id' => $announcement->announcement_id,
                'title' => $announcement->title,
                'user_id' => auth()->id()
            ]);
            
            return AnnouncementOperationResult::success(
                message: __('announcement::admin.announcement_updated_successfully'),
                data: $announcement
            );
            
        } catch (Throwable $e) {
            Log::error('Announcement update failed', [
                'announcement_id' => $id,
                'error' => $e->getMessage(),
                'data' => $data,
                'user_id' => auth()->id()
            ]);
            
            return AnnouncementOperationResult::error(
                message: __('announcement::admin.announcement_update_failed')
            );
        }
    }
    
    public function deleteAnnouncement(int $id): AnnouncementOperationResult
    {
        try {
            $announcement = $this->announcementRepository->findById($id)
                ?? throw AnnouncementNotFoundException::withId($id);
            
            $this->announcementRepository->delete($id);
            
            Log::info('Announcement deleted', [
                'announcement_id' => $id,
                'title' => $announcement->title,
                'user_id' => auth()->id()
            ]);
            
            return AnnouncementOperationResult::success(
                message: __('announcement::admin.announcement_deleted_successfully')
            );
            
        } catch (Throwable $e) {
            Log::error('Announcement deletion failed', [
                'announcement_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return AnnouncementOperationResult::error(
                message: __('announcement::admin.announcement_deletion_failed')
            );
        }
    }
    
    public function prepareAnnouncementForForm(int $id, string $currentLanguage): array
    {
        $announcement = $this->getAnnouncement($id);
        
        // SEO verilerini yükle
        $seoData = $this->seoRepository->getSeoData(
            $announcement,
            $currentLanguage
        );
        
        // Tab completion durumunu hesapla  
        $announcementData = array_merge(
            $announcement->only(['is_active']),
            [
                'title' => $announcement->getTranslated('title', $currentLanguage) ?? '',
                'body' => $announcement->getTranslated('body', $currentLanguage) ?? '',
                'slug' => $announcement->getTranslated('slug', $currentLanguage) ?? '',
            ],
            $seoData
        );
        
        $tabCompletion = GlobalTabService::getTabCompletionStatus($announcementData, 'announcement');
        
        // SEO limitleri
        $seoLimits = [
            'title' => ['min' => 30, 'max' => 60],
            'description' => ['min' => 120, 'max' => 160],
            'keywords' => ['min' => 3, 'max' => 10]
        ];
        
        return [
            'announcement' => $announcement,
            'seoData' => $seoData,
            'tabCompletion' => $tabCompletion,
            'seoLimits' => $seoLimits
        ];
    }
    
    // Eski methodları da koruyalım (backward compatibility)
    public function create(array $data): Announcement
    {
        $result = $this->createAnnouncement($data);
        return $result->data;
    }
    
    public function update(int $id, array $data): Announcement
    {
        $result = $this->updateAnnouncement($id, $data);
        return $result->data;
    }
    
    public function delete(int $id): bool
    {
        $result = $this->deleteAnnouncement($id);
        return $result->success;
    }
    
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->getPaginatedAnnouncements($filters, $perPage);
    }
    
    public function search(array $filters = []): Collection
    {
        // Eski arayüzü destekle
        $term = $filters['search'] ?? '';
        $locales = $filters['locales'] ?? [];
        return $this->searchAnnouncements($term, $locales);
    }
    
    public function getActive(): Collection
    {
        return $this->getActiveAnnouncements();
    }
    
    public function getRecent(int $limit = 10): Collection
    {
        return $this->announcementRepository->getRecent($limit);
    }
    
    public function getPopular(int $limit = 10): Collection
    {
        return $this->announcementRepository->getPopular($limit);
    }
    
    public function getById(int $id, array $with = []): ?Announcement
    {
        return $this->announcementRepository->findById($id, $with);
    }
    
    public function getBySlug(string $slug): ?Announcement
    {
        return $this->announcementRepository->findBySlug($slug);
    }
    
    public function updateSeo(int $id, array $seoData): Announcement
    {
        return $this->announcementRepository->updateSeo($id, $seoData);
    }
    
    public function clearCache(int $id = null): void
    {
        $this->announcementRepository->clearCache($id);
    }
    
    public function toggleAnnouncementStatus(int $id): AnnouncementOperationResult
    {
        try {
            $announcement = $this->announcementRepository->findById($id)
                ?? throw AnnouncementNotFoundException::withId($id);
            
            $newStatus = !$announcement->is_active;
            $announcement = $this->announcementRepository->update($id, ['is_active' => $newStatus]);
            
            $message = $newStatus 
                ? __('admin.item_activated_successfully')
                : __('admin.item_deactivated_successfully');
            
            Log::info('Announcement status toggled', [
                'announcement_id' => $id,
                'new_status' => $newStatus,
                'user_id' => auth()->id()
            ]);
            
            return AnnouncementOperationResult::success(
                message: $message,
                data: $announcement,
                meta: ['new_status' => $newStatus]
            );
            
        } catch (Throwable $e) {
            Log::error('Announcement status toggle failed', [
                'announcement_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return AnnouncementOperationResult::error(
                message: __('admin.operation_failed')
            );
        }
    }
    
    /**
     * Başlıklardan slug oluştur
     */
    protected function generateSlugsFromTitles(array $titles, int $announcementId = null): array
    {
        $slugs = [];
        
        foreach ($titles as $locale => $title) {
            if (!empty($title)) {
                $baseSlug = \Illuminate\Support\Str::slug($title);
                $slugs[$locale] = $this->makeUniqueSlug($baseSlug, $locale, $announcementId);
            }
        }
        
        return $slugs;
    }
    
    /**
     * SEO verilerini hazırla
     */
    protected function prepareSeoData(array $seoData): array
    {
        // SEO verilerini normalize et
        return array_filter($seoData, fn($value) => !empty(trim($value)));
    }
    
    /**
     * Unique slug oluştur
     */
    protected function makeUniqueSlug(string $slug, string $locale, int $announcementId = null): string
    {
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug, $locale, $announcementId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    /**
     * Slug varlık kontrolü
     */
    protected function slugExists(string $slug, string $locale, int $announcementId = null): bool
    {
        $query = Announcement::whereRaw("JSON_EXTRACT(slug, '$.\"" . $locale . "\"') = ?", [$slug]);
        
        if ($announcementId) {
            $query->where('announcement_id', '!=', $announcementId);
        }
        
        return $query->exists();
    }
}