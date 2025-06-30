<?php
namespace Modules\Announcement\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Announcement\App\Models\Announcement;
use Modules\Announcement\App\Services\AnnouncementService;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;

#[Layout('admin.layout')]
class AnnouncementManageComponent extends Component
{
   use WithFileUploads;

   protected AnnouncementService $announcementService;
   
   public $announcementId;
   public $currentLanguage = 'tr'; // Aktif dil sekmesi
   public $availableLanguages = []; // Site dillerinden dinamik olarak yüklenecek
   
   // Çoklu dil inputs - dinamik olarak oluşturulacak
   public $multiLangInputs = [];
   
   // Dil-neutral inputs
   public $inputs = [
       'is_active' => true,
   ];
   
   public $studioEnabled = false;

   public function boot(AnnouncementService $announcementService)
   {
       $this->announcementService = $announcementService;
   }

   public function mount($id = null)
   {
       // Site dillerini dinamik olarak yükle
       $this->loadAvailableLanguages();
       
       if ($id) {
           $this->announcementId = $id;
           $announcement = $this->announcementService->getById($id);
           
           if (!$announcement) {
               abort(404, 'Duyuru bulunamadı');
           }
           
           // Dil-neutral alanları doldur
           $this->inputs = [
               'is_active' => $announcement->is_active,
           ];
           
           // Çoklu dil alanları doldur
           foreach ($this->availableLanguages as $lang) {
               $this->multiLangInputs[$lang] = [
                   'title' => is_array($announcement->title) ? ($announcement->title[$lang] ?? '') : '',
                   'body' => is_array($announcement->body) ? ($announcement->body[$lang] ?? '') : '',
                   'slug' => is_array($announcement->slug) ? ($announcement->slug[$lang] ?? '') : '',
                   'metakey' => is_array($announcement->metakey) ? ($announcement->metakey[$lang] ?? '') : '',
                   'metadesc' => is_array($announcement->metadesc) ? ($announcement->metadesc[$lang] ?? '') : '',
               ];
           }
       } else {
           // Yeni duyuru için boş inputs hazırla
           $this->initializeEmptyInputs();
       }
       
       // Studio modülü aktif mi kontrol et
       $this->studioEnabled = class_exists('Modules\Studio\App\Http\Livewire\StudioEditor');
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
       \Log::info('ANNOUNCEMENT Module - Language Settings', [
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
               'metakey' => '',
               'metadesc' => '',
           ];
       }
   }

   protected function rules()
   {
       return [
           'inputs.title' => 'required|min:3|max:255',
           'inputs.slug' => 'nullable|unique:announcements,slug,' . $this->announcementId . ',announcement_id',
           'inputs.metakey' => 'nullable',
           'inputs.metadesc' => 'nullable|string|max:255',
           'inputs.is_active' => 'boolean',
       ];
   }

   protected $messages = [
       'inputs.title.required' => 'Başlık alanı zorunludur.',
       'inputs.title.min' => 'Başlık en az 3 karakter olmalıdır.',
       'inputs.title.max' => 'Başlık 255 karakteri geçemez.',
   ];

   public function save($redirect = false, $resetForm = false)
   {
      $this->validate();
      
      try {
          $locale = app()->getLocale();
          
          // Multi-language data preparation
          $data = [
              'title' => [$locale => Str::limit($this->inputs['title'], 191, '')],
              'slug' => [$locale => $this->inputs['slug'] ?: Str::slug($this->inputs['title'])],
              'body' => [$locale => $this->inputs['body']],
              'metakey' => [$locale => is_array($this->inputs['metakey']) ? implode(',', $this->inputs['metakey']) : $this->inputs['metakey']],
              'metadesc' => [$locale => Str::limit($this->inputs['metadesc'] ?? $this->inputs['body'], 191, '')],
              'is_active' => $this->inputs['is_active'],
          ];
       
          if ($this->announcementId) {
              // Güncelleme işlemi
              $announcement = $this->announcementService->update($this->announcementId, $data);
              
              $toast = [
                  'title' => __('admin.success'),
                  'message' => __('announcement::admin.announcement_updated'),
                  'type' => 'success'
              ];
          } else {
              // Oluşturma işlemi
              $announcement = $this->announcementService->create($data);
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

   public function render()
   {
       return view('announcement::admin.livewire.announcement-manage-component');
   }
}