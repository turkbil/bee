@php
    $tabs = $element['properties']['tabs'] ?? $element['tabs'] ?? [];
    $width = isset($element['properties']['width']) ? $element['properties']['width'] : 12;
    $tabId = 'tab-group-' . uniqid();
@endphp

<div class="col-{{ $width }}">
    <div class="card mb-3 w-100">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="{{ $tabId }}-tabs" role="tablist">
                @foreach($tabs as $index => $tab)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                            id="{{ $tabId }}-tab-{{ $index }}" 
                            data-bs-toggle="tab" 
                            data-bs-target="#{{ $tabId }}-pane-{{ $index }}" 
                            type="button" 
                            role="tab" 
                            aria-controls="{{ $tabId }}-pane-{{ $index }}" 
                            aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                            {{ $tab['title'] ?? 'Sekme ' . ($index + 1) }}
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="{{ $tabId }}-content">
                @foreach($tabs as $index => $tab)
                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                        id="{{ $tabId }}-pane-{{ $index }}" 
                        role="tabpanel" 
                        aria-labelledby="{{ $tabId }}-tab-{{ $index }}" 
                        tabindex="0">
                        <p>{{ $tab['content'] ?? 'İçerik ' . ($index + 1) }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>