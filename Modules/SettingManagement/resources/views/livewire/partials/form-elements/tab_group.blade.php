<div class="card mb-3">
    @php
        $tabId = 'tabs-' . Str::random(6);
    @endphp
    
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
            @if(isset($element['tabs']) && is_array($element['tabs']))
                @foreach($element['tabs'] as $index => $tab)
                    <li class="nav-item" role="presentation">
                        <a href="#{{ $tabId }}-{{ $index }}" class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                           data-bs-toggle="tab" role="tab" aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                           {{ $tab['title'] ?? 'Sekme ' . ($index + 1) }}
                        </a>
                    </li>
                @endforeach
            @endif
        </ul>
    </div>
    
    <div class="card-body">
        <div class="tab-content">
            @if(isset($element['tabs']) && is_array($element['tabs']))
                @foreach($element['tabs'] as $index => $tab)
                    <div class="tab-pane {{ $index === 0 ? 'active show' : '' }}" id="{{ $tabId }}-{{ $index }}">
                        @if(isset($tab['elements']) && is_array($tab['elements']))
                            @foreach($tab['elements'] as $tabElement)
                                @include('settingmanagement::livewire.partials.form-elements.' . $tabElement['type'], [
                                    'element' => $tabElement,
                                    'values' => $values,
                                    'settings' => $settings,
                                    'temporaryImages' => $temporaryImages ?? [],
                                    'temporaryMultipleImages' => $temporaryMultipleImages ?? [],
                                    'multipleImagesArrays' => $multipleImagesArrays ?? [],
                                    'originalValues' => $originalValues ?? []
                                ])
                            @endforeach
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>