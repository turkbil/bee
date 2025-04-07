<?php
namespace Modules\Portfolio\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Str;
use Modules\Portfolio\App\Models\PortfolioCategory;

#[Layout('admin.layout')]
class PortfolioCategoryManageComponent extends Component
{
   public $categoryId;
   public $inputs = [
       'title' => '',
       'slug' => '',
       'body' => '',
       'order' => 0,
       'metakey' => '',
       'metadesc' => '',
       'is_active' => true,
   ];

   public function mount($id = null)
   {
       if ($id) {
           $this->categoryId = $id;
           $category = PortfolioCategory::findOrFail($id);
           $this->inputs = $category->only(array_keys($this->inputs));
       } else {
           $this->inputs['order'] = PortfolioCategory::max('order') + 1;
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