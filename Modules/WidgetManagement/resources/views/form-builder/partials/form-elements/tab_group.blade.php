@php
    $fieldType = $element['type'] ?? 'tab_group';
    $fieldLabel = $element['label'] ?? 'Sekme Grubu';
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    $isSystem = isset($element['system']) && $element['system'];
    $tabId = 'tabs-' . Str::random(6);
    $tabsData = [];
    $marginBottom = isset($element['properties']['margin_bottom']) ? $element['properties']['margin_bottom'] : 4;
    $fillSpace = isset($element['properties']['fill_space']) && $element['properties']['fill_space'];
    $tabStyle = isset($element['properties']['tab_style']) ? $element['properties']['tab_style'] : 'tabs';
    $variant = isset($element['properties']['variant']) ? $element['properties']['variant'] : 'default';
    $cardBg = isset($element['properties']['card_bg']) ? 'bg-' . $element['properties']['card_bg'] : '';
    $headerBg = isset($element['properties']['header_bg']) ? 'bg-' . $element['properties']['header_bg'] : '';
    $bodyBg = isset($element['properties']['body_bg']) ? 'bg-' . $element['properties']['body_bg'] : '';
    $cardBorder = isset($element['properties']['card_border']) ? 'border-' . $element['properties']['card_border'] : '';
    $cardShadow = isset($element['properties']['card_shadow']) ? 'shadow-' . $element['properties']['card_shadow'] : '';
    $tabAlignment = isset($element['properties']['tab_alignment']) ? 'justify-content-' . $element['properties']['tab_alignment'] : '';
    $animate = isset($element['properties']['animate']) && $element['properties']['animate'];
    $iconPosition = isset($element['properties']['icon_position']) ? $element['properties']['icon_position'] : 'left';
    $activeTab = isset($element['properties']['active_tab']) ? (int)$element['properties']['active_tab'] : 0;
    $minHeight = isset($element['properties']['min_height']) ? 'min-height: ' . $element['properties']['min_height'] . 'px' : '';
    $equalHeight = isset($element['properties']['equal_height']) && $element['properties']['equal_height'];
    $rememberTab = isset($element['properties']['remember_tab']) && $element['properties']['remember_tab'];
    
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

<div class="col-{{ $width }} mb-{{ $marginBottom }}">
    <div class="card {{ $fillSpace ? 'h-100' : '' }} {{ $cardBg }} {{ $cardBorder }} {{ $cardShadow }}">
        <div class="card-header {{ $headerBg }}">
            <div class="d-flex align-items-center justify-content-between">
                @if($fieldLabel)
                <h3 class="card-title d-flex align-items-center">
                    <i class="fas fa-folder-open me-2 text-primary"></i>
                    {{ $fieldLabel }}
                    @if($isSystem)
                        <span class="badge bg-orange ms-2">Sistem</span>
                    @endif
                </h3>
                @endif
            </div>
            
            <ul class="nav nav-{{ $tabStyle }} {{ $variant === 'pills' ? 'nav-pills' : '' }} {{ $variant === 'underline' ? 'nav-bordered' : '' }} card-header-{{ $tabStyle }} mt-2 {{ $tabAlignment }}">
                @forelse($tabsData as $index => $tab)
                    <li class="nav-item">
                        <a href="#{{ $tabId }}-{{ $index }}" class="nav-link {{ $index === $activeTab ? 'active' : '' }}" 
                           data-bs-toggle="tab" id="{{ $tabId }}-tab-{{ $index }}" role="tab" 
                           aria-controls="{{ $tabId }}-{{ $index }}" aria-selected="{{ $index === $activeTab ? 'true' : 'false' }}">
                            <span class="d-inline-flex align-items-center">
                                @if(isset($tab['icon']) && $iconPosition === 'left')
                                    <i class="{{ $tab['icon'] }} me-2"></i>
                                @endif
                                {{ $tab['title'] ?? 'Sekme ' . ($index + 1) }}
                                @if(isset($tab['icon']) && $iconPosition === 'right')
                                    <i class="{{ $tab['icon'] }} ms-2"></i>
                                @endif
                                @if(isset($tab['badge']))
                                    <span class="badge bg-{{ $tab['badge_color'] ?? 'primary' }} ms-2">{{ $tab['badge'] }}</span>
                                @endif
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
        
        <div class="card-body {{ $fillSpace ? 'overflow-auto' : '' }} {{ $bodyBg }}" style="{{ $minHeight }}">
            <div class="tab-content">
                @forelse($tabsData as $index => $tab)
                    <div class="tab-pane {{ $animate ? 'fade' : '' }} {{ $index === $activeTab ? 'active show' : '' }} {{ $equalHeight ? 'h-100' : '' }}" 
                         id="{{ $tabId }}-{{ $index }}" 
                         role="tabpanel" 
                         aria-labelledby="{{ $tabId }}-tab-{{ $index }}">
                        @if(isset($tab['content']))
                            {!! $tab['content'] !!}
                        @elseif(isset($tab['elements']) && is_array($tab['elements']))
                            <div class="row h-100">
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