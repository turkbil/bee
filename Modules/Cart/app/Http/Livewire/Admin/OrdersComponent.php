<?php

namespace Modules\Cart\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Modules\Cart\App\Models\Order;
use Modules\Cart\App\Models\OrderItem;
use App\Models\User;

#[Layout('admin.layout')]
class OrdersComponent extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $status = '';

    #[Url]
    public $paymentStatus = '';

    #[Url]
    public $perPage = 25;

    public $selectedOrder = null;
    public $showModal = false;
    public $orderIds = [];

    // Durum değiştirme
    public $newStatus = '';
    public $trackingNumber = '';
    public $adminNotes = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'paymentStatus' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingPaymentStatus()
    {
        $this->resetPage();
    }

    public function viewOrder($orderId)
    {
        $this->selectedOrder = Order::with(['items', 'user', 'payments'])
            ->find($orderId);

        if (!$this->selectedOrder) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Sipariş bulunamadı',
                'type' => 'error',
            ]);
            return;
        }

        $this->newStatus = $this->selectedOrder->status;
        $this->trackingNumber = $this->selectedOrder->tracking_number ?? '';
        $this->adminNotes = $this->selectedOrder->admin_notes ?? '';
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedOrder = null;
        $this->newStatus = '';
        $this->trackingNumber = '';
        $this->adminNotes = '';
    }

    public function updateOrderStatus()
    {
        if (!$this->selectedOrder) {
            return;
        }

        $updateData = [
            'admin_notes' => $this->adminNotes,
        ];

        // Durum değişti mi?
        if ($this->newStatus !== $this->selectedOrder->status) {
            $updateData['status'] = $this->newStatus;

            // Duruma göre timestamp güncelle
            switch ($this->newStatus) {
                case 'processing':
                    $updateData['confirmed_at'] = now();
                    break;
                case 'shipped':
                    $updateData['shipped_at'] = now();
                    if ($this->trackingNumber) {
                        $updateData['tracking_number'] = $this->trackingNumber;
                    }
                    break;
                case 'delivered':
                    $updateData['delivered_at'] = now();
                    break;
                case 'completed':
                    $updateData['completed_at'] = now();
                    break;
                case 'cancelled':
                    $updateData['cancelled_at'] = now();
                    break;
            }
        }

        // Kargo takip no güncelle
        if ($this->trackingNumber !== ($this->selectedOrder->tracking_number ?? '')) {
            $updateData['tracking_number'] = $this->trackingNumber;
        }

        $this->selectedOrder->update($updateData);
        $this->selectedOrder->refresh();

        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Sipariş güncellendi',
            'type' => 'success',
        ]);
    }

    public function markAsPaid()
    {
        if (!$this->selectedOrder) {
            return;
        }

        $this->selectedOrder->update([
            'payment_status' => 'paid',
            'paid_amount' => $this->selectedOrder->total_amount,
        ]);
        $this->selectedOrder->refresh();

        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Sipariş ödendi olarak işaretlendi',
            'type' => 'success',
        ]);
    }

    public function canGoNext()
    {
        if (!$this->selectedOrder || empty($this->orderIds)) {
            return false;
        }

        $currentIndex = array_search($this->selectedOrder->order_id, $this->orderIds);
        return $currentIndex !== false && $currentIndex < count($this->orderIds) - 1;
    }

    public function canGoPrevious()
    {
        if (!$this->selectedOrder || empty($this->orderIds)) {
            return false;
        }

        $currentIndex = array_search($this->selectedOrder->order_id, $this->orderIds);
        return $currentIndex !== false && $currentIndex > 0;
    }

    public function nextOrder()
    {
        if (!$this->canGoNext()) {
            return;
        }

        $currentIndex = array_search($this->selectedOrder->order_id, $this->orderIds);
        $nextId = $this->orderIds[$currentIndex + 1];
        $this->viewOrder($nextId);
    }

    public function previousOrder()
    {
        if (!$this->canGoPrevious()) {
            return;
        }

        $currentIndex = array_search($this->selectedOrder->order_id, $this->orderIds);
        $previousId = $this->orderIds[$currentIndex - 1];
        $this->viewOrder($previousId);
    }

    public function render()
    {
        $query = Order::query()
            ->with(['items', 'user'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('order_number', 'like', '%' . $this->search . '%')
                        ->orWhere('customer_name', 'like', '%' . $this->search . '%')
                        ->orWhere('customer_email', 'like', '%' . $this->search . '%')
                        ->orWhere('customer_phone', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->paymentStatus, function ($query) {
                $query->where('payment_status', $this->paymentStatus);
            })
            ->orderBy('created_at', 'desc');

        $orders = $query->paginate($this->perPage);

        // Store order IDs for navigation
        $this->orderIds = (clone $query)->pluck('order_id')->toArray();

        // Statuses for filter
        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'completed', 'cancelled', 'payment_failed'];
        $paymentStatuses = ['pending', 'paid', 'failed', 'refunded'];

        return view('cart::livewire.admin.orders-component', [
            'orders' => $orders,
            'statuses' => $statuses,
            'paymentStatuses' => $paymentStatuses,
        ]);
    }
}
