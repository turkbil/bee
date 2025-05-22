@php
    $tabId = 'tabs-' . Str::random(6);
    $tabsData = [];
    
    // JSON verilerini doğru şekilde çek
    if (isset($element['properties']['tabs']) && is_array($element['properties']['tabs'])) {
        $tabsData = $element['properties']['tabs'];
    } elseif (isset($element['properties']['tabs']) && is_string($element['properties']['tabs'])) {
        try {
            $tabsData = json_decode($element['properties']['tabs'], true) ?: [];
        } catch (\Exception $e) {
            $tabsData = [];
        }
    }
@endphp

<div class="card mb-4">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
            @forelse($tabsData as $index => $tab)
                <li class="nav-item">
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
            @empty
                <li class="nav-item">
                    <a href="#{{ $tabId }}-empty" class="nav-link active" data-bs-toggle="tab" 
                       id="{{ $tabId }}-tab-empty" role="tab" aria-controls="{{ $tabId }}-empty" aria-selected="true">
                       Varsayılan Sekme
                    </a>
                </li>
            @endforelse
        </ul>
    </div>
    
    <div class="card-body">
        <div class="tab-content">
            @forelse($tabsData as $index => $tab)
                <div class="tab-pane {{ $index === 0 ? 'active show' : '' }}" id="{{ $tabId }}-{{ $index }}" 
                     role="tabpanel" aria-labelledby="{{ $tabId }}-tab-{{ $index }}">
                    @if(isset($tab['content']))
                        {!! $tab['content'] !!}
                    @elseif(isset($tab['elements']) && is_array($tab['elements']))
                        @foreach($tab['elements'] as $tabElement)
                            @php
                                $elementType = $tabElement['type'] ?? 'text';
                                $viewPath = 'settingmanagement::form-builder.partials.form-elements.' . $elementType;
                            @endphp
                            
                            @if(view()->exists($viewPath))
                                @include($viewPath, [
                                    'element' => $tabElement,
                                    'values' => $values ?? [],
                                    'settings' => $settings ?? [],
                                    'originalValues' => $originalValues ?? [],
                                    'temporaryImages' => $temporaryImages ?? [],
                                    'temporaryMultipleImages' => $temporaryMultipleImages ?? [],
                                    'multipleImagesArrays' => $multipleImagesArrays ?? []
                                ])
                            @else
                                <div class="alert alert-warning">
                                    '{{ $elementType }}' türü için görünüm bulunamadı.
                                </div>
                            @endif
                        @endforeach
                    @else
                        <p class="text-muted">Bu sekmede içerik bulunmuyor.</p>
                    @endif
                </div>
            @empty
                <div class="tab-pane active show" id="{{ $tabId }}-empty" 
                     role="tabpanel" aria-labelledby="{{ $tabId }}-tab-empty">
                    <p class="text-muted">Sekme yapılandırması bulunamadı.</p>
                </div>
            @endforelse
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