@extends('admin.layout')

@section('page-title', 'Kredi Yönetimi')
@section('page-subtitle', 'AI kredi paketleri ve kullanım yönetimi')

@section('content')
<div class="container-xl">
    <div class="row">
        <!-- Kredi Paketleri Kartı -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-coins me-2"></i>
                        Aktif Kredi Paketleri
                    </h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.ai.credits.packages') }}" class="btn btn-primary">
                            <i class="fas fa-cog me-1"></i>
                            Paket Yönetimi
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($packages->count() > 0)
                        <div class="row g-3">
                            @foreach($packages as $package)
                            <div class="col-md-6 col-xl-3">
                                <div class="card {{ $package->is_popular ? 'border-primary' : '' }}">
                                    @if($package->is_popular)
                                        <div class="ribbon ribbon-top bg-primary">
                                            <i class="fas fa-star"></i>
                                            Popüler
                                        </div>
                                    @endif
                                    
                                    <div class="card-body text-center">
                                        <h3 class="card-title">{{ $package->name }}</h3>
                                        <p class="text-muted">{{ $package->description }}</p>
                                        
                                        <div class="my-3">
                                            <span class="display-6 fw-bold text-primary">
                                                {{ number_format($package->credits, 0) }}
                                            </span>
                                            <span class="text-muted fs-4">kredi</span>
                                        </div>
                                        
                                        <div class="mb-3">
                                            @if($package->discount_percentage > 0)
                                                <span class="text-decoration-line-through text-muted">
                                                    ${{ number_format($package->price_usd, 2) }}
                                                </span>
                                                <br>
                                                <span class="h4 text-success">
                                                    ${{ number_format($package->discounted_price, 2) }}
                                                </span>
                                                <span class="badge bg-success ms-1">
                                                    %{{ $package->discount_percentage }} İndirim
                                                </span>
                                            @else
                                                <span class="h4 text-primary">
                                                    ${{ number_format($package->price_usd, 2) }}
                                                </span>
                                            @endif
                                            
                                            @if($package->price_try)
                                                <div class="text-muted small">
                                                    (₺{{ number_format($package->price_try, 2) }})
                                                </div>
                                            @endif
                                        </div>
                                        
                                        @if($package->features)
                                            <ul class="list-unstyled text-start small">
                                                @foreach($package->features as $feature)
                                                    <li class="mb-1">
                                                        <i class="fas fa-check text-success me-2"></i>
                                                        {{ $feature }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                        
                                        <div class="mt-3">
                                            <button class="btn btn-outline-primary w-100" disabled>
                                                Satın Al (Yakında)
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty">
                            <div class="empty-icon">
                                <i class="fas fa-coins"></i>
                            </div>
                            <p class="empty-title">Kredi paketi bulunamadı</p>
                            <p class="empty-subtitle text-muted">
                                Henüz kredi paketi tanımlanmamış.
                            </p>
                            <div class="empty-action">
                                <a href="{{ route('admin.ai.credits.packages') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>
                                    Paket Oluştur
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- İstatistikler Satırı -->
    <div class="row mt-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Toplam Kredi Paketi</div>
                    </div>
                    <div class="h1 mb-0">{{ $packages->count() }}</div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Aktif Paketler</div>
                    </div>
                    <div class="h1 mb-0 text-success">{{ $packages->where('is_active', true)->count() }}</div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Popüler Paket</div>
                    </div>
                    <div class="h1 mb-0 text-primary">
                        @if($packages->where('is_popular', true)->first())
                            {{ $packages->where('is_popular', true)->first()->name }}
                        @else
                            -
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">Fiyat Aralığı</div>
                    </div>
                    <div class="h1 mb-0 text-info">
                        @if($packages->count() > 0)
                            ${{ number_format($packages->min('price_usd'), 0) }} - ${{ number_format($packages->max('price_usd'), 0) }}
                        @else
                            -
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Hızlı Linkler -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Hızlı İşlemler</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('admin.ai.credits.packages') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-cog me-2"></i>
                                Paket Yönetimi
                            </a>
                        </div>
                        
                        <div class="col-md-3">
                            <a href="{{ route('admin.ai.credits.usage') }}" class="btn btn-outline-info w-100">
                                <i class="fas fa-chart-line me-2"></i>
                                Kullanım Raporları
                            </a>
                        </div>
                        
                        <div class="col-md-3">
                            <a href="{{ route('admin.ai.credits.transactions') }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-receipt me-2"></i>
                                İşlemler
                            </a>
                        </div>
                        
                        <div class="col-md-3">
                            <a href="{{ route('admin.ai.settings') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-sliders-h me-2"></i>
                                AI Ayarları
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection