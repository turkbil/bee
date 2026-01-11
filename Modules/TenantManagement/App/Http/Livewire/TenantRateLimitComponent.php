<?php

namespace Modules\TenantManagement\App\Http\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\TenantManagement\App\Models\TenantRateLimit;
use Modules\TenantManagement\App\Services\TenantRateLimitService;
use App\Models\Tenant;

#[Layout('admin.layout')]
class TenantRateLimitComponent extends Component
{
    use WithPagination;

    public $selectedTenantId = null;
    public $selectedTenant = null;
    public $selectedMethod = '';
    public $selectedStrategy = '';
    public $search = '';
    public $statusFilter = 'all';
    
    // Form fields
    public $editingRuleId = null;
    public $isEditing = false;
    public $endpoint_pattern = '*';
    public $method = '*';
    public $requests_per_minute = 60;
    public $requests_per_hour = 1000;
    public $requests_per_day = 10000;
    public $burst_limit = 10;
    public $concurrent_requests = 5;
    public $ip_whitelist = [];
    public $ip_blacklist = [];
    public $throttle_strategy = 'sliding_window';
    public $penalty_duration = 60;
    public $penalty_action = 'delay';
    public $is_active = true;
    public $log_violations = true;
    public $priority = 5;
    public $description = '';

    // IP Management
    public $newWhitelistIp = '';
    public $newBlacklistIp = '';

    // Bulk operations
    public $selectedRules = [];
    public $selectAll = false;
    public $bulkAction = '';

    // New rule form data
    public $newRule = [
        'tenant_id' => null,
        'endpoint_pattern' => '*',
        'http_method' => '*',
        'rate_limit_strategy' => 'sliding_window',
        'max_requests' => 60,
        'window_minutes' => 1,
        'priority' => 50,
        'whitelist_ips' => '',
        'blacklist_ips' => '',
        'is_active' => true
    ];

    // Testing
    public $testUrl = '';
    public $testMethod = 'GET';
    public $testIp = '';
    public $testResults = [];

    // Statistics
    public $rateLimitStats = [];
    public $showStats = false;
    public $statsTimeRange = 24;

    protected $listeners = [
        'tenantSelected' => 'selectTenant',
        'refreshRules' => '$refresh',
        'testCompleted' => 'loadStats'
    ];

    protected $rules = [
        'endpoint_pattern' => 'required|string|max:255',
        'method' => 'required|string|in:*,GET,POST,PUT,PATCH,DELETE,HEAD,OPTIONS',
        'requests_per_minute' => 'required|integer|min:1',
        'requests_per_hour' => 'required|integer|min:1',
        'requests_per_day' => 'required|integer|min:1',
        'burst_limit' => 'required|integer|min:1',
        'concurrent_requests' => 'required|integer|min:1',
        'throttle_strategy' => 'required|in:fixed_window,sliding_window,token_bucket',
        'penalty_duration' => 'required|integer|min:1',
        'penalty_action' => 'required|in:block,delay,queue,warn',
        'is_active' => 'boolean',
        'log_violations' => 'boolean',
        'priority' => 'required|integer|min:0|max:100',
        'description' => 'nullable|string|max:500'
    ];

    protected $queryString = [
        'selectedTenantId' => ['except' => null],
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
        $this->selectedRules = [];
        $this->selectAll = false;
        $this->loadStats();
    }

    public function clearTenantSelection()
    {
        $this->selectedTenantId = null;
        $this->resetPage();
        $this->rateLimitStats = [];
        $this->showStats = false;
    }

    public function updatedSearch()
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
            $this->selectedRules = $this->rateLimitRules->pluck('id')->toArray();
        } else {
            $this->selectedRules = [];
        }
    }

    public function updatedStatsTimeRange()
    {
        $this->loadStats();
    }

    public function newRule()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->dispatch('showModal', ['id' => 'modal-rule-form']);
    }

    public function editRule($id)
    {
        $rule = TenantRateLimit::findOrFail($id);
        
        $this->editingRuleId = $rule->id;
        $this->endpoint_pattern = $rule->endpoint_pattern;
        $this->method = $rule->method;
        $this->requests_per_minute = $rule->requests_per_minute;
        $this->requests_per_hour = $rule->requests_per_hour;
        $this->requests_per_day = $rule->requests_per_day;
        $this->burst_limit = $rule->burst_limit;
        $this->concurrent_requests = $rule->concurrent_requests;
        $this->ip_whitelist = $rule->ip_whitelist ?? [];
        $this->ip_blacklist = $rule->ip_blacklist ?? [];
        $this->throttle_strategy = $rule->throttle_strategy;
        $this->penalty_duration = $rule->penalty_duration;
        $this->penalty_action = $rule->penalty_action;
        $this->is_active = $rule->is_active;
        $this->log_violations = $rule->log_violations;
        $this->priority = $rule->priority;
        $this->description = $rule->description;
        
        $this->isEditing = true;
        $this->dispatch('showModal', ['id' => 'modal-rule-form']);
    }

    public function saveRule()
    {
        $this->validate();

        try {
            $data = [
                'tenant_id' => $this->selectedTenantId,
                'endpoint_pattern' => $this->endpoint_pattern,
                'method' => $this->method,
                'requests_per_minute' => $this->requests_per_minute,
                'requests_per_hour' => $this->requests_per_hour,
                'requests_per_day' => $this->requests_per_day,
                'burst_limit' => $this->burst_limit,
                'concurrent_requests' => $this->concurrent_requests,
                'ip_whitelist' => !empty($this->ip_whitelist) ? $this->ip_whitelist : null,
                'ip_blacklist' => !empty($this->ip_blacklist) ? $this->ip_blacklist : null,
                'throttle_strategy' => $this->throttle_strategy,
                'penalty_duration' => $this->penalty_duration,
                'penalty_action' => $this->penalty_action,
                'is_active' => $this->is_active,
                'log_violations' => $this->log_violations,
                'priority' => $this->priority,
                'description' => $this->description ?: null,
            ];

            if ($this->isEditing) {
                $rule = TenantRateLimit::findOrFail($this->editingRuleId);
                $rule->update($data);
                $message = 'Rate limit kuralı güncellendi.';
            } else {
                TenantRateLimit::create($data);
                $message = 'Rate limit kuralı oluşturuldu.';
            }

            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => $message,
                'type' => 'success'
            ]);

            $this->dispatch('hideModal', ['id' => 'modal-rule-form']);
            $this->resetForm();
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Kural kaydedilirken hata: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function deleteRule($id)
    {
        try {
            TenantRateLimit::findOrFail($id)->delete();
            
            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => 'Rate limit kuralı silindi.',
                'type' => 'success'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Kural silinirken hata: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function toggleRuleStatus($id)
    {
        try {
            $rule = TenantRateLimit::findOrFail($id);
            $rule->update(['is_active' => !$rule->is_active]);
            
            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => 'Kural durumu güncellendi.',
                'type' => 'success'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Kural durumu güncellenirken hata: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function addWhitelistIp()
    {
        if (empty($this->newWhitelistIp)) {
            return;
        }

        if (!filter_var($this->newWhitelistIp, FILTER_VALIDATE_IP)) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Geçersiz IP adresi.',
                'type' => 'error'
            ]);
            return;
        }

        if (!in_array($this->newWhitelistIp, $this->ip_whitelist)) {
            $this->ip_whitelist[] = $this->newWhitelistIp;
        }

        $this->newWhitelistIp = '';
    }

    public function removeWhitelistIp($index)
    {
        unset($this->ip_whitelist[$index]);
        $this->ip_whitelist = array_values($this->ip_whitelist);
    }

    public function addBlacklistIp()
    {
        if (empty($this->newBlacklistIp)) {
            return;
        }

        if (!filter_var($this->newBlacklistIp, FILTER_VALIDATE_IP)) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Geçersiz IP adresi.',
                'type' => 'error'
            ]);
            return;
        }

        if (!in_array($this->newBlacklistIp, $this->ip_blacklist)) {
            $this->ip_blacklist[] = $this->newBlacklistIp;
        }

        $this->newBlacklistIp = '';
    }

    public function removeBlacklistIp($index)
    {
        unset($this->ip_blacklist[$index]);
        $this->ip_blacklist = array_values($this->ip_blacklist);
    }

    public function testRateLimit()
    {
        if (!$this->selectedTenantId || empty($this->testUrl)) {
            return;
        }

        try {
            $rateLimitService = app(TenantRateLimitService::class);
            $testIp = $this->testIp ?: request()->ip();
            
            $result = $rateLimitService->checkRateLimit(
                $this->selectedTenantId,
                $testIp,
                $this->testUrl,
                $this->testMethod
            );

            $this->testResults = [
                'allowed' => $result['allowed'],
                'test_ip' => $testIp,
                'test_url' => $this->testUrl,
                'test_method' => $this->testMethod,
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'details' => $result
            ];

            if (!$result['allowed']) {
                $this->dispatch('toast', [
                    'title' => 'Test Sonucu',
                    'message' => 'İstek rate limit tarafından engellendi.',
                    'type' => 'warning'
                ]);
            } else {
                $this->dispatch('toast', [
                    'title' => 'Test Sonucu',
                    'message' => 'İstek rate limit kontrolünden geçti.',
                    'type' => 'success'
                ]);
            }

            $this->dispatch('showModal', ['id' => 'modal-test-results']);
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Test Hatası',
                'message' => 'Rate limit testi sırasında hata: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function clearCache($tenantId = null)
    {
        try {
            $rateLimitService = app(TenantRateLimitService::class);
            $rateLimitService->clearRateLimitCache($tenantId ?: $this->selectedTenantId);
            
            $this->dispatch('toast', [
                'title' => 'Cache Temizlendi',
                'message' => 'Rate limit cache temizlendi.',
                'type' => 'success'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Cache temizleme hatası: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function loadStats()
    {
        if (!$this->selectedTenantId) {
            return;
        }

        try {
            $rateLimitService = app(TenantRateLimitService::class);
            $this->rateLimitStats = $rateLimitService->getRateLimitStats($this->selectedTenantId, $this->statsTimeRange);
            $this->showStats = true;
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'İstatistikler yüklenirken hata: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function executeBulkAction()
    {
        if (empty($this->selectedRules) || !$this->bulkAction) {
            return;
        }

        try {
            $count = count($this->selectedRules);
            
            switch ($this->bulkAction) {
                case 'activate':
                    TenantRateLimit::whereIn('id', $this->selectedRules)
                        ->update(['is_active' => true]);
                    $message = "{$count} kural aktifleştirildi.";
                    break;
                    
                case 'deactivate':
                    TenantRateLimit::whereIn('id', $this->selectedRules)
                        ->update(['is_active' => false]);
                    $message = "{$count} kural devre dışı bırakıldı.";
                    break;
                    
                case 'enable_logging':
                    TenantRateLimit::whereIn('id', $this->selectedRules)
                        ->update(['log_violations' => true]);
                    $message = "{$count} kural için loglama aktifleştirildi.";
                    break;
                    
                case 'disable_logging':
                    TenantRateLimit::whereIn('id', $this->selectedRules)
                        ->update(['log_violations' => false]);
                    $message = "{$count} kural için loglama devre dışı bırakıldı.";
                    break;
                    
                case 'delete':
                    TenantRateLimit::whereIn('id', $this->selectedRules)->delete();
                    $message = "{$count} kural silindi.";
                    break;
                    
                default:
                    throw new \Exception('Geçersiz bulk aksiyon');
            }

            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => $message,
                'type' => 'success'
            ]);

            $this->selectedRules = [];
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

    public function createDefaultRules($tenantId)
    {
        try {
            TenantRateLimit::createDefaultRulesForTenant($tenantId);
            
            $this->dispatch('toast', [
                'title' => 'Başarılı',
                'message' => 'Varsayılan rate limit kuralları oluşturuldu.',
                'type' => 'success'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Varsayılan kurallar oluşturulurken hata: ' . $e->getMessage(),
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

    public function getRateLimitRulesProperty()
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

        $query = TenantRateLimit::where('tenant_id', $this->selectedTenantId);
        
        if ($this->statusFilter !== 'all') {
            $query->where('is_active', $this->statusFilter === 'active');
        }
        
        return $query->orderBy('priority', 'desc')
                    ->orderBy('endpoint_pattern')
                    ->paginate(20);
    }

    private function resetForm()
    {
        $this->editingRuleId = null;
        $this->isEditing = false;
        $this->endpoint_pattern = '*';
        $this->method = '*';
        $this->requests_per_minute = 60;
        $this->requests_per_hour = 1000;
        $this->requests_per_day = 10000;
        $this->burst_limit = 10;
        $this->concurrent_requests = 5;
        $this->ip_whitelist = [];
        $this->ip_blacklist = [];
        $this->throttle_strategy = 'sliding_window';
        $this->penalty_duration = 60;
        $this->penalty_action = 'delay';
        $this->is_active = true;
        $this->log_violations = true;
        $this->priority = 5;
        $this->description = '';
        $this->newWhitelistIp = '';
        $this->newBlacklistIp = '';
    }

    public function render()
    {
        return view('tenantmanagement::livewire.tenantratelimits', [
            'tenants' => $this->tenants,
            'rateLimits' => $this->rateLimitRules,
            'httpMethods' => TenantRateLimit::getHttpMethods(),
            'throttleStrategies' => TenantRateLimit::getThrottleStrategies(),
            'penaltyActions' => TenantRateLimit::getPenaltyActions(),
        ]);
    }
}