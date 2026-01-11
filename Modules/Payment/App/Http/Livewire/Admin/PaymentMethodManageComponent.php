<?php

namespace Modules\Payment\App\Http\Livewire\Admin;

use Livewire\Component;
use Modules\Payment\App\Models\PaymentMethod;

class PaymentMethodManageComponent extends Component
{
    public $paymentMethodId;
    public $title = [];
    public $slug;
    public $description = [];
    public $gateway = 'paytr';
    public $gateway_mode = 'test';
    public $gateway_config = [];
    public $supported_currencies = ['TRY'];
    public $supports_installment = false;
    public $max_installments = 1;
    public $fixed_fee = 0;
    public $percentage_fee = 0;
    public $is_active = true;
    public $sort_order = 0;

    protected $rules = [
        'title' => 'required|array',
        'slug' => 'required|string|max:255',
        'gateway' => 'required|in:paytr,stripe,iyzico,paypal,manual',
        'gateway_mode' => 'required|in:test,live',
        'supported_currencies' => 'required|array',
        'is_active' => 'boolean',
    ];

    public function mount($id = null)
    {
        if ($id) {
            $method = PaymentMethod::findOrFail($id);
            $this->paymentMethodId = $method->payment_method_id;
            $this->title = $method->title ?? [];
            $this->slug = $method->slug;
            $this->description = $method->description ?? [];
            $this->gateway = $method->gateway;
            $this->gateway_mode = $method->gateway_mode;
            $this->gateway_config = $method->gateway_config ?? [];
            $this->supported_currencies = $method->supported_currencies ?? ['TRY'];
            $this->supports_installment = $method->supports_installment;
            $this->max_installments = $method->max_installments;
            $this->fixed_fee = $method->fixed_fee;
            $this->percentage_fee = $method->percentage_fee;
            $this->is_active = $method->is_active;
            $this->sort_order = $method->sort_order;
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'gateway' => $this->gateway,
            'gateway_mode' => $this->gateway_mode,
            'gateway_config' => $this->gateway_config,
            'supported_currencies' => $this->supported_currencies,
            'supports_installment' => $this->supports_installment,
            'max_installments' => $this->max_installments,
            'fixed_fee' => $this->fixed_fee,
            'percentage_fee' => $this->percentage_fee,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
        ];

        if ($this->paymentMethodId) {
            PaymentMethod::find($this->paymentMethodId)->update($data);
            session()->flash('message', 'Ödeme yöntemi güncellendi.');
        } else {
            PaymentMethod::create($data);
            session()->flash('message', 'Ödeme yöntemi oluşturuldu.');
        }

        return redirect()->route('admin.payment.methods.index');
    }

    public function render()
    {
        return view('payment::admin.methods.manage')
            ->layout('admin.layout');
    }
}
