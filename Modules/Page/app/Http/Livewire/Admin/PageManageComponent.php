<?php
namespace Modules\Page\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Page\App\Models\Page;
use Modules\Page\App\Services\PageSeoService;
use Modules\Page\App\Services\PageTabService;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;

#[Layout('admin.layout')]
class PageManageComponent extends Component
{
   use WithFileUploads;

   public $pageId;
   public $currentLanguage;
   public $availableLanguages = [];
   public $activeTab;
   
   // Çoklu dil inputs
   public $multiLangInputs = [];
   
   // Dil-neutral inputs
   public $inputs = [
       'css' => '',
       'js' => '',
       'is_active' => true,
       'is_homepage' => false,
   ];
   
   // SEO Alanları
   public $seo_title = '';
   public $seo_description = '';
   public $seo_keywords = '';
   public $canonical_url = '';
   
   // Konfigürasyon verileri
   public $tabConfig = [];
   public $seoConfig = [];
   public $tabCompletionStatus = [];
   public $seoLimits = [];
   
   public $studioEnabled = false;
   
   // SOLID Dependencies
   protected $pageService;
   protected $seoRepository;
   
   // Livewire Listeners
   protected $listeners = [
       'refreshComponent' => '$refresh',
       'tab-changed' => 'handleTabChange',
       'seo-keywords-updated' => 'updateSeoKeywords',
       'seo-field-updated' => 'handleSeoFieldUpdate'
   ];
   
   // Dependency Injection Boot
   public function boot()
   {
       $this->pageService = app(\Modules\Page\App\Services\PageService::class);
       $this->seoRepository = app(\Modules\Page\App\Contracts\PageSeoRepositoryInterface::class);
   }
   
   public function updated($propertyName)
   {
       // SEO alanları için real-time validation
       if (in_array($propertyName, ['seo_title', 'seo_description', 'seo_keywords', 'canonical_url'])) {
           $this->updateTabCompletionStatus();
       }
   }
   
   /**
    * Tab completion durumunu güncelle
    */
   protected function updateTabCompletionStatus()
   {
       $allData = array_merge(
           $this->inputs,
           $this->multiLangInputs[$this->currentLanguage] ?? [],
           [
               'seo_title' => $this->seo_title,
               'seo_description' => $this->seo_description,
               'seo_keywords' => $this->seo_keywords,
               'canonical_url' => $this->canonical_url
           ]
       );
       
       $this->tabCompletionStatus = PageTabService::getTabCompletionStatus($allData);
   }

   public function mount($id = null)
   {
       // Dependencies initialize
       $this->boot();
       
       // Konfigürasyonları yükle
       $this->loadConfigurations();
       
       // Site dillerini yükle
       $this->loadAvailableLanguages();
       
       // Sayfa verilerini yükle
       if ($id) {
           $this->pageId = $id;
           $this->loadPageData($id);
       } else {
           $this->initializeEmptyInputs();
       }
       
       // Studio modül kontrolü
       $this->studioEnabled = class_exists('Modules\Studio\App\Http\Livewire\EditorComponent');
       
       // Tab completion durumunu hesapla
       $this->updateTabCompletionStatus();
   }

   /**
    * Konfigürasyonları yükle
    */
   protected function loadConfigurations()
   {
       $this->tabConfig = PageTabService::getAllTabs();
       $this->seoConfig = PageSeoService::getSeoConfig();
       $this->activeTab = PageTabService::getDefaultTabKey();
   }
   
   /**
    * Site dillerini yükle
    */
   protected function loadAvailableLanguages()
   {
       $this->availableLanguages = TenantLanguage::where('is_active', true)
           ->orderBy('sort_order')
           ->pluck('code')
           ->toArray();
           
       // Fallback sistem helper'ından
       if (empty($this->availableLanguages)) {
           $this->availableLanguages = ['tr'];
       }
       
       // Dinamik varsayılan dil - sistem helper'ından
       $this->currentLanguage = current_tenant_language();
   }

   /**
    * Sayfa verilerini yükle
    */
   protected function loadPageData($id)
   {
       $formData = $this->pageService->preparePageForForm($id, $this->currentLanguage);
       
       if ($formData['page']) {
           $page = $formData['page'];
           
           // Dil-neutral alanlar
           $this->inputs = $page->only(['css', 'js', 'is_active', 'is_homepage']);
           
           // Çoklu dil alanları
           foreach ($this->availableLanguages as $lang) {
               $this->multiLangInputs[$lang] = [
                   'title' => $page->getTranslated('title', $lang) ?? '',
                   'body' => $page->getTranslated('body', $lang) ?? '',
                   'slug' => $page->getTranslated('slug', $lang) ?? '',
               ];
           }
           
           // SEO alanlarını yükle
           $seoData = $formData['seoData'];
           $this->seo_title = $seoData['seo_title'] ?? '';
           $this->seo_description = $seoData['seo_description'] ?? '';
           $this->seo_keywords = $seoData['seo_keywords'] ?? '';
           $this->canonical_url = $seoData['canonical_url'] ?? '';
       }
       
       // Tab ve SEO konfigürasyonları
       $this->tabCompletionStatus = $formData['tabCompletion'];
       $this->seoLimits = $formData['seoLimits'];
   }
   
   /**
    * Boş inputs hazırla
    */
   protected function initializeEmptyInputs()
   {
       foreach ($this->availableLanguages as $lang) {
           $this->multiLangInputs[$lang] = [
               'title' => '',
               'body' => '',
               'slug' => '',
           ];
       }
       
       // SEO alanlarını boşalt
       $this->seo_title = '';
       $this->seo_description = '';
       $this->seo_keywords = '';
       $this->canonical_url = '';
   }

   protected function rules()
   {
       $rules = [
           'inputs.css' => 'nullable|string',
           'inputs.js' => 'nullable|string',
           'inputs.is_active' => 'boolean',
           'inputs.is_homepage' => 'boolean',
       ];
       
       // Çoklu dil alanları
       foreach ($this->availableLanguages as $lang) {
           $rules["multiLangInputs.{$lang}.title"] = $lang === 'tr' ? 'required|min:3|max:255' : 'nullable|min:3|max:255';
           $rules["multiLangInputs.{$lang}.slug"] = 'nullable|string|max:255';
           $rules["multiLangInputs.{$lang}.body"] = 'nullable|string';
       }
       
       // SEO validation kuralları
       $seoRules = PageSeoService::getSeoValidationRules();
       $rules = array_merge($rules, $seoRules);
       
       return $rules;
   }

   protected $messages = [
       'multiLangInputs.tr.title.required' => 'Başlık alanı zorunludur',
       'multiLangInputs.tr.title.min' => 'Başlık en az 3 karakter olmalıdır',
       'multiLangInputs.tr.title.max' => 'Başlık en fazla 255 karakter olabilir',
       'seo_title.required' => 'SEO başlığı zorunludur',
       'seo_title.max' => 'SEO başlığı en fazla 60 karakter olabilir',
       'seo_description.required' => 'SEO açıklaması zorunludur',
       'seo_description.max' => 'SEO açıklaması en fazla 160 karakter olabilir',
   ];
   
   /**
    * Dil sekmesi değiştir
    */
   public function switchLanguage($language)
   {
       if (in_array($language, $this->availableLanguages)) {
           $this->currentLanguage = $language;
           
           \Log::info('🎯 PageManageComponent switchLanguage çağrıldı', [
               'new_language' => $language,
               'current_language' => $this->currentLanguage
           ]);
           
           // JavaScript'e dil değişikliğini bildir (TinyMCE için)
           $this->dispatch('language-switched', [
               'language' => $language,
               'editorId' => "editor_{$language}",
               'content' => $this->multiLangInputs[$language]['body'] ?? ''
           ]);
           
           // SEO Component'e dil değişimini bildir - ÇOKLU EVENT DENEMESİ
           $this->dispatch('seo-language-change', ['language' => $language]);
           $this->dispatch('refresh-seo-language', ['language' => $language]);
           
           // Direkt component refresh - alternatif yöntem
           $this->dispatch('$refresh');
           
           \Log::info('✅ PageManageComponent eventleri gönderildi', [
               'language' => $language,
               'events' => ['seo-language-change', 'refresh-seo-language', '$refresh']
           ]);
       }
   }

   public function save($redirect = false, $resetForm = false)
   {
       \Log::info('🚀 SAVE METHOD BAŞLADI!', [
           'pageId' => $this->pageId,
           'redirect' => $redirect,
           'resetForm' => $resetForm,
           'currentLanguage' => $this->currentLanguage
       ]);
      // TinyMCE içeriğini senkronize et
      $this->dispatch('sync-tinymce-content');
      
      \Log::info('🔍 Validation başlıyor...', ['currentLanguage' => $this->currentLanguage]);
      
      try {
          $this->validate();
          \Log::info('✅ Validation başarılı geçti!');
      } catch (\Exception $e) {
          \Log::error('❌ Validation HATASI!', [
              'error' => $e->getMessage(),
              'file' => $e->getFile(),
              'line' => $e->getLine()
          ]);
          
          // Validation hatası varsa devam etme
          $this->dispatch('toast', [
              'title' => 'Validation Hatası',
              'message' => $e->getMessage(),
              'type' => 'error'
          ]);
          return;
      }
      
      // JSON formatında çoklu dil verilerini hazırla
      $multiLangData = [];
      foreach (['title', 'slug', 'body'] as $field) {
          $multiLangData[$field] = [];
          foreach ($this->availableLanguages as $lang) {
              $value = $this->multiLangInputs[$lang][$field] ?? '';
              
              // Boş slug'lar için otomatik oluştur - Türkçe karakter desteği ile
              if ($field === 'slug' && empty($value) && !empty($this->multiLangInputs[$lang]['title'])) {
                  // Page model'inin custom slug metodunu kullan
                  $page = new \Modules\Page\App\Models\Page();
                  $value = $page->customSlugMethod($this->multiLangInputs[$lang]['title']);
              }
              
              if (!empty($value)) {
                  $multiLangData[$field][$lang] = $value;
              }
          }
      }
      
      // SEO verilerini kaydet - Ayrı field'lardan array oluştur
      $seoData = [
          'title' => $this->seo_title,
          'description' => $this->seo_description,
          'keywords' => $this->seo_keywords,
          'canonical_url' => $this->canonical_url
      ];
      
      \Log::info('💾 SEO verilerini kaydediliyor...', [
          'seoData' => $seoData,
          'pageId' => $this->pageId,
          'currentLanguage' => $this->currentLanguage,
          'seo_title_length' => strlen($this->seo_title ?? ''),
          'seo_description_length' => strlen($this->seo_description ?? ''),
          'filtered_data' => array_filter($seoData)
      ]);
      
      if ($this->pageId && !empty(array_filter($seoData))) {
          $page = Page::findOrFail($this->pageId);
          
          // HasSeo trait metoduyla SEO verileri kaydet
          if (!empty(array_filter($seoData))) {
              \Log::info('✅ SEO Title hazırlandı', [
                  'language' => $this->currentLanguage,
                  'title' => $seoData['title']
              ]);
              
              \Log::info('✅ SEO Description hazırlandı', [
                  'language' => $this->currentLanguage,
                  'description' => substr($seoData['description'], 0, 100) . '...'
              ]);
              
              $page->updateSeoForLanguage($this->currentLanguage, $seoData);
              \Log::info('✅ SEO ayarları HasSeo trait ile kaydedildi', [
                  'language' => $this->currentLanguage,
                  'data' => $seoData
              ]);
          }
      }
      
      // SEO Component'e kaydetme event'i gönder
      if ($this->pageId) {
          $this->dispatch('parentFormSaving');
      }
      
      $data = array_merge($this->inputs, $multiLangData);

      // Eğer ana sayfa ise pasif yapılmasına izin verme
      if (($this->inputs['is_homepage'] || ($this->pageId && Page::find($this->pageId)?->is_homepage)) && isset($data['is_active']) && $data['is_active'] == false) {
          $this->dispatch('toast', [
              'title' => __('admin.warning'),
              'message' => __('page::messages.homepage_cannot_be_deactivated'),
              'type' => 'warning',
          ]);
          return;
      }
   
      if ($this->pageId) {
          $page = Page::findOrFail($this->pageId);
          $currentData = collect($page->toArray())->only(array_keys($data))->all();
          
          // SEO Component'e kaydetme event'i gönder (her durumda)
          $this->dispatch('parentFormSaving');
          
          if ($data == $currentData) {
              // Sayfa değişmemiş ama SEO değişmiş olabilir - her durumda başarı mesajı
              $toast = [
                  'title' => __('admin.success'),
                  'message' => __('page::messages.page_updated'),
                  'type' => 'success'
              ];
              
              // Page data unchanged, but save successful (SEO may have changed)
          } else {
              $page->update($data);
              log_activity($page, 'güncellendi');
              
              $toast = [
                  'title' => __('admin.success'),
                  'message' => __('page::messages.page_updated'),
                  'type' => 'success'
              ];
          }
      } else {
          $page = Page::create($data);
          $this->pageId = $page->page_id;
          log_activity($page, 'oluşturuldu');
          
          // Yeni oluşturulan sayfa için SEO verilerini kaydet
          if (!empty(array_filter($seoData))) {
              \App\Services\SeoFormService::saveSeoData($page, $seoData);
          }
          
          // SEO component verilerini güncelle
          $this->loadSeoComponentData($page);
          
          $toast = [
              'title' => __('admin.success'),
              'message' => __('page::messages.page_created'),
              'type' => 'success'
          ];
      }
   
      \Log::info('🎯 Save method tamamlanıyor...', [
          'pageId' => $this->pageId,
          'redirect' => $redirect,
          'toast' => $toast
      ]);
      
      if ($redirect) {
          session()->flash('toast', $toast);
          return redirect()->route('admin.page.index');
      }
   
      \Log::info('🎊 Toast mesajı gönderiliyor...', ['toast' => $toast]);
      $this->dispatch('toast', $toast);
      
      \Log::info('✅ Save method başarıyla tamamlandı!', ['pageId' => $this->pageId]);
   
      if ($resetForm && !$this->pageId) {
          $this->reset();
          $this->currentLanguage = 'tr';
          // Dinamik olarak aktif dillerden boş inputs oluştur
          $this->initializeEmptyInputs();
      }
   }

   /**
    * Global SEO sistemini yükle
    */
   protected function loadSeoComponentData($page)
   {
       $this->seoComponentData = \App\Services\SeoFormService::prepareComponentData($page);
       // $this->seoData kaldırıldı - getCurrentSeoDataProperty() kullanılıyor
   }
   
   /**
    * SEO form property'lerini yükle
    */
   protected function loadSeoFormProperties($page)
   {
       \Log::info('🔄 SEO form properties yükleniyor...', [
           'page_id' => $page->page_id,
           'current_language' => $this->currentLanguage
       ]);
       
       $seoSettings = $page->seoSetting;
       if ($seoSettings) {
           $this->seoTitle = $seoSettings->getTitle($this->currentLanguage) ?? '';
           $this->seoDescription = $seoSettings->getDescription($this->currentLanguage) ?? '';
           
           \Log::info('✅ SEO properties yüklendi', [
               'title' => $this->seoTitle,
               'description' => substr($this->seoDescription, 0, 100) . '...'
           ]);
       }
       
       // Slug'ı multiLangInputs'tan al
       $this->seoSlug = $this->multiLangInputs[$this->currentLanguage]['slug'] ?? '';
   }
   
   /**
    * SEO Listener - Child component events
    */
   protected function getListeners()
   {
       return [
           'seo-data-updated' => 'updateSeoData',
       ];
   }
   
   /**
    * SEO verilerini güncelle
    */
   public function updateSeoData($seoData)
   {
       $this->seoData = $seoData;
   }
   
   /**
    * AI SEO analizi
    */
   public function analyzeSeo()
   {
       if (!$this->pageId) {
           $this->dispatch('toast', [
               'title' => 'Uyarı',
               'message' => 'Önce sayfayı kaydedin',
               'type' => 'warning'
           ]);
           return;
       }
       
       try {
           $page = Page::findOrFail($this->pageId);
           $seoAnalysisService = app(\App\Services\AI\SeoAnalysisService::class);
           
           $this->aiAnalysis = $seoAnalysisService->analyzeSeoContent($page, $this->currentLanguage);
           
           $this->dispatch('toast', [
               'title' => 'Başarılı',
               'message' => 'SEO analizi tamamlandı',
               'type' => 'success'
           ]);
           
       } catch (\Exception $e) {
           $this->dispatch('toast', [
               'title' => 'Hata',
               'message' => 'SEO analizi başarısız: ' . $e->getMessage(),
               'type' => 'error'
           ]);
       }
   }
   
   /**
    * AI SEO önerileri
    */
   public function generateSeoSuggestions()
   {
       if (!$this->pageId) {
           $this->dispatch('toast', [
               'title' => 'Uyarı',
               'message' => 'Önce sayfayı kaydedin',
               'type' => 'warning'
           ]);
           return;
       }
       
       try {
           $page = Page::findOrFail($this->pageId);
           $seoAnalysisService = app(\App\Services\AI\SeoAnalysisService::class);
           
           $suggestions = $seoAnalysisService->generateOptimizationSuggestions($page, $this->currentLanguage);
           $this->aiAnalysis = $suggestions;
           
           $this->dispatch('toast', [
               'title' => 'Başarılı',
               'message' => 'AI önerileri oluşturuldu',
               'type' => 'success'
           ]);
           
       } catch (\Exception $e) {
           $this->dispatch('toast', [
               'title' => 'Hata',
               'message' => 'Öneri oluşturma başarısız: ' . $e->getMessage(),
               'type' => 'error'
           ]);
       }
   }
   
   /**
    * Otomatik SEO optimizasyonu
    */
   public function autoOptimizeSeo()
   {
       if (!$this->pageId) {
           $this->dispatch('toast', [
               'title' => 'Uyarı',
               'message' => 'Önce sayfayı kaydedin',
               'type' => 'warning'
           ]);
           return;
       }
       
       try {
           $page = Page::findOrFail($this->pageId);
           $seoAnalysisService = app(\App\Services\AI\SeoAnalysisService::class);
           
           $seoAnalysisService->autoOptimizeSeo($page, $this->currentLanguage);
           
           // SEO verilerini yeniden yükle
           $this->loadSeoComponentData($page);
           
           $this->dispatch('toast', [
               'title' => 'Başarılı',
               'message' => 'SEO otomatik optimizasyonu tamamlandı',
               'type' => 'success'
           ]);
           
       } catch (\Exception $e) {
           $this->dispatch('toast', [
               'title' => 'Hata',
               'message' => 'Otomatik optimizasyon başarısız: ' . $e->getMessage(),
               'type' => 'error'
           ]);
       }
   }
   
   /**
    * AI önerisini uygula
    */
   public function applySuggestion($type, $value)
   {
       $language = $this->currentLanguage;
       
       if ($type === 'title') {
           $this->seoData['titles'][$language] = $value;
       } elseif ($type === 'description') {
           $this->seoData['descriptions'][$language] = $value;
       }
       
       $this->dispatch('toast', [
           'title' => 'Başarılı',
           'message' => 'Öneri uygulandı',
           'type' => 'success'
       ]);
   }

   // SEO Form Event Handlers
   public function updateSeoTitle($data)
   {
       \Log::info('📥 updateSeoTitle çağrıldı', [
           'data' => $data,
           'current_language' => $this->currentLanguage,
           'page_id' => $this->pageId
       ]);
       
       $this->seoData['title'] = $data['value'];
       
       \Log::info('✅ SEO Title güncellendi', [
           'new_title' => $data['value'],
           'seo_data' => $this->seoData
       ]);
   }
   
   public function updateSeoDescription($data)
   {
       \Log::info('📥 updateSeoDescription çağrıldı', [
           'data' => $data,
           'current_language' => $this->currentLanguage,
           'page_id' => $this->pageId
       ]);
       
       $this->seoData['description'] = $data['value'];
       
       \Log::info('✅ SEO Description güncellendi', [
           'new_description' => substr($data['value'], 0, 100) . '...',
           'seo_data' => $this->seoData
       ]);
   }
   
   public function updateSeoSlug($data)
   {
       \Log::info('📥 updateSeoSlug çağrıldı', [
           'data' => $data,
           'current_language' => $this->currentLanguage,
           'page_id' => $this->pageId
       ]);
       
       // Slug'ı mevcut dil için kaydet
       if (!isset($this->multiLangInputs[$this->currentLanguage])) {
           $this->multiLangInputs[$this->currentLanguage] = [];
       }
       $this->multiLangInputs[$this->currentLanguage]['slug'] = $data['value'];
       
       \Log::info('✅ SEO Slug güncellendi', [
           'new_slug' => $data['value'],
           'current_language' => $this->currentLanguage,
           'multiLangInputs' => $this->multiLangInputs
       ]);
   }

   // SEO Field Update Handler - EVENT BAZLI SİSTEM
   public function handleSeoFieldUpdate($data)
   {
       \Log::info('🚨 SEO Field EVENT ALINDI!', [
           'field' => $data['field'] ?? 'unknown',
           'value' => $data['value'] ?? '',
           'value_length' => strlen($data['value'] ?? ''),
           'timestamp' => now(),
           'current_seo_title' => $this->seo_title,
           'current_seo_description' => $this->seo_description
       ]);
       
       $field = $data['field'] ?? '';
       $value = $data['value'] ?? '';
       
       // SEO alanlarını güncelle
       switch ($field) {
           case 'seo_title':
               $this->seo_title = $value;
               \Log::info('✅ SEO Title event ile güncellendi:', $value);
               break;
           case 'seo_description':
               $this->seo_description = $value;
               \Log::info('✅ SEO Description event ile güncellendi:', $value);
               break;
           case 'seo_keywords':
               $this->seo_keywords = $value;
               \Log::info('✅ SEO Keywords event ile güncellendi:', $value);
               break;
           case 'canonical_url':
               $this->canonical_url = $value;
               \Log::info('✅ Canonical URL event ile güncellendi:', $value);
               break;
           default:
               \Log::warning('❌ Bilinmeyen SEO field:', $field);
       }
       
       // Tab completion durumunu güncelle
       $this->updateTabCompletionStatus();
   }

   // Test event handler
   public function handleTestEvent($data)
   {
       \Log::info('🧪 TEST EVENT ALINDI!', [
           'data' => $data,
           'timestamp' => now(),
           'component' => 'PageManageComponent'
       ]);
   }

   /**
    * Computed Properties
    */
   public function getCurrentSeoDataProperty()
   {
       if (!$this->pageId) {
           return [
               'seo_title' => '',
               'seo_description' => '',
               'seo_keywords' => '',
               'canonical_url' => ''
           ];
       }
       
       $page = Page::find($this->pageId);
       $seoSettings = $page ? $page->seoSetting : null;
       
       if (!$seoSettings) {
           return [
               'seo_title' => '',
               'seo_description' => '',
               'seo_keywords' => '',
               'canonical_url' => ''
           ];
       }
       
       $titles = $seoSettings->titles ?? [];
       $descriptions = $seoSettings->descriptions ?? [];
       $keywords = $seoSettings->keywords ?? [];
       
       return [
           'seo_title' => $titles[$this->currentLanguage] ?? '',
           'seo_description' => $descriptions[$this->currentLanguage] ?? '',
           'seo_keywords' => is_array($keywords[$this->currentLanguage] ?? []) ? implode(', ', $keywords[$this->currentLanguage]) : '',
           'canonical_url' => $seoSettings->canonical_url ?? ''
       ];
   }

   public function render()
   {
       return view('page::admin.livewire.page-manage-component');
   }
}