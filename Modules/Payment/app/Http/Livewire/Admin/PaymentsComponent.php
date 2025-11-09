<?php

namespace Modules\Payment\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Payment\App\Models\Payment;

class PaymentsComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $gateway = '';
    public $perPage = 25;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'gateway' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $payments = Payment::query()
            ->with(['payable'])
            ->when($this->search, function ($query) {
                $query->where('payment_number', 'like', '%' . $this->search . '%')
                      ->orWhere('gateway_transaction_id', 'like', '%' . $this->search . '%');
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->gateway, function ($query) {
                $query->where('gateway', $this->gateway);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('payment::admin.payments.index', [
            'payments' => $payments,
        ])->layout('admin.layout');
    }
}
