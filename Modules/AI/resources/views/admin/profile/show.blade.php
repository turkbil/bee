@extends('admin.layout')

@include('ai::helper')

@section('content')
{{-- AI Profile Show - Modern Digital Experience --}}
<div class="ai-profile-show-container">

    {{-- Flash Messages --}}
    @if(session('brand_story_info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            {{ session('brand_story_info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('brand_story_error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('brand_story_error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('brand_story_generated'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('brand_story_generated') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

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
                            <div class="step-info-container d-flex align-items-center gap-3">
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
                                    flex-shrink: 0;
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
                                        // Model'den direkt progress bilgisini al (edit sayfalarına göre)
                                        $progressData = $profile ? $profile->getEditPageCompletionPercentage() : ['percentage' => 0, 'completed' => 0, 'total' => 1];
                                        
                                        $completionPercentage = $progressData['percentage'];
                                        $completedFields = $progressData['completed'];
                                        $totalFields = $progressData['total'];
                                    @endphp
                                    
                                    <x-progress-circle 
                                        :total-questions="$totalFields" 
                                        :answered-questions="$completedFields" 
                                        size="large" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Status Information --}}
                <div class="row mt-4">
                    <div class="col-12">
                        @if((!$profile || !$profile->is_completed) && round($completionPercentage) < 25)
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
                                                width: {{ round($completionPercentage) }}%;
                                                background: linear-gradient(135deg, #00d4ff, #9333ea);
                                            "></div>
                                        </div>
                                        <small class="text-white-50">Tamamlanma: {{ round($completionPercentage) }}%</small>
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
                            <a href="{{ route('admin.ai.profile.jquery-edit', ['step' => 1]) }}" class="btn btn-outline-light btn-lg me-3">
                                <i class="fas fa-edit me-2"></i>
                                Profili Düzenle
                            </a>
                            <a href="{{ route('admin.ai.index') }}" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-robot me-2"></i>
                                AI Asistanı Kullan
                            </a>
                        @else
                            <a href="{{ route('admin.ai.profile.jquery-edit', ['step' => 1]) }}" class="btn btn-lg" style="
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
                        
                        {{-- Profil Sıfırlama Butonu - Her durumda görünür --}}
                        @if($profile)
                            <div class="mt-3">
                                <button type="button" class="btn btn-outline-danger btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#resetProfileModal">
                                    <i class="fas fa-redo me-2"></i>
                                    Profili Sıfırla
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

            {{-- Content Card --}}
            <div class="card border-0 shadow-lg mt-4">
                <div class="card-body p-5">
                    @if($profile)
                        {{-- Brand Story Section - Her zaman göster --}}
                        <div class="row g-4 mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-book-open text-primary me-3" style="font-size: 1.5rem;"></i>
                                            <div>
                                                <h3 class="card-title mb-0">Marka Hikayeniz</h3>
                                                <small class="text-muted">AI tarafından özel olarak hazırlandı</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @if($profile->hasBrandStory())
                                            {{-- Hikaye mevcut --}}
                                            <div class="brand-story-content">
                                                <div class="brand-story-text p-4" style="font-size: 1.1rem; line-height: 1.7; background-color: var(--tblr-bg-surface);">
                                                    {!! nl2br(e($profile->brand_story)) !!}
                                                </div>
                                                    
                                                @if($profile->brand_story_created_at)
                                                    <div class="mt-3 text-muted d-flex align-items-center">
                                                        <i class="fas fa-calendar-alt me-2"></i>
                                                        <span>{{ $profile->brand_story_created_at->format('d.m.Y H:i') }} tarihinde oluşturuldu</span>
                                                    </div>
                                                @endif
                                                
                                                {{-- Yeniden Oluştur Butonu --}}
                                                <div class="mt-4">
                                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="regenerateBrandStory()">
                                                        <i class="fas fa-sync-alt me-2"></i>
                                                        Hikayeyi Yeniden Oluştur
                                                    </button>
                                                </div>
                                            </div>
                                        @else
                                            {{-- Hikaye yok --}}
                                            <div class="text-center py-5">
                                                <div class="mb-4">
                                                    <i class="fas fa-magic text-primary" style="font-size: 3rem; opacity: 0.5;"></i>
                                                </div>
                                                <h5 class="text-muted mb-3">Marka hikayeniz henüz oluşturulmadı</h5>
                                                <p class="text-muted mb-4">AI asistanınız profilinize göre özel bir marka hikayesi oluşturacak</p>
                                                
                                                @if($completionPercentage >= 25)
                                                    <button type="button" class="btn btn-primary" onclick="generateBrandStory()">
                                                        <i class="fas fa-wand-magic-sparkles me-2"></i>
                                                        Marka Hikayemi Oluştur
                                                    </button>
                                                @else
                                                    <div class="alert alert-info">
                                                        <i class="fas fa-info-circle me-2"></i>
                                                        Marka hikayesi oluşturmak için profili en az %25 tamamlamanız gerekiyor. 
                                                        (Şu an: %{{ round($completionPercentage) }})
                                                    </div>
                                                    <a href="{{ route('admin.ai.profile.jquery-edit', ['step' => 1]) }}" 
                                                       class="btn btn-outline-primary">
                                                        <i class="fas fa-edit me-2"></i>
                                                        Profili Tamamla
                                                    </a>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if($profile && $profile->is_completed)
                        {{-- Profil Tamamlandı - Diğer Detayları Göster --}}
                        <div class="row g-4">
                            {{-- Status Card --}}
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <i class="fas fa-check-circle text-success" style="font-size: 1.5rem;"></i>
                                                </div>
                                                <div>
                                                    <h3 class="mb-1 text-success">AI Profiliniz Aktif!</h3>
                                                    <p class="text-muted mb-0">Yapay zeka asistanınız markanıza özel içerik üretmeye hazır</p>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <form action="{{ route('admin.ai.profile.reset') }}" method="POST" class="d-inline" 
                                                      onsubmit="return confirm('⚠️ UYARI: Profili sıfırlamak istediğinize emin misiniz?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-muted btn-sm">
                                                        <i class="fas fa-trash-restore me-2"></i>Sıfırla
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                                'title' => 'AI Davranış ve İletişim Ayarları', 
                                                'icon' => 'fas fa-robot', 
                                                'desc' => 'AI asistanınızın iletişim tarzı ve davranış şeklini ayarlayın',
                                                'color' => 'purple'
                                            ]
                                        ];
                                    @endphp
                                    
                                    {{-- 5 Step için Optimize Edilmiş Layout: 2-1-2 Pyramid Design --}}
                                    {{-- İlk sıra: 2 step --}}
                                    <div class="row g-4 mb-4 justify-content-center">
                                        @foreach(array_slice($steps, 0, 2, true) as $stepNum => $step)
                                            <div class="col-12 col-md-6 col-xl-5">
                                                <div class="modern-step-card modern-step-large">
                                                    <div class="step-card-header">
                                                        <div class="step-number-badge step-large">{{ $stepNum }}</div>
                                                        <div class="step-icon-wrapper step-{{ $step['color'] }} step-icon-large">
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
                                    
                                    {{-- Orta sıra: 1 step (featured center) --}}
                                    <div class="row g-4 mb-4 justify-content-center">
                                        @php $centralStep = array_slice($steps, 2, 1, true); @endphp
                                        @foreach($centralStep as $stepNum => $step)
                                            <div class="col-12 col-md-8 col-lg-6">
                                                <div class="modern-step-card modern-step-featured">
                                                    <div class="step-card-header">
                                                        <div class="step-number-badge step-featured">{{ $stepNum }}</div>
                                                        <div class="step-icon-wrapper step-{{ $step['color'] }} step-icon-featured">
                                                            <i class="{{ $step['icon'] }}"></i>
                                                        </div>
                                                    </div>
                                                    <div class="step-card-body text-center">
                                                        <h5 class="step-card-title">{{ $step['title'] }}</h5>
                                                        <p class="step-card-description">{{ $step['desc'] }}</p>
                                                    </div>
                                                    <div class="step-card-footer justify-content-center">
                                                        <div class="step-status-indicator">
                                                            <i class="fas fa-clock"></i>
                                                            <span>Bekliyor</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    {{-- Alt sıra: 2 step --}}
                                    <div class="row g-4 justify-content-center">
                                        @foreach(array_slice($steps, 3, 2, true) as $stepNum => $step)
                                            <div class="col-12 col-md-6 col-xl-5">
                                                <div class="modern-step-card modern-step-large">
                                                    <div class="step-card-header">
                                                        <div class="step-number-badge step-large">{{ $stepNum }}</div>
                                                        <div class="step-icon-wrapper step-{{ $step['color'] }} step-icon-large">
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
                                                            stroke-dasharray="63" stroke-dashoffset="{{ 63 - (63 * round($completionPercentage) / 100) }}" 
                                                            stroke-linecap="round" transform="rotate(-90 12 12)"/>
                                                </svg>
                                            </div>
                                            <span class="text-muted">{{ round($completionPercentage) }}% Tamamlandı</span>
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.ai.profile.jquery-edit', ['step' => 1]) }}" class="btn btn-primary btn-lg px-4 py-2">
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

/* 2-1-2 Layout Specific Styles */
.modern-step-large {
    min-height: 200px;
}

.modern-step-featured {
    min-height: 220px;
    background: linear-gradient(135deg, var(--tblr-card-bg) 0%, rgba(var(--tblr-primary-rgb), 0.02) 100%);
    border: 2px solid rgba(var(--tblr-primary-rgb), 0.1);
    position: relative;
}

.modern-step-featured::before {
    background: linear-gradient(90deg, var(--tblr-primary), var(--tblr-purple));
    opacity: 1;
    height: 3px;
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

.step-large {
    width: 36px;
    height: 36px;
    font-size: 1rem;
}

.step-featured {
    width: 40px;
    height: 40px;
    font-size: 1.1rem;
    background: linear-gradient(135deg, var(--tblr-primary), var(--tblr-purple));
    box-shadow: 0 4px 12px rgba(var(--tblr-primary-rgb), 0.3);
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

.step-icon-large {
    width: 52px;
    height: 52px;
    font-size: 1.3rem;
    border-radius: 14px;
}

.step-icon-featured {
    width: 56px;
    height: 56px;
    font-size: 1.4rem;
    border-radius: 16px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
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
    
    .modern-step-large,
    .modern-step-featured {
        min-height: 180px;
    }
    
    .step-icon-wrapper {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
    
    .step-icon-large {
        width: 44px;
        height: 44px;
        font-size: 1.1rem;
    }
    
    .step-icon-featured {
        width: 48px;
        height: 48px;
        font-size: 1.2rem;
    }
    
    .step-number-badge {
        width: 28px;
        height: 28px;
        font-size: 0.8rem;
    }
    
    .step-large {
        width: 32px;
        height: 32px;
        font-size: 0.9rem;
    }
    
    .step-featured {
        width: 36px;
        height: 36px;
        font-size: 1rem;
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

// Marka hikayesi oluşturma fonksiyonu
function generateBrandStory() {
    const button = event.target;
    const originalText = button.innerHTML;
    
    // Butonu loading state'e al
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Hikaye Oluşturuluyor...';
    
    fetch('{{ route("admin.ai.profile.generate-story") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Başarılı - sayfayı yenile
            location.reload();
        } else {
            // Hata mesajı göster
            alert('Hata: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Hata:', error);
        alert('Hikaye oluşturulurken bir hata oluştu. Lütfen tekrar deneyin.');
    })
    .finally(() => {
        // Butonu normale döndür
        button.disabled = false;
        button.innerHTML = originalText;
    });
}

// Marka hikayesi yeniden oluşturma fonksiyonu
function regenerateBrandStory() {
    if (!confirm('Mevcut hikayeniz silinecek ve yeni bir hikaye oluşturulacak. Devam etmek istiyor musunuz?')) {
        return;
    }
    
    const button = event.target;
    const originalText = button.innerHTML;
    
    // Butonu loading state'e al
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Yeniden Oluşturuluyor...';
    
    fetch('{{ route("admin.ai.profile.generate-story") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            regenerate: true
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Başarılı - sayfayı yenile
            location.reload();
        } else {
            // Hata mesajı göster
            alert('Hata: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Hata:', error);
        alert('Hikaye yeniden oluşturulurken bir hata oluştu. Lütfen tekrar deneyin.');
    })
    .finally(() => {
        // Butonu normale döndür
        button.disabled = false;
        button.innerHTML = originalText;
    });
}

// Profil sıfırlama modal handler
$(document).on('click', '#confirmResetProfile', function() {
    const $btn = $(this);
    const originalText = $btn.html();
    
    // Loading state
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Sıfırlanıyor...');
    
    // AJAX ile profil sıfırlama
    $.ajax({
        url: '{{ route("admin.ai.profile.reset") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                // Modal'ı kapat
                $('#resetProfileModal').modal('hide');
                
                // Başarı mesajı göster
                alert('Profil başarıyla sıfırlandı! Sayfa yeniden yüklenecek.');
                
                // Sayfayı yenile
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                alert('Profil sıfırlanırken bir hata oluştu: ' + (response.message || 'Bilinmeyen hata'));
                $btn.prop('disabled', false).html(originalText);
            }
        },
        error: function(xhr, status, error) {
            console.error('Reset profile error:', error);
            alert('Profil sıfırlanırken bir hata oluştu. Lütfen tekrar deneyin.');
            $btn.prop('disabled', false).html(originalText);
        }
    });
});
</script>

{{-- Profil Sıfırlama Modal --}}
<div class="modal fade" id="resetProfileModal" tabindex="-1" aria-labelledby="resetProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resetProfileModalLabel">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                    Profili Sıfırla
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-warning me-2"></i>
                    <strong>Dikkat!</strong> Bu işlem geri alınamaz!
                </div>
                <p class="mb-3">
                    Profilinizi sıfırladığınızda:
                </p>
                <ul class="list-unstyled mb-3">
                    <li><i class="fas fa-times text-danger me-2"></i> Tüm AI profil verileri silinecek</li>
                    <li><i class="fas fa-times text-danger me-2"></i> Sektör ve marka bilgileri kaybolacak</li>
                    <li><i class="fas fa-times text-danger me-2"></i> AI davranış ayarları sıfırlanacak</li>
                    <li><i class="fas fa-times text-danger me-2"></i> Kurucu bilgileri silinecek</li>
                    <li><i class="fas fa-times text-danger me-2"></i> Marka hikayesi silinecek</li>
                </ul>
                <p class="text-muted">
                    Sıfırlama sonrası profil kurulumunu tekrar baştan yapmanız gerekecek.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>İptal
                </button>
                <button type="button" class="btn btn-danger" id="confirmResetProfile">
                    <i class="fas fa-trash me-2"></i>Evet, Profili Sıfırla
                </button>
            </div>
        </div>
    </div>
</div>

@endpush
@endsection