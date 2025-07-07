@extends('admin.layout')

@section('content')
{{-- Futuristic Hero Section --}}
<div class="ai-profile-hero" style="
    background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 25%, #16213e 50%, #0f3460 75%, #533483 100%);
    position: relative;
    overflow: hidden;
    min-height: 400px;
    border-radius: 0 0 2rem 2rem;
    margin: -1.5rem -1.5rem 2rem -1.5rem;
">
    {{-- Animated Background --}}
    <div class="position-absolute top-0 start-0 w-100 h-100" style="
        background-image: 
            radial-gradient(circle at 20% 20%, rgba(0, 212, 255, 0.3) 0%, transparent 50%),
            radial-gradient(circle at 80% 80%, rgba(147, 51, 234, 0.3) 0%, transparent 50%),
            radial-gradient(circle at 40% 60%, rgba(245, 158, 11, 0.2) 0%, transparent 50%);
        animation: float-bg 15s ease-in-out infinite;
    "></div>
    
    {{-- Digital Grid --}}
    <div class="position-absolute top-0 start-0 w-100 h-100" style="
        background-image: 
            linear-gradient(rgba(59, 130, 246, 0.1) 1px, transparent 1px),
            linear-gradient(90deg, rgba(59, 130, 246, 0.1) 1px, transparent 1px);
        background-size: 50px 50px;
        animation: grid-move 20s linear infinite;
    "></div>
    
    <div class="container-xl position-relative" style="z-index: 10;">
        <div class="row align-items-center min-vh-50">
            <div class="col-lg-8">
                <div class="d-flex align-items-center mb-4">
                    <div class="ai-avatar me-4" style="
                        width: 80px;
                        height: 80px;
                        background: linear-gradient(135deg, #00d4ff, #9333ea);
                        border-radius: 20px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        box-shadow: 0 20px 40px rgba(0, 212, 255, 0.3);
                        animation: pulse-glow 2s ease-in-out infinite;
                    ">
                        <i class="fas fa-robot" style="font-size: 2.5rem; color: white; filter: drop-shadow(0 0 10px rgba(255,255,255,0.5));"></i>
                    </div>
                    <div class="text-white">
                        <h1 style="
                            font-size: 3rem;
                            font-weight: 700;
                            background: linear-gradient(45deg, #00d4ff, #ffffff, #9333ea);
                            -webkit-background-clip: text;
                            -webkit-text-fill-color: transparent;
                            background-clip: text;
                            margin-bottom: 0.5rem;
                            animation: title-shimmer 3s ease-in-out infinite;
                        ">AI Profil Sistemi</h1>
                        <p class="fs-5 text-white-50 mb-0">Yapay zekanızı markanıza özel hale getirin</p>
                    </div>
                </div>
                
                @if(!$profile || !$profile->is_completed)
                    <div class="alert" style="
                        background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(251, 191, 36, 0.1));
                        border: 2px solid rgba(245, 158, 11, 0.3);
                        border-radius: 15px;
                        backdrop-filter: blur(10px);
                    ">
                        <div class="d-flex align-items-center text-white">
                            <div class="me-3">
                                <i class="fas fa-exclamation-triangle" style="font-size: 1.5rem; color: #f59e0b;"></i>
                            </div>
                            <div>
                                <h4 class="text-white mb-1">Profiliniz henüz tamamlanmamış</h4>
                                <p class="text-white-50 mb-0">AI asistanınızı aktifleştirmek için profil bilgilerinizi tamamlayın</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert" style="
                        background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(34, 197, 94, 0.1));
                        border: 2px solid rgba(16, 185, 129, 0.3);
                        border-radius: 15px;
                        backdrop-filter: blur(10px);
                    ">
                        <div class="d-flex align-items-center text-white">
                            <div class="me-3">
                                <i class="fas fa-check-circle" style="font-size: 1.5rem; color: #10b981;"></i>
                            </div>
                            <div>
                                <h4 class="text-white mb-1">AI Profiliniz aktif!</h4>
                                <p class="text-white-50 mb-0">Yapay zeka asistanınız markanıza özel içerik üretmeye hazır</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            
            <div class="col-lg-4">
                <div class="text-center">
                    @if(!$profile || !$profile->is_completed)
                        <div class="progress-circle-hero mb-4" style="
                            width: 150px;
                            height: 150px;
                            margin: 0 auto;
                            position: relative;
                        ">
                            <svg width="150" height="150" style="transform: rotate(-90deg);">
                                <circle cx="75" cy="75" r="65" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="8"/>
                                <circle cx="75" cy="75" r="65" fill="none" stroke="url(#heroGradient)" stroke-width="8" 
                                        stroke-dasharray="408.4" stroke-dashoffset="408.4" stroke-linecap="round"
                                        style="animation: progress-fill 2s ease-out forwards;">
                                    <animate attributeName="stroke-dashoffset" values="408.4;0" dur="2s" fill="freeze"/>
                                </circle>
                                <defs>
                                    <linearGradient id="heroGradient">
                                        <stop offset="0%" style="stop-color:#00d4ff"/>
                                        <stop offset="100%" style="stop-color:#9333ea"/>
                                    </linearGradient>
                                </defs>
                            </svg>
                            <div class="position-absolute top-50 start-50 translate-middle text-center">
                                <div class="text-white fs-2 fw-bold">0%</div>
                                <div class="text-white-50 small">Tamamlandı</div>
                            </div>
                        </div>
                        
                        <a href="{{ route('admin.ai.profile.edit') }}" class="btn btn-lg" style="
                            background: linear-gradient(135deg, #00d4ff, #9333ea);
                            border: none;
                            padding: 1rem 2.5rem;
                            border-radius: 15px;
                            color: white;
                            font-weight: 600;
                            font-size: 1.1rem;
                            box-shadow: 0 10px 30px rgba(0, 212, 255, 0.4);
                            animation: btn-pulse 2s ease-in-out infinite;
                        ">
                            <i class="fas fa-rocket me-2"></i>
                            Profili Oluşturmaya Başla
                        </a>
                    @else
                        <div class="success-indicator" style="
                            width: 150px;
                            height: 150px;
                            margin: 0 auto;
                            background: linear-gradient(135deg, #10b981, #059669);
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            box-shadow: 0 20px 40px rgba(16, 185, 129, 0.4);
                            animation: success-pulse 2s ease-in-out infinite;
                        ">
                            <i class="fas fa-check" style="font-size: 3rem; color: white;"></i>
                        </div>
                        
                        <div class="mt-4">
                            <a href="{{ route('admin.ai.profile.edit') }}" class="btn btn-outline-light btn-lg me-2">
                                <i class="fas fa-edit me-2"></i>
                                Düzenle
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Interactive Features Section --}}
<div class="container-xl">
    @if(!$profile || !$profile->is_completed)
        {{-- Benefits & Usage Areas --}}
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="text-center mb-5" style="
                    background: linear-gradient(45deg, #00d4ff, #9333ea);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    background-clip: text;
                    font-weight: 700;
                    font-size: 2.5rem;
                ">AI Profiliniz Nerede Kullanılacak?</h2>
            </div>
        </div>
        
        {{-- Interactive Cards --}}
        <div class="row g-4 mb-5">
            {{-- Content Generation --}}
            <div class="col-lg-4">
                <div class="feature-card h-100" style="
                    background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(0, 212, 255, 0.05));
                    border: 2px solid rgba(0, 212, 255, 0.2);
                    border-radius: 20px;
                    padding: 2rem;
                    transition: all 0.3s ease;
                    position: relative;
                    overflow: hidden;
                " onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 20px 40px rgba(0, 212, 255, 0.3)';"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                    <div class="text-center mb-4">
                        <div style="
                            width: 80px;
                            height: 80px;
                            background: linear-gradient(135deg, #00d4ff, #0ea5e9);
                            border-radius: 20px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            margin: 0 auto;
                            box-shadow: 0 10px 20px rgba(0, 212, 255, 0.3);
                        ">
                            <i class="fas fa-pen-fancy" style="font-size: 2rem; color: white;"></i>
                        </div>
                    </div>
                    <h4 class="text-center mb-3" style="color: #00d4ff; font-weight: 600;">İçerik Üretimi</h4>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Blog yazıları</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Sosyal medya içerikleri</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Ürün açıklamaları</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>E-mail kampanyaları</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Web sitesi metinleri</li>
                    </ul>
                    <div class="mt-4 p-3" style="background: rgba(0, 212, 255, 0.1); border-radius: 10px;">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Markanızın tonuna uygun, sektörünüze özel içerikler
                        </small>
                    </div>
                </div>
            </div>
            
            {{-- Customer Support --}}
            <div class="col-lg-4">
                <div class="feature-card h-100" style="
                    background: linear-gradient(135deg, rgba(147, 51, 234, 0.1), rgba(147, 51, 234, 0.05));
                    border: 2px solid rgba(147, 51, 234, 0.2);
                    border-radius: 20px;
                    padding: 2rem;
                    transition: all 0.3s ease;
                    position: relative;
                    overflow: hidden;
                " onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 20px 40px rgba(147, 51, 234, 0.3)';"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                    <div class="text-center mb-4">
                        <div style="
                            width: 80px;
                            height: 80px;
                            background: linear-gradient(135deg, #9333ea, #7c3aed);
                            border-radius: 20px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            margin: 0 auto;
                            box-shadow: 0 10px 20px rgba(147, 51, 234, 0.3);
                        ">
                            <i class="fas fa-headset" style="font-size: 2rem; color: white;"></i>
                        </div>
                    </div>
                    <h4 class="text-center mb-3" style="color: #9333ea; font-weight: 600;">Müşteri Desteği</h4>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Otomatik yanıtlar</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Sık sorulan sorular</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Ürün bilgilendirme</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Sipariş takibi</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Teknik destek</li>
                    </ul>
                    <div class="mt-4 p-3" style="background: rgba(147, 51, 234, 0.1); border-radius: 10px;">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            7/24 müşteri memnuniyeti ve hızlı çözümler
                        </small>
                    </div>
                </div>
            </div>
            
            {{-- Marketing & Sales --}}
            <div class="col-lg-4">
                <div class="feature-card h-100" style="
                    background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(245, 158, 11, 0.05));
                    border: 2px solid rgba(245, 158, 11, 0.2);
                    border-radius: 20px;
                    padding: 2rem;
                    transition: all 0.3s ease;
                    position: relative;
                    overflow: hidden;
                " onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 20px 40px rgba(245, 158, 11, 0.3)';"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                    <div class="text-center mb-4">
                        <div style="
                            width: 80px;
                            height: 80px;
                            background: linear-gradient(135deg, #f59e0b, #d97706);
                            border-radius: 20px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            margin: 0 auto;
                            box-shadow: 0 10px 20px rgba(245, 158, 11, 0.3);
                        ">
                            <i class="fas fa-chart-line" style="font-size: 2rem; color: white;"></i>
                        </div>
                    </div>
                    <h4 class="text-center mb-3" style="color: #f59e0b; font-weight: 600;">Pazarlama & Satış</h4>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Satış sunumları</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Pazarlama stratejileri</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Teklif hazırlama</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Reklamlar</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Müşteri analizi</li>
                    </ul>
                    <div class="mt-4 p-3" style="background: rgba(245, 158, 11, 0.1); border-radius: 10px;">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Satışlarınızı artıracak etkili pazarlama araçları
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Steps Process --}}
        <div class="row mb-5">
            <div class="col-12">
                <h3 class="text-center mb-5" style="color: #64748b; font-weight: 600;">Nasıl Çalışır?</h3>
                <div class="row">
                    <div class="col-lg-3 col-md-6 text-center mb-4">
                        <div class="step-indicator" style="
                            width: 60px;
                            height: 60px;
                            background: linear-gradient(135deg, #00d4ff, #0ea5e9);
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            margin: 0 auto 1rem;
                            color: white;
                            font-weight: 700;
                            font-size: 1.5rem;
                        ">1</div>
                        <h5>Bilgilerinizi Girin</h5>
                        <p class="text-muted small">Firma, sektör ve marka bilgilerinizi paylaşın</p>
                    </div>
                    <div class="col-lg-3 col-md-6 text-center mb-4">
                        <div class="step-indicator" style="
                            width: 60px;
                            height: 60px;
                            background: linear-gradient(135deg, #9333ea, #7c3aed);
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            margin: 0 auto 1rem;
                            color: white;
                            font-weight: 700;
                            font-size: 1.5rem;
                        ">2</div>
                        <h5>AI Davranışını Ayarlayın</h5>
                        <p class="text-muted small">Yapay zekanızın konuşma tarzını belirleyin</p>
                    </div>
                    <div class="col-lg-3 col-md-6 text-center mb-4">
                        <div class="step-indicator" style="
                            width: 60px;
                            height: 60px;
                            background: linear-gradient(135deg, #f59e0b, #d97706);
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            margin: 0 auto 1rem;
                            color: white;
                            font-weight: 700;
                            font-size: 1.5rem;
                        ">3</div>
                        <h5>Profili Aktifleştirin</h5>
                        <p class="text-muted small">Tüm bilgileri tamamlayıp AI'nızı aktif hale getirin</p>
                    </div>
                    <div class="col-lg-3 col-md-6 text-center mb-4">
                        <div class="step-indicator" style="
                            width: 60px;
                            height: 60px;
                            background: linear-gradient(135deg, #10b981, #059669);
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            margin: 0 auto 1rem;
                            color: white;
                            font-weight: 700;
                            font-size: 1.5rem;
                        ">4</div>
                        <h5>Kullanmaya Başlayın</h5>
                        <p class="text-muted small">Markanıza özel AI asistanınızla çalışmaya başlayın</p>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- CTA Section --}}
        <div class="text-center mb-5">
            <div class="card" style="
                background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(147, 51, 234, 0.1));
                border: 2px solid rgba(0, 212, 255, 0.2);
                border-radius: 20px;
                padding: 3rem;
            ">
                <h3 class="mb-3" style="
                    background: linear-gradient(45deg, #00d4ff, #9333ea);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    background-clip: text;
                    font-weight: 700;
                ">Hemen Başlayın!</h3>
                <p class="text-muted mb-4 fs-5">Sadece 5 dakikada AI asistanınızı markanıza özel hale getirin</p>
                <a href="{{ route('admin.ai.profile.edit') }}" class="btn btn-lg" style="
                    background: linear-gradient(135deg, #00d4ff, #9333ea);
                    border: none;
                    padding: 1rem 3rem;
                    border-radius: 15px;
                    color: white;
                    font-weight: 600;
                    font-size: 1.2rem;
                    box-shadow: 0 10px 30px rgba(0, 212, 255, 0.4);
                ">
                    <i class="fas fa-magic me-2"></i>
                    Profil Oluşturmaya Başla
                </a>
            </div>
        </div>
        
    @else
        {{-- Profile Completed View --}}
        <div class="row">
            @include('ai::admin.profile.partials.completed-profile', ['profile' => $profile, 'sector' => $sector])
        </div>
    @endif
</div>

<style>
@keyframes float-bg {
    0%, 100% { transform: translate(0, 0) scale(1); }
    50% { transform: translate(10px, -10px) scale(1.05); }
}

@keyframes grid-move {
    0% { transform: translate(0, 0); }
    100% { transform: translate(50px, 50px); }
}

@keyframes pulse-glow {
    0%, 100% { box-shadow: 0 20px 40px rgba(0, 212, 255, 0.3); }
    50% { box-shadow: 0 25px 50px rgba(0, 212, 255, 0.5); }
}

@keyframes title-shimmer {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

@keyframes btn-pulse {
    0%, 100% { transform: scale(1); box-shadow: 0 10px 30px rgba(0, 212, 255, 0.4); }
    50% { transform: scale(1.05); box-shadow: 0 15px 40px rgba(0, 212, 255, 0.6); }
}

@keyframes success-pulse {
    0%, 100% { transform: scale(1); box-shadow: 0 20px 40px rgba(16, 185, 129, 0.4); }
    50% { transform: scale(1.05); box-shadow: 0 25px 50px rgba(16, 185, 129, 0.6); }
}

.feature-card {
    cursor: pointer;
}

.step-indicator {
    animation: bounce 2s ease-in-out infinite;
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}
</style>
@endsection