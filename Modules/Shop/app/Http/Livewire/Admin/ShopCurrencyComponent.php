<?php

declare(strict_types=1);

namespace Modules\Shop\App\Http\Livewire\Admin;

use Livewire\Attributes\{Layout, Url};
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Shop\App\Http\Livewire\Traits\WithBulkActions;
use Modules\Shop\App\Models\ShopCurrency;
use Modules\Shop\App\Services\ShopCurrencyService;
use Modules\Shop\App\Services\TcmbExchangeRateService;

#[Layout('admin.layout')]
class ShopCurrencyComponent extends Component
{
    use WithPagination;
    use WithBulkActions;

    #[Url]
    public string $search = '';

    #[Url]
    public ?int $perPage = null;

    #[Url]
    public string $sortField = 'currency_id';

    #[Url]
    public string $sortDirection = 'asc';

    protected ShopCurrencyService $currencyService;

    public function boot(ShopCurrencyService $currencyService): void
    {
        $this->currencyService = $currencyService;
        $this->perPage ??= (int) config('modules.pagination.admin_per_page', 15);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->perPage = max(1, (int) $this->perPage);
        $this->resetPage();
    }

    protected function getModelClass(): string
    {
        return ShopCurrency::class;
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleActive(int $currencyId): void
    {
        $result = $this->currencyService->toggleCurrencyStatus($currencyId);

        $this->dispatch('toast', [
            'title' => $result['success'] ? __('admin.success') : __('admin.error'),
            'message' => $result['message'],
            'type' => $result['type'] ?? ($result['success'] ? 'success' : 'error'),
        ]);
    }

    public function setAsDefault(int $currencyId): void
    {
        $result = $this->currencyService->setAsDefault($currencyId);

        $this->dispatch('toast', [
            'title' => $result['success'] ? __('admin.success') : __('admin.error'),
            'message' => $result['message'],
            'type' => $result['type'] ?? ($result['success'] ? 'success' : 'error'),
        ]);
    }

    public function bulkDeleteSelected(): void
    {
        if (empty($this->selectedItems)) {
            $this->dispatch('toast', [
                'title' => __('admin.warning'),
                'message' => 'Please select records first',
                'type' => 'warning',
            ]);

            return;
        }

        $deleted = $this->currencyService->bulkDeleteCurrencies(array_map('intval', $this->selectedItems));

        $this->dispatch('toast', [
            'title' => __('admin.success'),
            'message' => "$deleted currencies deleted successfully",
            'type' => 'success',
        ]);

        $this->refreshSelectedItems();
        $this->resetPage();
    }

    /**
     * TCMB'den kurları otomatik güncelle (sadece is_auto_update=true olanlar)
     */
    public function updateFromTCMB(): void
    {
        $tcmbService = app(TcmbExchangeRateService::class);
        $result = $tcmbService->fetchRates();

        if (!$result['success']) {
            $this->dispatch('toast', [
                'title' => 'TCMB Hata',
                'message' => $result['message'],
                'type' => 'error',
            ]);

            return;
        }

        $updatedCount = 0;
        $skippedCount = 0;
        $tcmbRates = $result['rates'];

        // SADECE otomatik güncelleme aktif olanları çek
        $currencies = ShopCurrency::whereIn('code', array_keys($tcmbRates))
            ->where('is_auto_update', true)
            ->get();

        // Manuel kurları kontrol et
        $manualCurrencies = ShopCurrency::whereIn('code', array_keys($tcmbRates))
            ->where('is_auto_update', false)
            ->get();

        foreach ($currencies as $currency) {
            if (isset($tcmbRates[$currency->code])) {
                $oldRate = $currency->exchange_rate;
                $newRate = $tcmbRates[$currency->code];

                $currency->exchange_rate = $newRate;
                $currency->last_updated_at = now();
                $currency->save();

                $updatedCount++;

                \Log::info("Currency auto-updated from TCMB: {$currency->code}", [
                    'old_rate' => $oldRate,
                    'new_rate' => $newRate,
                    'updated_at' => $currency->last_updated_at,
                ]);
            }
        }

        $skippedCount = $manualCurrencies->count();

        $message = "$updatedCount para birimi güncellendi";
        if ($skippedCount > 0) {
            $message .= " ($skippedCount manuel kur korundu)";
        }
        $message .= ". (USD: ₺" . number_format($tcmbRates['USD'] ?? 0, 2) . ")";

        $this->dispatch('toast', [
            'title' => '✅ TCMB Güncellendi',
            'message' => $message,
            'type' => 'success',
        ]);
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $filters = [
            'search' => $this->search,
            'is_active' => null,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
        ];

        $currencies = $this->currencyService->getPaginatedCurrencies($filters, (int) $this->perPage);

        return view('shop::admin.livewire.currency-component', [
            'currencies' => $currencies,
        ]);
    }
}
