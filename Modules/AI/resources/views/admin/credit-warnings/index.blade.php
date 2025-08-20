{{-- Credit Warning Management Dashboard --}}
@extends('admin.layout')

@section('title', 'Kredi Uyarı Yönetimi')

@section('content')
<div class="page-wrapper">
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        AI Modülü
                    </div>
                    <h2 class="page-title">
                        Kredi Uyarı Yönetimi
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('admin.ai.credit-warnings.configuration') }}" class="btn btn-primary">
                            <i class="fas fa-cog"></i>
                            Uyarı Ayarları
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            {{-- Credit Statistics Cards --}}
            <div class="row row-deck row-cards mb-3">
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Mevcut Kredi</div>
                            </div>
                            <div class="h1 mb-3">{{ number_format($creditStats['current_balance'] ?? 0) }}</div>
                            <div class="d-flex mb-2">
                                <div class="flex-fill">
                                    <div class="progress progress-sm">
                                        @php
                                            $percentage = isset($creditStats['usage_percentage']) ? min(100, $creditStats['usage_percentage']) : 0;
                                            $colorClass = $percentage > 80 ? 'bg-danger' : ($percentage > 60 ? 'bg-warning' : 'bg-success');
                                        @endphp
                                        <div class="progress-bar {{ $colorClass }}" style="width: {{ $percentage }}%" role="progressbar"></div>
                                    </div>
                                </div>
                                <div class="ms-3">
                                    <span class="text-muted">{{ number_format($percentage, 1) }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Aylık Kullanım</div>
                            </div>
                            <div class="h1 mb-3">{{ number_format($creditStats['monthly_usage'] ?? 0) }}</div>
                            <div class="d-flex mb-2">
                                <div class="flex-fill">
                                    <small class="text-muted">Bu ay toplam</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Günlük Kullanım</div>
                            </div>
                            <div class="h1 mb-3">{{ number_format($creditStats['daily_usage'] ?? 0) }}</div>
                            <div class="d-flex mb-2">
                                <div class="flex-fill">
                                    <small class="text-muted">Bugün toplam</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Aktif Uyarı</div>
                            </div>
                            <div class="h1 mb-3">{{ count($activeWarnings ?? []) }}</div>
                            <div class="d-flex mb-2">
                                <div class="flex-fill">
                                    <small class="text-muted">Toplam uyarı sayısı</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Active Warnings --}}
            @if(!empty($activeWarnings) && count($activeWarnings) > 0)
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Aktif Uyarılar</h3>
                        </div>
                        <div class="card-body">
                            @foreach($activeWarnings as $warning)
                            <div class="alert alert-{{ $warning['level'] === 'critical' ? 'danger' : ($warning['level'] === 'warning' ? 'warning' : 'info') }}" role="alert">
                                <div class="d-flex">
                                    <div>
                                        <i class="fas fa-{{ $warning['level'] === 'critical' ? 'exclamation-triangle' : 'info-circle' }}"></i>
                                    </div>
                                    <div class="ms-2">
                                        <h4 class="alert-title">{{ $warning['title'] ?? 'Kredi Uyarısı' }}</h4>
                                        <div class="text-muted">{{ $warning['message'] ?? 'Kredi durumu kontrol edilmelidir.' }}</div>
                                        @if(isset($warning['created_at']))
                                            <small class="text-muted">{{ $warning['created_at'] }}</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Usage History Chart --}}
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Kredi Kullanım Geçmişi (Son 30 Gün)</h3>
                        </div>
                        <div class="card-body">
                            @if(!empty($usageHistory))
                                <div id="chart-usage-history"></div>
                            @else
                                <div class="empty">
                                    <p class="empty-title">Henüz kullanım verisi yok</p>
                                    <p class="empty-subtitle text-muted">
                                        Kredi kullanımı başladığında burada grafik görünecektir.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Warning Configuration Summary --}}
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Uyarı Konfigürasyonu</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Düşük Kredi Uyarısı</label>
                                        <div class="text-muted">{{ $warningConfig['low_credit_threshold'] ?? '20' }}% kaldığında</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Kritik Uyarı</label>
                                        <div class="text-muted">{{ $warningConfig['critical_threshold'] ?? '10' }}% kaldığında</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Son Uyarı</label>
                                        <div class="text-muted">{{ $warningConfig['final_threshold'] ?? '5' }}% kaldığında</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    @if(!empty($usageHistory))
    // Usage History Chart
    const usageHistory = @json($usageHistory);
    const chartData = {
        chart: {
            type: 'area',
            height: 350,
            fontFamily: 'inherit',
            parentHeightOffset: 0,
            toolbar: {
                show: false,
            },
            animations: {
                enabled: false
            },
        },
        dataLabels: {
            enabled: false,
        },
        fill: {
            opacity: 0.16,
            type: 'solid'
        },
        stroke: {
            width: 2,
            lineCap: "round",
            curve: "smooth",
        },
        series: [{
            name: "Kredi Kullanımı",
            data: usageHistory.values || []
        }],
        tooltip: {
            theme: 'dark'
        },
        grid: {
            padding: {
                top: -20,
                right: 0,
                left: -4,
                bottom: -4
            },
            strokeDashArray: 4,
        },
        xaxis: {
            labels: {
                padding: 0,
            },
            tooltip: {
                enabled: false
            },
            axisBorder: {
                show: false,
            },
            categories: usageHistory.labels || [],
        },
        yaxis: {
            labels: {
                padding: 4
            },
        },
        colors: ['#206bc4'],
        legend: {
            show: false,
        },
    };
    
    const chart = new ApexCharts(document.querySelector("#chart-usage-history"), chartData);
    chart.render();
    @endif
});
</script>
@endpush
@endsection