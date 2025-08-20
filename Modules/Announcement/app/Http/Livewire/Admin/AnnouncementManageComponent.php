<?php
namespace Modules\Announcement\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Announcement\App\Models\Announcement;
use App\Services\GlobalSeoService;
use App\Services\GlobalTabService;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use App\Helpers\SlugHelper;

#[Layout('admin.layout')]
class AnnouncementManageComponent extends Component
{
   use WithFileUploads;

   public $announcementId;
   public $currentLanguage;
   public $availableLanguages = [];
   public $activeTab;
   
   // Çoklu dil inputs
   public $multiLangInputs = [];
   
   // Dil-neutral inputs
   public $inputs = [
       'is_active' => true,
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
   
   
   // 🚨 PERFORMANCE FIX: Cached announcement with SEO
   protected $cachedAnnouncementWithSeo = null;
   
   // SOLID Dependencies
   protected $announcementService;
   protected $seoRepository;
   
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
       $this->announcementService = app(\Modules\Announcement\App\Services\AnnouncementService::class);
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
       
       $this->tabCompletionStatus = GlobalTabService::getTabCompletionStatus($allData, 'announcement');
   }

   public function mount($id = null)
   {
       // Dependencies initialize
       $this->boot();
       
       // Konfigürasyonları yükle
       $this->loadConfigurations();
       
       // Site dillerini yükle
       $this->loadAvailableLanguages();
       
       // Announcement verilerini yükle
       if ($id) {
           $this->announcementId = $id;
           $this->loadAnnouncementData($id);
       } else {
           $this->initializeEmptyInputs();
       }
       
       
       // Tab completion durumunu hesapla
       $this->updateTabCompletionStatus();
   }

   /**
    * Konfigürasyonları yükle
    */
   protected function loadConfigurations()
   {
       $this->tabConfig = GlobalTabService::getAllTabs('announcement');
       $this->seoConfig = GlobalSeoService::getSeoConfig('announcement');
       $this->activeTab = GlobalTabService::getDefaultTabKey('announcement');
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
       if (session('announcement_continue_mode') && session('js_saved_language')) {
           // Kaydet ve Devam Et durumu
           $this->currentLanguage = session('js_saved_language');
           session()->forget(['announcement_continue_mode', 'js_saved_language']);
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
    * Announcement verilerini yükle
    */
   protected function loadAnnouncementData($id)
   {
       $formData = $this->announcementService->prepareAnnouncementForForm($id, $this->currentLanguage);
       
       if ($formData['announcement']) {
           $announcement = $formData['announcement'];
           
           // Dil-neutral alanlar
           $this->inputs = $announcement->only(['is_active']);
           
           // Çoklu dil alanları
           foreach ($this->availableLanguages as $lang) {
               $this->multiLangInputs[$lang] = [
                   'title' => $announcement->getTranslated('title', $lang) ?? '',
                   'body' => $announcement->getTranslated('body', $lang) ?? '',
                   'slug' => $announcement->getTranslated('slug', $lang) ?? '',
               ];
           }
           
           // SEO alanlarını yükle - sadece mevcut dil için (backward compatibility)
           $seoData = $formData['seoData'];
           $this->seo_title = $seoData['seo_title'] ?? '';
           $this->seo_description = $seoData['seo_description'] ?? '';
           $this->seo_keywords = $seoData['seo_keywords'] ?? '';
           $this->canonical_url = $seoData['canonical_url'] ?? '';
           
           // KRİTİK FİX: Tüm dillerin SEO verilerini seoDataCache'e yükle
           // 🚨 PERFORMANCE FIX: Cached announcement kullan
           $cachedAnnouncement = $this->getCachedAnnouncementWithSeo();
           $seoSettings = $cachedAnnouncement ? $cachedAnnouncement->seoSetting : null;
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
       
       // ✅ YENİ DUYURU İÇİN VARSAYILAN SEO AYARLARI - UX İYİLEŞTİRMESİ
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
               'og_titles' => '',
               'og_descriptions' => '',
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
           'inputs.is_active' => 'boolean',
       ];
       
       // Çoklu dil alanları
       foreach ($this->availableLanguages as $lang) {
           $rules["multiLangInputs.{$lang}.title"] = $lang === 'tr' ? 'required|min:3|max:255' : 'nullable|min:3|max:255';
           $rules["multiLangInputs.{$lang}.body"] = 'nullable|string';
       }
       
       // SEO validation kuralları - yeni seoDataCache sistemi için
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
       'multiLangInputs.*.body.string' => 'İçerik metinsel olmalıdır',
       'inputs.is_active.boolean' => 'Aktif durumu doğru/yanlış olmalıdır',
       // SEO Cache messages for each language
       'seoDataCache.*.seo_title.required' => 'SEO başlığı zorunludur',
       'seoDataCache.*.seo_title.max' => 'SEO başlığı en fazla 60 karakter olabilir',
       'seoDataCache.*.seo_title.string' => 'SEO başlığı metinsel olmalıdır',
       'seoDataCache.*.seo_description.required' => 'SEO açıklaması zorunludur',
       'seoDataCache.*.seo_description.max' => 'SEO açıklaması en fazla 160 karakter olabilir',
       'seoDataCache.*.seo_description.string' => 'SEO açıklaması metinsel olmalıdır',
       'seoDataCache.*.seo_keywords.string' => 'SEO anahtar kelimeleri metinsel olmalıdır',
       'seoDataCache.*.canonical_url.url' => 'Geçerli bir URL giriniz',
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
           session(['announcement_manage_language' => $language]);
           
           \Log::info('🎯 AnnouncementManageComponent switchLanguage çağrıldı', [
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
           
           \Log::info('⛔ AnnouncementManageComponent SEO dil eventi GÖNDERİLMEDİ (API çağrısı engellendi)', [
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
           'announcementId' => $this->announcementId,
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
                          Announcement::class,
                          $this->multiLangInputs[$lang]['title'],
                          $lang,
                          'slug',
                          'announcement_id',
                          $this->announcementId
                      );
                  } elseif (!empty($value)) {
                      // Dolu slug'lar için unique kontrolü yap
                      $value = SlugHelper::generateUniqueSlug(
                          Announcement::class,
                          $value,
                          $lang,
                          'slug',
                          'announcement_id',
                          $this->announcementId
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
          'announcementId' => $this->announcementId,
          'currentLanguage' => $this->currentLanguage,
          'seo_title_length' => strlen($this->seo_title ?? ''),
          'seo_description_length' => strlen($this->seo_description ?? ''),
          'filtered_data' => array_filter($seoData)
      ]);
      
      if ($this->announcementId) {
          // 🚨 PERFORMANCE FIX: Cached announcement kullan
          $announcement = $this->getCachedAnnouncementWithSeo() ?? Announcement::findOrFail($this->announcementId);
          
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
                      $announcement->updateSeoForLanguage($lang, $langSeoData);
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
       
      try {
          // Announcement data hazırla - Page pattern'ına uygun
          $announcementData = array_merge($multiLangData, $this->inputs);
          
          if ($this->announcementId) {
              // Güncelleme işlemi
              $announcement = $this->announcementService->update($this->announcementId, $announcementData);
              
              $toast = [
                  'title' => __('admin.success'),
                  'message' => __('announcement::admin.announcement_updated'),
                  'type' => 'success'
              ];
          } else {
              // Oluşturma işlemi
              $announcement = $this->announcementService->create($announcementData);
              $this->announcementId = $announcement->announcement_id;
              
              $toast = [
                  'title' => __('admin.success'),
                  'message' => __('announcement::admin.announcement_created'),
                  'type' => 'success'
              ];
          }
      } catch (\Exception $e) {
          $toast = [
              'title' => __('admin.error'),
              'message' => 'İşlem sırasında bir hata oluştu: ' . $e->getMessage(),
              'type' => 'error'
          ];
      }
   
      if ($redirect) {
          session()->flash('toast', $toast);
          return redirect()->route('admin.announcement.index');
      }
   
      $this->dispatch('toast', $toast);
   
      if ($resetForm && !$this->announcementId) {
          $this->reset();
      }
   }
   
   /**
    * 🚨 PERFORMANCE FIX: Cached announcement with SEO
    */
   protected function getCachedAnnouncementWithSeo()
   {
       if ($this->cachedAnnouncementWithSeo === null && $this->announcementId) {
           $this->cachedAnnouncementWithSeo = Announcement::with('seoSetting')
               ->find($this->announcementId);
       }
       
       return $this->cachedAnnouncementWithSeo;
   }
   
   public function openStudioEditor()
   {
       if (!$this->announcementId) {
           // Önce duyuruyı kaydet
           $this->save();
           
           if ($this->announcementId) {
               return redirect()->route('admin.studio.editor', ['module' => 'announcement', 'id' => $this->announcementId]);
           }
       } else {
           return redirect()->route('admin.studio.editor', ['module' => 'announcement', 'id' => $this->announcementId]);
       }
   }

   /**
    * Computed property for universal SEO component
    */
   #[Computed]
   public function currentAnnouncement()
   {
       if (!$this->announcementId) {
           return null;
       }
       
       return $this->getCachedAnnouncementWithSeo() ?? Announcement::find($this->announcementId);
   }

   public function render()
   {
       return view('announcement::admin.livewire.announcement-manage-component');
   }
}