@extends('admin.layout')

@include('ai::admin.shared.helper')

@section('title', __('ai::admin.usage_examples'))

@push('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.ai.index') }}">AI Modülü</a>
            </li>
            <li class="breadcrumb-item active">AI Kullanım Örnekleri</li>
        </ol>
    </nav>
@endpush

@push('page-header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">AI Modülü</div>
            <h2 class="page-title">
                <span class="text-primary">🤖</span> AI Kullanım Örnekleri Test Merkezi
            </h2>
            <div class="page-subtitle text-muted">
                Her özelliği canlı test edebilir, sonuçları anlık görebilirsiniz
            </div>
        </div>
        <div class="col-auto">
            <div class="btn-list">
                <a href="{{ route('admin.ai.features.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-cog me-2"></i>AI Özellikleri Yönet
                </a>
                <a href="{{ route('admin.ai.settings') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-sliders-h me-2"></i>AI Ayarları
                </a>
            </div>
        </div>
    </div>
@endpush

@push('css')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
.feature-card {
    transition: all 0.2s ease;
    border: 1px solid #e9ecef;
}

.feature-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    border-color: var(--tblr-primary);
}

.feature-emoji {
    font-size: 2rem;
    line-height: 1;
}

.prompt-indicator {
    font-size: 0.8rem;
    opacity: 0.8;
}

.test-input {
    border: 2px solid #e9ecef;
    transition: border-color 0.2s;
}

.test-input:focus {
    border-color: var(--tblr-primary);
    box-shadow: 0 0 0 0.2rem rgba(var(--tblr-primary-rgb), 0.25);
}

.result-area {
    background: var(--tblr-card-bg);
    border: 1px solid var(--tblr-border-color);
    border-radius: 0.75rem;
    margin-top: 1rem;
}

.chat-result-container {
    padding: 1.25rem;
}

.chat-message-content {
    background: var(--tblr-body-bg);
    border: 1px solid var(--tblr-border-color);
    border-radius: 0.75rem;
    padding: 1rem;
    line-height: 1.6;
    font-family: inherit;
    box-shadow: var(--tblr-box-shadow-sm);
    text-align: left;
}

.chat-message-content h1, .chat-message-content h2, .chat-message-content h3,
.chat-message-content h4, .chat-message-content h5, .chat-message-content h6 {
    color: var(--tblr-body-color);
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
    font-weight: 600;
}

.chat-message-content h1 { font-size: 1.5rem; }
.chat-message-content h2 { font-size: 1.35rem; }
.chat-message-content h3 { font-size: 1.2rem; }
.chat-message-content h4 { font-size: 1.1rem; }

.chat-message-content p {
    margin-bottom: 1rem;
    color: var(--tblr-body-color);
}

.chat-message-content ul, .chat-message-content ol {
    margin-bottom: 1rem;
    padding-left: 1.5rem;
}

.chat-message-content li {
    margin-bottom: 0.25rem;
    color: var(--tblr-body-color);
}

.chat-message-content code {
    background: var(--tblr-gray-100);
    padding: 0.125rem 0.25rem;
    border-radius: 0.25rem;
    font-family: var(--tblr-font-monospace);
    font-size: 0.875em;
    color: var(--tblr-body-color);
}

.chat-message-content pre {
    background: var(--tblr-gray-50);
    border: 1px solid var(--tblr-border-color);
    border-radius: 0.5rem;
    padding: 1rem;
    overflow-x: auto;
    margin-bottom: 1rem;
}

.chat-message-content pre code {
    background: none;
    padding: 0;
    border-radius: 0;
}

.chat-message-content blockquote {
    border-left: 4px solid var(--tblr-primary);
    background: var(--tblr-gray-50);
    margin: 1rem 0;
    padding: 0.75rem 1rem;
    color: var(--tblr-muted);
    font-style: italic;
}

.chat-message-content strong {
    color: var(--tblr-body-color);
    font-weight: 600;
}

.chat-message-content em {
    color: var(--tblr-muted);
    font-style: italic;
}

.chat-message-content hr {
    border: none;
    border-top: 1px solid var(--tblr-border-color);
    margin: 1.5rem 0;
}

.loading-spinner {
    display: none;
}

.category-header {
    background: linear-gradient(45deg, var(--tblr-primary), var(--tblr-blue));
    color: white;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 1rem;
}

.example-pill {
    cursor: pointer;
    transition: all 0.2s;
}

.example-pill:hover {
    background-color: var(--tblr-primary) !important;
    color: white !important;
}

.stats-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Token Durumu -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stats-card text-center">
                <div class="card-body">
                    <h2 class="mb-0 {{ $tokenStatus['remaining_tokens'] > 0 ? 'text-white' : 'text-warning' }}" id="remaining-token-display">
                        {{ number_format($tokenStatus['remaining_tokens']) }}
                    </h2>
                    <p class="text-white-50 mb-0">Kalan Token</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="mb-0 text-info" id="daily-usage-display">{{ number_format($tokenStatus['daily_usage']) }}</h2>
                    <p class="text-muted mb-0">Bugünkü Kullanım</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="mb-0 text-warning" id="monthly-usage-display">{{ number_format($tokenStatus['monthly_usage']) }}</h2>
                    <p class="text-muted mb-0">Aylık Kullanım</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="mb-0 {{ $tokenStatus['provider_active'] ? 'text-success' : 'text-danger' }}">
                        {{ $tokenStatus['provider_active'] ? '✓' : '✗' }}
                    </h2>
                    <p class="text-muted mb-0">{{ ucfirst($tokenStatus['provider']) }} API</p>
                </div>
            </div>
        </div>
    </div>

    @if(empty($features))
        <!-- Özellik Bulunamadı -->
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="empty">
                    <div class="empty-img">
                        <i class="fas fa-robot fa-3x text-muted"></i>
                    </div>
                    <p class="empty-title">Henüz aktif AI özelliği bulunmuyor</p>
                    <p class="empty-subtitle text-muted">
                        Examples sayfasında görünecek AI özellikleri eklemek için AI Yönetim panelini kullanın.
                    </p>
                    <div class="empty-action">
                        <a href="{{ route('admin.ai.features.index') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>AI Özellikleri Yönet
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- AI Özellikleri Kategoriler -->
        @foreach($features as $category => $categoryFeatures)
        <div class="category-section mb-5">
            <div class="category-header">
                <h3 class="mb-0">
                    <i class="fas fa-layer-group me-2"></i>
                    {{ $categoryNames[$category] ?? ucfirst($category) }}
                    <span class="badge bg-white text-primary ms-2">{{ count($categoryFeatures) }} özellik</span>
                </h3>
            </div>

            <div class="row">
                @foreach($categoryFeatures as $feature)
                <div class="col-md-6 mb-4">
                    <div class="card feature-card h-100">
                        <div class="card-body">
                            <!-- Özellik Başlığı -->
                            <div class="d-flex align-items-center mb-3">
                                <div class="feature-emoji me-3">{{ $feature->emoji ?? '🤖' }}</div>
                                <div class="flex-fill">
                                    <h5 class="card-title mb-1">{{ $feature->name ?? 'AI Özelliği' }}</h5>
                                    <div class="text-muted small">{{ $feature->getCategoryName() }}</div>
                                </div>
                                @if($feature->is_featured)
                                    <span class="badge bg-warning-lt">⭐</span>
                                @endif
                            </div>

                            <!-- Açıklama -->
                            @if($feature->description)
                                <p class="text-muted small mb-3">{{ Str::limit($feature->description, 80) }}</p>
                            @endif

                            <!-- Prompt Bilgisi -->
                            <div class="prompt-indicator text-muted mb-3">
                                <i class="fas fa-comments me-1"></i>
                                {{ $feature->prompts->count() }} prompt bağlı
                                @if($feature->prompts->count() > 0)
                                    • Ana prompt: {{ $feature->prompts->first()->name }}
                                @endif
                            </div>

                            <!-- Hızlı Örnekler -->
                            @if($feature->example_inputs && count($feature->example_inputs) > 0)
                                <div class="mb-3">
                                    <div class="small text-muted mb-2">Hızlı Örnekler:</div>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach(array_slice($feature->example_inputs, 0, 3) as $example)
                                            <span class="badge bg-secondary-lt example-pill" 
                                                  onclick="fillExample('{{ $feature->id }}', '{{ addslashes($example['text'] ?? '') }}')">
                                                {{ Str::limit($example['text'] ?? '', 20) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Test Alanı -->
                            <div class="mb-3">
                                <label class="form-label small">Test Metni:</label>
                                <textarea 
                                    id="input-{{ $feature->id }}" 
                                    class="form-control test-input" 
                                    rows="3" 
                                    placeholder="{{ $feature->input_placeholder ?? 'Test metninizi buraya yazın...' }}"></textarea>
                            </div>

                            <!-- Test Mode Seçimi -->
                            <div class="mb-3">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="testMode{{ $feature->id }}" id="demo{{ $feature->id }}" value="demo">
                                    <label class="form-check-label" for="demo{{ $feature->id }}">
                                        <span class="text-muted">Demo Test</span>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="testMode{{ $feature->id }}" id="realAI{{ $feature->id }}" value="real" checked>
                                    <label class="form-check-label" for="realAI{{ $feature->id }}">
                                        <span class="text-primary fw-bold">⚡ Gerçek AI</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Test Butonu -->
                            <button 
                                class="btn btn-primary w-100 mb-3" 
                                onclick="testFeature({{ $feature->id }})"
                                id="btn-{{ $feature->id }}">
                                <span class="btn-text">Canlı Test Et</span>
                                <span class="loading-spinner spinner-border spinner-border-sm ms-2" role="status"></span>
                            </button>

                            <!-- Sonuç Alanı -->
                            <div class="result-area" id="result-{{ $feature->id }}" style="display: none;">
                                <div class="chat-result-container">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-primary text-white rounded-circle me-2">
                                                🤖
                                            </div>
                                            <div>
                                                <div class="fw-bold text-primary">AI Analiz Sonucu</div>
                                                <small class="text-muted" id="result-meta-{{ $feature->id }}">Analiz tamamlandı</small>
                                            </div>
                                        </div>
                                        <button class="btn btn-sm btn-outline-secondary rounded-pill" onclick="clearResult({{ $feature->id }})" title="Sonucu Temizle">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="chat-message-content" id="result-content-{{ $feature->id }}"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Alt Bilgiler -->
                        <div class="card-footer bg-light">
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="text-muted small">Kullanım</div>
                                    <div class="fw-bold">{{ number_format($feature->usage_count ?? 0) }}</div>
                                </div>
                                <div class="col-4">
                                    <div class="text-muted small">Puan</div>
                                    <div class="fw-bold">
                                        {{ ($feature->avg_rating ?? 0) > 0 ? number_format($feature->avg_rating, 1) : '-' }}
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="text-muted small">Seviye</div>
                                    <div class="fw-bold">{{ $feature->getComplexityName() }}</div>
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
</div>

@push('js')
<script>
// CSRF token'ı al
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Test fonksiyonu
async function testFeature(featureId) {
    const inputElement = document.getElementById(`input-${featureId}`);
    const btnElement = document.getElementById(`btn-${featureId}`);
    const resultElement = document.getElementById(`result-${featureId}`);
    const resultContentElement = document.getElementById(`result-content-${featureId}`);
    const btnText = btnElement.querySelector('.btn-text');
    const loadingSpinner = btnElement.querySelector('.loading-spinner');

    // Test mode seçimini al
    const realAIMode = document.getElementById(`realAI${featureId}`).checked;

    // Input kontrolü
    const inputText = inputElement ? inputElement.value.trim() : '';
    
    if (inputElement && !inputText) {
        alert('Lütfen test metni girin.');
        inputElement.focus();
        return;
    }

    // UI durumu - loading
    btnElement.disabled = true;
    btnText.textContent = 'Test Ediliyor...';
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
                real_ai: realAIMode
            })
        });

        const data = await response.json();

        if (data.success) {
            // Meta bilgileri güncelle
            const metaElement = document.getElementById(`result-meta-${featureId}`);
            metaElement.innerHTML = `
                <i class="fas fa-check-circle text-success me-1"></i>
                ${data.tokens_used || 0} token kullanıldı
                ${data.processing_time ? ` • ${data.processing_time}ms` : ''}
                ${data.demo_mode ? ' • Demo Mode' : ' • Real AI'}
            `;
            
            // Token bilgilerini real-time güncelle
            if (data.remaining_tokens !== undefined) {
                const remainingDisplay = document.getElementById('remaining-token-display');
                if (remainingDisplay) {
                    remainingDisplay.textContent = new Intl.NumberFormat().format(data.remaining_tokens);
                }
            }
            
            // AI sonucunu markdown'dan HTML'e çevir ve göster
            resultContentElement.innerHTML = formatAIResponse(data.ai_result || 'Sonuç alınamadı');
            resultElement.style.display = 'block';
        } else {
            // Hata durumu
            resultContentElement.innerHTML = `
                <div class="text-danger mb-2">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Hata: ${data.message || 'Bilinmeyen hata'}
                </div>
            `;
            resultElement.style.display = 'block';
        }

    } catch (error) {
        // Network hatası
        console.error('Test hatası:', error);
        resultContentElement.innerHTML = `
            <div class="text-danger">
                <i class="fas fa-exclamation-triangle me-1"></i>
                Bağlantı hatası: ${error.message}
            </div>
        `;
        resultElement.style.display = 'block';
    } finally {
        // UI durumu - normal
        btnElement.disabled = false;
        btnText.textContent = btnElement.dataset.originalText || 'Canlı Test Et';
        loadingSpinner.style.display = 'none';
    }
}

// Örnek doldurma fonksiyonu
function fillExample(featureId, exampleText) {
    const inputElement = document.getElementById(`input-${featureId}`);
    if (inputElement) {
        inputElement.value = exampleText;
        inputElement.focus();
    }
}

// Sonuç temizleme fonksiyonu
function clearResult(featureId) {
    const resultElement = document.getElementById(`result-${featureId}`);
    resultElement.style.display = 'none';
}

// AI yanıtını güzel HTML formatına çevir
function formatAIResponse(aiResult) {
    if (!aiResult) return 'Sonuç alınamadı';
    
    // Zaten HTML ise direkt döndür
    if (aiResult.includes('<')) {
        return aiResult;
    }
    
    // Markdown-benzeri formatları HTML'e çevir
    let formatted = aiResult
        // Başlıkları çevir
        .replace(/^### (.*$)/gim, '<h4>$1</h4>')
        .replace(/^## (.*$)/gim, '<h3>$1</h3>')
        .replace(/^# (.*$)/gim, '<h2>$1</h2>')
        
        // Bold ve italic
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.*?)\*/g, '<em>$1</em>')
        
        // Liste öğeleri
        .replace(/^[\s]*[-•] (.+)$/gm, '<li>$1</li>')
        
        // Kod blokları
        .replace(/```([\s\S]*?)```/g, '<pre><code>$1</code></pre>')
        .replace(/`([^`]+)`/g, '<code>$1</code>')
        
        // Satır sonları
        .replace(/\n\n/g, '</p><p>')
        .replace(/\n/g, '<br>');
    
    // Liste wrapper'ları ekle
    formatted = formatted.replace(/(<li>.*?<\/li>)+/gs, '<ul>$&</ul>');
    
    // Paragraf wrapper'ları ekle
    if (!formatted.includes('<p>') && !formatted.includes('<h')) {
        formatted = '<p>' + formatted + '</p>';
    }
    
    return formatted;
}

// Sayfa yüklendiğinde buton metinlerini kaydet
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[id^="btn-"]').forEach(btn => {
        const btnText = btn.querySelector('.btn-text');
        btn.dataset.originalText = btnText.textContent;
    });
});
</script>
@endpush
@endsection