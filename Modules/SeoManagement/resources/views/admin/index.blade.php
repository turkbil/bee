@extends('admin.layout')

@include('seomanagement::admin.helper')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <h3 class="mb-3">{{ __('admin.seo_management_title') }}</h3>
                <p class="text-muted mb-4">
                    {{ __('admin.seo_management_description') }}
                </p>

                <h4 class="mb-3">{{ __('admin.manage_pages_seo_preview') }}</h4>
                <p class="text-muted mb-3">
                    {{ __('admin.seo_tab_preview_description') }}
                </p>

                <!-- SEO Tab Preview -->
                <div class="card bg-light border-2 border-dashed">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fa-solid fa-eye me-2"></i>
                            {{ __('admin.preview_title') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Preview of Universal SEO Component -->
                        @include('seomanagement::components.universal-seo-tab-preview')
                    </div>
                </div>

                <div class="alert alert-info mt-4">
                    <i class="fa-solid fa-info-circle me-2"></i>
                    <strong>{{ __('admin.note') }}:</strong> {{ __('admin.preview_note') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
.steps {
    counter-reset: step;
}

.step-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1.5rem;
    position: relative;
}

.step-counter {
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    background: #206bc4;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 16px;
    margin-right: 1rem;
}

.step-display {
    flex: 1;
    padding-top: 4px;
}

.step-display h5 {
    margin-bottom: 0.25rem;
    color: #1e293b;
}

.step-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 19px;
    top: 45px;
    width: 2px;
    height: calc(100% - 20px);
    background: #e2e8f0;
}
</style>