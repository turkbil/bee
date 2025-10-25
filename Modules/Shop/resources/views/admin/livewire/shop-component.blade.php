@php
    View::share('pretitle', 'Shop Yönetimi');
@endphp

<div class="shop-component-wrapper">
    <div class="row row-cards">
        <!-- Ürünler Kartı -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fa-solid fa-box fa-3x text-primary"></i>
                    </div>
                    <h3 class="card-title">{{ __('shop::admin.products') }}</h3>
                    <p class="text-muted">{{ __('shop::admin.products_description') }}</p>
                    <a href="{{ route('admin.shop.index') }}" class="btn btn-primary">
                        <i class="fa-solid fa-arrow-right me-2"></i>
                        {{ __('admin.manage') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Kategoriler Kartı -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fa-solid fa-folder-tree fa-3x text-success"></i>
                    </div>
                    <h3 class="card-title">{{ __('shop::admin.categories') }}</h3>
                    <p class="text-muted">{{ __('shop::admin.categories_description') }}</p>
                    <a href="{{ route('admin.shop.categories.index') }}" class="btn btn-success">
                        <i class="fa-solid fa-arrow-right me-2"></i>
                        {{ __('admin.manage') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Markalar Kartı -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fa-solid fa-tag fa-3x text-info"></i>
                    </div>
                    <h3 class="card-title">{{ __('shop::admin.brands') }}</h3>
                    <p class="text-muted">{{ __('shop::admin.brands_description') }}</p>
                    <a href="{{ route('admin.shop.brands.index') }}" class="btn btn-info">
                        <i class="fa-solid fa-arrow-right me-2"></i>
                        {{ __('admin.manage') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Field Templates Kartı -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-th-list fa-3x text-purple"></i>
                    </div>
                    <h3 class="card-title">{{ __('shop::admin.field_templates') }}</h3>
                    <p class="text-muted">{{ __('shop::admin.field_templates_description') }}</p>
                    <a href="{{ route('admin.shop.field-templates.index') }}" class="btn btn-purple">
                        <i class="fa-solid fa-arrow-right me-2"></i>
                        {{ __('admin.manage') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
