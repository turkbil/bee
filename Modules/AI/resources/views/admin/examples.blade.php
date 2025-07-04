@extends('admin.layout')

@include('ai::admin.helper')

@section('pretitle', 'AI Yönetimi')
@section('title', 'AI Kullanım Örnekleri Test Sayfası')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@push('styles')
<style>
    .feature-card {
        transition: all 0.3s ease;
        border: 1px solid #e3e3e3;
    }
    .feature-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .test-result {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
        display: none;
    }
    .test-result.show {
        display: block;
    }
    .code-example {
        background-color: #2b2b2b;
        color: #f8f8f2;
        padding: 10px 15px;
        border-radius: 5px;
        font-family: 'Monaco', 'Courier New', monospace;
        font-size: 13px;
        overflow-x: auto;
    }
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid #f3f3f3;
        border-radius: 50%;
        border-top-color: #3498db;
        animation: spin 1s ease-in-out infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>
@endpush

@section('content')
    <!-- Token Durumu -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="mb-0 {{ $tokenStatus['remaining_tokens'] > 0 ? 'text-primary' : 'text-danger' }}">
                        {{ number_format($tokenStatus['remaining_tokens']) }}
                    </h2>
                    <p class="text-muted mb-0">Kalan Token</p>
                    <small class="text-muted">/ {{ number_format($tokenStatus['total_tokens']) }} toplam</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="mb-0 text-info">{{ number_format($tokenStatus['daily_usage']) }}</h2>
                    <p class="text-muted mb-0">Bugünkü Kullanım</p>
                    <small class="text-muted">token</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="mb-0 text-warning">{{ number_format($tokenStatus['monthly_usage']) }}</h2>
                    <p class="text-muted mb-0">Aylık Kullanım</p>
                    <small class="text-muted">token</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h2 class="mb-0">{{ ucfirst($tokenStatus['provider']) }}</h2>
                    <p class="text-muted mb-0">AI Provider</p>
                    <span class="badge {{ $tokenStatus['provider_active'] ? 'badge-success' : 'badge-danger' }}">
                        {{ $tokenStatus['provider_active'] ? 'Aktif' : 'Pasif' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Hızlı Erişim -->
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="card-title">Hızlı Erişim</h4>
            <div class="btn-group" role="group">
                <a href="{{ route('admin.ai.index') }}" class="btn btn-outline-primary">
                    <i class="ti ti-robot"></i> AI Asistan
                </a>
                <a href="{{ route('admin.ai.conversations.index') }}" class="btn btn-outline-info">
                    <i class="ti ti-messages"></i> Konuşmalar
                </a>
                <a href="{{ route('admin.ai.features') }}" class="btn btn-outline-success">
                    <i class="ti ti-dashboard"></i> Dashboard
                </a>
                <a href="{{ route('admin.ai.settings') }}" class="btn btn-outline-warning">
                    <i class="ti ti-settings"></i> Ayarlar
                </a>
            </div>
        </div>
    </div>

    <!-- Aktif Özellikler -->
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">
                <span class="badge badge-success">✓</span>
                Aktif AI Özellikleri ({{ count(collect($features['active'])->flatten(1)) }})
            </h3>
            <div class="card-subtitle">Test edebileceğiniz AI özellikleri</div>
        </div>
        <div class="card-body">
            @foreach($features['active'] as $categoryKey => $categoryFeatures)
            <div class="mb-4">
                <h4 class="mb-3">{{ str_replace('_', ' ', ucwords(str_replace('_', ' ', $categoryKey))) }}</h4>
                <div class="row">
                    @foreach($categoryFeatures as $index => $feature)
                    <div class="col-md-6 mb-3">
                        <div class="card feature-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-0">{{ $feature['name'] }}</h5>
                                    <span class="badge badge-success">Aktif</span>
                                </div>
                                <p class="text-muted small mb-2">{{ $feature['description'] }}</p>
                                
                                <div class="mb-2">
                                    <span class="badge badge-outline-secondary">{{ $feature['category'] }}</span>
                                </div>
                                
                                <div class="mb-3">
                                    <strong class="small">Kullanım Alanı:</strong>
                                    <div class="small text-muted">{{ $feature['usage'] }}</div>
                                </div>
                                
                                @if(isset($feature['example']))
                                <div class="mb-3">
                                    <strong class="small">Kod Örneği:</strong>
                                    <div class="code-example">{{ $feature['example'] }}</div>
                                </div>
                                @endif

                                <!-- Test Butonları -->
                                <div class="d-grid gap-2">
                                    <button class="btn btn-primary btn-sm" onclick="testFeature('{{ $feature['name'] }}', '{{ $categoryKey }}_{{ $index }}', false)">
                                        <i class="ti ti-flask"></i> Demo Test Et
                                    </button>
                                    <button class="btn btn-success btn-sm" onclick="testFeature('{{ $feature['name'] }}', '{{ $categoryKey }}_{{ $index }}', true)">
                                        <i class="ti ti-rocket"></i> Gerçek AI ile Test Et
                                    </button>
                                </div>
                                <small class="text-muted d-block text-center mt-1">
                                    Demo ücretsiz • Gerçek AI token harcar
                                </small>

                                <!-- Test Sonucu -->
                                <div id="result_{{ $categoryKey }}_{{ $index }}" class="test-result">
                                    <!-- Sonuç buraya gelecek -->
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Modül Entegrasyonları -->
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">Modül Entegrasyonları</h3>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($integrations as $key => $integration)
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $integration['name'] }}</h5>
                            <span class="badge {{ $integration['status'] == 'active' ? 'badge-success' : 'badge-warning' }} mb-2">
                                {{ $integration['status'] == 'active' ? 'Aktif' : 'Planlanan' }}
                            </span>
                            <ul class="list-unstyled small">
                                @foreach($integration['actions'] as $action => $desc)
                                <li><i class="ti ti-check text-success"></i> {{ $desc }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Planlanan Özellikler -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <span class="badge badge-warning">⏳</span>
                Planlanan Özellikler ({{ count(collect($features['potential'])->flatten(1)) }})
            </h3>
        </div>
        <div class="card-body">
            @foreach($features['potential'] as $categoryKey => $categoryFeatures)
            <div class="mb-4">
                <h4 class="mb-3">{{ str_replace('_', ' ', ucwords(str_replace('_', ' ', $categoryKey))) }}</h4>
                <div class="row">
                    @foreach($categoryFeatures as $feature)
                    <div class="col-md-6 mb-3">
                        <div class="card feature-card" style="opacity: 0.7;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-0">{{ $feature['name'] }}</h5>
                                    <span class="badge badge-warning">Yakında</span>
                                </div>
                                <p class="text-muted small mb-2">{{ $feature['description'] }}</p>
                                <div class="small text-muted">
                                    <i class="ti ti-clock"></i> Bu özellik henüz aktif değil
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
@endsection

@push('scripts')
<script>
function testFeature(featureName, featureId, useRealAI = false) {
    const exampleTexts = {
        'İçerik Oluşturma': 'Laravel ile RESTful API Geliştirme Rehberi',
        'Şablondan İçerik': 'MacBook Pro M3',
        'Başlık Alternatifleri': 'Evde Kahve Yapmanın 10 Altın Kuralı',
        'İçerik Özeti': 'Laravel, modern web uygulamaları geliştirmek için kullanılan açık kaynaklı bir PHP framework\'üdür. Taylor Otwell tarafından 2011 yılında geliştirilmeye başlanan Laravel, MVC (Model-View-Controller) mimarisini kullanır ve geliştiricilere temiz, okunabilir kod yazma imkanı sunar. Routing, authentication, sessions, caching gibi web geliştirmede sıkça ihtiyaç duyulan özellikleri hazır olarak sunar.',
        'SSS Oluşturma': 'Profesyonel web tasarım hizmetleri sunuyoruz. Modern, responsive ve SEO uyumlu web siteleri tasarlıyoruz.',
        'Eylem Çağrısı': 'Organik bal üretimi yapan aile işletmesi',
        'SEO Analizi': 'Laravel Cache Kullanımı: Redis, Memcached ve Dosya Cache Sistemleri',
        'Okunabilirlik Analizi': 'Kuantum bilgisayarlar, klasik bilgisayarlardan farklı olarak kuantum mekaniği prensiplerini kullanarak hesaplama yapan cihazlardır.',
        'Anahtar Kelime Çıkarma': 'Laravel framework kullanarak e-ticaret sitesi geliştirme sürecinde dikkat edilmesi gereken güvenlik önlemleri ve performans optimizasyonları',
        'Ton Analizi': 'Merhaba değerli müşterilerimiz! Yeni ürünlerimiz çok yakında sizlerle buluşacak. Heyecanla bekliyoruz!',
        'Meta Etiket Oluşturma': 'Laravel Eğitim Seti - Başlangıçtan İleri Seviyeye',
        'İçerik Çevirisi': 'Welcome to our online store. We offer the best products at competitive prices.',
        'İçerik Yeniden Yazma': 'Ürünlerimiz yüksek kaliteli malzemelerden üretilmektedir.',
        'Başlık Optimizasyonu': 'Web Tasarım Hizmetlerimiz',
        'İçerik Genişletme': 'Laravel güçlü bir framework\'tür.',
        'İyileştirme Önerileri': 'Bu makalede Laravel anlatılmaktadır.',
        'İlgili Konu Önerileri': 'Vue.js ile Single Page Application Geliştirme',
        'İçerik Ana Hatları': 'Sıfırdan e-ticaret sitesi kurulumu',
        'Sosyal Medya Postları': 'Yeni açılan kafemizde enfes kahveler ve tatlılar sizi bekliyor!'
    };
    
    const defaultText = exampleTexts[featureName] || 'Laravel ile modern web uygulaması geliştirme';
    const testMode = useRealAI ? 'Gerçek AI Test' : 'Demo Test';
    const warningText = useRealAI ? '\n\n⚠️ DİKKAT: Bu test gerçek token tüketecek!' : '\n\n✅ Bu demo testtir, token tüketmez.';
    
    const userInput = prompt(`${testMode} - ${featureName}\n\nTest edilecek metni girin:${warningText}`, defaultText);
    
    if (userInput && userInput.trim() !== '') {
        const resultDiv = document.getElementById(`result_${featureId}`);
        
        // Loading göster
        resultDiv.innerHTML = `
            <div class="text-center py-3">
                <div class="loading-spinner"></div>
                <p class="mt-2 mb-0">${testMode} yapılıyor...</p>
            </div>
        `;
        resultDiv.classList.add('show');
        
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
                real_ai: useRealAI
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const alertClass = data.demo_mode ? 'alert-info' : 'alert-success';
                const testLabel = data.demo_mode ? 'Demo Test' : 'Gerçek AI Test';
                const tokenInfo = data.demo_mode ? 'Token tüketilmedi' : `${data.tokens_used} token kullanıldı`;
                
                resultDiv.innerHTML = `
                    <div class="alert ${alertClass} mb-3">
                        <strong>✅ ${testLabel} Başarılı!</strong><br>
                        <small>${tokenInfo} • Süre: ${data.processing_time}ms</small>
                    </div>
                    
                    <div class="mb-3">
                        <h6>📝 Girilen Metin:</h6>
                        <div class="bg-light p-2 rounded">
                            <small>${escapeHtml(userInput)}</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <h6>🤖 AI Sonucu:</h6>
                        <div class="bg-primary text-white p-3 rounded">
                            ${data.ai_result}
                        </div>
                    </div>
                    
                    <div class="text-end">
                        <button class="btn btn-sm btn-secondary" onclick="closeResult('${featureId}')">
                            Kapat
                        </button>
                    </div>
                `;
                
                // Gerçek AI kullanıldıysa token bilgilerini güncelle
                if (!data.demo_mode && data.new_balance !== undefined) {
                    // Token sayacını güncelle
                    document.querySelector('.text-primary').textContent = number_format(data.new_balance);
                }
            } else {
                resultDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <strong>❌ Hata:</strong> ${data.message}
                    </div>
                    <div class="text-end">
                        <button class="btn btn-sm btn-secondary" onclick="closeResult('${featureId}')">
                            Kapat
                        </button>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Test hatası:', error);
            resultDiv.innerHTML = `
                <div class="alert alert-danger">
                    <strong>❌ Bağlantı Hatası:</strong> AI servisi ile iletişim kurulamadı.
                </div>
                <div class="text-end">
                    <button class="btn btn-sm btn-secondary" onclick="closeResult('${featureId}')">
                        Kapat
                    </button>
                </div>
            `;
        });
    }
}

function closeResult(featureId) {
    const resultDiv = document.getElementById(`result_${featureId}`);
    resultDiv.classList.remove('show');
    setTimeout(() => {
        resultDiv.innerHTML = '';
    }, 300);
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

function number_format(number) {
    return new Intl.NumberFormat('tr-TR').format(number);
}
</script>
@endpush