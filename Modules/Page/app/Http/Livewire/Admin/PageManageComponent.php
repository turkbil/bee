<?php
namespace Modules\Page\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Page\App\Models\Page;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;

#[Layout('admin.layout')]
class PageManageComponent extends Component
{
   use WithFileUploads;

   public $pageId;
   public $currentLanguage = 'tr'; // Aktif dil sekmesi
   public $availableLanguages = []; // Site dillerinden dinamik olarak yüklenecek
   
   // Çoklu dil inputs - dinamik olarak oluşturulacak
   public $multiLangInputs = [];
   
   // Dil-neutral inputs
   public $inputs = [
       'css' => '',
       'js' => '',
       'is_active' => true,
       'is_homepage' => false,
   ];
   
   public $studioEnabled = false;
   
   // SEO sistemi - Global servis kullanacak
   public $seoData = [];
   public $seoComponentData = [];

   public function mount($id = null)
   {
       // Site dillerini dinamik olarak yükle
       $this->loadAvailableLanguages();
       
       if ($id) {
           $this->pageId = $id;
           $page = Page::findOrFail($id);
           
           // Dil-neutral alanları doldur
           $this->inputs = $page->only(['css', 'js', 'is_active', 'is_homepage']);
           
           // Çoklu dil alanları doldur
           foreach ($this->availableLanguages as $lang) {
               $this->multiLangInputs[$lang] = [
                   'title' => $page->getTranslated('title', $lang) ?? '',
                   'body' => $page->getTranslated('body', $lang) ?? '',
                   'slug' => $page->getTranslated('slug', $lang) ?? '',
               ];
           }
           
           // Global SEO sistemini yükle
           $this->loadSeoComponentData($page);
       } else {
           // Yeni sayfa için boş inputs hazırla
           $this->initializeEmptyInputs();
       }
       
       // Studio modülü aktif mi kontrol et
       $this->studioEnabled = class_exists('Modules\Studio\App\Http\Livewire\EditorComponent');
   }

   /**
    * Site dillerini dinamik olarak yükle
    */
   protected function loadAvailableLanguages()
   {
       $this->availableLanguages = TenantLanguage::where('is_active', true)
           ->orderBy('sort_order')
           ->pluck('code')
           ->toArray();
           
       // Eğer hiç dil yoksa default tr ekle
       if (empty($this->availableLanguages)) {
           $this->availableLanguages = ['tr'];
       }
       
       // Site varsayılan dilini al - tenants tablosundan
       $currentTenant = null;
       if (app(\Stancl\Tenancy\Tenancy::class)->initialized) {
           $currentTenant = tenant();
       } else {
           // Central context'teyse domain'den çözümle
           $host = request()->getHost();
           $domain = \Stancl\Tenancy\Database\Models\Domain::with('tenant')
               ->where('domain', $host)
               ->first();
           $currentTenant = $domain?->tenant;
       }
       
       $defaultLang = $currentTenant ? $currentTenant->tenant_default_locale : 'tr';
       $this->currentLanguage = in_array($defaultLang, $this->availableLanguages) ? $defaultLang : $this->availableLanguages[0];
       
       // Debug log
       \Log::info('PAGE Module - Language Settings', [
           'available_languages' => $this->availableLanguages,
           'tenant_default_locale' => $defaultLang,
           'current_language' => $this->currentLanguage,
           'session_site_default' => session('site_default_language'),
           'app_locale' => app()->getLocale(),
           'tenancy_initialized' => app(\Stancl\Tenancy\Tenancy::class)->initialized,
           'request_host' => request()->getHost(),
           'tenant_info' => $currentTenant ? ['id' => $currentTenant->id, 'tenant_default_locale' => $currentTenant->tenant_default_locale] : null
       ]);
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
               'seo' => [
                   'meta_title' => '',
                   'meta_description' => '',
                   'keywords' => [],
                   'og_title' => '',
                   'og_description' => '',
                   'og_image' => '',
                   'canonical_url' => '',
                   'robots' => 'index,follow',
               ]
           ];
       }
   }

   protected function rules()
   {
       $rules = [
           'inputs.css' => 'nullable|string',
           'inputs.js' => 'nullable|string',
           'inputs.is_active' => 'boolean',
           'inputs.is_homepage' => 'boolean',
       ];
       
       // Her dil için validation kuralları ekle
       foreach ($this->availableLanguages as $lang) {
           $rules["multiLangInputs.{$lang}.title"] = $lang === 'tr' ? 'required|min:3|max:255' : 'nullable|min:3|max:255';
           $rules["multiLangInputs.{$lang}.slug"] = 'nullable|string|max:255';
           $rules["multiLangInputs.{$lang}.body"] = 'nullable|string';
           $rules["multiLangInputs.{$lang}.seo.meta_title"] = 'nullable|string|max:60';
           $rules["multiLangInputs.{$lang}.seo.meta_description"] = 'nullable|string|max:160';
           $rules["multiLangInputs.{$lang}.seo.keywords"] = 'nullable|array';
           $rules["multiLangInputs.{$lang}.seo.og_title"] = 'nullable|string|max:60';
           $rules["multiLangInputs.{$lang}.seo.og_description"] = 'nullable|string|max:160';
           $rules["multiLangInputs.{$lang}.seo.og_image"] = 'nullable|string|max:255';
           $rules["multiLangInputs.{$lang}.seo.canonical_url"] = 'nullable|url|max:255';
           $rules["multiLangInputs.{$lang}.seo.robots"] = 'nullable|string|max:50';
       }
       
       return $rules;
   }

   protected $messages = [
       'multiLangInputs.tr.title.required' => 'page::messages.title_required',
       'multiLangInputs.tr.title.min' => 'page::messages.title_min',
       'multiLangInputs.tr.title.max' => 'page::messages.title_max',
   ];
   
   /**
    * Dil sekmesi değiştir
    */
   public function switchLanguage($language)
   {
       if (in_array($language, $this->availableLanguages)) {
           $this->currentLanguage = $language;
           
           // JavaScript'e dil değişikliğini bildir (TinyMCE için)
           $this->dispatch('language-switched', [
               'language' => $language,
               'editorId' => "editor_{$language}",
               'content' => $this->multiLangInputs[$language]['body'] ?? ''
           ]);
       }
   }

   public function save($redirect = false, $resetForm = false)
   {
      // TinyMCE içeriğini senkronize et
      $this->dispatch('sync-tinymce-content');
      
      $this->validate();
      
      // JSON formatında çoklu dil verilerini hazırla
      $multiLangData = [];
      foreach (['title', 'slug', 'body'] as $field) {
          $multiLangData[$field] = [];
          foreach ($this->availableLanguages as $lang) {
              $value = $this->multiLangInputs[$lang][$field] ?? '';
              
              // Boş slug'lar için otomatik oluştur
              if ($field === 'slug' && empty($value) && !empty($this->multiLangInputs[$lang]['title'])) {
                  $value = Str::slug($this->multiLangInputs[$lang]['title']);
              }
              
              if (!empty($value)) {
                  $multiLangData[$field][$lang] = $value;
              }
          }
      }
      
      // Global SEO sistemini kaydet
      if ($this->pageId && !empty($this->seoData)) {
          $page = Page::findOrFail($this->pageId);
          \App\Services\SeoFormService::saveSeoData($page, $this->seoData);
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
          
          if ($data == $currentData) {
              $toast = [
                  'title' => __('admin.info'),
                  'message' => __('admin.no_changes'),
                  'type' => 'info'
              ];
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
          if (!empty($this->seoData)) {
              \App\Services\SeoFormService::saveSeoData($page, $this->seoData);
          }
          
          // SEO component verilerini güncelle
          $this->loadSeoComponentData($page);
          
          $toast = [
              'title' => __('admin.success'),
              'message' => __('page::messages.page_created'),
              'type' => 'success'
          ];
      }
   
      if ($redirect) {
          session()->flash('toast', $toast);
          return redirect()->route('admin.page.index');
      }
   
      $this->dispatch('toast', $toast);
   
      if ($resetForm && !$this->pageId) {
          $this->reset();
          $this->currentLanguage = 'tr';
          $this->multiLangInputs = [
              'tr' => ['title' => '', 'body' => '', 'slug' => '', 'metakey' => '', 'metadesc' => ''],
              'en' => ['title' => '', 'body' => '', 'slug' => '', 'metakey' => '', 'metadesc' => ''],
              'ar' => ['title' => '', 'body' => '', 'slug' => '', 'metakey' => '', 'metadesc' => ''],
          ];
      }
   }

   /**
    * Global SEO sistemini yükle
    */
   protected function loadSeoComponentData($page)
   {
       $this->seoComponentData = \App\Services\SeoFormService::prepareComponentData($page);
       $this->seoData = $this->seoComponentData['seoData'] ?? [];
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

   public function render()
   {
       return view('page::admin.livewire.page-manage-component');
   }
}