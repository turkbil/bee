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

    public $selectedPayment = null;
    public $showModal = false;
    public $paymentIds = [];
    public $editingNotes = false;
    public $notes = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'gateway' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function viewPayment($paymentId)
    {
        $this->selectedPayment = Payment::with(['paymentMethod'])->find($paymentId);

        if (!$this->selectedPayment) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Ödeme bulunamadı']);
            return;
        }

        $this->notes = $this->selectedPayment->notes ?? '';
        $this->editingNotes = false;
        $this->showModal = true;
    }

    public function toggleEditNotes()
    {
        $this->editingNotes = !$this->editingNotes;
        if ($this->editingNotes) {
            $this->notes = $this->selectedPayment->notes ?? '';
        }
    }

    public function saveNotes()
    {
        if (!$this->selectedPayment) {
            return;
        }

        $this->selectedPayment->update([
            'notes' => $this->notes,
        ]);

        $this->selectedPayment->refresh();
        $this->editingNotes = false;

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Notlar kaydedildi']);
    }

    public function cancelEditNotes()
    {
        $this->editingNotes = false;
        $this->notes = $this->selectedPayment->notes ?? '';
    }

    public function canGoNext()
    {
        if (!$this->selectedPayment || empty($this->paymentIds)) {
            return false;
        }

        $currentIndex = array_search($this->selectedPayment->payment_id, $this->paymentIds);
        return $currentIndex !== false && $currentIndex < count($this->paymentIds) - 1;
    }

    public function canGoPrevious()
    {
        if (!$this->selectedPayment || empty($this->paymentIds)) {
            return false;
        }

        $currentIndex = array_search($this->selectedPayment->payment_id, $this->paymentIds);
        return $currentIndex !== false && $currentIndex > 0;
    }

    public function nextPayment()
    {
        if (!$this->selectedPayment || empty($this->paymentIds)) {
            return;
        }

        $currentIndex = array_search($this->selectedPayment->payment_id, $this->paymentIds);
        if ($currentIndex !== false && $currentIndex < count($this->paymentIds) - 1) {
            $nextId = $this->paymentIds[$currentIndex + 1];
            $this->selectedPayment = Payment::with(['paymentMethod'])->findOrFail($nextId);
        }
    }

    public function previousPayment()
    {
        if (!$this->selectedPayment || empty($this->paymentIds)) {
            return;
        }

        $currentIndex = array_search($this->selectedPayment->payment_id, $this->paymentIds);
        if ($currentIndex !== false && $currentIndex > 0) {
            $prevId = $this->paymentIds[$currentIndex - 1];
            $this->selectedPayment = Payment::with(['paymentMethod'])->findOrFail($prevId);
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedPayment = null;
    }

    public function render()
    {
        $baseQuery = Payment::query()
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
            ->orderBy('created_at', 'desc');

        // Get all payment IDs for navigation (clone query)
        $this->paymentIds = (clone $baseQuery)->pluck('payment_id')->toArray();

        // Get paginated payments
        $payments = $baseQuery->paginate($this->perPage);

        return view('payment::admin.payments.index', [
            'payments' => $payments,
        ])->layout('admin.layout');
    }
}
