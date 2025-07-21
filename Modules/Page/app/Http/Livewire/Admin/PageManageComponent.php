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
   
   // AI Assistant properties
   public $aiChatMessage = '';
   public $aiAnalysis = [];
   public $aiSuggestions = [];
   public $aiProgress = false;
   
   // Livewire component state management
   protected $listeners = ['refreshComponent' => '$refresh'];
   
   // Livewire lifecycle - minimal intervention
   public function updated($propertyName)
   {
       // Let Livewire handle updates naturally - no manual interventions
   }

   /**
    * Test metod - basit debug
    */
   public function testAI()
   {
       \Log::info('🧪 TEST AI ÇAĞRILDI!', [
           'timestamp' => now()->format('H:i:s'),
           'user_id' => auth()->id(),
           'user_name' => auth()->user()->name ?? 'Unknown',
           'pageId' => $this->pageId,
           'currentLanguage' => $this->currentLanguage,
           'request_ip' => request()->ip(),
           'user_agent' => request()->userAgent()
       ]);
       
       // Console'a da yaz
       $this->dispatch('console-log', [
           'message' => '🧪 BACKEND TEST AI ÇAĞRILDI - ' . now()->format('H:i:s')
       ]);
       
       try {
           // 🤖 GERÇEK AI TEST ÇAĞRISI
           $testData = [
               'user_message' => 'Bu bir AI modülü test çağrısıdır', // ✅ Feature validation uyumlu
               'page_title' => $this->multiLangInputs[$this->currentLanguage]['title'] ?? 'Test Page',
               'page_content' => strip_tags($this->multiLangInputs[$this->currentLanguage]['content'] ?? ''),
               'current_language' => $this->currentLanguage,
               'conversation_type' => 'test',
               'page_id' => $this->pageId,
               'timestamp' => now()->format('Y-m-d H:i:s')
           ];
           
           \Log::info('🤖 AI TEST ÇAĞRISI BAŞLADI:', ['data' => $testData]);
           
           // AI modülünden test yanıtı al
           $aiResult = ai_execute_feature('ai-asistan-sohbet', $testData);
           
           \Log::info('🤖 AI TEST SONUCU:', ['result' => $aiResult]);
           
           if ($aiResult && !empty($aiResult['response'])) {
               $this->dispatch('toast', [
                   'title' => '🤖 AI TEST BAŞARILI',
                   'message' => 'AI modülü bağlantısı çalışıyor! Token kullanıldı.',
                   'type' => 'success'
               ]);
               
               // AI chat'e gerçek AI yanıtını ekle
               $this->dispatch('ai-message-received', [
                   'message' => '🤖 AI GERÇEK YANIT: ' . substr($aiResult['response'], 0, 200) . '...',
                   'is_user' => false
               ]);
               
               \Log::info('✅ AI MODÜLÜ BAŞARILI - GERÇEK ÇAĞRI:', ['response_preview' => substr($aiResult['response'], 0, 100)]);
           } else {
               $this->dispatch('toast', [
                   'title' => '⚠️ AI BAĞLANTI SORUNU',
                   'message' => 'AI modülü yanıt vermedi - konfigürasyon kontrol et',
                   'type' => 'warning'
               ]);
               
               $this->dispatch('ai-message-received', [
                   'message' => '⚠️ AI modülü bağlantı sorunu - konfigürasyon kontrol edilmeli',
                   'is_user' => false
               ]);
               
               \Log::warning('❌ AI MODÜLÜ YANIT YOK:', ['result' => $aiResult]);
           }
           
       } catch (\Exception $e) {
           \Log::error('❌ AI TEST HATASI:', ['error' => $e->getMessage()]);
           
           $this->dispatch('toast', [
               'title' => '❌ AI TEST HATASI',
               'message' => 'AI modülü hatası: ' . $e->getMessage(),
               'type' => 'error'
           ]);
           
           $this->dispatch('ai-message-received', [
               'message' => '❌ AI HATA: ' . $e->getMessage(),
               'is_user' => false
           ]);
       }
       
       // Sayfa verilerini de logla
       \Log::info('📊 SAYFA VERİLERİ:', [
           'title' => $this->multiLangInputs[$this->currentLanguage]['title'] ?? 'BOŞ',
           'content_length' => strlen($this->multiLangInputs[$this->currentLanguage]['body'] ?? ''),
           'available_languages' => $this->availableLanguages
       ]);
   }

   public function mount($id = null)
   {
       // Component state management
       $this->componentKey = 'page-manage-' . ($id ?? 'new') . '-' . time();
       
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
       
       // AI sonuçlarını sıfırla - Her seferinde fresh başla
       $this->aiAnalysis = null;
       $this->aiSuggestions = [];
       $this->aiProgress = false;
       
       // Session cache'lerini de temizle - Fresh start
       session()->forget(['ai_last_analysis', 'ai_last_suggestions']);
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
       
       // Language settings loaded successfully
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
   
   /**
    * 🚀 YENİ AI ASİSTAN METODLARİ
    */
   
   /**
    * Hızlı analiz - Ana AI paneli işlemi
    */
   public function runQuickAnalysis()
   {
       \Log::info('🔥 runQuickAnalysis ÇAĞRILDI!', ['pageId' => $this->pageId]);
       
       if (!$this->pageId) {
           $this->showSaveFirstWarning();
           return;
       }
       
       try {
           $this->aiProgress = true;
           // Event dispatch kaldırıldı - sayfa yenilemeyi engellemek için
           
           // GERÇEK ZAMANLI SAYFA VERİLERİNİ AL
           $title = $this->multiLangInputs[$this->currentLanguage]['title'] ?? '';
           $content = strip_tags($this->multiLangInputs[$this->currentLanguage]['body'] ?? '');
           $metaDesc = $this->seoData['descriptions'][$this->currentLanguage] ?? '';
           
           // 🤖 GERÇEK AI MODÜLÜ İLE ANALİZ YAP
           $analysisData = [
               'title' => $title,
               'content' => $content,
               'meta_description' => $metaDesc,
               'language' => $this->currentLanguage,
               'analysis_type' => 'comprehensive_seo'
           ];
           
           \Log::info('🤖 AI ÇAĞRISI BAŞLADI:', ['data' => $analysisData]);
           
           // AI modülünden gerçek analiz al
           $aiResult = ai_execute_feature('hizli-seo-analizi', $analysisData);
           
           \Log::info('🎯 AI SONUCU:', ['result' => $aiResult]);
           
           if ($aiResult && !empty($aiResult['response'])) {
               // AI'dan gelen string response'unu işle
               $aiResponseText = $aiResult['response'];
               
               \Log::info('🔍 AI RESPONSE İŞLENİYOR:', ['response_type' => gettype($aiResponseText), 'length' => strlen($aiResponseText)]);
               
               // AI yanıtından skorları ve önerileri çıkar
               $extractedScore = $this->extractScoreFromText($aiResponseText);
               $extractedSuggestions = $this->extractSuggestionsFromText($aiResponseText);
               
               $analysis = [
                   'overall_score' => $extractedScore,
                   'title_score' => max(50, $extractedScore - 10),
                   'content_score' => max(40, $extractedScore - 5),
                   'seo_score' => $extractedScore,
                   'suggestions' => $extractedSuggestions,
                   'ai_response_raw' => $aiResponseText, // Debug için tam metni de kaydet
                   'ai_full_response' => $aiResponseText, // Kullanıcı için tam AI yanıtı
                   'ai_formatted_response' => $this->formatAIResponseForDisplay($aiResponseText), // HTML formatlanmış yanıt
                   'stats' => [
                       'title_length' => mb_strlen($title),
                       'content_length' => mb_strlen($content),
                       'word_count' => str_word_count($content),
                       'meta_length' => mb_strlen($metaDesc),
                       'timestamp' => now()->format('H:i:s'),
                       'ai_used' => true
                   ]
               ];
           } else {
               // AI başarısız - ERROR durumu, analiz yapılamadı
               \Log::error('❌ AI ANALİZ BAŞARISIZ - Gerçek AI çağrısı yapılamadı');
               $this->aiAnalysis = null;
               $this->aiProgress = false;
               // Toast dispatch kaldırıldı - sayfa yenilemeyi engellemek için
               return;
           }
           
           // 1. Property'ye kaydet - Livewire snapshot safe
           $this->aiAnalysis = $analysis;
           $this->aiProgress = false;
           
           // Component integrity korunuyor - no additional dispatch
           
           // Session cache kullanmıyoruz - Her seferinde fresh AI çağrısı
           
           // 3. LIVEWIRE PROPERTY UPDATE - NO PAGE RELOAD!
           // Event dispatch'ler kaldırıldı - sayfa yenilemeyi engellemek için
           
           $aiStatus = $analysis['stats']['ai_used'] ? '🤖 AI Analizi' : '⚡ Hızlı Analiz';
           // Toast dispatch de kaldırıldı - inline sonuç yeterli
           
           // Log ile de kontrol edelim
           \Log::info('🎯 ANALIZ SONUCU HAZIR:', [
               'analysis' => $analysis,
               'aiAnalysis_property' => $this->aiAnalysis
           ]);
           
           // ✅ SAYFA YENİLENME SORUNU ÇÖZÜLDÜ LOG KAYDI
           \Log::info('✅ SEO ANALİZİ BAŞARILI - SAYFA YENİLENMEDİ:', [
               'dispatch_events_removed' => true,
               'inline_result_ready' => true,
               'page_refresh_prevented' => true,
               'ai_score' => $analysis['overall_score'],
               'suggestions_count' => count($analysis['suggestions']),
               'timestamp' => now()->format('H:i:s')
           ]);
           
       } catch (\Exception $e) {
           \Log::error('❌ AI ANALIZ HATASI:', ['error' => $e->getMessage()]);
           $this->aiProgress = false;
           // Toast dispatch kaldırıldı - sayfa yenilemeyi engellemek için
       }
   }
   
   /**
    * AI önerileri oluştur
    */
   public function generateAISuggestions()
   {
       \Log::info('🎯 generateAISuggestions ÇAĞRILDI!', ['pageId' => $this->pageId]);
       
       if (!$this->pageId) {
           $this->showSaveFirstWarning();
           return;
       }
       
       try {
           $this->aiProgress = true;
           $this->dispatch('ai-progress-start', ['message' => 'AI önerileri oluşturuluyor...']);
           
           // GERÇEK ZAMANLI SAYFA VERİLERİNİ AL
           $title = $this->multiLangInputs[$this->currentLanguage]['title'] ?? '';
           $content = strip_tags($this->multiLangInputs[$this->currentLanguage]['body'] ?? '');
           $metaDesc = $this->seoData['descriptions'][$this->currentLanguage] ?? '';
           
           // 🤖 GERÇEK AI MODÜLÜ İLE ÖNERİ ÜRET
           $suggestionData = [
               'content' => $content,
               'title' => $title,
               'meta_description' => $metaDesc,
               'language' => $this->currentLanguage,
               'improvement_type' => 'comprehensive_suggestions',
               'focus_areas' => ['seo', 'content_quality', 'user_engagement', 'readability']
           ];
           
           \Log::info('🤖 AI ÖNERİ ÇAĞRISI BAŞLADI:', ['data' => $suggestionData]);
           
           // AI modülünden gerçek öneriler al  
           $aiResult = ai_execute_feature('icerik-optimizasyonu', $suggestionData);
           
           \Log::info('🎯 AI ÖNERİ SONUCU:', ['result' => $aiResult]);
           
           if ($aiResult && !empty($aiResult['response'])) {
               // AI'dan gelen yanıtı işle - TAM AI RESPONSE'UNU KULLAN
               $aiResponse = $aiResult['response'];
               
               \Log::info('🔍 AI SUGGESTIONS RESPONSE:', ['type' => gettype($aiResponse), 'length' => is_string($aiResponse) ? strlen($aiResponse) : 'N/A']);
               
               // DOĞRUDAN TAM AI YANITI ATAR - Kullanıcı detaylı analizi görsün
               if (is_string($aiResponse) && strlen($aiResponse) > 50) {
                   $this->aiSuggestions = $aiResponse; // Tam AI yanıtını string olarak atar
               } elseif (is_array($aiResponse)) {
                   // Array ise düzleştir
                   $flatSuggestions = [];
                   foreach ($aiResponse as $item) {
                       if (is_array($item)) {
                           $flatSuggestions = array_merge($flatSuggestions, array_filter((array)$item));
                       } else {
                           $flatSuggestions[] = (string)$item;
                       }
                   }
                   $this->aiSuggestions = array_values(array_filter($flatSuggestions));
               } else {
                   $this->aiSuggestions = ['AI yanıtı işlenebilir formatta değil'];
               }
               
               \Log::info('✅ AI ÖNERİLERİ İŞLENDİ:', [
                   'suggestion_count' => is_array($this->aiSuggestions) ? count($this->aiSuggestions) : 'string',
                   'suggestion_type' => gettype($this->aiSuggestions),
                   'suggestion_length' => is_string($this->aiSuggestions) ? strlen($this->aiSuggestions) : 'N/A'
               ]);
               
           } else {
               // AI başarısız - ERROR durumu, hiç sonuç gösterme
               \Log::error('❌ AI ÖNERİ BAŞARISIZ - Gerçek AI çağrısı yapılamadı');
               $this->aiSuggestions = [];
               $this->aiProgress = false;
               // Toast dispatch kaldırıldı - sayfa yenilemeyi engellemek için
               return;
           }
           
           // Session cache kullanmıyoruz - Her seferinde fresh AI çağrısı
           
           $this->aiProgress = false;
           
           $aiStatus = '🤖 AI Önerileri';
           // Toast dispatch kaldırıldı - sayfa yenilemeyi engellemek için
           
       } catch (\Exception $e) {
           $this->aiProgress = false;
           \Log::error('❌ AI ÖNERİLERİ HATASI:', ['error' => $e->getMessage()]);
           // Toast dispatch kaldırıldı - sayfa yenilemeyi engellemek için
       }
   }
   
   /**
    * Otomatik optimize
    */
   public function autoOptimize()
   {
       if (!$this->pageId) {
           $this->showSaveFirstWarning();
           return;
       }
       
       try {
           $this->aiProgress = true;
           // Dispatch event kaldırıldı - sayfa yenilemeyi engellemek için
           
           $title = $this->multiLangInputs[$this->currentLanguage]['title'] ?? '';
           $content = $this->multiLangInputs[$this->currentLanguage]['body'] ?? '';
           
           if (empty($title) && empty($content)) {
               $this->aiProgress = false;
               // Toast dispatch kaldırıldı - sayfa yenilemeyi engellemek için
               return;
           }

           // AI ile otomatik optimizasyon
           $optimizationData = [
               'title' => $title,
               'content' => $content,
               'language' => $this->currentLanguage
           ];

           $aiResult = ai_execute_feature('otomatik-optimize', $optimizationData);
           
           if (!$aiResult['success']) {
               throw new \Exception($aiResult['error'] ?? 'AI optimizasyon hatası');
           }

           $optimization = $aiResult['data'] ?? $aiResult;
           
           // AI sonuçlarını uygula
           $optimizations = [];
           if (isset($optimization['optimized_title']) && !empty($optimization['optimized_title'])) {
               $this->multiLangInputs[$this->currentLanguage]['title'] = $optimization['optimized_title'];
               $optimizations[] = 'Başlık AI ile optimize edildi';
           }
           
           if (isset($optimization['optimized_content']) && !empty($optimization['optimized_content'])) {
               $this->multiLangInputs[$this->currentLanguage]['body'] = $optimization['optimized_content'];
               $optimizations[] = 'İçerik AI ile optimize edildi';
           }
           
           // AI önerilerini aiSuggestions'a kaydet
           if (isset($optimization['improvements']) && is_array($optimization['improvements'])) {
               $currentSuggestions = is_array($this->aiSuggestions) ? $this->aiSuggestions : [];
               // Array içindeki array'leri string'e çevir
               $cleanImprovements = array_map(function($item) {
                   return is_array($item) ? implode(' ', array_filter((array)$item)) : (string)$item;
               }, $optimization['improvements']);
               $this->aiSuggestions = array_values(array_merge($currentSuggestions, $cleanImprovements));
               session(['ai_last_suggestions' => $this->aiSuggestions]);
           }

           $this->aiProgress = false;
           
           $optimizationCount = count($optimizations);
           
           // Toast dispatch kaldırıldı - sayfa yenilemeyi engellemek için
           
           // Sonuçları göster - dispatch kaldırıldı
           // Event dispatch kaldırıldı - sayfa yenilemeyi engellemek için
           
       } catch (\Exception $e) {
           $this->aiProgress = false;
           // Toast dispatch kaldırıldı - sayfa yenilemeyi engellemek için
       }
   }
   
   /**
    * Anahtar kelime araştırması
    */
   public function researchKeywords()
   {
       if (!$this->pageId) {
           $this->showSaveFirstWarning();
           return;
       }
       
       try {
           $this->aiProgress = true;
           $this->dispatch('ai-progress-start', ['message' => 'Anahtar kelimeler araştırılıyor...']);
           
           $contentData = [
               'title' => $this->multiLangInputs[$this->currentLanguage]['title'] ?? '',
               'content' => $this->multiLangInputs[$this->currentLanguage]['body'] ?? '',
               'language' => $this->currentLanguage,
               'industry' => 'web_development'
           ];
           
           // AI Feature: Anahtar kelime araştırması
           $keywords = ai_execute_feature('anahtar-kelime-arastirmasi', $contentData);
           
           if ($keywords && $keywords['success'] && !empty($keywords['response'])) {
               // AI response'undan actual content'i al
               $aiResponse = $keywords['response'];
               
               // AI yanıtını safely array'e dönüştür ve nested array'leri temizle
               if (is_string($aiResponse)) {
                   // String ise satırlara böl
                   $lines = array_filter(explode("\n", $aiResponse));
                   $cleanLines = array_map('trim', $lines);
                   $this->aiSuggestions = array_values($cleanLines);
               } elseif (is_array($aiResponse)) {
                   // Array ise nested array'leri temizle
                   $cleanKeywords = array_map(function($item) {
                       if (is_array($item)) {
                           return implode(' ', array_filter((array)$item));
                       }
                       return (string)$item;
                   }, $aiResponse);
                   $this->aiSuggestions = array_values($cleanKeywords);
               } else {
                   $this->aiSuggestions = ['AI anahtar kelime araştırması tamamlandı'];
               }
               
               $this->dispatch('ai-keywords-ready', [
                   'keywords' => $keywords,
                   'language' => $this->currentLanguage
               ]);
               
               $this->dispatch('toast', [
                   'title' => '🔑 Anahtar Kelimeler Hazır',
                   'message' => 'Hedef kelimeler oluşturuldu',
                   'type' => 'success'
               ]);
           }
           
           $this->aiProgress = false;
           
       } catch (\Exception $e) {
           $this->aiProgress = false;
           $this->dispatch('toast', [
               'title' => 'Hata',
               'message' => 'Anahtar kelime araştırması başarısız: ' . $e->getMessage(),
               'type' => 'error'
           ]);
       }
   }
   
   /**
    * Çoklu dil çevirisi
    */
   public function translateMultiLanguage()
   {
       if (!$this->pageId) {
           $this->showSaveFirstWarning();
           return;
       }
       
       try {
           $this->aiProgress = true;
           $this->dispatch('ai-progress-start', ['message' => 'Çoklu dil çevirisi yapılıyor...']);
           
           $sourceLanguage = $this->currentLanguage;
           $sourceContent = [
               'title' => $this->multiLangInputs[$sourceLanguage]['title'] ?? '',
               'content' => $this->multiLangInputs[$sourceLanguage]['body'] ?? ''
           ];
           
           $translations = [];
           
           // AI ile çoklu dil çevirisi
           $multiLangData = [
               'source_text' => $sourceContent['title'] . "\n\n" . $sourceContent['content'],
               'source_language' => $sourceLanguage,
               'target_languages' => array_filter($this->availableLanguages, function($lang) use ($sourceLanguage) {
                   return $lang !== $sourceLanguage;
               }),
               'preserve_formatting' => true
           ];
           
           $aiResult = ai_execute_feature('coklu-dil-cevirisi', $multiLangData);
           
           if ($aiResult['success'] && isset($aiResult['data']['results'])) {
               $translations = $aiResult['data']['results'];
           } else {
               // Fallback: Tek tek çeviri yap
               foreach ($this->availableLanguages as $targetLang) {
                   if ($targetLang !== $sourceLanguage) {
                       $translationData = [
                           'source_text' => $sourceContent['title'] . "\n\n" . $sourceContent['content'],
                           'source_language' => $sourceLanguage,
                           'target_language' => $targetLang,
                           'content_type' => 'web_page'
                       ];
                       
                       $translation = ai_execute_feature('cevirmen', $translationData);
                       
                       if ($translation && isset($translation['translated_text'])) {
                           $translations[$targetLang] = $translation['translated_text'];
                       }
                   }
               }
           }
           
           if (!empty($translations)) {
               $this->dispatch('ai-translations-ready', [
                   'translations' => $translations,
                   'source_language' => $sourceLanguage
               ]);
               
               $this->dispatch('toast', [
                   'title' => '🌍 Çeviriler Hazır',
                   'message' => count($translations) . ' dile çeviri tamamlandı',
                   'type' => 'success'
               ]);
           }
           
           $this->aiProgress = false;
           
       } catch (\Exception $e) {
           $this->aiProgress = false;
           $this->dispatch('toast', [
               'title' => 'Hata',
               'message' => 'Çeviri işlemi başarısız: ' . $e->getMessage(),
               'type' => 'error'
           ]);
       }
   }
   
   /**
    * Rekabet analizi
    */
   public function competitorAnalysis()
   {
       if (!$this->pageId) {
           $this->showSaveFirstWarning();
           return;
       }
       
       try {
           $this->aiProgress = true;
           $this->dispatch('ai-progress-start', ['message' => 'Rekabet analizi yapılıyor...']);
           
           $analysisData = [
               'title' => $this->multiLangInputs[$this->currentLanguage]['title'] ?? '',
               'content' => $this->multiLangInputs[$this->currentLanguage]['body'] ?? '',
               'language' => $this->currentLanguage,
               'industry' => 'web_development'
           ];
           
           // AI ile rekabet analizi
           $aiResult = ai_execute_feature('rekabet-analizi', $analysisData);
           
           if (!$aiResult['success']) {
               throw new \Exception($aiResult['error'] ?? 'AI rekabet analizi hatası');
           }

           $analysis = $aiResult['data'] ?? $aiResult;
           
           // AI analiz sonuçlarını kaydet
           if (isset($analysis['improvement_areas']) && is_array($analysis['improvement_areas'])) {
               // Array içindeki array'leri string'e çevir
               $cleanAreas = array_map(function($item) {
                   return is_array($item) ? implode(' ', array_filter((array)$item)) : (string)$item;
               }, $analysis['improvement_areas']);
               $this->aiSuggestions = array_values(array_merge($this->aiSuggestions, $cleanAreas));
               session(['ai_last_suggestions' => $this->aiSuggestions]);
           }
           
           $this->dispatch('toast', [
               'title' => '📊 Rekabet Analizi Tamamlandı',
               'message' => 'Benzer sayfalarla karşılaştırma yapıldı',
               'type' => 'success'
           ]);
           
           $this->aiProgress = false;
           
       } catch (\Exception $e) {
           $this->aiProgress = false;
           $this->dispatch('toast', [
               'title' => 'Hata',
               'message' => 'Rekabet analizi başarısız: ' . $e->getMessage(),
               'type' => 'error'
           ]);
       }
   }
   
   /**
    * İçerik kalite skoru
    */
   public function contentQualityScore()
   {
       if (!$this->pageId) {
           $this->showSaveFirstWarning();
           return;
       }
       
       try {
           $this->aiProgress = true;
           $this->dispatch('ai-progress-start', ['message' => 'Kalite skoru hesaplanıyor...']);
           
           $contentData = [
               'title' => $this->multiLangInputs[$this->currentLanguage]['title'] ?? '',
               'content' => $this->multiLangInputs[$this->currentLanguage]['body'] ?? '',
               'language' => $this->currentLanguage
           ];
           
           // AI ile kalite skoru hesaplama
           $aiResult = ai_execute_feature('icerik-kalite-skoru', $contentData);
           
           if (!$aiResult['success']) {
               throw new \Exception($aiResult['error'] ?? 'AI kalite analizi hatası');
           }

           $qualityReport = $aiResult['data'] ?? $aiResult;
           
           // AI kalite skorunu analiz sonuçlarına kaydet
           if (isset($qualityReport['overall_score'])) {
               $this->aiAnalysis = array_merge($this->aiAnalysis, $qualityReport);
               session(['ai_last_analysis' => $this->aiAnalysis]);
           }
           
           // AI kalite raporunu safely array'e dönüştür
           if (is_string($qualityReport)) {
               $this->aiSuggestions = [$qualityReport];
           } elseif (is_array($qualityReport)) {
               $this->aiSuggestions = array_values($qualityReport);
           } else {
               $this->aiSuggestions = [];
           }
           
           $this->dispatch('toast', [
               'title' => '⭐ Kalite Skoru: ' . $qualityReport['overall_score'],
               'message' => 'İçerik kalitesi analizi tamamlandı',
               'type' => 'success'
           ]);
           
           $this->aiProgress = false;
           
       } catch (\Exception $e) {
           $this->aiProgress = false;
           $this->dispatch('toast', [
               'title' => 'Hata',
               'message' => 'Kalite skoru hesaplama başarısız: ' . $e->getMessage(),
               'type' => 'error'
           ]);
       }
   }
   
   /**
    * Schema markup oluştur
    */
   public function generateSchemaMarkup()
   {
       if (!$this->pageId) {
           $this->showSaveFirstWarning();
           return;
       }
       
       try {
           $this->aiProgress = true;
           $this->dispatch('ai-progress-start', ['message' => 'Schema markup oluşturuluyor...']);
           
           $page = Page::findOrFail($this->pageId);
           
           // AI ile schema markup oluştur
           $schemaData = [
               'title' => $this->multiLangInputs[$this->currentLanguage]['title'] ?? '',
               'content' => $this->multiLangInputs[$this->currentLanguage]['body'] ?? '',
               'page_type' => 'WebPage'
           ];

           $aiResult = ai_execute_feature('schema-markup-uretici', $schemaData);
           
           if (!$aiResult['success']) {
               throw new \Exception($aiResult['error'] ?? 'AI schema markup hatası');
           }

           $schemaMarkup = $aiResult['data'] ?? $aiResult;
           
           // AI schema markup önerilerini kaydet
           if (isset($schemaMarkup['recommendations']) && is_array($schemaMarkup['recommendations'])) {
               // Array içindeki array'leri string'e çevir
               $cleanRecommendations = array_map(function($item) {
                   return is_array($item) ? implode(' ', array_filter((array)$item)) : (string)$item;
               }, $schemaMarkup['recommendations']);
               $this->aiSuggestions = array_values(array_merge($this->aiSuggestions, $cleanRecommendations));
               session(['ai_last_suggestions' => $this->aiSuggestions]);
           }
           
           $this->dispatch('toast', [
               'title' => '🔗 Schema Markup Hazır',
               'message' => 'Yapılandırılmış veri önerileri oluşturuldu',
               'type' => 'success'
           ]);
           
           $this->aiProgress = false;
           
       } catch (\Exception $e) {
           $this->aiProgress = false;
           $this->dispatch('toast', [
               'title' => 'Hata',
               'message' => 'Schema markup oluşturma başarısız: ' . $e->getMessage(),
               'type' => 'error'
           ]);
       }
   }
   
   /**
    * Hızlı SEO analizi - Modern AI yaklaşımı (eski metod)
    */
   public function runQuickSeoAnalysis()
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
           $this->aiProgress = true;
           $this->dispatch('ai-progress-start', ['message' => 'SEO analizi yapılıyor...']);
           
           $page = Page::findOrFail($this->pageId);
           $seoService = app(\App\Services\AI\SeoAnalysisService::class);
           
           // Modern AI analiz - Comprehensive approach
           $analysisData = [
               'title' => $this->multiLangInputs[$this->currentLanguage]['title'] ?? '',
               'content' => $this->multiLangInputs[$this->currentLanguage]['body'] ?? '',
               'language' => $this->currentLanguage,
               'seo_data' => $this->seoData
           ];
           
           $analysis = $seoService->performComprehensiveAnalysis($analysisData);
           $this->aiAnalysis = $analysis;
           
           $this->aiProgress = false;
           
           // ❌ ai-analysis-complete dispatch kaldırıldı - Component kaybına sebep oluyor
           // $this->dispatch('ai-analysis-complete', ['analysis' => $analysis]);
           
           $this->dispatch('toast', [
               'title' => '🎯 Analiz Tamamlandı',
               'message' => 'SEO analizi başarıyla gerçekleştirildi - Panel kullanıma hazır',
               'type' => 'success'
           ]);
           
       } catch (\Exception $e) {
           $this->aiProgress = false;
           $this->dispatch('toast', [
               'title' => 'Hata',
               'message' => 'SEO analizi başarısız: ' . $e->getMessage(),
               'type' => 'error'
           ]);
       }
   }
   
   /**
    * İçerik optimizasyonu - AI destekli
    */
   public function optimizeContent()
   {
       if (!$this->pageId) {
           $this->showSaveFirstWarning();
           return;
       }
       
       try {
           $this->aiProgress = true;
           $this->dispatch('ai-progress-start', ['message' => 'İçerik optimizasyonu yapılıyor...']);
           
           $contentData = [
               'title' => $this->multiLangInputs[$this->currentLanguage]['title'] ?? '',
               'content' => $this->multiLangInputs[$this->currentLanguage]['body'] ?? '',
               'language' => $this->currentLanguage
           ];
           
           // AI Feature sistemi kullanarak optimizasyon
           $optimizedContent = ai_execute_feature('icerik-optimizasyonu', $contentData);
           
           if ($optimizedContent && isset($optimizedContent['suggestions'])) {
               // AI optimizasyon önerilerini safely array'e dönüştür
               if (is_string($optimizedContent['suggestions'])) {
                   $this->aiSuggestions = [$optimizedContent['suggestions']];
               } elseif (is_array($optimizedContent['suggestions'])) {
                   $this->aiSuggestions = array_values($optimizedContent['suggestions']);
               } else {
                   $this->aiSuggestions = [];
               }
               
               $this->dispatch('ai-suggestions-ready', [
                   'suggestions' => $this->aiSuggestions,
                   'type' => 'content_optimization'
               ]);
               
               $this->dispatch('toast', [
                   'title' => '✨ Optimizasyon Hazır',
                   'message' => 'İçerik önerileri oluşturuldu',
                   'type' => 'success'
               ]);
           }
           
           $this->aiProgress = false;
           
       } catch (\Exception $e) {
           $this->aiProgress = false;
           $this->dispatch('toast', [
               'title' => 'Hata',
               'message' => 'İçerik optimizasyonu başarısız: ' . $e->getMessage(),
               'type' => 'error'
           ]);
       }
   }
   
   /**
    * Anahtar kelime önerisi - AI destekli
    */
   public function suggestKeywords()
   {
       if (!$this->pageId) {
           $this->showSaveFirstWarning();
           return;
       }
       
       try {
           $this->aiProgress = true;
           $this->dispatch('ai-progress-start', ['message' => 'Anahtar kelimeler araştırılıyor...']);
           
           $contentData = [
               'title' => $this->multiLangInputs[$this->currentLanguage]['title'] ?? '',
               'content' => $this->multiLangInputs[$this->currentLanguage]['body'] ?? '',
               'language' => $this->currentLanguage,
               'industry' => 'web_development' // Tenant profilinden alınabilir
           ];
           
           // AI Feature: Anahtar kelime araştırması
           $keywordSuggestions = ai_execute_feature('anahtar-kelime-arastirmasi', $contentData);
           
           if ($keywordSuggestions && $keywordSuggestions['success'] && !empty($keywordSuggestions['response'])) {
               // AI response'undan actual content'i al
               $aiResponse = $keywordSuggestions['response'];
               
               // AI yanıtını safely array'e dönüştür ve nested array'leri temizle
               if (is_string($aiResponse)) {
                   // String ise satırlara böl
                   $lines = array_filter(explode("\n", $aiResponse));
                   $cleanLines = array_map('trim', $lines);
                   $this->aiSuggestions = array_values($cleanLines);
               } elseif (is_array($aiResponse)) {
                   // Array ise nested array'leri temizle
                   $cleanSuggestions = array_map(function($item) {
                       if (is_array($item)) {
                           return implode(' ', array_filter((array)$item));
                       }
                       return (string)$item;
                   }, $aiResponse);
                   $this->aiSuggestions = array_values($cleanSuggestions);
               } else {
                   $this->aiSuggestions = ['AI anahtar kelime önerileri hazırlandı'];
               }
               
               $this->dispatch('ai-keywords-ready', [
                   'keywords' => $keywordSuggestions,
                   'language' => $this->currentLanguage
               ]);
               
               $this->dispatch('toast', [
                   'title' => '🔑 Anahtar Kelimeler Hazır',
                   'message' => 'Hedef kelimeler oluşturuldu',
                   'type' => 'success'
               ]);
           }
           
           $this->aiProgress = false;
           
       } catch (\Exception $e) {
           $this->aiProgress = false;
           $this->dispatch('toast', [
               'title' => 'Hata',
               'message' => 'Anahtar kelime önerisi başarısız: ' . $e->getMessage(),
               'type' => 'error'
           ]);
       }
   }
   
   /**
    * Çeviri asistanı - Multi-language support
    */
   public function translateContent()
   {
       if (!$this->pageId) {
           $this->showSaveFirstWarning();
           return;
       }
       
       try {
           $this->aiProgress = true;
           $this->dispatch('ai-progress-start', ['message' => 'İçerik çevriliyor...']);
           
           $sourceLanguage = $this->currentLanguage;
           $sourceContent = [
               'title' => $this->multiLangInputs[$sourceLanguage]['title'] ?? '',
               'content' => $this->multiLangInputs[$sourceLanguage]['body'] ?? ''
           ];
           
           $translations = [];
           
           // Diğer dillere çeviri yap
           foreach ($this->availableLanguages as $targetLang) {
               if ($targetLang !== $sourceLanguage) {
                   $translationData = [
                       'source_text' => $sourceContent['title'] . "\n\n" . $sourceContent['content'],
                       'source_language' => $sourceLanguage,
                       'target_language' => $targetLang,
                       'content_type' => 'web_page'
                   ];
                   
                   $translation = ai_execute_feature('cevirmen', $translationData);
                   
                   if ($translation && isset($translation['translated_text'])) {
                       $translations[$targetLang] = $translation['translated_text'];
                   }
               }
           }
           
           if (!empty($translations)) {
               $this->dispatch('ai-translations-ready', [
                   'translations' => $translations,
                   'source_language' => $sourceLanguage
               ]);
               
               $this->dispatch('toast', [
                   'title' => '🌍 Çeviriler Hazır',
                   'message' => count($translations) . ' dile çeviri tamamlandı',
                   'type' => 'success'
               ]);
           }
           
           $this->aiProgress = false;
           
       } catch (\Exception $e) {
           $this->aiProgress = false;
           $this->dispatch('toast', [
               'title' => 'Hata',
               'message' => 'Çeviri işlemi başarısız: ' . $e->getMessage(),
               'type' => 'error'
           ]);
       }
   }
   
   /**
    * AI Chat mesajı gönderme
    */
   public function sendAiMessage()
   {
       if (empty(trim($this->aiChatMessage))) {
           return;
       }
       
       try {
           $userMessage = trim($this->aiChatMessage);
           $this->aiChatMessage = '';
           
           // User mesajını chat'e ekle
           $this->dispatch('ai-message-sent', [
               'message' => $userMessage,
               'is_user' => true
           ]);
           
           $this->aiProgress = true;
           
           // Context bilgileri hazırla
           $contextData = [
               'page_title' => $this->multiLangInputs[$this->currentLanguage]['title'] ?? '',
               'page_content' => $this->multiLangInputs[$this->currentLanguage]['body'] ?? '',
               'current_language' => $this->currentLanguage,
               'available_languages' => $this->availableLanguages,
               'user_message' => $userMessage,
               'conversation_type' => 'page_management'
           ];
           
           // AI Assistant - Genel sohbet feature'ı
           $aiResponse = ai_execute_feature('ai-asistan-sohbet', $contextData);
           
           if ($aiResponse && isset($aiResponse['response'])) {
               $this->dispatch('ai-message-received', [
                   'message' => $aiResponse['response'],
                   'is_user' => false
               ]);
           }
           
           $this->aiProgress = false;
           
       } catch (\Exception $e) {
           $this->aiProgress = false;
           $this->dispatch('ai-message-received', [
               'message' => 'Üzgünüm, şu anda bir teknik sorun yaşıyorum. Lütfen daha sonra tekrar deneyin.',
               'is_user' => false
           ]);
       }
   }
   
   /**
    * AI önerisini sayfa alanlarına uygula
    */
   public function applyAiSuggestion($field, $value, $language = null)
   {
       $targetLanguage = $language ?: $this->currentLanguage;
       
       if ($field === 'title') {
           $this->multiLangInputs[$targetLanguage]['title'] = $value;
       } elseif ($field === 'content') {
           $this->multiLangInputs[$targetLanguage]['body'] = $value;
       } elseif ($field === 'slug') {
           $this->multiLangInputs[$targetLanguage]['slug'] = $value;
       }
       
       // SEO alanları için
       if (str_starts_with($field, 'seo_')) {
           $seoField = str_replace('seo_', '', $field);
           if (!isset($this->seoData[$seoField])) {
               $this->seoData[$seoField] = [];
           }
           $this->seoData[$seoField][$targetLanguage] = $value;
       }
       
       $this->dispatch('toast', [
           'title' => '✅ Uygulandı',
           'message' => 'AI önerisi başarıyla uygulandı',
           'type' => 'success'
       ]);
       
       // Form alanlarını güncelle
       $this->dispatch('form-field-updated', [
           'field' => $field,
           'value' => $value,
           'language' => $targetLanguage
       ]);
   }
   
   /**
    * AI response'undan skor çıkarma - REGEX ile smart extraction
    */
   private function extractScoreFromText(string $text): int
   {
       // SEO Puanı: 25/100 formatını ara
       if (preg_match('/SEO Puanı:\s*(\d+)\/100/i', $text, $matches)) {
           return (int)$matches[1];
       }
       
       // Skor: 25/100 formatını ara
       if (preg_match('/Skor:\s*(\d+)\/100/i', $text, $matches)) {
           return (int)$matches[1];
       }
       
       // Puan: 25 formatını ara
       if (preg_match('/Puan:\s*(\d+)/i', $text, $matches)) {
           return (int)$matches[1];
       }
       
       // Score: 25 formatını ara
       if (preg_match('/Score:\s*(\d+)/i', $text, $matches)) {
           return (int)$matches[1];
       }
       
       // Default fallback - AI response kalitesine göre
       $textLength = strlen($text);
       if ($textLength > 1000) return 75; // Detaylı analiz
       if ($textLength > 500) return 65;  // Orta analiz
       return 55; // Kısa analiz
   }
   
   /**
    * AI response'undan önerileri çıkarma - REGEX ile smart extraction
    */
   private function extractSuggestionsFromText(string $text): array
   {
       $suggestions = [];
       
       // Numaralı liste formatı: 1. 2. 3. - FIXED REGEX
       if (preg_match_all('/\d+\.\s*([^\r\n]+)/i', $text, $matches)) {
           foreach ($matches[1] as $suggestion) {
               $cleaned = trim($suggestion);
               if (strlen($cleaned) > 10) { // Çok kısa önerileri filtrele
                   $suggestions[] = $cleaned;
               }
           }
       }
       
       // Satır başı tire formatı: - Öneri
       if (empty($suggestions) && preg_match_all('/^[-•]\s*([^\n]+)/m', $text, $matches)) {
           foreach ($matches[1] as $suggestion) {
               $cleaned = trim($suggestion);
               if (strlen($cleaned) > 10) {
                   $suggestions[] = $cleaned;
               }
           }
       }
       
       // Eğer hiç öneri bulunamadıysa, cümleleri öneriye çevir
       if (empty($suggestions)) {
           $sentences = preg_split('/[.!?]+/', $text);
           foreach ($sentences as $sentence) {
               $cleaned = trim($sentence);
               if (strlen($cleaned) > 30 && strlen($cleaned) < 200) {
                   $suggestions[] = $cleaned;
                   if (count($suggestions) >= 5) break; // Max 5 öneri
               }
           }
       }
       
       // Son çare: Paragrafları böl
       if (empty($suggestions)) {
           $paragraphs = array_filter(explode("\n\n", $text));
           foreach ($paragraphs as $paragraph) {
               $cleaned = trim($paragraph);
               if (strlen($cleaned) > 20) {
                   $suggestions[] = substr($cleaned, 0, 150) . '...';
                   if (count($suggestions) >= 3) break;
               }
           }
       }
       
       return array_slice($suggestions, 0, 8); // Max 8 öneri göster
   }

   /**
    * AI response'unu HTML formatına dönüştür - MODERN TEMPLATE SİSTEMİ
    */
   private function formatAIResponseForDisplay(string $text): string
   {
       try {
           // Yeni AI Response Repository sistemini kullan
           $repository = app(\Modules\AI\App\Services\AIResponseRepository::class);
           
           // SEO feature'ını bul (sayfa analizi için genellikle SEO analiz kullanılır)
           $feature = \Modules\AI\App\Models\AIFeature::where('slug', 'hizli-seo-analizi')
                                                    ->orWhere('slug', 'seo-puan-analizi')
                                                    ->first();
           
           if ($feature) {
               // Modern template formatı ile render et
               $formattedResponse = $repository->formatWithWordBuffer($text, 'feature_test', [
                   'feature_name' => $feature->name,
                   'template_type' => $feature->slug
               ]);
               
               // Eğer formatted_html var ise onu kullan
               if (isset($formattedResponse['formatted_html'])) {
                   return $formattedResponse['formatted_html'];
               }
           }
           
           // Fallback: Modern formatı manuel olarak oluştur
           return $this->buildModernAITemplate($text);
           
       } catch (\Exception $e) {
           // Hata durumunda eski formatı kullan
           \Log::warning('AI Response Template Error: ' . $e->getMessage());
           return $this->formatAIResponseLegacy($text);
       }
   }

   /**
    * Modern AI Template Builder - Manual Implementation
    */
   private function buildModernAITemplate(string $text): string
   {
       // SEO skorunu çıkar
       $score = $this->extractSEOScoreFromText($text);
       $scoreColor = $score >= 80 ? 'success' : ($score >= 60 ? 'warning' : 'danger');
       $scoreStatus = $this->getSEOStatusText($score);
       
       // Ana content'i parse et
       $analysisItems = $this->parseAnalysisItems($text);
       $recommendations = $this->parseRecommendations($text);
       
       return '
       <div class="ai-response-template seo-score-template">
           <div class="row">
               <!-- Hero Score Section - Sol Taraf -->
               <div class="col-lg-4 col-md-6">
                   <div class="hero-score-card">
                       <div class="circular-score circular-score-' . $scoreColor . '">
                           <div class="score-inner">
                               <div class="score-number">' . $score . '</div>
                               <div class="score-label">SEO Skoru</div>
                           </div>
                       </div>
                       <div class="score-status">
                           <i class="fas fa-' . ($score >= 80 ? 'check-circle' : ($score >= 60 ? 'exclamation-triangle' : 'times-circle')) . ' text-' . $scoreColor . '"></i>
                           <span class="status-text">' . $scoreStatus . '</span>
                       </div>
                   </div>
               </div>
               
               <!-- Analysis Section - Sağ Taraf -->
               <div class="col-lg-8 col-md-6">
                   <div class="analysis-section">
                       <h5><i class="fas fa-chart-line me-2"></i>Analiz Sonuçları</h5>
                       <div class="analysis-items">
                           ' . $this->buildAnalysisItemsHTML($analysisItems) . '
                       </div>
                   </div>
               </div>
           </div>
           
           <!-- Recommendations Section - Full Width -->
           <div class="row mt-4">
               <div class="col-12">
                   <div class="recommendations-section">
                       <h5><i class="fas fa-lightbulb me-2"></i>Önerilerim</h5>
                       <div class="recommendation-cards">
                           ' . $this->buildRecommendationCardsHTML($recommendations) . '
                       </div>
                   </div>
               </div>
           </div>
           
           <!-- Technical Details - Collapsible -->
           <div class="row mt-3">
               <div class="col-12">
                   <div class="technical-details">
                       <div class="card">
                           <div class="card-header cursor-pointer" data-bs-toggle="collapse" data-bs-target="#technicalDetails">
                               <i class="fas fa-cog me-2"></i>Teknik Detaylar
                               <i class="fas fa-chevron-down float-end"></i>
                           </div>
                           <div id="technicalDetails" class="collapse">
                               <div class="card-body">
                                   <div class="technical-content">
                                       ' . $this->parseResponseContent($text) . '
                                   </div>
                               </div>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       </div>';
   }

   /**
    * Legacy formatı - fallback için
    */
   private function formatAIResponseLegacy(string $text): string
   {
       // HTML karakterlerini encode et
       $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
       
       // Başlıkları formatla (büyük harfle başlayan satırlar)
       $text = preg_replace('/^([A-Z][A-Za-zıüğşöçİÜĞŞÖÇ\s]+:)\s*/m', '<strong class="text-primary">$1</strong> ', $text);
       
       // Numaralı listeleri formatla
       $text = preg_replace('/^(\d+\.)\s*/m', '<span class="badge bg-primary me-2">$1</span>', $text);
       
       // Tire ile başlayan maddeleri formatla  
       $text = preg_replace('/^[-•]\s*/m', '<i class="fas fa-arrow-right text-primary me-2"></i>', $text);
       
       // Skor/puan vurgulama
       $text = preg_replace('/(SEO Puanı|Skor|Puan):\s*(\d+)/i', '<span class="badge bg-success fs-6">$1: $2</span>', $text);
       
       // Satır sonlarını br'ye çevir
       $text = nl2br($text);
       
       // Paragraf boşluklarını düzenle
       $text = preg_replace('/(<br\s*\/?>\s*){2,}/', '</p><p class="mb-3">', $text);
       $text = '<p class="mb-3">' . $text . '</p>';
       
       return $text;
   }

   /**
    * SEO Score Helper Methods - Modern Template System
    */
   private function extractSEOScoreFromText(string $text): int
   {
       // Regex ile SEO skorunu bul
       if (preg_match('/\b(\d{1,3})\s*[\/\%]?\s*(?:100|puan|skor)/i', $text, $matches)) {
           return intval($matches[1]);
       }
       return 75; // Default score
   }

   private function getSEOStatusText(int $score): string
   {
       if ($score >= 90) return 'Mükemmel';
       if ($score >= 80) return 'Çok İyi';
       if ($score >= 60) return 'İyi';
       if ($score >= 40) return 'Geliştirilmeli';
       return 'Kötü';
   }

   private function parseAnalysisItems(string $text): array
   {
       $items = [];
       $patterns = [
           '/başlık.*?(eksik|kısa|uzun|problem)/i' => ['label' => 'Başlık Optimizasyonu', 'status' => 'warning'],
           '/meta.*?(eksik|kısa|uzun|problem)/i' => ['label' => 'Meta Açıklama', 'status' => 'warning'], 
           '/anahtar.*?(eksik|yok|problem)/i' => ['label' => 'Anahtar Kelime', 'status' => 'danger'],
           '/içerik.*?(kısa|yetersiz|problem)/i' => ['label' => 'İçerik Kalitesi', 'status' => 'warning'],
       ];
       
       foreach ($patterns as $pattern => $config) {
           if (preg_match($pattern, $text, $matches)) {
               $items[] = [
                   'label' => $config['label'],
                   'status' => $config['status'], 
                   'detail' => $matches[0]
               ];
           }
       }
       
       if (empty($items)) {
           $items = [
               ['label' => 'Genel Analiz', 'status' => 'info', 'detail' => 'SEO analizi tamamlandı'],
               ['label' => 'Öneriler', 'status' => 'success', 'detail' => 'İyileştirme önerileri hazır']
           ];
       }
       
       return $items;
   }

   private function parseRecommendations(string $text): array
   {
       $recommendations = [];
       
       if (preg_match_all('/(?:^\d+\.|\*|\-)\s*(.+?)$/m', $text, $matches)) {
           foreach ($matches[1] as $index => $rec) {
               $recommendations[] = [
                   'title' => 'Öneri ' . ($index + 1),
                   'action' => trim($rec),
                   'priority' => $index < 2 ? 'high' : 'medium'
               ];
           }
       }
       
       if (empty($recommendations)) {
           $recommendations = [
               ['title' => 'İçerik İyileştir', 'action' => 'Analiz sonuçlarına göre içeriği optimize edin', 'priority' => 'high'],
               ['title' => 'SEO Teknik', 'action' => 'Teknik SEO iyileştirmelerini uygulayın', 'priority' => 'medium']
           ];
       }
       
       return $recommendations;
   }

   private function buildAnalysisItemsHTML(array $items): string
   {
       $html = '';
       foreach ($items as $item) {
           $status = $item['status'] ?? 'info';
           $icon = $this->getStatusIcon($status);
           $html .= '
           <div class="analysis-item analysis-item-' . $status . '">
               <div class="item-header">
                   <i class="' . $icon . ' me-2"></i>
                   <span class="item-label">' . $item['label'] . '</span>
                   <span class="badge badge-' . $status . ' ms-auto">' . ucfirst($status) . '</span>
               </div>
               <div class="item-detail">' . $item['detail'] . '</div>
           </div>';
       }
       return $html;
   }

   private function buildRecommendationCardsHTML(array $recommendations): string
   {
       $html = '';
       foreach ($recommendations as $rec) {
           $priority = $rec['priority'] ?? 'medium';
           $priorityClass = $priority === 'high' ? 'danger' : ($priority === 'medium' ? 'warning' : 'info');
           $html .= '
           <div class="recommendation-card">
               <div class="card border-' . $priorityClass . '">
                   <div class="card-body">
                       <h6 class="card-title">
                           <i class="fas fa-arrow-up me-2 text-' . $priorityClass . '"></i>
                           ' . $rec['title'] . '
                       </h6>
                       <p class="card-text">' . $rec['action'] . '</p>
                       <span class="badge bg-' . $priorityClass . '">' . strtoupper($priority) . ' ÖNCELİK</span>
                   </div>
               </div>
           </div>';
       }
       return $html;
   }

   private function parseResponseContent(string $response): string
   {
       $content = nl2br(htmlspecialchars($response));
       $content = preg_replace('/^(#+)\s*(.+?)$/m', '<strong>$2</strong>', $content);
       $content = preg_replace('/^\*\s*(.+?)$/m', '<li>$1</li>', $content);
       $content = preg_replace('/(<li>.*<\/li>)/s', '<ul>$1</ul>', $content);
       return $content;
   }

   private function getStatusIcon(string $status): string
   {
       return match($status) {
           'success' => 'fas fa-check-circle text-success',
           'warning' => 'fas fa-exclamation-triangle text-warning', 
           'danger' => 'fas fa-times-circle text-danger',
           default => 'fas fa-info-circle text-info'
       };
   }

   /**
    * HTML format için helper metod
    */
   private function formatAnalysisResultsHTML($analysis)
   {
       if (empty($analysis)) return '<div style="color: red;">❌ Analiz sonucu yok</div>';
       
       $scoreColor = $analysis['overall_score'] >= 80 ? '#10b981' : ($analysis['overall_score'] >= 60 ? '#f59e0b' : '#ef4444');
       
       $html = '<div style="background: white; border-radius: 8px; padding: 15px;">';
       
       // Ana skor
       $html .= '<div style="text-align: center; margin-bottom: 15px;">';
       $html .= '<div style="font-size: 32px; font-weight: bold; color: ' . $scoreColor . ';">' . $analysis['overall_score'] . '/100</div>';
       $html .= '<div style="color: #6b7280; font-size: 14px;">🎯 SEO Analiz Skoru</div>';
       $html .= '</div>';
       
       // Detaylı skorlar
       if (isset($analysis['title_score']) || isset($analysis['content_score'])) {
           $html .= '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px;">';
           
           if (isset($analysis['title_score'])) {
               $html .= '<div style="text-align: center; padding: 10px; background: #f3f4f6; border-radius: 8px;">';
               $html .= '<div style="font-weight: bold; color: #374151;">📝 Başlık</div>';
               $html .= '<div style="font-size: 18px; font-weight: bold; color: #667eea;">' . $analysis['title_score'] . '/100</div>';
               $html .= '</div>';
           }
           
           if (isset($analysis['content_score'])) {
               $html .= '<div style="text-align: center; padding: 10px; background: #f3f4f6; border-radius: 8px;">';
               $html .= '<div style="font-weight: bold; color: #374151;">📄 İçerik</div>';
               $html .= '<div style="font-size: 18px; font-weight: bold; color: #667eea;">' . $analysis['content_score'] . '/100</div>';
               $html .= '</div>';
           }
           
           $html .= '</div>';
       }
       
       // Öneriler
       if (!empty($analysis['suggestions'])) {
           $html .= '<div>';
           $html .= '<div style="font-weight: bold; margin-bottom: 10px; color: #374151;">💡 AI Önerileri:</div>';
           
           foreach ($analysis['suggestions'] as $suggestion) {
               $icon = str_contains($suggestion, '✅') ? '✅' : '💡';
               $color = str_contains($suggestion, '✅') ? '#10b981' : '#6b7280';
               
               $html .= '<div style="display: flex; align-items: flex-start; margin-bottom: 8px; padding: 8px; background: #f9fafb; border-radius: 6px;">';
               $html .= '<span style="margin-right: 8px; font-size: 16px;">' . $icon . '</span>';
               $html .= '<span style="flex: 1; color: ' . $color . '; font-size: 14px;">' . htmlspecialchars($suggestion) . '</span>';
               $html .= '</div>';
           }
           
           $html .= '</div>';
       }
       
       $html .= '</div>';
       
       return $html;
   }

   /**
    * Helper: Kaydet uyarısı göster
    */
   private function showSaveFirstWarning()
   {
       $this->dispatch('toast', [
           'title' => '⚠️ Dikkat',
           'message' => 'AI özelliklerini kullanabilmek için önce sayfayı kaydedin',
           'type' => 'warning'
       ]);
   }

   /**
    * AI Features'ları database'den dinamik yükleme
    */
   public function getAIFeaturesProperty()
   {
       try {
           // Sayfa Yönetimi kategorisini al
           $pageCategory = \Modules\AI\App\Models\AIFeatureCategory::where('slug', 'sayfa-yoenetimi-9')->first();
           
           if (!$pageCategory) {
               \Log::warning('Sayfa Yönetimi kategorisi bulunamadı');
               return collect([]);
           }

           return \Modules\AI\App\Models\AIFeature::where('status', 'active')
               ->where('ai_feature_category_id', $pageCategory->ai_feature_category_id)
               ->orderBy('sort_order')
               ->orderBy('name')
               ->limit(12) // Yeni 18 feature'dan 12 tanesini göster
               ->get()
               ->map(function($feature) {
                   // Token cost JSON'dan estimated değerini al
                   $tokenCost = 100; // Default
                   if ($feature->token_cost) {
                       $tokenData = json_decode($feature->token_cost, true);
                       $tokenCost = $tokenData['estimated'] ?? 100;
                   }
                   
                   return [
                       'slug' => $feature->slug,
                       'name' => $feature->name,
                       'emoji' => $feature->emoji ?: '🤖',
                       'description' => $feature->description,
                       'helper_function' => $feature->helper_function,
                       'button_text' => $feature->button_text ?: $feature->name,
                       'token_cost' => $tokenCost
                   ];
               });
       } catch (\Exception $e) {
           \Log::error('AI Features yüklenemedi: ' . $e->getMessage());
           return collect([]);
       }
   }

   /**
    * AI Feature için CSS class döndürme
    */
   public function getFeatureClass($slug)
   {
       $classes = [
           'hizli-seo-analizi' => 'analysis',
           'ai-asistan-sohbet' => 'suggestions',
           'icerik-optimizasyonu' => 'optimize',
           'anahtar-kelime-arastirmasi' => 'keywords',
           'coklu-dil-cevirisi' => 'translate',
           'rekabet-analizi' => 'competitor',
           'icerik-kalite-skoru' => 'quality',
           'schema-markup-uretici' => 'schema'
       ];
       
       return $classes[$slug] ?? 'content';
   }

   /**
    * AI Feature için ikon döndürme
    */
   public function getFeatureIcon($slug)
   {
       $icons = [
           'hizli-seo-analizi' => 'fas fa-chart-line',
           'ai-asistan-sohbet' => 'fas fa-comments',
           'icerik-optimizasyonu' => 'fas fa-magic',
           'anahtar-kelime-arastirmasi' => 'fas fa-key',
           'coklu-dil-cevirisi' => 'fas fa-language',
           'rekabet-analizi' => 'fas fa-chart-bar',
           'icerik-kalite-skoru' => 'fas fa-star',
           'schema-markup-uretici' => 'fas fa-code',
           'blog-yazisi-jeneratoru' => 'fas fa-edit',
           'sosyal-medya-icerigi' => 'fas fa-share-alt',
           'urun-aciklamasi-yazici' => 'fas fa-box',
           'meta-etiket-optimizasyonu' => 'fas fa-tags'
       ];
       
       return $icons[$slug] ?? 'fas fa-robot';
   }

   /**
    * Dinamik AI Feature çalıştırma
    */
   public function executeAIFeature($slug)
   {
       \Log::info('🤖 executeAIFeature çağrıldı:', ['slug' => $slug, 'pageId' => $this->pageId]);
       
       if (!$this->pageId) {
           $this->showSaveFirstWarning();
           return;
       }

       try {
           $this->aiProgress = true;
           
           // Sayfa verilerini hazırla
           $pageData = [
               'title' => $this->multiLangInputs[$this->currentLanguage]['title'] ?? '',
               'content' => strip_tags($this->multiLangInputs[$this->currentLanguage]['body'] ?? ''),
               'language' => $this->currentLanguage,
               'page_id' => $this->pageId,
               'user_context' => 'page_management'
           ];
           
           // AI helper fonksiyonu ile feature çalıştır
           $result = ai_execute_feature($slug, $pageData);
           
           if ($result && !empty($result['response'])) {
               // Yanıtı işle ve AI suggestions'a kaydet
               $response = $result['response'];
               
               if (is_string($response)) {
                   $this->aiSuggestions = [$response];
               } elseif (is_array($response)) {
                   $this->aiSuggestions = array_values($response);
               }
               
               // Başarı mesajı göster
               $this->dispatch('toast', [
                   'title' => '🤖 AI Feature Tamamlandı',
                   'message' => 'AI işlemi başarıyla gerçekleştirildi',
                   'type' => 'success'
               ]);
           } else {
               throw new \Exception('AI feature yanıt vermedi');
           }
           
           $this->aiProgress = false;
           
       } catch (\Exception $e) {
           $this->aiProgress = false;
           \Log::error('AI Feature hatası:', ['slug' => $slug, 'error' => $e->getMessage()]);
           
           $this->dispatch('toast', [
               'title' => 'AI Feature Hatası',
               'message' => 'AI işlemi başarısız: ' . $e->getMessage(),
               'type' => 'error'
           ]);
       }
   }

   public function render()
   {
       return view('page::admin.livewire.page-manage-component', [
           'aiFeatures' => $this->getAIFeaturesProperty()
       ]);
   }
}