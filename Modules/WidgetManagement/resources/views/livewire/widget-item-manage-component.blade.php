@php
    View::share('pretitle', $itemId ? 'Widget Öğe Düzenle' : 'Yeni Widget Öğe');
@endphp
@include('widgetmanagement::helper')
<div>
    @include('admin.partials.error_message')
    <form wire:submit.prevent="save">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                    <li class="nav-item">
                        <a href="#tabs-1" class="nav-link active" data-bs-toggle="tab">
                            {{ $tenantWidget->widget->name }} - 
                            @if($itemId)
                                {{ __('widgetmanagement.items.edit') }}
                            @else
                                {{ __('widgetmanagement.items.create') }}
                            @endif
                        </a>
                    </li>
                </ul>
                
                <div class="card-actions">
                    <a href="{{ route('admin.widgetmanagement.items', $tenantWidgetId) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> {{ __('admin.back') }}
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane fade active show" id="tabs-1">
                        <div class="row g-3">
                            @foreach($schema as $element)
                                @if(isset($element['type']))
                                    @if($element['type'] === 'row' && isset($element['columns']))
                                        <div class="col-12">
                                            <div class="row g-3">
                                                @foreach($element['columns'] as $column)
                                                    <div class="col-md-{{ $column['width'] ?? 6 }}">
                                                        @if(isset($column['elements']) && is_array($column['elements']))
                                                            @foreach($column['elements'] as $columnElement)
                                                                @include('widgetmanagement::form-builder.partials.form-elements.' . $columnElement['type'], [
                                                                    'element' => $columnElement,
                                                                    'formData' => $formData,
                                                                    'temporaryImages' => $temporaryImages,
                                                                    'photos' => $photos
                                                                ])
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @elseif(!isset($element['hidden']) || !$element['hidden'])
                                        @include('widgetmanagement::form-builder.partials.form-elements.' . $element['type'], [
                                            'element' => $element,
                                            'formData' => $formData,
                                            'temporaryImages' => $temporaryImages,
                                            'photos' => $photos
                                        ])
                                    @endif
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <x-form-footer route="admin.widgetmanagement.items" :model-id="$itemId" />

        </div>
    </form>
</div>