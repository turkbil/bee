<?php
namespace Modules\Page\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Page\App\Models\Page;
use App\Services\GlobalSeoService;
use App\Services\GlobalTabService;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use App\Helpers\SlugHelper;

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
  
  // SEO Cache - Tüm dillerin SEO verileri (Performance Optimization)
  public $seoDataCache = [];
  
  // JavaScript için tüm dillerin SEO verileri (Blade exposure)
  public $allLanguagesSeoData = [];
   
   // Konfigürasyon verileri
   public $tabConfig = [];
   public $seoConfig = [];
   public $tabCompletionStatus = [];
   public $seoLimits = [];
   
   public $studioEnabled = false;
   
   // 🚨 PERFORMANCE FIX: Cached page with SEO
   protected $cachedPageWithSeo = null;
   
   // SOLID Dependencies
   protected $pageService;
   protected $seoRepository;
   
   /**
    * Get current page model for universal SEO component
    */
   #[Computed]
   public function currentPage()
   {
       if (!$this->pageId) {
           return null;
       }
       
       return $this->getCachedPageWithSeo() ?? Page::find($this->pageId);
   }
   
   // Livewire Listeners
   protected $listeners = [
       'refreshComponent' => '$refresh',
       'tab-changed' => 'handleTabChange',
       'seo-keywords-updated' => 'updateSeoKeywords',
       'seo-field-updated' => 'handleSeoFieldUpdate',
       'switchLanguage' => 'switchLanguage',
       'js-language-sync' => 'handleJavaScriptLanguageSync',
       'handleTestEvent' => 'handleTestEvent',
       'simple-test' => 'handleSimpleTest',
       'handleJavaScriptLanguageSync' => 'handleJavaScriptLanguageSync',
       'debug-test' => 'handleDebugTest',
       'set-js-language' => 'setJavaScriptLanguage',
       'set-continue-mode' => 'setContinueMode'
   ];
   
   /**
    * SEO Keywords Updated Handler
    */
   public function updateSeoKeywords($data)
   {
       $language = $data['lang'] ?? $this->currentLanguage;
       $keywords = $data['keywords'] ?? '';
       
       // seoDataCache'e kaydet
       if (!isset($this->seoDataCache[$language])) {
           $this->seoDataCache[$language] = [
               'seo_title' => '',
               'seo_description' => '',
               'seo_keywords' => '',
               'canonical_url' => ''
           ];
       }
       
       $this->seoDataCache[$language]['seo_keywords'] = $keywords;
       
       // Mevcut dil ise eski property'yi de güncelle (backward compatibility)
       if ($language === $this->currentLanguage) {
           $this->seo_keywords = $keywords;
       }
   }
   
   // Dependency Injection Boot
   public function boot()
   {
       $this->pageService = app(\Modules\Page\App\Services\PageService::class);
       $this->seoRepository = app(\App\Contracts\GlobalSeoRepositoryInterface::class);
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
       
       $this->tabCompletionStatus = GlobalTabService::getTabCompletionStatus($allData, 'page');
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
       $this->tabConfig = GlobalTabService::getAllTabs('page');
       $this->seoConfig = GlobalSeoService::getSeoConfig('page');
       $this->activeTab = GlobalTabService::getDefaultTabKey('page');
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
       
       // 🎯 KRİTİK: Her durumda mevcut kullanıcı dilini koru
       // Önce session'dan kontrol et, yoksa ilk aktif dil
       if (session('page_continue_mode') && session('js_saved_language')) {
           // Kaydet ve Devam Et durumu
           $this->currentLanguage = session('js_saved_language');
           session()->forget(['page_continue_mode', 'js_saved_language']);
           \Log::info('🔄 Kaydet ve Devam Et - dil korundu:', ['language' => $this->currentLanguage]);
       } elseif (session('js_current_language') && in_array(session('js_current_language'), $this->availableLanguages)) {
           // Normal kaydet - mevcut JS dilini koru
           $this->currentLanguage = session('js_current_language');
           \Log::info('🔄 Normal kaydet - JS dili korundu:', ['language' => $this->currentLanguage]);
       } else {
           // İlk yükleme - DAIMA TR default
           $defaultLanguage = session('site_default_language', 'tr');
           $this->currentLanguage = in_array($defaultLanguage, $this->availableLanguages) ? $defaultLanguage : 'tr';
       }
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
           
           // SEO alanlarını yükle - sadece mevcut dil için (backward compatibility)
           $seoData = $formData['seoData'];
           $this->seo_title = $seoData['seo_title'] ?? '';
           $this->seo_description = $seoData['seo_description'] ?? '';
           $this->seo_keywords = $seoData['seo_keywords'] ?? '';
           $this->canonical_url = $seoData['canonical_url'] ?? '';
           
           // KRİTİK FİX: Tüm dillerin SEO verilerini seoDataCache'e yükle
           // 🚨 PERFORMANCE FIX: Cached page kullan
           $cachedPage = $this->getCachedPageWithSeo();
           $seoSettings = $cachedPage ? $cachedPage->seoSetting : null;
           if ($seoSettings) {
               $titles = $seoSettings->titles ?? [];
               $descriptions = $seoSettings->descriptions ?? [];
               $keywords = $seoSettings->keywords ?? [];
               
               foreach ($this->availableLanguages as $lang) {
                   // Keywords güvenli işleme
                   $keywordData = $keywords[$lang] ?? [];
                   $keywordString = '';
                   if (is_array($keywordData)) {
                       $keywordString = implode(', ', $keywordData);
                   } elseif (is_string($keywordData)) {
                       $keywordString = $keywordData;
                   }
                   
                   $this->seoDataCache[$lang] = [
                       'seo_title' => $titles[$lang] ?? '',
                       'seo_description' => $descriptions[$lang] ?? '',
                       'seo_keywords' => $keywordString,
                       'canonical_url' => $seoSettings->canonical_url ?? ''
                   ];
               }
               
               // ✅ JavaScript için allLanguagesSeoData property'sini de güncelle
               $this->allLanguagesSeoData = [];
               foreach ($this->availableLanguages as $lang) {
                   $this->allLanguagesSeoData[$lang] = $this->seoDataCache[$lang];
               }
           } else {
               // SEO ayarları yoksa boş cache oluştur
               foreach ($this->availableLanguages as $lang) {
                   $this->seoDataCache[$lang] = [
                       'seo_title' => '',
                       'seo_description' => '',
                       'seo_keywords' => '',
                       'canonical_url' => ''
                   ];
               }
               
               // JavaScript için de boş data
               $this->allLanguagesSeoData = $this->seoDataCache;
           }
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
       
       // ✅ YENİ SAYFA İÇİN VARSAYILAN SEO AYARLARI - UX İYİLEŞTİRMESİ
       // SEO alanlarını boşalt ama kullanıcı yazabilir hale getir
       $this->seo_title = '';
       $this->seo_description = '';
       $this->seo_keywords = '';
       $this->canonical_url = '';
       
       // SEO cache'i de başlat - her dil için boş veri
       foreach ($this->availableLanguages as $lang) {
           $this->seoDataCache[$lang] = [
               'seo_title' => '',
               'seo_description' => '',
               'seo_keywords' => '',
               'canonical_url' => '',
               'og_title' => '',
               'og_description' => '',
               'og_image' => '',
               'robots_meta' => 'index, follow, archive, snippet, imageindex',
               'focus_keyword' => '',
               'auto_generate' => false
           ];
       }
       
       // JavaScript için de boş data
       $this->allLanguagesSeoData = $this->seoDataCache;
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
           $rules["multiLangInputs.{$lang}.body"] = 'nullable|string';
       }
       
       // Slug validation kuralları KALDIRILDI - Otomatik normalizasyon yapılıyor
       // $slugRules = SlugHelper::getValidationRules($this->availableLanguages, 'multiLangInputs', false);
       // $rules = array_merge($rules, $slugRules);
       
       // SEO validation kuralları - yeni seoDataCache sistemi için (karakter limiti YOK - kullanıcı karar verir)
       foreach ($this->availableLanguages as $lang) {
           $rules["seoDataCache.{$lang}.seo_title"] = 'nullable|string';
           $rules["seoDataCache.{$lang}.seo_description"] = 'nullable|string';
           $rules["seoDataCache.{$lang}.seo_keywords"] = 'nullable|string';
           $rules["seoDataCache.{$lang}.canonical_url"] = 'nullable|url';
       }
       
       
       return $rules;
   }

   protected $messages = [
       'multiLangInputs.*.title.required' => 'Başlık alanı zorunludur',
       'multiLangInputs.*.title.min' => 'Başlık en az 3 karakter olmalıdır',
       'multiLangInputs.*.title.max' => 'Başlık en fazla 255 karakter olabilir',
       // SEO Cache messages for each language
       'seoDataCache.*.seo_title.required' => 'SEO başlığı zorunludur',
       'seoDataCache.*.seo_title.max' => 'SEO başlığı en fazla 60 karakter olabilir',
       'seoDataCache.*.seo_description.required' => 'SEO açıklaması zorunludur',
       'seoDataCache.*.seo_description.max' => 'SEO açıklaması en fazla 160 karakter olabilir',
       // Backward compatibility
       'seo_title.required' => 'SEO başlığı zorunludur',
       'seo_title.max' => 'SEO başlığı en fazla 60 karakter olabilir',
       'seo_description.required' => 'SEO açıklaması zorunludur',
       'seo_description.max' => 'SEO açıklaması en fazla 160 karakter olabilir',
   ];
   
   /**
    * Tüm validation mesajlarını al
    */
   protected function getMessages()
   {
       // Slug validation mesajları - SlugHelper'dan al
       $slugMessages = SlugHelper::getValidationMessages($this->availableLanguages, 'multiLangInputs');
       
       return array_merge($this->messages, $slugMessages);
   }
   
   
   /**
    * Dil sekmesi değiştir
    */
   public function switchLanguage($language)
   {
       if (in_array($language, $this->availableLanguages)) {
           $oldLanguage = $this->currentLanguage;
           $this->currentLanguage = $language;
           
           // Session'a kaydet - save sonrası dil koruması için
           session(['page_manage_language' => $language]);
           
           \Log::info('🎯 PageManageComponent switchLanguage çağrıldı', [
               'old_language' => $oldLanguage,
               'new_language' => $language,
               'current_language' => $this->currentLanguage,
               'is_successfully_changed' => $this->currentLanguage === $language
           ]);
           
           // JavaScript'e dil değişikliğini bildir (TinyMCE için)
           $this->dispatch('language-switched', [
               'language' => $language,
               'editorId' => "editor_{$language}",
               'content' => $this->multiLangInputs[$language]['body'] ?? ''
           ]);
           
           // SEO Component'e dil değişimini bildir - KAPALI (API çağrısı engellendi)
           // $this->dispatch('seo-language-change', ['language' => $language]);
           
           \Log::info('⛔ PageManageComponent SEO dil eventi GÖNDERİLMEDİ (API çağrısı engellendi)', [
               'language' => $language
           ]);
       }
   }

   public function save($redirect = false, $resetForm = false)
   {
       // CRITICAL FIX: Session'dan JavaScript currentLanguage'i al ve senkronize et
       $jsCurrentLanguage = session('js_current_language', $this->currentLanguage);
       if ($jsCurrentLanguage !== $this->currentLanguage && in_array($jsCurrentLanguage, $this->availableLanguages)) {
           \Log::info('🔄 SAVE SYNC: JavaScript dili ile senkronize ediliyor', [
               'old_livewire_language' => $this->currentLanguage,
               'js_session_language' => $jsCurrentLanguage,
               'syncing_for_save' => true
           ]);
           $this->currentLanguage = $jsCurrentLanguage;
       }
       
       \Log::info('🚀 SAVE METHOD BAŞLADI!', [
           'pageId' => $this->pageId,
           'redirect' => $redirect,
           'resetForm' => $resetForm,
           'currentLanguage' => $this->currentLanguage,
           'seo_title' => $this->seo_title,
           'seo_description' => $this->seo_description,
           'js_session_language' => $jsCurrentLanguage,
           'language_synced' => $jsCurrentLanguage === $this->currentLanguage
       ]);
      // TinyMCE içeriğini senkronize et
      $this->dispatch('sync-tinymce-content');
      
      \Log::info('🔍 Validation başlıyor...', ['currentLanguage' => $this->currentLanguage]);
      
      try {
          $this->validate($this->rules(), $this->getMessages());
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
              
              // Slug işleme - SlugHelper kullan
              if ($field === 'slug') {
                  if (empty($value) && !empty($this->multiLangInputs[$lang]['title'])) {
                      // Boş slug'lar için title'dan oluştur
                      $value = SlugHelper::generateFromTitle(
                          Page::class,
                          $this->multiLangInputs[$lang]['title'],
                          $lang,
                          'slug',
                          'page_id',
                          $this->pageId
                      );
                  } elseif (!empty($value)) {
                      // Dolu slug'lar için unique kontrolü yap
                      $value = SlugHelper::generateUniqueSlug(
                          Page::class,
                          $value,
                          $lang,
                          'slug',
                          'page_id',
                          $this->pageId
                      );
                  }
              }
              
              if (!empty($value)) {
                  $multiLangData[$field][$lang] = $value;
              }
          }
      }
      
      // SEO verilerini kaydet - TÜM DİLLERİN VERİLERİNİ KAYDET (ÇOKLU DİL DESTEĞİ)
      \Log::info('🔍 SAVE METHOD - seoDataCache durumu', [
          'currentLanguage' => $this->currentLanguage,
          'seoDataCache' => $this->seoDataCache,
          'seoDataCache_for_current_lang' => $this->seoDataCache[$this->currentLanguage] ?? 'YOK!'
      ]);
      
      // KRİTİK FİX: TÜM dillerin SEO verilerini kaydet
      $allLanguagesSeoData = [
          'titles' => [],
          'descriptions' => [],
          'keywords' => [],
          'canonical_url' => $this->seoDataCache[$this->currentLanguage]['canonical_url'] ?? ''
      ];
      
      foreach ($this->availableLanguages as $lang) {
          if (isset($this->seoDataCache[$lang])) {
              $allLanguagesSeoData['titles'][$lang] = $this->seoDataCache[$lang]['seo_title'] ?? '';
              $allLanguagesSeoData['descriptions'][$lang] = $this->seoDataCache[$lang]['seo_description'] ?? '';
              
              // Keywords - string'i array'e çevir
              $keywordString = $this->seoDataCache[$lang]['seo_keywords'] ?? '';
              if (!empty(trim($keywordString))) {
                  $keywordArray = array_filter(array_map('trim', explode(',', $keywordString)));
                  $allLanguagesSeoData['keywords'][$lang] = $keywordArray;
              } else {
                  $allLanguagesSeoData['keywords'][$lang] = [];
              }
          }
      }
      
      \Log::info('🔄 TÜM DİLLERİN SEO verileri hazırlandı', [
          'allLanguagesSeoData' => $allLanguagesSeoData,
          'tr_keywords' => $allLanguagesSeoData['keywords']['tr'] ?? 'YOK',
          'en_keywords' => $allLanguagesSeoData['keywords']['en'] ?? 'YOK'
      ]);
      
      // ✅ KRİTİK FİX: JavaScript için allLanguagesSeoData property'sini güncelle
      $this->allLanguagesSeoData = $allLanguagesSeoData;
      \Log::info('✅ allLanguagesSeoData property güncellendi', [
          'property_set' => true,
          'data_size' => count($this->allLanguagesSeoData)
      ]);
      
      // Eski format için backward compatibility
      $seoData = [
          'title' => $allLanguagesSeoData['titles'][$this->currentLanguage] ?? '',
          'description' => $allLanguagesSeoData['descriptions'][$this->currentLanguage] ?? '',
          'keywords' => implode(', ', $allLanguagesSeoData['keywords'][$this->currentLanguage] ?? []),
          'canonical_url' => $allLanguagesSeoData['canonical_url']
      ];
      
      \Log::info('💾 SEO verilerini kaydediliyor...', [
          'seoData' => $seoData,
          'pageId' => $this->pageId,
          'currentLanguage' => $this->currentLanguage,
          'seo_title_length' => strlen($this->seo_title ?? ''),
          'seo_description_length' => strlen($this->seo_description ?? ''),
          'filtered_data' => array_filter($seoData)
      ]);
      
      if ($this->pageId) {
          // 🚨 PERFORMANCE FIX: Cached page kullan
          $page = $this->getCachedPageWithSeo() ?? Page::findOrFail($this->pageId);
          
          // KRİTİK FİX: TÜM dillerin SEO verilerini kaydet
          foreach ($this->availableLanguages as $lang) {
              if (isset($allLanguagesSeoData['titles'][$lang]) || 
                  isset($allLanguagesSeoData['descriptions'][$lang]) || 
                  isset($allLanguagesSeoData['keywords'][$lang])) {
                  
                  $langSeoData = [
                      'title' => $allLanguagesSeoData['titles'][$lang] ?? '',
                      'description' => $allLanguagesSeoData['descriptions'][$lang] ?? '', 
                      'keywords' => implode(', ', $allLanguagesSeoData['keywords'][$lang] ?? []),
                      'canonical_url' => $allLanguagesSeoData['canonical_url']
                  ];
                  
                  // Boş olmayan veriler varsa kaydet
                  if (!empty(array_filter($langSeoData, fn($v) => !empty(trim($v))))) {
                      $page->updateSeoForLanguage($lang, $langSeoData);
                      \Log::info('✅ SEO ayarları kaydedildi', [
                          'language' => $lang,
                          'title' => $langSeoData['title'],
                          'description' => substr($langSeoData['description'], 0, 50) . '...',
                          'keywords_count' => count($allLanguagesSeoData['keywords'][$lang] ?? []),
                          'has_data' => true
                      ]);
                  }
              }
          }
      }
      
      // SEO Component'e kaydetme event'i gönder
      if ($this->pageId) {
          $this->dispatch('parentFormSaving');
      }
      
      $data = array_merge($this->inputs, $multiLangData);

      // 🚨 PERFORMANCE FIX: Cached page kullan
      $currentPage = $this->pageId ? $this->getCachedPageWithSeo() : null;
      if (($this->inputs['is_homepage'] || ($currentPage && $currentPage->is_homepage)) && isset($data['is_active']) && $data['is_active'] == false) {
          $this->dispatch('toast', [
              'title' => __('admin.warning'),
              'message' => __('admin.homepage_cannot_be_deactivated'),
              'type' => 'warning',
          ]);
          return;
      }
   
      if ($this->pageId) {
          // 🚨 PERFORMANCE FIX: Cached page kullan
          $page = $this->getCachedPageWithSeo() ?? Page::findOrFail($this->pageId);
          $currentData = collect($page->toArray())->only(array_keys($data))->all();
          
          // SEO Component'e kaydetme event'i gönder (her durumda)
          $this->dispatch('parentFormSaving');
          
          if ($data == $currentData) {
              // Sayfa değişmemiş ama SEO değişmiş olabilir - her durumda başarı mesajı
              $toast = [
                  'title' => __('admin.success'),
                  'message' => __('admin.page_updated'),
                  'type' => 'success'
              ];
              
              // Page data unchanged, but save successful (SEO may have changed)
          } else {
              $page->update($data);
              log_activity($page, 'güncellendi');
              
              $toast = [
                  'title' => __('admin.success'),
                  'message' => __('admin.page_updated'),
                  'type' => 'success'
              ];
          }
      } else {
          $page = Page::create($data);
          $this->pageId = $page->page_id;
          log_activity($page, 'oluşturuldu');
          
          // Yeni oluşturulan sayfa için SEO verilerini kaydet (ÇOKLU DİL DESTEĞİ)
          if (!empty(array_filter($seoData))) {
              $page->updateSeoForLanguage($this->currentLanguage, $seoData);
              \Log::info('✅ Yeni sayfa için SEO verileri kaydedildi', [
                  'language' => $this->currentLanguage,
                  'data' => $seoData
              ]);
          }
          
          // SEO component verilerini güncelle
          $this->loadSeoComponentData($page);
          
          $toast = [
              'title' => __('admin.success'),
              'message' => __('admin.page_created'),
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
      
      // ✅ TAB KORUMA SİSTEMİ - Kaydetme sonrası event dispatch
      $this->dispatch('page-saved');
      
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
   /**
    * 🚨 PERFORMANCE FIX: Global cache service
    */
   protected function getCachedPageWithSeo()
   {
       if (!$this->pageId) {
           return null;
       }
       
       return \App\Services\GlobalCacheService::getPageWithSeo($this->pageId);
   }
   
   /**
    * Clear cached page data
    */
   protected function clearCachedPage()
   {
       $this->cachedPageWithSeo = null;
   }
   
   protected function loadSeoFormProperties($page)
   {
       \Log::info('🔄 SEO form properties yükleniyor...', [
           'page_id' => $page->page_id,
           'current_language' => $this->currentLanguage
       ]);
       
       // 🚨 PERFORMANCE FIX: Cached page kullan
       $cachedPage = $this->getCachedPageWithSeo();
       $seoSettings = $cachedPage ? $cachedPage->seoSetting : null;
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
           // 🚨 PERFORMANCE FIX: Cached page kullan
          $page = $this->getCachedPageWithSeo() ?? Page::findOrFail($this->pageId);
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
           // 🚨 PERFORMANCE FIX: Cached page kullan
          $page = $this->getCachedPageWithSeo() ?? Page::findOrFail($this->pageId);
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
           // 🚨 PERFORMANCE FIX: Cached page kullan
          $page = $this->getCachedPageWithSeo() ?? Page::findOrFail($this->pageId);
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

   // SEO Field Update Handler - EVENT BAZLI SİSTEM (MULTI-LANGUAGE)
   public function handleSeoFieldUpdate($data)
   {
       $field = $data['field'] ?? '';
       $value = $data['value'] ?? '';
       $language = $data['language'] ?? $this->currentLanguage;
       $silent = $data['silent'] ?? false;
       
       \Log::info('🚨 SEO Field EVENT ALINDI!', [
           'field' => $field,
           'value' => $value,
           'language' => $language,
           'current_language' => $this->currentLanguage,
           'value_length' => strlen($value),
           'silent_mode' => $silent,
           'timestamp' => now()
       ]);
       
       // CRITICAL FIX: JavaScript'ten gelen dil ile Livewire currentLanguage'i senkronize et
       if ($language !== $this->currentLanguage && in_array($language, $this->availableLanguages)) {
           \Log::info('🔄 LANGUAGE SYNC: JavaScript\'ten gelen dil ile senkronize ediliyor', [
               'old_language' => $this->currentLanguage,
               'new_language' => $language,
               'field' => $field,
               'syncing' => true
           ]);
           $this->currentLanguage = $language;
           
           // CRITICAL FIX: Cache temizle - anında güncellenme için
           if ($this->pageId) {
               $page = $this->getCachedPageWithSeo();
               if ($page && $page->seoSetting) {
                   \App\Services\SeoCacheService::forgetModelCache($page);
                   \Log::info('🗑️ Livewire: SEO cache temizlendi', ['language' => $language]);
                   // Clear our local cache too
                   $this->clearCachedPage();
               }
           }
       }
       
       // KRİTİK FİX: seoDataCache'i güncelle (yeni sistem)
       if (!isset($this->seoDataCache[$language])) {
           $this->seoDataCache[$language] = [
               'seo_title' => '',
               'seo_description' => '',
               'seo_keywords' => '',
               'canonical_url' => ''
           ];
       }
       
       // SEO alanlarını hem eski property'lerde hem seoDataCache'de güncelle
       switch ($field) {
           case 'seo_title':
               $this->seo_title = $value;
               $this->seoDataCache[$language]['seo_title'] = $value;
               \Log::info('✅ SEO Title güncellendi:', [
                   'language' => $language,
                   'value' => $value,
                   'length' => strlen($value),
                   'seoDataCache_updated' => true
               ]);
               break;
           case 'seo_description':
               $this->seo_description = $value;
               $this->seoDataCache[$language]['seo_description'] = $value;
               \Log::info('✅ SEO Description güncellendi:', [
                   'language' => $language,
                   'value' => $value,
                   'length' => strlen($value),
                   'seoDataCache_updated' => true
               ]);
               break;
           case 'seo_keywords':
               $this->seo_keywords = $value;
               $this->seoDataCache[$language]['seo_keywords'] = $value;
               \Log::info('✅ SEO Keywords güncellendi:', [
                   'language' => $language,
                   'value' => $value,
                   'keyword_count' => count(array_filter(explode(',', $value))),
                   'seoDataCache_updated' => true
               ]);
               break;
           case 'canonical_url':
               $this->canonical_url = $value;
               $this->seoDataCache[$language]['canonical_url'] = $value;
               \Log::info('✅ Canonical URL güncellendi:', [
                   'language' => $language,
                   'value' => $value,
                   'is_valid_url' => filter_var($value, FILTER_VALIDATE_URL) !== false,
                   'seoDataCache_updated' => true
               ]);
               break;
           default:
               \Log::warning('❌ Bilinmeyen SEO field:', $field);
       }
       
       // Tab completion durumunu güncelle
       $this->updateTabCompletionStatus();
   }

   // JavaScript Language Sync Handler
   public function handleJavaScriptLanguageSync($data)
   {
       $jsLanguage = $data['language'] ?? '';
       $oldLanguage = $this->currentLanguage;
       
       \Log::info('🚨 KRİTİK: handleJavaScriptLanguageSync çağrıldı', [
           'js_language' => $jsLanguage,
           'current_language' => $this->currentLanguage,
           'data' => $data,
           'will_change' => in_array($jsLanguage, $this->availableLanguages) && $jsLanguage !== $this->currentLanguage
       ]);
       
       if (in_array($jsLanguage, $this->availableLanguages) && $jsLanguage !== $this->currentLanguage) {
           $this->currentLanguage = $jsLanguage;
           
           // JavaScript'e confirmation gönder
           $this->dispatch('language-sync-completed', [
               'language' => $jsLanguage,
               'oldLanguage' => $oldLanguage,
               'success' => true
           ]);
           
           \Log::info('🔄 JavaScript Language Sync - Livewire güncellendi', [
               'old_language' => $oldLanguage,
               'new_language' => $jsLanguage,
               'current_language' => $this->currentLanguage,
               'sync_successful' => true
           ]);
       } else {
           // Değişiklik yoksa da confirmation gönder
           $this->dispatch('language-sync-completed', [
               'language' => $this->currentLanguage,
               'oldLanguage' => $oldLanguage,
               'success' => false,
               'reason' => 'no_change_needed'
           ]);
           
           \Log::info('🔄 JavaScript Language Sync - Değişiklik yok', [
               'js_language' => $jsLanguage,
               'current_language' => $this->currentLanguage,
               'is_valid_language' => in_array($jsLanguage, $this->availableLanguages)
           ]);
       }
   }

   // Test event handler
   public function handleTestEvent($data)
   {
       \Log::info('🧪 TEST EVENT ALINDI! Livewire listener calisiyor!', [
           'data' => $data,
           'timestamp' => now(),
           'component' => 'PageManageComponent',
           'event_working' => 'YES - JavaScript to Livewire works!'
       ]);
   }

   // Simple test handler
   public function handleSimpleTest($data)
   {
       \Log::info('🎯 SIMPLE TEST EVENT ALINDI! jQuery + Livewire 3.6.3 calisiyor!', [
           'data' => $data,
           'timestamp' => now(),
           'message' => $data['message'] ?? 'no message',
           'language' => $data['language'] ?? 'no language',
           'test_successful' => true
       ]);
   }

   // Debug Test Handler
   public function handleDebugTest($data)
   {
       \Log::info('🔥 DEBUG TEST EVENT ALINDI!', [
           'data' => $data,
           'current_language' => $this->currentLanguage,
           'message' => $data['message'] ?? 'no message',
           'language' => $data['language'] ?? 'no language',
           'timestamp' => $data['timestamp'] ?? 'no timestamp',
           'livewire_working' => true
       ]);
   }

   // JavaScript Language Session Handler
   public function setJavaScriptLanguage($data)
   {
       $jsLanguage = $data['language'] ?? '';
       
       // Session'a JavaScript currentLanguage'i kaydet
       session(['js_current_language' => $jsLanguage]);
       
       \Log::info('📝 JavaScript language session\'a kaydedildi', [
           'js_language' => $jsLanguage,
           'session_set' => true,
           'current_livewire_language' => $this->currentLanguage
       ]);
   }

   // Kaydet ve Devam Et Handler
   public function setContinueMode($data)
   {
       session([
           'page_continue_mode' => $data['continue_mode'] ?? false,
           'js_saved_language' => $data['saved_language'] ?? 'tr'
       ]);

       \Log::info('✅ Kaydet ve Devam Et - session verileri kaydedildi', [
           'continue_mode' => $data['continue_mode'] ?? false,
           'saved_language' => $data['saved_language'] ?? 'tr',
           'session_set' => true
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
       
       $page = $this->getCachedPageWithSeo();
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

   /**
    * Tüm dillerin SEO verilerini döndür (Ultra Performance - Zero API Calls)
    */
   public function getAllLanguagesSeoDataProperty()
   {
       if (!$this->pageId) {
           return [];
       }
       
       $page = $this->getCachedPageWithSeo();
       $seoSettings = $page ? $page->seoSetting : null;
       
       if (!$seoSettings) {
           return [];
       }
       
       $allData = [];
       $titles = $seoSettings->titles ?? [];
       $descriptions = $seoSettings->descriptions ?? [];
       $keywords = $seoSettings->keywords ?? [];
       
       foreach ($this->availableLanguages as $lang) {
           $keywordData = $keywords[$lang] ?? [];
           $keywordString = '';
           
           if (is_array($keywordData)) {
               $keywordString = implode(', ', $keywordData);
           } elseif (is_string($keywordData)) {
               $keywordString = $keywordData;
           }
           
           $allData[$lang] = [
               'seo_title' => $titles[$lang] ?? '',
               'seo_description' => $descriptions[$lang] ?? '',
               'seo_keywords' => $keywordString,
               'canonical_url' => $seoSettings->canonical_url ?? ''
           ];
       }
       
       return $allData;
   }

   public function render()
   {
       return view('page::admin.livewire.page-manage-component');
   }
}