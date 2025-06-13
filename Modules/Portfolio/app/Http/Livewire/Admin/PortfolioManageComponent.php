<?php
namespace Modules\Portfolio\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Modules\Portfolio\App\Models\Portfolio;
use Modules\Portfolio\App\Models\PortfolioCategory;
use Modules\Portfolio\App\Http\Livewire\Traits\WithImageUpload;
use Illuminate\Support\Str;

#[Layout('admin.layout')]
class PortfolioManageComponent extends Component
{
   use WithFileUploads, WithImageUpload;

   public $portfolioId;
   public $studioEnabled = false;
   public $categories = [];
   public $inputs = [
       'portfolio_category_id' => '',
       'title' => '',
       'body' => '',
       'slug' => '',
       'metakey' => '',
       'metadesc' => '',
       'css' => '',
       'js' => '',
       'is_active' => true,
   ];

   public function mount($id = null)
   {
       $this->categories = PortfolioCategory::where('is_active', true)
            ->orderBy('title')
            ->get();
            
       // Studio modülü aktif mi kontrol et
       $this->studioEnabled = class_exists('Modules\Studio\App\Http\Livewire\EditorComponent');
            
       if ($id) {
           $this->portfolioId = $id;
           $portfolio = Portfolio::findOrFail($id);
           $this->inputs = $portfolio->only(array_keys($this->inputs));
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
       'inputs.portfolio_category_id.required' => 'Kategori seçimi zorunludur',
       'inputs.title.required' => 'Başlık alanı zorunludur.',
       'inputs.title.min' => 'Başlık en az 3 karakter olmalıdır.',
       'inputs.title.max' => 'Başlık 255 karakteri geçemez.',
       'temporaryImages.*.image' => 'Dosya bir resim olmalıdır',
       'temporaryImages.*.mimes' => 'Resim dosyası jpg, jpeg, png veya webp formatında olmalıdır',
       'temporaryImages.*.max' => 'Resim dosyası en fazla 2MB olabilir'
   ];

   public function save($redirect = false, $resetForm = false)
   {
      $this->validate();
      
      $data = array_merge($this->inputs, [
          'title' => Str::limit($this->inputs['title'], 191, ''),
          'slug' => $this->inputs['slug'] ?: Str::slug($this->inputs['title']),
          'metakey' => is_array($this->inputs['metakey']) ? implode(',', $this->inputs['metakey']) : $this->inputs['metakey'],
          'metadesc' => Str::limit($this->inputs['metadesc'] ?? $this->inputs['body'], 191, '')
      ]);
   
      if ($this->portfolioId) {
          $portfolio = Portfolio::findOrFail($this->portfolioId);
          $currentData = collect($portfolio->toArray())->only(array_keys($data))->all();
          
          if ($data == $currentData) {
              $toast = [
                  'title' => 'Bilgi',
                  'message' => 'Herhangi bir değişiklik yapılmadı.',
                  'type' => 'info'
              ];
          } else {
              $portfolio->update($data);
              $this->handleImageUpload($portfolio);
              
              log_activity($portfolio, 'güncellendi');
              
              $toast = [
                  'title' => 'Başarılı!',
                  'message' => 'Portfolyo başarıyla güncellendi.',
                  'type' => 'success'
              ];
          }
      } else {
          $portfolio = Portfolio::create($data);
          $this->portfolioId = $portfolio->portfolio_id;
          $this->handleImageUpload($portfolio);
          
          log_activity($portfolio, 'oluşturuldu');
          
          $toast = [
              'title' => 'Başarılı!',
              'message' => 'Portfolyo başarıyla oluşturuldu.',
              'type' => 'success'
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