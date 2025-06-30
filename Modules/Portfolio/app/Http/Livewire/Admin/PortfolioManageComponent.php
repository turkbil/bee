<?php
namespace Modules\Portfolio\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Modules\Portfolio\App\Models\Portfolio;
use Modules\Portfolio\App\Models\PortfolioCategory;
use Modules\Portfolio\App\Http\Livewire\Traits\WithImageUpload;
use Modules\Portfolio\App\Services\PortfolioService;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Illuminate\Support\Str;

#[Layout('admin.layout')]
class PortfolioManageComponent extends Component
{
   use WithFileUploads, WithImageUpload;

   protected PortfolioService $portfolioService;
   
   public $portfolioId;
   public $studioEnabled = false;
   public $categories = [];
   public $currentLanguage = 'tr'; // Aktif dil sekmesi
   public $availableLanguages = []; // Site dillerinden dinamik olarak yüklenecek
   
   // Çoklu dil inputs - dinamik olarak oluşturulacak
   public $multiLangInputs = [];
   
   // Dil-neutral inputs
   public $inputs = [
       'portfolio_category_id' => '',
       'css' => '',
       'js' => '',
       'is_active' => true,
   ];

   public function boot(PortfolioService $portfolioService)
   {
       $this->portfolioService = $portfolioService;
   }

   public function mount($id = null)
   {
       // Site dillerini dinamik olarak yükle
       $this->loadAvailableLanguages();
       
       $this->categories = PortfolioCategory::where('is_active', true)
            ->orderBy('portfolio_category_id')
            ->get();
            
       // Studio modülü aktif mi kontrol et
       $this->studioEnabled = class_exists('Modules\Studio\App\Http\Livewire\EditorComponent');
            
       if ($id) {
           $this->portfolioId = $id;
           $portfolio = $this->portfolioService->getById($id);
           
           if (!$portfolio) {
               abort(404, 'Portfolio bulunamadı');
           }
           
           // Dil-neutral alanları doldur
           $this->inputs = [
               'portfolio_category_id' => $portfolio->portfolio_category_id,
               'css' => $portfolio->css,
               'js' => $portfolio->js,
               'is_active' => $portfolio->is_active,
           ];
           
           // Çoklu dil alanları doldur
           foreach ($this->availableLanguages as $lang) {
               $this->multiLangInputs[$lang] = [
                   'title' => is_array($portfolio->title) ? ($portfolio->title[$lang] ?? '') : '',
                   'body' => is_array($portfolio->body) ? ($portfolio->body[$lang] ?? '') : '',
                   'slug' => is_array($portfolio->slug) ? ($portfolio->slug[$lang] ?? '') : '',
                   'metakey' => is_array($portfolio->metakey) ? ($portfolio->metakey[$lang] ?? '') : '',
                   'metadesc' => is_array($portfolio->metadesc) ? ($portfolio->metadesc[$lang] ?? '') : '',
               ];
           }
       } else {
           // Yeni portfolio için boş inputs hazırla
           $this->initializeEmptyInputs();
       }
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
       \Log::info('PORTFOLIO Module - Language Settings', [
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
           'inputs.portfolio_category_id' => 'required',
           'inputs.title' => 'required|min:3|max:255',
           'inputs.slug' => 'nullable|unique:portfolios,slug,' . $this->portfolioId . ',portfolio_id',
           'inputs.metakey' => 'nullable',
           'inputs.metadesc' => 'nullable|string|max:255',
           'inputs.css' => 'nullable|string',
           'inputs.js' => 'nullable|string',
           'inputs.is_active' => 'boolean',
           'temporaryImages.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
       ];
   }

   protected $messages = [
       'inputs.portfolio_category_id.required' => 'portfolio::admin.category_required',
       'inputs.title.required' => 'portfolio::admin.title_required',
       'inputs.title.min' => 'portfolio::admin.title_min',
       'inputs.title.max' => 'portfolio::admin.title_max',
       'temporaryImages.*.image' => 'admin.file_must_be_image',
       'temporaryImages.*.mimes' => 'admin.image_format_error',
       'temporaryImages.*.max' => 'admin.image_size_error'
   ];

   public function save($redirect = false, $resetForm = false)
   {
      $this->validate();
      
      try {
          $locale = app()->getLocale();
          
          // Multi-language data preparation
          $data = [
              'portfolio_category_id' => $this->inputs['portfolio_category_id'],
              'title' => [$locale => Str::limit($this->inputs['title'], 191, '')],
              'slug' => [$locale => $this->inputs['slug'] ?: Str::slug($this->inputs['title'])],
              'body' => [$locale => $this->inputs['body']],
              'metakey' => [$locale => is_array($this->inputs['metakey']) ? implode(',', $this->inputs['metakey']) : $this->inputs['metakey']],
              'metadesc' => [$locale => Str::limit($this->inputs['metadesc'] ?? $this->inputs['body'], 191, '')],
              'image' => $this->inputs['image'] ?? null,
              'css' => $this->inputs['css'] ?? null,
              'js' => $this->inputs['js'] ?? null,
              'client' => $this->inputs['client'] ?? null,
              'date' => $this->inputs['date'] ?? null,
              'url' => $this->inputs['url'] ?? null,
              'is_active' => $this->inputs['is_active'],
          ];
       
          if ($this->portfolioId) {
              // Güncelleme işlemi
              $portfolio = $this->portfolioService->update($this->portfolioId, $data);
              $this->handleImageUpload($portfolio);
              
              $toast = [
                  'title' => __('admin.success'),
                  'message' => __('portfolio::admin.portfolio_updated'),
                  'type' => 'success'
              ];
          } else {
              // Oluşturma işlemi
              $portfolio = $this->portfolioService->create($data);
              $this->portfolioId = $portfolio->portfolio_id;
              $this->handleImageUpload($portfolio);
              
              $toast = [
                  'title' => __('admin.success'),
                  'message' => __('portfolio::admin.portfolio_created'),
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
          return redirect()->route('admin.portfolio.index');
      }
   
      $this->dispatch('toast', $toast);
   
      if ($resetForm && !$this->portfolioId) {
          $this->reset();
      }
   }

   public function render()
   {
       return view('portfolio::admin.livewire.portfolio-manage-component', [
           'model' => $this->portfolioId ? Portfolio::find($this->portfolioId) : null
       ]);
   }
}