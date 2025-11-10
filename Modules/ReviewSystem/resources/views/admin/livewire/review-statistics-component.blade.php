@php
    View::share('pretitle', 'Yorum ve Puan İstatistikleri');
@endphp

<div class="review-statistics-wrapper">
    @include('reviewsystem::admin.helper')

    <div class="row row-deck row-cards">
        <!-- Genel İstatistikler -->
        <div class="col-12">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Genel İstatistikler</h3></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h1 class="display-5 fw-bold text-primary">{{ number_format($stats['total_reviews']) }}</h1>
                                <p class="text-muted mb-0">Toplam Yorum</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h1 class="display-5 fw-bold text-orange">{{ number_format($stats['pending_reviews']) }}</h1>
                                <p class="text-muted mb-0">Onay Bekleyen</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h1 class="display-5 fw-bold text-success">{{ number_format($stats['total_ratings']) }}</h1>
                                <p class="text-muted mb-0">Toplam Puan</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h1 class="display-5 fw-bold text-yellow">{{ $stats['average_rating'] ?? 0 }}</h1>
                                <p class="text-muted mb-0">Ortalama Puan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Puan Dağılımı -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Puan Dağılımı</h3></div>
                <div class="card-body">
                    <table class="table table-vcenter">
                        <thead>
                            <tr><th>Yıldız</th><th class="text-end">Adet</th></tr>
                        </thead>
                        <tbody>
                            @for($i = 5; $i >= 1; $i--)
                                <tr>
                                    <td>
                                        <span class="badge bg-yellow-lt">
                                            @for($s = 0; $s < $i; $s++) <i class="fas fa-star"></i> @endfor
                                        </span>
                                    </td>
                                    <td class="text-end"><strong>{{ $stats['ratings_distribution'][$i] ?? 0 }}</strong></td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Model Tiplerine Göre -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Model Tiplerine Göre</h3></div>
                <div class="card-body">
                    <table class="table table-vcenter">
                        <thead>
                            <tr><th>Model Tipi</th><th class="text-end">Yorum Sayısı</th></tr>
                        </thead>
                        <tbody>
                            @forelse($stats['by_model'] as $model => $count)
                                <tr>
                                    <td><span class="badge bg-azure-lt">{{ $model }}</span></td>
                                    <td class="text-end"><strong>{{ number_format($count) }}</strong></td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center text-muted">Veri yok</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Son Yorumlar -->
        <div class="col-12">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Son Eklenen Yorumlar</h3></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr><th>Yazar</th><th>Yorum</th><th>Model</th><th>Puan</th><th>Tarih</th></tr>
                            </thead>
                            <tbody>
                                @forelse($stats['recent_reviews'] as $review)
                                    <tr>
                                        <td><strong>{{ $review->author_name ?? $review->user?->name }}</strong></td>
                                        <td><div class="text-muted small">{{ \Str::limit($review->review_body, 80) }}</div></td>
                                        <td><span class="badge bg-azure-lt">{{ class_basename($review->reviewable_type) }}</span></td>
                                        <td>
                                            @if($review->rating_value)
                                                <span class="badge bg-yellow-lt"><i class="fas fa-star"></i> {{ $review->rating_value }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td><small class="text-muted">{{ $review->created_at->format('d.m.Y H:i') }}</small></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted py-4">Henüz yorum yok</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
