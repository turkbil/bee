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
    public $description = '';
    public $is_active = true;
    public $categories;
    public $editingCategoryId = null;
    public $showForm = false;
    
    // Arama için
    public $search = '';
    
    // Sıralama için
    public $sortField = 'title';
    public $sortDirection = 'asc';

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'is_active' => 'boolean',
    ];

    protected $messages = [
        'title.required' => 'Kategori başlığı zorunludur.',
        'title.max' => 'Kategori başlığı en fazla 255 karakter olmalıdır.',
        'description.max' => 'Açıklama en fazla 1000 karakter olmalıdır.',
    ];

    public function mount()
    {
        $this->loadCategories();
    }

    #[On('refreshPage')] 
    public function loadCategories()
    {
        try {
            // AI kategoriler normal tenant bağlamında çalışıyor, central kullanmıyoruz
            $query = AIFeatureCategory::query();
            
            // Arama varsa uygula
            if ($this->search) {
                $query->where('title', 'like', '%' . $this->search . '%');
            }
            
            // Sıralama
            $query->orderBy($this->sortField, $this->sortDirection);
            
            $this->categories = $query->get();
            
            // Her kategori için AI feature sayılarını manuel olarak hesapla
            foreach ($this->categories as $category) {
                $category->ai_features_count = $category->aiFeatures()->count();
            }
            
            // Debug log
            \Log::info('AI Categories loaded: ' . $this->categories->count());
            
        } catch (\Exception $e) {
            \Log::error('AI Categories load error: ' . $e->getMessage());
            session()->flash('error', 'Kategoriler yüklenirken hata oluştu: ' . $e->getMessage());
            $this->categories = collect();
        }
    }

    public function updatedSearch()
    {
        $this->loadCategories();
    }

    public function showAddForm()
    {
        $this->reset(['title', 'description', 'is_active', 'editingCategoryId']);
        $this->is_active = true;
        $this->showForm = true;
    }

    public function hideForm()
    {
        $this->reset(['title', 'description', 'is_active', 'editingCategoryId']);
        $this->showForm = false;
    }

    public function addCategory()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ], [], [
            'title' => __('ai::admin.category_name'),
            'description' => __('ai::admin.description'),
        ]);

        try {
            AIFeatureCategory::create([
                'title' => $this->title,
                'description' => $this->description ?? null,
                'is_active' => $this->is_active ?? true,
                'order' => AIFeatureCategory::max('order') + 1,
            ]);

            $this->reset(['title', 'description', 'is_active']);
            $this->showForm = false;
            $this->loadCategories();
            session()->flash('success', __('ai::admin.category_added_successfully'));
        } catch (\Exception $e) {
            session()->flash('error', 'Kategori eklenirken hata oluştu: ' . $e->getMessage());
        }
    }

    public function editCategory($categoryId)
    {
        try {
            $category = AIFeatureCategory::findOrFail($categoryId);
            $this->title = $category->title;
            $this->description = $category->description;
            $this->is_active = $category->is_active;
            $this->editingCategoryId = $categoryId;
            $this->showForm = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Kategori düzenlenirken hata oluştu: ' . $e->getMessage());
        }
    }

    public function updateCategory()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ], [], [
            'title' => __('ai::admin.category_name'),
            'description' => __('ai::admin.description'),
        ]);

        try {
            $category = AIFeatureCategory::findOrFail($this->editingCategoryId);
            $category->update([
                'title' => $this->title,
                'description' => $this->description ?? null,
                'is_active' => $this->is_active ?? true,
            ]);

            $this->reset(['title', 'description', 'is_active', 'editingCategoryId']);
            $this->showForm = false;
            $this->loadCategories();
            session()->flash('success', __('ai::admin.category_updated_successfully'));
        } catch (\Exception $e) {
            session()->flash('error', 'Kategori güncellenirken hata oluştu: ' . $e->getMessage());
        }
    }

    public function deleteCategory($categoryId)
    {
        try {
            $category = AIFeatureCategory::findOrFail($categoryId);
            
            // AI Feature'ları kontrol et
            if ($category->aiFeatures()->count() > 0) {
                throw new \Exception('Bu kategoride AI feature\'lar var. Önce onları başka kategoriye taşıyın.');
            }

            $category->delete();

            $this->loadCategories();
            session()->flash('success', 'Kategori başarıyla silindi!');
        } catch (\Exception $e) {
            session()->flash('error', 'Kategori silinirken hata oluştu: ' . $e->getMessage());
        }
    }

    public function toggleActive($categoryId)
    {
        try {
            $category = AIFeatureCategory::findOrFail($categoryId);
            $category->is_active = !$category->is_active;
            $category->save();

            $this->loadCategories();
            session()->flash('success', 'Kategori durumu güncellendi!');
        } catch (\Exception $e) {
            session()->flash('error', 'Durum güncellenirken hata oluştu: ' . $e->getMessage());
        }
    }

    public function getName()
    {
        return 'modules.ai.app.http.livewire.admin.features.ai-feature-category-component';
    }

    public function render()
    {
        return view('ai::admin.features.categories.index');
    }
}