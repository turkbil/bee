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

    // Yeni filtreler
    #[Url]
    public $dateFrom = '';

    #[Url]
    public $dateTo = '';

    #[Url]
    public $amountMin = '';

    #[Url]
    public $amountMax = '';

    #[Url]
    public $paymentMethod = '';

    // Durum filtreleri (default: sadece ödenenler gösterilir)
    #[Url]
    public $showPaid = true;      // Ödenenler (varsayılan: göster)

    #[Url]
    public $showPending = false;  // Bekleyenler (varsayılan: gizle)

    #[Url]
    public $showFailed = false;   // Başarısızlar (varsayılan: gizle)

    public $selectedOrder = null;
    public $showModal = false;
    public $orderIds = [];

    // Durum değiştirme
    public $newStatus = '';
    public $trackingNumber = '';
    public $adminNotes = '';

    // Bulk selection
    public $selectedOrders = [];
    public $selectAll = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'paymentStatus' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'amountMin' => ['except' => ''],
        'amountMax' => ['except' => ''],
        'paymentMethod' => ['except' => ''],
        'showPaid' => ['except' => true],
        'showPending' => ['except' => false],
        'showFailed' => ['except' => false],
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

        // Order model'deki markAsPaid() metodu subscription'ları da aktifleştirir!
        $this->selectedOrder->markAsPaid();
        $this->selectedOrder->refresh();

        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Sipariş ödendi olarak işaretlendi ve abonelikler aktifleştirildi',
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

    /**
     * Tüm filtreleri temizle
     */
    public function clearFilters()
    {
        $this->search = '';
        $this->status = '';
        $this->paymentStatus = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->amountMin = '';
        $this->amountMax = '';
        $this->paymentMethod = '';
        $this->showPaid = true;  // varsayılan
        $this->showPending = false;
        $this->showFailed = false;
        $this->selectedOrders = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    /**
     * Filtre aktif mi kontrol et
     */
    public function hasActiveFilters(): bool
    {
        return $this->search !== ''
            || $this->status !== ''
            || $this->paymentStatus !== ''
            || $this->dateFrom !== ''
            || $this->dateTo !== ''
            || $this->amountMin !== ''
            || $this->amountMax !== ''
            || $this->paymentMethod !== ''
            || $this->showPaid === false  // varsayılandan farklıysa
            || $this->showPending === true
            || $this->showFailed === true;
    }

    /**
     * Bulk selection - Tümünü seç/kaldır
     */
    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedOrders = $this->getOrdersQuery()->pluck('order_id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedOrders = [];
        }
    }

    /**
     * Toplu ödendi işaretle
     */
    public function bulkMarkAsPaid()
    {
        if (empty($this->selectedOrders)) {
            $this->dispatch('toast', ['title' => 'Uyarı', 'message' => 'Lütfen en az bir sipariş seçin', 'type' => 'warning']);
            return;
        }

        $marked = 0;
        foreach ($this->selectedOrders as $orderId) {
            $order = Order::find($orderId);
            if ($order && $order->payment_status === 'pending') {
                $order->markAsPaid();
                $marked++;
            }
        }

        $this->selectedOrders = [];
        $this->selectAll = false;

        $this->dispatch('toast', ['title' => 'Başarılı', 'message' => "{$marked} sipariş ödendi olarak işaretlendi", 'type' => 'success']);
    }

    /**
     * Toplu durum değiştir
     */
    public function bulkChangeStatus($newStatus)
    {
        if (empty($this->selectedOrders)) {
            $this->dispatch('toast', ['title' => 'Uyarı', 'message' => 'Lütfen en az bir sipariş seçin', 'type' => 'warning']);
            return;
        }

        Order::whereIn('order_id', $this->selectedOrders)->update(['status' => $newStatus]);

        $this->selectedOrders = [];
        $this->selectAll = false;

        $this->dispatch('toast', ['title' => 'Başarılı', 'message' => 'Durum güncellendi', 'type' => 'success']);
    }

    /**
     * CSV Export
     */
    public function exportOrders()
    {
        $query = $this->getOrdersQuery();

        $filename = 'orders_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function() use ($query) {
            $handle = fopen('php://output', 'w');

            // BOM for Excel UTF-8 support
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header
            fputcsv($handle, [
                'Sipariş No',
                'Müşteri',
                'Email',
                'Telefon',
                'Ürün Sayısı',
                'Ara Toplam',
                'KDV',
                'İndirim',
                'Toplam',
                'Ödeme Yöntemi',
                'Sipariş Durumu',
                'Ödeme Durumu',
                'Tarih'
            ], ';');

            // Data (chunk ile bellek optimizasyonu)
            $query->with(['items', 'payments'])->chunk(100, function($orders) use ($handle) {
                foreach($orders as $order) {
                    $paymentMethod = $order->payments->first()?->gateway ?? '-';

                    fputcsv($handle, [
                        $order->order_number,
                        $order->customer_name,
                        $order->customer_email,
                        $order->customer_phone,
                        $order->items->count(),
                        number_format($order->subtotal, 2, ',', ''),
                        number_format($order->tax_amount ?? 0, 2, ',', ''),
                        number_format($order->discount_amount ?? 0, 2, ',', ''),
                        number_format($order->total_amount, 2, ',', ''),
                        $paymentMethod,
                        $order->status,
                        $order->payment_status,
                        $order->created_at->format('Y-m-d H:i'),
                    ], ';');
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * İstatistikleri hesapla
     */
    public function getStats()
    {
        $query = $this->getOrdersQuery();

        $totalCount = (clone $query)->count();
        $totalAmount = (clone $query)->sum('total_amount');
        $paidCount = (clone $query)->where('payment_status', 'paid')->count();
        $paidAmount = (clone $query)->where('payment_status', 'paid')->sum('total_amount');
        $pendingCount = (clone $query)->where('payment_status', 'pending')->count();
        $pendingAmount = (clone $query)->where('payment_status', 'pending')->sum('total_amount');

        return [
            'total_count' => $totalCount,
            'total_amount' => $totalAmount,
            'paid_count' => $paidCount,
            'paid_amount' => $paidAmount,
            'pending_count' => $pendingCount,
            'pending_amount' => $pendingAmount,
        ];
    }

    /**
     * Base query builder (filtreler uygulanmış)
     */
    protected function getOrdersQuery()
    {
        return Order::query()
            ->with(['items', 'user', 'payments'])
            // Durum filtreleri: hangi ödeme durumları gösterilsin?
            ->when(!$this->paymentStatus, function ($query) {
                $includeStatuses = [];
                if ($this->showPaid) {
                    $includeStatuses[] = 'paid';
                }
                if ($this->showPending) {
                    $includeStatuses[] = 'pending';
                }
                if ($this->showFailed) {
                    $includeStatuses[] = 'failed';
                }
                // Hiçbiri seçili değilse hepsini göster
                if (!empty($includeStatuses)) {
                    $query->whereIn('payment_status', $includeStatuses);
                }
            })
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
            // Tarih filtreleri
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            // Tutar filtreleri
            ->when($this->amountMin, function ($query) {
                $query->where('total_amount', '>=', (float) $this->amountMin);
            })
            ->when($this->amountMax, function ($query) {
                $query->where('total_amount', '<=', (float) $this->amountMax);
            })
            // Ödeme yöntemi filtresi
            ->when($this->paymentMethod, function ($query) {
                $query->whereHas('payments', function ($q) {
                    $q->where('gateway', $this->paymentMethod);
                });
            })
            ->orderBy('created_at', 'desc');
    }

    public function render()
    {
        $query = $this->getOrdersQuery();

        $orders = $query->paginate($this->perPage);

        // Store order IDs for navigation
        $this->orderIds = (clone $query)->pluck('order_id')->toArray();

        // Statuses for filter
        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'completed', 'cancelled', 'payment_failed'];
        $paymentStatuses = ['pending', 'paid', 'failed', 'refunded'];

        // İstatistikler
        $stats = $this->getStats();

        return view('cart::livewire.admin.orders-component', [
            'orders' => $orders,
            'statuses' => $statuses,
            'paymentStatuses' => $paymentStatuses,
            'stats' => $stats,
        ]);
    }
}
