@php
    // Section özelliklerini al
    $title = $element['title'] ?? 'Bölüm';
    $subtitle = $element['subtitle'] ?? null;
    $width = $element['width'] ?? 12;
@endphp

<div class="col-{{ $width }}">
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title mb-0">{{ $title }}</h3>
            @if($subtitle)
                <div class="text-muted small mt-1">{{ $subtitle }}</div>
            @endif
        </div>

        <div class="card-body">
            @if(isset($element['elements']) && is_array($element['elements']))
                <div class="row g-3">
                    @foreach($element['elements'] as $sectionElement)
                        @include('settingmanagement::form-builder.partials.form-elements.' . $sectionElement['type'], [
                            'element' => $sectionElement,
                            'values' => $values,
                            'settings' => $settings,
                            'originalValues' => $originalValues ?? [],
                            'temporaryImages' => $temporaryImages ?? [],
                            'temporaryMultipleImages' => $temporaryMultipleImages ?? [],
                            'multipleImagesArrays' => $multipleImagesArrays ?? []
                        ])
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
