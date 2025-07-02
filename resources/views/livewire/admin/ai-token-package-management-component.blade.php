@include('ai::admin.helper')
<div>
    @include('admin.partials.error_message')
    
    @if($showForm)
    <form wire:submit.prevent="save">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    {{ $packageId ? __('ai::admin.edit') . ' ' . __('ai::admin.package') : __('ai::admin.add_new_package') }}
                </h3>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <!-- Sol Kolon - Temel Bilgiler -->
                    <div class="col-12 col-lg-6">
                        <!-- Paket Adı -->
                        <div class="form-floating mb-3">
                            <input type="text" wire:model="name"
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="{{ __('ai::admin.package_name_placeholder') }}">
                            <label>
                                {{ __('ai::admin.package_name') }} *
                            </label>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Token Miktarı ve Fiyat -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="number" wire:model="token_amount"
                                        class="form-control @error('token_amount') is-invalid @enderror"
                                        placeholder="1000">
                                    <label>{{ __('ai::admin.token_amount') }} *</label>
                                    @error('token_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <input type="number" step="0.01" wire:model="price"
                                        class="form-control @error('price') is-invalid @enderror"
                                        placeholder="29.99">
                                    <label>{{ __('ai::admin.price') }} *</label>
                                    @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating mb-3">
                                    <select wire:model="currency"
                                        class="form-control @error('currency') is-invalid @enderror"
                                        data-choices 
                                        data-choices-search="false"
                                        data-choices-filter="true">
                                        <option value="TRY">TRY</option>
                                        <option value="USD">USD</option>
                                        <option value="EUR">EUR</option>
                                    </select>
                                    <label>{{ __('ai::admin.currency') }}</label>
                                    @error('currency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Açıklama -->
                        <div class="form-floating mb-3">
                            <textarea wire:model="description" class="form-control" data-bs-toggle="autosize"
                                placeholder="{{ __('ai::admin.package_description_placeholder') }}"></textarea>
                            <label>{{ __('ai::admin.description') }}</label>
                        </div>

                        <!-- Sıralama -->
                        <div class="form-floating mb-3">
                            <input type="number" wire:model="sort_order"
                                class="form-control @error('sort_order') is-invalid @enderror"
                                placeholder="0">
                            <label>{{ __('ai::admin.sort_order') }}</label>
                            @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-hint">{{ __('ai::admin.sort_order_hint') }}</small>
                        </div>

                        <!-- Durum -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                        <input type="checkbox" wire:model="is_active" value="1" />
                                        <div class="state p-success p-on ms-2">
                                            <label>{{ __('ai::admin.active') }}</label>
                                        </div>
                                        <div class="state p-danger p-off ms-2">
                                            <label>{{ __('ai::admin.not_active') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="pretty p-default p-curve p-toggle p-smooth ms-1">
                                        <input type="checkbox" wire:model="is_popular" value="1" />
                                        <div class="state p-warning p-on ms-2">
                                            <label>{{ __('ai::admin.popular_package') }}</label>
                                        </div>
                                        <div class="state p-off ms-2">
                                            <label>{{ __('ai::admin.not_popular') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sağ Kolon - Özellikler -->
                    <div class="col-12 col-lg-6">
                        <h4 class="mb-3">{{ __('ai::admin.package_features') }}</h4>
                        
                        <!-- Yeni Özellik Ekleme -->
                        <div class="mb-4">
                            <div class="input-group">
                                <div class="form-floating flex-fill">
                                    <input type="text" wire:model="newFeature" class="form-control" 
                                           placeholder="{{ __('ai::admin.add_feature_placeholder') }}">
                                    <label>{{ __('ai::admin.feature_title') }}</label>
                                </div>
                                <button class="btn btn-primary" type="button" wire:click="addFeature">
                                    <i class="fas fa-plus me-1"></i>
                                    {{ __('ai::admin.add') }}
                                </button>
                            </div>
                        </div>
                        
                        <!-- Özellik Listesi -->
                        @if(count($features) > 0)
                        <div class="list-group">
                            @foreach($features as $index => $feature)
                            <div class="list-group-item">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fas fa-check-circle text-success"></i>
                                        </div>
                                        <div>{{ $feature }}</div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            wire:click="removeFeature({{ $index }})">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="empty">
                            <div class="empty-img">
                                <i class="fas fa-list fa-4x text-muted"></i>
                            </div>
                            <p class="empty-title">{{ __('ai::admin.no_features_yet') }}</p>
                            <p class="empty-subtitle text-muted">
                                {{ __('ai::admin.add_features_help') }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card-footer text-end">
                <button type="button" class="btn btn-ghost-danger" wire:click="cancel">
                    {{ __('ai::admin.cancel') }}
                </button>
                <button type="submit" class="btn btn-primary">
                    <span wire:loading.remove wire:target="save">
                        {{ $packageId ? __('ai::admin.update') : __('ai::admin.save') }}
                    </span>
                    <span wire:loading wire:target="save">
                        {{ __('ai::admin.loading') }}...
                    </span>
                </button>
            </div>
        </div>
    </form>
    @else
    <!-- Paketlerin Listesi -->
    <div class="card">
        <div class="card-body">
            <!-- Header Bölümü -->
            <div class="row mb-3">
                <!-- Arama Kutusu -->
                <div class="col">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" wire:model.live="search" class="form-control"
                            placeholder="{{ __('ai::admin.search_packages_placeholder') }}">
                    </div>
                </div>
                <!-- Ortadaki Loading -->
                <div class="col position-relative">
                    <div wire:loading
                        wire:target="render, search, gotoPage, previousPage, nextPage, delete, toggleActive"
                        class="position-absolute top-50 start-50 translate-middle text-center"
                        style="width: 100%; max-width: 250px; z-index: 10;">
                        <div class="small text-muted mb-2">{{ __('ai::admin.loading') }}</div>
                        <div class="progress mb-1">
                            <div class="progress-bar progress-bar-indeterminate"></div>
                        </div>
                    </div>
                </div>
                <!-- Sağ Taraf -->
                <div class="col">
                    <div class="d-flex align-items-center justify-content-end gap-3">
                        <button type="button" class="btn btn-primary" wire:click="create">
                            <i class="fas fa-plus me-1"></i>
                            {{ __('ai::admin.add_new_package') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tablo Bölümü -->
            <div id="table-default" class="table-responsive">
                <table class="table table-vcenter card-table table-hover text-nowrap datatable">
                    <thead>
                        <tr>
                            <th style="width: 50px">
                                <button
                                    class="table-sort {{ $sortField === 'id' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('id')">
                                    ID
                                </button>
                            </th>
                            <th>
                                <button
                                    class="table-sort {{ $sortField === 'name' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('name')">
                                    {{ __('ai::admin.package_name') }}
                                </button>
                            </th>
                            <th style="width: 120px">
                                <button
                                    class="table-sort {{ $sortField === 'token_amount' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('token_amount')">
                                    {{ __('ai::admin.tokens') }}
                                </button>
                            </th>
                            <th style="width: 120px">
                                <button
                                    class="table-sort {{ $sortField === 'price' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('price')">
                                    {{ __('ai::admin.price') }}
                                </button>
                            </th>
                            <th class="text-center" style="width: 80px">
                                <button
                                    class="table-sort {{ $sortField === 'is_active' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('is_active')">
                                    {{ __('ai::admin.status') }}
                                </button>
                            </th>
                            <th class="text-center" style="width: 80px">
                                <button
                                    class="table-sort {{ $sortField === 'sort_order' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('sort_order')">
                                    {{ __('ai::admin.order') }}
                                </button>
                            </th>
                            <th class="text-center" style="width: 160px">{{ __('ai::admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-tbody">
                        @forelse($packages as $package)
                        <tr class="hover-trigger" wire:key="row-{{ $package->id }}">
                            <td class="sort-id small">
                                <div class="hover-toggle">
                                    <span class="hover-hide">#{{ $package->id }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex py-1 align-items-center">
                                    <div class="flex-fill">
                                        <div class="font-weight-medium">
                                            {{ $package->name }}
                                            @if($package->is_popular)
                                            <span class="badge bg-yellow ms-1">
                                                <i class="fas fa-star"></i>
                                            </span>
                                            @endif
                                        </div>
                                        @if($package->description)
                                        <div class="text-muted">{{ Str::limit($package->description, 60) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-blue">{{ number_format($package->token_amount) }}</span>
                            </td>
                            <td>
                                <div class="text-green font-weight-medium">
                                    {{ number_format($package->price, 2) }} {{ $package->currency }}
                                </div>
                                <div class="text-muted small">
                                    ~{{ number_format($package->price / $package->token_amount, 4) }} {{ $package->currency }}/token
                                </div>
                            </td>
                            <td wire:key="status-{{ $package->id }}" class="text-center align-middle">
                                <button wire:click="toggleActive({{ $package->id }})"
                                    class="btn btn-icon btn-sm {{ $package->is_active ? 'text-muted bg-transparent' : 'text-red bg-transparent' }}">
                                    <!-- Loading Durumu -->
                                    <div wire:loading wire:target="toggleActive({{ $package->id }})"
                                        class="spinner-border spinner-border-sm">
                                    </div>
                                    <!-- Normal Durum: Aktif/Pasif İkonları -->
                                    <div wire:loading.remove wire:target="toggleActive({{ $package->id }})">
                                        @if($package->is_active)
                                        <i class="fas fa-check"></i>
                                        @else
                                        <i class="fas fa-times"></i>
                                        @endif
                                    </div>
                                </button>
                            </td>
                            <td class="text-center">
                                <span class="text-muted">{{ $package->sort_order }}</span>
                            </td>
                            <td class="text-center align-middle">
                                <div class="container">
                                    <div class="row">
                                        <div class="col">
                                            <a href="javascript:void(0);" wire:click="edit({{ $package->id }})"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('ai::admin.edit') }}">
                                                <i class="fa-solid fa-pen-to-square link-secondary fa-lg"></i>
                                            </a>
                                        </div>
                                        <div class="col lh-1">
                                            <div class="dropdown mt-1">
                                                <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa-solid fa-bars-sort fa-flip-horizontal fa-lg"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a href="javascript:void(0);" 
                                                       wire:click="$dispatch('showDeleteModal', {
                                                            module: 'ai-token-package',
                                                            id: {{ $package->id }},
                                                            title: '{{ addslashes($package->name) }}'
                                                        })" 
                                                       class="dropdown-item link-danger">
                                                        {{ __('ai::admin.delete') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="empty">
                                    <p class="empty-title">{{ __('ai::admin.no_packages_found') }}</p>
                                    <p class="empty-subtitle text-muted">
                                        {{ __('ai::admin.no_packages_found_subtitle') }}
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{ $packages->links() }}
    </div>

    <livewire:modals.delete-modal />
    @endif
</div>