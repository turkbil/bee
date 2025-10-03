<?php

declare(strict_types=1);

namespace Modules\Announcement\App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Modules\Announcement\App\Contracts\AnnouncementRepositoryInterface;
use App\Contracts\GlobalSeoRepositoryInterface;
use App\Services\GlobalTabService;
use Modules\Announcement\App\Models\Announcement;
use Modules\Announcement\App\DataTransferObjects\{AnnouncementOperationResult, BulkOperationResult};
use Modules\Announcement\App\Exceptions\{AnnouncementNotFoundException, AnnouncementCreationException, AnnouncementProtectionException};
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
        return $this->announcementRepository->getPaginated($filters, $perPage);
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
                message: __('announcement::admin.page_created_successfully'),
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
                $data['slug'] = $this->generateSlugsFromTitles($data['title'], $announcement->slug ?? []);
            }

            // SEO verileri hazırlama
            if (isset($data['seo']) && is_array($data['seo'])) {
                $data['seo'] = $this->prepareSeoData($data['seo'], $announcement->seo ?? []);
            }

            $this->announcementRepository->update($id, $data);

            Log::info('Announcement updated', [
                'announcement_id' => $id,
                'title' => $data['title'] ?? 'unchanged',
                'user_id' => auth()->id()
            ]);

            return AnnouncementOperationResult::success(
                message: __('announcement::admin.page_updated_successfully'),
                data: $announcement->refresh()
            );
        } catch (AnnouncementNotFoundException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Announcement update failed', [
                'announcement_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return AnnouncementOperationResult::error(
                message: __('announcement::admin.update_failed'),
                type: 'error'
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
                message: __('announcement::admin.page_deleted_successfully')
            );
        } catch (AnnouncementNotFoundException | AnnouncementProtectionException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Announcement deletion failed', [
                'announcement_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return AnnouncementOperationResult::error(
                message: __('announcement::admin.deletion_failed'),
                type: 'error'
            );
        }
    }

    public function toggleAnnouncementStatus(int $id): AnnouncementOperationResult
    {
        try {
            $announcement = $this->announcementRepository->findById($id)
                ?? throw AnnouncementNotFoundException::withId($id);

            $this->announcementRepository->toggleActive($id);
            $announcement->refresh();

            Log::info('Announcement status toggled', [
                'announcement_id' => $id,
                'new_status' => $announcement->is_active,
                'user_id' => auth()->id()
            ]);

            return AnnouncementOperationResult::success(
                message: __($announcement->is_active ? 'admin.activated' : 'admin.deactivated'),
                data: $announcement,
                meta: ['new_status' => $announcement->is_active]
            );
        } catch (AnnouncementNotFoundException $e) {
            return AnnouncementOperationResult::error(
                message: __('admin.page_not_found'),
                type: 'error'
            );
        } catch (AnnouncementProtectionException $e) {
            return AnnouncementOperationResult::warning(
                message: $e->getMessage()
            );
        } catch (Throwable $e) {
            Log::error('Announcement status toggle failed', [
                'announcement_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return AnnouncementOperationResult::error(
                message: __('admin.operation_failed'),
                type: 'error'
            );
        }
    }

    public function bulkDeleteAnnouncements(array $ids): BulkOperationResult
    {
        try {
            if (empty($ids)) {
                return BulkOperationResult::failure(
                    message: __('admin.no_announcements_selected')
                );
            }

            $deletedCount = $this->announcementRepository->bulkDelete($ids);

            Log::info('Bulk delete performed', [
                'deleted_count' => $deletedCount,
                'user_id' => auth()->id()
            ]);

            return BulkOperationResult::success(
                message: __('admin.deleted_successfully'),
                affectedCount: $deletedCount
            );
        } catch (Throwable $e) {
            Log::error('Bulk delete failed', [
                'error' => $e->getMessage(),
                'ids' => $ids,
                'user_id' => auth()->id()
            ]);

            return BulkOperationResult::failure(
                message: __('admin.bulk_operation_failed'),
                errors: [$e->getMessage()]
            );
        }
    }

    public function bulkToggleAnnouncementStatus(array $ids): BulkOperationResult
    {
        try {
            $affectedCount = $this->announcementRepository->bulkToggleActive($ids);

            Log::info('Bulk status toggle performed', [
                'affected_count' => $affectedCount,
                'user_id' => auth()->id()
            ]);

            return BulkOperationResult::success(
                message: __('admin.updated_successfully'),
                affectedCount: $affectedCount
            );
        } catch (Throwable $e) {
            Log::error('Bulk status toggle failed', [
                'error' => $e->getMessage(),
                'ids' => $ids,
                'user_id' => auth()->id()
            ]);

            return BulkOperationResult::failure(
                message: __('admin.bulk_operation_failed'),
                errors: [$e->getMessage()]
            );
        }
    }

    public function updateSeoField(int $id, string $locale, string $field, mixed $value): bool
    {
        $result = $this->announcementRepository->updateSeoField($id, $locale, $field, $value);

        if ($result) {
            Log::info('SEO field updated', [
                'announcement_id' => $id,
                'locale' => $locale,
                'field' => $field,
                'user_id' => auth()->id()
            ]);
        }

        return $result;
    }

    protected function generateSlugsFromTitles(array $titles, array $existingSlugs = []): array
    {
        $slugs = $existingSlugs;

        foreach ($titles as $locale => $title) {
            if (!empty($title) && empty($slugs[$locale])) {
                $slugs[$locale] = \Str::slug($title);
            }
        }

        return $slugs;
    }

    protected function prepareSeoData(array $seoData, array $existingSeo = []): array
    {
        $prepared = $existingSeo;

        foreach ($seoData as $locale => $data) {
            if (is_array($data)) {
                // Boş değerleri temizle
                $cleanData = array_filter($data, function ($value) {
                    return !is_null($value) && $value !== '' && $value !== [];
                });

                if (!empty($cleanData)) {
                    $prepared[$locale] = array_merge($prepared[$locale] ?? [], $cleanData);
                }
            }
        }

        return $prepared;
    }

    /**
     * Sayfa verilerini form için hazırla
     */
    public function prepareAnnouncementForForm(int $id, string $language): array
    {
        // 🚨 PERFORMANCE FIX: Eager loading ile bir seferde çek
        $announcement = $this->announcementRepository->findByIdWithSeo($id);

        if (!$announcement) {
            return $this->getEmptyFormData($language);
        }

        // 🚨 PERFORMANCE FIX: SEO data'yı sadece bir kez çek
        $seoData = $this->seoRepository->getSeoData($announcement, $language);

        // Tab completion durumunu hesapla
        $allData = array_merge($announcement->toArray(), $seoData);
        $tabCompletion = GlobalTabService::getTabCompletionStatus($allData, 'announcement');

        return [
            'announcement' => $announcement,
            'seoData' => $seoData, // Tekrar çekme!
            'tabCompletion' => $tabCompletion,
            'tabConfig' => GlobalTabService::getJavaScriptConfig('announcement'),
            'seoLimits' => $this->seoRepository->getFieldLimits('announcement')
        ];
    }

    /**
     * Yeni sayfa için boş form verisi
     */
    public function getEmptyFormData(string $language): array
    {
        $emptyData = [
            'title' => '',
            'body' => '',
            'slug' => '',
            'seo_title' => '',
            'seo_description' => '',
            'seo_keywords' => '',
            'canonical_url' => ''
        ];

        return [
            'announcement' => null,
            'seoData' => $emptyData,
            'tabCompletion' => GlobalTabService::getTabCompletionStatus($emptyData, 'announcement'),
            'tabConfig' => GlobalTabService::getJavaScriptConfig('announcement'),
            'seoLimits' => $this->seoRepository->getFieldLimits('announcement')
        ];
    }

    /**
     * Form validation kurallarını getir
     */
    public function getValidationRules(array $availableLanguages): array
    {
        $rules = [
            'inputs.is_active' => 'boolean',
        ];

        // Çoklu dil alanları
        foreach ($availableLanguages as $lang) {
            $rules["multiLangInputs.{$lang}.title"] = $lang === 'tr' ? 'required|min:3|max:255' : 'nullable|min:3|max:255';
            $rules["multiLangInputs.{$lang}.slug"] = 'nullable|string|max:255';
            $rules["multiLangInputs.{$lang}.body"] = 'nullable|string';
        }

        // SEO validation kuralları
        $seoRules = $this->seoRepository->getValidationRules('announcement');

        return array_merge($rules, $seoRules);
    }

    /**
     * SEO skorunu hesapla
     */
    public function calculateSeoScore(array $seoData): array
    {
        return $this->seoRepository->calculateSeoScore($seoData, 'announcement');
    }

    public function clearCache(): void
    {
        $this->announcementRepository->clearCache();

        Log::info('Announcement cache cleared', [
            'user_id' => auth()->id()
        ]);
    }
}
