<?php

namespace Modules\WidgetManagement\app\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Str;
use Modules\WidgetManagement\app\Models\WidgetCategory;

#[Layout('admin.layout')]
class WidgetCategoryManageComponent extends Component
{
   public $categoryId;
   public $inputs = [
       'title' => '',
       'slug' => '',
       'description' => '',
       'icon' => 'fa-puzzle-piece',
       'order' => 0,
       'is_active' => true,
   ];

   public function mount($id = null)
   {
       if ($id) {
           $this->categoryId = $id;
           $category = WidgetCategory::findOrFail($id);
           $this->inputs = $category->only(array_keys($this->inputs));
       } else {
           $this->inputs['order'] = WidgetCategory::max('order') + 1;
       }
   }

   protected function rules()
   {
       return [
           'inputs.title' => 'required|min:3|max:255',
           'inputs.slug' => 'nullable|unique:widget_categories,slug,' . $this->categoryId . ',widget_category_id',
           'inputs.description' => 'nullable',
           'inputs.icon' => 'nullable|string|max:50',
           'inputs.order' => 'required|integer|min:0',
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
           'icon' => $this->inputs['icon'] ?: 'fa-puzzle-piece',
       ]);
    
       if ($this->categoryId) {
           $category = WidgetCategory::findOrFail($this->categoryId);
           $currentData = collect($category->toArray())->only(array_keys($data))->all();
          
           if ($data == $currentData) {
               $toast = [
                   'title' => 'Bilgi',
                   'message' => 'Herhangi bir değişiklik yapılmadı.',
                   'type' => 'info'
               ];
           } else {
               $category->update($data);
               
               if (function_exists('log_activity')) {
                   log_activity($category, 'güncellendi');
               }
               
               $toast = [
                   'title' => 'Başarılı!',
                   'message' => 'Kategori başarıyla güncellendi.',
                   'type' => 'success',
               ];
           }
       } else {
           $category = WidgetCategory::create($data);
           
           if (function_exists('log_activity')) {
               log_activity($category, 'oluşturuldu');
           }
           
           $toast = [
               'title' => 'Başarılı!',
               'message' => 'Kategori başarıyla oluşturuldu.',
               'type' => 'success',
           ];
       }
    
       if ($redirect) {
           session()->flash('toast', $toast);
           return redirect()->route('admin.widgetmanagement.category.index');
       }
    
       $this->dispatch('toast', $toast);
   }

   public function render()
   {
       return view('widgetmanagement::livewire.widget-category-manage-component');
   }
}