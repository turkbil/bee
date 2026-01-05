<?php

namespace Modules\Payment\App\Http\Livewire\Admin;

use Livewire\Component;
use Modules\Payment\App\Models\Payment;

class PaymentDetailComponent extends Component
{
    public Payment $payment;
    public $notes = '';

    public function mount($id)
    {
        $this->payment = Payment::with(['paymentMethod', 'payable'])->findOrFail($id);
        $this->notes = $this->payment->notes ?? '';
    }

    public function updateNotes()
    {
        $this->payment->update([
            'notes' => $this->notes,
        ]);

        session()->flash('message', 'Notlar güncellendi.');
    }

    public function render()
    {
        return view('payment::admin.payments.detail')
            ->layout('admin.layout');
    }
}
