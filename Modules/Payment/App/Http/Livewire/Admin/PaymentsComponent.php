<?php

namespace Modules\Payment\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Modules\Payment\App\Models\Payment;

class PaymentsComponent extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $status = '';
    public $gateway = '';
    public $perPage = 25;

    // Filtreler
    public $dateFrom = '';
    public $dateTo = '';
    public $amountMin = '';
    public $amountMax = '';

    // Beklemedeki ve başarısız ödemeleri göster (default: false - gizle)
    public $showPending = false;
    public $showFailed = false;

    public $selectedPayment = null;
    public $showModal = false;
    public $paymentIds = [];
    public $editingNotes = false;
    public $notes = '';

    // Dekont yükleme
    public $receiptFile = null;

    // Bulk selection
    public $selectedPayments = [];
    public $selectAll = false;

    // Grafik için
    public $chartViewMode = 'daily';
    public $chartDate = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'gateway' => ['except' => ''],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
        'amountMin' => ['except' => ''],
        'amountMax' => ['except' => ''],
        'showPending' => ['except' => false],
        'showFailed' => ['except' => false],
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

    /**
     * Tüm filtreleri temizle
     */
    public function clearFilters()
    {
        $this->search = '';
        $this->status = '';
        $this->gateway = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->amountMin = '';
        $this->amountMax = '';
        $this->showPending = false;
        $this->showFailed = false;
        $this->selectedPayments = [];
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
            || $this->gateway !== ''
            || $this->dateFrom !== ''
            || $this->dateTo !== ''
            || $this->amountMin !== ''
            || $this->amountMax !== ''
            || $this->showPending === true
            || $this->showFailed === true;
    }

    /**
     * Bulk selection - Tümünü seç/kaldır
     */
    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedPayments = $this->getPaymentsQuery()->pluck('payment_id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selectedPayments = [];
        }
    }

    /**
     * Toplu onayla
     */
    public function bulkApprove()
    {
        if (empty($this->selectedPayments)) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Lütfen en az bir ödeme seçin']);
            return;
        }

        $approved = 0;
        foreach ($this->selectedPayments as $paymentId) {
            $payment = Payment::with(['payable'])->find($paymentId);
            if ($payment && $payment->status === 'pending') {
                $payment->update([
                    'status' => 'completed',
                    'paid_at' => now(),
                    'notes' => ($payment->notes ? $payment->notes . "\n" : '') . '[Admin] Toplu onay: ' . now()->format('d.m.Y H:i'),
                ]);

                // Order durumunu güncelle
                $payable = $payment->payable;
                if ($payable && method_exists($payable, 'onPaymentCompleted')) {
                    $payable->onPaymentCompleted($payment);
                }
                $approved++;
            }
        }

        $this->selectedPayments = [];
        $this->selectAll = false;

        $this->dispatch('notify', ['type' => 'success', 'message' => "{$approved} ödeme onaylandı"]);
    }

    /**
     * Toplu reddet
     */
    public function bulkReject()
    {
        if (empty($this->selectedPayments)) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Lütfen en az bir ödeme seçin']);
            return;
        }

        $rejected = 0;
        foreach ($this->selectedPayments as $paymentId) {
            $payment = Payment::with(['payable'])->find($paymentId);
            if ($payment && $payment->status === 'pending') {
                $payment->update([
                    'status' => 'failed',
                    'notes' => ($payment->notes ? $payment->notes . "\n" : '') . '[Admin] Toplu red: ' . now()->format('d.m.Y H:i'),
                ]);

                // Order durumunu güncelle
                $payable = $payment->payable;
                if ($payable && method_exists($payable, 'onPaymentFailed')) {
                    $payable->onPaymentFailed($payment);
                }
                $rejected++;
            }
        }

        $this->selectedPayments = [];
        $this->selectAll = false;

        $this->dispatch('notify', ['type' => 'success', 'message' => "{$rejected} ödeme reddedildi"]);
    }

    /**
     * CSV Export
     */
    public function exportPayments()
    {
        $query = $this->getPaymentsQuery();

        $filename = 'payments_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function() use ($query) {
            $handle = fopen('php://output', 'w');

            // BOM for Excel UTF-8 support
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header
            fputcsv($handle, [
                'Ödeme No',
                'Sipariş No',
                'Müşteri',
                'Tutar',
                'Para Birimi',
                'Ödeme Yöntemi',
                'Durum',
                'Oluşturma Tarihi',
                'Ödeme Tarihi',
                'Dekont',
                'Notlar'
            ], ';');

            // Data (chunk ile bellek optimizasyonu)
            $query->with(['payable'])->chunk(100, function($payments) use ($handle) {
                foreach($payments as $payment) {
                    $orderNumber = '-';
                    $customerName = '-';

                    if ($payment->payable) {
                        $orderNumber = $payment->payable->order_number ?? '-';
                        $customerName = $payment->payable->customer_name ?? ($payment->payable->user?->name ?? '-');
                    }

                    fputcsv($handle, [
                        $payment->payment_number,
                        $orderNumber,
                        $customerName,
                        number_format($payment->amount, 2, ',', ''),
                        $payment->currency ?? 'TRY',
                        $payment->gateway ?? '-',
                        $payment->status,
                        $payment->created_at->format('Y-m-d H:i'),
                        $payment->paid_at?->format('Y-m-d H:i') ?? '-',
                        $payment->receipt_path ? 'Var' : 'Yok',
                        strip_tags($payment->notes ?? ''),
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
        $query = $this->getPaymentsQuery();

        $totalCount = (clone $query)->count();
        $totalAmount = (clone $query)->sum('amount');
        $pendingCount = (clone $query)->where('status', 'pending')->count();
        $pendingAmount = (clone $query)->where('status', 'pending')->sum('amount');
        $failedCount = (clone $query)->where('status', 'failed')->count();
        $completedCount = (clone $query)->where('status', 'completed')->count();
        $completedAmount = (clone $query)->where('status', 'completed')->sum('amount');

        return [
            'total_count' => $totalCount,
            'total_amount' => $totalAmount,
            'pending_count' => $pendingCount,
            'pending_amount' => $pendingAmount,
            'failed_count' => $failedCount,
            'completed_count' => $completedCount,
            'completed_amount' => $completedAmount,
        ];
    }

    /**
     * Base query builder (filtreler uygulanmış)
     */
    protected function getPaymentsQuery()
    {
        return Payment::query()
            ->with(['payable'])
            // Beklemedeki ve başarısız ödemeleri göster/gizle (default: gizle)
            ->when(!$this->status, function ($query) {
                $excludeStatuses = [];
                if (!$this->showPending) {
                    $excludeStatuses[] = 'pending';
                }
                if (!$this->showFailed) {
                    $excludeStatuses[] = 'failed';
                }
                if (!empty($excludeStatuses)) {
                    $query->whereNotIn('status', $excludeStatuses);
                }
            })
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
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->when($this->amountMin, function ($query) {
                $query->where('amount', '>=', (float) $this->amountMin);
            })
            ->when($this->amountMax, function ($query) {
                $query->where('amount', '<=', (float) $this->amountMax);
            })
            ->orderByRaw('paid_at IS NULL, paid_at DESC, created_at DESC'); // En son ödeme yapan önce
    }

    /**
     * Mount - Grafik tarihi default bugün
     */
    public function mount()
    {
        $this->chartDate = now()->format('Y-m-d');
    }

    /**
     * Grafik görünüm modunu değiştir
     */
    public function setChartViewMode($mode)
    {
        $this->chartViewMode = $mode;
    }

    /**
     * Önceki güne git (grafik için)
     */
    public function goToPreviousChartDay()
    {
        $this->chartDate = Carbon::parse($this->chartDate)->subDay()->format('Y-m-d');
    }

    /**
     * Sonraki güne git (grafik için)
     */
    public function goToNextChartDay()
    {
        $nextDate = Carbon::parse($this->chartDate)->addDay();
        if ($nextDate->lte(now())) {
            $this->chartDate = $nextDate->format('Y-m-d');
        }
    }

    /**
     * Bugüne git (grafik için)
     */
    public function goToChartToday()
    {
        $this->chartDate = now()->format('Y-m-d');
    }

    /**
     * Kazanç özeti kartları için istatistikler
     */
    public function getEarningCardsProperty()
    {
        $today = now()->startOfDay();
        $yesterday = now()->subDay()->startOfDay();
        $thisWeekStart = now()->startOfWeek();
        $lastWeekStart = now()->subWeek()->startOfWeek();
        $lastWeekEnd = now()->subWeek()->endOfWeek();
        $thisMonthStart = now()->startOfMonth();
        $lastMonthStart = now()->subMonth()->startOfMonth();
        $lastMonthEnd = now()->subMonth()->endOfMonth();

        // Bugünkü kazanç
        $todayAmount = Payment::where('status', 'completed')
            ->whereDate('paid_at', $today)
            ->sum('amount');

        // Dünkü kazanç
        $yesterdayAmount = Payment::where('status', 'completed')
            ->whereDate('paid_at', $yesterday)
            ->sum('amount');

        // Bu haftaki kazanç
        $thisWeekAmount = Payment::where('status', 'completed')
            ->whereBetween('paid_at', [$thisWeekStart, now()])
            ->sum('amount');

        // Geçen haftaki kazanç
        $lastWeekAmount = Payment::where('status', 'completed')
            ->whereBetween('paid_at', [$lastWeekStart, $lastWeekEnd])
            ->sum('amount');

        // Bu ayki kazanç
        $thisMonthAmount = Payment::where('status', 'completed')
            ->whereBetween('paid_at', [$thisMonthStart, now()])
            ->sum('amount');

        // Geçen ayki kazanç
        $lastMonthAmount = Payment::where('status', 'completed')
            ->whereBetween('paid_at', [$lastMonthStart, $lastMonthEnd])
            ->sum('amount');

        // Bugünkü ödeme sayısı
        $todayCount = Payment::where('status', 'completed')
            ->whereDate('paid_at', $today)
            ->count();

        // Bekleyen ödemeler (potansiyel kazanç)
        $pendingAmount = Payment::where('status', 'pending')->sum('amount');
        $pendingCount = Payment::where('status', 'pending')->count();

        return [
            'today' => [
                'amount' => $todayAmount,
                'count' => $todayCount,
                'trend' => $yesterdayAmount > 0 ? (($todayAmount - $yesterdayAmount) / $yesterdayAmount) * 100 : 0,
            ],
            'week' => [
                'amount' => $thisWeekAmount,
                'trend' => $lastWeekAmount > 0 ? (($thisWeekAmount - $lastWeekAmount) / $lastWeekAmount) * 100 : 0,
            ],
            'month' => [
                'amount' => $thisMonthAmount,
                'trend' => $lastMonthAmount > 0 ? (($thisMonthAmount - $lastMonthAmount) / $lastMonthAmount) * 100 : 0,
            ],
            'pending' => [
                'amount' => $pendingAmount,
                'count' => $pendingCount,
            ],
        ];
    }

    /**
     * Saatlik kazanç istatistikleri (seçilen gün için)
     */
    public function getHourlyEarningsProperty()
    {
        $date = Carbon::parse($this->chartDate);
        $stats = [];

        // 0-23 saat için
        for ($hour = 0; $hour < 24; $hour++) {
            $stats[$hour] = 0;
        }

        $earnings = Payment::where('status', 'completed')
            ->whereDate('paid_at', $date)
            ->select(DB::raw('HOUR(paid_at) as hour'), DB::raw('SUM(amount) as total'))
            ->groupBy('hour')
            ->pluck('total', 'hour')
            ->toArray();

        foreach ($earnings as $hour => $total) {
            $stats[(int)$hour] = (float)$total;
        }

        return $stats;
    }

    /**
     * Günlük kazanç istatistikleri (son 7 gün)
     */
    public function getDailyEarningsProperty()
    {
        $stats = [];
        $date = Carbon::parse($this->chartDate);

        for ($i = 6; $i >= 0; $i--) {
            $day = $date->copy()->subDays($i);
            $key = $day->format('Y-m-d');
            $stats[$key] = 0;
        }

        $earnings = Payment::where('status', 'completed')
            ->whereBetween('paid_at', [$date->copy()->subDays(6)->startOfDay(), $date->copy()->endOfDay()])
            ->select(DB::raw('DATE(paid_at) as date'), DB::raw('SUM(amount) as total'))
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        foreach ($earnings as $dateKey => $total) {
            if (isset($stats[$dateKey])) {
                $stats[$dateKey] = (float)$total;
            }
        }

        return $stats;
    }

    /**
     * Haftalık kazanç istatistikleri (son 4 hafta)
     */
    public function getWeeklyEarningsProperty()
    {
        $stats = [];

        for ($i = 3; $i >= 0; $i--) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekEnd = now()->subWeeks($i)->endOfWeek();

            // Son hafta için bugüne kadar
            if ($i === 0) {
                $weekEnd = now();
            }

            $total = Payment::where('status', 'completed')
                ->whereBetween('paid_at', [$weekStart, $weekEnd])
                ->sum('amount');

            $label = $weekStart->format('d') . '-' . $weekEnd->format('d M');
            $stats[$label] = (float)$total;
        }

        return $stats;
    }

    /**
     * Aylık kazanç istatistikleri (son 6 ay)
     */
    public function getMonthlyEarningsProperty()
    {
        $stats = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthStart = now()->subMonths($i)->startOfMonth();
            $monthEnd = now()->subMonths($i)->endOfMonth();

            // Bu ay için bugüne kadar
            if ($i === 0) {
                $monthEnd = now();
            }

            $total = Payment::where('status', 'completed')
                ->whereBetween('paid_at', [$monthStart, $monthEnd])
                ->sum('amount');

            $label = $monthStart->translatedFormat('M Y');
            $stats[$label] = (float)$total;
        }

        return $stats;
    }

    public function render()
    {
        $baseQuery = $this->getPaymentsQuery();

        // Get all payment IDs for navigation (clone query)
        $this->paymentIds = (clone $baseQuery)->pluck('payment_id')->toArray();

        // Get paginated payments
        $payments = $baseQuery->paginate($this->perPage);

        // İstatistikler
        $stats = $this->getStats();

        return view('payment::admin.payments.index', [
            'payments' => $payments,
            'stats' => $stats,
        ])->layout('admin.layout');
    }
}
