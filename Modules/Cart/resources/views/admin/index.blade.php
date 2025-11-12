@extends('admin.layout')

@section('title', __('cart::admin.module_name'))

@section('content')
<div class="container-xl">
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    <i class="fa-solid fa-shopping-cart me-2 text-purple"></i>
                    {{ __('cart::admin.module_name') }}
                </h2>
                <div class="text-muted mt-1">{{ __('cart::admin.module_description') }}</div>
            </div>
        </div>
    </div>

    <div class="row row-cards mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('cart::admin.carts') }}</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-0">
                        <h4 class="alert-title">
                            <i class="fa-solid fa-info-circle me-2"></i>
                            Cart Module - Coming Soon
                        </h4>
                        <div class="text-muted">
                            Cart management interface will be available soon. This module provides universal shopping cart functionality for Shop, Subscription, and Service modules.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
