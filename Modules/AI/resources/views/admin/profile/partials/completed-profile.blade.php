{{-- Profile Completed - Modern Cards --}}
<div class="col-12">
    <div class="card" style="
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(34, 197, 94, 0.05));
        border: 2px solid rgba(16, 185, 129, 0.2);
        border-radius: 20px;
        margin-bottom: 2rem;
    ">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="me-3" style="
                        width: 60px;
                        height: 60px;
                        background: linear-gradient(135deg, #10b981, #059669);
                        border-radius: 15px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
                    ">
                        <i class="fas fa-check" style="font-size: 1.5rem; color: white;"></i>
                    </div>
                    <div>
                        <h3 class="mb-1" style="color: #10b981; font-weight: 700;">AI Profiliniz Aktif!</h3>
                        <p class="text-muted mb-0">Yapay zeka asistanınız markanıza özel içerik üretmeye hazır</p>
                    </div>
                </div>
                <div class="text-end">
                    <div class="btn-list">
                        <a href="{{ route('admin.ai.profile.edit') }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>
                            Düzenle
                        </a>
                        <form action="{{ route('admin.ai.profile.reset') }}" method="POST" class="d-inline" 
                              onsubmit="return confirm('⚠️ UYARI: Profili sıfırlamak istediğinize emin misiniz?\n\nBu işlem:\n• Tüm profil bilgilerini silecek\n• Database kaydını tamamen kaldıracak\n• Geri alınamaz bir işlemdir\n\nDevam etmek istiyor musunuz?')">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="fas fa-trash-restore me-2"></i>
                                Sıfırla
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Brand Story Section - MOVED TO TOP --}}
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card" style="
            border-radius: 20px; 
            border: 2px solid rgba(147, 51, 234, 0.3);
            background: linear-gradient(135deg, rgba(147, 51, 234, 0.1), rgba(147, 51, 234, 0.05));
            box-shadow: 0 10px 30px rgba(147, 51, 234, 0.1);
        ">
            <div class="card-header" style="
                background: linear-gradient(135deg, rgba(147, 51, 234, 0.15), rgba(147, 51, 234, 0.1)); 
                border-radius: 18px 18px 0 0;
                border-bottom: 2px solid rgba(147, 51, 234, 0.2);
                padding: 1.5rem;
            ">
                <h3 class="card-title d-flex align-items-center mb-0" style="font-size: 1.5rem; font-weight: 700;">
                    <div class="me-3" style="
                        width: 50px;
                        height: 50px;
                        background: linear-gradient(135deg, #9333ea, #7c3aed);
                        border-radius: 15px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        box-shadow: 0 8px 16px rgba(147, 51, 234, 0.3);
                    ">
                        <i class="fas fa-book-open" style="color: white; font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <div style="color: #9333ea;">Marka Hikayeniz</div>
                        <small class="text-muted fw-normal">AI tarafından özel olarak hazırlandı</small>
                    </div>
                </h3>
            </div>
            <div class="card-body" style="padding: 2rem;">
                @if($profile->hasBrandStory())
                    {{-- Hikaye mevcut --}}
                    <div class="brand-story-content">
                        <div class="brand-story-text" style="
                            font-size: 1.15rem;
                            line-height: 1.8;
                            color: #374151;
                            text-align: justify;
                            padding: 2rem;
                            background: linear-gradient(135deg, rgba(255, 255, 255, 0.8), rgba(147, 51, 234, 0.05));
                            border-radius: 15px;
                            border: 1px solid rgba(147, 51, 234, 0.1);
                            box-shadow: 0 5px 15px rgba(147, 51, 234, 0.08);
                        ">
                            {!! nl2br(e($profile->brand_story)) !!}
                        </div>
                        
                        @if($profile->brand_story_created_at)
                            <div class="mt-3 text-muted d-flex align-items-center">
                                <i class="fas fa-clock me-2"></i>
                                <span>{{ $profile->brand_story_created_at->format('d.m.Y H:i') }} tarihinde oluşturuldu</span>
                            </div>
                        @endif
                        
                        <div class="mt-4 d-flex gap-2 flex-wrap">
                            <button class="btn btn-primary" onclick="regenerateBrandStory()" style="
                                background: linear-gradient(135deg, #9333ea, #7c3aed);
                                border: none;
                                border-radius: 10px;
                                padding: 0.75rem 1.5rem;
                                font-weight: 600;
                                box-shadow: 0 4px 12px rgba(147, 51, 234, 0.3);
                            ">
                                <i class="fas fa-sync-alt me-2"></i>
                                Hikayeyi Yeniden Oluştur
                            </button>
                            <button class="btn btn-outline-secondary" onclick="copyBrandStory()" style="
                                border-radius: 10px;
                                padding: 0.75rem 1.5rem;
                                font-weight: 600;
                            ">
                                <i class="fas fa-copy me-2"></i>
                                Kopyala
                            </button>
                        </div>
                    </div>
                @elseif(isset($brandStoryGenerating) && $brandStoryGenerating)
                    {{-- Modern Loading State --}}
                    <div class="text-center py-5" id="brand-story-loading">
                        <div style="
                            width: 120px;
                            height: 120px;
                            margin: 0 auto 2rem;
                            position: relative;
                        ">
                            {{-- Digital Loading Animation --}}
                            <div style="
                                width: 120px;
                                height: 120px;
                                border: 4px solid rgba(147, 51, 234, 0.1);
                                border-radius: 50%;
                                position: relative;
                                animation: pulse-ring 2s ease-in-out infinite;
                            ">
                                <div style="
                                    width: 100px;
                                    height: 100px;
                                    border: 3px solid transparent;
                                    border-top: 3px solid #9333ea;
                                    border-radius: 50%;
                                    position: absolute;
                                    top: 50%;
                                    left: 50%;
                                    transform: translate(-50%, -50%);
                                    animation: spin 1s linear infinite;
                                "></div>
                                <div style="
                                    position: absolute;
                                    top: 50%;
                                    left: 50%;
                                    transform: translate(-50%, -50%);
                                    background: linear-gradient(135deg, #9333ea, #7c3aed);
                                    width: 40px;
                                    height: 40px;
                                    border-radius: 50%;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                ">
                                    <i class="fas fa-magic" style="color: white; font-size: 1.2rem;"></i>
                                </div>
                            </div>
                        </div>
                        
                        <h4 style="
                            background: linear-gradient(45deg, #9333ea, #7c3aed);
                            -webkit-background-clip: text;
                            -webkit-text-fill-color: transparent;
                            background-clip: text;
                            font-weight: 700;
                            margin-bottom: 1rem;
                        ">Hikayeniz Oluşturuluyor, Lütfen Bekleyiniz...</h4>
                        
                        <div class="mb-4">
                            <p class="text-muted mb-2">Yapay zeka asistanınız profil bilgilerinize göre özel bir hikaye hazırlıyor.</p>
                            <div style="
                                background: rgba(147, 51, 234, 0.1);
                                border: 1px solid rgba(147, 51, 234, 0.2);
                                border-radius: 10px;
                                padding: 1rem;
                                margin: 1rem 0;
                            ">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-brain me-2" style="color: #9333ea;"></i>
                                    <small class="text-muted">AI, markanızın değerlerini ve kimliğini analiz ediyor...</small>
                                </div>
                            </div>
                        </div>
                        
                        <button class="btn btn-outline-primary" onclick="location.reload()" style="
                            border-radius: 10px;
                            padding: 0.75rem 2rem;
                            font-weight: 600;
                            border-color: #9333ea;
                            color: #9333ea;
                        ">
                            <i class="fas fa-refresh me-2"></i>
                            Sayfayı Yenile
                        </button>
                    </div>
                @else
                    {{-- Hikaye yok - Auto Loading --}}
                    <div class="text-center py-5" id="brand-story-auto-loading">
                        <div style="
                            width: 100px;
                            height: 100px;
                            margin: 0 auto 2rem;
                            position: relative;
                        ">
                            <div style="
                                width: 100px;
                                height: 100px;
                                border: 3px solid rgba(147, 51, 234, 0.2);
                                border-radius: 50%;
                                position: relative;
                            ">
                                <div style="
                                    width: 80px;
                                    height: 80px;
                                    border: 2px solid transparent;
                                    border-top: 2px solid #9333ea;
                                    border-radius: 50%;
                                    position: absolute;
                                    top: 50%;
                                    left: 50%;
                                    transform: translate(-50%, -50%);
                                    animation: spin 1.5s linear infinite;
                                "></div>
                                <div style="
                                    position: absolute;
                                    top: 50%;
                                    left: 50%;
                                    transform: translate(-50%, -50%);
                                    background: #9333ea;
                                    width: 30px;
                                    height: 30px;
                                    border-radius: 50%;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                ">
                                    <i class="fas fa-book-open" style="color: white; font-size: 0.9rem;"></i>
                                </div>
                            </div>
                        </div>
                        
                        <h5 class="mb-2" style="color: #9333ea; font-weight: 600;">Marka Hikayeniz Hazırlanıyor</h5>
                        <p class="text-muted mb-3">Profil bilgilerinize göre otomatik olarak bir marka hikayesi oluşturuluyor...</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Profile Summary Cards --}}
<div class="row g-4">
    {{-- Company Info --}}
    <div class="col-lg-6">
        <div class="card h-100" style="border-radius: 15px; border: 1px solid rgba(0, 212, 255, 0.2);">
            <div class="card-header" style="background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(0, 212, 255, 0.05)); border-radius: 15px 15px 0 0;">
                <h3 class="card-title d-flex align-items-center">
                    <i class="fas fa-building me-2" style="color: #00d4ff;"></i>
                    Firma Bilgileri
                </h3>
            </div>
            <div class="card-body">
                @php
                    $hasCompanyInfo = !empty($profile->company_info['brand_name']) || 
                                      !empty($profile->company_info['city']) || 
                                      !empty($profile->company_info['main_service']) ||
                                      !empty($profile->company_info['contact_info']);
                @endphp
                
                @if($hasCompanyInfo)
                    @if(isset($profile->company_info['brand_name']))
                        <div class="mb-3">
                            <strong class="text-muted">Marka Adı:</strong>
                            <div class="fs-5">{{ $profile->company_info['brand_name'] }}</div>
                        </div>
                    @endif
                    
                    @if(isset($profile->company_info['city']))
                        <div class="mb-3">
                            <strong class="text-muted">Şehir:</strong>
                            <div>{{ $profile->company_info['city'] }}</div>
                        </div>
                    @endif
                    
                    @if(isset($profile->company_info['main_service']))
                        <div class="mb-3">
                            <strong class="text-muted">Ana Hizmet:</strong>
                            <div>{{ $profile->company_info['main_service'] }}</div>
                        </div>
                    @endif
                    
                    @if(isset($profile->company_info['contact_info']) && !empty($profile->company_info['contact_info']))
                        <div class="mb-3">
                            <strong class="text-muted">İletişim Kanalları:</strong>
                            <div class="mt-2">
                                @foreach($profile->company_info['contact_info'] as $channel => $value)
                                    @if($value && $value !== false)
                                        <span class="badge bg-success me-1 mb-1">
                                            <i class="fas fa-check me-1"></i>
                                            {{ ucfirst(str_replace('_', ' ', $channel)) }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-building mb-3" style="font-size: 2rem; color: #e9ecef;"></i>
                        <p class="mb-2">Firma bilgileri henüz tamamlanmamış</p>
                        <small>Profil düzenleme sayfasından bilgilerinizi ekleyebilirsiniz</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    {{-- Sector Info --}}
    <div class="col-lg-6">
        <div class="card h-100" style="border-radius: 15px; border: 1px solid rgba(147, 51, 234, 0.2);">
            <div class="card-header" style="background: linear-gradient(135deg, rgba(147, 51, 234, 0.1), rgba(147, 51, 234, 0.05)); border-radius: 15px 15px 0 0;">
                <h3 class="card-title d-flex align-items-center">
                    @if($sector)
                        <i class="{{ $sector->icon }} me-2" style="color: #9333ea;"></i>
                    @else
                        <i class="fas fa-industry me-2" style="color: #9333ea;"></i>
                    @endif
                    Sektör Bilgileri
                </h3>
            </div>
            <div class="card-body">
                @php
                    $hasSectorDetails = $sector || ($profile->sector_details && count(array_filter($profile->sector_details)) > 1);
                @endphp
                
                @if($hasSectorDetails)
                    @if($sector)
                        <div class="mb-3">
                            <div class="fs-5 fw-bold" style="color: #9333ea;">{{ $sector->name }}</div>
                            <div class="text-muted">{{ $sector->description }}</div>
                        </div>
                    @endif
                    
                    @if($profile->sector_details)
                        @foreach($profile->sector_details as $key => $value)
                            @if($key !== 'sector' && !empty($value) && $value !== false)
                                <div class="mb-2">
                                    <strong class="text-muted">{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                    @if(is_array($value))
                                        <div class="mt-1">
                                            @foreach($value as $subkey => $subvalue)
                                                @if($subvalue && $subvalue !== false)
                                                    <span class="badge bg-purple me-1 mb-1">
                                                        @if(is_string($subkey))
                                                            {{ ucfirst(str_replace('-', ' ', $subkey)) }}
                                                        @else
                                                            {{ $subvalue }}
                                                        @endif
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <div>{{ is_string($value) ? ucfirst(str_replace('_', ' ', $value)) : $value }}</div>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    @endif
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-industry mb-3" style="font-size: 2rem; color: #e9ecef;"></i>
                        <p class="mb-2">Sektör bilgileri henüz seçilmemiş</p>
                        <small>Sektörünüzü seçerek detaylı bilgiler ekleyebilirsiniz</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    {{-- AI Behavior Rules --}}
    <div class="col-12">
        <div class="card" style="border-radius: 15px; border: 1px solid rgba(245, 158, 11, 0.2);">
            <div class="card-header" style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(245, 158, 11, 0.05)); border-radius: 15px 15px 0 0;">
                <h3 class="card-title d-flex align-items-center">
                    <i class="fas fa-brain me-2" style="color: #f59e0b;"></i>
                    Yapay Zeka Davranış Kuralları
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    @if(isset($profile->ai_behavior_rules['writing_tone']))
                        <div class="col-md-3 mb-3">
                            <strong class="text-muted">Yazı Tonu:</strong>
                            <div class="mt-1">
                                @if(is_array($profile->ai_behavior_rules['writing_tone']))
                                    @foreach($profile->ai_behavior_rules['writing_tone'] as $tone_key => $tone_value)
                                        @if($tone_value && $tone_value !== false)
                                            <span class="badge bg-primary me-1 mb-1">{{ ucfirst(str_replace('_', ' ', $tone_key)) }}</span>
                                        @endif
                                    @endforeach
                                @else
                                    <span class="badge bg-primary fs-6">
                                        {{ is_string($profile->ai_behavior_rules['writing_tone']) ? ucfirst($profile->ai_behavior_rules['writing_tone']) : $profile->ai_behavior_rules['writing_tone'] }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif
                    
                    @if(isset($profile->ai_behavior_rules['emphasis_points']) && is_array($profile->ai_behavior_rules['emphasis_points']))
                        <div class="col-md-3 mb-3">
                            <strong class="text-muted">Vurgu Noktaları:</strong>
                            <div class="mt-1">
                                @foreach($profile->ai_behavior_rules['emphasis_points'] as $key => $value)
                                    @if($value)
                                        <span class="badge bg-success me-1 mb-1">{{ ucfirst(str_replace(['-', '_'], ' ', $key)) }}</span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    @if(isset($profile->ai_behavior_rules['avoid_topics']) && is_array($profile->ai_behavior_rules['avoid_topics']))
                        <div class="col-md-3 mb-3">
                            <strong class="text-muted">Kaçınılacak Konular:</strong>
                            <div class="mt-1">
                                @foreach($profile->ai_behavior_rules['avoid_topics'] as $key => $value)
                                    @if($value)
                                        <span class="badge bg-danger me-1 mb-1">{{ ucfirst(str_replace(['-', '_'], ' ', $key)) }}</span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    @if(isset($profile->ai_behavior_rules['special_terminology']))
                        <div class="col-md-3 mb-3">
                            <strong class="text-muted">Özel Terminoloji:</strong>
                            <div class="mt-1 text-muted">{{ $profile->ai_behavior_rules['special_terminology'] }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    {{-- Success Stories --}}
    @if($profile->success_stories && !empty(array_filter($profile->success_stories)))
        <div class="col-12">
            <div class="card" style="border-radius: 15px; border: 1px solid rgba(16, 185, 129, 0.2);">
                <div class="card-header" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.05)); border-radius: 15px 15px 0 0;">
                    <h3 class="card-title d-flex align-items-center">
                        <i class="fas fa-trophy me-2" style="color: #10b981;"></i>
                        Başarı Hikayeleri
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if(isset($profile->success_stories['major_projects']))
                            <div class="col-md-6 mb-3">
                                <strong class="text-muted">Önemli Projeler:</strong>
                                <div class="mt-1">{{ $profile->success_stories['major_projects'] }}</div>
                            </div>
                        @endif
                        
                        @if(isset($profile->success_stories['client_references']))
                            <div class="col-md-6 mb-3">
                                <strong class="text-muted">Müşteri Referansları:</strong>
                                <div class="mt-1">{{ $profile->success_stories['client_references'] }}</div>
                            </div>
                        @endif
                        
                        @if(isset($profile->success_stories['success_metrics']))
                            <div class="col-md-6 mb-3">
                                <strong class="text-muted">Başarı Metrikleri:</strong>
                                <div class="mt-1">{{ $profile->success_stories['success_metrics'] }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    {{-- Founder Info --}}
    @if($profile->founder_info && !empty(array_filter($profile->founder_info)))
        <div class="col-12">
            <div class="card" style="border-radius: 15px; border: 1px solid rgba(99, 102, 241, 0.2);">
                <div class="card-header" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(99, 102, 241, 0.05)); border-radius: 15px 15px 0 0;">
                    <h3 class="card-title d-flex align-items-center">
                        <i class="fas fa-user-star me-2" style="color: #6366f1;"></i>
                        Kurucu Bilgileri
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($profile->founder_info as $key => $value)
                            @if(!empty($value) && $value !== false)
                                <div class="col-md-6 mb-3">
                                    <strong class="text-muted">{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                    <div class="mt-1">
                                        @if(is_array($value))
                                            @foreach($value as $subkey => $subvalue)
                                                @if($subvalue && $subvalue !== false)
                                                    <span class="badge bg-indigo me-1 mb-1">
                                                        {{ is_string($subkey) ? ucfirst(str_replace('-', ' ', $subkey)) : $subvalue }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        @else
                                            {{ $value }}
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    {{-- Profile Stats --}}
    <div class="col-12">
        <div class="card" style="border-radius: 15px; background: linear-gradient(135deg, rgba(0, 0, 0, 0.05), rgba(0, 0, 0, 0.02));">
            <div class="card-body text-center py-4">
                <div class="text-muted">
                    <i class="fas fa-clock me-2"></i>
                    Son güncelleme: {{ $profile->updated_at->format('d.m.Y H:i') }}
                    <span class="mx-3">•</span>
                    <i class="fas fa-check-circle me-2 text-success"></i>
                    Profil durumu: Aktif
                    <span class="mx-3">•</span>
                    <i class="fas fa-shield-alt me-2 text-primary"></i>
                    AI asistanınız hazır
                </div>
            </div>
        </div>
    </div>
    
</div>

<style>
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@keyframes pulse-ring {
    0%, 100% { 
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(147, 51, 234, 0.4);
    }
    50% { 
        transform: scale(1.05);
        box-shadow: 0 0 0 20px rgba(147, 51, 234, 0.1);
    }
}

.brand-story-text {
    transition: all 0.3s ease;
}

.brand-story-text:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(147, 51, 234, 0.15) !important;
}

/* Gradient Text Animation */
@keyframes gradient-shift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

/* Digital Grid Effect */
@keyframes float-digital {
    0%, 100% { 
        transform: translate(0, 0) scale(1);
        opacity: 0.8;
    }
    50% { 
        transform: translate(5px, -5px) scale(1.02);
        opacity: 1;
    }
}

/* Card Hover Effects */
.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
}

/* Button Pulse Effect */
@keyframes btn-pulse {
    0%, 100% { 
        transform: scale(1);
        box-shadow: 0 4px 12px rgba(147, 51, 234, 0.3);
    }
    50% { 
        transform: scale(1.02);
        box-shadow: 0 6px 18px rgba(147, 51, 234, 0.4);
    }
}

.btn-primary {
    animation: btn-pulse 3s ease-in-out infinite;
}

/* Loading States */
#brand-story-loading, #brand-story-auto-loading {
    animation: float-digital 4s ease-in-out infinite;
}

/* Text Shimmer Effect */
.text-shimmer {
    background: linear-gradient(45deg, #9333ea, #7c3aed, #9333ea);
    background-size: 200% 200%;
    animation: gradient-shift 3s ease-in-out infinite;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
</style>

<script>
function regenerateBrandStory() {
    if (confirm('Mevcut hikayeniz silinip yeni bir hikaye oluşturulacak. Devam etmek istiyor musunuz?')) {
        // AJAX request to regenerate brand story
        fetch('{{ route("admin.ai.profile.generate-story") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Hikaye oluşturulurken hata: ' + (data.message || 'Bilinmeyen hata'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Hikaye oluşturulurken hata oluştu');
        });
    }
}

function copyBrandStory() {
    const storyText = document.querySelector('.brand-story-text').innerText;
    navigator.clipboard.writeText(storyText).then(() => {
        // Toast veya alert göster
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check me-2"></i>Kopyalandı!';
        btn.classList.add('btn-success');
        btn.classList.remove('btn-outline-secondary');
        
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-secondary');
        }, 2000);
    }).catch(err => {
        alert('Kopyalama başarısız');
    });
}
</script>