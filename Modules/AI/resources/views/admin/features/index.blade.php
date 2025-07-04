@extends('admin.layout')

@include('ai::admin.shared.helper')

@section('pretitle', __('ai::admin.ai_management'))
@section('title', __('ai::admin.features_capabilities'))


@section('content')
    <!-- Statistics Row -->
    <div class="row mb-4">
        <div class="col-auto">
            <span class="badge badge-success">{{ count(collect($features['active'])->flatten(1)) }} {{ __('ai::admin.active_feature') }}</span>
        </div>
        <div class="col-auto">
            <span class="badge badge-warning">{{ count(collect($features['potential'])->flatten(1)) }} {{ __('ai::admin.planned') }}</span>
        </div>
    </div>

    <!-- Token Status Overview - Modern Design -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar bg-primary-lt me-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <circle cx="12" cy="12" r="9"/>
                                <path d="M9 12h6"/>
                                <path d="M12 9v6"/>
                            </svg>
                        </div>
                        <div>
                            <div class="small text-muted text-uppercase fw-bold">Kullanılabilir</div>
                            @if($tokenStatus['remaining_tokens'] > 0)
                                <div class="h3 mb-0 text-primary" id="features-remaining-tokens">{{ number_format($tokenStatus['remaining_tokens']) }}</div>
                            @else
                                <div class="h3 mb-0 text-danger" id="features-remaining-tokens">0</div>
                            @endif
                        </div>
                    </div>
                    <div class="progress progress-sm">
                        @php
                            $percentage = $tokenStatus['total_tokens'] > 0 ? 
                                ($tokenStatus['remaining_tokens'] / $tokenStatus['total_tokens']) * 100 : 0;
                        @endphp
                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $percentage }}%" id="features-token-progress"></div>
                    </div>
                    <div class="small text-muted mt-2">
                        Toplam: {{ number_format($tokenStatus['total_tokens']) }} token
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar bg-info-lt me-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 8v4l3 3"/>
                                <circle cx="12" cy="12" r="9"/>
                            </svg>
                        </div>
                        <div>
                            <div class="small text-muted text-uppercase fw-bold">Bugün</div>
                            <div class="h3 mb-0 text-info">{{ number_format($tokenStatus['daily_usage']) }}</div>
                        </div>
                    </div>
                    <div class="small text-muted">
                        <i class="ti ti-trending-up"></i> Token tüketimi
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar bg-warning-lt me-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <rect x="4" y="5" width="16" height="16" rx="2"/>
                                <line x1="16" y1="3" x2="16" y2="7"/>
                                <line x1="8" y1="3" x2="8" y2="7"/>
                                <line x1="4" y1="11" x2="20" y2="11"/>
                                <rect x="8" y="15" width="2" height="2"/>
                            </svg>
                        </div>
                        <div>
                            <div class="small text-muted text-uppercase fw-bold">Bu Ay</div>
                            <div class="h3 mb-0 text-warning">{{ number_format($tokenStatus['monthly_usage']) }}</div>
                        </div>
                    </div>
                    <div class="small text-muted">
                        <i class="ti ti-chart-line"></i> Aylık tüketim
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar bg-success-lt me-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                <path d="M21 21v-2a4 4 0 0 0 -3 -3.85"/>
                            </svg>
                        </div>
                        <div class="flex-fill">
                            <div class="small text-muted text-uppercase fw-bold">Sağlayıcı</div>
                            <div class="h3 mb-0">{{ ucfirst($tokenStatus['provider']) }}</div>
                        </div>
                        <div>
                            <span class="badge {{ $tokenStatus['provider_active'] ? 'bg-success' : 'bg-danger' }} badge-blink">
                                {{ $tokenStatus['provider_active'] ? 'Aktif' : 'Pasif' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hızlı Erişim - Modern Style -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h4 class="card-title mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M13 5h8"/>
                    <path d="M13 9h5"/>
                    <path d="M13 15h8"/>
                    <path d="M13 19h5"/>
                    <rect x="3" y="4" width="6" height="6" rx="1"/>
                    <rect x="3" y="14" width="6" height="6" rx="1"/>
                </svg>
                Hızlı Erişim
            </h4>
            <div class="row g-2">
                <div class="col-auto">
                    <a href="{{ route('admin.ai.tokens.packages') }}" class="btn btn-pill btn-outline-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5"/>
                            <path d="M12 12l8 -4.5"/>
                            <path d="M12 12l0 9"/>
                            <path d="M12 12l-8 -4.5"/>
                        </svg>
                        Token Paketleri
                    </a>
                </div>
                <div class="col-auto">
                    <a href="{{ route('admin.ai.tokens.purchases') }}" class="btn btn-pill btn-outline-success">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"/>
                            <rect x="9" y="3" width="6" height="4" rx="2"/>
                            <line x1="9" y1="12" x2="9.01" y2="12"/>
                            <line x1="13" y1="12" x2="15" y2="12"/>
                            <line x1="9" y1="16" x2="9.01" y2="16"/>
                            <line x1="13" y1="16" x2="15" y2="16"/>
                        </svg>
                        Satın Alma Geçmişi
                    </a>
                </div>
                <div class="col-auto">
                    <a href="{{ route('admin.ai.tokens.usage-stats') }}" class="btn btn-pill btn-outline-info">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <line x1="4" y1="19" x2="20" y2="19"/>
                            <polyline points="4 15 8 9 12 11 16 6 20 10"/>
                        </svg>
                        Kullanım İstatistikleri
                    </a>
                </div>
                <div class="col-auto">
                    <a href="{{ route('admin.ai.index') }}" class="btn btn-pill btn-outline-warning">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M21 14l-3 -3h-7a1 1 0 0 1 -1 -1v-6a1 1 0 0 1 1 -1h9a1 1 0 0 1 1 1v10"/>
                            <path d="M14 15v2a1 1 0 0 1 -1 1h-7l-3 3v-10a1 1 0 0 1 1 -1h2"/>
                        </svg>
                        AI Asistan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modül Entegrasyonları -->
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">Modül Entegrasyonları</h3>
            <div class="card-subtitle">AI özelliklerinin hangi modüllerde kullanıldığı</div>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($integrations as $key => $integration)
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h5 class="card-title mb-0">{{ $integration['name'] }}</h5>
                                    <div class="text-secondary small">{{ $key }}</div>
                                </div>
                                @if($integration['status'] == 'active')
                                    <span class="badge badge-success">Aktif</span>
                                @elseif($integration['status'] == 'potential')
                                    <span class="badge badge-warning">Planlanan</span>
                                @else
                                    <span class="badge badge-secondary">Pasif</span>
                                @endif
                            </div>
                            
                            <div class="mb-3">
                                <span class="badge badge-outline">{{ count($integration['actions']) }} özellik</span>
                            </div>

                            <div class="collapse" id="integration-{{ $key }}">
                                <div class="border-top pt-3">
                                    <h6 class="mb-2">Mevcut Özellikler:</h6>
                                    @foreach($integration['actions'] as $action => $description)
                                    <div class="d-flex align-items-start mb-2">
                                        <span class="badge badge-success me-2 mt-1">✓</span>
                                        <div>
                                            <div class="fw-medium">{{ $action }}</div>
                                            <div class="small text-secondary">{{ $description }}</div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <button class="btn btn-sm btn-outline-primary w-100" data-bs-toggle="collapse" data-bs-target="#integration-{{ $key }}">
                                Özellikleri Göster/Gizle
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Aktif Özellikler -->
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title d-flex align-items-center">
                <span class="badge badge-success me-2">✓</span>
                Aktif Özellikler ({{ count(collect($features['active'])->flatten(1)) }})
            </h3>
            <div class="card-subtitle">Şu anda sistemde kullanılabilen AI özellikleri</div>
        </div>
        <div class="card-body">
            @foreach($features['active'] as $categoryKey => $categoryFeatures)
            <div class="mb-4">
                <h4 class="h5 mb-3 border-bottom pb-2">
                    📋 {{ str_replace('_', ' ', ucwords(str_replace('_', ' ', $categoryKey))) }}
                </h4>
                <div class="row">
                    @foreach($categoryFeatures as $feature)
                    <div class="col-md-6 mb-3">
                        <div class="card border-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-0">{{ $feature['name'] }}</h5>
                                    <span class="badge badge-success">✅ Aktif</span>
                                </div>
                                <p class="text-muted mb-3">{{ $feature['description'] }}</p>
                                
                                <div class="mb-2">
                                    <span class="badge badge-outline">{{ $feature['category'] }}</span>
                                </div>
                                
                                <div class="mb-2">
                                    <strong class="small">🎯 Kullanım Alanı:</strong>
                                    <div class="small text-muted">{{ $feature['usage'] }}</div>
                                </div>
                                
                                @if(isset($feature['example']))
                                <div class="mt-3">
                                    <strong class="small">💻 Kod Örneği:</strong>
                                    <div class="bg-light p-2 rounded mt-1">
                                        <code class="small">{{ $feature['example'] }}</code>
                                    </div>
                                </div>
                                @endif

                                <!-- Test Butonları -->
                                <div class="mt-3">
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary" onclick="testFeature('{{ $feature['name'] }}', '{{ $loop->parent->index }}_{{ $loop->index }}', false)">
                                            🧪 Demo Test
                                        </button>
                                        <button class="btn btn-sm btn-outline-success" onclick="testFeature('{{ $feature['name'] }}', '{{ $loop->parent->index }}_{{ $loop->index }}', true)">
                                            🚀 Gerçek AI Test
                                        </button>
                                    </div>
                                    <div class="small text-muted mt-1">
                                        Demo ücretsiz • Gerçek AI token tüketir
                                    </div>
                                </div>
                                
                                <!-- Test Sonuç Accordion -->
                                <div class="collapse mt-3" id="testResult_{{ $loop->parent->index }}_{{ $loop->index }}">
                                    <div class="card border-info">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0">🤖 AI Test Sonucu</h6>
                                                <button type="button" class="btn-close btn-sm" onclick="closeTestResult('{{ $loop->parent->index }}_{{ $loop->index }}')"></button>
                                            </div>
                                            <div id="testResultContent_{{ $loop->parent->index }}_{{ $loop->index }}">
                                                <!-- Sonuç buraya gelecek -->
                                            </div>
                                        </div>
                                    </div>
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

    <!-- Planlanan Özellikler -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title d-flex align-items-center">
                <span class="badge badge-warning me-2">⏳</span>
                Planlanan Özellikler ({{ count(collect($features['potential'])->flatten(1)) }})
            </h3>
            <div class="card-subtitle">Gelecekte eklenecek AI özellikleri</div>
        </div>
        <div class="card-body">
            @foreach($features['potential'] as $categoryKey => $categoryFeatures)
            <div class="mb-4">
                <h4 class="h5 mb-3 border-bottom pb-2">
                    🚀 {{ str_replace('_', ' ', ucwords(str_replace('_', ' ', $categoryKey))) }}
                </h4>
                <div class="row">
                    @foreach($categoryFeatures as $feature)
                    <div class="col-md-6 mb-3">
                        <div class="card border-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-0">{{ $feature['name'] }}</h5>
                                    <span class="badge badge-warning">⏳ Planlanan</span>
                                </div>
                                <p class="text-muted mb-3">{{ $feature['description'] }}</p>
                                
                                <div class="mb-2">
                                    <span class="badge badge-outline">{{ $feature['category'] }}</span>
                                </div>
                                
                                <div class="mb-2">
                                    <strong class="small">🎯 Hedef Kullanım:</strong>
                                    <div class="small text-muted">{{ $feature['usage'] }}</div>
                                </div>

                                <!-- Planlanan özellik için bilgi -->
                                <div class="mt-3">
                                    <div class="alert alert-warning py-2 mb-0">
                                        <small>📅 Bu özellik geliştirilme aşamasındadır</small>
                                    </div>
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
    // Feature cards hover effect
    document.querySelectorAll('.card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.transition = 'all 0.3s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Test feature function
    function testFeature(featureName, cardId, useRealAI = false) {
        // Her özelliğe özel örnek metin - o özelliğin nasıl çalıştığını gösteren
        const exampleTexts = {
            'İçerik Oluşturma': 'Sivas Kangal köpeği',
            'Şablondan İçerik': 'iPhone 15 Pro Max',
            'Başlık Alternatifleri': 'Evde Kahve Demleme Teknikleri',
            'İçerik Özeti': 'Türkiye\'nin en büyük teknoloji fuarı TechnoFest bu yıl İstanbul\'da düzenlenecek. Etkinlikte yapay zeka, robotik, havacılık ve uzay teknolojileri alanında yüzlerce proje sergilenecek. Özellikle gençlerin teknolojiye olan ilgisini artırmayı hedefleyen fuarda, TEKNOFEST\'in ana sponsoru olan BAYKAR\'ın son teknoloji insansız hava araçları da tanıtılacak.',
            'SSS Oluşturma': 'Online yoga dersleri veriyoruz. Uzman eğitmenlerimizle evden yoga yapabilirsiniz.',
            'Eylem Çağrısı': 'Organik zeytinyağı üretim çiftliği',
            'SEO Analizi': 'WordPress site hızlandırma rehberi: Cache, CDN ve optimizasyon teknikleri',
            'Okunabilirlik Analizi': 'Blockchain teknolojisi merkezi olmayan bir veri tabanı sistemidir ve kriptografik hash fonksiyonları kullanarak bilgilerin güvenliğini sağlar.',
            'Anahtar Kelime Çıkarma': 'Organik tarım yöntemleri ile yetiştirilen domates, biber ve patlıcan sebzeleri sağlıklı beslenmenin temel taşlarıdır.',
            'Ton Analizi': 'Merhaba arkadaşlar! Bugün sizlere süper eğlenceli bir tarif getirdim. Kesinlikle denemelisiniz!',
            'Meta Etiket Oluşturma': 'İstanbul\'da açılacak yeni müze',
            'İçerik Çevirisi': 'Good morning everyone, welcome to our cooking show',
            'İçerik Yeniden Yazma': 'Bu ürün çok kaliteli ve fiyatı uygun',
            'Başlık Optimizasyonu': 'Web tasarım hizmetlerimiz profesyonel ekibimizle',
            'İçerik Genişletme': 'Kahve sağlıklıdır',
            'İyileştirme Önerileri': 'Bu yazımızda Laravel framework anlatılmıştır',
            'İlgili Konu Önerileri': 'React Native ile mobil uygulama geliştirme',
            'İçerik Ana Hatları': 'Sıfırdan dropshipping işi nasıl kurulur',
            'Sosyal Medya Postları': 'Yeni açtığımız restoranda İtalyan mutfağının en lezzetli yemeklerini deneyebilirsiniz'
        };
        
        const defaultText = exampleTexts[featureName] || 'Laravel ile modern web uygulaması geliştirme';
        const testMode = useRealAI ? '🚀 Gerçek AI Test' : '🧪 Demo Test';
        const warningText = useRealAI ? '\n⚠️ Bu test gerçek token tüketecek!' : '\n✅ Bu test ücretsizdir (demo).';
        const userInput = prompt(`${testMode}\n\nTest edilecek metni girin:${warningText}`, defaultText);
        
        if (userInput && userInput.trim() !== '') {
            // Accordion area'yi göster
            const accordionArea = document.getElementById(`testResult_${cardId}`);
            const contentArea = document.getElementById(`testResultContent_${cardId}`);
            
            // Loading göster
            const loadingText = useRealAI ? 'Gerçek AI analizi yapılıyor...' : 'Demo test hazırlanıyor...';
            contentArea.innerHTML = `
                <div class="d-flex align-items-center">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                    <strong>${featureName}</strong> - ${loadingText}
                </div>
            `;
            
            // Accordion'u aç
            accordionArea.classList.add('show');
            
            // AI API çağrısı yap
            fetch('/admin/ai/test-feature', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    feature_name: featureName,
                    input_text: userInput,
                    tenant_id: 1, // 1 numaralı tenant
                    real_ai: useRealAI
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Test tipine göre alert rengi
                    const alertClass = data.demo_mode ? 'alert-info' : 'alert-success';
                    const testLabel = data.demo_mode ? '🧪 Demo Test' : '🚀 Gerçek AI Test';
                    const tokenInfo = data.demo_mode ? 'Demo - ücretsiz' : `${data.tokens_used} token tüketildi`;
                    
                    // Başarılı sonuç accordion'da göster
                    contentArea.innerHTML = `
                        <div class="alert ${alertClass}">
                            <strong>✅ ${testLabel} Başarılı!</strong> ${tokenInfo}
                            ${data.message ? '<br><small>' + data.message + '</small>' : ''}
                        </div>
                        <h6>Girilen Metin:</h6>
                        <div class="bg-light p-2 rounded mb-3">
                            <small>${userInput}</small>
                        </div>
                        <h6>🤖 AI Sonucu:</h6>
                        <div class="bg-primary text-white p-3 rounded">
                            ${data.ai_result}
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">
                                ${data.demo_mode ? 'Demo Modu' : 'Token Kullanımı: ' + data.tokens_used} • 
                                Kalan Token: ${data.new_balance || data.remaining_tokens} • 
                                Süre: ${data.processing_time}ms
                            </small>
                        </div>
                        ${!data.demo_mode ? '<div class="mt-2"><div class="alert alert-info py-2"><small>💫 Token bilgileri güncellendi!</small></div></div>' : ''}
                    `;
                    
                    // Gerçek AI test sonrası sayfa yenile (token bilgileri güncellensin)
                    if (!data.demo_mode) {
                        setTimeout(() => {
                            window.location.reload();
                        }, 3000);
                    }
                } else {
                    // Hata accordion'da göster
                    contentArea.innerHTML = `
                        <div class="alert alert-danger">
                            <strong>Hata:</strong> ${data.message}
                        </div>
                        ${data.error_details ? '<div class="small text-muted">' + data.error_details + '</div>' : ''}
                    `;
                }
            })
            .catch(error => {
                console.error('Test hatası:', error);
                
                contentArea.innerHTML = `
                    <div class="alert alert-danger">
                        <strong>Bağlantı Hatası:</strong> AI servisi ile iletişim kurulamadı.
                    </div>
                `;
            });
        }
    }

    // Accordion kapatma fonksiyonu
    function closeTestResult(cardId) {
        const accordionArea = document.getElementById(`testResult_${cardId}`);
        if (accordionArea) {
            accordionArea.classList.remove('show');
        }
    }
</script>
@endpush