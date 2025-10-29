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
                            <a href="{{ route('admin.ai.profile.edit', ['step' => 1]) }}" class="btn btn-outline-light btn-lg me-3">
                                <i class="fas fa-edit me-2"></i>
                                Profili Düzenle
                            </a>
                            <a href="{{ route('admin.ai.index') }}" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-robot me-2"></i>
                                AI Asistanı Kullan
                            </a>
                        @else
                            <a href="{{ route('admin.ai.profile.edit', ['step' => 1]) }}" class="btn btn-lg" style="
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
                        
                        {{-- Profil Sıfırlama Butonu - Sadece profil doluyken görünür --}}
                        @if($profile && ($profile->is_completed || $completionPercentage > 0))
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
                                        {{-- Loading State (Hikaye oluşturuluyor) --}}
                                        <div id="brand-story-loading" class="text-center py-5" style="display: none;">
                                            <div class="mb-4">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
                                            <h5 class="text-primary mb-3">
                                                <i class="fas fa-magic me-2"></i>
                                                Marka hikayeniz oluşturuluyor...
                                            </h5>
                                            <p class="text-muted mb-4">AI asistanınız profilinize göre özel bir hikaye yazıyor. Lütfen bekleyin.</p>
                                            <div class="progress mx-auto" style="width: 300px; height: 8px;">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                                     role="progressbar" 
                                                     style="width: 100%; background: linear-gradient(90deg, #007bff, #6610f2);"></div>
                                            </div>
                                        </div>
                                        
                                        {{-- Hikaye Mevcut --}}
                                        <div id="brand-story-content" style="display: @if($profile->hasBrandStory()) block @else none @endif;">
                                            @if($profile->hasBrandStory())
                                                <div class="brand-story-content">
                                                    <div class="brand-story-text p-4" style="font-size: 1.1rem; line-height: 1.7; background-color: var(--tblr-bg-surface);">
                                                        {!! nl2br(e($profile->brand_story)) !!}
                                                    </div>
                                                    
                                                    {{-- Hikaye Bilgileri ve Aksiyonlar --}}
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <div class="brand-story-info">
                                                            @if($profile->brand_story_created_at)
                                                                <div class="text-muted d-flex align-items-center">
                                                                    <i class="fas fa-calendar-alt me-2"></i>
                                                                    <span>{{ $profile->brand_story_created_at->format('d.m.Y H:i') }} tarihinde oluşturuldu</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        
                                                        <div class="brand-story-actions">
                                                            <button type="button" class="btn btn-outline-primary btn-sm" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#regenerateStoryModal">
                                                                <i class="fas fa-sync-alt me-2"></i>
                                                                Yeniden Oluştur
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        {{-- Hikaye Yok --}}
                                        <div id="brand-story-empty" style="display: @if($profile->hasBrandStory()) none @else block @endif;">
                                            <div class="text-center py-5">
                                                <div class="mb-4">
                                                    <i class="fas fa-magic text-primary" style="font-size: 3rem; opacity: 0.5;"></i>
                                                </div>
                                                <h5 class="text-muted mb-3">Marka hikayeniz henüz oluşturulmadı</h5>
                                                <p class="text-muted mb-4">AI asistanınız profilinize göre özel bir marka hikayesi oluşturacak</p>
                                                
                                                @php
                                                    $brandName = $profile->company_info['brand_name'] ?? null;
                                                    $sector = $profile->sector_details['sector_selection'] ?? null;
                                                    $hasRequiredFields = !empty($brandName) && !empty($sector);
                                                @endphp
                                                
                                                {{-- Debug bilgisi --}}
                                                <div class="small text-muted mb-3">
                                                    Debug: Brand=<code>{{ $brandName ?? 'null' }}</code>, 
                                                    Sector=<code>{{ $sector ?? 'null' }}</code>, 
                                                    Required=<code>{{ $hasRequiredFields ? 'true' : 'false' }}</code>
                                                </div>
                                                
                                                @if($hasRequiredFields)
                                                    <button type="button" class="btn btn-primary" onclick="generateBrandStory()">
                                                        <i class="fas fa-wand-magic-sparkles me-2"></i>
                                                        Marka Hikayemi Oluştur
                                                    </button>
                                                @else
                                                    <div class="text-center">
                                                        <a href="{{ route('admin.ai.profile.edit', ['step' => 1]) }}" 
                                                           class="btn btn-primary">
                                                            <i class="fas fa-edit me-2"></i>
                                                            Profili Tamamla
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
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
                        {{-- Profil Tamamlanmamış - Basit Kurulum Rehberi --}}
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <div class="text-center mb-4">
                                    <h3 class="mb-2">AI Asistanı Kurulum Rehberi</h3>
                                    <p class="text-muted">Yapay zeka asistanınızı kişiselleştirmek için aşağıdaki adımları tamamlayın</p>
                                </div>
                                
                                <div class="row g-3">
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <span class="avatar avatar-lg bg-primary text-white">
                                                        <i class="fas fa-industry"></i>
                                                    </span>
                                                </div>
                                                <h5 class="card-title">1. Sektör Seçimi</h5>
                                                <p class="text-muted small">Yapay zeka asistanınız için en uygun sektörü seçin</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <span class="avatar avatar-lg bg-success text-white">
                                                        <i class="fas fa-building"></i>
                                                    </span>
                                                </div>
                                                <h5 class="card-title">2. Temel Bilgiler</h5>
                                                <p class="text-muted small">İşletmenizin temel bilgilerini girin</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <span class="avatar avatar-lg bg-warning text-white">
                                                        <i class="fas fa-palette"></i>
                                                    </span>
                                                </div>
                                                <h5 class="card-title">3. Marka Detayları</h5>
                                                <p class="text-muted small">Markanızın kişiliğini tanımlayın</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <span class="avatar avatar-lg bg-info text-white">
                                                        <i class="fas fa-user-tie"></i>
                                                    </span>
                                                </div>
                                                <h5 class="card-title">4. Kurucu Bilgileri</h5>
                                                <p class="text-muted small">Kurucu bilgilerini paylaşın (isteğe bağlı)</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <span class="avatar avatar-lg bg-purple text-white">
                                                        <i class="fas fa-robot"></i>
                                                    </span>
                                                </div>
                                                <h5 class="card-title">5. AI Davranış Ayarları</h5>
                                                <p class="text-muted small">AI asistanınızın iletişim tarzını ayarlayın</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card border-success">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <span class="avatar avatar-lg bg-success text-white">
                                                        <i class="fas fa-check-circle"></i>
                                                    </span>
                                                </div>
                                                <h5 class="card-title text-success">6. Hazır!</h5>
                                                <p class="text-muted small">AI asistanınız markanıza özel içerik üretecek</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-center mt-4">
                                    <div class="mb-3">
                                        <span class="text-muted">{{ round($completionPercentage) }}% Tamamlandı</span>
                                    </div>
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
<link rel="stylesheet" href="{{ asset('admin-assets/css/ai-profile-wizard.css') }}?v={{ time() }}">
<script src="{{ asset('admin-assets/libs/ai/ai-word-buffer.js') }}?v={{ time() }}"></script>
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

/* Brand Story Font Size Override */
.brand-story-text,
#brand-story-content p,
#brand-story-content .brand-story-text {
    font-size: 16px !important;
    line-height: 1.6 !important;
    font-weight: 400 !important;
    color: var(--tblr-body-color) !important;
}

.brand-story-content .card-body {
    font-size: 16px !important;
    line-height: 1.6 !important;
}

/* Dark/Light mode text color */
[data-bs-theme="dark"] .brand-story-text,
[data-bs-theme="dark"] #brand-story-content p,
[data-bs-theme="dark"] #brand-story-content .brand-story-text {
    color: var(--tblr-body-color) !important;
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
    
    // Profil tamamlandıktan sonra otomatik hikaye oluşturma
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('completed') === '1') {
        console.log('Profil tamamlandı - otomatik hikaye oluşturma başlatılıyor');
        
        // Query parametresini URL'den temizle
        const newUrl = window.location.pathname;
        window.history.replaceState({}, document.title, newUrl);
        
        // Hikaye yoksa otomatik oluştur
        const brandStoryEmpty = document.getElementById('brand-story-empty');
        if (brandStoryEmpty && brandStoryEmpty.style.display !== 'none') {
            // Hemen loading state'e geç
            brandStoryEmpty.style.display = 'none';
            document.getElementById('brand-story-loading').style.display = 'block';
            
            // Kısa bir delay ile otomatik hikaye oluştur
            setTimeout(() => {
                generateBrandStory();
            }, 500);
        }
    }
    
    // Sayfa açılışında hikaye yoksa ve gerekli bilgiler varsa otomatik oluştur
    console.log('🔍 Sayfa yüklendi - otomatik hikaye kontrolü');
    
    // Sadece hikaye boş ve gerekli bilgiler varsa çalışsın
    const brandStoryEmpty = document.getElementById('brand-story-empty');
    const brandStoryLoading = document.getElementById('brand-story-loading');
    const generateButton = document.querySelector('button[onclick="generateBrandStory()"]');
    
    console.log('🔍 Element kontrolleri:', {
        brandStoryEmpty: brandStoryEmpty ? 'bulundu' : 'bulunamadı',
        brandStoryLoading: brandStoryLoading ? 'bulundu' : 'bulunamadı',
        generateButton: generateButton ? 'bulundu' : 'bulunamadı',
        emptyVisible: brandStoryEmpty ? brandStoryEmpty.style.display !== 'none' : false,
        emptyDisplayStyle: brandStoryEmpty ? brandStoryEmpty.style.display : 'null',
        emptyComputedStyle: brandStoryEmpty ? window.getComputedStyle(brandStoryEmpty).display : 'null'
    });
    
    // Daha güvenli visibility kontrolü - element görünür mü?
    const isEmptyVisible = brandStoryEmpty && brandStoryEmpty.offsetParent !== null;
    
    console.log('🔍 Visibility kontrol:', {
        hasOffsetParent: brandStoryEmpty ? brandStoryEmpty.offsetParent !== null : false,
        isEmptyVisible: isEmptyVisible
    });
    
    if (brandStoryEmpty && isEmptyVisible && generateButton) {
        console.log('✅ Sayfa açılış - otomatik hikaye oluşturma DEVRE DIŞI (streaming test için)');
        
        // Test için otomatik oluşturmayı devre dışı bırak
        // setTimeout(() => {
        //     console.log('✅ generateBrandStory() çağrılıyor');
        //     generateBrandStory();
        // }, 500);
    } else {
        console.log('❌ Otomatik hikaye oluşturma şartları sağlanmadı');
    }
});

// 🚀 GERÇEK ZAMANLI STREAMING - Marka hikayesi oluşturma fonksiyonu
function generateBrandStory() {
    const button = event ? event.target : null;
    const originalText = button ? button.innerHTML : '';
    
    // Butonu loading state'e al (varsa)
    if (button) {
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Hikaye Oluşturuluyor...';
    }
    
    // UI state'leri değiştir
    const brandStoryEmpty = document.getElementById('brand-story-empty');
    const brandStoryLoading = document.getElementById('brand-story-loading');
    
    if (brandStoryEmpty && brandStoryEmpty.style.display !== 'none') {
        brandStoryEmpty.style.display = 'none';
        brandStoryLoading.style.display = 'block';
    }

    // ✨ SERVER-SENT EVENTS ile REAL-TIME STREAMING
    const streamUrl = '{{ route("admin.ai.profile.generate-story-stream") }}?v=' + Date.now();
    console.log('🔗 Stream URL (with cache bust):', streamUrl);
    
    const eventSource = new EventSource(streamUrl);
    
    let isStreamStarted = false;
    let storyContainer = null;
    let storyElement = null;
    let wordBuffer = null;
    
    console.log('📡 EventSource oluşturuldu, bağlantı kuruluyor...');
    console.log('🔄 CACHE BUST VERSION:', Date.now());
    
    // Test bağlantısı
    eventSource.onopen = function(event) {
        console.log('🎯 STREAMING CONNECTION AÇILDI!', event);
    };
    
    eventSource.onmessage = function(event) {
        const data = JSON.parse(event.data);
        
        console.log('📡 Stream data received:', data);
        console.log('🎯 STREAMING ENDPOINT ÇALIŞIYOR! Cache bust başarılı!');
        
        switch(data.type) {
            case 'start':
                console.log('🚀 Stream başladı:', data.message);
                // Container'ı hazırla
                prepareStreamingContainer();
                break;
                
            case 'chunk':
                console.log('📝 Chunk received:', data.content);
                // Chunk'ı word buffer'a ekle
                if (wordBuffer) {
                    wordBuffer.addContent(data.content);
                }
                break;
                
            case 'complete':
                console.log('✅ Stream tamamlandı');
                // Final flush
                if (wordBuffer) {
                    wordBuffer.flush();
                }
                eventSource.close();
                break;
                
            case 'error':
                console.error('❌ Stream hatası:', data.message);
                showStoryErrorModal(data.message);
                eventSource.close();
                // UI state'leri geri al
                document.getElementById('brand-story-loading').style.display = 'none';
                document.getElementById('brand-story-empty').style.display = 'block';
                break;
        }
    };
    
    eventSource.onerror = function(error) {
        console.error('❌ EventSource hatası:', error);
        eventSource.close();
        showStoryErrorModal('Hikaye oluşturulurken bağlantı hatası. Lütfen tekrar deneyin.');
        // UI state'leri geri al
        document.getElementById('brand-story-loading').style.display = 'none';
        document.getElementById('brand-story-empty').style.display = 'block';
    };
    
    // Container hazırlama fonksiyonu
    function prepareStreamingContainer() {
        // Loading'i gizle
        document.getElementById('brand-story-loading').style.display = 'none';
        
        // Hikaye container'ını hazırla
        storyContainer = document.getElementById('brand-story-content');
        if (!storyContainer) {
            console.error('❌ Story container bulunamadı');
            return;
        }
        
        // Container'ı görünür yap
        storyContainer.style.display = 'block';
        
        // Hikaye metnini gösterecek element'i bul
        storyElement = storyContainer.querySelector('.brand-story-text') || storyContainer.querySelector('p') || storyContainer;
        
        // Metin alanını temizle
        storyElement.innerHTML = '';
        
        // CSS sınıfını ekle (font size için)
        storyElement.classList.add('brand-story-text');
        
        // ✨ REAL-TIME Word Buffer'ı başlat
        wordBuffer = window.createAIWordBuffer(storyElement, {
            wordDelay: 60,               // Çok hızlı (gerçek zamanlı)
            minWordLength: 1,            // En az 1 karakter
            punctuationDelay: 100,       // Noktalama sonrası 100ms ek
            enableMarkdown: true,        // Markdown desteği
            scrollCallback: () => {
                // Scroll to bottom if needed
                storyContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });
        
        // Word Buffer'ı başlat
        wordBuffer.start();
        
        console.log('✅ Streaming container hazırlandı');
    }
    
    // Butonu normale döndür (stream bittiğinde)
    setTimeout(() => {
        if (button) {
            button.disabled = false;
            button.innerHTML = originalText;
        }
    }, 1000); // 1 saniye delay
}

// ✨ Word Buffer ile hikaye gösterimi
function showStoryWithWordBuffer(storyText) {
    console.log('🎬 Word Buffer ile hikaye gösterimi başlatılıyor');
    
    // Loading'i gizle
    document.getElementById('brand-story-loading').style.display = 'none';
    
    // Hikaye container'ını hazırla
    const storyContainer = document.getElementById('brand-story-content');
    if (!storyContainer) {
        console.error('❌ Story container bulunamadı');
        location.reload();
        return;
    }
    
    // Container'ı görünür yap
    storyContainer.style.display = 'block';
    
    // Hikaye metnini gösterecek element'i bul
    const storyElement = storyContainer.querySelector('.brand-story-text') || storyContainer.querySelector('p') || storyContainer;
    
    // Metin alanını temizle
    storyElement.innerHTML = '';
    
    // CSS sınıfını ekle (font size için)
    storyElement.classList.add('brand-story-text');
    
    // Word Buffer'ı başlat
    const wordBuffer = window.createAIWordBuffer(storyElement, {
        wordDelay: 120,              // Kelime başına 120ms
        minWordLength: 1,            // En az 1 karakter
        punctuationDelay: 200,       // Noktalama sonrası 200ms ek
        enableMarkdown: true,        // Markdown desteği
        scrollCallback: () => {
            // Scroll to bottom if needed
            storyContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    });
    
    // Word Buffer'ı başlat
    wordBuffer.start();
    
    // Hikaye metnini buffer'a ekle
    wordBuffer.addContent(storyText);
    
    // 5 saniye sonra flush (güvenlik için)
    setTimeout(() => {
        wordBuffer.flush();
    }, 5000);
    
    console.log('✅ Word Buffer hikaye gösterimi başlatıldı');
}

// Marka hikayesi yeniden oluşturma fonksiyonu
function regenerateBrandStory() {
    const $btn = $('#confirmRegenerateStory');
    const originalText = $btn.html();
    
    // Butonu loading state'e al
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Yeniden Oluşturuluyor...');
    
    // Modal'ı kapat
    $('#regenerateStoryModal').modal('hide');
    
    // UI state'leri değiştir
    document.getElementById('brand-story-content').style.display = 'none';
    document.getElementById('brand-story-loading').style.display = 'block';
    
    // ✨ STREAMING kullanarak yeniden oluştur
    console.log('🔄 Regeneration -> STREAMING ENDPOINT kullanılıyor');
    
    // Streaming endpoint'i çağır (regenerate parametresi ile)
    const streamUrl = '{{ route("admin.ai.profile.generate-story-stream") }}?regenerate=true&v=' + Date.now();
    console.log('🔗 Regeneration Stream URL:', streamUrl);
    
    const eventSource = new EventSource(streamUrl);
    
    let wordBuffer = null;
    
    eventSource.onmessage = function(event) {
        const data = JSON.parse(event.data);
        
        console.log('📡 Regeneration stream data:', data);
        
        switch(data.type) {
            case 'start':
                console.log('🚀 Regeneration stream başladı');
                prepareStreamingContainer();
                break;
                
            case 'chunk':
                console.log('📝 Regeneration chunk:', data.content);
                if (wordBuffer) {
                    wordBuffer.addContent(data.content);
                }
                break;
                
            case 'complete':
                console.log('✅ Regeneration stream tamamlandı');
                if (wordBuffer) {
                    wordBuffer.flush();
                }
                // Mevcut hikaye varsa Word Buffer ile göster
                if (data.story) {
                    showStoryWithWordBuffer(data.story);
                }
                eventSource.close();
                break;
                
            case 'error':
                console.error('❌ Regeneration stream hatası:', data.message);
                showStoryErrorModal(data.message);
                eventSource.close();
                break;
        }
    };
    
    eventSource.onerror = function(error) {
        console.error('❌ Regeneration EventSource hatası:', error);
        eventSource.close();
        showStoryErrorModal('Hikaye yeniden oluşturulurken bağlantı hatası.');
    };
    
    // Butonu normale döndür
    $btn.prop('disabled', false).html(originalText);
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
                
                // Başarı mesajını modal body'de göster
                showResetSuccessMessage(response.message);
                
                // Sayfayı yenile
                setTimeout(function() {
                    location.reload();
                }, 2000);
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

// Hikaye yeniden oluşturma modal handler
$(document).on('click', '#confirmRegenerateStory', function() {
    regenerateBrandStory();
});

// Hikaye hata modal'ını göster fonksiyonu
function showStoryErrorModal(message) {
    // Modal içeriğini güncelle
    const messageEl = document.getElementById('storyErrorMessage');
    if (messageEl) {
        messageEl.textContent = message;
    }

    // Modal'ı göster - Bootstrap 5 native API kullan
    const modalEl = document.getElementById('storyErrorModal');
    if (modalEl) {
        // Bootstrap Modal varsa kullan
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
        // Yoksa Tabler.io modal kullan
        else if (typeof tata !== 'undefined') {
            tata.error('Hata', message);
        }
        // Hiçbiri yoksa alert
        else {
            alert(message);
        }
    } else {
        // Modal elementi yoksa alert göster
        console.error('❌ Modal element bulunamadı:', message);
        alert(message);
    }
}

// Başarı mesajı göster fonksiyonu
function showResetSuccessMessage(message) {
    // Mevcut marka hikayesi alanını temizle
    document.getElementById('brand-story-content').style.display = 'none';
    document.getElementById('brand-story-loading').style.display = 'none';
    
    // Başarı mesajını göster
    const successDiv = document.createElement('div');
    successDiv.innerHTML = `
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
            </div>
            <h5 class="text-success mb-3">${message}</h5>
            
            <div class="alert alert-info mb-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle me-3" style="font-size: 1.2rem;"></i>
                    <div class="text-start">
                        <h6 class="mb-1">Marka Hikayesi Oluşturmak İçin:</h6>
                        <p class="mb-0 small">AI asistanınızın size özel hikaye yazabilmesi için profil bilgilerinizi tamamlamanız gerekiyor.</p>
                    </div>
                </div>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body text-center p-3">
                            <i class="fas fa-industry mb-2" style="font-size: 2rem;"></i>
                            <h6 class="mb-1">Sektör Bilgisi</h6>
                            <small class="text-muted">Hangi sektörde çalışıyorsunuz?</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body text-center p-3">
                            <i class="fas fa-building mb-2" style="font-size: 2rem;"></i>
                            <h6 class="mb-1">Şirket Bilgileri</h6>
                            <small class="text-muted">Markanızın temel özellikleri</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-4">
                <a href="{{ route('admin.ai.profile.edit', ['step' => 1]) }}" class="btn btn-primary btn-lg me-2">
                    <i class="fas fa-rocket me-2"></i>
                    Profili Oluşturmaya Başla
                </a>
            </div>
            
            <div class="text-muted mb-3">
                <small>Profil tamamlandıktan sonra AI asistanınız size özel marka hikayesi yazacak</small>
            </div>
            
            <div class="spinner-border text-success" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    
    // Marka hikayesi card body'sini değiştir
    const cardBody = document.querySelector('.card-body');
    if (cardBody) {
        const brandStorySection = cardBody.querySelector('.row.g-4.mb-4');
        if (brandStorySection) {
            const brandStoryCardBody = brandStorySection.querySelector('.card-body');
            if (brandStoryCardBody) {
                brandStoryCardBody.innerHTML = successDiv.innerHTML;
            }
        }
    }
}
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

{{-- Hikaye Hata Modal --}}
<div class="modal fade" id="storyErrorModal" tabindex="-1" aria-labelledby="storyErrorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="storyErrorModalLabel">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                    Hikaye Oluşturma Hatası
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-times-circle me-2"></i>
                    <strong>Hata:</strong> Hikaye oluşturma işlemi başarısız oldu.
                </div>
                <p class="mb-3">
                    <i class="fas fa-info-circle text-muted me-2"></i>
                    <span id="storyErrorMessage">Bilinmeyen bir hata oluştu.</span>
                </p>
                <div class="alert alert-info" role="alert">
                    <i class="fas fa-lightbulb me-2"></i>
                    <strong>Çözüm önerileri:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Profil bilgilerinizi kontrol edin (sektör ve marka adı dolu olmalı)</li>
                        <li>İnternet bağlantınızı kontrol edin</li>
                        <li>Birkaç dakika sonra tekrar deneyin</li>
                        <li>Sorun devam ederse destek ekibine başvurun</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Kapat
                </button>
                <a href="{{ route('admin.ai.profile.edit', ['step' => 1]) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>Profili Düzenle
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Hikaye Yeniden Oluşturma Modal --}}
<div class="modal fade" id="regenerateStoryModal" tabindex="-1" aria-labelledby="regenerateStoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="regenerateStoryModalLabel">
                    <i class="fas fa-sync-alt text-primary me-2"></i>
                    Hikayeyi Yeniden Oluştur
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Bilgi:</strong> Mevcut hikayeniz yenisiyle değiştirilecek!
                </div>
                <p class="mb-3">
                    Hikayenizi yeniden oluşturduğunuzda:
                </p>
                <ul class="list-unstyled mb-3">
                    <li><i class="fas fa-trash text-warning me-2"></i> Mevcut hikayeniz silinecek</li>
                    <li><i class="fas fa-magic text-primary me-2"></i> AI güncel profil bilgilerinize göre yeni bir hikaye yazacak</li>
                    <li><i class="fas fa-clock text-muted me-2"></i> İşlem 30-60 saniye sürebilir</li>
                    <li><i class="fas fa-check text-success me-2"></i> Yeni hikaye otomatik olarak kaydedilecek</li>
                </ul>
                <p class="text-muted">
                    Devam etmek istiyor musunuz?
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>İptal
                </button>
                <button type="button" class="btn btn-primary" id="confirmRegenerateStory">
                    <i class="fas fa-wand-magic-sparkles me-2"></i>Evet, Yeniden Oluştur
                </button>
            </div>
        </div>
    </div>
</div>

@endpush
@endsection