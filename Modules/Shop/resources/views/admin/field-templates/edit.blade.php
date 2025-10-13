@extends('admin.layouts.master')

@section('title', __('shop::admin.edit_field_template'))

@section('content')
    {{-- Helper --}}
    @include('admin.partials.helper', [
        'title' => __('shop::admin.edit_field_template'),
        'description' => __('shop::admin.edit_template_description'),
        'icon' => 'ti ti-template',
    ])

    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        {{ __('shop::admin.edit_field_template') }}
                    </h2>
                    <div class="text-muted mt-1">
                        {{ $template->name }}
                    </div>
                </div>
                <div class="col-auto ms-auto">
                    <a href="{{ route('admin.shop.field-templates.index') }}" class="btn btn-ghost-secondary">
                        <i class="ti ti-arrow-left"></i>
                        {{ __('shop::admin.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h4 class="alert-title">
                        <i class="ti ti-alert-circle me-2"></i>
                        {{ __('shop::admin.validation_errors') }}
                    </h4>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @include('shop::admin.field-templates._form', ['template' => $template])
        </div>
    </div>
@endsection
