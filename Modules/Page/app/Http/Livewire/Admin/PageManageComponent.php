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
   
   // ✅ SEO system now handled by Universal SEO Tab component
   public $seoDataCache = [];
   public $allLanguagesSeoData = [];
   
   // Konfigürasyon verileri
   public $tabConfig = [];
   public $tabCompletionStatus = [];
   
   public $studioEnabled = false;
   
   // 🚨 PERFORMANCE FIX: Cached page with SEO
   protected $cachedPageWithSeo = null;
   
   // SOLID Dependencies
   protected $pageService;
   protected $aiService;
   
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
       'switchLanguage' => 'switchLanguage',
       'js-language-sync' => 'handleJavaScriptLanguageSync',
       'handleTestEvent' => 'handleTestEvent',
       'simple-test' => 'handleSimpleTest',
       'handleJavaScriptLanguageSync' => 'handleJavaScriptLanguageSync',
       'debug-test' => 'handleDebugTest',
       'set-js-language' => 'setJavaScriptLanguage',
       'set-continue-mode' => 'setContinueMode',
       'translate-content' => 'translateContent'
   ];

   /**
    * SEO Image File Upload Handlers
    */
   public function updatedSeoImageFiles($value, $name)
   {
       // $name = 'og_image' veya 'twitter_image'
       \Log::info('📁 SEO image file uploaded', [
           'field' => $name,
           'file' => $value ? get_class($value) : 'null',
           'current_language' => $this->currentLanguage
       ]);

       if ($value) {
           try {
               // Dosyayı storage'a kaydet ve URL al
               $imageUrl = $this->processImageUpload($value, $name);
               
               if ($imageUrl) {
                   // SEO cache'e kaydet
                   $this->seoDataCache[$this->currentLanguage][$name] = $imageUrl;
                   
                   // Success event dispatch
                   $this->dispatch('seoImageUploaded', [
                       'type' => $name,
                       'url' => $imageUrl,
                       'language' => $this->currentLanguage
                   ]);
                   
                   $this->dispatch('toast', [
                       'title' => 'Başarılı',
                       'message' => ($name === 'og_image' ? 'Sosyal medya resmi' : 'Twitter resmi') . ' yüklendi',
                       'type' => 'success'
                   ]);
                   
                   \Log::info('✅ SEO image uploaded successfully', [
                       'field' => $name,
                       'url' => $imageUrl,
                       'language' => $this->currentLanguage
                   ]);
               }
               
           } catch (\Exception $e) {
               \Log::error('❌ SEO image upload error', [
                   'field' => $name,
                   'error' => $e->getMessage()
               ]);
               
               $this->dispatch('toast', [
                   'title' => 'Hata',
                   'message' => 'Resim yükleme başarısız: ' . $e->getMessage(),
                   'type' => 'error'
               ]);
           }
           
           // Clear temporary file
           $this->seoImageFiles[$name] = null;
       }
   }

   /**
    * Process image upload and return URL
    */
   protected function processImageUpload($file, $fieldType)
   {
       // Dosya doğrulama
       $this->validateOnly("seoImageFiles.{$fieldType}", [
           "seoImageFiles.{$fieldType}" => 'image|max:2048' // 2MB limit
       ]);

       // Dosya adını oluştur
       $extension = $file->getClientOriginalExtension();
       $filename = 'seo_' . $fieldType . '_' . time() . '.' . $extension;
       
       // Public storage'a kaydet
       $path = $file->storeAs('seo-images', $filename, 'public');
       
       if ($path) {
           return asset('storage/' . $path);
       }
       
       throw new \Exception('Dosya kaydedilemedi');
   }
   
   // ✅ SEO system now handled by Universal SEO Tab component
   
   // Dependency Injection Boot
   public function boot()
   {
       $this->pageService = app(\Modules\Page\App\Services\PageService::class);
       $this->aiService = app(\Modules\AI\App\Services\AIService::class);
       
       // Layout sections
       view()->share('pretitle', __('page::admin.page_management'));
       view()->share('title', __('page::admin.pages'));
   }
   
   public function updated($propertyName)
   {
       // Tab completion status güncelleme
       $this->updateTabCompletionStatus();
   }
   
   /**
    * Tab completion durumunu güncelle
    */
   protected function updateTabCompletionStatus()
   {
       // ✅ SEO verileri artık Universal SEO Tab'dan gelir
       $currentSeoData = $this->seoDataCache[$this->currentLanguage] ?? [];
       
       $allData = array_merge(
           $this->inputs,
           $this->multiLangInputs[$this->currentLanguage] ?? [],
           [
               'seo_title' => $currentSeoData['seo_title'] ?? '',
               'seo_description' => $currentSeoData['seo_description'] ?? '',
               'seo_keywords' => $currentSeoData['seo_keywords'] ?? '',
               'canonical_url' => $currentSeoData['canonical_url'] ?? ''
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
       // İsteğe göre: Önce aktif+visible, sonra aktif+invisible - sadece is_visible=true olanlar
       $this->availableLanguages = TenantLanguage::where('is_active', true)
           ->where('is_visible', true)
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
           // İlk yükleme - dinamik default dil
           $defaultLanguage = session('site_default_language', \App\Services\TenantLanguageProvider::getDefaultLanguageCode());
           $this->currentLanguage = in_array($defaultLanguage, $this->availableLanguages) ? $defaultLanguage : \App\Services\TenantLanguageProvider::getDefaultLanguageCode();
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
           
           // ✅ SEO verileri artık seoDataCache sistemi ile yüklenir
           
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
                       'focus_keywords' => $seoSettings->focus_keywords[$lang] ?? '',
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
                       'focus_keywords' => '',
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
           
           // ✅ SEO veriler Universal SEO Tab component tarafından yönetilir
           $this->seoDataCache[$lang] = [
               'seo_title' => '',
               'seo_description' => '',
               'seo_keywords' => '',
               'canonical_url' => '',
               'robots_index' => true,
               'robots_follow' => true,
               'robots_snippet' => true
           ];
       }
       $this->robots_imageindex = true;     // ✅ Aktif - Resimleri indekslesin
       
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
               'og_type' => 'website',
               // Google SEO optimal varsayılanlar
               'robots_index' => true,
               'robots_follow' => true,
               'robots_snippet' => true,
               'robots_imageindex' => true,
               // ✅ 2025 AI CRAWLER PERMISSIONS (Varsayılan: İZİNLİ)
               'allow_gptbot' => true,          // ChatGPT crawling izni
               'allow_claudebot' => true,       // Claude crawling izni  
               'allow_google_extended' => true, // Bard/Gemini crawling izni
               'allow_bingbot_ai' => true,      // Bing AI crawling izni
               'focus_keywords' => '',
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
          'focus_keywords' => [],
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
              
              // Focus Keywords ekle
              $allLanguagesSeoData['focus_keywords'][$lang] = $this->seoDataCache[$lang]['focus_keywords'] ?? '';
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
                      'focus_keywords' => $allLanguagesSeoData['focus_keywords'][$lang] ?? '',
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
          $this->currentLanguage = get_tenant_default_locale();
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

   // ✅ SEO field updates now handled by Universal SEO Tab component

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
    * AI İÇERİK ÇEVİRİ SİSTEMİ - KAYNAK DİLİ HEDEF DİLLERE ÇEVIR
    * ULTRA DEEP THINK: Çeviri sistemi tamamen yeniden yazıldı
    */
   public function translateContent($data)
   {
       $sourceLanguage = $data['sourceLanguage'] ?? $this->currentLanguage;
       $targetLanguages = $data['targetLanguages'] ?? [];
       $fields = $data['fields'] ?? ['title', 'body'];
       $overwriteExisting = $data['overwriteExisting'] ?? false;

       \Log::info('🚀 AI Translation System başlatıldı', [
           'source_language' => $sourceLanguage,
           'target_languages' => $targetLanguages,
           'fields' => $fields,
           'overwrite_existing' => $overwriteExisting,
           'available_languages' => $this->availableLanguages
       ]);

       // Validasyon kontrolleri
       if (empty($targetLanguages)) {
           $this->dispatch('toast', [
               'title' => 'Uyarı',
               'message' => 'Hedef dil seçiniz',
               'type' => 'warning'
           ]);
           return;
       }

       // Kaynak dili kontrol et - sadece aktif diller kabul edilir
       if (!in_array($sourceLanguage, $this->availableLanguages)) {
           $this->dispatch('toast', [
               'title' => 'Hata',
               'message' => "Kaynak dil ({$sourceLanguage}) aktif değil",
               'type' => 'error'
           ]);
           return;
       }

       // Hedef dilleri kontrol et - sadece aktif diller kabul edilir
       $validTargetLanguages = array_intersect($targetLanguages, $this->availableLanguages);
       if (empty($validTargetLanguages)) {
           $this->dispatch('toast', [
               'title' => 'Hata',
               'message' => 'Geçerli hedef dil bulunamadı',
               'type' => 'error'
           ]);
           return;
       }

       // Kaynak dil verilerinin var olduğunu kontrol et
       $sourceData = $this->multiLangInputs[$sourceLanguage] ?? [];
       if (empty(array_filter($sourceData))) {
           $this->dispatch('toast', [
               'title' => 'Uyarı', 
               'message' => "Kaynak dil ({$sourceLanguage}) verileri bulunamadı",
               'type' => 'warning'
           ]);
           return;
       }

       try {
           $translatedCount = 0;
           $skippedCount = 0;
           $errorCount = 0;
           $results = [];

           foreach ($validTargetLanguages as $targetLang) {
               if ($targetLang === $sourceLanguage) {
                   \Log::info("⏭️ Kaynak dil atlandı: {$targetLang}");
                   continue;
               }

               // Hedef dil inputlarını hazırla
               if (!isset($this->multiLangInputs[$targetLang])) {
                   $this->multiLangInputs[$targetLang] = [
                       'title' => '',
                       'body' => '',
                       'slug' => '',
                   ];
               }

               foreach ($fields as $field) {
                   $sourceText = $sourceData[$field] ?? '';
                   if (empty(trim($sourceText))) {
                       \Log::info("⏭️ Boş kaynak alan atlandı: {$targetLang}.{$field}");
                       continue;
                   }

                   // Mevcut veri kontrolü - üzerine yazma kontrolü
                   $existingText = $this->multiLangInputs[$targetLang][$field] ?? '';
                   if (!empty(trim($existingText)) && !$overwriteExisting) {
                       \Log::info("⏭️ Mevcut veri korundu: {$targetLang}.{$field}");
                       $skippedCount++;
                       continue;
                   }

                   try {
                       \Log::info("🔄 Çeviri başlatılıyor: {$sourceLanguage} -> {$targetLang} [{$field}]");
                       
                       $translatedText = $this->aiService->translateText(
                           $sourceText,
                           $sourceLanguage,
                           $targetLang,
                           [
                               'context' => $field === 'body' ? 'html_content' : 'title',
                               'max_length' => $field === 'title' ? 255 : null,
                               'preserve_html' => $field === 'body'
                           ]
                       );

                       if (!empty(trim($translatedText))) {
                           $this->multiLangInputs[$targetLang][$field] = $translatedText;
                           
                           // Slug otomatik oluştur (sadece title çevirildiyse)
                           if ($field === 'title') {
                               $this->multiLangInputs[$targetLang]['slug'] = SlugHelper::generateFromTitle(
                                   Page::class,
                                   $translatedText,
                                   $targetLang,
                                   'slug',
                                   'page_id',
                                   $this->pageId
                               );
                           }

                           $translatedCount++;
                           $results[] = [
                               'language' => $targetLang,
                               'field' => $field,
                               'success' => true,
                               'original' => substr($sourceText, 0, 100) . (strlen($sourceText) > 100 ? '...' : ''),
                               'translated' => substr($translatedText, 0, 100) . (strlen($translatedText) > 100 ? '...' : '')
                           ];

                           \Log::info("✅ Çeviri başarılı: {$targetLang}.{$field}", [
                               'source_length' => strlen($sourceText),
                               'translated_length' => strlen($translatedText)
                           ]);
                       } else {
                           throw new \Exception('Boş çeviri sonucu');
                       }

                   } catch (\Exception $e) {
                       $errorCount++;
                       $results[] = [
                           'language' => $targetLang,
                           'field' => $field,
                           'success' => false,
                           'error' => $e->getMessage()
                       ];

                       \Log::error("❌ Çeviri hatası: {$targetLang}.{$field}", [
                           'error' => $e->getMessage(),
                           'source_text_length' => strlen($sourceText)
                       ]);
                   }
               }
           }

           // SEO verilerini de çevir
           if (isset($this->seoDataCache[$sourceLanguage])) {
               \Log::info('🔍 SEO verileri çevriliyor...');
               
               foreach ($validTargetLanguages as $targetLang) {
                   if ($targetLang === $sourceLanguage) continue;

                   try {
                       $sourceSeo = $this->seoDataCache[$sourceLanguage];
                       
                       if (!isset($this->seoDataCache[$targetLang])) {
                           $this->seoDataCache[$targetLang] = [
                               'seo_title' => '',
                               'seo_description' => '',
                               'seo_keywords' => '',
                               'canonical_url' => ''
                           ];
                       }

                       // SEO Title çevir
                       if (!empty(trim($sourceSeo['seo_title']))) {
                           $existingSeoTitle = $this->seoDataCache[$targetLang]['seo_title'] ?? '';
                           if (empty(trim($existingSeoTitle)) || $overwriteExisting) {
                               $translatedSeoTitle = $this->aiService->translateText(
                                   $sourceSeo['seo_title'],
                                   $sourceLanguage,
                                   $targetLang,
                                   ['context' => 'seo_title', 'max_length' => 60]
                               );
                               $this->seoDataCache[$targetLang]['seo_title'] = $translatedSeoTitle;
                               \Log::info("✅ SEO Title çevrildi: {$targetLang}");
                           }
                       }

                       // SEO Description çevir
                       if (!empty(trim($sourceSeo['seo_description']))) {
                           $existingSeoDesc = $this->seoDataCache[$targetLang]['seo_description'] ?? '';
                           if (empty(trim($existingSeoDesc)) || $overwriteExisting) {
                               $translatedSeoDesc = $this->aiService->translateText(
                                   $sourceSeo['seo_description'],
                                   $sourceLanguage,
                                   $targetLang,
                                   ['context' => 'seo_description', 'max_length' => 160]
                               );
                               $this->seoDataCache[$targetLang]['seo_description'] = $translatedSeoDesc;
                               \Log::info("✅ SEO Description çevrildi: {$targetLang}");
                           }
                       }

                       // SEO Keywords çevir
                       if (!empty(trim($sourceSeo['seo_keywords']))) {
                           $existingSeoKeywords = $this->seoDataCache[$targetLang]['seo_keywords'] ?? '';
                           if (empty(trim($existingSeoKeywords)) || $overwriteExisting) {
                               $translatedSeoKeywords = $this->aiService->translateText(
                                   $sourceSeo['seo_keywords'],
                                   $sourceLanguage,
                                   $targetLang,
                                   ['context' => 'seo_keywords']
                               );
                               $this->seoDataCache[$targetLang]['seo_keywords'] = $translatedSeoKeywords;
                               \Log::info("✅ SEO Keywords çevrildi: {$targetLang}");
                           }
                       }

                       // Focus Keywords çevir
                       if (!empty(trim($sourceSeo['focus_keywords'] ?? ''))) {
                           $existingFocusKeywords = $this->seoDataCache[$targetLang]['focus_keywords'] ?? '';
                           if (empty(trim($existingFocusKeywords)) || $overwriteExisting) {
                               $translatedFocusKeywords = $this->aiService->translateText(
                                   $sourceSeo['focus_keywords'],
                                   $sourceLanguage,
                                   $targetLang,
                                   ['context' => 'focus_keywords']
                               );
                               $this->seoDataCache[$targetLang]['focus_keywords'] = $translatedFocusKeywords;
                               \Log::info("✅ Focus Keywords çevrildi: {$targetLang}");
                           }
                       }

                       // Canonical URL aynı kalır
                       $this->seoDataCache[$targetLang]['canonical_url'] = $sourceSeo['canonical_url'] ?? '';

                   } catch (\Exception $e) {
                       \Log::error("❌ SEO çeviri hatası: {$targetLang}", [
                           'error' => $e->getMessage()
                       ]);
                   }
               }
           }

           // Tab completion durumunu güncelle
           $this->updateTabCompletionStatus();

           // Sonuç mesajları
           $messages = [];
           if ($translatedCount > 0) {
               $messages[] = "{$translatedCount} alan çevrildi";
           }
           if ($skippedCount > 0) {
               $messages[] = "{$skippedCount} alan atlandı";
           }
           if ($errorCount > 0) {
               $messages[] = "{$errorCount} hata";
           }

           if ($translatedCount > 0) {
               // ÇEVİRİLERİ VERİTABANINA KAYDET
               $this->save();
               
               $this->dispatch('toast', [
                   'title' => 'Çeviri Tamamlandı',
                   'message' => implode(', ', $messages) . ' ve kaydedildi',
                   'type' => 'success'
               ]);
           } else {
               $this->dispatch('toast', [
                   'title' => $skippedCount > 0 ? 'Çeviri Atlandı' : 'Çeviri Başarısız',
                   'message' => $skippedCount > 0 ? 'Tüm alanlar zaten dolu (üzerine yazma kapalı)' : 'Hiçbir alan çevrilemedi',
                   'type' => $skippedCount > 0 ? 'info' : 'error'
               ]);
           }

           \Log::info('🏁 AI Content Translation tamamlandı', [
               'source_language' => $sourceLanguage,
               'target_languages' => $validTargetLanguages,
               'translated_fields' => $translatedCount,
               'skipped_fields' => $skippedCount,
               'errors' => $errorCount,
               'results_count' => count($results)
           ]);

       } catch (\Exception $e) {
           $this->dispatch('toast', [
               'title' => 'Çeviri Sistemi Hatası',
               'message' => 'Çeviri işlemi başarısız: ' . $e->getMessage(),
               'type' => 'error'
           ]);

           \Log::error('🚨 AI Translation System Error', [
               'error' => $e->getMessage(),
               'file' => $e->getFile(),
               'line' => $e->getLine(),
               'source_language' => $sourceLanguage,
               'target_languages' => $targetLanguages
           ]);
       }
   }

   /**
    * SEO Analizi Verilerini Sıfırla
    */
   public function clearSeoAnalysis()
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
           $page = $this->getCachedPageWithSeo() ?? Page::findOrFail($this->pageId);
           $seoSettings = $page->seoSetting;
           
           if ($seoSettings) {
               $seoSettings->update([
                   'analysis_results' => null,
                   'analysis_date' => null,
                   'overall_score' => null,
                   'strengths' => null,
                   'improvements' => null,
                   'action_items' => null
               ]);
               
               $this->clearCachedPage();
               
               $this->dispatch('toast', [
                   'title' => 'Başarılı',
                   'message' => 'SEO analizi verileri sıfırlandı',
                   'type' => 'success'
               ]);
               
               \Log::info('✅ SEO analizi verileri sıfırlandı', [
                   'page_id' => $this->pageId,
                   'cleared_fields' => ['analysis_results', 'analysis_date', 'overall_score', 'strengths', 'improvements', 'action_items']
               ]);
               
           } else {
               $this->dispatch('toast', [
                   'title' => 'Bilgi',
                   'message' => 'SEO ayarları bulunamadı',
                   'type' => 'info'
               ]);
           }
           
       } catch (\Exception $e) {
           $this->dispatch('toast', [
               'title' => 'Hata',
               'message' => 'SEO analizi sıfırlama başarısız: ' . $e->getMessage(),
               'type' => 'error'
           ]);
           
           \Log::error('❌ SEO analizi sıfırlama hatası', [
               'page_id' => $this->pageId,
               'error' => $e->getMessage()
           ]);
       }
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
       // JavaScript değişkenlerini view'a gönder
       return view('page::admin.livewire.page-manage-component', [
           'jsVariables' => [
               'currentPageId' => $this->pageId ?? null,
               'currentLanguage' => $this->currentLanguage ?? 'tr'
           ]
       ]);
   }
}