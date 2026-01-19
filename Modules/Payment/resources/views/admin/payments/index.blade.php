@include('payment::admin.helper')

<div>
    <!-- Kazanç İstatistik Kartları -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card bg-success-lt h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <span class="bg-success text-white avatar me-3">
                            <i class="fas fa-calendar-day"></i>
                        </span>
                        <div>
                            <div class="h2 mb-0">{{ number_format($this->earningCards['today']['amount'], 0, ',', '.') }} ₺</div>
                            <div class="text-muted small">Bugünkü Kazanç</div>
                            @if($this->earningCards['today']['count'] > 0)
                                <div class="text-muted small">{{ $this->earningCards['today']['count'] }} ödeme</div>
                            @endif
                        </div>
                    </div>
                    @if($this->earningCards['today']['trend'] != 0)
                        <div class="mt-2 small {{ $this->earningCards['today']['trend'] > 0 ? 'text-success' : 'text-danger' }}">
                            <i class="fas fa-{{ $this->earningCards['today']['trend'] > 0 ? 'arrow-up' : 'arrow-down' }} me-1"></i>
                            %{{ number_format(abs($this->earningCards['today']['trend']), 1) }} düne göre
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card bg-primary-lt h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <span class="bg-primary text-white avatar me-3">
                            <i class="fas fa-calendar-week"></i>
                        </span>
                        <div>
                            <div class="h2 mb-0">{{ number_format($this->earningCards['week']['amount'], 0, ',', '.') }} ₺</div>
                            <div class="text-muted small">Bu Hafta</div>
                        </div>
                    </div>
                    @if($this->earningCards['week']['trend'] != 0)
                        <div class="mt-2 small {{ $this->earningCards['week']['trend'] > 0 ? 'text-success' : 'text-danger' }}">
                            <i class="fas fa-{{ $this->earningCards['week']['trend'] > 0 ? 'arrow-up' : 'arrow-down' }} me-1"></i>
                            %{{ number_format(abs($this->earningCards['week']['trend']), 1) }} geçen haftaya göre
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card bg-purple-lt h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <span class="bg-purple text-white avatar me-3">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                        <div>
                            <div class="h2 mb-0">{{ number_format($this->earningCards['month']['amount'], 0, ',', '.') }} ₺</div>
                            <div class="text-muted small">Bu Ay</div>
                        </div>
                    </div>
                    @if($this->earningCards['month']['trend'] != 0)
                        <div class="mt-2 small {{ $this->earningCards['month']['trend'] > 0 ? 'text-success' : 'text-danger' }}">
                            <i class="fas fa-{{ $this->earningCards['month']['trend'] > 0 ? 'arrow-up' : 'arrow-down' }} me-1"></i>
                            %{{ number_format(abs($this->earningCards['month']['trend']), 1) }} geçen aya göre
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card bg-azure-lt h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <span class="bg-azure text-white avatar me-3">
                            <i class="fas fa-chart-line"></i>
                        </span>
                        <div>
                            <div class="h2 mb-0">{{ $stats['completed_count'] }}</div>
                            <div class="text-muted small">Tamamlanan Ödeme</div>
                            <div class="text-muted small">{{ number_format($stats['completed_amount'], 0, ',', '.') }} ₺</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kazanç Grafiği -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="row align-items-center w-100">
                <div class="col">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-chart-bar text-success me-2"></i>
                        Kazanç Dağılımı
                    </h3>
                </div>
                <div class="col-auto">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <!-- View Mode Buttons -->
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" wire:click="setChartViewMode('hourly')"
                                class="btn btn-sm {{ $chartViewMode === 'hourly' ? 'btn-success' : 'btn-outline-secondary' }}">
                                Saatlik
                            </button>
                            <button type="button" wire:click="setChartViewMode('daily')"
                                class="btn btn-sm {{ $chartViewMode === 'daily' ? 'btn-success' : 'btn-outline-secondary' }}">
                                Günlük
                            </button>
                            <button type="button" wire:click="setChartViewMode('weekly')"
                                class="btn btn-sm {{ $chartViewMode === 'weekly' ? 'btn-success' : 'btn-outline-secondary' }}">
                                Haftalık
                            </button>
                            <button type="button" wire:click="setChartViewMode('monthly')"
                                class="btn btn-sm {{ $chartViewMode === 'monthly' ? 'btn-success' : 'btn-outline-secondary' }}">
                                Aylık
                            </button>
                        </div>

                        <!-- Date Navigation (Sadece hourly/daily mode'da) -->
                        @if(in_array($chartViewMode, ['hourly', 'daily']))
                            <div class="d-flex align-items-center gap-2">
                                <button wire:click="goToPreviousChartDay" class="btn btn-sm btn-icon btn-outline-secondary" title="Önceki Gün">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <input type="date" wire:model.live="chartDate" class="form-control form-control-sm" style="width: 140px;" max="{{ now()->format('Y-m-d') }}">
                                <button wire:click="goToNextChartDay" class="btn btn-sm btn-icon btn-outline-secondary" title="Sonraki Gün" @if(Carbon\Carbon::parse($chartDate)->isToday()) disabled @endif>
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                                <button wire:click="goToChartToday" class="btn btn-sm btn-outline-success">
                                    Bugün
                                </button>
                            </div>
                        @endif

                        <div wire:loading class="ms-1">
                            <span class="spinner-border spinner-border-sm text-success"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Hourly Chart -->
            @if($chartViewMode === 'hourly')
                @php
                    $chartStats = $this->hourlyEarnings;
                    $maxValue = (!empty($chartStats) && max($chartStats) > 0) ? max($chartStats) : 1;
                @endphp
                <div class="d-flex">
                    <!-- Y Axis -->
                    <div class="d-flex flex-column justify-content-between text-end pe-2 pt-2" style="width: 70px; height: 120px;">
                        <span class="text-muted small fw-medium">{{ number_format($maxValue, 0, ',', '.') }} ₺</span>
                        <span class="text-muted small">{{ number_format(intval($maxValue / 2), 0, ',', '.') }}</span>
                        <span class="text-muted small">0</span>
                    </div>
                    <!-- Bars -->
                    <div class="flex-fill d-flex align-items-end gap-1 pt-3" style="height: 140px;">
                        @foreach($chartStats as $hour => $amount)
                            <div class="flex-fill text-center">
                                @if($amount > 0)
                                    <div class="text-muted small mb-1" style="font-size: 9px;">{{ $amount > 999 ? number_format($amount/1000, 1).'k' : number_format($amount, 0) }}</div>
                                @endif
                                <div class="bg-success rounded-top transition-all" style="height: {{ ($amount / $maxValue) * 100 }}px; min-height: 2px;"></div>
                                <div class="text-muted small mt-1" style="font-size: 10px;">{{ sprintf('%02d', $hour) }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Daily Chart (Son 7 Gün) -->
            @if($chartViewMode === 'daily')
                @php
                    $chartStats = $this->dailyEarnings;
                    $maxValue = (!empty($chartStats) && max($chartStats) > 0) ? max($chartStats) : 1;
                @endphp
                <div class="d-flex">
                    <!-- Y Axis -->
                    <div class="d-flex flex-column justify-content-between text-end pe-2 pt-2" style="width: 70px; height: 120px;">
                        <span class="text-muted small fw-medium">{{ number_format($maxValue, 0, ',', '.') }} ₺</span>
                        <span class="text-muted small">{{ number_format(intval($maxValue / 2), 0, ',', '.') }}</span>
                        <span class="text-muted small">0</span>
                    </div>
                    <!-- Bars -->
                    <div class="flex-fill d-flex align-items-end justify-content-around gap-2 pt-3" style="height: 140px;">
                        @foreach($chartStats as $date => $amount)
                            @php
                                $carbonDate = Carbon\Carbon::parse($date);
                                $isToday = $carbonDate->isToday();
                            @endphp
                            <div class="flex-fill text-center" style="max-width: 80px;">
                                <div class="text-muted small mb-1 fw-medium" style="font-size: 10px;">{{ $amount > 999 ? number_format($amount/1000, 1).'k' : number_format($amount, 0) }} ₺</div>
                                <div class="{{ $isToday ? 'bg-primary' : 'bg-success' }} rounded-top transition-all" style="height: {{ ($amount / $maxValue) * 100 }}px; min-height: 2px;"></div>
                                <div class="text-muted small mt-1">{{ $carbonDate->translatedFormat('D') }}</div>
                                <div class="text-muted small" style="font-size: 10px;">{{ $carbonDate->format('d/m') }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Weekly Chart (Son 4 Hafta) -->
            @if($chartViewMode === 'weekly')
                @php
                    $chartStats = $this->weeklyEarnings;
                    $maxValue = (!empty($chartStats) && max($chartStats) > 0) ? max($chartStats) : 1;
                @endphp
                <div class="d-flex">
                    <!-- Y Axis -->
                    <div class="d-flex flex-column justify-content-between text-end pe-2 pt-2" style="width: 70px; height: 120px;">
                        <span class="text-muted small fw-medium">{{ number_format($maxValue, 0, ',', '.') }} ₺</span>
                        <span class="text-muted small">{{ number_format(intval($maxValue / 2), 0, ',', '.') }}</span>
                        <span class="text-muted small">0</span>
                    </div>
                    <!-- Bars -->
                    <div class="flex-fill d-flex align-items-end justify-content-around gap-3 pt-3" style="height: 140px;">
                        @foreach($chartStats as $label => $amount)
                            <div class="flex-fill text-center" style="max-width: 150px;">
                                <div class="text-muted small mb-1 fw-medium" style="font-size: 10px;">{{ $amount > 999 ? number_format($amount/1000, 1).'k' : number_format($amount, 0) }} ₺</div>
                                <div class="bg-purple rounded-top transition-all" style="height: {{ ($amount / $maxValue) * 100 }}px; min-height: 2px;"></div>
                                <div class="text-muted small mt-1" style="font-size: 11px;">{{ $label }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Monthly Chart (Son 6 Ay) -->
            @if($chartViewMode === 'monthly')
                @php
                    $chartStats = $this->monthlyEarnings;
                    $maxValue = (!empty($chartStats) && max($chartStats) > 0) ? max($chartStats) : 1;
                @endphp
                <div class="d-flex">
                    <!-- Y Axis -->
                    <div class="d-flex flex-column justify-content-between text-end pe-2 pt-2" style="width: 70px; height: 120px;">
                        <span class="text-muted small fw-medium">{{ number_format($maxValue, 0, ',', '.') }} ₺</span>
                        <span class="text-muted small">{{ number_format(intval($maxValue / 2), 0, ',', '.') }}</span>
                        <span class="text-muted small">0</span>
                    </div>
                    <!-- Bars -->
                    <div class="flex-fill d-flex align-items-end justify-content-around gap-2 pt-3" style="height: 140px;">
                        @foreach($chartStats as $label => $amount)
                            <div class="flex-fill text-center" style="max-width: 100px;">
                                <div class="text-muted small mb-1 fw-medium" style="font-size: 10px;">{{ $amount > 999 ? number_format($amount/1000, 1).'k' : number_format($amount, 0) }} ₺</div>
                                <div class="bg-azure rounded-top transition-all" style="height: {{ ($amount / $maxValue) * 100 }}px; min-height: 2px;"></div>
                                <div class="text-muted small mt-1" style="font-size: 11px;">{{ $label }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- Header Bolumu -->
            <div class="row mb-3">
                <!-- Arama Kutusu -->
                <div class="col">
                    <div class="input-icon">
                        <span class="input-icon-addon"><i class="fas fa-search"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                            placeholder="Odeme no, islem ID...">
                    </div>
                </div>

                <!-- Ortadaki Loading -->
                <div class="col position-relative">
                    <div wire:loading class="position-absolute top-50 start-50 translate-middle text-center" style="width: 100%; max-width: 250px;">
                        <div class="small mb-2" style="color: var(--tblr-body-color); opacity: 0.7;">Guncelleniyor...</div>
                        <div class="progress mb-1">
                            <div class="progress-bar progress-bar-indeterminate"></div>
                        </div>
                    </div>
                </div>

                <!-- Sag Taraf -->
                <div class="col">
                    <div class="d-flex align-items-center justify-content-end gap-3">
                        <!-- Filtre Butonu -->
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse"
                            data-bs-target="#filterCollapse" aria-expanded="false">
                            <i class="fas fa-filter me-1"></i>Filtreler
                            @if($this->hasActiveFilters())
                            <span class="badge bg-primary ms-1">Aktif</span>
                            @endif
                        </button>

                        <!-- Excel Export -->
                        <button wire:click="exportPayments" class="btn btn-sm btn-success">
                            <i class="fas fa-download me-1"></i>Excel
                        </button>

                        <!-- Sayfa Adeti -->
                        <div style="min-width: 70px">
                            <select wire:model.live="perPage" class="form-select form-select-sm">
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtre Bolumu - Acilir Kapanir -->
            <div class="collapse mb-3" id="filterCollapse">
                <div class="card card-body bg-light">
                    <!-- Row 1: Durum Filtreleri -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-2">
                            <label class="form-label small text-muted">Ödeme Durumu</label>
                            <select wire:model.live="status" class="form-select form-select-sm">
                                <option value="">Tümü</option>
                                <option value="completed">Tamamlandı</option>
                                <option value="pending">Bekliyor</option>
                                <option value="failed">Başarısız</option>
                                <option value="refunded">İade</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted">Ödeme Yöntemi</label>
                            <select wire:model.live="gateway" class="form-select form-select-sm">
                                <option value="">Tümü</option>
                                <option value="paytr">Kredi Kartı</option>
                                <option value="manual">Havale/EFT</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted">Başlangıç Tarihi</label>
                            <input type="date" wire:model.live="dateFrom" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted">Bitiş Tarihi</label>
                            <input type="date" wire:model.live="dateTo" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted">Min Tutar (TL)</label>
                            <input type="number" wire:model.live.debounce.500ms="amountMin" class="form-control form-control-sm" placeholder="0">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            @if($this->hasActiveFilters())
                            <button type="button" class="btn btn-sm btn-outline-danger w-100" wire:click="clearFilters">
                                <i class="fas fa-times me-1"></i>Temizle
                            </button>
                            @endif
                        </div>
                    </div>

                    <!-- Row 2: Tutar ve Switch'ler -->
                    <div class="row g-3 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label small text-muted">Max Tutar (TL)</label>
                            <input type="number" wire:model.live.debounce.500ms="amountMax" class="form-control form-control-sm" placeholder="~">
                        </div>
                        <div class="col-md-4"></div>
                        <div class="col-md-2">
                            <label class="form-check form-switch mb-0">
                                <input type="checkbox" wire:model.live="showCompleted" class="form-check-input">
                                <span class="form-check-label small text-success"><i class="fas fa-check-circle me-1"></i>Tamamlananlar</span>
                            </label>
                        </div>
                        <div class="col-md-2">
                            <label class="form-check form-switch mb-0">
                                <input type="checkbox" wire:model.live="showPending" class="form-check-input">
                                <span class="form-check-label small text-warning"><i class="fas fa-clock me-1"></i>Bekleyenler</span>
                            </label>
                        </div>
                        <div class="col-md-2">
                            <label class="form-check form-switch mb-0">
                                <input type="checkbox" wire:model.live="showFailed" class="form-check-input">
                                <span class="form-check-label small text-danger"><i class="fas fa-times-circle me-1"></i>Başarısızlar</span>
                            </label>
                        </div>
                    </div>

                    <!-- Aktif Filtreler Badge'leri -->
                    @if($this->hasActiveFilters())
                    <div class="d-flex flex-wrap gap-2 mt-3 pt-3 border-top">
                        @if($search)
                            <span class="badge bg-azure-lt text-azure">Arama: {{ $search }}</span>
                        @endif
                        @if($status)
                            @php
                                $sLabels = ['completed' => 'Tamamlandı', 'pending' => 'Bekliyor', 'failed' => 'Başarısız', 'refunded' => 'İade'];
                            @endphp
                            <span class="badge bg-green-lt text-green">Durum: {{ $sLabels[$status] ?? $status }}</span>
                        @endif
                        @if($gateway)
                            <span class="badge bg-purple-lt text-purple">Yöntem: {{ $gateway === 'paytr' ? 'Kredi Kartı' : 'Havale' }}</span>
                        @endif
                        @if($dateFrom)
                            <span class="badge bg-teal-lt text-teal">Başlangıç: {{ $dateFrom }}</span>
                        @endif
                        @if($dateTo)
                            <span class="badge bg-cyan-lt text-cyan">Bitiş: {{ $dateTo }}</span>
                        @endif
                        @if($amountMin)
                            <span class="badge bg-orange-lt text-orange">Min: {{ $amountMin }} TL</span>
                        @endif
                        @if($amountMax)
                            <span class="badge bg-red-lt text-red">Max: {{ $amountMax }} TL</span>
                        @endif
                        @if(!$showCompleted)
                            <span class="badge bg-secondary-lt text-secondary">Tamamlananlar Hariç</span>
                        @endif
                        @if($showPending)
                            <span class="badge bg-yellow-lt text-yellow">Bekleyenler Dahil</span>
                        @endif
                        @if($showFailed)
                            <span class="badge bg-red-lt text-red">Başarısızlar Dahil</span>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Ozet Istatistikler (Compact) -->
            <div class="d-flex gap-4 mb-3 small">
                <span><i class="fas fa-credit-card me-1"></i>Toplam: <strong>{{ $stats['total_count'] }}</strong> ({{ number_format($stats['total_amount'], 0, ',', '.') }} TL)</span>
                <span class="text-success"><i class="fas fa-check-circle me-1"></i>Tamamlanan: <strong>{{ $stats['completed_count'] }}</strong></span>
                <span class="text-warning"><i class="fas fa-clock me-1"></i>Bekleyen: <strong>{{ $stats['pending_count'] }}</strong></span>
                <span class="text-danger"><i class="fas fa-times-circle me-1"></i>Basarisiz: <strong>{{ $stats['failed_count'] }}</strong></span>
            </div>

            <!-- Tablo -->
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Siparis</th>
                            <th>Musteri</th>
                            <th class="text-end">Tutar</th>
                            <th class="text-center">Yontem</th>
                            <th class="text-center">Durum</th>
                            <th class="text-center" title="Fatura">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </th>
                            <th>Tarih</th>
                            <th class="text-center">Islem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                        @php
                            $statusConfig = [
                                'pending' => ['color' => 'yellow', 'icon' => 'clock', 'label' => 'Bekliyor'],
                                'processing' => ['color' => 'blue', 'icon' => 'spinner fa-spin', 'label' => 'Isleniyor'],
                                'completed' => ['color' => 'green', 'icon' => 'check-circle', 'label' => 'Tamamlandi'],
                                'failed' => ['color' => 'red', 'icon' => 'times-circle', 'label' => 'Basarisiz'],
                                'cancelled' => ['color' => 'dark', 'icon' => 'ban', 'label' => 'Iptal'],
                                'refunded' => ['color' => 'orange', 'icon' => 'undo', 'label' => 'Iade'],
                            ];
                            $gatewayConfig = [
                                'paytr' => ['color' => 'purple', 'icon' => 'credit-card', 'label' => 'Kart'],
                                'manual' => ['color' => 'cyan', 'icon' => 'building-columns', 'label' => 'Havale'],
                                'bank_transfer' => ['color' => 'cyan', 'icon' => 'building-columns', 'label' => 'Havale'],
                            ];
                            $sc = $statusConfig[$payment->status] ?? ['color' => 'dark', 'icon' => 'question', 'label' => $payment->status];
                            $gc = $gatewayConfig[$payment->gateway] ?? ['color' => 'azure', 'icon' => 'wallet', 'label' => '-'];
                        @endphp
                        <tr wire:key="payment-{{ $payment->payment_id }}">
                            <td>
                                <div class="fw-medium">{{ $payment->payable?->order_number ?? '#' . $payment->payable_id }}</div>
                                <div class="small" style="color: var(--tblr-body-color); opacity: 0.7;">{{ $payment->payment_number }}</div>
                            </td>
                            <td>
                                @if($payment->payable?->customer_name)
                                    <div>{{ Str::limit($payment->payable->customer_name, 20) }}</div>
                                    @if($payment->payable->customer_email)
                                        <div class="small" style="color: var(--tblr-body-color); opacity: 0.7;">{{ Str::limit($payment->payable->customer_email, 25) }}</div>
                                    @endif
                                @else
                                    <span>-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <strong>{{ number_format($payment->amount, 0, ',', '.') }} TL</strong>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $gc['color'] }}">
                                    <i class="fas fa-{{ $gc['icon'] }} me-1"></i>{{ $gc['label'] }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $sc['color'] }}">{{ $sc['label'] }}</span>
                            </td>
                            <td class="text-center">
                                @if($payment->invoice_path)
                                    <span class="text-success" title="Fatura Yüklendi" data-bs-toggle="tooltip">
                                        <i class="fas fa-check-circle"></i>
                                    </span>
                                @else
                                    <span class="text-muted" title="Fatura Bekleniyor" data-bs-toggle="tooltip">
                                        <i class="fas fa-minus-circle"></i>
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($payment->paid_at)
                                    <div class="small text-success">{{ $payment->paid_at->format('d.m.Y H:i') }}</div>
                                @else
                                    <div class="small">{{ $payment->created_at->format('d.m.Y H:i') }}</div>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($payment->status === 'pending')
                                    <div class="btn-group">
                                        <button wire:click="markAsCompleted({{ $payment->payment_id }})"
                                                wire:confirm="Odemeyi onaylamak istediginizden emin misiniz?"
                                                class="btn btn-sm btn-success" title="Onayla">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button wire:click="markAsFailed({{ $payment->payment_id }})"
                                                wire:confirm="Odemeyi reddetmek istediginizden emin misiniz?"
                                                class="btn btn-sm btn-outline-danger" title="Reddet">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @else
                                    <button type="button" class="btn btn-sm btn-primary"
                                            data-bs-toggle="modal" data-bs-target="#paymentDetailModal"
                                            onclick="loadPaymentDetail({{ $payment->payment_id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="empty">
                                    <div class="empty-icon"><i class="fas fa-credit-card fa-3x" style="color: var(--tblr-body-color); opacity: 0.5;"></i></div>
                                    <p class="empty-title">Odeme bulunamadi</p>
                                    <p class="empty-subtitle" style="color: var(--tblr-body-color); opacity: 0.7;">Filtrelere uygun odeme yok.</p>
                                    @if($this->hasActiveFilters())
                                    <div class="empty-action">
                                        <button wire:click="clearFilters" class="btn btn-primary">
                                            <i class="fas fa-times me-1"></i>Filtreleri Temizle
                                        </button>
                                    </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($payments->hasPages())
        <div class="card-footer d-flex justify-content-end">
            {{ $payments->links() }}
        </div>
        @endif
    </div>

    <!-- Payment Detail Modal (Bootstrap) -->
    <div class="modal modal-blur fade" id="paymentDetailModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-credit-card me-2"></i>
                        <span id="modalPaymentTitle">Ödeme Detayı</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="paymentModalBody">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary"></div>
                        <div class="mt-2 text-muted">Yükleniyor...</div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <div>
                        <button type="button" class="btn btn-ghost-secondary" onclick="loadPrevPayment()" id="prevPaymentBtn" disabled>
                            <i class="fas fa-chevron-left me-1"></i>Önceki
                        </button>
                        <button type="button" class="btn btn-ghost-secondary" onclick="loadNextPayment()" id="nextPaymentBtn" disabled>
                            Sonraki<i class="fas fa-chevron-right ms-1"></i>
                        </button>
                    </div>
                    <button type="button" class="btn" data-bs-dismiss="modal">Kapat</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Payment navigation
let paymentIds = [];
let currentPaymentIndex = -1;

function updatePaymentIds() {
    paymentIds = Array.from(document.querySelectorAll('input[wire\\:model\\.live="selectedPayments"]')).map(el => parseInt(el.value));
}

function loadPaymentDetail(paymentId) {
    updatePaymentIds();
    currentPaymentIndex = paymentIds.indexOf(paymentId);
    updateNavButtons();

    document.getElementById('paymentModalBody').innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"></div><div class="mt-2 text-muted">Yükleniyor...</div></div>';
    document.getElementById('modalPaymentTitle').textContent = 'Ödeme Detayı';

    fetch(`/admin/payment/${paymentId}/ajax-detail`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('modalPaymentTitle').textContent = data.payment.payment_number;
                document.getElementById('paymentModalBody').innerHTML = data.html;
            } else {
                document.getElementById('paymentModalBody').innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Ödeme bulunamadı</div>';
            }
        })
        .catch(error => {
            console.error('Payment detail error:', error);
            document.getElementById('paymentModalBody').innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Bir hata oluştu</div>';
        });
}

function updateNavButtons() {
    document.getElementById('prevPaymentBtn').disabled = currentPaymentIndex <= 0;
    document.getElementById('nextPaymentBtn').disabled = currentPaymentIndex >= paymentIds.length - 1;
}

function loadPrevPayment() {
    if (currentPaymentIndex > 0) {
        loadPaymentDetail(paymentIds[currentPaymentIndex - 1]);
    }
}

function loadNextPayment() {
    if (currentPaymentIndex < paymentIds.length - 1) {
        loadPaymentDetail(paymentIds[currentPaymentIndex + 1]);
    }
}

// Payment Detail Modal Fonksiyonları
function copyText(text) {
    navigator.clipboard.writeText(text).catch(() => {
        const ta = document.createElement('textarea');
        ta.value = text;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
    });
}

function handleDragOver(e) {
    e.preventDefault();
    e.currentTarget.style.background = 'rgba(64, 192, 87, 0.1)';
}
function handleDragLeave(e) {
    e.preventDefault();
    e.currentTarget.style.background = '';
}
function handleDrop(e, id) {
    e.preventDefault();
    e.currentTarget.style.background = '';
    if (e.dataTransfer.files.length) uploadInvoice(e.dataTransfer.files[0], id);
}
function handleSelect(e, id) {
    if (e.target.files.length) uploadInvoice(e.target.files[0], id);
}

function uploadInvoice(file, id) {
    const allowed = ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'];
    if (!allowed.includes(file.type)) return showToast('Sadece PDF, JPG, PNG!', 'warning');
    if (file.size > 10 * 1024 * 1024) return showToast('Max 10MB!', 'warning');

    document.getElementById(`dropzoneContent-${id}`).classList.add('d-none');
    document.getElementById(`dropzoneLoading-${id}`).classList.remove('d-none');

    const fd = new FormData();
    fd.append('invoice_file', file);
    fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', `/admin/payment/${id}/upload-invoice`);
    xhr.upload.onprogress = e => {
        if (e.lengthComputable) document.getElementById(`uploadProgress-${id}`).style.width = (e.loaded / e.total * 100) + '%';
    };
    xhr.onload = () => {
        try {
            const r = JSON.parse(xhr.responseText);
            if (xhr.status === 200 && r.success) {
                showToast('Fatura yüklendi', 'success');
                loadPaymentDetail(id);
            } else {
                showToast(r.message || 'Hata!', 'danger');
                resetUpload(id);
            }
        } catch(e) { showToast('Hata!', 'danger'); resetUpload(id); }
    };
    xhr.onerror = () => { showToast('Bağlantı hatası!', 'danger'); resetUpload(id); };
    xhr.send(fd);
}

function resetUpload(id) {
    document.getElementById(`dropzoneContent-${id}`).classList.remove('d-none');
    document.getElementById(`dropzoneLoading-${id}`).classList.add('d-none');
    document.getElementById(`uploadProgress-${id}`).style.width = '0%';
}

function deleteInvoice(id) {
    const m = document.createElement('div');
    m.innerHTML = `
        <div class="modal fade" tabindex="-1">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center py-4">
                        <i class="fas fa-trash fa-3x text-danger mb-3"></i>
                        <h5>Faturayı Sil?</h5>
                    </div>
                    <div class="modal-footer justify-content-center border-0 pt-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="button" class="btn btn-danger" id="confirmDel">Sil</button>
                    </div>
                </div>
            </div>
        </div>`;
    document.body.appendChild(m);
    const modal = new bootstrap.Modal(m.querySelector('.modal'));
    m.querySelector('#confirmDel').onclick = () => {
        modal.hide();
        fetch(`/admin/payment/${id}/delete-invoice`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
        }).then(r => r.json()).then(d => {
            if (d.success) {
                showToast('Silindi', 'success');
                loadPaymentDetail(id);
            }
            else showToast('Hata!', 'danger');
        });
    };
    m.querySelector('.modal').addEventListener('hidden.bs.modal', () => m.remove());
    modal.show();
}

function showUploadArea(id) {
    document.getElementById(`invoicePreview-${id}`).classList.add('d-none');
    document.getElementById(`invoiceUploadArea-${id}`).classList.remove('d-none');
}

function showToast(msg, type = 'info') {
    let c = document.getElementById('toast-container');
    if (!c) {
        c = document.createElement('div');
        c.id = 'toast-container';
        c.className = 'toast-container position-fixed top-0 end-0 p-3';
        c.style.zIndex = '9999';
        document.body.appendChild(c);
    }
    const t = document.createElement('div');
    t.className = `toast show align-items-center text-white bg-${type} border-0`;
    t.innerHTML = `<div class="d-flex"><div class="toast-body">${msg}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="this.closest('.toast').remove()"></button></div>`;
    c.appendChild(t);
    setTimeout(() => t.remove(), 3000);
}
</script>
@endpush
