<?php

namespace App\Http\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AITokenPackage;

#[Layout('admin.layout')]
class AITokenPackageManagementComponent extends Component
{
    use WithPagination;

    public $showForm = false;
    public $packageId = null;
    public $search = '';
    public $sortField = 'sort_order';
    public $sortDirection = 'asc';
    
    // Form fields
    public $name = '';
    public $token_amount = '';
    public $price = '';
    public $currency = 'TRY';
    public $description = '';
    public $is_active = true;
    public $is_popular = false;
    public $features = [];
    public $sort_order = 0;
    public $newFeature = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'token_amount' => 'required|integer|min:1',
        'price' => 'required|numeric|min:0',
        'currency' => 'required|string|max:3',
        'description' => 'nullable|string',
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
        'sort_order' => 'integer|min:0'
    ];

    public function render()
    {
        $packages = AITokenPackage::when($this->search, function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(20);
        
        return view('livewire.admin.ai-token-package-management-component', compact('packages'));
    }

    public function create()
    {
        $this->reset(['packageId', 'name', 'token_amount', 'price', 'currency', 'description', 'is_active', 'is_popular', 'features', 'sort_order', 'newFeature']);
        $this->currency = 'TRY';
        $this->is_active = true;
        $this->is_popular = false;
        $this->sort_order = 0;
        $this->showForm = true;
    }

    public function edit($packageId)
    {
        $package = AITokenPackage::findOrFail($packageId);
        
        $this->packageId = $package->id;
        $this->name = $package->name;
        $this->token_amount = $package->token_amount;
        $this->price = $package->price;
        $this->currency = $package->currency;
        $this->description = $package->description;
        $this->is_active = $package->is_active;
        $this->is_popular = $package->is_popular;
        $this->features = $package->features ?? [];
        $this->sort_order = $package->sort_order;
        
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'token_amount' => $this->token_amount,
            'price' => $this->price,
            'currency' => $this->currency,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'is_popular' => $this->is_popular,
            'features' => $this->features,
            'sort_order' => $this->sort_order
        ];

        if ($this->packageId) {
            AITokenPackage::findOrFail($this->packageId)->update($data);
            $this->dispatch('show-toast', [
                'message' => __('ai::admin.success.updated_successfully'),
                'type' => 'success'
            ]);
        } else {
            AITokenPackage::create($data);
            $this->dispatch('show-toast', [
                'message' => __('ai::admin.success.created_successfully'),
                'type' => 'success'
            ]);
        }

        $this->showForm = false;
        $this->resetForm();
    }

    public function delete($packageId)
    {
        AITokenPackage::findOrFail($packageId)->delete();
        $this->dispatch('show-toast', [
            'message' => __('ai::admin.success.deleted_successfully'),
            'type' => 'success'
        ]);
    }

    public function toggleActive($packageId)
    {
        try {
            $package = AITokenPackage::findOrFail($packageId);
            $package->is_active = !$package->is_active;
            $package->save();
            
            $this->dispatch('show-toast', [
                'message' => $package->is_active ? __('ai::admin.package_activated') : __('ai::admin.package_deactivated'),
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'message' => __('ai::admin.error.status_update_failed'),
                'type' => 'error'
            ]);
        }
    }

    public function addFeature()
    {
        if (!empty($this->newFeature)) {
            $this->features[] = $this->newFeature;
            $this->newFeature = '';
        }
    }

    public function removeFeature($index)
    {
        unset($this->features[$index]);
        $this->features = array_values($this->features);
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

    public function resetForm()
    {
        $this->reset(['packageId', 'name', 'token_amount', 'price', 'currency', 'description', 'is_active', 'is_popular', 'features', 'sort_order', 'newFeature']);
    }

    public function cancel()
    {
        $this->showForm = false;
        $this->resetForm();
    }
}