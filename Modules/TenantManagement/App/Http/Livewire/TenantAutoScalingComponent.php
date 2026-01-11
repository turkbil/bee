<?php

namespace Modules\TenantManagement\App\Http\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Modules\TenantManagement\App\Services\RealTimeAutoScalingService;

#[Layout('admin.layout')]
class TenantAutoScalingComponent extends Component
{
    protected $autoScalingService;
    
    public $metrics = [];
    public $autoRefresh = false;
    public $refreshInterval = 5;
    public $scalingHistory = [];

    protected $listeners = [
        'refreshMetrics' => 'loadMetrics',
        'triggerScaling' => 'executeScaling'
    ];

    public function mount()
    {
        $this->autoScalingService = app(RealTimeAutoScalingService::class);
        $this->loadMetrics();
    }

    public function loadMetrics()
    {
        // Initialize service if not set
        if (!$this->autoScalingService) {
            $this->autoScalingService = app(RealTimeAutoScalingService::class);
        }

        try {
            $this->metrics = $this->autoScalingService->getRealTimeSystemMetrics();
            
            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => 'Gerçek zamanlı veriler güncellendi.',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Veriler yüklenirken hata: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function toggleAutoRefresh()
    {
        $this->autoRefresh = !$this->autoRefresh;
        
        if ($this->autoRefresh) {
            $this->dispatch('startAutoRefresh', ['interval' => $this->refreshInterval * 1000]);
        } else {
            $this->dispatch('stopAutoRefresh');
        }
    }

    public function executeScaling($action = 'auto')
    {
        // Initialize service if not set
        if (!$this->autoScalingService) {
            $this->autoScalingService = app(RealTimeAutoScalingService::class);
        }

        try {
            $operation = $this->autoScalingService->triggerAutoScaling($action);
            
            $this->dispatch('toast', [
                'title' => 'Scaling Tamamlandı',
                'message' => 'Auto-scaling işlemi başarıyla gerçekleştirildi.',
                'type' => 'success'
            ]);
            
            $this->loadMetrics();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Scaling Hatası',
                'message' => 'Auto-scaling işlemi sırasında hata: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function render()
    {
        // Initialize service if not set
        if (!$this->autoScalingService) {
            $this->autoScalingService = app(RealTimeAutoScalingService::class);
        }

        // Default metrics if empty
        if (empty($this->metrics)) {
            $this->loadMetrics();
        }

        return view('tenantmanagement::livewire.tenant-auto-scaling', [
            'metrics' => $this->metrics
        ]);
    }
}