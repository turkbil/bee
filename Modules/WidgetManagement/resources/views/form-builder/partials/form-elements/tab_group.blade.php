@php
    $fieldLabel = $element['label'] ?? 'Sekme Grubu';
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    $isSystem = isset($element['system']) && $element['system'];
    $tabId = 'tabs-' . Str::random(6);
    $tabStyle = isset($element['properties']['tab_style']) ? $element['properties']['tab_style'] : 'tabs';
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

<div class="col-{{ $width }} mb-3">
    <div class="card">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
                @if($fieldLabel)
                <h3 class="card-title">
                    {{ $fieldLabel }}
                    @if($isSystem)
                        <span class="badge bg-orange ms-2">Sistem</span>
                    @endif
                </h3>
                @endif
            </div>
            
            <ul class="nav nav-{{ $tabStyle }} card-header-{{ $tabStyle }} mt-2">
                @forelse($tabsData as $index => $tab)
                    <li class="nav-item">
                        <a href="#{{ $tabId }}-{{ $index }}" class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                           data-bs-toggle="tab" id="{{ $tabId }}-tab-{{ $index }}" role="tab" 
                           aria-controls="{{ $tabId }}-{{ $index }}" aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                            {{ $tab['title'] ?? 'Sekme ' . ($index + 1) }}
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
                    <div class="tab-pane {{ $index === 0 ? 'active show' : '' }}" 
                         id="{{ $tabId }}-{{ $index }}" 
                         role="tabpanel" 
                         aria-labelledby="{{ $tabId }}-tab-{{ $index }}">
                        @if(isset($tab['content']))
                            {!! $tab['content'] !!}
                        @elseif(isset($tab['elements']) && is_array($tab['elements']))
                            <div class="row">
                                @foreach($tab['elements'] as $tabElement)
                                    @php
                                        $elementType = $tabElement['type'] ?? 'text';
                                        $viewPath = 'widgetmanagement::form-builder.partials.form-elements.' . $elementType;
                                    @endphp
                                    
                                    @if(view()->exists($viewPath))
                                        @if(isset($formData))
                                            @include($viewPath, [
                                                'element' => $tabElement,
                                                'formData' => $formData ?? []
                                            ])
                                        @else
                                            @include($viewPath, [
                                                'element' => $tabElement,
                                                'settings' => $settings ?? []
                                            ])
                                        @endif
                                    @else
                                        <div class="alert alert-warning">
                                            '{{ $elementType }}' türü için görünüm bulunamadı.
                                        </div>
                                    @endif
                                @endforeach
                            </div>
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