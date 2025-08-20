<?php

declare(strict_types=1);

namespace Modules\Announcement\App\Http\Livewire\Admin;

use Livewire\Attributes\{Url, Layout, Computed};
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Announcement\App\Http\Livewire\Traits\{InlineEditTitle, WithBulkActions};
use Modules\Announcement\App\Services\AnnouncementService;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Modules\Announcement\App\DataTransferObjects\AnnouncementOperationResult;
use Modules\Announcement\App\Models\Announcement;
use App\Traits\HasUniversalTranslation;

#[Layout('admin.layout')]
class AnnouncementComponent extends Component
{
    use WithPagination, WithBulkActions, InlineEditTitle, HasUniversalTranslation;

    #[Url]
    public $search = '';

    #[Url]
    public $perPage = 10;

    #[Url]
    public $sortField = 'announcement_id';

    #[Url]
    public $sortDirection = 'desc';

    // Hibrit dil sistemi için dinamik dil listesi
    private ?array $availableSiteLanguages = null;
    
    // Event listeners
    protected $listeners = ['refreshAnnouncementData' => 'refreshAnnouncementData'];
    
    private AnnouncementService $announcementService;
    
    public function boot(AnnouncementService $announcementService): void
    {
        $this->announcementService = $announcementService;
    }
    
    public function refreshAnnouncementData()
    {
        // Cache'leri temizle
        $this->availableSiteLanguages = null;
        $this->announcementService->clearCache();
        
        // Component'i yeniden render et
        $this->render();
    }

    protected function getModelClass()
    {
        return Announcement::class;
    }

    #[Computed]
    public function availableSiteLanguages(): array
    {
        return $this->availableSiteLanguages ??= TenantLanguage::where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('code')
            ->toArray();
    }

    #[Computed]
    public function adminLocale(): string
    {
        return session('admin_locale', \App\Services\TenantLanguageProvider::getDefaultLanguageCode());
    }

    #[Computed]
    public function siteLocale(): string
    {
        // Query string'den data_lang_changed parametresini kontrol et
        $dataLangChanged = request()->get('data_lang_changed');
        
        // Eğer query string'de dil değişim parametresi varsa onu kullan
        if ($dataLangChanged && in_array($dataLangChanged, $this->availableSiteLanguages)) {
            // Session'ı da güncelle (query'den gelen dili session'a yaz)
            session(['tenant_locale' => $dataLangChanged]);
            session()->save();
            
            return $dataLangChanged;
        }
        
        // 1. Kullanıcının kendi tenant_locale tercihi (en yüksek öncelik)
        if (auth()->check() && auth()->user()->tenant_locale) {
            $userLocale = auth()->user()->tenant_locale;
            
            // Session'ı da güncelle
            if (session('tenant_locale') !== $userLocale) {
                session(['tenant_locale' => $userLocale]);
            }
            
            return $userLocale;
        }
        
        // 2. Session fallback
        return session('tenant_locale', \App\Services\TenantLanguageProvider::getDefaultLanguageCode());
    }

    public function updatedPerPage()
    {
        $this->perPage = (int) $this->perPage;
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField     = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleActive(int $id): void
    {
        try {
            $result = $this->announcementService->toggleAnnouncementStatus($id);
            
            $this->dispatch('toast', [
                'title' => $result->success ? __('admin.success') : __('admin.' . $result->type),
                'message' => $result->message,
                'type' => $result->type,
            ]);
            
            if ($result->success && $result->meta) {
                log_activity(
                    $result->data,
                    $result->meta['new_status'] ? __('admin.activated') : __('admin.deactivated')
                );
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('admin.operation_failed'),
                'type' => 'error',
            ]);
        }
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $filters = [
            'search' => $this->search,
            'locales' => $this->availableSiteLanguages,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'currentLocale' => $this->siteLocale
        ];
        
        $announcements = $this->announcementService->getPaginatedAnnouncements($filters, $this->perPage);
    
        return view('announcement::admin.livewire.announcement-component', [
            'announcements' => $announcements,
            'currentSiteLocale' => $this->siteLocale,
            'siteLanguages' => $this->availableSiteLanguages,
        ]);
    }

    public function queueTranslation($announcementId, $sourceLanguage, $targetLanguages, $overwriteExisting = true)
    {
        try {
            \Log::info("🚀 ANNOUNCEMENT QUEUE Translation başlatıldı", [
                'announcement_id' => $announcementId,
                'source' => $sourceLanguage,
                'targets' => $targetLanguages
            ]);

            // Job'ı kuyruğa ekle
            \Modules\AI\app\Jobs\TranslateEntityJob::dispatch(
                'announcement',
                $announcementId,
                $sourceLanguage,
                $targetLanguages,
                $overwriteExisting
            );

            $this->dispatch('translationQueued', 'Announcement çeviri işlemi başlatıldı!');
            
        } catch (\Exception $e) {
            \Log::error('❌ Announcement queue translation hatası', [
                'announcement_id' => $announcementId,
                'error' => $e->getMessage()
            ]);
            
            $this->dispatch('translationError', 'Announcement çeviri kuyruğu hatası: ' . $e->getMessage());
        }
    }

    public function translateFromModal(int $announcementId, string $sourceLanguage, array $targetLanguages): void
    {
        try {
            \Log::info('🌍 Announcement Translation modal çeviri başlatıldı', [
                'announcement_id' => $announcementId,
                'source_language' => $sourceLanguage,
                'target_languages' => $targetLanguages,
                'user_id' => auth()->id()
            ]);

            // Announcement'ı bul
            $announcement = Announcement::find($announcementId);
            if (!$announcement) {
                $this->dispatch('translationError', 'Announcement bulunamadı');
                return;
            }

            // Her hedef dil için çeviri yap
            $translatedCount = 0;
            $errors = [];

            foreach ($targetLanguages as $targetLanguage) {
                try {
                    // Kaynak dil verilerini al
                    $sourceTitle = $announcement->getTranslated('title', $sourceLanguage);
                    $sourceBody = $announcement->getTranslated('body', $sourceLanguage);

                    if (empty($sourceTitle) && empty($sourceBody)) {
                        $errors[] = "Kaynak dil ({$sourceLanguage}) verileri bulunamadı";
                        continue;
                    }

                    $translatedData = [];

                    // Title çevir
                    if (!empty($sourceTitle)) {
                        $translatedTitle = app(\Modules\AI\App\Services\AIService::class)->translateText(
                            $sourceTitle,
                            $sourceLanguage,
                            $targetLanguage,
                            ['context' => 'announcement_title', 'source' => 'translation_modal']
                        );
                        $translatedData['title'] = $translatedTitle;
                    }

                    // Body çevir
                    if (!empty($sourceBody)) {
                        $translatedBody = app(\Modules\AI\App\Services\AIService::class)->translateText(
                            $sourceBody,
                            $sourceLanguage,
                            $targetLanguage,
                            ['context' => 'announcement_content', 'source' => 'translation_modal', 'preserve_html' => true]
                        );
                        $translatedData['body'] = $translatedBody;
                    }

                    // Slug oluştur
                    if (!empty($translatedData['title'])) {
                        $translatedData['slug'] = \App\Helpers\SlugHelper::generateFromTitle(
                            Announcement::class,
                            $translatedData['title'],
                            $targetLanguage,
                            'slug',
                            'announcement_id',
                            $announcementId
                        );
                    }

                    // Çevrilmiş verileri kaydet
                    if (!empty($translatedData)) {
                        foreach ($translatedData as $field => $value) {
                            $currentData = $announcement->{$field} ?? [];
                            $currentData[$targetLanguage] = $value;
                            $announcement->{$field} = $currentData;
                        }
                        $announcement->save();
                        $translatedCount++;

                        \Log::info('✅ Announcement çevirisi tamamlandı', [
                            'announcement_id' => $announcementId,
                            'target_language' => $targetLanguage,
                            'fields' => array_keys($translatedData)
                        ]);
                    }

                } catch (\Exception $e) {
                    $errors[] = "Çeviri hatası ({$targetLanguage}): " . $e->getMessage();
                    \Log::error('❌ Announcement çeviri hatası', [
                        'announcement_id' => $announcementId,
                        'target_language' => $targetLanguage,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Session ID oluştur ve döndür
            $sessionId = 'translation_' . uniqid();
            
            // Başarı mesajı
            if ($translatedCount > 0) {
                $message = "{$translatedCount} dil için Announcement çeviri tamamlandı";
                if (!empty($errors)) {
                    $message .= ". " . count($errors) . " hata oluştu";
                }
                
                $this->dispatch('translationQueued', [
                    'sessionId' => $sessionId,
                    'success' => true,
                    'message' => $message,
                    'translatedCount' => $translatedCount,
                    'errors' => $errors
                ]);
                
                // Sayfayı yenile
                $this->render();
            } else {
                $this->dispatch('translationError', 'Hiçbir Announcement çeviri yapılamadı: ' . implode(', ', $errors));
            }

        } catch (\Exception $e) {
            \Log::error('❌ Announcement Translation modal genel hatası', [
                'announcement_id' => $announcementId,
                'error' => $e->getMessage()
            ]);
            
            $this->dispatch('translationError', 'Announcement çeviri işlemi başarısız: ' . $e->getMessage());
        }
    }
}