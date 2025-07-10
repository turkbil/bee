@extends('admin.layout')

@include('ai::helper')

@section('content')
{{-- AI Profile Show - Modern Digital Experience --}}
<div class="ai-profile-show-container">

    {{-- Main Content Container --}}
    <div class="container mt-3">
    <div class="row justify-content-center">
        <div class="col-12">
            
            {{-- Profile Hero Section --}}
            <div class="profile-hero-section mb-4">
                <div class="hero-section">
                    <div class="hero-background">
                        <div class="digital-grid"></div>
                        <div class="floating-elements"></div>
                        <div class="cyber-waves"></div>
                    </div>
                    
                    <div class="hero-content">
                        <div class="container">
                            {{-- Profile Badge --}}
                            <div class="row mb-2">
                                <div class="col-12 text-start">
                                    <div class="hero-main-badge-container">
                                        <span class="badge hero-main-badge">
                                            @if($profile && $profile->is_completed)
                                                <i class="fas fa-check-circle me-2"></i>Yapay Zeka Asistanı Aktif
                                            @else
                                                <i class="fas fa-cog fa-spin me-2"></i>Yapay Zeka Profil Kurulumu
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                
                {{-- Ana İçerik Bölümü --}}
                <div class="row align-items-center">
                    {{-- Sol - Profil Bilgileri --}}
                    <div class="col-lg-8 col-md-7">
                        <div class="hero-left-content">
                            <div class="step-info-container">
                                <div class="ai-hologram" style="
                                    width: 80px;
                                    height: 80px;
                                    background: conic-gradient(from 0deg, #00d4ff, #9333ea, #f59e0b, #10b981, #00d4ff);
                                    border-radius: 50%;
                                    animation: hologram-pulse 4s ease-in-out infinite;
                                    filter: drop-shadow(0 0 20px rgba(0, 212, 255, 0.6));
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    position: relative;
                                ">
                                    <div style="
                                        width: 68px;
                                        height: 68px;
                                        background: linear-gradient(135deg, var(--tblr-bg-surface), var(--tblr-bg-surface-dark));
                                        border-radius: 50%;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        border: 2px solid var(--tblr-border-color);
                                    ">
                                        @if($profile && $profile->is_completed)
                                            <i class="fas fa-robot" style="
                                                font-size: 2rem;
                                                color: #00d4ff;
                                                filter: drop-shadow(0 0 10px rgba(0, 212, 255, 0.8));
                                                animation: float-icon 3s ease-in-out infinite;
                                            "></i>
                                        @else
                                            <i class="fas fa-cog" style="
                                                font-size: 2rem;
                                                color: #f59e0b;
                                                filter: drop-shadow(0 0 10px rgba(245, 158, 11, 0.8));
                                                animation: float-icon 3s ease-in-out infinite;
                                            "></i>
                                        @endif
                                    </div>
                                </div>
                                <div class="step-text-content">
                                    <h1 class="hero-title">
                                        @if($profile && $profile->is_completed)
                                            Merhaba {{ Auth::user()->name ?? 'Kullanıcı' }}!
                                        @else
                                            AI Asistanınız Güçleniyor
                                        @endif
                                    </h1>
                                    <p class="hero-subtitle">
                                        @if($profile && $profile->is_completed)
                                            {{ $profile->company_info['brand_name'] ?? 'Şirketiniz' }} için yapay zeka asistanınız aktif ve kullanıma hazır
                                        @else
                                            Asistanınızı kişiselleştirmek için profili tamamlayın
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Sağ - Progress Circle --}}
                    <div class="col-lg-4 col-md-5">
                        <div class="hero-right-content">
                            <div class="progress-section">
                                <div class="progress-circle-container">
                                    @php
                                        $completionData = $profile ? $profile->getCompletionPercentage() : ['percentage' => 0, 'completed' => 0, 'total' => 0];
                                        $completionPercentage = $completionData['percentage'];
                                        $completedFields = $completionData['completed'];
                                        $totalFields = $completionData['total'];
                                    @endphp
                                    
                                    <div class="progress-circle progress-circle-large">
                                        <svg class="progress-svg" viewBox="0 0 100 100">
                                            <circle cx="50" cy="50" r="45" fill="none" stroke="rgba(var(--tblr-muted-rgb, 255,255,255),0.1)" stroke-width="6"/>
                                            <circle cx="50" cy="50" r="45" fill="none" stroke="url(#gradient)" stroke-width="6" 
                                                    stroke-dasharray="282.74" stroke-dashoffset="{{ 282.74 - (282.74 * $completionPercentage / 100) }}"
                                                    transform="rotate(-90 50 50)" stroke-linecap="round"/>
                                            <defs>
                                                <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                                    <stop offset="0%" style="stop-color:#00d4ff"/>
                                                    <stop offset="50%" style="stop-color:#9333ea"/>
                                                    <stop offset="100%" style="stop-color:#f59e0b"/>
                                                </linearGradient>
                                            </defs>
                                        </svg>
                                        <div class="progress-text">
                                            <span class="progress-percentage">{{ $completionPercentage }}%</span>
                                            <small class="progress-label">
                                                @if($profile && $profile->is_completed)
                                                    Tamamlandı
                                                @else
                                                    Tamamlanma
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Status Information --}}
                <div class="row mt-4">
                    <div class="col-12">
                        @if(!$profile || !$profile->is_completed)
                            <div class="alert alert-warning" style="
                                background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(251, 191, 36, 0.1));
                                border: 2px solid rgba(245, 158, 11, 0.3);
                                border-radius: 15px;
                                backdrop-filter: blur(10px);
                            ">
                                <div class="d-flex align-items-center text-white">
                                    <div class="me-3">
                                        <i class="fas fa-cog fa-spin" style="font-size: 1.5rem; color: #f59e0b;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h4 class="text-white mb-1">🚀 Yapay Zeka Kurulumu Devam Ediyor</h4>
                                        <p class="text-white-50 mb-2">Asistanınızı kişiselleştirmek için birkaç adım daha kaldı</p>
                                        <div class="progress" style="height: 6px; background: rgba(255,255,255,0.1);">
                                            <div class="progress-bar" style="
                                                width: {{ $completionPercentage }}%;
                                                background: linear-gradient(135deg, #00d4ff, #9333ea);
                                            "></div>
                                        </div>
                                        <small class="text-white-50">Tamamlanma: {{ $completionPercentage }}%</small>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                
                {{-- Action Buttons --}}
                <div class="row mt-4">
                    <div class="col-12 text-center">
                        @if($profile && $profile->is_completed)
                            <a href="{{ route('admin.ai.profile.edit') }}" class="btn btn-outline-light btn-lg me-3">
                                <i class="fas fa-edit me-2"></i>
                                Profili Düzenle
                            </a>
                            <a href="{{ route('admin.ai.index') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-robot me-2"></i>
                                AI Asistanı Kullan
                            </a>
                        @else
                            <a href="{{ route('admin.ai.profile.edit') }}" class="btn btn-lg" style="
                                background: linear-gradient(135deg, #00d4ff, #9333ea);
                                border: none;
                                padding: 1rem 2.5rem;
                                border-radius: 15px;
                                color: white;
                                font-weight: 600;
                                font-size: 1.1rem;
                                box-shadow: 0 10px 30px rgba(0, 212, 255, 0.4);
                            ">
                                <i class="fas fa-rocket me-2"></i>
                                Profili Oluşturmaya Başla
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

            {{-- Content Card --}}
            <div class="card border-0 shadow-lg mt-4">
                <div class="card-body p-5">
                    @if($profile && $profile->is_completed)
                        {{-- Profil Tamamlandı - Detayları Göster --}}
                        <div class="row g-4">
                            @include('ai::admin.profile.partials.completed-profile')
                        </div>
                    @else
                        {{-- Profil Tamamlanmamış - Modern Kurulum Rehberi --}}
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <div class="modern-setup-header mb-5 text-center">
                                    <div class="setup-icon-container mb-3">
                                        <div class="setup-main-icon">
                                            <i class="fas fa-cogs"></i>
                                        </div>
                                    </div>
                                    <h3 class="setup-title mb-2">AI Asistanı Kurulum Rehberi</h3>
                                    <p class="setup-subtitle">Yapay zeka asistanınızı kişiselleştirmek için aşağıdaki adımları tamamlayın</p>
                                </div>
                                
                                <div class="modern-setup-steps">
                                    @php
                                        $steps = [
                                            1 => [
                                                'title' => 'Sektör Seçimi', 
                                                'icon' => 'fas fa-industry', 
                                                'desc' => 'Yapay zeka asistanınız için en uygun sektörü seçin',
                                                'color' => 'primary'
                                            ],
                                            2 => [
                                                'title' => 'Temel Bilgiler', 
                                                'icon' => 'fas fa-building', 
                                                'desc' => 'İşletmenizin temel bilgilerini girin',
                                                'color' => 'success'
                                            ],
                                            3 => [
                                                'title' => 'Marka Detayları', 
                                                'icon' => 'fas fa-palette', 
                                                'desc' => 'Markanızın kişiliğini tanımlayın',
                                                'color' => 'warning'
                                            ],
                                            4 => [
                                                'title' => 'Kurucu Bilgileri', 
                                                'icon' => 'fas fa-user-tie', 
                                                'desc' => 'Kurucu bilgilerini paylaşın (isteğe bağlı)',
                                                'color' => 'info'
                                            ],
                                            5 => [
                                                'title' => 'Başarı Hikayeleri', 
                                                'icon' => 'fas fa-trophy', 
                                                'desc' => 'Başarılarınızı ve deneyimlerinizi ekleyin',
                                                'color' => 'orange'
                                            ],
                                            6 => [
                                                'title' => 'AI Davranış Ayarları', 
                                                'icon' => 'fas fa-robot', 
                                                'desc' => 'Yapay zeka asistanınızın davranış tarzını belirleyin',
                                                'color' => 'purple'
                                            ]
                                        ];
                                    @endphp
                                    
                                    <div class="row g-4">
                                        @foreach($steps as $stepNum => $step)
                                            <div class="col-12 col-md-6 col-lg-4">
                                                <div class="modern-step-card">
                                                    <div class="step-card-header">
                                                        <div class="step-number-badge">{{ $stepNum }}</div>
                                                        <div class="step-icon-wrapper step-{{ $step['color'] }}">
                                                            <i class="{{ $step['icon'] }}"></i>
                                                        </div>
                                                    </div>
                                                    <div class="step-card-body">
                                                        <h5 class="step-card-title">{{ $step['title'] }}</h5>
                                                        <p class="step-card-description">{{ $step['desc'] }}</p>
                                                    </div>
                                                    <div class="step-card-footer">
                                                        <div class="step-status-indicator">
                                                            <i class="fas fa-clock"></i>
                                                            <span>Bekliyor</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                
                                <div class="setup-footer text-center mt-5">
                                    <div class="setup-progress-info mb-3">
                                        <div class="d-flex justify-content-center align-items-center gap-3">
                                            <div class="progress-circle-mini">
                                                <svg width="24" height="24" viewBox="0 0 24 24">
                                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none" opacity="0.3"/>
                                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none" 
                                                            stroke-dasharray="63" stroke-dashoffset="{{ 63 - (63 * $completionPercentage / 100) }}" 
                                                            stroke-linecap="round" transform="rotate(-90 12 12)"/>
                                                </svg>
                                            </div>
                                            <span class="text-muted">{{ $completionPercentage }}% Tamamlandı</span>
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.ai.profile.edit') }}" class="btn btn-primary btn-lg px-4 py-2">
                                        <i class="fas fa-play me-2"></i>
                                        Kuruluma Başla
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('admin-assets/css/ai-profile-wizard.css') }}">
<style>
.ai-profile-show-container {
    min-height: 100vh;
}

/* Modern Setup Steps - Dark/Light Mode Compatible */

/* Setup Header */
.modern-setup-header {
    padding: 2rem 0;
}

.setup-icon-container {
    display: flex;
    justify-content: center;
    align-items: center;
}

.setup-main-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--tblr-primary), var(--tblr-purple));
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    box-shadow: 0 8px 25px rgba(var(--tblr-primary-rgb), 0.3);
    animation: gentle-pulse 3s ease-in-out infinite;
}

@keyframes gentle-pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.setup-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--tblr-body-color);
    margin-bottom: 0.5rem;
}

.setup-subtitle {
    font-size: 1rem;
    color: var(--tblr-muted);
    margin-bottom: 0;
}

/* Modern Step Cards */
.modern-step-card {
    background: var(--tblr-card-bg);
    border-radius: 16px;
    padding: 1.5rem;
    height: 100%;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.modern-step-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
}

.modern-step-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--tblr-primary), var(--tblr-purple));
    opacity: 0;
    transition: opacity 0.3s ease;
}

.modern-step-card:hover::before {
    opacity: 1;
}

.step-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.step-number-badge {
    width: 32px;
    height: 32px;
    background: var(--tblr-primary);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.9rem;
}

.step-icon-wrapper {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.step-primary { background: var(--tblr-primary); }
.step-success { background: var(--tblr-success); }
.step-warning { background: var(--tblr-warning); }
.step-info { background: var(--tblr-info); }
.step-orange { background: var(--tblr-orange); }
.step-purple { background: var(--tblr-purple); }

.step-card-body {
    margin-bottom: 1rem;
}

.step-card-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--tblr-body-color);
    margin-bottom: 0.5rem;
}

.step-card-description {
    font-size: 0.9rem;
    color: var(--tblr-muted);
    line-height: 1.5;
    margin-bottom: 0;
}

.step-card-footer {
    padding-top: 1rem;
    position: relative;
}

.step-card-footer::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(var(--tblr-primary-rgb), 0.15), transparent);
}

.step-status-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    color: var(--tblr-muted);
}

.step-status-indicator i {
    font-size: 0.8rem;
}

/* Setup Footer */
.setup-footer {
    padding: 2rem 0;
    position: relative;
}

.setup-footer::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 200px;
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--tblr-primary), transparent);
    opacity: 0.3;
}

.setup-progress-info {
    display: flex;
    justify-content: center;
    align-items: center;
}

.progress-circle-mini {
    color: var(--tblr-primary);
}

.progress-circle-mini svg {
    width: 24px;
    height: 24px;
}

/* Dark Mode Adjustments */
[data-bs-theme="dark"] .modern-step-card {
    background: var(--tblr-dark);
    border-color: var(--tblr-border-color-dark);
}

[data-bs-theme="dark"] .modern-step-card:hover {
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.3);
}

[data-bs-theme="dark"] .step-card-footer::before {
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
}

[data-bs-theme="dark"] .setup-footer::before {
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent);
}

/* Responsive Design */
@media (max-width: 768px) {
    .setup-main-icon {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
    
    .setup-title {
        font-size: 1.5rem;
    }
    
    .modern-step-card {
        padding: 1.25rem;
    }
    
    .step-icon-wrapper {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
    
    .step-number-badge {
        width: 28px;
        height: 28px;
        font-size: 0.8rem;
    }
}

@keyframes hologram-pulse {
    0% { 
        transform: scale(1); 
        filter: drop-shadow(0 0 20px rgba(0, 212, 255, 0.6));
    }
    25% { 
        transform: scale(1.05);
        filter: drop-shadow(0 0 25px rgba(147, 51, 234, 0.6));
    }
    50% { 
        transform: scale(1.1);
        filter: drop-shadow(0 0 30px rgba(245, 158, 11, 0.6));
    }
    75% { 
        transform: scale(1.05);
        filter: drop-shadow(0 0 25px rgba(16, 185, 129, 0.6));
    }
    100% { 
        transform: scale(1); 
        filter: drop-shadow(0 0 20px rgba(0, 212, 255, 0.6));
    }
}

@keyframes float-icon {
    0%, 100% { 
        transform: scale(1); 
    }
    50% { 
        transform: scale(1.2); 
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate progress circle on load
    const progressCircle = document.querySelector('.progress-circle svg circle:last-child');
    if (progressCircle) {
        const currentOffset = progressCircle.style.strokeDashoffset;
        progressCircle.style.strokeDashoffset = '282.74';
        
        setTimeout(() => {
            progressCircle.style.transition = 'stroke-dashoffset 1.5s ease-out';
            progressCircle.style.strokeDashoffset = currentOffset;
        }, 500);
    }
});
</script>
@endpush
@endsection