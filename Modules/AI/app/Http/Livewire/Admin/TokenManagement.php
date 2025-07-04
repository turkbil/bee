<?php

namespace Modules\AI\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Tenant;
use Modules\AI\App\Models\AITokenPackage;
use Modules\AI\App\Models\AITokenPurchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TokenManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $selectedTenant = null;
    public $showTenantDetails = false;
    public $showModal = false;
    public $tokenAmount = '';
    public $adjustmentReason = '';
    public $currentBalance = 0;

    protected $listeners = ['tokenAdjusted' => 'refreshComponent'];

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'id'],
        'sortDirection' => ['except' => 'desc']
    ];

    public function render()
    {
        $tenants = Tenant::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('domain', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        // Her kiracı için gerçek token bilgilerini hesapla (TokenHelper kullanarak)
        foreach ($tenants as $tenant) {
            // TokenHelper üzerinden hesaplama
            $tenant->ai_tokens_used_this_month = \App\Helpers\TokenHelper::monthlyUsage($tenant);
            $tenant->real_token_balance = \App\Helpers\TokenHelper::remaining($tenant);
            $tenant->total_purchased = \App\Helpers\TokenHelper::totalPurchased($tenant);
            $tenant->total_used = \App\Helpers\TokenHelper::totalUsed($tenant);
        }

        return view('ai::admin.livewire.token-management', compact('tenants'))
            ->layout('admin.layout', [
                'pretitle' => 'AI Token Yönetimi',
                'title' => 'Token Kullanımları'
            ]);
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

    public function showTenantTokens($tenantId)
    {
        try {
            $this->selectedTenant = Tenant::find($tenantId);
            $this->showTenantDetails = true;
        } catch (\Exception $e) {
            Log::error('Tenant token detayları gösterilirken hata: ' . $e->getMessage());
            session()->flash('error', 'Tenant bilgileri yüklenemedi.');
        }
    }

    public function closeTenantDetails()
    {
        $this->selectedTenant = null;
        $this->showTenantDetails = false;
    }

    public function toggleAI($tenantId)
    {
        try {
            $tenant = Tenant::find($tenantId);
            if ($tenant) {
                // Tenant AI durumunu toggle et
                $tenant->ai_enabled = !$tenant->ai_enabled;
                $tenant->save();

                session()->flash('success', 'Tenant AI durumu güncellendi.');
            }
        } catch (\Exception $e) {
            Log::error('Tenant AI durumu güncellenirken hata: ' . $e->getMessage());
            session()->flash('error', 'AI durumu güncellenemedi.');
        }
    }

    public function openTokenModal($tenantId)
    {
        try {
            $this->selectedTenant = Tenant::find($tenantId);
            // TokenHelper ile gerçek token bakiyesini hesapla
            $this->currentBalance = \App\Helpers\TokenHelper::remaining($this->selectedTenant);
            $this->selectedTenant->total_purchased = \App\Helpers\TokenHelper::totalPurchased($this->selectedTenant);
            $this->selectedTenant->total_used = \App\Helpers\TokenHelper::totalUsed($this->selectedTenant);
            $this->tokenAmount = '';
            $this->adjustmentReason = '';
            $this->showModal = true;
        } catch (\Exception $e) {
            Log::error('Token modal açılırken hata: ' . $e->getMessage());
            session()->flash('error', 'Modal açılamadı.');
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedTenant = null;
        $this->tokenAmount = '';
        $this->adjustmentReason = '';
        $this->currentBalance = 0;
        $this->resetErrorBag();
    }

    public function adjustTokens()
    {
        $this->validate([
            'tokenAmount' => 'required|integer|not_in:0',
            'adjustmentReason' => 'required|string|min:10|max:500'
        ], [
            'tokenAmount.required' => 'Token miktarı gereklidir.',
            'tokenAmount.integer' => 'Token miktarı sayı olmalıdır.',
            'tokenAmount.not_in' => 'Token miktarı sıfır olamaz.',
            'adjustmentReason.required' => 'Açıklama gereklidir.',
            'adjustmentReason.min' => 'Açıklama en az 10 karakter olmalıdır.',
            'adjustmentReason.max' => 'Açıklama en fazla 500 karakter olmalıdır.'
        ]);

        try {
            if ($this->selectedTenant) {
                $newBalance = $this->currentBalance + $this->tokenAmount;
                
                if ($newBalance < 0) {
                    $this->addError('general', 'Yeterli token bakiyesi yok. Mevcut bakiye: ' . number_format($this->currentBalance));
                    return;
                }

                $this->selectedTenant->ai_tokens_balance = $newBalance;
                $this->selectedTenant->save();

                // Usage kaydı oluştur
                \Modules\AI\App\Models\AITokenUsage::create([
                    'tenant_id' => $this->selectedTenant->id,
                    'user_id' => auth()->id(),
                    'tokens_used' => abs($this->tokenAmount),
                    'prompt_tokens' => 0,
                    'completion_tokens' => 0,
                    'usage_type' => 'admin_adjustment',
                    'model' => 'admin',
                    'purpose' => $this->tokenAmount > 0 ? 'token_addition' : 'token_deduction',
                    'description' => $this->adjustmentReason,
                    'metadata' => json_encode([
                        'admin_id' => auth()->id(),
                        'adjustment_amount' => $this->tokenAmount,
                        'old_balance' => $this->currentBalance,
                        'new_balance' => $newBalance
                    ]),
                    'used_at' => now()
                ]);

                session()->flash('success', 'Token bakiyesi başarıyla güncellendi.');
                $this->dispatch('tokenAdjusted');
                $this->closeModal();
                $this->render();
            }
        } catch (\Exception $e) {
            Log::error('Token ayarlama hatası: ' . $e->getMessage());
            $this->addError('general', 'Token ayarlama sırasında hata oluştu.');
        }
    }

    public function refreshComponent()
    {
        // Component verilerini yenile
        $this->resetPage();
    }

    public function getTenantTokenStats($tenantId)
    {
        try {
            // Satın alınan toplam token
            $totalPurchased = AITokenPurchase::where('tenant_id', $tenantId)
                ->where('status', 'completed')
                ->sum('token_amount');

            // Kullanılan toplam token
            $totalUsed = \Modules\AI\App\Models\AITokenUsage::where('tenant_id', $tenantId)
                ->sum('tokens_used') ?? 0;

            // Kalan token (gerçek bakiye formülü)
            $remaining = max(0, $totalPurchased - $totalUsed);

            return [
                'purchased' => $totalPurchased,
                'used' => $totalUsed,
                'remaining' => $remaining
            ];
        } catch (\Exception $e) {
            Log::error('Tenant token istatistikleri hesaplanırken hata: ' . $e->getMessage());
            return [
                'purchased' => 0,
                'used' => 0,
                'remaining' => 0
            ];
        }
    }
}