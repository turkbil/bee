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
    public $slug = '';
    public $description = '';
    public $icon = '';
    public $is_active = true;
    public $parentId = null;
    public $categories = [];
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
    
    // Sıralama için
    public $sortField = 'order';
    public $sortDirection = 'asc';

    protected $rules = [
        'title' => 'required|min:3|max:255',
        'slug' => 'nullable|regex:/^[a-z0-9\-_]+$/i|max:255',
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
        try {
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
                $categories = $query->whereNull('parent_id')
                    ->with(['children' => function ($q) {
                        $q->orderBy('order');
                    }])
                    ->get();
                    
                // Her kategori için widget sayılarını manuel olarak hesapla
                foreach ($categories as $category) {
                    $category->widgets_count = $category->widgets()->count();
                    
                    if ($category->children) {
                        foreach ($category->children as $child) {
                            $child->widgets_count = $child->widgets()->count();
                        }
                    }
                }
            } else {
                // Arama varsa tüm kategorileri getir, parent_id filtrelemesi yapma
                $categories = $query->get();
                
                // Her kategori için widget sayılarını manuel olarak hesapla
                foreach ($categories as $category) {
                    $category->widgets_count = $category->widgets()->count();
                }
            }

            $this->categories = $categories;
        } catch (\Exception $e) {
            // Hata durumunda konsola log
            logger()->error('WidgetCategoryComponent loadCategories hatası: ' . $e->getMessage());
        }
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

        try {
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
        } catch (\Exception $e) {
            // Hata durumunda konsola log
            logger()->error('WidgetCategoryComponent saveEdit hatası: ' . $e->getMessage());
            
            $this->dispatch('toast', [
                'title' => 'Hata!', 
                'message' => 'Kategori güncellenirken bir hata oluştu.',
                'type' => 'error'
            ]);
        }
    }

    public function toggleActive($id)
    {
        try {
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
        } catch (\Exception $e) {
            // Hata durumunda konsola log
            logger()->error('WidgetCategoryComponent toggleActive hatası: ' . $e->getMessage());
            
            $this->dispatch('toast', [
                'title' => 'Hata!', 
                'message' => 'Kategori durumu değiştirilirken bir hata oluştu.',
                'type' => 'error'
            ]);
        }
    }

    public function delete($id)
    {
        try {
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
        } catch (\Exception $e) {
            // Hata durumunda konsola log
            logger()->error('WidgetCategoryComponent delete hatası: ' . $e->getMessage());
            
            $this->dispatch('toast', [
                'title' => 'Hata!', 
                'message' => 'Kategori silinirken bir hata oluştu.',
                'type' => 'error'
            ]);
        }
    }

    public function quickAdd()
    {
        try {
            $this->validate([
                'title' => 'required|min:3|max:255',
                'slug' => 'nullable|regex:/^[a-z0-9\-_]+$/i|max:255',
            ]);
            
            $maxOrder = WidgetCategory::whereNull('parent_id')->max('order') ?? 0;
            
            // Slug boşsa otomatik oluştur
            if (empty($this->slug)) {
                $this->slug = Str::slug($this->title);
            }
            
            $category = WidgetCategory::create([
                'title' => $this->title,
                'slug' => $this->slug,
                'description' => $this->description,
                'icon' => $this->icon,
                'order' => $maxOrder + 1,
                'parent_id' => $this->parentId,
                'is_active' => $this->is_active,
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
            
            $this->reset(['title', 'slug', 'description', 'icon', 'parentId', 'is_active']);
            $this->is_active = true; // Varsayılan olarak aktif
            $this->loadCategories();

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validasyon hatası
            logger()->error('WidgetCategoryComponent quickAdd validasyon hatası: ' . json_encode($e->errors()));
            
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Lütfen form alanlarını kontrol ediniz.',
                'type' => 'error',
            ]);
        } catch (\Exception $e) {
            // Genel hata
            logger()->error('WidgetCategoryComponent quickAdd hatası: ' . $e->getMessage());
            
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Kategori eklenirken bir hata oluştu.',
                'type' => 'error',
            ]);
        }
    }

    #[On('updateOrder')] 
    public function updateOrder($list)
    {
        if (!is_array($list)) {
            logger()->error('WidgetCategoryComponent updateOrder: Liste dizi değil');
            return;
        }
    
        try {
            // Loglama için
            logger()->info('Sıralama güncellemesi: ' . json_encode($list));
            
            foreach ($list as $item) {
                // Eski kontrolü kaldırıyoruz, bunun yerine her bir alanı ayrı kontrol edeceğiz
                if (!isset($item['id'])) {
                    logger()->warning('updateOrder: ID eksik - ' . json_encode($item));
                    continue;
                }
                
                if (!isset($item['order'])) {
                    $item['order'] = 0; // Varsayılan değer
                    logger()->warning('updateOrder: Order değeri eksik - varsayılan kullanılıyor');
                }
                
                // parentId null olabilir, bu yüzden bu kontrolü kaldırıyoruz
    
                $category = WidgetCategory::find($item['id']);
                if ($category) {
                    $oldParentId = $category->parent_id;
                    $newParentId = isset($item['parentId']) && !empty($item['parentId']) ? $item['parentId'] : null;
                    
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
                } else {
                    logger()->warning('updateOrder: Kategori bulunamadı - ID: ' . $item['id']);
                }
            }
    
            $this->loadCategories();
            
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Kategori sıralaması güncellendi.',
                'type' => 'success',
            ]);
        } catch (\Exception $e) {
            // Hata durumunda konsola log
            logger()->error('WidgetCategoryComponent updateOrder hatası: ' . $e->getMessage());
            
            $this->dispatch('toast', [
                'title' => 'Hata!', 
                'message' => 'Kategori sıralaması güncellenirken bir hata oluştu.',
                'type' => 'error'
            ]);
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