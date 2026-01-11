<?php

namespace Modules\Payment\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Payment\App\Models\PaymentMethod;

class PaymentMethodsComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $gateway = '';
    public $perPage = 25;

    protected $queryString = [
        'search' => ['except' => ''],
        'gateway' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleActive($id)
    {
        $method = PaymentMethod::findOrFail($id);
        $method->update(['is_active' => !$method->is_active]);

        session()->flash('message', 'Ödeme yöntemi durumu güncellendi.');
    }

    public function render()
    {
        $methods = PaymentMethod::query()
            ->when($this->search, function ($query) {
                $query->where('slug', 'like', '%' . $this->search . '%');
            })
            ->when($this->gateway, function ($query) {
                $query->where('gateway', $this->gateway);
            })
            ->orderBy('sort_order')
            ->paginate($this->perPage);

        return view('payment::admin.methods.index', [
            'methods' => $methods,
        ])->layout('admin.layout');
    }
}
