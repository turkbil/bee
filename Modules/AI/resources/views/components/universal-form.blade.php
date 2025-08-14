@props([
    'featureId' => null,
    'featureSlug' => null, 
    'mode' => 'accordion', // accordion | modal | inline
    'enableAutoSave' => false,
    'autoSaveDelay' => 2000,
    'onFormSubmit' => null,
    'onInputChange' => null,
    'customOptions' => '{}',
    'containerClass' => 'universal-form-container',
    'loadingText' => 'Form yükleniyor...',
    'showTitle' => true,
    'showDescription' => true
])

@php
    // Feature ID'yi slug'dan çözümle
    if (!$featureId && $featureSlug) {
        $feature = \Modules\AI\App\Models\AIFeature::where('slug', $featureSlug)->first();
        $featureId = $feature?->id;
    }
    
    // Feature bilgilerini al
    $feature = null;
    if ($featureId) {
        $feature = \Modules\AI\App\Models\AIFeature::find($featureId);
    }
    
    if (!$feature) {
        // Hata durumu
        $errorMessage = $featureSlug 
            ? "AI Feature bulunamadı: {$featureSlug}" 
            : "Geçersiz Feature ID: {$featureId}";
    }
    
    // JavaScript options
    $jsOptions = array_merge([
        'mode' => $mode,
        'enableAutoSave' => $enableAutoSave,
        'autoSaveDelay' => $autoSaveDelay,
        'onFormSubmit' => $onFormSubmit,
        'onInputChange' => $onInputChange
    ], json_decode($customOptions, true) ?: []);
@endphp

<div class="{{ $containerClass }}" 
     data-feature-id="{{ $featureId }}"
     data-feature-slug="{{ $featureSlug }}"
     data-mode="{{ $mode }}"
     data-options="{{ json_encode($jsOptions) }}">

    @if(isset($errorMessage))
        {{-- Error State --}}
        <div class="alert alert-danger">
            <div class="d-flex align-items-center">
                <i class="ti ti-alert-circle me-2"></i>
                <div>
                    <h4 class="alert-title">Form Yüklenemedi</h4>
                    <div class="text-muted">{{ $errorMessage }}</div>
                </div>
            </div>
        </div>
    @else
        {{-- Feature Header (Opsiyonel) --}}
        @if($showTitle || $showDescription)
            <div class="feature-header mb-4">
                @if($showTitle)
                    <h3 class="feature-title">{{ $feature->name }}</h3>
                @endif
                @if($showDescription && $feature->description)
                    <p class="feature-description text-muted">{{ $feature->description }}</p>
                @endif
            </div>
        @endif
        
        {{-- Form Container --}}
        <div class="form-container">
            {{-- Loading State --}}
            <div class="form-loader text-center p-5">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">{{ $loadingText }}</span>
                </div>
                <p class="text-muted">{{ $loadingText }}</p>
            </div>
            
            {{-- Form Content (JavaScript ile doldurulacak) --}}
            <div class="form-content" style="display: none;">
                {{-- Universal Form Builder JavaScript burada render edecek --}}
            </div>
        </div>
        
        {{-- Result Container --}}
        <div class="result-container mt-4" style="display: none;">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="ti ti-sparkles me-2"></i>
                        AI Sonucu
                    </h4>
                </div>
                <div class="card-body">
                    <div class="result-content">
                        {{-- AI response will be displayed here --}}
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            <i class="ti ti-clock me-1"></i>
                            <span class="generation-time">-</span>
                        </div>
                        <div class="result-actions">
                            <button type="button" class="btn btn-outline-secondary btn-sm copy-result-btn">
                                <i class="ti ti-copy me-1"></i>
                                Kopyala
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm regenerate-btn">
                                <i class="ti ti-refresh me-1"></i>
                                Yeniden Üret
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@unless(isset($errorMessage))
@push('scripts')
{{-- Universal Form Builder JavaScript --}}
<script src="{{ asset('modules/ai/js/universal-form-builder.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    const container = document.querySelector('[data-feature-id="{{ $featureId }}"]');
    if (!container) return;
    
    const formContainer = container.querySelector('.form-content');
    const loadingContainer = container.querySelector('.form-loader');
    const resultContainer = container.querySelector('.result-container');
    const resultContent = container.querySelector('.result-content');
    
    // Options
    const options = {!! json_encode($jsOptions) !!};
    
    // Custom form submit handler
    options.onFormSubmit = async function(formData, feature) {
        try {
            // Show loading
            const submitBtn = document.getElementById('submitFormBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>İşleniyor...';
            }
            
            // Process form via API
            const response = await fetch(`/admin/ai/api/features/{{ $featureId }}/process-form`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const result = await response.json();
            
            // Show result
            displayResult(result);
            
        } catch (error) {
            console.error('Form processing error:', error);
            toastr.error('Form işlenirken hata oluştu: ' + error.message);
        } finally {
            // Reset submit button
            const submitBtn = document.getElementById('submitFormBtn');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="ti ti-sparkles me-1"></i><span class="submit-text">AI ile İşle</span>';
            }
        }
    };
    
    // Initialize Universal Form Builder
    const formBuilder = new UniversalFormBuilder({{ $featureId }}, formContainer, options);
    
    // Store reference
    container.formBuilder = formBuilder;
    
    // Event handlers
    document.addEventListener('universalForm:formReady', function(e) {
        if (e.detail.formBuilder === formBuilder) {
            loadingContainer.style.display = 'none';
            formContainer.style.display = 'block';
        }
    });
    
    document.addEventListener('universalForm:processComplete', function(e) {
        if (e.detail.formBuilder === formBuilder) {
            displayResult(e.detail);
        }
    });
    
    // Display AI result
    function displayResult(result) {
        if (!result || !result.success) {
            toastr.error(result?.message || 'İşlem başarısız');
            return;
        }
        
        // Format result based on type
        let formattedContent = '';
        
        if (typeof result.data === 'string') {
            // Simple text result
            formattedContent = `<div class="generated-text">${result.data.replace(/\n/g, '<br>')}</div>`;
        } else if (result.data && typeof result.data === 'object') {
            // Structured result
            if (result.data.content) {
                formattedContent = `<div class="generated-content">${result.data.content.replace(/\n/g, '<br>')}</div>`;
            }
            
            // Additional data sections
            if (result.data.sections) {
                result.data.sections.forEach(section => {
                    formattedContent += `
                        <div class="result-section mt-3">
                            <h6 class="section-title">${section.title}</h6>
                            <div class="section-content">${section.content}</div>
                        </div>
                    `;
                });
            }
        }
        
        // Update result container
        resultContent.innerHTML = formattedContent;
        
        // Show generation time
        const timeElement = container.querySelector('.generation-time');
        if (timeElement && result.generation_time) {
            timeElement.textContent = result.generation_time + 's';
        }
        
        // Show result container
        resultContainer.style.display = 'block';
        
        // Scroll to result
        resultContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        
        // Success notification
        toastr.success(result.message || 'AI işlemi başarıyla tamamlandı');
    }
    
    // Copy result functionality
    container.addEventListener('click', function(e) {
        if (e.target.closest('.copy-result-btn')) {
            const textContent = resultContent.innerText;
            if (textContent) {
                navigator.clipboard.writeText(textContent).then(() => {
                    toastr.success('İçerik kopyalandı');
                }).catch(() => {
                    toastr.error('Kopyalama başarısız');
                });
            }
        }
        
        if (e.target.closest('.regenerate-btn')) {
            // Re-submit the form
            const form = document.getElementById('ai-universal-form');
            if (form) {
                form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
            }
        }
    });
    
    @if($mode === 'modal')
    // Modal specific functionality
    container.addEventListener('show.bs.modal', function() {
        if (container.formBuilder) {
            container.formBuilder.refresh();
        }
    });
    @endif
});
</script>
@endpush

@push('styles')
<style>
.universal-form-container {
    position: relative;
}

.feature-header {
    text-align: center;
    border-bottom: 1px solid var(--tblr-border-color);
    padding-bottom: 1rem;
    margin-bottom: 1.5rem;
}

.feature-title {
    color: var(--tblr-primary);
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.feature-description {
    font-size: 0.95rem;
    line-height: 1.5;
}

.form-loader {
    min-height: 200px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.universal-ai-form .primary-input-container {
    background: var(--tblr-bg-surface-secondary);
    border-radius: var(--tblr-border-radius);
    padding: 1.5rem;
    margin-bottom: 2rem;
    border: 2px solid var(--tblr-primary-light);
}

.universal-ai-form .primary-input-container .form-label {
    color: var(--tblr-primary);
    font-size: 1.1rem;
}

.universal-ai-form .accordion-button {
    font-weight: 500;
}

.universal-ai-form .submit-section {
    background: var(--tblr-bg-surface);
    border-radius: var(--tblr-border-radius);
    padding: 1.5rem;
}

.result-container .generated-text,
.result-container .generated-content {
    background: var(--tblr-bg-surface-secondary);
    border-radius: var(--tblr-border-radius);
    padding: 1.25rem;
    line-height: 1.6;
    white-space: pre-wrap;
    word-wrap: break-word;
}

.result-section {
    border-left: 3px solid var(--tblr-primary);
    padding-left: 1rem;
}

.result-section .section-title {
    color: var(--tblr-primary);
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.result-actions .btn {
    margin-left: 0.5rem;
}

.form-info {
    font-style: italic;
}

/* Dark mode adjustments */
[data-bs-theme="dark"] .universal-ai-form .primary-input-container {
    background: var(--tblr-dark);
    border-color: var(--tblr-primary-darken);
}

[data-bs-theme="dark"] .result-container .generated-text,
[data-bs-theme="dark"] .result-container .generated-content {
    background: var(--tblr-dark);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .universal-ai-form .primary-input-container {
        padding: 1rem;
    }
    
    .universal-ai-form .submit-section {
        padding: 1rem;
    }
    
    .result-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .result-actions .btn {
        margin: 0.25rem 0;
    }
}

/* Animation for result appearance */
.result-container {
    animation: slideInUp 0.5s ease-out;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Loading state improvements */
.form-loader .spinner-border {
    width: 3rem;
    height: 3rem;
}

/* Validation error styling */
.universal-ai-form .is-invalid {
    border-color: var(--tblr-danger);
    box-shadow: 0 0 0 0.2rem rgba(var(--tblr-danger-rgb), 0.25);
}

.universal-ai-form .invalid-feedback {
    display: block;
    color: var(--tblr-danger);
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

/* Conditional field transitions */
.secondary-input-container {
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.secondary-input-container[style*="display: none"] {
    opacity: 0;
    transform: translateY(-10px);
}
</style>
@endpush
@endunless