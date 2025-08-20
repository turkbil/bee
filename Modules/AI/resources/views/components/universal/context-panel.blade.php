@php
/**
 * Universal AI Context Panel Component - ENTERPRISE V3.0.0
 * 
 * Displays intelligent context information for AI processing
 * Shows detected context variables and smart recommendations
 * 
 * @var array $context - Context data (user, module, time, tenant)
 * @var string $feature - Feature slug for context-specific display
 * @var string $class - Additional CSS classes
 */

$contextTypes = [
    'user' => [
        'icon' => 'fas fa-user',
        'label' => __('ai::admin.user_context'),
        'color' => 'primary'
    ],
    'module' => [
        'icon' => 'fas fa-puzzle-piece',
        'label' => __('ai::admin.module_context'),
        'color' => 'info'
    ],
    'time' => [
        'icon' => 'fas fa-clock',
        'label' => __('ai::admin.time_context'),
        'color' => 'warning'
    ],
    'tenant' => [
        'icon' => 'fas fa-building',
        'label' => __('ai::admin.tenant_context'),
        'color' => 'success'
    ],
    'content' => [
        'icon' => 'fas fa-file-text',
        'label' => __('ai::admin.content_context'),
        'color' => 'secondary'
    ]
];

$hasContext = !empty($context) && is_array($context);
@endphp

@if($hasContext)
<div class="ai-context-panel {{ $class ?? '' }}" 
     x-data="contextPanel(@js($context))"
     x-init="analyzeContext">
    
    <!-- Panel Header -->
    <div class="context-header">
        <div class="d-flex align-items-center justify-content-between">
            <h6 class="context-title mb-0 d-flex align-items-center">
                <i class="fas fa-brain me-2 text-primary"></i>
                {{ __('ai::admin.intelligent_context') }}
                <span class="badge badge-primary ms-2" x-text="contextCount + ' ' + '{{ __('ai::admin.detected') }}'"></span>
            </h6>
            <button 
                class="btn btn-sm btn-outline-secondary" 
                type="button"
                data-bs-toggle="collapse" 
                data-bs-target="#contextDetails"
                aria-expanded="false" 
                aria-controls="contextDetails"
                x-data="{ collapsed: true }"
                x-on:click="collapsed = !collapsed"
            >
                <i class="fas fa-chevron-down transition-transform" 
                   :class="{ 'rotate-180': !collapsed }"></i>
            </button>
        </div>
        
        <!-- Quick Context Summary -->
        <div class="context-summary mt-2">
            <div class="d-flex flex-wrap gap-1">
                @foreach($contextTypes as $type => $config)
                    @if(isset($context[$type]))
                        <span class="badge bg-{{ $config['color'] }} bg-opacity-15 text-{{ $config['color'] }} border border-{{ $config['color'] }}">
                            <i class="{{ $config['icon'] }} me-1"></i>
                            {{ $config['label'] }}
                        </span>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <!-- Collapsible Context Details -->
    <div class="collapse" id="contextDetails">
        <div class="context-body mt-3">
            <div class="row">
                @foreach($contextTypes as $type => $config)
                    @if(isset($context[$type]))
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="context-item border border-{{ $config['color'] }} rounded p-3">
                                <div class="context-item-header mb-2">
                                    <h6 class="mb-0 d-flex align-items-center text-{{ $config['color'] }}">
                                        <i class="{{ $config['icon'] }} me-2"></i>
                                        {{ $config['label'] }}
                                    </h6>
                                </div>
                                <div class="context-item-content">
                                    @if(is_array($context[$type]))
                                        <div class="context-data">
                                            @foreach($context[$type] as $key => $value)
                                                <div class="context-pair d-flex justify-content-between align-items-center mb-1">
                                                    <span class="context-key text-muted small">{{ ucfirst($key) }}:</span>
                                                    <span class="context-value badge bg-light text-dark">
                                                        {{ is_array($value) ? json_encode($value) : Str::limit(strval($value), 25) }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="badge bg-{{ $config['color'] }} bg-opacity-15 text-{{ $config['color'] }}">
                                            {{ Str::limit(strval($context[$type]), 40) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <!-- Smart Recommendations -->
            <div class="context-recommendations mt-3" x-show="recommendations.length > 0">
                <h6 class="text-primary">
                    <i class="fas fa-lightbulb me-2"></i>
                    {{ __('ai::admin.smart_recommendations') }}
                </h6>
                <div class="recommendations-list">
                    <template x-for="recommendation in recommendations" :key="recommendation.id">
                        <div class="alert alert-info alert-sm border-info">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-info-circle me-2 mt-1"></i>
                                <div class="flex-grow-1">
                                    <strong x-text="recommendation.title"></strong>
                                    <p class="mb-1 small" x-text="recommendation.description"></p>
                                    <div x-show="recommendation.actions">
                                        <template x-for="action in recommendation.actions" :key="action.id">
                                            <button 
                                                class="btn btn-sm btn-outline-info me-1"
                                                x-text="action.label"
                                                x-on:click="applyRecommendation(action)"
                                            ></button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Context Quality Score -->
            <div class="context-quality mt-3">
                <div class="d-flex align-items-center justify-content-between">
                    <span class="text-muted">{{ __('ai::admin.context_quality') }}:</span>
                    <div class="d-flex align-items-center">
                        <div class="progress me-2" style="width: 100px; height: 8px;">
                            <div 
                                class="progress-bar"
                                :class="getQualityColorClass(contextQuality)"
                                role="progressbar"
                                :style="'width: ' + contextQuality + '%'"
                                :aria-valuenow="contextQuality"
                                aria-valuemin="0"
                                aria-valuemax="100"
                            ></div>
                        </div>
                        <span class="badge" 
                              :class="'bg-' + getQualityColor(contextQuality)"
                              x-text="contextQuality + '%'"></span>
                    </div>
                </div>
                <small class="text-muted" x-text="getQualityDescription(contextQuality)"></small>
            </div>

            <!-- Performance Impact -->
            <div class="performance-impact mt-2">
                <div class="d-flex align-items-center justify-content-between">
                    <span class="text-muted small">{{ __('ai::admin.performance_impact') }}:</span>
                    <span class="badge" 
                          :class="getPerformanceClass(performanceImpact)"
                          x-text="performanceImpact"></span>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@push('styles')
<style>
.ai-context-panel {
    background: linear-gradient(135deg, #f8f9ff 0%, #e3f2fd 100%);
    border: 1px solid var(--bs-primary-border-subtle);
    border-radius: 0.75rem;
    padding: 1rem;
    margin-bottom: 1rem;
    position: relative;
    overflow: hidden;
}

.ai-context-panel::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--bs-primary), var(--bs-info), var(--bs-success));
}

.context-header .context-title {
    font-weight: 600;
    color: var(--bs-primary);
}

.context-summary .badge {
    font-size: 0.75rem;
    font-weight: 500;
}

.context-item {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.context-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.context-pair {
    font-size: 0.85rem;
}

.context-key {
    font-weight: 500;
    min-width: 80px;
}

.context-value {
    font-size: 0.75rem;
    max-width: 150px;
    word-break: break-all;
}

.recommendations-list .alert-sm {
    padding: 0.5rem;
    margin-bottom: 0.5rem;
}

.context-quality .progress {
    border-radius: 10px;
    background-color: var(--bs-gray-200);
}

.context-quality .progress-bar {
    border-radius: 10px;
    transition: all 0.3s ease;
}

.performance-impact .badge {
    min-width: 60px;
    text-align: center;
}

/* Responsive design */
@media (max-width: 768px) {
    .context-summary .badge {
        font-size: 0.7rem;
        margin-bottom: 0.25rem;
    }
    
    .context-item {
        margin-bottom: 1rem;
    }
    
    .context-pair {
        flex-direction: column;
        align-items: flex-start !important;
    }
    
    .context-value {
        max-width: 100%;
        margin-top: 0.25rem;
    }
}

/* Animation classes */
.transition-transform {
    transition: transform 0.3s ease;
}

.rotate-180 {
    transform: rotate(180deg);
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .ai-context-panel {
        background: linear-gradient(135deg, #1a1d29 0%, #2d3748 100%);
        border-color: var(--bs-primary-border-subtle);
        color: var(--bs-light);
    }
    
    .context-item {
        background: rgba(45, 55, 72, 0.8);
        border-color: var(--bs-border-color-translucent) !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('contextPanel', (initialContext = {}) => ({
        context: initialContext,
        contextCount: 0,
        contextQuality: 0,
        performanceImpact: 'Low',
        recommendations: [],
        
        init() {
            this.analyzeContext();
            this.generateRecommendations();
        },
        
        analyzeContext() {
            // Count context items
            this.contextCount = Object.keys(this.context).length;
            
            // Calculate context quality based on completeness and relevance
            this.contextQuality = this.calculateContextQuality();
            
            // Determine performance impact
            this.performanceImpact = this.calculatePerformanceImpact();
        },
        
        calculateContextQuality() {
            let score = 0;
            let maxScore = 100;
            
            // Base score for having any context
            if (this.contextCount > 0) {
                score += 20;
            }
            
            // User context (20 points)
            if (this.context.user) {
                score += 20;
                if (this.context.user.preferences || this.context.user.history) {
                    score += 10; // Bonus for rich user data
                }
            }
            
            // Module context (15 points)
            if (this.context.module) {
                score += 15;
            }
            
            // Time context (10 points)
            if (this.context.time) {
                score += 10;
            }
            
            // Tenant context (10 points)
            if (this.context.tenant) {
                score += 10;
            }
            
            // Content context (15 points)
            if (this.context.content) {
                score += 15;
                if (this.context.content.type && this.context.content.metadata) {
                    score += 10; // Bonus for structured content data
                }
            }
            
            return Math.min(score, maxScore);
        },
        
        calculatePerformanceImpact() {
            const contextSize = JSON.stringify(this.context).length;
            
            if (contextSize < 1000) return 'Low';
            if (contextSize < 5000) return 'Medium';
            return 'High';
        },
        
        generateRecommendations() {
            this.recommendations = [];
            
            // Missing user context
            if (!this.context.user && this.contextQuality < 60) {
                this.recommendations.push({
                    id: 'user_context',
                    title: '{{ __("ai::admin.add_user_context") }}',
                    description: '{{ __("ai::admin.user_context_description") }}',
                    actions: [
                        {
                            id: 'enable_user_context',
                            label: '{{ __("ai::admin.enable_user_context") }}'
                        }
                    ]
                });
            }
            
            // Poor context quality
            if (this.contextQuality < 50) {
                this.recommendations.push({
                    id: 'improve_context',
                    title: '{{ __("ai::admin.improve_context_quality") }}',
                    description: '{{ __("ai::admin.context_quality_description") }}',
                    actions: [
                        {
                            id: 'add_more_context',
                            label: '{{ __("ai::admin.add_context_data") }}'
                        }
                    ]
                });
            }
            
            // High performance impact
            if (this.performanceImpact === 'High') {
                this.recommendations.push({
                    id: 'optimize_context',
                    title: '{{ __("ai::admin.optimize_context") }}',
                    description: '{{ __("ai::admin.context_optimization_description") }}',
                    actions: [
                        {
                            id: 'reduce_context_size',
                            label: '{{ __("ai::admin.optimize_now") }}'
                        }
                    ]
                });
            }
        },
        
        getQualityColor(quality) {
            if (quality >= 80) return 'success';
            if (quality >= 60) return 'info';
            if (quality >= 40) return 'warning';
            return 'danger';
        },
        
        getQualityColorClass(quality) {
            return 'bg-' + this.getQualityColor(quality);
        },
        
        getQualityDescription(quality) {
            if (quality >= 80) return '{{ __("ai::admin.excellent_context") }}';
            if (quality >= 60) return '{{ __("ai::admin.good_context") }}';
            if (quality >= 40) return '{{ __("ai::admin.fair_context") }}';
            return '{{ __("ai::admin.poor_context") }}';
        },
        
        getPerformanceClass(impact) {
            switch (impact) {
                case 'Low': return 'bg-success';
                case 'Medium': return 'bg-warning';
                case 'High': return 'bg-danger';
                default: return 'bg-secondary';
            }
        },
        
        applyRecommendation(action) {
            switch (action.id) {
                case 'enable_user_context':
                    this.enableUserContext();
                    break;
                case 'add_more_context':
                    this.showContextHelp();
                    break;
                case 'reduce_context_size':
                    this.optimizeContext();
                    break;
            }
        },
        
        enableUserContext() {
            // This would trigger user context collection
            console.log('Enabling user context collection...');
            
            // Show success message
            if (window.toast) {
                window.toast.success('{{ __("ai::admin.user_context_enabled") }}');
            }
        },
        
        showContextHelp() {
            // Show modal or guide about improving context
            console.log('Showing context improvement guide...');
        },
        
        optimizeContext() {
            // Optimize context data size
            console.log('Optimizing context data...');
            
            // Remove unnecessary data
            Object.keys(this.context).forEach(key => {
                if (typeof this.context[key] === 'string' && this.context[key].length > 100) {
                    this.context[key] = this.context[key].substring(0, 97) + '...';
                }
            });
            
            // Recalculate metrics
            this.analyzeContext();
            
            if (window.toast) {
                window.toast.success('{{ __("ai::admin.context_optimized") }}');
            }
        }
    }));
});
</script>
@endpush