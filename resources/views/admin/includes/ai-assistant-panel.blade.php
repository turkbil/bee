{{--
    Global AI Assistant Panel
    Tüm modüllerde kullanılabilir AI asistan paneli
    
    Kullanım:
    @include('admin.includes.ai-assistant-panel', [
        'moduleSlug' => 'page',
        'recordId' => $pageId ?? null,
        'features' => $aiFeatures ?? []
    ])
--}}

@php
    $moduleSlug = $moduleSlug ?? 'unknown';
    $recordId = $recordId ?? null;
    $features = $features ?? [];
    $aiProgress = $aiProgress ?? false;
    $aiAnalysis = $aiAnalysis ?? [];
@endphp

{{-- CSS Include --}}
@push('styles')
<link rel="stylesheet" href="{{ asset('admin-assets/css/ai-response-templates.css') }}">
@endpush

{{-- 🤖 AI ANALIZ SONUÇLARI VE KONTROL PANELİ - A60 STİLİ --}}
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-robot me-2 text-primary"></i>AI Asistan & Analiz Merkezi
        </h5>
    </div>
    <div class="card-body">
        {{-- AI Kontrol Butonları --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <button type="button" wire:click="runQuickAnalysis" class="btn btn-primary w-100" @if($aiProgress) disabled @endif>
                    @if($aiProgress)
                        <i class="fas fa-spinner fa-spin me-2"></i>Analiz Ediliyor...
                    @else
                        <i class="fas fa-tachometer-alt me-2"></i>🚀 Hızlı SEO Analizi
                    @endif
                </button>
            </div>
            <div class="col-md-4">
                <button type="button" wire:click="generateAISuggestions" class="btn btn-success w-100" @if($aiProgress) disabled @endif>
                    @if($aiProgress)
                        <i class="fas fa-spinner fa-spin me-2"></i>Üretiliyor...
                    @else
                        <i class="fas fa-lightbulb me-2"></i>🎯 AI Önerileri
                    @endif
                </button>
            </div>
            <div class="col-md-4">
                <button type="button" wire:click="testAI" class="btn btn-info w-100">
                    <i class="fas fa-vial me-2"></i>🧪 AI Test
                </button>
            </div>
        </div>

        {{-- AI Feature Grid --}}
        @if(!empty($features) && count($features) > 0)
        <div class="row mb-4">
            @foreach($features as $feature)
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-3">
                <div class="card card-link" style="height: 140px;">
                    <div class="card-body d-flex flex-column text-center p-3">
                        <div class="mb-2">
                            <span class="avatar avatar-md" style="font-size: 24px;">
                                {{ $feature['emoji'] ?? '🤖' }}
                            </span>
                        </div>
                        <h6 class="card-title text-truncate mb-2" style="font-size: 13px; line-height: 1.2;">
                            {{ $feature['name'] }}
                        </h6>
                        <div class="mt-auto">
                            <button type="button" 
                                    wire:click="executeAIFeature('{{ $feature['slug'] }}', {{ $recordId }})"
                                    class="btn btn-primary btn-sm w-100"
                                    @if($aiProgress) disabled @endif>
                                @if($aiProgress)
                                    <i class="fas fa-spinner fa-spin me-1"></i>
                                @else
                                    <i class="fas fa-sparkles me-1"></i>
                                @endif
                                {{ $feature['button_text'] ?? $feature['name'] }}
                            </button>
                            
                            @if(isset($feature['token_cost']) && $feature['token_cost'] > 0)
                            <div class="text-muted small mt-1">
                                <i class="fas fa-coins me-1"></i>{{ $feature['token_cost'] }} token
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- AI Progress Indicator --}}
        @if($aiProgress)
        <div class="card mb-4" wire:key="ai-progress-card">
            <div class="card-body text-center py-5">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Yükleniyor...</span>
                </div>
                <h5 class="text-muted">AI analizi devam ediyor...</h5>
                <p class="text-muted small">Lütfen bekleyin, yapay zeka sonuçlarınızı hazırlıyor.</p>
            </div>
        </div>
        @endif

        {{-- AI Analysis Results --}}
        @if(!empty($aiAnalysis) && !$aiProgress)
        <div class="card" wire:key="ai-analysis-results">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <div class="card-title">📊 AI Analiz Sonuçları</div>
                    <div class="ms-auto">
                        <span class="badge bg-primary">
                            🤖 AI {{ now()->format('H:i:s') }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                
                {{-- AI Formatted Response --}}
                @if(!empty($aiAnalysis['ai_formatted_response']))
                <div class="ai-response-content">
                    {!! $aiAnalysis['ai_formatted_response'] !!}
                </div>
                @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    AI yanıtı formatlanamadı. Lütfen tekrar deneyin.
                </div>
                @endif
                
            </div>
        </div>
        @else
        <div class="card border-dashed" wire:key="ai-analysis-placeholder">
            <div class="card-body text-center py-5">
                <div class="text-muted mb-3">
                    <i class="fas fa-robot fa-3x"></i>
                </div>
                <h5 class="text-muted">🤖 AI analiz sonuçları burada görünecek...</h5>
                <p class="text-muted small mb-3">Yukarıdaki AI özelliklerinden birine tıklayın</p>
                <small class="text-muted">
                    {{ $moduleSlug ? 'Modül: ' . ucfirst($moduleSlug) : 'Genel' }} | 
                    {{ $recordId ? 'Kayıt ID: ' . $recordId : 'Yeni Kayıt' }} |
                    Zaman: {{ now()->format('H:i:s') }}
                </small>
            </div>
        </div>
        @endif

    </div>
</div>

{{-- JavaScript for AI Panel --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // AI panel auto-scroll on result
    window.addEventListener('ai-result-updated', function() {
        const aiPanel = document.querySelector('.ai-assistant-panel');
        if (aiPanel) {
            aiPanel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    });
});
</script>
@endpush