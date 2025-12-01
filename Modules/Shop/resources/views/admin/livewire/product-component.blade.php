@php
    View::share('pretitle', __('shop::admin.products'));
@endphp

<div class="product-component-wrapper">
    <div class="card">
        @include('shop::admin.helper')

        <div class="card-body p-0">
            <!-- Header Bölümü -->
            <div class="row mx-2 my-3">
                <!-- Arama Kutusu -->
                <div class="col-md-3">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" wire:model.live="search" class="form-control"
                            placeholder="{{ __('shop::admin.search_products') }}">
                    </div>
                </div>

                <!-- Kategori Dropdown -->
                <div class="col-md-2">
                    <div class="input-group">
                        <select wire:model.live="selectedCategory" class="form-select">
                            <option value="">{{ __('shop::admin.all_categories') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category['category_id'] }}">
                                    {{ $category['title'] }}
                                </option>
                            @endforeach
                        </select>
                        @if($selectedCategory)
                            <button wire:click="clearCategoryFilter" class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Ürün Sayısı Gösterimi -->
                <div class="col-md-2">
                    <div class="d-flex align-items-center h-100">
                        <span class="text-muted">
                            <i class="fas fa-box me-2"></i>
                            <strong>{{ $products->total() }}</strong> {{ __('shop::admin.products') }}
                            @if($selectedCategory)
                                <span class="badge bg-primary ms-2">{{ __('shop::admin.filtered') }}</span>
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Ortadaki Loading Indicator -->
                <div class="col-md-3 position-relative">
                    <div wire:loading
                        wire:target="render, search, perPage, sortBy, selectedCategory, gotoPage, previousPage, nextPage, toggleActive, selectedItems, selectAll, bulkDelete, bulkToggleSelected"
                        class="position-absolute top-50 start-50 translate-middle text-center"
                        style="width: 100%; max-width: 250px;">
                        <div class="small text-muted mb-2">{{ __('admin.updating') }}</div>
                        <div class="progress mb-1">
                            <div class="progress-bar progress-bar-indeterminate"></div>
                        </div>
                    </div>
                </div>

                <!-- Sağ Taraf (Hızlı Düzenle + Sayfa Adeti) -->
                <div class="col-md-2">
                    <div class="d-flex align-items-center justify-content-end gap-3">
                        <!-- Mod Toggle: Düzenleme (default) / Detay -->
                        <button wire:click="toggleQuickEditMode"
                            class="btn btn-sm {{ $quickEditMode ? 'btn-primary' : 'btn-outline-secondary' }}"
                            data-bs-toggle="tooltip"
                            title="{{ $quickEditMode ? 'Detay moduna geç: Kategori, marka ve SKU bilgilerini görüntüle' : 'Düzenleme moduna geç: Hızlı fiyat/stok düzenleme' }}">
                            <i class="fas fa-{{ $quickEditMode ? 'list' : 'edit' }} me-1"></i>
                            {{ $quickEditMode ? 'Detay Modu' : 'Düzenleme Modu' }}
                        </button>

                        <div style="width: 80px; min-width: 80px">
                            <select wire:model.live="perPage" class="form-control listing-filter-select" data-choices
                                data-choices-search="false" data-choices-filter="true">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tablo Bölümü -->
            <div id="table-default" class="table-responsive">
                <table class="table table-vcenter card-table table-hover text-nowrap datatable">
                    <thead>
                        <tr>
                            <th style="width: 50px">
                                @if($selectedCategory)
                                    <i class="fas fa-grip-vertical text-muted" data-bs-toggle="tooltip" title="{{ __('shop::admin.drag_to_sort') }}"></i>
                                @endif
                            </th>
                            <th style="width: 50px">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="checkbox"
                                           wire:model.live="selectAll"
                                           class="form-check-input"
                                           id="selectAllCheckbox"
                                           x-data="{
                                               indeterminate: {{ count($selectedItems) > 0 && !$selectAll ? 'true' : 'false' }}
                                           }"
                                           x-init="$el.indeterminate = indeterminate"
                                           x-effect="$el.indeterminate = ({{ count($selectedItems) }} > 0 && !{{ $selectAll ? 'true' : 'false' }})"
                                           @checked($selectAll)>
                                    <button
                                        class="table-sort {{ $sortField === 'product_id' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                        wire:click="sortBy('product_id')">
                                    </button>
                                </div>
                            </th>
                            <th class="text-center" style="width: 60px">
                                <i class="fas fa-boxes text-muted" data-bs-toggle="tooltip" title="{{ __('shop::admin.variants') }}"></i>
                            </th>
                            <th>
                                <button
                                    class="table-sort {{ $sortField === 'title' ? ($sortDirection === 'asc' ? 'asc' : 'desc') : '' }}"
                                    wire:click="sortBy('title')">
                                    {{ __('shop::admin.product_title') }}
                                </button>
                            </th>
                            @if(!$quickEditMode)
                                <th>{{ __('shop::admin.category') }}</th>
                                <th>{{ __('shop::admin.brand') }}</th>
                            @endif
                            <th class="text-end">{{ __('shop::admin.price') }}</th>
                            <th class="text-center" style="width: 100px">Stok</th>
                            <th style="width: 80px">{{ __('shop::admin.status') }}</th>
                            <th class="text-center" style="width: 160px">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="table-tbody {{ $selectedCategory ? 'sortable-list' : '' }}" x-data="{ openVariants: {} }" id="sortable-products">
                        @forelse($products as $product)
                            <tr class="hover-trigger {{ $selectedCategory ? 'sortable-item' : '' }}"
                                wire:key="product-row-{{ $product->product_id }}"
                                data-product-id="{{ $product->product_id }}"
                                x-init="openVariants[{{ $product->product_id }}] = false">
                                <td class="text-center">
                                    @if($selectedCategory)
                                        <i class="fas fa-grip-vertical text-muted sortable-handle" style="cursor: grab;"></i>
                                    @endif
                                </td>
                                <td class="sort-id small">
                                    <div class="hover-toggle">
                                        <span class="hover-hide">{{ $product->product_id }}</span>
                                        <input type="checkbox"
                                               wire:model.live="selectedItems"
                                               value="{{ $product->product_id }}"
                                               class="form-check-input hover-show"
                                               id="checkbox-{{ $product->product_id }}"
                                               @checked(in_array($product->product_id, $selectedItems))>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($product->childProducts && $product->childProducts->count() > 0)
                                        <button @click="openVariants[{{ $product->product_id }}] = !openVariants[{{ $product->product_id }}]"
                                            class="btn btn-sm btn-ghost-secondary variant-toggle-btn"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            :title="openVariants[{{ $product->product_id }}] ? '{{ __('shop::admin.hide_variants') }}' : '{{ __('shop::admin.show_variants') }}'">
                                            <i class="fas fa-boxes fa-sm me-1"></i>
                                            <span class="badge bg-primary">{{ $product->childProducts->count() }}</span>
                                            <i class="fas ms-1"
                                               :class="openVariants[{{ $product->product_id }}] ? 'fa-chevron-up' : 'fa-chevron-down'"
                                               style="font-size: 0.7rem;"></i>
                                        </button>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td wire:key="title-{{ $product->product_id }}" class="position-relative">
                                    @if ($editingTitleId === $product->product_id)
                                        <div class="d-flex align-items-center gap-3" x-data
                                            @click.outside="$wire.updateTitleInline()">
                                            <div class="flexible-input-wrapper">
                                                <input type="text" wire:model.defer="newTitle"
                                                    class="form-control form-control-sm flexible-input"
                                                    placeholder="{{ __('shop::admin.product_title') }}"
                                                    wire:keydown.enter="updateTitleInline"
                                                    wire:keydown.escape="$set('editingTitleId', null)"
                                                    x-init="$nextTick(() => {
                                                        $el.focus();
                                                        $el.style.width = '20px';
                                                        $el.style.width = ($el.scrollWidth + 2) + 'px';
                                                    })"
                                                    x-on:input="
                                                        $el.style.width = '20px';
                                                        $el.style.width = ($el.scrollWidth + 2) + 'px'
                                                    "
                                                    style="min-width: 60px; max-width: 100%;">
                                            </div>
                                            <button class="btn px-2 py-1 btn-outline-success"
                                                wire:click="updateTitleInline">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn px-2 py-1 btn-outline-danger"
                                                wire:click="$set('editingTitleId', null)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-3">
                                                @php
                                                    $heroImage = getFirstMediaWithFallback($product);
                                                @endphp
                                                @if($heroImage)
                                                    <img src="{{ $heroImage->getUrl('thumb') }}"
                                                         alt="Product"
                                                         style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                                @else
                                                    <div style="width: 40px; height: 40px; background: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-image text-muted" style="font-size: 0.9rem;"></i>
                                                    </div>
                                                @endif
                                                <span class="editable-title pr-4">
                                                    {{ $product->getTranslated('title', $currentSiteLocale) ?? $product->getTranslated('title', 'tr') }}
                                                </span>
                                                <button class="btn btn-sm px-2 py-1 edit-icon ms-2"
                                                    wire:click="startEditingTitle({{ $product->product_id }}, '{{ addslashes($product->getTranslated('title', $currentSiteLocale) ?? $product->getTranslated('title', 'tr')) }}')">
                                                    <i class="fas fa-pen"></i>
                                                </button>
                                            </div>
                                            @if(!$quickEditMode && $product->sku)
                                                <span class="badge bg-secondary-lt ms-2">{{ $product->sku }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                @if(!$quickEditMode)
                                    <td>
                                        @if($product->category)
                                            {{ $product->category->getTranslated('title', $currentSiteLocale) ?? '—' }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->brand)
                                            {{ $product->brand->getTranslated('title', $currentSiteLocale) ?? '—' }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                @endif
                                <td class="text-end" wire:key="price-{{ $product->product_id }}">
                                    @if ($quickEditMode)
                                        {{-- Quick Edit Mode: Always editable inputs --}}
                                        <div class="d-flex align-items-center justify-content-end gap-2">
                                            <input type="number" step="0.01"
                                                value="{{ $product->base_price }}"
                                                wire:blur="updateProductField({{ $product->product_id }}, 'base_price', $event.target.value)"
                                                wire:keydown.enter="updateProductField({{ $product->product_id }}, 'base_price', $event.target.value)"
                                                class="form-control form-control-sm text-end quick-edit-input"
                                                style="width: 90px;"
                                                placeholder="0.00">
                                            <select
                                                wire:change="updateProductField({{ $product->product_id }}, 'currency', $event.target.value)"
                                                class="form-select form-select-sm quick-edit-select"
                                                style="width: 75px;">
                                                <option value="TRY" {{ ($product->currency ?? 'TRY') === 'TRY' ? 'selected' : '' }}>TRY</option>
                                                <option value="USD" {{ $product->currency === 'USD' ? 'selected' : '' }}>USD</option>
                                                <option value="EUR" {{ $product->currency === 'EUR' ? 'selected' : '' }}>EUR</option>
                                            </select>
                                        </div>
                                    @elseif ($editingPriceId === $product->product_id)
                                        <div class="d-flex align-items-center justify-content-end gap-2" x-data
                                            @click.outside="$wire.updatePriceInline()">
                                            <input type="number" step="0.01" wire:model.defer="newPrice"
                                                class="form-control form-control-sm text-end"
                                                style="width: 100px;"
                                                placeholder="0.00"
                                                wire:keydown.enter="updatePriceInline"
                                                wire:keydown.escape="cancelPriceEdit"
                                                x-init="$nextTick(() => $el.focus())">
                                            <select wire:model.defer="newCurrency" class="form-select form-select-sm"
                                                style="width: 80px;">
                                                <option value="TRY">TRY</option>
                                                <option value="USD">USD</option>
                                                <option value="EUR">EUR</option>
                                            </select>
                                            <button class="btn px-2 py-1 btn-outline-success btn-sm"
                                                wire:click="updatePriceInline">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn px-2 py-1 btn-outline-danger btn-sm"
                                                wire:click="cancelPriceEdit">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center justify-content-end gap-2">
                                            @if ($product->price_display_mode === 'show' && $product->base_price && $product->base_price > 0)
                                                @php
                                                    // ✨ OTOMATIK İNDİRİM SİSTEMİ (Admin Panel)
                                                    $compareAtPrice = $product->compare_at_price;
                                                    $discountPercentage = null;

                                                    // Eğer compare_at_price yoksa veya base_price'dan küçükse, otomatik hesapla
                                                    if (!$compareAtPrice || $compareAtPrice <= $product->base_price) {
                                                        // Hedef indirim yüzdesi (badge için - SABİT: %5, %10, %15, %20)
                                                        $discountPercentage = (($product->product_id % 4) * 5 + 5);

                                                        // Eski fiyatı hesapla (ters formül: old = new / (1 - discount))
                                                        $compareAtPrice = $product->base_price / (1 - ($discountPercentage / 100));

                                                        // Yuvarlama (00 veya 50)
                                                        $lastTwo = $compareAtPrice % 100;
                                                        if ($lastTwo <= 24) {
                                                            $compareAtPrice = floor($compareAtPrice / 100) * 100;
                                                        } elseif ($lastTwo <= 74) {
                                                            $compareAtPrice = floor($compareAtPrice / 100) * 100 + 50;
                                                        } else {
                                                            $compareAtPrice = ceil($compareAtPrice / 100) * 100;
                                                        }
                                                    } else {
                                                        // Manuel compare_at_price var, gerçek indirim yüzdesini hesapla
                                                        $discountPercentage = round((($compareAtPrice - $product->base_price) / $compareAtPrice) * 100);
                                                    }
                                                @endphp

                                                <div class="d-flex flex-column align-items-end position-relative">
                                                    {{-- İndirim Badge (sadece %5+ indirim varsa) --}}
                                                    @if($discountPercentage && $discountPercentage >= 5)
                                                        <span class="badge bg-danger position-absolute" style="top: -8px; right: -8px; font-size: 0.7rem;">
                                                            -%{{ $discountPercentage }}
                                                        </span>
                                                    @endif

                                                    <span class="fw-semibold editable-price">{{ formatPrice($product->base_price, $product->currency ?? 'TRY') }}</span>
                                                    @if ($compareAtPrice && $compareAtPrice > $product->base_price)
                                                        <small class="text-muted text-decoration-line-through">
                                                            {{ formatPrice($compareAtPrice, $product->currency ?? 'TRY') }}
                                                        </small>
                                                    @endif
                                                </div>
                                            @elseif ($product->price_display_mode === 'request')
                                                <span class="badge bg-secondary editable-price">{{ __('shop::admin.price_on_request') }}</span>
                                            @else
                                                <span class="badge bg-warning text-dark editable-price">{{ __('shop::admin.price_hidden') }}</span>
                                            @endif
                                            <button class="btn btn-sm px-2 py-1 edit-icon"
                                                wire:click="startEditingPrice({{ $product->product_id }}, '{{ $product->base_price }}', '{{ $product->currency ?? 'TRY' }}')">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center" wire:key="stock-{{ $product->product_id }}">
                                    @if ($quickEditMode)
                                        {{-- Quick Edit Mode: Always editable input --}}
                                        <input type="number" step="1" min="0"
                                            value="{{ $product->current_stock }}"
                                            wire:blur="updateProductField({{ $product->product_id }}, 'current_stock', $event.target.value)"
                                            wire:keydown.enter="updateProductField({{ $product->product_id }}, 'current_stock', $event.target.value)"
                                            class="form-control form-control-sm text-center quick-edit-input"
                                            style="width: 70px;"
                                            placeholder="0">
                                    @elseif ($editingStockId === $product->product_id)
                                        <div class="d-flex flex-column gap-2 align-items-center" x-data
                                            @click.outside="$wire.updateStockInline()">
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox"
                                                    wire:model.live="newStockTracking"
                                                    id="stockTracking-{{ $product->product_id }}">
                                                <label class="form-check-label small text-nowrap" for="stockTracking-{{ $product->product_id }}">
                                                    Takip
                                                </label>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <input type="number" step="1" min="0" wire:model.defer="newStock"
                                                    class="form-control form-control-sm text-center"
                                                    style="width: 70px;"
                                                    placeholder="0"
                                                    wire:keydown.enter="updateStockInline"
                                                    wire:keydown.escape="cancelStockEdit"
                                                    :disabled="!$wire.newStockTracking"
                                                    x-init="$wire.newStockTracking && $nextTick(() => $el.focus())">
                                                <button class="btn px-2 py-1 btn-outline-success btn-sm"
                                                    wire:click="updateStockInline">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button class="btn px-2 py-1 btn-outline-danger btn-sm"
                                                    wire:click="cancelStockEdit">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            @if ($product->stock_tracking)
                                                @php
                                                    $hasNoStockData = $product->current_stock == 0;
                                                    $isLowStock = $product->current_stock > 0 && $product->current_stock <= $product->low_stock_threshold;
                                                @endphp
                                                <div class="d-flex flex-column align-items-center">
                                                    <span class="fw-semibold editable-stock {{ $hasNoStockData ? 'text-muted' : ($isLowStock ? 'text-warning' : '') }}">
                                                        {{ $hasNoStockData ? '0' : $product->current_stock }}
                                                    </span>
                                                    @if ($isLowStock)
                                                        <small class="badge bg-warning-lt" style="font-size: 0.65rem;">Düşük</small>
                                                    @endif
                                                    @if ($hasNoStockData)
                                                        <small class="badge bg-danger-lt" style="font-size: 0.65rem;">Stoksuz</small>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted small editable-stock">—</span>
                                            @endif
                                            <button class="btn btn-sm px-2 py-1 edit-icon"
                                                wire:click="startEditingStock({{ $product->product_id }}, {{ $product->current_stock }}, {{ $product->stock_tracking ? 'true' : 'false' }})">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                        </div>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center gap-2">
                                        <!-- Aktif/Pasif -->
                                        <button wire:click="toggleActive({{ $product->product_id }})"
                                            class="btn btn-icon btn-sm {{ $product->is_active ? 'text-muted bg-transparent' : 'text-red bg-transparent' }}"
                                            data-bs-toggle="tooltip"
                                            title="{{ $product->is_active ? __('admin.active') : __('admin.inactive') }}">
                                            <div wire:loading wire:target="toggleActive({{ $product->product_id }})"
                                                class="spinner-border spinner-border-sm"></div>
                                            <div wire:loading.remove wire:target="toggleActive({{ $product->product_id }})">
                                                @if ($product->is_active)
                                                    <i class="fas fa-check"></i>
                                                @else
                                                    <i class="fas fa-times"></i>
                                                @endif
                                            </div>
                                        </button>

                                        <!-- Anasayfa Göster -->
                                        <button wire:click="toggleHomepage({{ $product->product_id }})"
                                            class="btn btn-icon btn-sm {{ $product->show_on_homepage ? 'text-success bg-transparent' : 'text-muted bg-transparent' }}"
                                            data-bs-toggle="tooltip"
                                            title="{{ $product->show_on_homepage ? 'Anasayfada gösteriliyor' : 'Anasayfada gösterilmiyor' }}">
                                            <div wire:loading wire:target="toggleHomepage({{ $product->product_id }})"
                                                class="spinner-border spinner-border-sm"></div>
                                            <div wire:loading.remove wire:target="toggleHomepage({{ $product->product_id }})">
                                                <i class="fas fa-home"></i>
                                            </div>
                                        </button>

                                        @php
                                            $hasFeaturedImage = $product->hasMedia('hero');
                                            $galleryCount = $product->getMedia('gallery')->count();
                                        @endphp

                                        <!-- Ana Foto -->
                                        <div class="btn btn-icon btn-sm {{ $hasFeaturedImage ? 'text-primary' : 'text-muted' }} bg-transparent"
                                            data-bs-toggle="tooltip"
                                            title="{{ $hasFeaturedImage ? 'Ana foto var' : 'Ana foto yok' }}">
                                            <i class="fas fa-image"></i>
                                        </div>

                                        <!-- Galeri -->
                                        <div class="btn btn-icon btn-sm {{ $galleryCount > 0 ? 'text-info' : 'text-muted' }} bg-transparent d-flex align-items-center gap-1"
                                            data-bs-toggle="tooltip"
                                            title="{{ $galleryCount > 0 ? $galleryCount . ' galeri fotoğrafı' : 'Galeri yok' }}">
                                            <i class="fas fa-images"></i>
                                            @if($galleryCount > 0)
                                                <small class="fw-bold">{{ $galleryCount }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="d-flex align-items-center gap-3 justify-content-center">
                                        <!-- Edit Product -->
                                        <a href="{{ route('admin.shop.manage', ['id' => $product->product_id]) }}"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="{{ __('admin.edit') }}"
                                            style="min-height: 24px; display: inline-flex; align-items: center; text-decoration: none;">
                                            <i class="fa-solid fa-pen-to-square link-secondary fa-lg"></i>
                                        </a>

                                        <!-- AI Translation -->
                                        <x-ai-translation :entity-type="'shop-product'" :entity-id="$product->product_id"
                                            tooltip="{{ __('admin.ai_translate') }}" />

                                        <!-- Delete Dropdown -->
                                        @hasmoduleaccess('shop', 'delete')
                                        <div class="dropdown">
                                            <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false"
                                                style="min-height: 24px; display: inline-flex; align-items: center; text-decoration: none;">
                                                <i class="fa-solid fa-bars-sort fa-flip-horizontal fa-lg"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a href="javascript:void(0);"
                                                    wire:click="$dispatch('showDeleteModal', {
                                                        module: 'shop-product',
                                                        id: {{ $product->product_id }},
                                                        title: '{{ addslashes($product->getTranslated('title', app()->getLocale()) ?? $product->getTranslated('title', 'tr')) }}'
                                                    })"
                                                    class="dropdown-item link-danger">
                                                    {{ __('admin.delete') }}
                                                </a>
                                            </div>
                                        </div>
                                        @endhasmoduleaccess
                                    </div>
                                </td>
                            </tr>

                            <!-- Variant Rows (Same Structure as Product Rows) -->
                            @if($product->childProducts && $product->childProducts->count() > 0)
                                @foreach($product->childProducts as $variant)
                                    <tr class="hover-trigger variant-row"
                                        x-show="openVariants[{{ $product->product_id }}]"
                                        x-cloak
                                        wire:key="variant-{{ $variant->product_id }}">
                                        <td></td>
                                        <td class="sort-id small">
                                            <div class="hover-toggle ps-2">
                                                <i class="fas fa-level-up-alt fa-rotate-90 text-muted me-1" style="font-size: 0.65rem;"></i>
                                                <span class="text-muted" style="font-size: 0.85rem;">{{ $variant->product_id }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if($variant->variant_type)
                                                <span class="badge bg-azure-lt" style="font-size: 0.65rem; padding: 0.2rem 0.4rem;">
                                                    <i class="fas fa-tag" style="font-size: 0.6rem;"></i>
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if(!$quickEditMode && $variant->sku)
                                                    <span class="badge bg-secondary-lt" style="font-size: 0.7rem; padding: 0.2rem 0.4rem;">{{ $variant->sku }}</span>
                                                @endif
                                                <span style="font-size: 0.875rem;">{{ $variant->getTranslated('title', $currentSiteLocale) ?? $variant->getTranslated('title', 'tr') }}</span>
                                                @if($variant->variant_type)
                                                    <span class="badge bg-azure-lt" style="font-size: 0.65rem; padding: 0.2rem 0.4rem;">{{ $variant->variant_type }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        @if(!$quickEditMode)
                                            <td>
                                                @if($variant->category)
                                                    {{ $variant->category->getTranslated('title', $currentSiteLocale) ?? '—' }}
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($variant->brand)
                                                    {{ $variant->brand->getTranslated('title', $currentSiteLocale) ?? '—' }}
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                        @endif
                                        <td class="text-end" wire:key="price-variant-{{ $variant->product_id }}">
                                            @if ($quickEditMode)
                                                {{-- Quick Edit Mode: Always editable inputs --}}
                                                <div class="d-flex align-items-center justify-content-end gap-2">
                                                    <input type="number" step="0.01"
                                                        value="{{ $variant->base_price }}"
                                                        wire:blur="updateProductField({{ $variant->product_id }}, 'base_price', $event.target.value)"
                                                        wire:keydown.enter="updateProductField({{ $variant->product_id }}, 'base_price', $event.target.value)"
                                                        class="form-control form-control-sm text-end quick-edit-input"
                                                        style="width: 90px;"
                                                        placeholder="0.00">
                                                    <select
                                                        wire:change="updateProductField({{ $variant->product_id }}, 'currency', $event.target.value)"
                                                        class="form-select form-select-sm quick-edit-select"
                                                        style="width: 75px;">
                                                        <option value="TRY" {{ ($variant->currency ?? 'TRY') === 'TRY' ? 'selected' : '' }}>TRY</option>
                                                        <option value="USD" {{ $variant->currency === 'USD' ? 'selected' : '' }}>USD</option>
                                                        <option value="EUR" {{ $variant->currency === 'EUR' ? 'selected' : '' }}>EUR</option>
                                                    </select>
                                                </div>
                                            @elseif ($editingPriceId === $variant->product_id)
                                                <div class="d-flex align-items-center justify-content-end gap-2" x-data
                                                    @click.outside="$wire.updatePriceInline()">
                                                    <input type="number" step="0.01" wire:model.defer="newPrice"
                                                        class="form-control form-control-sm text-end"
                                                        style="width: 100px;"
                                                        placeholder="0.00"
                                                        wire:keydown.enter="updatePriceInline"
                                                        wire:keydown.escape="cancelPriceEdit"
                                                        x-init="$nextTick(() => $el.focus())">
                                                    <select wire:model.defer="newCurrency" class="form-select form-select-sm"
                                                        style="width: 80px;">
                                                        <option value="TRY">TRY</option>
                                                        <option value="USD">USD</option>
                                                        <option value="EUR">EUR</option>
                                                    </select>
                                                    <button class="btn px-2 py-1 btn-outline-success btn-sm"
                                                        wire:click="updatePriceInline">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button class="btn px-2 py-1 btn-outline-danger btn-sm"
                                                        wire:click="cancelPriceEdit">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            @else
                                                <div class="d-flex align-items-center justify-content-end gap-2">
                                                    @if ($variant->price_display_mode === 'show' && $variant->base_price && $variant->base_price > 0)
                                                        @php
                                                            // ✨ OTOMATIK İNDİRİM SİSTEMİ (Variant - Admin Panel)
                                                            $variantCompareAtPrice = $variant->compare_at_price;
                                                            $variantDiscountPercentage = null;

                                                            // Eğer compare_at_price yoksa veya base_price'dan küçükse, otomatik hesapla
                                                            if (!$variantCompareAtPrice || $variantCompareAtPrice <= $variant->base_price) {
                                                                // Hedef indirim yüzdesi (badge için - SABİT: %5, %10, %15, %20)
                                                                $variantDiscountPercentage = (($variant->product_id % 4) * 5 + 5);

                                                                // Eski fiyatı hesapla (ters formül: old = new / (1 - discount))
                                                                $variantCompareAtPrice = $variant->base_price / (1 - ($variantDiscountPercentage / 100));

                                                                // Yuvarlama (00 veya 50)
                                                                $lastTwo = $variantCompareAtPrice % 100;
                                                                if ($lastTwo <= 24) {
                                                                    $variantCompareAtPrice = floor($variantCompareAtPrice / 100) * 100;
                                                                } elseif ($lastTwo <= 74) {
                                                                    $variantCompareAtPrice = floor($variantCompareAtPrice / 100) * 100 + 50;
                                                                } else {
                                                                    $variantCompareAtPrice = ceil($variantCompareAtPrice / 100) * 100;
                                                                }
                                                            } else {
                                                                // Manuel compare_at_price var, gerçek indirim yüzdesini hesapla
                                                                $variantDiscountPercentage = round((($variantCompareAtPrice - $variant->base_price) / $variantCompareAtPrice) * 100);
                                                            }
                                                        @endphp

                                                        <div class="d-flex flex-column align-items-end position-relative">
                                                            {{-- İndirim Badge (sadece %5+ indirim varsa) --}}
                                                            @if($variantDiscountPercentage && $variantDiscountPercentage >= 5)
                                                                <span class="badge bg-danger position-absolute" style="top: -8px; right: -8px; font-size: 0.7rem;">
                                                                    -%{{ $variantDiscountPercentage }}
                                                                </span>
                                                            @endif

                                                            <span class="fw-semibold editable-price">{{ formatPrice($variant->base_price, $variant->currency ?? 'TRY') }}</span>
                                                            @if ($variantCompareAtPrice && $variantCompareAtPrice > $variant->base_price)
                                                                <small class="text-muted text-decoration-line-through">
                                                                    {{ formatPrice($variantCompareAtPrice, $variant->currency ?? 'TRY') }}
                                                                </small>
                                                            @endif
                                                        </div>
                                                    @elseif ($variant->price_display_mode === 'request')
                                                        <span class="badge bg-secondary editable-price">{{ __('shop::admin.price_on_request') }}</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark editable-price">{{ __('shop::admin.price_hidden') }}</span>
                                                    @endif
                                                    <button class="btn btn-sm px-2 py-1 edit-icon"
                                                        wire:click="startEditingPrice({{ $variant->product_id }}, '{{ $variant->base_price }}', '{{ $variant->currency ?? 'TRY' }}')">
                                                        <i class="fas fa-pen"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center" wire:key="stock-variant-{{ $variant->product_id }}">
                                            @if ($quickEditMode)
                                                {{-- Quick Edit Mode: Always editable input --}}
                                                <input type="number" step="1" min="0"
                                                    value="{{ $variant->current_stock }}"
                                                    wire:blur="updateProductField({{ $variant->product_id }}, 'current_stock', $event.target.value)"
                                                    wire:keydown.enter="updateProductField({{ $variant->product_id }}, 'current_stock', $event.target.value)"
                                                    class="form-control form-control-sm text-center quick-edit-input"
                                                    style="width: 70px;"
                                                    placeholder="0">
                                            @elseif ($editingStockId === $variant->product_id)
                                                <div class="d-flex flex-column gap-2 align-items-center" x-data
                                                    @click.outside="$wire.updateStockInline()">
                                                    <div class="form-check form-switch mb-0">
                                                        <input class="form-check-input" type="checkbox"
                                                            wire:model.live="newStockTracking"
                                                            id="stockTracking-{{ $variant->product_id }}">
                                                        <label class="form-check-label small text-nowrap" for="stockTracking-{{ $variant->product_id }}">
                                                            Takip
                                                        </label>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <input type="number" step="1" min="0" wire:model.defer="newStock"
                                                            class="form-control form-control-sm text-center"
                                                            style="width: 70px;"
                                                            placeholder="0"
                                                            wire:keydown.enter="updateStockInline"
                                                            wire:keydown.escape="cancelStockEdit"
                                                            :disabled="!$wire.newStockTracking"
                                                            x-init="$wire.newStockTracking && $nextTick(() => $el.focus())">
                                                        <button class="btn px-2 py-1 btn-outline-success btn-sm"
                                                            wire:click="updateStockInline">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button class="btn px-2 py-1 btn-outline-danger btn-sm"
                                                            wire:click="cancelStockEdit">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="d-flex align-items-center justify-content-center gap-2">
                                                    @if ($variant->stock_tracking)
                                                        @php
                                                            $hasNoStockData = $variant->current_stock == 0;
                                                            $isLowStock = $variant->current_stock > 0 && $variant->current_stock <= $variant->low_stock_threshold;
                                                        @endphp
                                                        <div class="d-flex flex-column align-items-center">
                                                            <span class="fw-semibold editable-stock {{ $hasNoStockData ? 'text-muted' : ($isLowStock ? 'text-warning' : '') }}">
                                                                {{ $hasNoStockData ? '0' : $variant->current_stock }}
                                                            </span>
                                                            @if ($isLowStock)
                                                                <small class="badge bg-warning-lt" style="font-size: 0.65rem;">Düşük</small>
                                                            @endif
                                                            @if ($hasNoStockData)
                                                                <small class="badge bg-danger-lt" style="font-size: 0.65rem;">Stoksuz</small>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="text-muted small editable-stock">—</span>
                                                    @endif
                                                    <button class="btn btn-sm px-2 py-1 edit-icon"
                                                        wire:click="startEditingStock({{ $variant->product_id }}, {{ $variant->current_stock }}, {{ $variant->stock_tracking ? 'true' : 'false' }})">
                                                        <i class="fas fa-pen"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center align-middle">
                                            <button wire:click="toggleActive({{ $variant->product_id }})"
                                                class="btn btn-icon btn-sm {{ $variant->is_active ? 'text-muted bg-transparent' : 'text-red bg-transparent' }}">
                                                <div wire:loading wire:target="toggleActive({{ $variant->product_id }})"
                                                    class="spinner-border spinner-border-sm"></div>
                                                <div wire:loading.remove wire:target="toggleActive({{ $variant->product_id }})">
                                                    @if ($variant->is_active)
                                                        <i class="fas fa-check"></i>
                                                    @else
                                                        <i class="fas fa-times"></i>
                                                    @endif
                                                </div>
                                            </button>
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="d-flex align-items-center gap-3 justify-content-center">
                                                <!-- Edit Variant -->
                                                <a href="{{ route('admin.shop.manage', ['id' => $variant->product_id]) }}"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{ __('admin.edit') }}"
                                                    style="min-height: 24px; display: inline-flex; align-items: center; text-decoration: none;">
                                                    <i class="fa-solid fa-pen-to-square link-secondary fa-lg"></i>
                                                </a>

                                                <!-- AI Translation -->
                                                <x-ai-translation :entity-type="'shop-product'" :entity-id="$variant->product_id"
                                                    tooltip="{{ __('admin.ai_translate') }}" />

                                                <!-- Delete Dropdown -->
                                                @hasmoduleaccess('shop', 'delete')
                                                <div class="dropdown">
                                                    <a class="dropdown-toggle text-secondary" href="#" data-bs-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false"
                                                        style="min-height: 24px; display: inline-flex; align-items: center; text-decoration: none;">
                                                        <i class="fa-solid fa-bars-sort fa-flip-horizontal fa-lg"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a href="javascript:void(0);"
                                                            wire:click="$dispatch('showDeleteModal', {
                                                                module: 'shop-product',
                                                                id: {{ $variant->product_id }},
                                                                title: '{{ addslashes($variant->getTranslated('title', app()->getLocale()) ?? $variant->getTranslated('title', 'tr')) }}'
                                                            })"
                                                            class="dropdown-item link-danger">
                                                            {{ __('admin.delete') }}
                                                        </a>
                                                    </div>
                                                </div>
                                                @endhasmoduleaccess
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="empty">
                                        <p class="empty-title">{{ __('shop::admin.no_products_found') }}</p>
                                        <p class="empty-subtitle text-muted">
                                            {{ __('shop::admin.no_results') }}
                                        </p>
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
            @if ($products->hasPages())
                {{ $products->links() }}
            @else
                <div class="d-flex justify-content-between align-items-center mb-0">
                    <p class="small text-muted mb-0">
                        {{ __('admin.total') }} <span class="fw-semibold">{{ $products->total() }}</span> {{ __('admin.results') }}
                    </p>
                </div>
            @endif
        </div>

        <!-- Bulk Actions -->
        @include('shop::admin.partials.bulk-actions', ['moduleType' => 'shop-product'])

        <livewire:modals.bulk-delete-modal />
        <livewire:modals.delete-modal />
    </div>
</div>

@push('styles')
<style>
/* Dark Mode Support */
:root[data-bs-theme="dark"] .variant-row-bg {
    background: rgba(255, 255, 255, 0.02) !important;
}

:root[data-bs-theme="light"] .variant-row-bg {
    background: rgba(0, 0, 0, 0.02) !important;
}

/* Variant Toggle Button */
.variant-toggle-btn {
    min-width: auto;
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.variant-toggle-btn:hover {
    background: var(--tblr-btn-hover-bg, rgba(var(--tblr-secondary-rgb), 0.1));
}

/* Variant Row - Same Structure as Product Row */
.variant-row {
    background: var(--tblr-bg-surface-secondary, rgba(0, 0, 0, 0.02));
}

:root[data-bs-theme="dark"] .variant-row {
    background: rgba(255, 255, 255, 0.02);
}

.variant-row td {
    border-top: 1px solid var(--tblr-border-color-translucent, rgba(0, 0, 0, 0.06));
    padding: 0.5rem 0.75rem !important;
    line-height: 1.4285714;
}

.variant-row .badge {
    font-size: 0.7rem !important;
    padding: 0.2rem 0.4rem !important;
}

.variant-row .btn {
    padding: 0.25rem 0.5rem !important;
}

.variant-row .btn-icon {
    width: 1.75rem !important;
    height: 1.75rem !important;
}

/* Hover Effects */
.hover-trigger:hover .hover-hide {
    display: none;
}

.hover-trigger .hover-show {
    display: none;
}

.hover-trigger:hover .hover-show {
    display: inline-block;
}

/* Edit Icon Hover */
.edit-icon {
    opacity: 0;
    transition: opacity 0.2s ease;
}

.hover-trigger:hover .edit-icon {
    opacity: 1;
}

/* Editable Price Hover */
.editable-price {
    cursor: pointer;
    transition: color 0.2s ease;
}

.hover-trigger:hover .editable-price {
    color: var(--tblr-primary, #206bc4);
}

/* Editable Stock Hover */
.editable-stock {
    cursor: pointer;
    transition: color 0.2s ease;
}

.hover-trigger:hover .editable-stock {
    color: var(--tblr-primary, #206bc4);
}

/* Badge Improvements - Dark Mode Compatible */
.badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

:root[data-bs-theme="dark"] .badge.bg-secondary-lt {
    background-color: rgba(255, 255, 255, 0.1) !important;
    color: rgba(255, 255, 255, 0.8) !important;
}

:root[data-bs-theme="light"] .badge.bg-secondary-lt {
    background-color: #e9ecef !important;
    color: #495057 !important;
}

:root[data-bs-theme="dark"] .badge.bg-primary-lt {
    background-color: rgba(var(--tblr-primary-rgb), 0.2) !important;
    color: var(--tblr-primary) !important;
}

:root[data-bs-theme="light"] .badge.bg-primary-lt {
    background-color: #cfe2ff !important;
    color: #084298 !important;
}

:root[data-bs-theme="dark"] .badge.bg-success-lt {
    background-color: rgba(var(--tblr-success-rgb), 0.2) !important;
    color: var(--tblr-success) !important;
}

:root[data-bs-theme="light"] .badge.bg-success-lt {
    background-color: #d1e7dd !important;
    color: #0f5132 !important;
}

:root[data-bs-theme="dark"] .badge.bg-danger-lt {
    background-color: rgba(var(--tblr-danger-rgb), 0.2) !important;
    color: var(--tblr-danger) !important;
}

:root[data-bs-theme="light"] .badge.bg-danger-lt {
    background-color: #f8d7da !important;
    color: #842029 !important;
}

:root[data-bs-theme="dark"] .badge.bg-info-lt {
    background-color: rgba(var(--tblr-info-rgb), 0.2) !important;
    color: var(--tblr-info) !important;
}

:root[data-bs-theme="light"] .badge.bg-info-lt {
    background-color: #cfe2ff !important;
    color: #055160 !important;
}

/* Responsive */
@media (max-width: 768px) {
    .variant-row td {
        font-size: 0.875rem;
    }
}

/* Sortable Styles */
.sortable-handle {
    cursor: grab !important;
}

.sortable-handle:active {
    cursor: grabbing !important;
}

.sortable-ghost {
    opacity: 0.4;
    background-color: var(--tblr-primary-lt, #e6f3ff) !important;
}

.sortable-chosen {
    background-color: var(--tblr-primary-lt, #e6f3ff) !important;
}

.sortable-drag {
    opacity: 0.8;
    cursor: grabbing !important;
}

:root[data-bs-theme="dark"] .sortable-ghost,
:root[data-bs-theme="dark"] .sortable-chosen {
    background-color: rgba(var(--tblr-primary-rgb), 0.2) !important;
}

/* Quick Edit Mode Styles */
.quick-edit-input,
.quick-edit-select {
    border: 1px solid var(--tblr-border-color) !important;
    background-color: var(--tblr-bg-forms) !important;
    transition: all 0.2s ease;
}

.quick-edit-input:focus,
.quick-edit-select:focus {
    border-color: var(--tblr-primary) !important;
    box-shadow: 0 0 0 0.25rem rgba(var(--tblr-primary-rgb), 0.15) !important;
    background-color: var(--tblr-bg-surface) !important;
}

.quick-edit-input:disabled,
.quick-edit-select:disabled {
    background-color: var(--tblr-gray-100) !important;
    cursor: not-allowed;
    opacity: 0.6;
}

:root[data-bs-theme="dark"] .quick-edit-input,
:root[data-bs-theme="dark"] .quick-edit-select {
    background-color: rgba(255, 255, 255, 0.05) !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
    color: var(--tblr-body-color) !important;
}

:root[data-bs-theme="dark"] .quick-edit-input:focus,
:root[data-bs-theme="dark"] .quick-edit-select:focus {
    background-color: rgba(255, 255, 255, 0.08) !important;
    border-color: var(--tblr-primary) !important;
}

:root[data-bs-theme="dark"] .quick-edit-input:disabled,
:root[data-bs-theme="dark"] .quick-edit-select:disabled {
    background-color: rgba(255, 255, 255, 0.02) !important;
    opacity: 0.5;
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('assets/js/simple-translation-modal.js') }}?v={{ time() }}"></script>
<script src="{{ asset('admin-assets/libs/sortable/sortable.min.js') }}"></script>
<script>
let productSortableInstance = null;

function initSortable() {
    const sortableList = document.getElementById('sortable-products');

    if (!sortableList || !sortableList.classList.contains('sortable-list')) {
        // Eski instance'ı temizle
        if (productSortableInstance) {
            productSortableInstance.destroy();
            productSortableInstance = null;
        }
        return;
    }

    // Eski sortable instance'ı temizle
    if (productSortableInstance) {
        productSortableInstance.destroy();
        productSortableInstance = null;
    }

    productSortableInstance = new Sortable(sortableList, {
        animation: 150,
        handle: '.sortable-handle',
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        dragClass: 'sortable-drag',
        draggable: '.sortable-item',  // Sadece sortable-item class'ına sahip olanlar sürüklenebilir
        filter: '.variant-row',  // Variant row'ları filtreleme
        preventOnFilter: true,
        onEnd: function(evt) {
            const productIds = [];
            const rows = sortableList.querySelectorAll('.sortable-item');

            rows.forEach(function(row) {
                const productId = row.getAttribute('data-product-id');
                if (productId) {
                    productIds.push(parseInt(productId));
                }
            });

            console.log('Sortable onEnd - Product IDs:', productIds);

            // Livewire component'e sıralama bilgisini gönder
            @this.call('updateSortOrder', productIds);
        }
    });

    console.log('Sortable initialized for products');
}

// İlk yükleme
document.addEventListener('DOMContentLoaded', function() {
    initSortable();
});

// Livewire güncellemelerinden sonra yeniden initialize
document.addEventListener('livewire:navigated', function() {
    initSortable();
});

// Livewire component güncellendiğinde
document.addEventListener('livewire:update', function() {
    setTimeout(initSortable, 100);
});

// Livewire v3 - Component initialized event
Livewire.hook('morph.updated', ({ component }) => {
    setTimeout(initSortable, 100);
});
</script>
@endpush
