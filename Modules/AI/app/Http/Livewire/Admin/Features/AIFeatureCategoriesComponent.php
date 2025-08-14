<?php
namespace Modules\AI\App\Http\Livewire\Admin\Features;

use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\AI\App\Models\AIFeatureCategory;

#[Layout('admin.layout')]
class AIFeatureCategoriesComponent extends Component
{
    #[Url]
    public $search = '';

    #[Url]
    public $sortField = 'order';

    #[Url]
    public $sortDirection = 'asc';

    public $categories = [];

    protected $queryString = [
        'sortField' => ['except' => 'order'],
        'sortDirection' => ['except' => 'asc'],
        'search' => ['except' => ''],
    ];

    public function mount()
    {
        $this->loadCategories();
    }

    #[On('refreshPage')] 
    public function loadCategories()
    {
        $this->categories = AIFeatureCategory::orderBy('order')
            ->get();
    }

    public function updatedSearch()
    {
        // Search değiştiğinde herhangi bir işlem yapılabilir
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleActive($id)
    {
        $category = AIFeatureCategory::where('ai_feature_category_id', $id)->first();
    
        if ($category) {
            $category->update(['is_active' => !$category->is_active]);
            
            log_activity(
                $category,
                $category->is_active ? 'aktif edildi' : 'pasif edildi',
                ['is_active' => $category->is_active]
            );
    
            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('admin.item_status_changed', ['title' => $category->title, 'status' => $category->is_active ? __('admin.active') : __('admin.inactive')]),
                'type' => 'success',
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
            if (!isset($item['value'], $item['order'])) {
                continue;
            }

            AIFeatureCategory::where('ai_feature_category_id', $item['value'])
                ->update(['order' => $item['order']]);
        }

        // Sıralama işlemi log'u
        if (function_exists('log_activity')) {
            activity()
                ->causedBy(auth()->user())
                ->inLog('AIFeatureCategory')
                ->withProperties(['kategori_sayisi' => count($list)])
                ->log('AI kategorileri sıralandı');
        }

        $this->loadCategories();
        
        $this->dispatch('toast', [
            'title' => __('admin.success'),
            'message' => __('admin.order_updated'),
            'type' => 'success',
        ]);
    }

    public function render()
    {
        // Eğer search varsa filtrelenmiş kategorileri göster, yoksa sortable için loadCategories'den alınanları göster
        if ($this->search) {
            $categories = AIFeatureCategory::query()
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('title', 'like', '%' . $this->search . '%')
                            ->orWhere('slug', 'like', '%' . $this->search . '%')
                            ->orWhere('description', 'like', '%' . $this->search . '%');
                    });
                })
                ->orderBy($this->sortField, $this->sortDirection)
                ->get();
        } else {
            $categories = $this->categories;
        }

        return view('ai::admin.livewire.features.ai-feature-categories-component', [
            'categories' => $categories,
        ]);
    }
}