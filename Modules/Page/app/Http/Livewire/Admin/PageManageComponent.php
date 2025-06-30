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
                   'seo' => [
                       'meta_title' => $page->getSeoField($lang, 'meta_title', ''),
                       'meta_description' => $page->getSeoField($lang, 'meta_description', ''),
                       'keywords' => $page->getSeoField($lang, 'keywords', []),
                       'og_title' => $page->getSeoField($lang, 'og_title', ''),
                       'og_description' => $page->getSeoField($lang, 'og_description', ''),
                       'og_image' => $page->getSeoField($lang, 'og_image', ''),
                       'canonical_url' => $page->getSeoField($lang, 'canonical_url', ''),
                       'robots' => $page->getSeoField($lang, 'robots', 'index,follow'),
                   ]
               ];
           }
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
      
      // SEO verilerini hazırla
      $seoData = [];
      foreach ($this->availableLanguages as $lang) {
          if (isset($this->multiLangInputs[$lang]['seo'])) {
              $langSeoData = $this->multiLangInputs[$lang]['seo'];
              
              // Boş meta_title için title'dan oluştur
              if (empty($langSeoData['meta_title']) && !empty($this->multiLangInputs[$lang]['title'])) {
                  $langSeoData['meta_title'] = $this->multiLangInputs[$lang]['title'];
              }
              
              // Boş meta_description için body'den oluştur
              if (empty($langSeoData['meta_description']) && !empty($this->multiLangInputs[$lang]['body'])) {
                  $langSeoData['meta_description'] = Str::limit(strip_tags($this->multiLangInputs[$lang]['body']), 160, '');
              }
              
              // Boş değerleri temizle
              $langSeoData = array_filter($langSeoData, function($value) {
                  return !is_null($value) && $value !== '' && $value !== [];
              });
              
              if (!empty($langSeoData)) {
                  $seoData[$lang] = $langSeoData;
              }
          }
      }
      
      if (!empty($seoData)) {
          $multiLangData['seo'] = $seoData;
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

   public function render()
   {
       return view('page::admin.livewire.page-manage-component');
   }
}