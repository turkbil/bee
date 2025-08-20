@extends('admin.layout')

@include('ai::helper')

@section('pretitle', 'AI Yönetimi')
@section('title', 'AI Test Sayfası')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@push('styles')
<style>
    .feature-box {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 15px;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .feature-box:hover {
        background: #e9ecef;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }
    .feature-title {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }
    .feature-desc {
        color: #666;
        font-size: 14px;
    }
    .token-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 20px;
        text-align: center;
    }
    .token-number {
        font-size: 48px;
        font-weight: 700;
        margin: 10px 0;
    }
    .test-btn {
        background: #667eea;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .test-btn:hover {
        background: #5a67d8;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }
    .result-box {
        background: #f0f9ff;
        border: 2px solid #3182ce;
        border-radius: 10px;
        padding: 20px;
        margin-top: 20px;
        display: none;
    }
    .result-box.show {
        display: block;
        animation: slideIn 0.3s ease;
    }
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .loading {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid #f3f3f3;
        border-radius: 50%;
        border-top-color: #667eea;
        animation: spin 1s ease-in-out infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>
@endpush

@section('content')
<div class="container-xl">
    <!-- Token Durumu -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-6">
            <div class="token-card">
                <h3 class="mb-2">Token Bakiyeniz</h3>
                @if($tokenStatus['remaining_tokens'] > 0)
                    <div class="token-number">{{ ai_format_token_count($tokenStatus['remaining_tokens']) }}</div>
                    <p class="mb-0">AI özelliklerini kullanmaya hazırsınız!</p>
                @else
                    <div class="token-number text-warning">0</div>
                    <p class="mb-0">Token satın almanız gerekiyor.</p>
                    <a href="{{ route('admin.ai.credits.packages') }}" class="btn btn-light mt-3">Token Satın Al</a>
                @endif
            </div>
        </div>
    </div>

    <!-- Bilgilendirme -->
    <div class="alert alert-info mb-4">
        <h4 class="alert-heading">
            <i class="fa-solid fa-info-circle"></i> AI Test Sayfasına Hoş Geldiniz!
        </h4>
        <p class="mb-0">
            Aşağıdaki kutulardan herhangi birine tıklayarak AI özelliklerini test edebilirsiniz. 
            Her test token harcar, dikkatli kullanın.
        </p>
    </div>

    <!-- Özellikler -->
    <div class="row">
        @foreach($features as $name => $description)
        <div class="col-md-6 mb-3">
            <div class="feature-box" onclick="testFeature('{{ $name }}')">
                <div class="feature-title">
                    <i class="fa-solid fa-sparkles"></i> {{ $name }}
                </div>
                <div class="feature-desc">{{ $description }}</div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Sonuç Alanı -->
    <div id="resultArea" class="result-box">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">
                <i class="fa-solid fa-robot"></i> AI Sonucu
            </h4>
            <button class="btn btn-sm btn-secondary" onclick="closeResult()">Kapat</button>
        </div>
        <div id="resultContent">
            <!-- Sonuç buraya gelecek -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const examples = {
    'İçerik Oluşturma': 'Organik bal üretimi hakkında bir blog yazısı',
    'Başlık Önerileri': 'Sağlıklı Yaşam İpuçları',
    'İçerik Özeti': 'E-ticaret, elektronik ortamda gerçekleştirilen ticari faaliyetlerin tümüdür. İnternetin yaygınlaşmasıyla birlikte e-ticaret, geleneksel ticarete kıyasla çok daha hızlı büyüyen bir sektör haline gelmiştir. Günümüzde milyonlarca insan, ihtiyaçlarını e-ticaret siteleri üzerinden karşılamaktadır.',
    'SSS Oluşturma': 'Ücretsiz kargo hizmeti sunuyoruz. 150 TL ve üzeri alışverişlerde kargo ücretsizdir.',
    'SEO Analizi': 'En İyi Kahve Makineleri 2024 - Alım Rehberi',
    'İçerik Çevirisi': 'Merhaba, bugün nasılsınız? Umarım iyi bir gün geçiriyorsunuzdur.',
    'İçerik İyileştirme': 'Ürünümüz kalitelidir ve ucuzdur.',
    'Sosyal Medya Metni': 'Yeni sezon ürünlerimiz mağazamızda! Modern tasarımlar ve uygun fiyatlar sizi bekliyor.'
};

function testFeature(featureName) {
    const defaultText = examples[featureName] || 'Test metni';
    const userInput = prompt(`${featureName} için test metni girin:`, defaultText);
    
    if (userInput && userInput.trim() !== '') {
        const resultArea = document.getElementById('resultArea');
        const resultContent = document.getElementById('resultContent');
        
        // Loading göster
        resultContent.innerHTML = `
            <div class="text-center py-4">
                <div class="loading mb-3"></div>
                <p class="mb-0">AI yanıt hazırlıyor...</p>
            </div>
        `;
        resultArea.classList.add('show');
        
        // API çağrısı
        fetch('/admin/ai/test-feature', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                feature_name: featureName,
                input_text: userInput,
                tenant_id: {{ tenant('id') ?? 1 }},
                real_ai: true
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                resultContent.innerHTML = `
                    <div class="alert alert-success mb-3">
                        <strong>✅ İşlem Başarılı!</strong> 
                        ${data.tokens_used_formatted || (data.tokens_used + ' token kullanıldı')}.
                    </div>
                    
                    <div class="mb-3">
                        <h5>Gönderilen:</h5>
                        <div class="bg-light p-3 rounded">
                            ${escapeHtml(userInput)}
                        </div>
                    </div>
                    
                    <div>
                        <h5>AI Yanıtı:</h5>
                        <div class="bg-white border p-3 rounded">
                            ${data.ai_result}
                        </div>
                    </div>
                    
                    <div class="mt-3 text-muted small">
                        <i class="fa-solid fa-clock"></i> İşlem süresi: ${data.processing_time}ms | 
                        <i class="fa-solid fa-coins"></i> Kalan token: ${data.new_balance_formatted || data.new_balance || data.remaining_tokens || 0}
                    </div>
                `;
            } else {
                resultContent.innerHTML = `
                    <div class="alert alert-danger">
                        <strong>❌ Hata:</strong> ${data.message}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Test hatası:', error);
            resultContent.innerHTML = `
                <div class="alert alert-danger">
                    <strong>❌ Bağlantı Hatası:</strong> AI servisi ile iletişim kurulamadı.
                </div>
            `;
        });
    }
}

function closeResult() {
    const resultArea = document.getElementById('resultArea');
    resultArea.classList.remove('show');
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

</script>
@endpush