@php
/**
 * Universal AI Form Builder Component - ENTERPRISE V3.0.0
 * 
 * Features:
 * - Dynamic form generation based on AI feature configuration
 * - Context-aware field generation with smart defaults
 * - Real-time validation and user feedback
 * - Progress tracking and loading states
 * - Multi-language support
 * - Advanced options and collapsible sections
 * - Draft saving and preview capabilities
 * - Responsive design with mobile optimization
 * - Alpine.js integration for reactive functionality
 * 
 * @var array $formConfig - Complete form configuration from UniversalInputManager
 * @var string $featureSlug - Feature identifier for context
 * @var array $contextData - Current context data (user, module, time, tenant)
 * @var bool $showContextPanel - Whether to show context information panel
 * @var string $submitButtonText - Custom submit button text
 * @var string $formAction - Form action URL endpoint
 */
@endphp

<div class="ai-universal-form-builder" data-feature="{{ $featureSlug ?? '' }}">
    <form 
        action="{{ $formAction ?? route('admin.ai.universal.process') }}" 
        method="POST" 
        enctype="multipart/form-data"
        class="needs-validation universal-ai-form"
        novalidate
        x-data="universalFormBuilder({
            feature: '{{ $featureSlug ?? '' }}',
            config: @js($formConfig ?? []),
            context: @js($contextData ?? [])
        })"
        x-on:submit="handleSubmit"
    >
        @csrf
        @method('POST')
        
        <!-- Hidden Fields -->
        <input type="hidden" name="feature_slug" value="{{ $featureSlug ?? '' }}">
        <input type="hidden" name="context_data" x-model="JSON.stringify(contextData)">
        
        <!-- Context Panel -->
        @if($showContextPanel ?? true)
            <x-ai::universal.context-panel 
                :context="$contextData ?? []"
                :feature="$featureSlug ?? ''" 
                class="mb-4"
            />
        @endif

        <!-- Form Fields Container -->
        <div class="form-content">
            @if(isset($formConfig['groups']) && is_array($formConfig['groups']))
                <!-- Grouped Fields -->
                @foreach($formConfig['groups'] as $groupIndex => $group)
                    <div class="form-group-section mb-4" data-group="{{ $groupIndex }}">
                        <!-- Group Header -->
                        @if(!empty($group['name']))
                            <div class="group-header mb-3">
                                <h5 class="group-title d-flex align-items-center">
                                    @if(!empty($group['icon']))
                                        <i class="{{ $group['icon'] }} me-2 text-primary"></i>
                                    @endif
                                    {{ $group['name'] }}
                                </h5>
                                @if(!empty($group['description']))
                                    <p class="group-description text-muted small mb-0">{{ $group['description'] }}</p>
                                @endif
                            </div>
                        @endif

                        <!-- Group Fields -->
                        <div class="row">
                            @foreach($group['fields'] as $fieldIndex => $field)
                                <div class="{{ $field['wrapper_class'] ?? 'col-md-6' }} mb-3">
                                    <x-ai::universal.input-field 
                                        :field="$field"
                                        :context="$contextData ?? []"
                                        :feature="$featureSlug ?? ''"
                                        :fieldIndex="$groupIndex . '_' . $fieldIndex"
                                    />
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif

            @if(isset($formConfig['fields']) && is_array($formConfig['fields']) && !isset($formConfig['groups']))
                <!-- Direct Fields (no grouping) -->
                <div class="row">
                    @foreach($formConfig['fields'] as $fieldIndex => $field)
                        <div class="{{ $field['wrapper_class'] ?? 'col-md-6' }} mb-3">
                            <x-ai::universal.input-field 
                                :field="$field"
                                :context="$contextData ?? []"
                                :feature="$featureSlug ?? ''"
                                :fieldIndex="$fieldIndex"
                            />
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Advanced Options Section -->
            @if(isset($formConfig['advanced_options']) && count($formConfig['advanced_options']) > 0)
                <div class="advanced-options-section mt-4">
                    <div class="card border-primary">
                        <div class="card-header bg-primary bg-opacity-10">
                            <button 
                                class="btn btn-link text-decoration-none p-0 w-100 text-start" 
                                type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#advancedOptions"
                                aria-expanded="false" 
                                aria-controls="advancedOptions"
                                x-data="{ collapsed: true }"
                                x-on:click="collapsed = !collapsed"
                            >
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="d-flex align-items-center">
                                        <i class="fas fa-cogs me-2 text-primary"></i>
                                        <strong>{{ __('ai::admin.advanced_options') }}</strong>
                                    </span>
                                    <i class="fas fa-chevron-down transition-transform" 
                                       :class="{ 'rotate-180': !collapsed }"></i>
                                </div>
                            </button>
                        </div>
                        <div id="advancedOptions" class="collapse">
                            <div class="card-body">
                                <div class="row">
                                    @foreach($formConfig['advanced_options'] as $fieldIndex => $field)
                                        <div class="{{ $field['wrapper_class'] ?? 'col-md-6' }} mb-3">
                                            <x-ai::universal.input-field 
                                                :field="$field"
                                                :context="$contextData ?? []"
                                                :feature="$featureSlug ?? ''"
                                                :fieldIndex="'advanced_' . $fieldIndex"
                                            />
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Context Variables Display -->
        @if(!empty($contextData) && is_array($contextData))
            <div class="context-display mt-4">
                <div class="alert alert-info border-info">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-info-circle me-2 mt-1 text-info"></i>
                        <div class="flex-grow-1">
                            <h6 class="alert-heading mb-2">{{ __('ai::admin.detected_context') }}</h6>
                            <div class="context-tags d-flex flex-wrap gap-1">
                                @foreach($contextData as $key => $value)
                                    <span class="badge bg-info bg-opacity-15 text-info border border-info">
                                        <strong>{{ ucfirst($key) }}:</strong> 
                                        {{ is_array($value) ? json_encode($value) : Str::limit(strval($value), 30) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form Actions -->
        <div class="form-actions mt-4 p-3 bg-light rounded">
            <div class="row align-items-center">
                <!-- Form Statistics -->
                <div class="col-md-6">
                    <div class="form-stats">
                        <small class="text-muted d-flex align-items-center">
                            <i class="fas fa-clock me-2"></i>
                            <span x-text="'Estimated time: ' + estimatedTime + ' seconds'"></span>
                        </small>
                        <small class="text-muted d-flex align-items-center mt-1">
                            <i class="fas fa-list-check me-2"></i>
                            <span x-text="fieldValidationStatus"></span>
                        </small>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="col-md-6">
                    <div class="action-buttons d-flex justify-content-end gap-2">
                        <!-- Preview Button -->
                        @if(($formConfig['supports_preview'] ?? false))
                            <button 
                                type="button" 
                                class="btn btn-outline-info"
                                x-on:click="handlePreview"
                                :disabled="!isFormValid || isSubmitting"
                                data-bs-toggle="tooltip"
                                title="{{ __('ai::admin.preview_tooltip') }}"
                            >
                                <i class="fas fa-eye me-1"></i>
                                {{ __('ai::admin.preview') }}
                            </button>
                        @endif

                        <!-- Save Draft Button -->
                        @if(($formConfig['supports_draft'] ?? false))
                            <button 
                                type="button" 
                                class="btn btn-outline-secondary"
                                x-on:click="handleSaveDraft"
                                :disabled="isSubmitting"
                                data-bs-toggle="tooltip"
                                title="{{ __('ai::admin.save_draft_tooltip') }}"
                            >
                                <i class="fas fa-save me-1"></i>
                                {{ __('ai::admin.save_draft') }}
                            </button>
                        @endif

                        <!-- Generate Button -->
                        <button 
                            type="submit" 
                            class="btn btn-primary btn-lg"
                            :disabled="!isFormValid || isSubmitting"
                            :class="{ 'loading': isSubmitting }"
                        >
                            <span x-show="!isSubmitting" class="d-flex align-items-center">
                                <i class="fas fa-magic me-2"></i>
                                {{ $submitButtonText ?? __('ai::admin.generate_content') }}
                            </span>
                            <span x-show="isSubmitting" class="d-flex align-items-center">
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                <span x-text="submitStatusText">{{ __('ai::admin.processing') }}</span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div x-show="isSubmitting" x-transition class="progress mt-3" style="height: 8px;">
            <div 
                class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
                role="progressbar" 
                :style="'width: ' + submitProgress + '%'"
                :aria-valuenow="submitProgress"
                aria-valuemin="0" 
                aria-valuemax="100"
            ></div>
        </div>
    </form>

    <!-- Loading Overlay -->
    <div 
        class="loading-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
        x-show="isSubmitting"
        x-transition:enter="transition-opacity duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity duration-300"  
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="background: rgba(255, 255, 255, 0.95); z-index: 1050; display: none;"
    >
        <div class="loading-content text-center">
            <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                <span class="visually-hidden">{{ __('ai::admin.loading') }}</span>
            </div>
            <h5 x-text="submitStatusText" class="text-primary"></h5>
            <p class="text-muted small" x-text="'Progress: ' + submitProgress + '%'"></p>
        </div>
    </div>
</div>