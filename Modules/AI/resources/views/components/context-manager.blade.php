{{-- 
    Context Manager Component V3 Professional
    
    Enterprise-level context management interface component
    - Real-time context extraction and analysis
    - Smart rule management interface
    - Pattern recognition dashboard
    - Performance monitoring
    
    @version 3.0.0 Professional
    @since 2025-08-10
--}}

@props([
    'featureId' => null,
    'contextDepth' => 'enhanced',
    'enableRealTime' => true,
    'enablePatternAnalysis' => true,
    'enableRuleOptimization' => true,
    'theme' => 'professional',
    'containerClass' => 'context-manager-container',
    'height' => 'auto',
    'debugMode' => false,
    'options' => []
])

@php
    $managerId = 'context-manager-' . uniqid();
    $componentOptions = array_merge([
        'featureId' => $featureId,
        'contextDepth' => $contextDepth,
        'enableRealTimeUpdates' => $enableRealTime,
        'enablePatternAnalysis' => $enablePatternAnalysis,
        'enableRuleOptimization' => $enableRuleOptimization,
        'theme' => $theme,
        'debugMode' => $debugMode,
        'apiEndpoint' => '/admin/ai/v3/context'
    ], $options);
@endphp

@push('styles')
<style>
/* Context Manager V3 Styles */
.context-manager-v3 {
    --cm-primary-color: #007bff;
    --cm-success-color: #28a745;
    --cm-warning-color: #ffc107;
    --cm-danger-color: #dc3545;
    --cm-info-color: #17a2b8;
    --cm-border-radius: 8px;
    --cm-shadow: 0 2px 10px rgba(0,0,0,0.1);
    --cm-transition: all 0.3s ease;
    
    background: #ffffff;
    border-radius: var(--cm-border-radius);
    box-shadow: var(--cm-shadow);
    overflow: hidden;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.context-manager-v3.theme-dark {
    --cm-bg-color: #2d3748;
    --cm-text-color: #e2e8f0;
    --cm-border-color: #4a5568;
    background: var(--cm-bg-color);
    color: var(--cm-text-color);
}

/* Header */
.cm-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
}

.cm-title {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 18px;
    font-weight: 600;
    color: #374151;
}

.cm-controls {
    display: flex;
    gap: 8px;
}

.cm-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border: 2px solid transparent;
    border-radius: 6px;
    background: transparent;
    cursor: pointer;
    transition: var(--cm-transition);
    color: #6b7280;
}

.cm-btn:hover {
    background: #e5e7eb;
    color: #374151;
}

.cm-btn-refresh:hover {
    color: var(--cm-info-color);
}

.cm-btn-optimize:hover {
    color: var(--cm-warning-color);
}

.cm-btn-settings:hover {
    color: var(--cm-primary-color);
}

/* Loading State */
.cm-loading {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
    text-align: center;
}

.cm-spinner {
    width: 40px;
    height: 40px;
    border: 3px solid rgba(0,123,255,0.1);
    border-left: 3px solid var(--cm-primary-color);
    border-radius: 50%;
    animation: cmSpin 1s linear infinite;
    margin-bottom: 16px;
}

@keyframes cmSpin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Tabs */
.cm-tabs {
    height: 100%;
    display: flex;
    flex-direction: column;
}

.cm-tab-headers {
    display: flex;
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    overflow-x: auto;
}

.cm-tab-header {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 16px 20px;
    border: none;
    background: transparent;
    color: #6b7280;
    cursor: pointer;
    transition: var(--cm-transition);
    white-space: nowrap;
    font-size: 14px;
    font-weight: 500;
}

.cm-tab-header:hover {
    background: #e5e7eb;
    color: #374151;
}

.cm-tab-header.active {
    background: white;
    color: var(--cm-primary-color);
    border-bottom: 2px solid var(--cm-primary-color);
}

.cm-tab-contents {
    flex: 1;
    position: relative;
    overflow: hidden;
}

.cm-tab-content {
    display: none;
    height: 100%;
    overflow-y: auto;
}

.cm-tab-content.active {
    display: block;
}

/* Context Overview */
.cm-context-overview {
    padding: 24px;
}

.cm-quality-indicator {
    text-align: center;
    margin-bottom: 32px;
}

.cm-quality-circle {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: conic-gradient(var(--cm-success-color) calc(var(--score) * 1%), #e5e7eb 0);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
    position: relative;
}

.cm-quality-circle::before {
    content: '';
    position: absolute;
    width: 80px;
    height: 80px;
    background: white;
    border-radius: 50%;
}

.cm-quality-value {
    font-size: 20px;
    font-weight: 700;
    color: var(--cm-success-color);
    z-index: 1;
    position: relative;
}

.cm-quality-label {
    font-size: 16px;
    color: #6b7280;
    font-weight: 500;
}

/* Context Types */
.cm-context-types {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 32px;
}

.cm-context-type {
    background: #f8f9fa;
    border-radius: var(--cm-border-radius);
    padding: 16px;
    transition: var(--cm-transition);
    cursor: pointer;
}

.cm-context-type:hover {
    background: #e9ecef;
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.cm-context-type-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.cm-context-type-name {
    font-weight: 600;
    color: #374151;
}

.cm-context-type-count {
    background: var(--cm-primary-color);
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
}

.cm-context-type-confidence {
    display: flex;
    align-items: center;
    gap: 8px;
}

.cm-confidence-bar {
    flex: 1;
    height: 6px;
    background: #e5e7eb;
    border-radius: 3px;
    overflow: hidden;
}

.cm-confidence-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--cm-warning-color) 0%, var(--cm-success-color) 100%);
    transition: width 0.3s ease;
}

.cm-confidence-value {
    font-size: 12px;
    font-weight: 500;
    color: #6b7280;
}

/* Rules Manager */
.cm-rules-manager {
    padding: 24px;
}

.cm-rules-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    gap: 16px;
}

.cm-rules-search {
    position: relative;
    min-width: 250px;
}

.cm-search-input {
    width: 100%;
    padding: 10px 16px 10px 40px;
    border: 2px solid #e5e7eb;
    border-radius: 6px;
    font-size: 14px;
    transition: var(--cm-transition);
}

.cm-search-input:focus {
    outline: none;
    border-color: var(--cm-primary-color);
    box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
}

.cm-rules-search i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
}

.cm-btn-primary {
    background: var(--cm-primary-color);
    border-color: var(--cm-primary-color);
    color: white;
    padding: 10px 16px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: var(--cm-transition);
}

.cm-btn-primary:hover {
    background: #0056b3;
    border-color: #0056b3;
}

/* Stats */
.cm-rules-stats {
    display: flex;
    gap: 24px;
    margin-top: 24px;
}

.cm-stat {
    text-align: center;
    padding: 16px;
    background: #f8f9fa;
    border-radius: var(--cm-border-radius);
    flex: 1;
}

.cm-stat-value {
    display: block;
    font-size: 24px;
    font-weight: 700;
    color: var(--cm-primary-color);
    margin-bottom: 4px;
}

.cm-stat-label {
    font-size: 12px;
    color: #6b7280;
    text-transform: uppercase;
    font-weight: 500;
}

/* Responsive Design */
@media (max-width: 768px) {
    .cm-header {
        padding: 16px;
    }
    
    .cm-context-overview {
        padding: 16px;
    }
    
    .cm-rules-manager {
        padding: 16px;
    }
    
    .cm-rules-header {
        flex-direction: column;
        align-items: stretch;
    }
    
    .cm-context-types {
        grid-template-columns: 1fr;
    }
    
    .cm-rules-stats {
        flex-direction: column;
    }
    
    .cm-tab-headers {
        overflow-x: auto;
    }
}
</style>
@endpush

<div 
    id="{{ $managerId }}" 
    class="context-manager-v3 {{ $containerClass }}"
    data-context-manager
    data-context-manager-options="{{ json_encode($componentOptions) }}"
    @if($height !== 'auto') style="height: {{ $height }};" @endif
    aria-label="AI Context Manager">
    
    {{-- Server-side fallback content --}}
    <div class="cm-header">
        <h3 class="cm-title">
            <i class="fas fa-brain"></i>
            Bağlam Yönetimi
        </h3>
        <div class="cm-controls">
            <button class="cm-btn cm-btn-refresh" data-action="refresh" title="Yenile">
                <i class="fas fa-sync-alt"></i>
            </button>
            <button class="cm-btn cm-btn-optimize" data-action="optimize" title="Optimize Et">
                <i class="fas fa-magic"></i>
            </button>
            <button class="cm-btn cm-btn-settings" data-action="settings" title="Ayarlar">
                <i class="fas fa-cog"></i>
            </button>
        </div>
    </div>
    
    <div class="cm-content">
        <div class="cm-loading" id="cm-loading">
            <div class="cm-spinner" role="status" aria-label="Loading context data"></div>
            <span>Bağlam verileri yükleniyor...</span>
        </div>
    </div>
    
    <noscript>
        <div class="alert alert-warning m-3" role="alert">
            <h4>JavaScript Gerekli</h4>
            <p>Bağlam yöneticisi JavaScript gerektirmektedir. Lütfen tarayıcınızda JavaScript'i etkinleştirin.</p>
        </div>
    </noscript>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('{{ $managerId }}');
    if (container && typeof ContextManagerV3 !== 'undefined') {
        try {
            const contextManager = new ContextManagerV3(container, {!! json_encode($componentOptions) !!});
            
            // Store reference for external access
            container.contextManager = contextManager;
            
            // Emit ready event
            container.dispatchEvent(new CustomEvent('context-manager:ready', {
                detail: { contextManager, featureId: {{ $featureId ?: 'null' }} }
            }));
            
            // Handle context manager events
            container.addEventListener('cm:initialized', function() {
                console.log('Context Manager initialized successfully');
            });
            
            container.addEventListener('cm:error', function(e) {
                console.error('Context Manager Error:', e.detail.message);
                
                // Show user-friendly error
                const errorHtml = `
                    <div class="alert alert-danger m-3" role="alert">
                        <h4><i class="fas fa-exclamation-triangle"></i> Hata Oluştu</h4>
                        <p>${e.detail.message}</p>
                        <button type="button" class="btn btn-outline-danger btn-sm mt-2" onclick="location.reload()">
                            <i class="fas fa-redo"></i> Yeniden Dene
                        </button>
                    </div>
                `;
                
                container.querySelector('.cm-content').innerHTML = errorHtml;
            });
            
            container.addEventListener('cm:optimized', function(e) {
                // Show success message for optimization
                if (typeof toastr !== 'undefined') {
                    toastr.success('Bağlam optimizasyonu tamamlandı');
                }
            });
            
        } catch (error) {
            console.error('Failed to initialize Context Manager:', error);
            
            // Show error state
            container.querySelector('.cm-content').innerHTML = `
                <div class="alert alert-danger m-3" role="alert">
                    <h4><i class="fas fa-exclamation-triangle"></i> Başlatma Hatası</h4>
                    <p>Bağlam yöneticisi başlatılırken bir hata oluştu.</p>
                    <button type="button" class="btn btn-outline-danger btn-sm mt-2" onclick="location.reload()">
                        <i class="fas fa-redo"></i> Yeniden Dene
                    </button>
                </div>
            `;
        }
    } else {
        console.warn('ContextManagerV3 not loaded or container not found');
    }
});
</script>
@endpush