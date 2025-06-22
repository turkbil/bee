@php
    // Temel değişkenler
    $element = $element ?? [];
    $formData = $formData ?? [];
    $values = $values ?? [];
    $settings = $settings ?? collect([]);
    $originalValues = $originalValues ?? [];
    $temporaryImages = $temporaryImages ?? [];
    $temporaryMultipleImages = $temporaryMultipleImages ?? [];
    $multipleImagesArrays = $multipleImagesArrays ?? [];
    
    // Element adı ve ID'si
    $tabGroupId = $element['id'] ?? ('tab_group_' . uniqid());
    $tabGroupName = $element['name'] ?? '';
    $tabGroupLabel = $element['label'] ?? 'Sekme Grubu';
    
    // Element özellikleri
    if (!isset($element['properties']) || !is_array($element['properties'])) {
        $element['properties'] = [];
    }
    
    // Benzersiz sekme kimliği oluştur
    $tabId = 'tabs-' . Str::random(6);
    
    // Sekme verilerini al
    $tabsData = [];
    if (isset($element['properties']['tabs']) && is_array($element['properties']['tabs'])) {
        $tabsData = $element['properties']['tabs'];
    } elseif (isset($element['properties']['tabs']) && is_string($element['properties']['tabs'])) {
        try {
            $tabsData = json_decode($element['properties']['tabs'], true) ?: [];
        } catch (\Exception $e) {
            $tabsData = [];
        }
    }
    
    // Boş sekme verileri için varsayılan ekle
    if (empty($tabsData)) {
        $tabsData = [
            [
                'title' => 'Varsayılan Sekme',
                'elements' => []
            ]
        ];
    }
    
    // Sekme içerik kontrolü
    $hasTabs = !empty($tabsData);
@endphp

<div class="card mb-4" id="{{ $tabGroupId }}_container">
    
    <input type="hidden" name="elements[{{ $loop->index ?? 0 }}][id]" value="{{ $tabGroupId }}">
    <input type="hidden" name="elements[{{ $loop->index ?? 0 }}][type]" value="tab_group">
    <input type="hidden" name="elements[{{ $loop->index ?? 0 }}][name]" value="{{ $tabGroupName }}">
    <input type="hidden" name="elements[{{ $loop->index ?? 0 }}][label]" value="{{ $tabGroupLabel }}">
    
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" role="tablist">
            @foreach($tabsData as $index => $tab)
                <li class="nav-item" role="presentation">
                    <a href="#{{ $tabId }}-{{ $index }}" class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                       data-bs-toggle="tab" id="{{ $tabId }}-tab-{{ $index }}" role="tab" 
                       aria-controls="{{ $tabId }}-{{ $index }}" aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                        <span class="d-inline-flex align-items-center">
                            @if(isset($tab['icon']))
                                <i class="{{ $tab['icon'] }} me-2"></i>
                            @endif
                            {{ $tab['title'] ?? 'Sekme ' . ($index + 1) }}
                        </span>
                    </a>
                </li>
                
                <input type="hidden" name="elements[{{ $loop->index ?? 0 }}][properties][tabs][{{ $index }}][title]" value="{{ $tab['title'] ?? 'Sekme ' . ($index + 1) }}">
                @if(isset($tab['icon']))
                    <input type="hidden" name="elements[{{ $loop->index ?? 0 }}][properties][tabs][{{ $index }}][icon]" value="{{ $tab['icon'] }}">
                @endif
            @endforeach
        </ul>
    </div>
    
    <div class="card-body" style="border-radius: 0.25rem;">
        <div class="tab-content">
            @foreach($tabsData as $index => $tab)
                <div class="tab-pane {{ $index === 0 ? 'active show' : '' }}" id="{{ $tabId }}-{{ $index }}" 
                     role="tabpanel" aria-labelledby="{{ $tabId }}-tab-{{ $index }}">
                    @php
                        $hasTabContent = isset($tab['content']) && !empty($tab['content']);
                        $hasTabElements = isset($tab['elements']) && is_array($tab['elements']) && count($tab['elements']) > 0;
                    @endphp

                    @if($hasTabContent)
                        <input type="hidden" name="elements[{{ $loop->index ?? 0 }}][properties][tabs][{{ $index }}][content]" value="{{ $tab['content'] }}">
                        {!! $tab['content'] !!}
                    @endif
                    
                    @if($hasTabElements)
                        <div class="row">
                            @foreach($tab['elements'] as $elementIndex => $tabElement)
                                @php
                                    $elementType = $tabElement['type'] ?? 'text';
                                    $viewPath = 'widgetmanagement::form-builder.partials.form-elements.' . $elementType;
                                @endphp
                                
                                @if(view()->exists($viewPath))
                                    @include($viewPath, [
                                        'element' => $tabElement,
                                        'values' => $values,
                                        'settings' => $settings,
                                        'originalValues' => $originalValues,
                                        'temporaryImages' => $temporaryImages,
                                        'temporaryMultipleImages' => $temporaryMultipleImages,
                                        'multipleImagesArrays' => $multipleImagesArrays,
                                        'formData' => $formData,
                                        'loop' => (object)['index' => $elementIndex]
                                    ])
                                @else
                                    <div class="alert alert-warning">
                                        <strong>{{ $elementType }}</strong> türü için görünüm bulunamadı.
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @elseif(!$hasTabContent)
                        <p class="text-muted">Bu sekmede içerik bulunmuyor.</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
    // {{ $tabId }} için sekme işlevini yöneten kod
    document.addEventListener('DOMContentLoaded', function() {
        initTabsFor{{ $tabId }}();
    });
    
    document.addEventListener('livewire:initialized', function() {
        initTabsFor{{ $tabId }}();
    });
    
    document.addEventListener('livewire:navigated', function() {
        initTabsFor{{ $tabId }}();
    });
    
    document.addEventListener('livewire:update', function() {
        initTabsFor{{ $tabId }}();
    });
    
    function initTabsFor{{ $tabId }}() {
        const tabLinks = document.querySelectorAll(`[id^="{{ $tabId }}-tab-"]`);
        
        tabLinks.forEach(function(tabLink) {
            // Önce mevcut olay dinleyicilerini kaldır
            tabLink.removeEventListener('click', handleTabClick{{ $tabId }});
            // Sonra yeni olay dinleyicisi ekle
            tabLink.addEventListener('click', handleTabClick{{ $tabId }});
        });
    }
    
    function handleTabClick{{ $tabId }}(e) {
        e.preventDefault();
        
        // Aktif sekmeyi kaldır
        const tabContainer = this.closest('.nav-tabs');
        tabContainer.querySelectorAll('.nav-link').forEach(function(link) {
            link.classList.remove('active');
            link.setAttribute('aria-selected', 'false');
        });
        
        // Bu sekmeyi aktif yap
        this.classList.add('active');
        this.setAttribute('aria-selected', 'true');
        
        // Tab panellerini gizle
        const tabPanels = document.querySelectorAll(`[id^="{{ $tabId }}-"]`);
        tabPanels.forEach(function(panel) {
            if (panel.classList.contains('tab-pane')) {
                panel.classList.remove('active', 'show');
            }
        });
        
        // Hedef paneli göster
        const targetId = this.getAttribute('href');
        const targetPanel = document.querySelector(targetId);
        if (targetPanel) {
            targetPanel.classList.add('active', 'show');
        }
    }
</script>
@endpush