<?php
namespace Modules\Portfolio\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Illuminate\Support\Str;
use Modules\Portfolio\App\Models\PortfolioCategory;

#[Layout('admin.layout')]
class PortfolioCategoryComponent extends Component
{
    use WithPagination;

    public $title = '';
    public $newCategoryTitle = '';
    public $categories = [];
    public $maxPortfoliosCount = 0; 

    protected $rules = [
        'title' => 'required|min:3|max:255',
    ];

    protected $messages = [
        'title.required' => 'portfolio::admin.category_title_required',
        'title.min' => 'portfolio::admin.category_title_min',
        'title.max' => 'portfolio::admin.category_title_max',
    ];

    public function mount()
    {
        $this->loadCategories();
    }

    #[On('refreshPage')] 
    public function loadCategories()
    {
        $this->categories = PortfolioCategory::orderBy('order')
            ->withCount('portfolios')
            ->get();

        $this->maxPortfoliosCount = $this->categories->max('portfolios_count') ?? 1; 
    }

    public function toggleActive($id)
    {
        $category = PortfolioCategory::findOrFail($id);
        $category->is_active = !$category->is_active;
        $category->save();

        log_activity(
            $category,
            $category->is_active ? 'aktif edildi' : 'pasif edildi'
        );

        $this->dispatch('toast', [
            'title' => __('admin.success'), 
            'message' => __('admin.category_status_changed', ['status' => $category->is_active ? __('admin.active') : __('admin.inactive')]),
            'type' => 'success'
        ]);

        $this->loadCategories();
    }

    public function delete($id)
    {
        $category = PortfolioCategory::findOrFail($id);
        
        if ($category->portfolios()->count() > 0) {
            $this->dispatch('toast', [
                'title' => __('admin.warning'),
                'message' => __('admin.category_has_items'),
                'type' => 'warning'
            ]);
            return;
        }
        
        $category->delete();

        log_activity(
            $category,
            'silindi'
        );

        $this->dispatch('toast', [
            'title' => __('admin.success'),
            'message' => __('admin.category_deleted'),
            'type' => 'success'
        ]);

        $this->loadCategories();
    }

    public function quickAdd()
    {
        try {
            $this->validate();
            
            $maxOrder = PortfolioCategory::max('order') ?? 0;
            
            // Varsayılan dili al
            $defaultLang = $this->siteLocale();
            
            // JSON formatında title ve slug oluştur
            $titleJson = [$defaultLang => $this->title];
            $slugJson = [$defaultLang => Str::slug($this->title)];
            
            $category = PortfolioCategory::create([
                'title' => $titleJson,
                'slug' => $slugJson,
                'order' => $maxOrder + 1,
                'is_active' => true,
            ]);
            
            if (!$category) {
                throw new \Exception('Kategori eklenirken bir hata oluştu.');
            }

            log_activity($category, 'oluşturuldu');

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('admin.category_created'),
                'type' => 'success',
            ]);
            
            $this->reset('title');
            $this->loadCategories();

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('admin.form_error'),
                'type' => 'error',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => __('admin.category_create_error'),
                'type' => 'error',
            ]);
        }
    }

    #[On('updateOrder')] 
    public function updateOrder($list)
    {
        if (!is_array($list)) {
            return;
        }

        foreach ($list as $item) {
            if (!isset($item['value'], $item['order'])) {
                continue;
            }

            PortfolioCategory::where('portfolio_category_id', $item['value'])
                ->update(['order' => $item['order']]);
        }

        // Sıralama işlemi log'u
        if (function_exists('log_activity')) {
            activity()
                ->causedBy(auth()->user())
                ->inLog('PortfolioCategory')
                ->withProperties(['kategori_sayisi' => count($list)])
                ->log('Portfolio kategorileri sıralandı');
        }

        $this->loadCategories();
        
        $this->dispatch('toast', [
            'title' => __('admin.success'),
            'message' => __('admin.order_updated'),
            'type' => 'success',
        ]);
    }

    public function siteLocale(): string
    {
        $tenant = tenant();
        $defaultLang = $tenant?->tenant_default_locale ?? 'tr';
        
        return $defaultLang;
    }

    public function render()
    {
        return view('portfolio::admin.livewire.portfolio-category-component');
    }

    public function queueTranslation($categoryId, $sourceLanguage, $targetLanguages, $overwriteExisting = true)
    {
        try {
            \Log::info("🚀 PORTFOLIO CATEGORY QUEUE Translation başlatıldı", [
                'category_id' => $categoryId,
                'source' => $sourceLanguage,
                'targets' => $targetLanguages
            ]);

            // Job'ı kuyruğa ekle
            \Modules\AI\app\Jobs\TranslateEntityJob::dispatch(
                'portfolio_category',
                $categoryId,
                $sourceLanguage,
                $targetLanguages,
                $overwriteExisting
            );

            $this->dispatch('translationQueued', 'Portfolio Category çeviri işlemi başlatıldı!');
            
        } catch (\Exception $e) {
            \Log::error('❌ Portfolio Category queue translation hatası', [
                'category_id' => $categoryId,
                'error' => $e->getMessage()
            ]);
            
            $this->dispatch('translationError', 'Portfolio Category çeviri kuyruğu hatası: ' . $e->getMessage());
        }
    }

    public function translateFromModal(int $categoryId, string $sourceLanguage, array $targetLanguages): void
    {
        try {
            \Log::info('🌍 Portfolio Category Translation modal çeviri başlatıldı', [
                'category_id' => $categoryId,
                'source_language' => $sourceLanguage,
                'target_languages' => $targetLanguages,
                'user_id' => auth()->id()
            ]);

            // Category'yi bul
            $category = PortfolioCategory::find($categoryId);
            if (!$category) {
                $this->dispatch('translationError', 'Portfolio Category bulunamadı');
                return;
            }

            // Her hedef dil için çeviri yap
            $translatedCount = 0;
            $errors = [];

            foreach ($targetLanguages as $targetLanguage) {
                try {
                    // Kaynak dil verilerini al
                    $sourceTitle = $category->getTranslated('title', $sourceLanguage);

                    if (empty($sourceTitle)) {
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
                            ['context' => 'portfolio_category_title', 'source' => 'translation_modal']
                        );
                        $translatedData['title'] = $translatedTitle;
                    }

                    // Slug oluştur
                    if (!empty($translatedData['title'])) {
                        $translatedData['slug'] = \App\Helpers\SlugHelper::generateFromTitle(
                            PortfolioCategory::class,
                            $translatedData['title'],
                            $targetLanguage,
                            'slug',
                            'portfolio_category_id',
                            $categoryId
                        );
                    }

                    // Çevrilmiş verileri kaydet
                    if (!empty($translatedData)) {
                        foreach ($translatedData as $field => $value) {
                            $currentData = $category->{$field} ?? [];
                            $currentData[$targetLanguage] = $value;
                            $category->{$field} = $currentData;
                        }
                        $category->save();
                        $translatedCount++;

                        \Log::info('✅ Portfolio Category çevirisi tamamlandı', [
                            'category_id' => $categoryId,
                            'target_language' => $targetLanguage,
                            'fields' => array_keys($translatedData)
                        ]);
                    }

                } catch (\Exception $e) {
                    $errors[] = "Çeviri hatası ({$targetLanguage}): " . $e->getMessage();
                    \Log::error('❌ Portfolio Category çeviri hatası', [
                        'category_id' => $categoryId,
                        'target_language' => $targetLanguage,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Session ID oluştur ve döndür
            $sessionId = 'translation_' . uniqid();
            
            // Başarı mesajı
            if ($translatedCount > 0) {
                $message = "{$translatedCount} dil için Portfolio Category çeviri tamamlandı";
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
                
                // Kategorileri yeniden yükle
                $this->loadCategories();
            } else {
                $this->dispatch('translationError', 'Hiçbir Portfolio Category çeviri yapılamadı: ' . implode(', ', $errors));
            }

        } catch (\Exception $e) {
            \Log::error('❌ Portfolio Category Translation modal genel hatası', [
                'category_id' => $categoryId,
                'error' => $e->getMessage()
            ]);
            
            $this->dispatch('translationError', 'Portfolio Category çeviri işlemi başarısız: ' . $e->getMessage());
        }
    }
}