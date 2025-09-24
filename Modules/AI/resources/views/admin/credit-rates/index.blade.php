@extends('admin.layout')

@section('title', 'Model Credit Rate Yönetimi')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="fas fa-calculator me-2"></i>
                    Model Credit Rate Yönetimi
                </h2>
                <p class="text-secondary mt-1">
                    AI provider modellerinin kredi maliyetlerini yönetin ve analiz edin
                </p>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="{{ route('admin.ai.credit-rates.calculator') }}" class="btn btn-outline-primary">
                        <i class="fas fa-calculator me-1"></i>
                        Kredi Hesaplayıcı
                    </a>
                    <a href="{{ route('admin.ai.credit-rates.import') }}" class="btn btn-outline-info">
                        <i class="fas fa-upload me-1"></i>
                        İçe Aktar
                    </a>
                    <a href="{{ route('admin.ai.credit-rates.export') }}" class="btn btn-outline-success">
                        <i class="fas fa-download me-1"></i>
                        Dışa Aktar
                    </a>
                    <a href="{{ route('admin.ai.credit-rates.analytics') }}" class="btn btn-primary">
                        <i class="fas fa-chart-line me-1"></i>
                        Analytics
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        
        <!-- İstatistikler -->
        <div class="row row-deck row-cards mb-3">
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-primary text-white avatar">
                                    <i class="fas fa-server"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    {{ $statistics['total_providers'] }}
                                </div>
                                <div class="text-secondary">
                                    Toplam Provider
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-info text-white avatar">
                                    <i class="fas fa-microchip"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    {{ $statistics['total_models'] }}
                                </div>
                                <div class="text-secondary">
                                    Mevcut Model
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-success text-white avatar">
                                    <i class="fas fa-cog"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    {{ $statistics['configured_rates'] }}
                                </div>
                                <div class="text-secondary">
                                    Yapılandırılmış Rate
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-warning text-white avatar">
                                    <i class="fas fa-dollar-sign"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    {{ number_format($statistics['avg_input_cost'] ?? 0, 2) }}
                                </div>
                                <div class="text-secondary">
                                    Ortalama Input Cost
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Provider Listesi -->
        <div class="row">
            @foreach($providers as $provider)
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-status-top @if($provider->is_active) bg-green @else bg-red @endif"></div>
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-server me-2"></i>
                            {{ $provider->name }}
                        </h3>
                        <div class="card-actions">
                            <a href="{{ route('admin.ai.credit-rates.manage', $provider->id) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit me-1"></i>
                                Yönet
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($provider->available_models && is_array($provider->available_models))
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="text-secondary">Toplam Model:</div>
                                    <div class="h4 m-0">{{ count($provider->available_models) }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-secondary">Configured:</div>
                                    <div class="h4 m-0">{{ $provider->modelCreditRates->count() }}</div>
                                </div>
                            </div>

                            <!-- Model Listesi -->
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless">
                                    <tbody>
                                        @foreach(array_slice($provider->available_models, 0, 5) as $key => $model)
                                            @php 
                                                $modelName = is_string($model) ? $model : $key;
                                                $modelRate = $provider->getModelRate($modelName);
                                            @endphp
                                            <tr>
                                                <td class="text-truncate" style="max-width: 120px;">
                                                    <span class="text-secondary">{{ $modelName }}</span>
                                                </td>
                                                <td class="text-end">
                                                    @if($modelRate)
                                                        <span class="badge badge-success">
                                                            {{ $modelRate->input_cost_per_1k }}/{{ $modelRate->output_cost_per_1k }}
                                                        </span>
                                                    @else
                                                        <span class="badge badge-danger">
                                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                                            Yapılandırılmamış
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        
                                        @if(count($provider->available_models) > 5)
                                            <tr>
                                                <td colspan="2" class="text-center">
                                                    <small class="text-secondary">
                                                        +{{ count($provider->available_models) - 5 }} daha...
                                                    </small>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-secondary text-center py-3">
                                <i class="fas fa-exclamation-circle"></i>
                                Model bilgisi bulunamadı
                            </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="text-secondary">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $provider->updated_at->diffForHumans() }}
                                </div>
                            </div>
                            <div class="col-auto">
                                @if($provider->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-danger">Pasif</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            @if($providers->isEmpty())
                <div class="col-12">
                    <div class="card">
                        <div class="empty">
                            <div class="empty-img">
                                <img src="{{ asset('admin/static/illustrations/undraw_void.svg') }}" height="128" alt="">
                            </div>
                            <p class="empty-title">Henüz provider bulunamadı</p>
                            <p class="empty-subtitle text-secondary">
                                Model credit rate yönetimi için önce AI provider'lar yapılandırılmalıdır.
                            </p>
                            <div class="empty-action">
                                <a href="{{ route('admin.ai.providers') }}" class="btn btn-primary">
                                    <i class="fas fa-server me-1"></i>
                                    Provider Ayarları
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Hızlı İşlemler -->
        @if($providers->isNotEmpty())
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-bolt me-2"></i>
                            Hızlı İşlemler
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <a href="{{ route('admin.ai.credit-rates.calculator') }}" class="btn btn-outline-primary w-100 py-3">
                                    <div class="d-flex align-items-center">
                                        <span class="me-3">
                                            <i class="fas fa-calculator fa-2x"></i>
                                        </span>
                                        <div class="text-start">
                                            <div class="fw-bold">Kredi Hesaplayıcı</div>
                                            <div class="text-secondary">Model maliyetlerini karşılaştır</div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            
                            <div class="col-md-4">
                                <a href="{{ route('admin.ai.credit-rates.import') }}" class="btn btn-outline-info w-100 py-3">
                                    <div class="d-flex align-items-center">
                                        <span class="me-3">
                                            <i class="fas fa-upload fa-2x"></i>
                                        </span>
                                        <div class="text-start">
                                            <div class="fw-bold">Toplu İçe Aktar</div>
                                            <div class="text-secondary">CSV/JSON ile rate yükle</div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            
                            <div class="col-md-4">
                                <a href="{{ route('admin.ai.credit-rates.analytics') }}" class="btn btn-outline-success w-100 py-3">
                                    <div class="d-flex align-items-center">
                                        <span class="me-3">
                                            <i class="fas fa-chart-line fa-2x"></i>
                                        </span>
                                        <div class="text-start">
                                            <div class="fw-bold">Performans Analytics</div>
                                            <div class="text-secondary">Maliyet analizleri ve raporlar</div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>

@push('styles')
<style>
.card-status-top {
    height: 3px;
}

.table-sm td {
    padding: 0.25rem 0.5rem;
}

.text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.empty-img img {
    filter: grayscale(100%);
    opacity: 0.3;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh statistics every 30 seconds
    setInterval(function() {
        // Sayfa istatistiklerini yenile (30 saniyede bir)
        if (document.querySelector('.statistics-container')) {
            window.location.reload();
        }
    }, 30000);
});
</script>
@endpush
@endsection