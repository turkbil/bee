<?php
namespace Modules\Page\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Page\App\Models\Page;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;

#[Layout('admin.layout')]
class PageManageComponent extends Component
{
   use WithFileUploads;

   public $pageId;
   public $currentLanguage = 'tr'; // Aktif dil sekmesi
   public $availableLanguages = ['tr', 'en', 'ar']; // Mevcut diller
   
   // Çoklu dil inputs - her dil için ayrı
   public $multiLangInputs = [
       'tr' => [
           'title' => '',
           'body' => '',
           'slug' => '',
           'metakey' => '',
           'metadesc' => '',
       ],
       'en' => [
           'title' => '',
           'body' => '',
           'slug' => '',
           'metakey' => '',
           'metadesc' => '',
       ],
       'ar' => [
           'title' => '',
           'body' => '',
           'slug' => '',
           'metakey' => '',
           'metadesc' => '',
       ],
   ];
   
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
                   'metakey' => $page->getTranslated('metakey', $lang) ?? '',
                   'metadesc' => $page->getTranslated('metadesc', $lang) ?? '',
               ];
           }
       }
       
       // Studio modülü aktif mi kontrol et
       $this->studioEnabled = class_exists('Modules\Studio\App\Http\Livewire\EditorComponent');
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
           $rules["multiLangInputs.{$lang}.metakey"] = 'nullable|string';
           $rules["multiLangInputs.{$lang}.metadesc"] = 'nullable|string|max:255';
           $rules["multiLangInputs.{$lang}.body"] = 'nullable|string';
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
      foreach (['title', 'slug', 'body', 'metakey', 'metadesc'] as $field) {
          $multiLangData[$field] = [];
          foreach ($this->availableLanguages as $lang) {
              $value = $this->multiLangInputs[$lang][$field] ?? '';
              
              // Boş slug'lar için otomatik oluştur
              if ($field === 'slug' && empty($value) && !empty($this->multiLangInputs[$lang]['title'])) {
                  $value = Str::slug($this->multiLangInputs[$lang]['title']);
              }
              
              // Boş metadesc için body'den oluştur  
              if ($field === 'metadesc' && empty($value) && !empty($this->multiLangInputs[$lang]['body'])) {
                  $value = Str::limit(strip_tags($this->multiLangInputs[$lang]['body']), 191, '');
              }
              
              if (!empty($value)) {
                  $multiLangData[$field][$lang] = $value;
              }
          }
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