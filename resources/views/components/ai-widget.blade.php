{{-- Global AI Widget Component --}}
<div id="{{ $widgetId }}" class="ai-widget-container mt-4" 
     data-context="{{ $context }}" 
     data-entity-id="{{ $entityId }}" 
     data-entity-type="{{ $entityType }}">
    <!-- Widget Header -->
    <div class="card">
        <div class="card-header bg-primary-subtle">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm bg-primary text-white rounded me-3">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-primary fw-bold">AI Assistant</h6>
                        <small class="text-muted">{{ ucfirst($context) }} için AI özellikleri</small>
                    </div>
                </div>
                <button class="btn btn-sm btn-outline-primary" onclick="toggleAIWidget('{{ $widgetId }}')" id="toggle-{{ $widgetId }}">
                    <i class="fas fa-chevron-up"></i>
                    <span>Küçült</span>
                </button>
            </div>
        </div>

        <!-- Widget Content -->
        <div class="card-body p-0" id="content-{{ $widgetId }}">
            <div class="row g-0">
                <!-- AI Features Panel -->
                <div class="col-md-4 border-end bg-light">
                    <div class="p-3">
                        <h6 class="fw-bold mb-3 text-dark">
                            <i class="fas fa-magic me-2 text-primary"></i>AI Özellikleri
                        </h6>
                        
                        @foreach($categories as $category)
                        <div class="ai-category mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <small class="text-uppercase fw-bold text-muted">{{ $category->name }}</small>
                            </div>
                            
                            @if(isset($features[$category->id]))
                            <div class="ai-feature-list">
                                @foreach($features[$category->id] as $feature)
                                <button class="ai-feature-btn btn btn-sm btn-outline-secondary w-100 mb-2 text-start" 
                                        onclick="executeAIWidgetFeature('{{ $widgetId }}', '{{ $feature->slug }}', '{{ $feature->name }}')"
                                        data-feature-slug="{{ $feature->slug }}">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <span class="me-2">{{ $feature->emoji ?? '🤖' }}</span>
                                            <span class="small">{{ $feature->name }}</span>
                                        </div>
                                        <i class="fas fa-chevron-right small text-muted"></i>
                                    </div>
                                </button>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- AI Results Panel -->  
                <div class="col-md-8">
                    <div class="p-3">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="fw-bold mb-0 text-dark">
                                <i class="fas fa-chart-line me-2 text-success"></i>AI Sonuçları
                            </h6>
                            <button class="btn btn-sm btn-outline-danger" onclick="clearAIWidgetResults('{{ $widgetId }}')" style="display: none;" id="clear-{{ $widgetId }}">
                                <i class="fas fa-trash me-1"></i>Temizle
                            </button>
                        </div>
                        
                        <!-- Results Container -->
                        <div id="results-{{ $widgetId }}" class="ai-results-container">
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-robot fa-3x mb-3 opacity-50"></i>
                                <p class="mb-0">AI özelliklerinden birini seçin</p>
                                <small>Sonuçlar burada görünecek</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


