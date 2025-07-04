@extends('admin.layout')

@include('ai::admin.shared.helper')

@php
use Illuminate\Support\Str;
@endphp

@section('title', 'AI Prowess & Skills')

@push('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.ai.index') }}">AI Module</a>
        </li>
        <li class="breadcrumb-item active">AI Prowess</li>
    </ol>
</nav>
@endpush

@push('page-header')
<div class="row g-2 align-items-center">
    <div class="col">
        <div class="page-pretitle">{{ __('ai::admin.artificial_intelligence') }}</div>
        <h2 class="page-title">
            <span class="text-primary">🌟</span> {{ __('ai::admin.prowess.title') }}
        </h2>
        <div class="page-subtitle text-muted">
            {{ __('ai::admin.prowess.subtitle') }}
        </div>
    </div>
    <div class="col-auto">
        <div class="btn-list">
            <a href="{{ route('admin.ai.features.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-cog me-2"></i>{{ __('ai::admin.prowess.manage_skills') }}
            </a>
            <a href="{{ route('admin.ai.examples') }}" class="btn btn-outline-secondary">
                <i class="fas fa-code me-2"></i>{{ __('ai::admin.prowess.developer_tools') }}
            </a>
        </div>
    </div>
</div>
@endpush

@push('css')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .prowess-card {
        transition: all 0.3s ease;
        border: 1px solid var(--tblr-border-color);
        background: var(--tblr-card-bg);
        overflow: hidden;
        position: relative;
    }

    .prowess-card:hover {
        box-shadow: var(--tblr-box-shadow-lg);
        border-color: var(--tblr-primary);
    }

    .prowess-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--tblr-primary), var(--tblr-success));
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .prowess-card:hover::before {
        opacity: 1;
    }

    .skill-icon {
        font-size: 3rem;
        line-height: 1;
        margin-bottom: 1rem;
    }

    .category-header {
        background: linear-gradient(135deg, var(--tblr-primary), var(--tblr-blue));
        color: white;
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .skill-badge {
        background: var(--tblr-success);
        color: white;
        border: none;
        font-weight: 600;
        padding: 0.5rem 1rem;
        border-radius: 2rem;
    }

    .test-btn {
        background: linear-gradient(45deg, var(--tblr-primary), var(--tblr-purple));
        border: none;
        color: white;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 2rem;
        transition: all 0.3s ease;
    }

    .test-btn:hover {
        box-shadow: var(--tblr-box-shadow);
        color: white;
    }

    .result-showcase {
        background: var(--tblr-card-bg);
        border: 1px solid var(--tblr-border-color);
        border-radius: 1rem;
        margin-top: 1.5rem;
        overflow: hidden;
    }

    .result-header {
        background: linear-gradient(90deg, var(--tblr-primary), var(--tblr-success));
        color: white;
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .result-content {
        padding: 1.5rem;
        line-height: 1.7;
        font-family: inherit;
        text-align: left;
    }

    .stats-showcase {
        background: linear-gradient(135deg, var(--tblr-primary), var(--tblr-purple));
        color: white;
        border: none;
        border-radius: 1rem;
        overflow: hidden;
    }

    .btn-outline-primary {
        transition: all 0.2s ease;
    }

    .btn-outline-primary:hover {
        /* Sabit duracak */
    }

    .fs-2 {
        font-size: 1.75rem !important;
    }
</style>
@endpush

@section('content')
    <!-- Performance Dashboard -->
    <div class="row mb-5">
        <div class="col-md-3">
            <div class="card stats-showcase text-center">
                <div class="card-body">
                    <div class="display-6 fw-bold" id="token-display">{{ number_format($tokenStatus['remaining_tokens']) }}</div>
                    <p class="text-white-50 mb-0">{{ __('ai::admin.prowess.available_tokens') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="display-6 fw-bold text-success">{{ count($features->flatten()) }}</div>
                    <p class="text-muted mb-0">{{ __('ai::admin.prowess.ai_skills') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="display-6 fw-bold text-info">{{ count($features) }}</div>
                    <p class="text-muted mb-0">{{ __('ai::admin.prowess.categories') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="display-6 fw-bold text-primary">{{ $tokenStatus['provider_active'] ? '●' : '○' }}</div>
                    <p class="text-muted mb-0">{{ $tokenStatus['provider'] }}</p>
                </div>
            </div>
        </div>
    </div>

    @if(empty($features))
    <!-- No Skills Available -->
    <div class="card">
        <div class="card-body text-center py-5">
            <div class="empty">
                <div class="empty-img">
                    <i class="fas fa-robot fa-4x text-muted"></i>
                </div>
                <p class="empty-title h3">{{ __('ai::admin.prowess.no_skills_title') }}</p>
                <p class="empty-subtitle text-muted">
                    {{ __('ai::admin.prowess.no_skills_subtitle') }}
                </p>
                <div class="empty-action">
                    <a href="{{ route('admin.ai.features.index') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>{{ __('ai::admin.prowess.configure_skills') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- AI Skills Showcase -->
    @foreach($features as $category => $categoryFeatures)
    <div class="category-section mb-5">
        <div class="category-header">
            <h2 class="mb-0 position-relative">
                <i class="fas fa-magic me-3"></i>
                {{ $categoryNames[$category] ?? ucfirst($category) }}
                <span class="badge skill-badge ms-3">{{ __('ai::admin.prowess.skills_count', ['count' => count($categoryFeatures)]) }}</span>
            </h2>
            <p class="mb-0 mt-2 opacity-75">Unleash the power of AI in {{ strtolower($categoryNames[$category] ??
                $category) }}</p>
        </div>

        <div class="row">
            @foreach($categoryFeatures as $feature)
            <div class="col-md-6 mb-4">
                <div class="card prowess-card h-100">
                    <div class="card-body text-center">
                        <!-- Skill Icon & Title -->
                        <div class="skill-icon">{{ $feature->emoji ?? '🤖' }}</div>
                        <h3 class="card-title fw-bold mb-3 fs-2">{{ $feature->name }}</h3>

                        <!-- Description -->
                        <p class="text-muted mb-4 fs-5">{{ $feature->description }}</p>

                        <!-- Skill Level -->
                        <div class="mb-4">
                            <span class="badge bg-gradient text-white px-3 py-2">
                                {{ $feature->getComplexityName() }} {{ __('ai::admin.prowess.level') }}
                            </span>
                        </div>

                        <!-- Example Prompts -->
                        @if($feature->example_prompts)
                        <div class="mb-3">
                            <div class="text-muted small mb-2">{{ __('ai::admin.prowess.try_examples') }}</div>
                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                @foreach(array_slice(json_decode($feature->example_prompts, true) ?? [], 0, 3) as $prompt)
                                <button class="btn btn-sm btn-outline-muted"
                                    onclick="setExamplePrompt({{ $feature->id }}, '{{ addslashes($prompt) }}'); console.log('Example clicked: {{ addslashes($prompt) }}');">
                                    {{ Str::limit($prompt, 40) }}
                                </button>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Test Input -->
                        <div class="mb-4">
                            <textarea id="input-{{ $feature->id }}" class="form-control form-control-lg" rows="3"
                                placeholder="{{ $feature->input_placeholder ?? __('ai::admin.prowess.enter_challenge') }}"></textarea>
                        </div>

                        <!-- Test Button -->
                        <button class="test-btn w-100 mb-3" onclick="console.log('Test button clicked for feature:', {{ $feature->id }}); testSkill({{ $feature->id }})"
                            id="btn-{{ $feature->id }}">
                            <span class="btn-text">
                                <i class="fas fa-magic me-2"></i>{{ __('ai::admin.prowess.experience_magic') }}
                            </span>
                            <span class="loading-spinner spinner-border spinner-border-sm ms-2" role="status"
                                style="display: none;"></span>
                        </button>

                        <!-- Result Showcase -->
                        <div class="result-showcase" id="result-{{ $feature->id }}" style="display: none;">
                            <div class="result-header">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-sparkles"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ __('ai::admin.prowess.ai_result') }}</div>
                                        <small class="opacity-75" id="result-meta-{{ $feature->id }}">{{ __('ai::admin.prowess.processing_complete') }}
                                            complete</small>
                                    </div>
                                </div>
                                <button class="btn btn-sm btn-light" onclick="clearResult({{ $feature->id }})"
                                    title="{{ __('ai::admin.prowess.clear_result') }}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="result-content" id="result-content-{{ $feature->id }}"></div>
                        </div>
                    </div>

                    <!-- Card Footer Stats -->
                    <div class="card-footer bg-light text-center">
                        <div class="row">
                            <div class="col-4">
                                <div class="text-muted small">{{ __('ai::admin.prowess.usage') }}</div>
                                <div class="fw-bold">{{ number_format($feature->usage_count ?? 0) }}</div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted small">{{ __('ai::admin.prowess.rating') }}</div>
                                <div class="fw-bold">
                                    {{ ($feature->avg_rating ?? 0) > 0 ? number_format($feature->avg_rating, 1) : '-' }}
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted small">{{ __('ai::admin.prowess.category') }}</div>
                                <div class="fw-bold">{{ $feature->getCategoryName() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
    @endif

@push('js')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Test skill function
async function testSkill(featureId) {
    console.log('testSkill called for feature:', featureId);
    const inputElement = document.getElementById(`input-${featureId}`);
    const btnElement = document.getElementById(`btn-${featureId}`);
    const resultElement = document.getElementById(`result-${featureId}`);
    const resultContentElement = document.getElementById(`result-content-${featureId}`);
    const btnText = btnElement.querySelector('.btn-text');
    const loadingSpinner = btnElement.querySelector('.loading-spinner');
    
    console.log('Elements found:', {
        inputElement: !!inputElement,
        btnElement: !!btnElement,
        resultElement: !!resultElement,
        resultContentElement: !!resultContentElement,
        btnText: !!btnText,
        loadingSpinner: !!loadingSpinner
    });

    const inputText = inputElement ? inputElement.value.trim() : '';
    
    if (inputElement && !inputText) {
        alert('{{ addslashes(__('ai::admin.prowess.enter_challenge_alert')) }}');
        inputElement.focus();
        return;
    }

    // UI state - loading
    btnElement.disabled = true;
    btnText.innerHTML = '<i class="fas fa-cogs me-2"></i>{{ addslashes(__('ai::admin.prowess.ai_working')) }}';
    loadingSpinner.style.display = 'inline-block';
    resultElement.style.display = 'none';

    try {
        const response = await fetch('{{ route("admin.ai.test-feature") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                feature_id: featureId,
                input_text: inputText,
                real_ai: true  // Always use real AI for prowess showcase
            })
        });

        const data = await response.json();

        if (data.success) {
            // Update meta info
            const metaElement = document.getElementById(`result-meta-${featureId}`);
            metaElement.innerHTML = `
                <i class="fas fa-check-circle me-1"></i>
${data.tokens_used || 0} token kullanıldı
                ${data.processing_time ? ` • ${data.processing_time}ms` : ''}
            `;
            
            // Update token display real-time
            if (data.remaining_tokens !== undefined) {
                const tokenDisplay = document.getElementById('token-display');
                if (tokenDisplay) {
                    tokenDisplay.textContent = new Intl.NumberFormat().format(data.remaining_tokens);
                }
            }
            
            // Show AI result with elegant formatting
            resultContentElement.innerHTML = formatAIResponse(data.ai_result || 'No result received');
            resultElement.style.display = 'block';
        } else {
            // Error state
            resultContentElement.innerHTML = `
                <div class="text-danger">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Hata: ${data.message || 'Bilinmeyen hata oluştu'}
                </div>
            `;
            resultElement.style.display = 'block';
        }

    } catch (error) {
        console.error('Skill test error:', error);
        resultContentElement.innerHTML = `
            <div class="text-danger">
                <i class="fas fa-exclamation-triangle me-1"></i>
                Bağlantı hatası: ${error.message}
            </div>
        `;
        resultElement.style.display = 'block';
    } finally {
        // Reset UI state
        btnElement.disabled = false;
        btnText.innerHTML = '<i class="fas fa-magic me-2"></i>{{ addslashes(__('ai::admin.prowess.experience_magic')) }}';
        loadingSpinner.style.display = 'none';
    }
}

// Clear result function
function clearResult(featureId) {
    const resultElement = document.getElementById(`result-${featureId}`);
    resultElement.style.display = 'none';
}

// Set example prompt
function setExamplePrompt(featureId, prompt) {
    console.log('setExamplePrompt called:', { featureId, prompt });
    const inputElement = document.getElementById(`input-${featureId}`);
    console.log('Found input element:', inputElement);
    if (inputElement) {
        inputElement.value = prompt;
        inputElement.focus();
        console.log('Input value set to:', prompt);
    } else {
        console.error('Input element not found for feature:', featureId);
    }
}

// Format AI response for elegant display
function formatAIResponse(aiResult) {
    if (!aiResult) return 'Mevcut sonuç yok';
    
    // Already HTML formatted - return as is
    if (aiResult.includes('<')) {
        return aiResult;
    }
    
    // Clean up markdown and convert to elegant HTML
    let formatted = aiResult
        // Remove markdown headers and make them elegant
        .replace(/^### (.*$)/gim, '<div class="fw-bold text-primary mb-2 mt-3">$1</div>')
        .replace(/^## (.*$)/gim, '<div class="h5 text-primary mb-2 mt-3">$1</div>')
        .replace(/^# (.*$)/gim, '<div class="h4 text-primary mb-3 mt-3">$1</div>')
        
        // Bold and italic
        .replace(/\*\*(.*?)\*\*/g, '<span class="fw-bold text-dark">$1</span>')
        .replace(/\*(.*?)\*/g, '<span class="fst-italic">$1</span>')
        
        // Convert ugly bullet points to elegant format
        .replace(/^[\s]*[-•\*] (.+)$/gm, '<div class="d-flex align-items-start mb-2"><i class="fas fa-check-circle text-success me-2 mt-1"></i><span>$1</span></div>')
        
        // Remove code blocks completely or make them simple
        .replace(/```[\s\S]*?```/g, '')
        .replace(/`([^`]+)`/g, '<span class="badge bg-light text-dark">$1</span>')
        
        // Clean up excessive line breaks
        .replace(/\n{3,}/g, '\n\n')
        .replace(/\n\n/g, '</p><p class="mb-3">')
        .replace(/\n/g, '<br>');
    
    // Wrap in paragraph if not already formatted
    if (!formatted.includes('<p>') && !formatted.includes('<div')) {
        formatted = '<p class="mb-3">' + formatted + '</p>';
    }
    
    // Add proper paragraph wrapper
    if (formatted.includes('<p>') && !formatted.startsWith('<p>')) {
        formatted = '<p class="mb-3">' + formatted;
    }
    if (formatted.includes('</p>') && !formatted.endsWith('</p>')) {
        formatted = formatted + '</p>';
    }
    
    return formatted;
}
</script>
@endpush
@endsection