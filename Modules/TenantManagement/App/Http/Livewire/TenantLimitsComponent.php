<?php

namespace Modules\TenantManagement\App\Http\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\TenantManagement\App\Models\TenantResourceLimit;
use App\Models\Tenant;

#[Layout('admin.layout')]
class TenantLimitsComponent extends Component
{
    use WithPagination;

    public $selectedTenantId = null;
    public $selectedTenant = null;
    public $selectedResourceType = '';
    public $search = '';
    public $resourceTypeFilter = 'all';
    public $statusFilter = 'all';
    
    // Form fields
    public $editingLimitId = null;
    public $isEditing = false;
    public $resource_type = '';
    public $hourly_limit = '';
    public $daily_limit = '';
    public $monthly_limit = '';
    public $concurrent_limit = '';
    public $storage_limit_mb = '';
    public $memory_limit_mb = '';
    public $cpu_limit_percent = '';
    public $connection_limit = '';
    public $is_active = true;
    public $enforce_limit = true;
    public $limit_action = 'throttle';
    public $description = '';

    // Bulk operations
    public $selectedLimits = [];
    public $selectAll = false;
    public $bulkAction = '';
    public $bulkData = [
        'hourly_limit' => '',
        'daily_limit' => '',
        'monthly_limit' => '',
        'limit_action' => 'throttle'
    ];

    // Presets
    public $showPresets = false;
    public $selectedPreset = '';

    protected $listeners = [
        'tenantSelected' => 'selectTenant',
        'refreshLimits' => '$refresh',
        'bulkActionCompleted' => '$refresh'
    ];

    protected $rules = [
        'resource_type' => 'required|string',
        'hourly_limit' => 'nullable|integer|min:0',
        'daily_limit' => 'nullable|integer|min:0',
        'monthly_limit' => 'nullable|integer|min:0',
        'concurrent_limit' => 'nullable|integer|min:0',
        'storage_limit_mb' => 'nullable|integer|min:0',
        'memory_limit_mb' => 'nullable|integer|min:0',
        'cpu_limit_percent' => 'nullable|numeric|min:0|max:100',
        'connection_limit' => 'nullable|integer|min:0',
        'is_active' => 'boolean',
        'enforce_limit' => 'boolean',
        'limit_action' => 'required|in:block,throttle,warn,queue',
        'description' => 'nullable|string|max:255'
    ];

    protected $queryString = [
        'selectedTenantId' => ['except' => null],
        'resourceTypeFilter' => ['except' => 'all'],
        'statusFilter' => ['except' => 'all']
    ];

    public function mount()
    {
        $this->resetForm();
    }

    public function selectTenant($tenantId)
    {
        $this->selectedTenantId = $tenantId;
        $this->resetPage();
        $this->selectedLimits = [];
        $this->selectAll = false;
    }

    public function clearTenantSelection()
    {
        $this->selectedTenantId = null;
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedResourceTypeFilter()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedLimits = $this->resourceLimits->pluck('id')->toArray();
        } else {
            $this->selectedLimits = [];
        }
    }

    public function newLimit()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->dispatch('showModal', ['id' => 'modal-limit-form']);
    }

    public function editLimit($id)
    {
        $limit = TenantResourceLimit::findOrFail($id);
        
        $this->editingLimitId = $limit->id;
        $this->resource_type = $limit->resource_type;
        $this->hourly_limit = $limit->hourly_limit;
        $this->daily_limit = $limit->daily_limit;
        $this->monthly_limit = $limit->monthly_limit;
        $this->concurrent_limit = $limit->concurrent_limit;
        $this->storage_limit_mb = $limit->storage_limit_mb;
        $this->memory_limit_mb = $limit->memory_limit_mb;
        $this->cpu_limit_percent = $limit->cpu_limit_percent;
        $this->connection_limit = $limit->connection_limit;
        $this->is_active = $limit->is_active;
        $this->enforce_limit = $limit->enforce_limit;
        $this->limit_action = $limit->limit_action;
        $this->description = $limit->description;
        
        $this->isEditing = true;
        $this->dispatch('showModal', ['id' => 'modal-limit-form']);
    }

    public function saveLimit()
    {
        $this->validate();

        try {
            $data = [
                'tenant_id' => $this->selectedTenantId,
                'resource_type' => $this->resource_type,
                'hourly_limit' => $this->hourly_limit ?: null,
                'daily_limit' => $this->daily_limit ?: null,
                'monthly_limit' => $this->monthly_limit ?: null,
                'concurrent_limit' => $this->concurrent_limit ?: null,
                'storage_limit_mb' => $this->storage_limit_mb ?: null,
                'memory_limit_mb' => $this->memory_limit_mb ?: null,
                'cpu_limit_percent' => $this->cpu_limit_percent ?: null,
                'connection_limit' => $this->connection_limit ?: null,
                'is_active' => $this->is_active,
                'enforce_limit' => $this->enforce_limit,
                'limit_action' => $this->limit_action,
                'description' => $this->description ?: null,
            ];

            if ($this->isEditing) {
                $limit = TenantResourceLimit::findOrFail($this->editingLimitId);
                $limit->update($data);
                $message = 'Kaynak limiti güncellendi.';
            } else {
                // Check for existing limit for this tenant and resource type
                $existing = TenantResourceLimit::where('tenant_id', $this->selectedTenantId)
                    ->where('resource_type', $this->resource_type)
                    ->first();

                if ($existing) {
                    $existing->update($data);
                    $message = 'Mevcut kaynak limiti güncellendi.';
                } else {
                    TenantResourceLimit::create($data);
                    $message = 'Kaynak limiti oluşturuldu.';
                }
            }

            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => $message,
                'type' => 'success'
            ]);

            $this->dispatch('hideModal', ['id' => 'modal-limit-form']);
            $this->resetForm();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Kaynak limiti kaydedilirken hata: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function deleteLimit($id)
    {
        try {
            TenantResourceLimit::findOrFail($id)->delete();
            
            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => 'Kaynak limiti silindi.',
                'type' => 'success'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Kaynak limiti silinirken hata: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function toggleLimitStatus($id)
    {
        try {
            $limit = TenantResourceLimit::findOrFail($id);
            $limit->update(['is_active' => !$limit->is_active]);
            
            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => 'Limit durumu güncellendi.',
                'type' => 'success'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Limit durumu güncellenirken hata: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function showPresets()
    {
        $this->showPresets = true;
        $this->dispatch('showModal', ['id' => 'modal-presets']);
    }

    public function applyPreset()
    {
        if (!$this->selectedTenantId || !$this->selectedPreset) {
            return;
        }

        try {
            $presets = $this->getPresetConfigurations();
            
            if (!isset($presets[$this->selectedPreset])) {
                throw new \Exception('Geçersiz preset seçimi');
            }

            $presetData = $presets[$this->selectedPreset];
            
            foreach ($presetData['limits'] as $resourceType => $limits) {
                TenantResourceLimit::updateOrCreate(
                    [
                        'tenant_id' => $this->selectedTenantId,
                        'resource_type' => $resourceType
                    ],
                    array_merge($limits, [
                        'is_active' => true,
                        'enforce_limit' => true,
                        'limit_action' => 'throttle',
                        'description' => $presetData['name'] . ' preset ile oluşturuldu'
                    ])
                );
            }

            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => $presetData['name'] . ' preset başarıyla uygulandı.',
                'type' => 'success'
            ]);

            $this->dispatch('hideModal', ['id' => 'modal-presets']);
            $this->showPresets = false;
            $this->selectedPreset = '';
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Preset uygulanırken hata: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function executeBulkAction()
    {
        if (empty($this->selectedLimits) || !$this->bulkAction) {
            return;
        }

        try {
            $count = count($this->selectedLimits);
            
            switch ($this->bulkAction) {
                case 'activate':
                    TenantResourceLimit::whereIn('id', $this->selectedLimits)
                        ->update(['is_active' => true]);
                    $message = "{$count} limit aktifleştirildi.";
                    break;
                    
                case 'deactivate':
                    TenantResourceLimit::whereIn('id', $this->selectedLimits)
                        ->update(['is_active' => false]);
                    $message = "{$count} limit devre dışı bırakıldı.";
                    break;
                    
                case 'enforce':
                    TenantResourceLimit::whereIn('id', $this->selectedLimits)
                        ->update(['enforce_limit' => true]);
                    $message = "{$count} limit zorunlu hale getirildi.";
                    break;
                    
                case 'unforce':
                    TenantResourceLimit::whereIn('id', $this->selectedLimits)
                        ->update(['enforce_limit' => false]);
                    $message = "{$count} limit zorunlu olmaktan çıkarıldı.";
                    break;
                    
                case 'delete':
                    TenantResourceLimit::whereIn('id', $this->selectedLimits)->delete();
                    $message = "{$count} limit silindi.";
                    break;
                    
                default:
                    throw new \Exception('Geçersiz bulk aksiyon');
            }

            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => $message,
                'type' => 'success'
            ]);

            $this->selectedLimits = [];
            $this->selectAll = false;
            $this->bulkAction = '';
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Toplu işlem sırasında hata: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function createDefaultLimits($tenantId)
    {
        try {
            TenantResourceLimit::createDefaultLimitsForTenant($tenantId);
            
            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => 'Varsayılan limitler oluşturuldu.',
                'type' => 'success'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Varsayılan limitler oluşturulurken hata: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function getTenantsProperty()
    {
        $query = Tenant::query();
        
        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('id', 'like', '%' . $this->search . '%');
        }
        
        return $query->orderBy('title')->paginate(20);
    }

    public function getResourceLimitsProperty()
    {
        if (!$this->selectedTenantId) {
            return new \Illuminate\Pagination\LengthAwarePaginator(
                collect(), 
                0, 
                20, 
                1, 
                ['path' => request()->url(), 'pageName' => 'page']
            );
        }

        $query = TenantResourceLimit::where('tenant_id', $this->selectedTenantId);
        
        if ($this->resourceTypeFilter !== 'all') {
            $query->where('resource_type', $this->resourceTypeFilter);
        }
        
        if ($this->statusFilter !== 'all') {
            $query->where('is_active', $this->statusFilter === 'active');
        }
        
        return $query->orderBy('resource_type')->paginate(20);
    }

    private function getPresetConfigurations(): array
    {
        return [
            'basic' => [
                'name' => 'Temel Paket',
                'description' => 'Küçük projeler için temel limitler',
                'limits' => [
                    'api' => ['hourly_limit' => 500, 'daily_limit' => 5000, 'monthly_limit' => 50000],
                    'database' => ['hourly_limit' => 1000, 'daily_limit' => 10000, 'monthly_limit' => 100000],
                    'cache' => ['memory_limit_mb' => 128, 'concurrent_limit' => 50],
                    'storage' => ['storage_limit_mb' => 512, 'monthly_limit' => 2048],
                    'ai' => ['hourly_limit' => 1000, 'daily_limit' => 5000, 'monthly_limit' => 25000]
                ]
            ],
            'professional' => [
                'name' => 'Profesyonel Paket',
                'description' => 'Orta ölçekli projeler için gelişmiş limitler',
                'limits' => [
                    'api' => ['hourly_limit' => 2000, 'daily_limit' => 20000, 'monthly_limit' => 200000],
                    'database' => ['hourly_limit' => 5000, 'daily_limit' => 50000, 'monthly_limit' => 500000],
                    'cache' => ['memory_limit_mb' => 512, 'concurrent_limit' => 200],
                    'storage' => ['storage_limit_mb' => 2048, 'monthly_limit' => 10240],
                    'ai' => ['hourly_limit' => 5000, 'daily_limit' => 25000, 'monthly_limit' => 100000]
                ]
            ],
            'enterprise' => [
                'name' => 'Kurumsal Paket',
                'description' => 'Büyük ölçekli projeler için yüksek limitler',
                'limits' => [
                    'api' => ['hourly_limit' => 10000, 'daily_limit' => 100000, 'monthly_limit' => 1000000],
                    'database' => ['hourly_limit' => 25000, 'daily_limit' => 250000, 'monthly_limit' => 2500000],
                    'cache' => ['memory_limit_mb' => 2048, 'concurrent_limit' => 1000],
                    'storage' => ['storage_limit_mb' => 10240, 'monthly_limit' => 51200],
                    'ai' => ['hourly_limit' => 25000, 'daily_limit' => 100000, 'monthly_limit' => 500000]
                ]
            ]
        ];
    }

    private function resetForm()
    {
        $this->editingLimitId = null;
        $this->isEditing = false;
        $this->resource_type = '';
        $this->hourly_limit = '';
        $this->daily_limit = '';
        $this->monthly_limit = '';
        $this->concurrent_limit = '';
        $this->storage_limit_mb = '';
        $this->memory_limit_mb = '';
        $this->cpu_limit_percent = '';
        $this->connection_limit = '';
        $this->is_active = true;
        $this->enforce_limit = true;
        $this->limit_action = 'throttle';
        $this->description = '';
    }

    public function render()
    {
        return view('tenantmanagement::livewire.tenantlimits', [
            'tenants' => $this->tenants,
            'limits' => $this->resourceLimits,
            'resourceTypes' => TenantResourceLimit::getResourceTypes(),
            'limitActions' => TenantResourceLimit::getLimitActions(),
            'presets' => $this->getPresetConfigurations(),
        ]);
    }
}