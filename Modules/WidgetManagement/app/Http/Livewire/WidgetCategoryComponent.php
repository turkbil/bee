<?php

namespace Modules\WidgetManagement\app\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Illuminate\Support\Str;
use Modules\WidgetManagement\app\Models\WidgetCategory;

#[Layout('admin.layout')]
class WidgetCategoryComponent extends Component
{
    use WithPagination;

    public $title = '';
    public $newCategoryTitle = '';
    public $parentId = null;
    public $categories = [];
    public $expandedCategories = [];
    public $editCategoryId = null;
    public $editData = [
        'title' => '',
        'slug' => '',
        'description' => '',
        'icon' => '',
        'parent_id' => null,
        'is_active' => true
    ];
    
    // Arama için
    public $search = '';
    
    // Seçim işlemleri için
    public $selectedItems = [];
    public $selectAll = false;
    
    // Sıralama için
    public $sortField = 'order';
    public $sortDirection = 'asc';

    protected $rules = [
        'title' => 'required|min:3|max:255',
        'editData.title' => 'required|min:3|max:255',
        'editData.slug' => 'nullable|regex:/^[a-z0-9\-_]+$/i|max:255',
        'editData.description' => 'nullable|max:1000',
        'editData.icon' => 'nullable|max:50',
        'editData.is_active' => 'boolean',
    ];

    protected $messages = [
        'title.required' => 'Kategori başlığı zorunludur.',
        'title.min' => 'Kategori başlığı en az 3 karakter olmalıdır.',
        'title.max' => 'Kategori başlığı en fazla 255 karakter olmalıdır.',
        'editData.title.required' => 'Kategori başlığı zorunludur.',
        'editData.title.min' => 'Kategori başlığı en az 3 karakter olmalıdır.',
        'editData.title.max' => 'Kategori başlığı en fazla 255 karakter olmalıdır.',
        'editData.slug.regex' => 'Slug sadece harfler, rakamlar, tire ve alt çizgi içerebilir.',
    ];

    public function mount()
    {
        $this->loadCategories();
    }

    #[On('refreshPage')] 
    public function loadCategories()
    {
        // Tüm kategorileri hiyerarşik yapıda yükle
        $query = WidgetCategory::query();
        
        // Arama varsa uygula
        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }
        
        // Sıralama
        $query->orderBy($this->sortField, $this->sortDirection);
        
        // Ana kategoriler ve alt kategorileri al
        if (empty($this->search)) {
            $categories = $query->withCount('widgets')
                ->with(['children' => function ($q) {
                    $q->withCount('widgets')->orderBy('order');
                }])
                ->whereNull('parent_id')
                ->get();
        } else {
            // Arama varsa tüm kategorileri getir, parent_id filtrelemesi yapma
            $categories = $query->withCount('widgets')->get();
        }

        $this->categories = $categories;
    }
    
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        
        $this->loadCategories();
    }

    public function toggleExpand($categoryId)
    {
        if (isset($this->expandedCategories[$categoryId])) {
            unset($this->expandedCategories[$categoryId]);
        } else {
            $this->expandedCategories[$categoryId] = true;
        }
    }
    
    public function expandAll()
    {
        foreach ($this->categories as $category) {
            $this->expandedCategories[$category->widget_category_id] = true;
        }
    }
    
    public function collapseAll()
    {
        $this->expandedCategories = [];
    }

    public function startEdit($categoryId)
    {
        $this->editCategoryId = $categoryId;
        $category = WidgetCategory::findOrFail($categoryId);
        
        $this->editData = [
            'title' => $category->title,
            'slug' => $category->slug,
            'description' => $category->description,
            'icon' => $category->icon,
            'parent_id' => $category->parent_id,
            'is_active' => $category->is_active
        ];
    }

    public function cancelEdit()
    {
        $this->editCategoryId = null;
        $this->editData = [
            'title' => '',
            'slug' => '',
            'description' => '',
            'icon' => '',
            'parent_id' => null,
            'is_active' => true
        ];
    }

    public function saveEdit()
    {
        $this->validate([
            'editData.title' => 'required|min:3|max:255',
            'editData.slug' => 'nullable|regex:/^[a-z0-9\-_]+$/i|max:255',
        ]);

        $category = WidgetCategory::findOrFail($this->editCategoryId);
        
        // Slug boşsa otomatik oluştur
        if (empty($this->editData['slug'])) {
            $this->editData['slug'] = Str::slug($this->editData['title']);
        }
        
        $category->update($this->editData);

        if (function_exists('log_activity')) {
            log_activity($category, 'güncellendi');
        }

        $this->dispatch('toast', [
            'title' => 'Başarılı!', 
            'message' => 'Kategori başarıyla güncellendi.',
            'type' => 'success'
        ]);

        $this->cancelEdit();
        $this->loadCategories();
    }

    public function toggleActive($id)
    {
        $category = WidgetCategory::findOrFail($id);
        $category->is_active = !$category->is_active;
        $category->save();

        if (function_exists('log_activity')) {
            log_activity(
                $category,
                $category->is_active ? 'aktif edildi' : 'pasif edildi'
            );
        }

        $this->dispatch('toast', [
            'title' => 'Başarılı!', 
            'message' => "Kategori " . ($category->is_active ? 'aktif' : 'pasif') . " edildi.",
            'type' => 'success'
        ]);

        $this->loadCategories();
    }

    public function delete($id)
    {
        $category = WidgetCategory::findOrFail($id);
        
        if ($category->widgets()->count() > 0) {
            $this->dispatch('toast', [
                'title' => 'Uyarı!',
                'message' => 'Bu kategoriye bağlı widget\'lar var. Önce bunları silmelisiniz veya başka kategoriye taşımalısınız.',
                'type' => 'warning'
            ]);
            return;
        }
        
        if ($category->children()->count() > 0) {
            $this->dispatch('toast', [
                'title' => 'Uyarı!',
                'message' => 'Bu kategorinin alt kategorileri var. Önce alt kategorileri silmelisiniz.',
                'type' => 'warning'
            ]);
            return;
        }
        
        $category->delete();

        if (function_exists('log_activity')) {
            log_activity(
                $category,
                'silindi'
            );
        }

        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Kategori başarıyla silindi.',
            'type' => 'success'
        ]);

        $this->loadCategories();
    }
    
    public function bulkDelete()
    {
        if (empty($this->selectedItems)) {
            $this->dispatch('toast', [
                'title' => 'Uyarı!',
                'message' => 'Lütfen silinecek kategorileri seçin.',
                'type' => 'warning'
            ]);
            return;
        }
        
        $categories = WidgetCategory::whereIn('widget_category_id', $this->selectedItems)->get();
        
        foreach ($categories as $category) {
            if ($category->widgets()->count() > 0) {
                $this->dispatch('toast', [
                    'title' => 'Uyarı!',
                    'message' => "'{$category->title}' kategorisine bağlı widget'lar var. Bu kategori silinemedi.",
                    'type' => 'warning'
                ]);
                continue;
            }
            
            if ($category->children()->count() > 0) {
                $this->dispatch('toast', [
                    'title' => 'Uyarı!',
                    'message' => "'{$category->title}' kategorisinin alt kategorileri var. Bu kategori silinemedi.",
                    'type' => 'warning'
                ]);
                continue;
            }
            
            $category->delete();
            
            if (function_exists('log_activity')) {
                log_activity($category, 'toplu silme işlemi ile silindi');
            }
        }
        
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Seçili kategoriler silindi.',
            'type' => 'success'
        ]);
        
        $this->selectAll = false;
        $this->selectedItems = [];
        $this->loadCategories();
    }
    
    public function bulkToggleActive($status)
    {
        if (empty($this->selectedItems)) {
            $this->dispatch('toast', [
                'title' => 'Uyarı!',
                'message' => 'Lütfen durumu değiştirilecek kategorileri seçin.',
                'type' => 'warning'
            ]);
            return;
        }
        
        $categories = WidgetCategory::whereIn('widget_category_id', $this->selectedItems)->get();
        
        foreach ($categories as $category) {
            $category->is_active = $status;
            $category->save();
            
            if (function_exists('log_activity')) {
                log_activity(
                    $category,
                    $status ? 'toplu aktifleştirme ile aktif edildi' : 'toplu pasifleştirme ile pasif edildi'
                );
            }
        }
        
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Seçili kategoriler ' . ($status ? 'aktifleştirildi' : 'pasifleştirildi') . '.',
            'type' => 'success'
        ]);
        
        $this->selectAll = false;
        $this->selectedItems = [];
        $this->loadCategories();
    }

    public function quickAdd()
    {
        try {
            $this->validate([
                'title' => 'required|min:3|max:255'
            ]);
            
            $maxOrder = WidgetCategory::whereNull('parent_id')->max('order') ?? 0;
            
            $category = WidgetCategory::create([
                'title' => $this->title,
                'slug' => Str::slug($this->title),
                'order' => $maxOrder + 1,
                'parent_id' => $this->parentId,
                'is_active' => true,
            ]);
            
            if (!$category) {
                throw new \Exception('Kategori eklenirken bir hata oluştu.');
            }

            if (function_exists('log_activity')) {
                log_activity($category, 'oluşturuldu');
            }

            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Kategori başarıyla eklendi.',
                'type' => 'success',
            ]);
            
            $this->reset('title', 'parentId');
            $this->loadCategories();

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Lütfen form alanlarını kontrol ediniz.',
                'type' => 'error',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Kategori eklenirken bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error',
            ]);
        }
    }

    #[On('updateOrder')] 
    public function updateOrder($list)
    {
        if (!is_array($list)) {
            return;
        }

        foreach ($list as $item) {
            if (!isset($item['id'], $item['order'], $item['parentId'])) {
                continue;
            }

            $category = WidgetCategory::find($item['id']);
            if ($category) {
                $oldParentId = $category->parent_id;
                $newParentId = !empty($item['parentId']) ? $item['parentId'] : null;
                
                $category->update([
                    'order' => $item['order'],
                    'parent_id' => $newParentId
                ]);
                
                // Parent değişimi olursa log
                if ($oldParentId != $newParentId) {
                    if (function_exists('log_activity')) {
                        if ($newParentId) {
                            $parentCategory = WidgetCategory::find($newParentId);
                            $logMessage = 'kategori "' . ($parentCategory->title ?? 'Bilinmeyen') . '" altına taşındı';
                        } else {
                            $logMessage = 'ana kategori olarak taşındı';
                        }
                        
                        log_activity($category, $logMessage);
                    }
                }
            }
        }

        $this->loadCategories();
        
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Kategori sıralaması güncellendi.',
            'type' => 'success',
        ]);
    }
    
    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedItems = $this->categories->pluck('widget_category_id')->toArray();
            
            // Alt kategorileri de ekle
            foreach ($this->categories as $category) {
                if ($category->children) {
                    $this->selectedItems = array_merge(
                        $this->selectedItems, 
                        $category->children->pluck('widget_category_id')->toArray()
                    );
                }
            }
        } else {
            $this->selectedItems = [];
        }
    }
    
    public function updatedSearch()
    {
        $this->loadCategories();
    }

    public function render()
    {
        // Ana kategorileri getir (alt kategoriler için)
        $parentCategories = WidgetCategory::whereNull('parent_id')
            ->orderBy('title')
            ->get();
            
        return view('widgetmanagement::livewire.widget-category-component', [
            'parentCategories' => $parentCategories
        ]);
    }
}