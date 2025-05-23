@php
    // Temel değişkenler
    $element = $element ?? [];
    $formData = $formData ?? [];
    $values = $values ?? [];
    $settings = $settings ?? collect([]);
    
    // Element adı ve ID'si
    $cardId = $element['id'] ?? ('card_' . uniqid());
    $cardName = $element['name'] ?? '';
    
    // Element özellikleri
    if (!isset($element['properties']) || !is_array($element['properties'])) {
        $element['properties'] = [];
    }
    
    // Kart başlığı ve içeriği
    $cardTitle = $element['label'] ?? ($element['properties']['title'] ?? 'Kart');
    $cardContent = $element['properties']['content'] ?? null;
    
    // Kart verilerini JSON'a kaydet
    $element['properties']['title'] = $cardTitle;
    $element['properties']['content'] = $cardContent;
    
    // Formdaki değerlerden güncellenebilir
    if (isset($formData['title'])) {
        $element['properties']['title'] = $formData['title'];
        $cardTitle = $formData['title'];
    }
    
    if (isset($formData['content'])) {
        $element['properties']['content'] = $formData['content'];
        $cardContent = $formData['content'];
    }
    
    // Element dizisini kontrol et
    $hasElements = isset($element['elements']) && is_array($element['elements']) && count($element['elements']) > 0;
@endphp

<div class="col-12" id="{{ $cardId }}_container">
    <div class="card mb-3 w-100">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                <h3 class="card-title d-flex align-items-center">
                    <i class="fas fa-square me-2 text-primary"></i>
                    {{ $cardTitle }}
                </h3>
            </div>
        </div>
        
        <div class="card-body">
            @if($cardContent)
                <p>{{ $cardContent }}</p>
            @endif
            
            <input type="hidden" name="elements[{{ $loop->index ?? 0 }}][id]" value="{{ $cardId }}">
            <input type="hidden" name="elements[{{ $loop->index ?? 0 }}][type]" value="card">
            <input type="hidden" name="elements[{{ $loop->index ?? 0 }}][name]" value="{{ $cardName }}">
            <input type="hidden" name="elements[{{ $loop->index ?? 0 }}][label]" value="{{ $cardTitle }}">
            <input type="hidden" name="elements[{{ $loop->index ?? 0 }}][properties][title]" value="{{ $cardTitle }}">
            <input type="hidden" name="elements[{{ $loop->index ?? 0 }}][properties][content]" value="{{ $cardContent }}">
            
            @if($hasElements)
                <div class="row">
                    @foreach($element['elements'] as $index => $cardElement)
                        @php
                            $elementType = $cardElement['type'] ?? 'text';
                            $viewPath = 'widgetmanagement::form-builder.partials.form-elements.' . $elementType;
                        @endphp
                        
                        @if(view()->exists($viewPath))
                            @include($viewPath, [
                                'element' => $cardElement,
                                'values' => $values,
                                'settings' => $settings,
                                'originalValues' => $originalValues ?? [],
                                'temporaryImages' => $temporaryImages ?? [],
                                'temporaryMultipleImages' => $temporaryMultipleImages ?? [],
                                'multipleImagesArrays' => $multipleImagesArrays ?? [],
                                'formData' => $formData,
                                'loop' => (object)['index' => $index]
                            ])
                        @else
                            <div class="alert alert-warning">
                                <strong>{{ $elementType }}</strong> türü için görünüm bulunamadı.
                            </div>
                        @endif
                    @endforeach
                </div>
            @elseif(!$cardContent)
                <div class="alert alert-info">
                    Bu kart için içerik eklemek için "Kart İçeriği" alanını kullanabilirsiniz.
                </div>
            @endif
        </div>
    </div>
</div>