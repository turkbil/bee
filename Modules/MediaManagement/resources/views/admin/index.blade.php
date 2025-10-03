@extends('admin.layout')

@section('content')
<div class="card">
    @include('mediamanagement::admin.helper')

    <div class="card-body">
                <!-- Info Alert -->
                <div class="alert alert-info mb-4">
                    <div class="d-flex">
                        <div>
                            <i class="fas fa-lightbulb fa-2x me-3"></i>
                        </div>
                        <div>
                            <h4 class="alert-title">{{ __('mediamanagement::admin.how_to_use') }}</h4>
                            <div class="text-secondary">
                                {{ __('mediamanagement::admin.usage_instructions') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Supported Media Types -->
                <div class="mb-4">
                    <h3 class="mb-3">
                        <i class="fas fa-file-alt me-2"></i>{{ __('mediamanagement::admin.supported_types') }}
                    </h3>
                    <div class="row g-3">
                        <!-- Image -->
                        <div class="col-md-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fas fa-image fa-2x text-primary"></i>
                                        </div>
                                        <div>
                                            <h4 class="mb-1">{{ __('mediamanagement::admin.type_image') }}</h4>
                                            <div class="text-secondary small">
                                                JPG, PNG, WebP, GIF, SVG
                                                <br><strong>{{ __('mediamanagement::admin.max_size') }}:</strong> 10MB
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Video -->
                        <div class="col-md-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fas fa-video fa-2x text-success"></i>
                                        </div>
                                        <div>
                                            <h4 class="mb-1">{{ __('mediamanagement::admin.type_video') }}</h4>
                                            <div class="text-secondary small">
                                                MP4, WebM, OGG, MOV
                                                <br><strong>{{ __('mediamanagement::admin.max_size') }}:</strong> 100MB
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Audio -->
                        <div class="col-md-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fas fa-music fa-2x text-info"></i>
                                        </div>
                                        <div>
                                            <h4 class="mb-1">{{ __('mediamanagement::admin.type_audio') }}</h4>
                                            <div class="text-secondary small">
                                                MP3, WAV, OGG
                                                <br><strong>{{ __('mediamanagement::admin.max_size') }}:</strong> 50MB
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Document -->
                        <div class="col-md-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                        </div>
                                        <div>
                                            <h4 class="mb-1">{{ __('mediamanagement::admin.type_document') }}</h4>
                                            <div class="text-secondary small">
                                                PDF, DOC, DOCX, XLS, XLSX
                                                <br><strong>{{ __('mediamanagement::admin.max_size') }}:</strong> 20MB
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Features -->
                <div>
                    <h3 class="mb-3">
                        <i class="fas fa-star me-2"></i>{{ __('mediamanagement::admin.features') }}
                    </h3>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <i class="fas fa-arrows-alt fa-lg text-primary"></i>
                                </div>
                                <div>
                                    <strong>{{ __('mediamanagement::admin.drag_drop') }}</strong>
                                    <div class="text-secondary small">{{ __('mediamanagement::admin.drag_drop_desc') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <i class="fas fa-layer-group fa-lg text-success"></i>
                                </div>
                                <div>
                                    <strong>{{ __('mediamanagement::admin.auto_conversion') }}</strong>
                                    <div class="text-secondary small">{{ __('mediamanagement::admin.auto_conversion_desc') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <i class="fas fa-sort fa-lg text-info"></i>
                                </div>
                                <div>
                                    <strong>{{ __('mediamanagement::admin.sortable_gallery') }}</strong>
                                    <div class="text-secondary small">{{ __('mediamanagement::admin.sortable_gallery_desc') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <i class="fas fa-shield-alt fa-lg text-warning"></i>
                                </div>
                                <div>
                                    <strong>{{ __('mediamanagement::admin.secure') }}</strong>
                                    <div class="text-secondary small">{{ __('mediamanagement::admin.secure_desc') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
