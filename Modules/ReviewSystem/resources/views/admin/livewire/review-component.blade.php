@php
    View::share('pretitle', 'Yorum & Puan Yönetimi');
@endphp

<div class="review-component-wrapper">
    <!-- Tab Navigation -->
    <div class="card mb-4">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'reviews' ? 'active' : '' }}"
                       wire:click="$set('activeTab', 'reviews')"
                       href="javascript:void(0);">
                        <i class="fas fa-comments me-2"></i> Yorumlar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'ratings' ? 'active' : '' }}"
                       wire:click="$set('activeTab', 'ratings')"
                       href="javascript:void(0);">
                        <i class="fas fa-star me-2"></i> Puanlar
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Model İstatistik Kartları -->
    @if($modelStats->isNotEmpty())
    <div class="row mb-4">
        @foreach($modelStats as $stat)
        <div class="col-md-6 col-xl-4 mb-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-fill">
                            <h3 class="card-title mb-1">
                                <span class="badge bg-primary-lt fs-3">{{ $stat['model_name'] }}</span>
                            </h3>
                        </div>
                        @if($stat['avg_rating'] > 0)
                        <div class="text-end">
                            <div class="text-yellow fs-2">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="text-muted small">{{ $stat['avg_rating'] }}</div>
                        </div>
                        @endif
                    </div>

                    <div class="row g-2">
                        <div class="col-6">
                            <div class="card card-sm bg-primary-lt">
                                <div class="card-body text-center">
                                    <div class="text-primary fs-2 fw-bold">{{ $stat['total_reviews'] }}</div>
                                    <div class="text-muted small">Toplam Yorum</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card card-sm bg-yellow-lt">
                                <div class="card-body text-center">
                                    <div class="text-yellow fs-2 fw-bold">{{ $stat['total_ratings'] }}</div>
                                    <div class="text-muted small">Toplam Puan</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card card-sm bg-green-lt">
                                <div class="card-body text-center">
                                    <div class="text-green fs-2 fw-bold">{{ $stat['approved_count'] }}</div>
                                    <div class="text-muted small">Onaylı</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card card-sm bg-orange-lt">
                                <div class="card-body text-center">
                                    <div class="text-orange fs-2 fw-bold">{{ $stat['pending_count'] }}</div>
                                    <div class="text-muted small">Bekleyen</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($stat['recent_items']->isNotEmpty())
                    <div class="mt-3 pt-3 border-top">
                        <div class="text-muted small fw-semibold mb-2">
                            <i class="fas fa-clock me-1"></i> Son İçerikler:
                        </div>
                        <div class="list-group list-group-flush">
                            @foreach($stat['recent_items'] as $item)
                            <div class="list-group-item px-0 py-2">
                                <div class="d-flex align-items-center">
                                    <div class="flex-fill text-truncate">
                                        <small class="text-muted">{{ \Str::limit($item['title'], 35) }}</small>
                                    </div>
                                    <code class="ms-2 small">#{{ $item['id'] }}</code>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Reviews Tab -->
    @if($activeTab === 'reviews')
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
                                    @if($editingId === $review->id)
                                        {{-- Edit Mode --}}
                                        <div class="mb-2">
                                            <input type="text" wire:model="editAuthorName" class="form-control form-control-sm mb-2" placeholder="Yazar adı">
                                            <textarea wire:model="editBody" class="form-control form-control-sm" rows="3" placeholder="Yorum içeriği"></textarea>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button wire:click="saveEdit" class="btn btn-sm btn-success">
                                                <i class="fas fa-check"></i> Kaydet
                                            </button>
                                            <button wire:click="cancelEdit" class="btn btn-sm btn-secondary">
                                                <i class="fas fa-times"></i> İptal
                                            </button>
                                        </div>
                                    @else
                                        {{-- View Mode --}}
                                        <div class="mb-1">
                                            <strong>{{ $review->author_name ?? $review->user?->name }}</strong>
                                        </div>
                                        <div class="text-muted small" style="max-width: 300px;">
                                            {{ \Str::limit($review->review_body, 100) }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-azure-lt">{{ class_basename($review->reviewable_type) }}</span>
                                    <br>
                                    @if($review->reviewable)
                                        @php
                                            $title = $review->reviewable->title ?? $review->reviewable->name ?? 'İçerik #' . $review->reviewable_id;
                                            if (is_array($title)) {
                                                $currentLocale = session('tenant_locale', config('app.locale'));
                                                $title = $title[$currentLocale] ?? reset($title) ?? 'İçerik #' . $review->reviewable_id;
                                            }
                                        @endphp
                                        <div class="text-muted small mt-1" style="max-width: 200px;">
                                            {{ $title }}
                                        </div>
                                    @else
                                        <code class="small">ID: {{ $review->reviewable_id }}</code>
                                    @endif
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
                                        @if($editingId !== $review->id)
                                            @if(!$review->is_approved)
                                                <a href="javascript:void(0);"
                                                    wire:click="approveReview({{ $review->id }})"
                                                    data-bs-toggle="tooltip" title="Onayla">
                                                    <i class="fa-solid fa-check link-success fa-lg"></i>
                                                </a>
                                            @endif
                                            <a href="javascript:void(0);"
                                                wire:click="startEdit({{ $review->id }})"
                                                data-bs-toggle="tooltip" title="Düzenle">
                                                <i class="fa-solid fa-edit link-primary fa-lg"></i>
                                            </a>
                                            <a href="javascript:void(0);"
                                                wire:click="deleteReview({{ $review->id }})"
                                                wire:confirm="Bu yorumu silmek istediğinizden emin misiniz?"
                                                data-bs-toggle="tooltip" title="{{ __('admin.delete') }}">
                                                <i class="fa-solid fa-trash link-danger fa-lg"></i>
                                            </a>
                                        @endif
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
    @endif

    <!-- Ratings Tab -->
    @if($activeTab === 'ratings')
    <div class="card">
        <div class="card-body p-0">
            <!-- Header -->
            <div class="row mx-2 my-3">
                <div class="col">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-star text-yellow me-2"></i> Puan Yönetimi
                    </h3>
                </div>
                <div class="col">
                    <div class="d-flex align-items-center justify-content-end gap-3">
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
                            <th>Kullanıcı</th>
                            <th>Model</th>
                            <th style="width: 120px">Puan</th>
                            <th>
                                <button class="table-sort {{ $sortField === 'created_at' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('created_at')">Tarih</button>
                            </th>
                            <th class="text-center" style="width: 100px">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ratings as $rating)
                            <tr wire:key="rating-row-{{ $rating->id }}">
                                <td class="sort-id small">{{ $rating->id }}</td>
                                <td>
                                    @if($rating->user_id)
                                        <strong>{{ $rating->user?->name ?? 'Anonim' }}</strong>
                                        <br>
                                        <code class="small">User #{{ $rating->user_id }}</code>
                                    @else
                                        <span class="badge bg-info-lt">Fallback (5⭐)</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-azure-lt">{{ class_basename($rating->ratable_type) }}</span>
                                    <br>
                                    @if($rating->ratable)
                                        @php
                                            $title = $rating->ratable->title ?? $rating->ratable->name ?? 'İçerik #' . $rating->ratable_id;
                                            if (is_array($title)) {
                                                $currentLocale = session('tenant_locale', config('app.locale'));
                                                $title = $title[$currentLocale] ?? reset($title) ?? 'İçerik #' . $rating->ratable_id;
                                            }
                                        @endphp
                                        <div class="text-muted small mt-1" style="max-width: 200px;">
                                            {{ $title }}
                                        </div>
                                    @else
                                        <code class="small">ID: {{ $rating->ratable_id }}</code>
                                    @endif
                                </td>
                                <td>
                                    @if($editingRatingId === $rating->id)
                                        {{-- Edit Mode --}}
                                        <div class="d-flex gap-2 align-items-center">
                                            <select wire:model="editRatingValue" class="form-control form-control-sm" style="width: 80px;">
                                                <option value="1">1 ⭐</option>
                                                <option value="2">2 ⭐</option>
                                                <option value="3">3 ⭐</option>
                                                <option value="4">4 ⭐</option>
                                                <option value="5">5 ⭐</option>
                                            </select>
                                            <button wire:click="saveEditRating" class="btn btn-sm btn-success">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button wire:click="cancelEditRating" class="btn btn-sm btn-secondary">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @else
                                        {{-- View Mode --}}
                                        <div class="d-flex gap-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="{{ $i <= $rating->rating_value ? 'fas' : 'far' }} fa-star text-yellow"></i>
                                            @endfor
                                        </div>
                                        <small class="text-muted">{{ $rating->rating_value }}/5</small>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $rating->created_at->format('d.m.Y H:i') }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center gap-3 justify-content-center">
                                        @if($editingRatingId !== $rating->id)
                                            <a href="javascript:void(0);"
                                                wire:click="startEditRating({{ $rating->id }})"
                                                data-bs-toggle="tooltip" title="Düzenle">
                                                <i class="fa-solid fa-edit link-primary fa-lg"></i>
                                            </a>
                                            <a href="javascript:void(0);"
                                                wire:click="deleteRating({{ $rating->id }})"
                                                wire:confirm="Bu puanı silmek istediğinizden emin misiniz?"
                                                data-bs-toggle="tooltip" title="{{ __('admin.delete') }}">
                                                <i class="fa-solid fa-trash link-danger fa-lg"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="empty">
                                        <p class="empty-title">Puan bulunamadı</p>
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
            @if ($ratings->hasPages())
                {{ $ratings->links() }}
            @else
                <div class="d-flex justify-content-between align-items-center mb-0">
                    <p class="small text-muted mb-0">
                        Toplam <span class="fw-semibold">{{ $ratings->total() }}</span> puan
                    </p>
                </div>
            @endif
        </div>
    </div>
    @endif
</div>
