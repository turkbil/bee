<?php

namespace Modules\AI\App\Http\Livewire\Admin\Features;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Illuminate\Support\Str;
use Modules\AI\App\Models\AIFeatureCategory;
use App\Helpers\TenantHelpers;

#[Layout('admin.layout')]
class AIFeatureCategoryComponent extends Component
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
            TenantHelpers::central(function() {
                // Tüm kategorileri hiyerarşik yapıda yükle
                $query = AIFeatureCategory::query();
                
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
                        
                    // Her kategori için AI feature sayılarını manuel olarak hesapla
                    foreach ($categories as $category) {
                        $category->ai_features_count = $category->aiFeatures()->count();
                        
                        if ($category->children) {
                            foreach ($category->children as $child) {
                                $child->ai_features_count = $child->aiFeatures()->count();
                            }
                        }
                    }
                } else {
                    // Arama varsa tüm kategorileri getir, parent_id filtrelemesi yapma
                    $categories = $query->get();
                    
                    foreach ($categories as $category) {
                        $category->ai_features_count = $category->aiFeatures()->count();
                    }
                }
                
                $this->categories = $categories;
            });
        } catch (\Exception $e) {
            session()->flash('error', 'Kategoriler yüklenirken hata oluştu: ' . $e->getMessage());
            $this->categories = collect();
        }
    }

    public function updatedSearch()
    {
        $this->loadCategories();
    }

    public function quickAdd()
    {
        $this->validate([
            'title' => 'required|min:3|max:255',
            'slug' => 'nullable|regex:/^[a-z0-9\-_]+$/i|max:255',
        ]);

        try {
            TenantHelpers::central(function() {
                // Slug otomatik oluştur
                if (empty($this->slug)) {
                    $this->slug = Str::slug($this->title);
                }

                // Son sıra numarasını al
                $lastOrder = AIFeatureCategory::max('order') ?? 0;

                AIFeatureCategory::create([
                    'title' => $this->title,
                    'slug' => $this->slug,
                    'description' => $this->description,
                    'icon' => $this->icon ?: 'fas fa-folder',
                    'is_active' => $this->is_active,
                    'parent_id' => $this->parentId,
                    'order' => $lastOrder + 1,
                ]);
            });

            // Formu temizle
            $this->reset(['title', 'slug', 'description', 'icon', 'parentId']);
            $this->is_active = true;

            $this->loadCategories();
            session()->flash('success', 'Kategori başarıyla eklendi!');
        } catch (\Exception $e) {
            session()->flash('error', 'Kategori eklenirken hata oluştu: ' . $e->getMessage());
        }
    }

    public function startEdit($categoryId)
    {
        try {
            TenantHelpers::central(function() use ($categoryId) {
                $category = AIFeatureCategory::find($categoryId);
                if ($category) {
                    $this->editCategoryId = $categoryId;
                    $this->editData = [
                        'title' => $category->title,
                        'slug' => $category->slug,
                        'description' => $category->description,
                        'icon' => $category->icon,
                        'parent_id' => $category->parent_id,
                        'is_active' => $category->is_active,
                    ];
                }
            });
        } catch (\Exception $e) {
            session()->flash('error', 'Kategori düzenlenirken hata oluştu: ' . $e->getMessage());
        }
    }

    public function saveEdit()
    {
        $this->validate([
            'editData.title' => 'required|min:3|max:255',
            'editData.slug' => 'nullable|regex:/^[a-z0-9\-_]+$/i|max:255',
            'editData.description' => 'nullable|max:1000',
            'editData.icon' => 'nullable|max:50',
            'editData.is_active' => 'boolean',
        ]);

        try {
            TenantHelpers::central(function() {
                $category = AIFeatureCategory::find($this->editCategoryId);
                if ($category) {
                    // Slug otomatik oluştur
                    if (empty($this->editData['slug'])) {
                        $this->editData['slug'] = Str::slug($this->editData['title']);
                    }

                    $category->update($this->editData);
                }
            });

            $this->cancelEdit();
            $this->loadCategories();
            session()->flash('success', 'Kategori başarıyla güncellendi!');
        } catch (\Exception $e) {
            session()->flash('error', 'Kategori güncellenirken hata oluştu: ' . $e->getMessage());
        }
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

    public function toggleActive($categoryId)
    {
        try {
            TenantHelpers::central(function() use ($categoryId) {
                $category = AIFeatureCategory::find($categoryId);
                if ($category) {
                    $category->is_active = !$category->is_active;
                    $category->save();
                }
            });

            $this->loadCategories();
            session()->flash('success', 'Kategori durumu güncellendi!');
        } catch (\Exception $e) {
            session()->flash('error', 'Durum güncellenirken hata oluştu: ' . $e->getMessage());
        }
    }

    public function delete($categoryId)
    {
        try {
            TenantHelpers::central(function() use ($categoryId) {
                $category = AIFeatureCategory::find($categoryId);
                if ($category) {
                    // Alt kategorileri kontrol et
                    if ($category->children()->count() > 0) {
                        throw new \Exception('Bu kategorinin alt kategorileri var. Önce onları silin.');
                    }

                    // AI Feature'ları kontrol et
                    if ($category->aiFeatures()->count() > 0) {
                        throw new \Exception('Bu kategoride AI feature\'lar var. Önce onları başka kategoriye taşıyın.');
                    }

                    $category->delete();
                }
            });

            $this->loadCategories();
            session()->flash('success', 'Kategori başarıyla silindi!');
        } catch (\Exception $e) {
            session()->flash('error', 'Kategori silinirken hata oluştu: ' . $e->getMessage());
        }
    }

    #[On('updateOrder')]
    public function updateOrder($orderedIds)
    {
        try {
            TenantHelpers::central(function() use ($orderedIds) {
                foreach ($orderedIds as $index => $id) {
                    AIFeatureCategory::where('ai_feature_category_id', $id)->update(['order' => $index + 1]);
                }
            });

            $this->loadCategories();
        } catch (\Exception $e) {
            session()->flash('error', 'Sıralama güncellenirken hata oluştu: ' . $e->getMessage());
        }
    }

    public function getParentCategoriesProperty()
    {
        try {
            return TenantHelpers::central(function() {
                return AIFeatureCategory::whereNull('parent_id')
                    ->where('is_active', true)
                    ->orderBy('title')
                    ->get();
            });
        } catch (\Exception $e) {
            return collect();
        }
    }

    public function render()
    {
        return view('ai::admin.features.categories.index');
    }
}