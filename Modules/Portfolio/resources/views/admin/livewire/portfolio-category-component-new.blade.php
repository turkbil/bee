@include('portfolio::admin.helper')
<div>
    <div class="card mb-3">
        <div class="card-body">
            <form wire:submit="quickAdd">
                <div class="row align-items-center flex-column flex-md-row text-center text-md-start">
                    <div class="col-12 col-md-auto mb-2 mb-md-0">
                        <span class="fw-bold">{{ __('portfolio::admin.quick_add_category') }}:</span>
                    </div>
                    <div class="col-12 col-md mb-2 mb-md-0">
                        <div class="input-icon">
                            <input type="text" wire:model="title"
                                class="form-control @error('title') is-invalid @enderror"
                                placeholder="{{ __('portfolio::admin.category_name_placeholder') }}"
                                autocomplete="off">
                            <span class="input-icon-addon">
                                <i class="fas fa-tag"></i>
                            </span>
                        </div>
                        @error('title')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 col-md-auto">
                        <button type="submit" class="btn btn-muted w-100 w-md-auto" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="quickAdd">
                                {{ __('portfolio::admin.add_category') }}
                            </span>
                            <span wire:loading wire:target="quickAdd">
                                {{ __('portfolio::admin.adding') }}...
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3" id="sortable-list">
        @forelse($categories as $index => $category)
        <div class="col-12 col-sm-4 col-xxl-4 category-wrapper"
            wire:key="category-{{ $category->portfolio_category_id }}" id="item-{{ $category->portfolio_category_id }}"
            data-id="{{ $category->portfolio_category_id }}">

            <!-- Card Yapısı -->
            <div class="card">
                <div class="card-body p-2">
                    <!-- Padding'i azaltmak için p-2 kullandık -->
                    <div class="d-flex gap-2">
                        <!-- Sol Kolon - Sayı -->
                        <div class="d-flex align-items-center">
                            <span class="bg-primary-lt rounded-2 d-flex align-items-center justify-content-center"
                                style="width: 3rem; height: 3rem;">
                                <span class="order-number" style="font-size: 1.5rem">{{ $index + 1 }}</span>
                            </span>
                        </div>

                        <!-- Sağ Kolon - İçerik -->
                        <div class="flex-grow-1">
                            <div class="h2 mb-0">{{ $category->title }}</div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted small">
                                    {{ $category->portfolios_count }} {{ __('portfolio::admin.content_count') }}
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <button wire:click="toggleActive({{ $category->portfolio_category_id }})"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="{{ $category->is_active ? __('portfolio::admin.make_inactive') : __('portfolio::admin.make_active') }}"
                                        class="btn btn-icon btn-sm {{ $category->is_active ? 'text-muted bg-transparent' : 'text-red bg-transparent' }}">
                                        <!-- Loading Durumu -->
                                        <div wire:loading
                                            wire:target="toggleActive({{ $category->portfolio_category_id }})"
                                            class="spinner-border spinner-border-sm">
                                        </div>
                                        <!-- Normal Durum: Aktif/Pasif İkonları -->
                                        <div wire:loading.remove
                                            wire:target="toggleActive({{ $category->portfolio_category_id }})">
                                            @if($category->is_active)
                                            <i class="fas fa-check"></i>
                                            @else
                                            <i class="fas fa-times"></i>
                                            @endif
                                        </div>
                                    </button>
                                    <a href="{{ route('admin.portfolio.category.manage', $category->portfolio_category_id) }}"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('portfolio::admin.edit') }}"
                                        class="btn btn-icon btn-sm">
                                        <i class="fa-solid fa-pen-to-square link-secondary fa-lg"></i>
                                    </a>
                                    <div class="dropdown">
                                        <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="fa-solid fa-bars-sort fa-flip-horizontal fa-lg"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            @if($category->portfolios_count == 0)
                                            <a href="#"
                                                wire:click.prevent="delete({{ $category->portfolio_category_id }})"
                                                class="dropdown-item link-danger">
                                                <i class="fas fa-trash me-2"></i> {{ __('portfolio::admin.delete') }}
                                            </a>
                                            @else
                                            <a href="javascript:void(0);" wire:click="$dispatch('showCategoryDeleteModal', {
                                                    module: 'portfolio', 
                                                    id: {{ $category->portfolio_category_id }}, 
                                                    title: '{{ $category->title }}'
                                                })" class="dropdown-item link-danger">
                                                <i class="fas fa-trash me-2"></i> {{ __('portfolio::admin.delete') }}
                                            </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Progress Bar (Card'ın altına eklendi) -->
                <div class="progress progress-sm card-progress" style="height: 2px;">
                    <div class="progress-bar bg-blue"
                        style="height: 2px; opacity: 0.4; width: {{ ($category->portfolios_count / $maxPortfoliosCount) * 80 }}%"
                        role="progressbar"
                        aria-valuenow="{{ ($category->portfolios_count / $maxPortfoliosCount) * 80 }}" aria-valuemin="0"
                        aria-valuemax="100" aria-label="{{ ($category->portfolios_count / $maxPortfoliosCount) * 80 }}">
                        <span class="visually-hidden">{{ ($category->portfolios_count / $maxPortfoliosCount) * 80 }}%
                            Complete</span>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="empty">
                <p class="empty-title">{{ __('portfolio::admin.no_categories_yet') }}</p>
                <p class="empty-subtitle text-muted">
                    {{ __('portfolio::admin.add_category_instruction') }}
                </p>
            </div>
        </div>
        @endforelse
    </div>

    <livewire:modals.category-delete-modal />

</div>

@push('scripts')
<script
    src="{{ asset('admin-assets/libs/sortable/sortable.min.js') }}?v={{ filemtime(public_path('admin-assets/libs/sortable/sortable.min.js')) }}">
</script>
<script
    src="{{ asset('admin-assets/libs/sortable/sortable-settings.js') }}?v={{ filemtime(public_path('admin-assets/libs/sortable/sortable-settings.js')) }}">
</script>
@endpush
