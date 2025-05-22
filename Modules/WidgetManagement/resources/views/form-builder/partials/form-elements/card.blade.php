@php
    $title = $element['properties']['title'] ?? 'Kart';
    $content = $element['properties']['content'] ?? '';
    $width = $element['properties']['width'] ?? 12;
    $showHeader = isset($element['properties']['show_header']) ? $element['properties']['show_header'] : true;
    $showFooter = isset($element['properties']['show_footer']) ? $element['properties']['show_footer'] : false;
    $collapsible = isset($element['properties']['collapsible']) && $element['properties']['collapsible'];
    $cardId = 'card-' . Str::random(6);
    $elements = isset($element['elements']) && is_array($element['elements']) ? $element['elements'] : [];
    $footerContent = $element['properties']['footer_content'] ?? '';
@endphp

<div class="col-{{ $width }}">
    <div class="card mb-3">
        @if($showHeader)
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h3 class="card-title">{{ $title }}</h3>
                    @if($collapsible)
                        <div>
                            <a href="#{{ $cardId }}" class="btn btn-sm" data-bs-toggle="collapse" role="button" aria-expanded="true">
                                <i class="fas fa-chevron-up"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif
        
        <div class="card-body" id="{{ $cardId }}">
            @if($content)
                <p>{{ $content }}</p>
            @endif
            
            @if(count($elements) > 0)
                <div class="row">
                    @foreach($elements as $cardElement)
                        @if(isset($formData))
                            @include('widgetmanagement::form-builder.partials.form-elements.' . $cardElement['type'], [
                                'element' => $cardElement,
                                'formData' => $formData ?? []
                            ])
                        @else
                            @include('widgetmanagement::form-builder.partials.form-elements.' . $cardElement['type'], [
                                'element' => $cardElement,
                                'settings' => $settings ?? []
                            ])
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
        
        @if($showFooter)
            <div class="card-footer">
                {!! $footerContent !!}
            </div>
        @endif
    </div>
</div>