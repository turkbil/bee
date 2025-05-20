<div class="col-12">
    <div class="card mb-3 w-100">
        <div class="card-header">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h3 class="card-title d-flex align-items-center">
                    <i class="fas fa-folder me-2 text-primary"></i>
                    {{ $element['properties']['label'] ?? 'Sekme Grubu' }}
                </h3>
            </div>
            
            @php
                $tabId = 'tabs-' . Str::random(6);
            @endphp
            
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
                            <div class="row">
                                @if(isset($tab['elements']) && is_array($tab['elements']))
                                    @foreach($tab['elements'] as $tabElement)
                                        @include('settingmanagement::livewire.partials.form-elements.' . $tabElement['type'], [
                                            'element' => $tabElement,
                                            'values' => $values,
                                            'settings' => $settings,
                                            'originalValues' => $originalValues ?? [],
                                            'temporaryImages' => $temporaryImages ?? [],
                                            'temporaryMultipleImages' => $temporaryMultipleImages ?? [],
                                            'multipleImagesArrays' => $multipleImagesArrays ?? []
                                        ])
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>