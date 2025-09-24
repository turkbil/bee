<?php
namespace Modules\Portfolio\App\Http\Livewire\Admin;

use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Portfolio\App\Http\Livewire\Traits\InlineEditTitle;
use Modules\Portfolio\App\Http\Livewire\Traits\WithBulkActionsQueue;
use Modules\Portfolio\App\Models\Portfolio;
use Modules\Portfolio\App\Models\PortfolioCategory;
use App\Traits\HasUniversalTranslation;

#[Layout('admin.layout')]
class PortfolioComponent extends Component
{
    use WithPagination, WithBulkActionsQueue, InlineEditTitle, HasUniversalTranslation;

    #[Url]
    public $search = '';

    #[Url]
    public $perPage = 10;

    #[Url]
    public $sortField = 'portfolio_id';

    #[Url]
    public $sortDirection = 'desc';

    #[Url]
    public $selectedCategory = '';

    // Bulk actions properties (WithBulkActionsQueue trait için gerekli)
    public $selectedItems = [];
    public $selectAll = false;
    public $bulkActionsEnabled = false;

    protected $queryString = [
        'sortField' => ['except' => 'portfolio_id'],
        'sortDirection' => ['except' => 'desc'],
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'selectedCategory' => ['except' => ''],
    ];

    protected function getModelClass()
    {
        return Portfolio::class;
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

    public function updatedSelectedCategory()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleActive($id)
    {
        $portfolio = Portfolio::where('portfolio_id', $id)->first();
    
        if ($portfolio) {
            $portfolio->update(['is_active' => !$portfolio->is_active]);
            
            log_activity(
                $portfolio,
                $portfolio->is_active ? 'aktif edildi' : 'pasif edildi',
                ['is_active' => $portfolio->is_active]
            );
    
            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('admin.item_status_changed', ['title' => $portfolio->title, 'status' => $portfolio->is_active ? __('admin.active') : __('admin.inactive')]),
                'type' => 'success',
            ]);
        }
    }

    public function render()
    {
        $baseQuery = Portfolio::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('portfolios.title', 'like', '%' . $this->search . '%')
                        ->orWhere('portfolios.slug', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedCategory, function ($query) {
                $query->where('portfolios.portfolio_category_id', $this->selectedCategory);
            });

        if ($this->sortField === 'portfolio_category_id') {
            $query = $baseQuery->select('portfolios.*')
                ->leftJoin('portfolio_categories', function($join) {
                    $join->on('portfolios.portfolio_category_id', '=', 'portfolio_categories.portfolio_category_id');
                })
                ->orderBy('portfolio_categories.title', $this->sortDirection);
        } else {
            $query = $baseQuery->orderBy('portfolios.' . $this->sortField, $this->sortDirection);
        }
    
        // 🚀 PERFORMANCE FIX: Eager loading ile N+1 query sorununu çöz
        $portfolios = $query->with(['category', 'seoSetting'])->paginate($this->perPage);
    
        $categories = PortfolioCategory::where('is_active', true)
            ->orderBy('title')
            ->get();
    
        return view('portfolio::admin.livewire.portfolio-component', [
            'portfolios' => $portfolios,
            'categories' => $categories,
        ]);
    }

    public function queueTranslation($portfolioId, $sourceLanguage, $targetLanguages, $overwriteExisting = true)
    {
        try {
            \Log::info("🚀 PORTFOLIO QUEUE Translation başlatıldı", [
                'portfolio_id' => $portfolioId,
                'source' => $sourceLanguage,
                'targets' => $targetLanguages
            ]);

            // Job'ı kuyruğa ekle
            \Modules\AI\app\Jobs\TranslateEntityJob::dispatch(
                'portfolio',
                $portfolioId,
                $sourceLanguage,
                $targetLanguages,
                $overwriteExisting
            );

            $this->dispatch('translationQueued', 'Portfolio çeviri işlemi başlatıldı!');
            
        } catch (\Exception $e) {
            \Log::error('❌ Portfolio queue translation hatası', [
                'portfolio_id' => $portfolioId,
                'error' => $e->getMessage()
            ]);
            
            $this->dispatch('translationError', 'Portfolio çeviri kuyruğu hatası: ' . $e->getMessage());
        }
    }

    public function translateFromModal(int $portfolioId, string $sourceLanguage, array $targetLanguages): void
    {
        try {
            \Log::info('🌍 Portfolio Translation modal çeviri başlatıldı', [
                'portfolio_id' => $portfolioId,
                'source_language' => $sourceLanguage,
                'target_languages' => $targetLanguages,
                'user_id' => auth()->id()
            ]);

            // Portfolio'yu bul
            $portfolio = Portfolio::find($portfolioId);
            if (!$portfolio) {
                $this->dispatch('translationError', 'Portfolio bulunamadı');
                return;
            }

            // Her hedef dil için çeviri yap
            $translatedCount = 0;
            $errors = [];

            foreach ($targetLanguages as $targetLanguage) {
                try {
                    // Kaynak dil verilerini al
                    $sourceTitle = $portfolio->getTranslated('title', $sourceLanguage);
                    $sourceBody = $portfolio->getTranslated('body', $sourceLanguage);

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
                            ['context' => 'portfolio_title', 'source' => 'translation_modal']
                        );
                        $translatedData['title'] = $translatedTitle;
                    }

                    // Body çevir
                    if (!empty($sourceBody)) {
                        $translatedBody = app(\Modules\AI\App\Services\AIService::class)->translateText(
                            $sourceBody,
                            $sourceLanguage,
                            $targetLanguage,
                            ['context' => 'portfolio_content', 'source' => 'translation_modal', 'preserve_html' => true]
                        );
                        $translatedData['body'] = $translatedBody;
                    }

                    // Slug oluştur
                    if (!empty($translatedData['title'])) {
                        $translatedData['slug'] = \App\Helpers\SlugHelper::generateFromTitle(
                            Portfolio::class,
                            $translatedData['title'],
                            $targetLanguage,
                            'slug',
                            'portfolio_id',
                            $portfolioId
                        );
                    }

                    // Çevrilmiş verileri kaydet
                    if (!empty($translatedData)) {
                        foreach ($translatedData as $field => $value) {
                            $currentData = $portfolio->{$field} ?? [];
                            $currentData[$targetLanguage] = $value;
                            $portfolio->{$field} = $currentData;
                        }
                        $portfolio->save();
                        $translatedCount++;

                        \Log::info('✅ Portfolio çevirisi tamamlandı', [
                            'portfolio_id' => $portfolioId,
                            'target_language' => $targetLanguage,
                            'fields' => array_keys($translatedData)
                        ]);
                    }

                } catch (\Exception $e) {
                    $errors[] = "Çeviri hatası ({$targetLanguage}): " . $e->getMessage();
                    \Log::error('❌ Portfolio çeviri hatası', [
                        'portfolio_id' => $portfolioId,
                        'target_language' => $targetLanguage,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Session ID oluştur ve döndür
            $sessionId = 'translation_' . uniqid();
            
            // Başarı mesajı
            if ($translatedCount > 0) {
                $message = "{$translatedCount} dil için Portfolio çeviri tamamlandı";
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
                $this->dispatch('translationError', 'Hiçbir Portfolio çeviri yapılamadı: ' . implode(', ', $errors));
            }

        } catch (\Exception $e) {
            \Log::error('❌ Portfolio Translation modal genel hatası', [
                'portfolio_id' => $portfolioId,
                'error' => $e->getMessage()
            ]);
            
            $this->dispatch('translationError', 'Portfolio çeviri işlemi başarısız: ' . $e->getMessage());
        }
    }
}