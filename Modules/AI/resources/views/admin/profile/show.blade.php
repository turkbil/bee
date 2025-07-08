@extends('admin.layout')

{{-- Override page-body to remove container and make full-width --}}
@section('content')
</div>
</div>

{{-- Futuristic Hero Section - FULL WIDTH --}}
<div class="ai-profile-hero" style="
    background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 25%, #16213e 50%, #0f3460 75%, #533483 100%);
    position: relative;
    overflow: hidden;
    min-height: 400px;
    margin-top: -1.5rem;
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
        
        {{-- Content in normal container --}}
        <div class="container-xl position-relative" style="z-index: 10; padding-top: 3rem; padding-bottom: 3rem;">
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
                            font-size: 2.5rem;
                            font-weight: 700;
                            background: linear-gradient(45deg, #00d4ff, #ffffff, #9333ea);
                            -webkit-background-clip: text;
                            -webkit-text-fill-color: transparent;
                            background-clip: text;
                            margin-bottom: 0.5rem;
                            animation: title-shimmer 3s ease-in-out infinite;
                            line-height: 1.2;
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
                    @else
                        <a href="{{ route('admin.ai.profile.edit') }}" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-edit me-2"></i>
                            Profili Düzenle
                        </a>
                    @endif
                </div>
            </div>
        </div>
        </div>
    </div>
</div>

{{-- Content --}}
@if($profile && $profile->is_completed)
    {{-- Profile Completed - Include Partials --}}
    <div class="container-xl">
        <div class="row g-4 mt-4">
            @include('ai::admin.profile.partials.completed-profile')
        </div>
    </div>
@endif

<div class="container">
    {{-- Dummy div to close admin layout containers --}}

{{-- Hero Animations --}}
<style>
@keyframes float-bg {
    0%, 100% { 
        background-position: 0% 50%; 
    }
    50% { 
        background-position: 100% 50%; 
    }
}

@keyframes grid-move {
    0% { 
        transform: translate(0, 0); 
    }
    100% { 
        transform: translate(50px, 50px); 
    }
}

@keyframes pulse-glow {
    0%, 100% { 
        box-shadow: 0 20px 40px rgba(0, 212, 255, 0.3); 
    }
    50% { 
        box-shadow: 0 25px 50px rgba(0, 212, 255, 0.5); 
    }
}

@keyframes title-shimmer {
    0%, 100% { 
        background-position: 0% 50%; 
    }
    50% { 
        background-position: 100% 50%; 
    }
}

{{-- Disable card transitions --}}
.card {
    transition: none !important;
}
.card:hover {
    transform: none !important;
    box-shadow: none !important;
}
</style>
@endsection