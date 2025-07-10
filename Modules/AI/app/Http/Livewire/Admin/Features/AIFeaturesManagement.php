<?php

namespace Modules\AI\App\Http\Livewire\Admin\Features;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\AI\App\Models\AIFeature;
use Modules\AI\App\Models\AIFeatureCategory;
use Illuminate\Support\Facades\DB;
use App\Helpers\TenantHelpers;

class AIFeaturesManagement extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Search ve filter properties
    public $search = '';
    public $status = '';
    public $category = '';
    public $featured = '';
    public $perPage = 150;

    // Bulk action properties
    public $selectedItems = [];
    public $selectAll = false;

    // Loading states
    public $loadingToggle = [];
    public $loadingDelete = [];

    // Sorting properties
    public $sortField = 'sort_order';
    public $sortDirection = 'asc';

    protected $queryString = ['search', 'status', 'category', 'featured', 'sortField', 'sortDirection'];

    protected $listeners = [
        'itemDeleted' => 'refreshComponent',
        'statusToggled' => 'refreshComponent',
        'sortUpdated' => 'refreshComponent',
        'updateOrder' => 'updateOrder'
    ];

    // Bulk actions property
    public function getBulkActionsEnabledProperty()
    {
        return count($this->selectedItems) > 0;
    }

    public function mount()
    {
        $this->fill(request()->only(['search', 'status', 'category', 'featured']));
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function updatingFeatured()
    {
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedItems = $this->features->pluck('id')->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function toggleStatus($featureId)
    {
        $this->loadingToggle[$featureId] = true;

        try {
            $feature = AIFeature::findOrFail($featureId);
            
            if ($feature->is_system) {
                $this->addError('toggle_error', 'Sistem feature\'larının durumu değiştirilemez!');
                return;
            }

            $newStatus = $feature->status === 'active' ? 'inactive' : 'active';
            $feature->update(['status' => $newStatus]);

            $this->emit('statusToggled', $featureId, $newStatus);
            
            session()->flash('success', 'Feature durumu başarıyla güncellendi!');

        } catch (\Exception $e) {
            $this->addError('toggle_error', 'Durum güncellenirken hata oluştu: ' . $e->getMessage());
        } finally {
            $this->loadingToggle[$featureId] = false;
        }
    }

    public function deleteFeature($featureId)
    {
        $this->loadingDelete[$featureId] = true;

        try {
            $feature = AIFeature::findOrFail($featureId);
            
            if ($feature->is_system) {
                $this->addError('delete_error', 'Sistem feature\'ları silinemez!');
                return;
            }

            $feature->delete();
            
            $this->emit('itemDeleted', $featureId);
            session()->flash('success', 'Feature başarıyla silindi!');

        } catch (\Exception $e) {
            $this->addError('delete_error', 'Feature silinirken hata oluştu: ' . $e->getMessage());
        } finally {
            $this->loadingDelete[$featureId] = false;
        }
    }

    public function updateOrder($list)
    {
        if (!is_array($list)) {
            return;
        }

        try {
            DB::transaction(function () use ($list) {
                foreach ($list as $item) {
                    if (!isset($item['value'], $item['order'])) {
                        continue;
                    }

                    AIFeature::where('id', $item['value'])
                        ->update(['sort_order' => $item['order']]);
                }
            });

            // Sıralama işlemi log'u
            if (function_exists('log_activity')) {
                activity()
                    ->causedBy(auth()->user())
                    ->inLog('AIFeature')
                    ->withProperties(['feature_sayisi' => count($list)])
                    ->log('AI Feature\'ları sıralandı');
            }

            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => __('admin.order_updated'),
                'type' => 'success',
            ]);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => 'Sıralama güncellenirken hata oluştu: ' . $e->getMessage(),
                'type' => 'error',
            ]);
        }
    }

    public function bulkDelete()
    {
        if (empty($this->selectedItems)) {
            $this->addError('bulk_error', 'Silinecek öğe seçilmedi!');
            return;
        }

        try {
            // Sistem feature'larını kontrol et
            $systemFeatures = AIFeature::whereIn('id', $this->selectedItems)
                ->where('is_system', true)
                ->count();

            if ($systemFeatures > 0) {
                $this->addError('bulk_error', 'Sistem feature\'ları silinemez!');
                return;
            }

            AIFeature::whereIn('id', $this->selectedItems)->delete();
            
            $deletedCount = count($this->selectedItems);
            $this->selectedItems = [];
            $this->selectAll = false;
            
            session()->flash('success', "{$deletedCount} feature başarıyla silindi!");

        } catch (\Exception $e) {
            $this->addError('bulk_error', 'Toplu silme işleminde hata oluştu: ' . $e->getMessage());
        }
    }

    public function bulkStatusUpdate($status)
    {
        if (empty($this->selectedItems)) {
            $this->addError('bulk_error', 'Güncellenecek öğe seçilmedi!');
            return;
        }

        try {
            // Sistem feature'larını kontrol et
            $systemFeatures = AIFeature::whereIn('id', $this->selectedItems)
                ->where('is_system', true)
                ->count();

            if ($systemFeatures > 0) {
                $this->addError('bulk_error', 'Sistem feature\'larının durumu değiştirilemez!');
                return;
            }

            AIFeature::whereIn('id', $this->selectedItems)
                ->update(['status' => $status]);
            
            $updatedCount = count($this->selectedItems);
            $this->selectedItems = [];
            $this->selectAll = false;
            
            $statusText = $status === 'active' ? 'aktif' : 'pasif';
            session()->flash('success', "{$updatedCount} feature {$statusText} olarak işaretlendi!");

        } catch (\Exception $e) {
            $this->addError('bulk_error', 'Toplu güncelleme işleminde hata oluştu: ' . $e->getMessage());
        }
    }

    public function clearFilters()
    {
        $this->reset(['search', 'status', 'category', 'featured']);
        $this->resetPage();
    }

    public function toggleFeatured($featureId)
    {
        try {
            $feature = AIFeature::findOrFail($featureId);
            
            $newFeatured = !$feature->is_featured;
            $feature->update(['is_featured' => $newFeatured]);
            
            $featuredText = $newFeatured ? 'öne çıkan' : 'normal';
            session()->flash('success', "Feature {$featuredText} olarak işaretlendi!");

        } catch (\Exception $e) {
            $this->addError('featured_error', 'Öne çıkan durumu güncellenirken hata oluştu: ' . $e->getMessage());
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
        $this->resetPage();
    }

    public function refreshComponent()
    {
        // Component'i yenile
    }

    public function getFeaturesProperty()
    {
        $query = AIFeature::with('category');

        // Search
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('helper_function', 'like', '%' . $this->search . '%')
                  ->orWhereHas('category', function($sq) {
                      $sq->where('title', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Filters
        if (!empty($this->status)) {
            $query->where('status', $this->status);
        }

        if (!empty($this->category)) {
            $query->where('ai_feature_category_id', $this->category);
        }

        if ($this->featured === '1') {
            $query->where('is_featured', true);
        }

        // Sorting
        if ($this->sortField) {
            $query->orderBy($this->sortField, $this->sortDirection);
        } else {
            $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc');
        }

        return $query->paginate($this->perPage);
    }

    public function getCategoriesProperty()
    {
        try {
            return TenantHelpers::central(function() {
                return AIFeatureCategory::where('is_active', true)
                    ->orderBy('order')
                    ->pluck('title', 'ai_feature_category_id')
                    ->toArray();
            });
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getStatusesProperty()
    {
        return [
            'active' => 'Aktif',
            'inactive' => 'Pasif',
            'beta' => 'Beta',
            'planned' => 'Planlanmış'
        ];
    }

    public function render()
    {
        return view('ai::admin.features.ai-features-management', [
            'features' => $this->features,
            'categories' => $this->categories
        ])->extends('admin.layout')
            ->section('content');
    }
}