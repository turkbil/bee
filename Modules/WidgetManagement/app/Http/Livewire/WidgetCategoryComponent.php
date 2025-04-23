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
    public $maxWidgetsCount = 0;
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
        $categories = WidgetCategory::withCount('widgets')
            ->with('children')
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();

        $this->categories = $categories;
        $this->maxWidgetsCount = WidgetCategory::withCount('widgets')->max('widgets_count') ?? 1;
    }

    public function toggleExpand($categoryId)
    {
        if (isset($this->expandedCategories[$categoryId])) {
            unset($this->expandedCategories[$categoryId]);
        } else {
            $this->expandedCategories[$categoryId] = true;
        }
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

    public function quickAdd()
    {
        try {
            $this->validate();
            
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
            if (!isset($item['value'], $item['order'], $item['parent'])) {
                continue;
            }

            $category = WidgetCategory::find($item['value']);
            if ($category) {
                $category->update([
                    'order' => $item['order'],
                    'parent_id' => $item['parent'] ?: null
                ]);
            }
        }

        $this->loadCategories();
        
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Kategori sıralaması güncellendi.',
            'type' => 'success',
        ]);
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