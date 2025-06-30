<?php
namespace Modules\Portfolio\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Str;
use Modules\Portfolio\App\Models\PortfolioCategory;
use Modules\LanguageManagement\App\Models\TenantLanguage;

#[Layout('admin.layout')]
class PortfolioCategoryManageComponent extends Component
{
   public $categoryId;
   public $currentLanguage = 'tr'; // Aktif dil sekmesi
   public $availableLanguages = []; // Site dillerinden dinamik olarak yüklenecek
   
   // Çoklu dil inputs - dinamik olarak oluşturulacak
   public $multiLangInputs = [];
   
   // Dil-neutral inputs
   public $inputs = [
       'order' => 0,
       'is_active' => true,
   ];

   public function mount($id = null)
   {
       // Site dillerini dinamik olarak yükle
       $this->loadAvailableLanguages();
       
       if ($id) {
           $this->categoryId = $id;
           $category = PortfolioCategory::findOrFail($id);
           
           // Dil-neutral alanları doldur
           $this->inputs = [
               'order' => $category->order,
               'is_active' => $category->is_active,
           ];
           
           // Çoklu dil alanları doldur
           foreach ($this->availableLanguages as $lang) {
               $this->multiLangInputs[$lang] = [
                   'title' => is_array($category->title) ? ($category->title[$lang] ?? '') : '',
                   'body' => is_array($category->body) ? ($category->body[$lang] ?? '') : '',
                   'slug' => is_array($category->slug) ? ($category->slug[$lang] ?? '') : '',
                   'metakey' => is_array($category->metakey) ? ($category->metakey[$lang] ?? '') : '',
                   'metadesc' => is_array($category->metadesc) ? ($category->metadesc[$lang] ?? '') : '',
               ];
           }
       } else {
           // Yeni kategori için boş inputs hazırla
           $this->inputs['order'] = PortfolioCategory::max('order') + 1;
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
       \Log::info('PORTFOLIO CATEGORY Module - Language Settings', [
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
           'inputs.slug' => 'nullable|unique:portfolio_categories,slug,' . $this->categoryId . ',portfolio_category_id',
           'inputs.body' => 'nullable',
           'inputs.order' => 'required|integer|min:0',
           'inputs.metakey' => 'nullable',
           'inputs.metadesc' => 'nullable|string|max:255',
           'inputs.is_active' => 'boolean',
       ];
   }

   protected $messages = [
       'inputs.title.required' => 'Başlık alanı zorunludur',
       'inputs.title.min' => 'Başlık en az 3 karakter olmalıdır',
       'inputs.title.max' => 'Başlık en fazla 255 karakter olmalıdır',
       'inputs.slug.unique' => 'Bu URL daha önce kullanılmış',
       'inputs.order.required' => 'Sıra numarası zorunludur',
       'inputs.order.integer' => 'Sıra numarası tam sayı olmalıdır',
       'inputs.order.min' => 'Sıra numarası en az 0 olmalıdır',
   ];

   public function save($redirect = false)
   {
       $this->validate();
    
       $data = array_merge($this->inputs, [
           'title' => Str::limit($this->inputs['title'], 191, ''),
           'slug' => $this->inputs['slug'] ?: Str::slug($this->inputs['title']),
           'metakey' => is_array($this->inputs['metakey']) ? implode(',', $this->inputs['metakey']) : $this->inputs['metakey'],
           'metadesc' => Str::limit($this->inputs['metadesc'] ?? $this->inputs['body'], 191, '')
       ]);
    
       if ($this->categoryId) {
           $category = PortfolioCategory::findOrFail($this->categoryId);
           $currentData = collect($category->toArray())->only(array_keys($data))->all();
          
           if ($data == $currentData) {
               $toast = [
                   'title' => 'Bilgi',
                   'message' => 'Herhangi bir değişiklik yapılmadı.',
                   'type' => 'info'
               ];
           } else {
               $category->update($data);
               
               log_activity($category, 'güncellendi');
               
               $toast = [
                   'title' => 'Başarılı!',
                   'message' => 'Kategori başarıyla güncellendi.',
                   'type' => 'success',
               ];
           }
       } else {
           $category = PortfolioCategory::create($data);
           
           log_activity($category, 'oluşturuldu');
           
           $toast = [
               'title' => 'Başarılı!',
               'message' => 'Kategori başarıyla oluşturuldu.',
               'type' => 'success',
           ];
       }
    
       if ($redirect) {
           session()->flash('toast', $toast);
           return redirect()->route('admin.portfolio.category.index');
       }
    
       $this->dispatch('toast', $toast);
   }

   public function render()
   {
       return view('portfolio::admin.livewire.portfolio-category-manage-component');
   }
}