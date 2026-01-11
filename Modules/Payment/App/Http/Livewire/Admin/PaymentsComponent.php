<?php

namespace Modules\Payment\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Modules\Payment\App\Models\Payment;

class PaymentsComponent extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $status = '';
    public $gateway = '';
    public $perPage = 25;

    public $selectedPayment = null;
    public $showModal = false;
    public $paymentIds = [];
    public $editingNotes = false;
    public $notes = '';

    // Dekont yükleme
    public $receiptFile = null;

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

    /**
     * Ödemeyi manuel olarak "Ödendi" olarak işaretle (Havale/EFT için)
     */
    public function markAsCompleted($paymentId)
    {
        $payment = Payment::with(['payable'])->find($paymentId);

        if (!$payment) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Ödeme bulunamadı']);
            return;
        }

        if ($payment->status === 'completed') {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Ödeme zaten tamamlanmış']);
            return;
        }

        // Payment durumunu güncelle
        $payment->update([
            'status' => 'completed',
            'paid_at' => now(),
            'notes' => ($payment->notes ? $payment->notes . "\n" : '') . '[Admin] Manuel onay: ' . now()->format('d.m.Y H:i'),
        ]);

        // Order durumunu güncelle (eğer varsa)
        $payable = $payment->payable;
        if ($payable && method_exists($payable, 'onPaymentCompleted')) {
            $payable->onPaymentCompleted($payment);
        }

        // Modal'daki payment'ı güncelle
        if ($this->selectedPayment && $this->selectedPayment->payment_id === $paymentId) {
            $this->selectedPayment = $payment->fresh(['payable']);
        }

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Ödeme onaylandı!']);
    }

    /**
     * Ödemeyi "Başarısız" olarak işaretle
     */
    public function markAsFailed($paymentId)
    {
        $payment = Payment::with(['payable'])->find($paymentId);

        if (!$payment) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Ödeme bulunamadı']);
            return;
        }

        if ($payment->status === 'failed') {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Ödeme zaten başarısız']);
            return;
        }

        // Payment durumunu güncelle
        $payment->update([
            'status' => 'failed',
            'notes' => ($payment->notes ? $payment->notes . "\n" : '') . '[Admin] Manuel red: ' . now()->format('d.m.Y H:i'),
        ]);

        // Order durumunu güncelle (eğer varsa)
        $payable = $payment->payable;
        if ($payable && method_exists($payable, 'onPaymentFailed')) {
            $payable->onPaymentFailed($payment);
        }

        // Modal'daki payment'ı güncelle
        if ($this->selectedPayment && $this->selectedPayment->payment_id === $paymentId) {
            $this->selectedPayment = $payment->fresh(['payable']);
        }

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Ödeme reddedildi']);
    }

    /**
     * Dekont dosyası yükle
     */
    public function uploadReceipt()
    {
        if (!$this->selectedPayment) {
            return;
        }

        $this->validate([
            'receiptFile' => 'required|file|mimes:jpg,jpeg,png,pdf,webp|max:5120', // 5MB max
        ], [
            'receiptFile.required' => 'Dosya seçiniz',
            'receiptFile.mimes' => 'Sadece JPG, PNG, PDF veya WebP dosyaları yüklenebilir',
            'receiptFile.max' => 'Dosya boyutu en fazla 5MB olabilir',
        ]);

        // Eski dekont varsa sil
        if ($this->selectedPayment->receipt_path) {
            Storage::disk('public')->delete($this->selectedPayment->receipt_path);
        }

        // Yeni dosyayı kaydet
        $path = $this->receiptFile->store('receipts/' . date('Y/m'), 'public');

        $this->selectedPayment->update([
            'receipt_path' => $path,
            'receipt_uploaded_at' => now(),
            'notes' => ($this->selectedPayment->notes ? $this->selectedPayment->notes . "\n" : '') . '[Admin] Dekont yüklendi: ' . now()->format('d.m.Y H:i'),
        ]);

        $this->selectedPayment->refresh();
        $this->receiptFile = null;

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Dekont başarıyla yüklendi']);
    }

    /**
     * Dekont dosyasını sil
     */
    public function deleteReceipt()
    {
        if (!$this->selectedPayment || !$this->selectedPayment->receipt_path) {
            return;
        }

        Storage::disk('public')->delete($this->selectedPayment->receipt_path);

        $this->selectedPayment->update([
            'receipt_path' => null,
            'receipt_uploaded_at' => null,
            'notes' => ($this->selectedPayment->notes ? $this->selectedPayment->notes . "\n" : '') . '[Admin] Dekont silindi: ' . now()->format('d.m.Y H:i'),
        ]);

        $this->selectedPayment->refresh();

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Dekont silindi']);
    }

    public function render()
    {
        $baseQuery = Payment::query()
            ->with(['payable']) // Sipariş bilgisi için
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('payment_number', 'like', '%' . $this->search . '%')
                      ->orWhere('gateway_transaction_id', 'like', '%' . $this->search . '%');
                });
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
