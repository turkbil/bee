@php
    $fieldType = $element['type'] ?? 'card';
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    $title = $element['properties']['title'] ?? 'Kart';
    $icon = $element['properties']['icon'] ?? 'square';
    $iconColor = $element['properties']['icon_color'] ?? 'primary';
    $content = $element['properties']['content'] ?? '';
    $borderColor = isset($element['properties']['border_color']) ? 'border-' . $element['properties']['border_color'] : '';
    $headerBg = isset($element['properties']['header_bg']) ? 'bg-' . $element['properties']['header_bg'] : '';
    $cardBg = isset($element['properties']['card_bg']) ? 'bg-' . $element['properties']['card_bg'] : '';
    $textColor = isset($element['properties']['text_color']) ? 'text-' . $element['properties']['text_color'] : '';
    $isCollapsible = isset($element['properties']['collapsible']) && $element['properties']['collapsible'];
    $isCollapsed = isset($element['properties']['collapsed']) && $element['properties']['collapsed'];
    $elements = isset($element['elements']) && is_array($element['elements']) ? $element['elements'] : [];
    $marginBottom = isset($element['properties']['margin_bottom']) ? $element['properties']['margin_bottom'] : 4;
    $cardId = 'card_' . uniqid();
    $shadow = isset($element['properties']['shadow']) ? 'shadow-' . $element['properties']['shadow'] : '';
    $rounded = isset($element['properties']['rounded']) ? 'rounded-' . $element['properties']['rounded'] : 'rounded';
    $showFooter = isset($element['properties']['show_footer']) && $element['properties']['show_footer'];
    $footerContent = isset($element['properties']['footer_content']) ? $element['properties']['footer_content'] : '';
@endphp

<div class="col-{{ $width }}">
    <div class="card mb-{{ $marginBottom }} w-100 {{ $borderColor }} {{ $cardBg }} {{ $shadow }} {{ $rounded }}">
        <div class="card-header {{ $headerBg }}">
            <div class="d-flex align-items-center justify-content-between">
                <h3 class="card-title d-flex align-items-center">
                    <i class="fas fa-{{ $icon }} me-2 text-{{ $iconColor }}"></i>
                    {{ $title }}
                </h3>
                @if($isCollapsible)
                    <div>
                        <a href="#{{ $cardId }}" class="btn btn-sm" data-bs-toggle="collapse" role="button" aria-expanded="{{ $isCollapsed ? 'false' : 'true' }}">
                            <i class="fas fa-chevron-{{ $isCollapsed ? 'down' : 'up' }}"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="card-body {{ $isCollapsible ? 'collapse '.($isCollapsed ? '' : 'show') : '' }} {{ $textColor }}" id="{{ $cardId }}">
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
            <div class="card-footer text-muted">
                {!! $footerContent !!}
            </div>
        @endif
    </div>
</div>