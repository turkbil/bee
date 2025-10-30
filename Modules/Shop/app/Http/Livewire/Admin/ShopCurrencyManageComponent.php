<?php

declare(strict_types=1);

namespace Modules\Shop\App\Http\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Modules\Shop\App\Models\ShopCurrency;
use Modules\Shop\App\Services\ShopCurrencyService;

#[Layout('admin.layout')]
class ShopCurrencyManageComponent extends Component
{
    public ?int $currencyId = null;
    public string $code = '';
    public string $symbol = '';
    public string $name = '';
    public array $nameTranslations = [];
    public string $exchangeRate = '1.0000';
    public bool $isActive = true;
    public bool $isDefault = false;
    public bool $isAutoUpdate = false;
    public int $decimalPlaces = 2;
    public string $format = 'symbol_after';

    protected ShopCurrencyService $currencyService;

    protected function rules(): array
    {
        $currencyId = $this->currencyId;

        return [
            'code' => ['required', 'string', 'max:3', 'unique:shop_currencies,code,' . $currencyId . ',currency_id'],
            'symbol' => 'required|string|max:10',
            'name' => 'required|string|max:255',
            'nameTranslations.tr' => 'nullable|string|max:255',
            'nameTranslations.en' => 'nullable|string|max:255',
            'exchangeRate' => 'required|numeric|min:0.0001',
            'isActive' => 'boolean',
            'isDefault' => 'boolean',
            'isAutoUpdate' => 'boolean',
            'decimalPlaces' => 'required|integer|min:0|max:4',
            'format' => 'required|in:symbol_before,symbol_after',
        ];
    }

    public function boot(ShopCurrencyService $currencyService): void
    {
        $this->currencyService = $currencyService;
    }

    public function mount(?int $currencyId = null): void
    {
        if ($currencyId) {
            $currency = ShopCurrency::findOrFail($currencyId);
            $this->currencyId = $currency->currency_id;
            $this->code = $currency->code;
            $this->symbol = $currency->symbol;
            $this->name = $currency->name;
            $this->nameTranslations = $currency->name_translations ?? [];
            $this->exchangeRate = (string) $currency->exchange_rate;
            $this->isActive = $currency->is_active;
            $this->isDefault = $currency->is_default;
            $this->isAutoUpdate = $currency->is_auto_update;
            $this->decimalPlaces = $currency->decimal_places;
            $this->format = $currency->format;
        }
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'code' => strtoupper($this->code),
            'symbol' => $this->symbol,
            'name' => $this->name,
            'name_translations' => $this->nameTranslations,
            'exchange_rate' => (float) $this->exchangeRate,
            'is_active' => $this->isActive,
            'is_default' => $this->isDefault,
            'is_auto_update' => $this->isAutoUpdate,
            'decimal_places' => $this->decimalPlaces,
            'format' => $this->format,
        ];

        $result = $this->currencyService->saveCurrency($data, $this->currencyId);

        if ($result['success']) {
            $this->dispatch('toast', [
                'title' => __('admin.success'),
                'message' => $result['message'],
                'type' => 'success',
            ]);

            $this->redirect(route('admin.shop.currencies.index'), navigate: true);
        } else {
            $this->dispatch('toast', [
                'title' => __('admin.error'),
                'message' => $result['message'],
                'type' => 'error',
            ]);
        }
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('shop::admin.livewire.currency-manage-component');
    }
}
