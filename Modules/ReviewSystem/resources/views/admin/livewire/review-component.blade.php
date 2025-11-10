@php
    View::share('pretitle', 'Yorum Yönetimi');
@endphp

<div class="review-component-wrapper">
    <div class="card">
        @include('reviewsystem::admin.helper')
        <div class="card-body p-0">
            <!-- Header -->
            <div class="row mx-2 my-3">
                <div class="col">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" wire:model.live="search" class="form-control"
                            placeholder="Yorum içeriğinde ara...">
                    </div>
                </div>
                <div class="col position-relative">
                    <div wire:loading class="position-absolute top-50 start-50 translate-middle text-center">
                        <div class="small text-muted mb-2">{{ __('admin.updating') }}</div>
                        <div class="progress mb-1">
                            <div class="progress-bar progress-bar-indeterminate"></div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="d-flex align-items-center justify-content-end gap-3">
                        <div style="width: 120px;">
                            <select wire:model.live="filterStatus" class="form-control" data-choices>
                                <option value="all">Tümü</option>
                                <option value="approved">Onaylı</option>
                                <option value="pending">Bekleyen</option>
                            </select>
                        </div>
                        <div style="width: 80px;">
                            <select wire:model.live="perPage" class="form-control" data-choices>
                                <option value="10">10</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tablo -->
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 50px">
                                <button class="table-sort {{ $sortField === 'id' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('id')">ID</button>
                            </th>
                            <th>Yorum</th>
                            <th>Model</th>
                            <th style="width: 80px">Puan</th>
                            <th style="width: 100px">Durum</th>
                            <th>
                                <button class="table-sort {{ $sortField === 'created_at' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('created_at')">Tarih</button>
                            </th>
                            <th class="text-center" style="width: 120px">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                            <tr wire:key="row-{{ $review->id }}">
                                <td class="sort-id small">{{ $review->id }}</td>
                                <td>
                                    <div class="mb-1">
                                        <strong>{{ $review->author_name ?? $review->user?->name }}</strong>
                                    </div>
                                    <div class="text-muted small" style="max-width: 300px;">
                                        {{ \Str::limit($review->review_body, 100) }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-azure-lt">{{ class_basename($review->reviewable_type) }}</span>
                                    <br>
                                    <code class="small">{{ $review->reviewable_id }}</code>
                                </td>
                                <td class="text-center">
                                    @if($review->rating_value)
                                        <span class="badge bg-yellow-lt">
                                            <i class="fas fa-star"></i> {{ $review->rating_value }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($review->is_approved)
                                        <span class="badge bg-green-lt">Onaylı</span>
                                    @else
                                        <span class="badge bg-orange-lt">Bekliyor</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $review->created_at->format('d.m.Y H:i') }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center gap-3 justify-content-center">
                                        @if(!$review->is_approved)
                                            <a href="javascript:void(0);"
                                                wire:click="approveReview({{ $review->id }})"
                                                data-bs-toggle="tooltip" title="Onayla">
                                                <i class="fa-solid fa-check link-success fa-lg"></i>
                                            </a>
                                        @endif
                                        <a href="javascript:void(0);"
                                            wire:click="deleteReview({{ $review->id }})"
                                            wire:confirm="Bu yorumu silmek istediğinizden emin misiniz?"
                                            data-bs-toggle="tooltip" title="{{ __('admin.delete') }}">
                                            <i class="fa-solid fa-trash link-danger fa-lg"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="empty">
                                        <p class="empty-title">Yorum bulunamadı</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="card-footer">
            @if ($reviews->hasPages())
                {{ $reviews->links() }}
            @else
                <div class="d-flex justify-content-between align-items-center mb-0">
                    <p class="small text-muted mb-0">
                        Toplam <span class="fw-semibold">{{ $reviews->total() }}</span> yorum
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
