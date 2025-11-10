@php
    View::share('pretitle', 'Onay Bekleyen Yorumlar');
@endphp

<div class="pending-reviews-wrapper">
    <div class="card">
        @include('reviewsystem::admin.helper')
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Yorum</th>
                            <th>Model</th>
                            <th>Puan</th>
                            <th>Tarih</th>
                            <th class="text-center">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                            <tr>
                                <td>{{ $review->id }}</td>
                                <td>
                                    <div class="mb-1"><strong>{{ $review->author_name ?? $review->user?->name }}</strong></div>
                                    <div class="text-muted small">{{ \Str::limit($review->review_body, 100) }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-azure-lt">{{ class_basename($review->reviewable_type) }}</span>
                                    <br><code>{{ $review->reviewable_id }}</code>
                                </td>
                                <td class="text-center">
                                    @if($review->rating_value)
                                        <span class="badge bg-yellow-lt"><i class="fas fa-star"></i> {{ $review->rating_value }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td><small class="text-muted">{{ $review->created_at->format('d.m.Y H:i') }}</small></td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center gap-3 justify-content-center">
                                        <a href="javascript:void(0);" wire:click="approveReview({{ $review->id }})" data-bs-toggle="tooltip" title="Onayla">
                                            <i class="fa-solid fa-check link-success fa-lg"></i>
                                        </a>
                                        <a href="javascript:void(0);" wire:click="deleteReview({{ $review->id }})" wire:confirm="Silmek istediğinizden emin misiniz?" data-bs-toggle="tooltip" title="Sil">
                                            <i class="fa-solid fa-trash link-danger fa-lg"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="empty">
                                        <p class="empty-title">Onay bekleyen yorum yok</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            @if ($reviews->hasPages())
                {{ $reviews->links() }}
            @else
                <p class="small text-muted mb-0">Toplam <span class="fw-semibold">{{ $reviews->total() }}</span> yorum</p>
            @endif
        </div>
    </div>
</div>
