{{-- Status Card - Simple --}}
<div class="col-12">
    <div class="card mb-4">
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

{{-- Brand Story Section --}}
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
                            <i class="fas fa-clock me-2"></i>
                            <span>{{ $profile->brand_story_created_at->format('d.m.Y H:i') }} tarihinde oluşturuldu</span>
                        </div>
                    @endif
                        
                    <div class="mt-4 d-flex gap-2 flex-wrap">
                        <button class="btn btn-secondary" onclick="regenerateBrandStory()">
                            <i class="fas fa-sync-alt me-2"></i>
                            Hikayeyi Yeniden Oluştur
                        </button>
                        <button class="btn btn-outline-secondary" onclick="copyBrandStory()">
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
                                    top: 6px;
                                    left: 6px;
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
                        
                        <button class="btn btn-outline-muted" onclick="location.reload()" style="
                            border-radius: 10px;
                            padding: 0.75rem 2rem;
                            font-weight: 600;
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
                                    top: 6px;
                                    left: 6px;
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
<div class="row g-4 mt-4">
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
                                        <span class="badge bg-muted me-1 mb-1" style="border-radius: 0.25rem !important; color: var(--tblr-body-color) !important; background-color: var(--tblr-bg-surface) !important; border: 1px solid var(--tblr-border-color) !important;">
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
                        @php
                            // Türkçe alan adları mapping
                            $fieldLabels = [
                                'sector_name' => 'Sektör Adı',
                                'sector_selection' => 'Sektör Seçimi', 
                                'target_customers' => 'Hedef Müşteri Kitlesi',
                                'brand_personality' => 'Marka Kişiliği',
                                'sector_description' => 'Sektör Açıklaması',
                                'main_business_activities' => 'Ana İş Kolları',
                                'main_business_activities_question' => 'Ana İş Kolları Sorusu'
                            ];
                            
                            // Değer çevirileri
                            $valueTranslations = [
                                'bireysel_musteriler' => 'Bireysel Müşteriler',
                                'kucuk_isletmeler' => 'Küçük İşletmeler', 
                                'buyuk_sirketler' => 'Büyük Şirketler',
                                'hizli_dinamik' => 'Hızlı ve Dinamik',
                                'uzman_guvenilir' => 'Uzman ve Güvenilir',
                                'yenilikci_modern' => 'Yenilikçi ve Modern',
                                'profesyonel_ciddi' => 'Profesyonel ve Ciddi',
                                'web' => 'Web Teknolojileri'
                            ];
                        @endphp
                        
                        @foreach($profile->sector_details as $key => $value)
                            @if($key !== 'sector' && !empty($value) && $value !== false)
                                <div class="mb-2">
                                    <strong class="text-muted">{{ $fieldLabels[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                    @if(is_array($value))
                                        <div class="mt-1">
                                            @foreach($value as $subkey => $subvalue)
                                                @if($subvalue && $subvalue !== false)
                                                    <span class="badge bg-muted me-1 mb-1" style="border-radius: 0.25rem !important; color: var(--tblr-body-color) !important; background-color: var(--tblr-bg-surface) !important; border: 1px solid var(--tblr-border-color) !important;">
                                                        @if(is_string($subkey))
                                                            {{ $valueTranslations[$subkey] ?? ucfirst(str_replace(['_', '-'], ' ', $subkey)) }}
                                                        @else
                                                            {{ $valueTranslations[$subvalue] ?? $subvalue }}
                                                        @endif
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <div>{{ $valueTranslations[$value] ?? (is_string($value) ? ucfirst(str_replace('_', ' ', $value)) : $value) }}</div>
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
                @php
                    // AI davranış kuralları için Türkçe çeviriler
                    $aiTranslations = [
                        // Brand Character
                        'samimi_dostane' => 'Samimi ve Dostane',
                        'ciddi_kurumsal' => 'Ciddi ve Kurumsal', 
                        'enerjik_heyecanli' => 'Enerjik ve Heyecanlı',
                        'sakin_temkinli' => 'Sakin ve Temkinli',
                        'yenilikci_cesur' => 'Yenilikçi ve Cesur',
                        'geleneksel_koklu' => 'Geleneksel ve Köklü',
                        'eglenceli_yaratici' => 'Eğlenceli ve Yaratıcı',
                        'pratik_cozum_odakli' => 'Pratik ve Çözüm Odaklı',
                        
                        // Writing Style  
                        'kisa_net' => 'Kısa ve Net',
                        'detayli_kapsamli' => 'Detaylı ve Kapsamlı',
                        'teknik_bilimsel' => 'Teknik ve Bilimsel',
                        'sade_anlasilir' => 'Sade ve Anlaşılır',
                        'duygusal_etkileyici' => 'Duygusal ve Etkileyici',
                        'gunluk_konusma' => 'Günlük Konuşma Tarzı',
                        'formal_profesyonel' => 'Formal ve Profesyonel',
                        
                        // Sales Approach
                        'guven_kurma' => 'Güven Kurma',
                        'hizli_karar' => 'Hızlı Karar Verme',
                        'detayli_analiz' => 'Detaylı Analiz',
                        'duygusal_baglanti' => 'Duygusal Bağlantı',
                        
                        // AI Response Style
                        'kisa_ozet' => 'Kısa Özet',
                        'uzun_kapsamli' => 'Uzun ve Kapsamlı',
                        'orta_dengeli' => 'Orta ve Dengeli'
                    ];
                @endphp
                
                <div class="row">
                    @if(isset($profile->success_stories['brand_character']) && is_array($profile->success_stories['brand_character']))
                        <div class="col-md-6 mb-3">
                            <strong class="text-muted">Marka Karakteri:</strong>
                            <div class="mt-1">
                                @foreach($profile->success_stories['brand_character'] as $key => $value)
                                    @if($value && $value !== false)
                                        <span class="badge bg-muted me-1 mb-1" style="border-radius: 0.25rem !important; color: var(--tblr-body-color) !important; background-color: var(--tblr-bg-surface) !important; border: 1px solid var(--tblr-border-color) !important;">
                                            {{ $aiTranslations[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    @if(isset($profile->success_stories['writing_style']) && is_array($profile->success_stories['writing_style']))
                        <div class="col-md-6 mb-3">
                            <strong class="text-muted">Yazım Tarzı:</strong>
                            <div class="mt-1">
                                @foreach($profile->success_stories['writing_style'] as $key => $value)
                                    @if($value && $value !== false)
                                        <span class="badge bg-muted me-1 mb-1" style="border-radius: 0.25rem !important; color: var(--tblr-body-color) !important; background-color: var(--tblr-bg-surface) !important; border: 1px solid var(--tblr-border-color) !important;">
                                            {{ $aiTranslations[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    @if(isset($profile->success_stories['sales_approach']) && is_array($profile->success_stories['sales_approach']))
                        <div class="col-md-6 mb-3">
                            <strong class="text-muted">Satış Yaklaşımı:</strong>
                            <div class="mt-1">
                                @foreach($profile->success_stories['sales_approach'] as $key => $value)
                                    @if($value && $value !== false)
                                        <span class="badge bg-muted me-1 mb-1" style="border-radius: 0.25rem !important; color: var(--tblr-body-color) !important; background-color: var(--tblr-bg-surface) !important; border: 1px solid var(--tblr-border-color) !important;">
                                            {{ $aiTranslations[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    @if(isset($profile->ai_behavior_rules['ai_response_style']) && is_array($profile->ai_behavior_rules['ai_response_style']))
                        <div class="col-md-6 mb-3">
                            <strong class="text-muted">AI Yanıt Stili:</strong>
                            <div class="mt-1">
                                @foreach($profile->ai_behavior_rules['ai_response_style'] as $key => $value)
                                    @if($value && $value !== false)
                                        <span class="badge bg-muted me-1 mb-1" style="border-radius: 0.25rem !important; color: var(--tblr-body-color) !important; background-color: var(--tblr-bg-surface) !important; border: 1px solid var(--tblr-border-color) !important;">
                                            {{ $aiTranslations[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    
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
                        @php
                            // Kurucu bilgileri için Türkçe çeviriler
                            $founderLabels = [
                                'founder_name' => 'Kurucu Adı',
                                'founder_story' => 'Kurucu Hikayesi',
                                'founder_name_question' => 'Kurucu Adı Sorusu',
                                'founder_story_question' => 'Kurucu Hikayesi Sorusu',
                                'founder_position' => 'Pozisyon',
                                'founder_qualities' => 'Özellikler'
                            ];
                        @endphp
                        
                        @foreach($profile->founder_info as $key => $value)
                            @if(!empty($value) && $value !== false && !str_ends_with($key, '_question'))
                                <div class="col-md-6 mb-3">
                                    <strong class="text-muted">{{ $founderLabels[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                    <div class="mt-1">
                                        @if(is_array($value))
                                            @foreach($value as $subkey => $subvalue)
                                                @if($subvalue && $subvalue !== false)
                                                    <span class="badge bg-muted me-1 mb-1" style="border-radius: 0.25rem !important; color: var(--tblr-body-color) !important; background-color: var(--tblr-bg-surface) !important; border: 1px solid var(--tblr-border-color) !important;">
                                                        {{ is_string($subkey) ? ucfirst(str_replace(['_', '-'], ' ', $subkey)) : $subvalue }}
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

/* NO ANIMATIONS - STATIC DESIGN */
.card {
    transition: none !important;
}

.card:hover {
    transform: none !important;
    box-shadow: none !important;
}

.btn {
    transition: none !important;
    animation: none !important;
}

.btn:hover {
    transform: none !important;
}

.brand-story-text {
    transition: none !important;
    transform: none !important;
}

.brand-story-text:hover {
    transform: none !important;
    box-shadow: none !important;
}
</style>

<!-- Word Buffer JavaScript Import -->
<script src="{{ asset('admin-assets/libs/ai-word-buffer/ai-word-buffer.js') }}"></script>

<script>
function regenerateBrandStory() {
    if (confirm('Mevcut hikayeniz silinip yeni bir hikaye oluşturulacak. Devam etmek istiyor musunuz?')) {
        
        // Loading state'i göster
        const storyContainer = document.querySelector('.brand-story-text');
        const originalContent = storyContainer.innerHTML;
        
        // Modern loading animation
        storyContainer.innerHTML = `
            <div class="d-flex align-items-center justify-content-center p-4">
                <div class="me-3">
                    <div class="spinner-border text-primary" role="status" style="animation-duration: 0.8s;">
                        <span class="visually-hidden">Hikaye oluşturuluyor...</span>
                    </div>
                </div>
                <div>
                    <div class="fw-bold text-primary">AI marka hikayenizi oluşturuyor...</div>
                    <small class="text-muted">Bu işlem birkaç dakika sürebilir, lütfen bekleyin</small>
                </div>
            </div>
        `;
        
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
            if (data.success && data.story) {
                // 🚀 WORD BUFFER SİSTEMİ İLE HIKAYEYİ GÖSTER
                
                // Word buffer ile hikayeyi animated olarak göster
                if (window.AIWordBuffer) {
                    const buffer = new window.AIWordBuffer(storyContainer, {
                        wordDelay: 120,           // Hikaye için biraz yavaş
                        fadeEffect: true,
                        enableMarkdown: false,    // HTML değil düz metin
                        typewriterSpeed: 100,
                        punctuationDelay: 200     // Noktalama işaretlerinde dur
                    });
                    
                    // Buffer'ı başlat
                    buffer.start();
                    
                    // Hikayeyi ekle
                    buffer.addContent(data.story);
                    
                    // 1 saniye sonra flush et
                    setTimeout(() => {
                        buffer.flush();
                        
                        // 3 saniye sonra glow efekti ekle
                        setTimeout(() => {
                            storyContainer.style.transition = 'all 0.5s ease';
                            storyContainer.style.boxShadow = '0 0 20px rgba(147, 51, 234, 0.4)';
                            setTimeout(() => {
                                storyContainer.style.boxShadow = '';
                            }, 2000);
                        }, 3000);
                    }, 1000);
                    
                } else {
                    // Fallback: Normal metin gösterimi
                    storyContainer.innerHTML = data.story.replace(/\n/g, '<br>');
                }
                
            } else {
                // Hata durumunda eski içeriği geri yükle
                storyContainer.innerHTML = originalContent;
            }
        })
        .catch(error => {
            storyContainer.innerHTML = originalContent;
        });
    }
}

function copyBrandStory() {
    const storyElement = document.querySelector('.brand-story-text');
    if (!storyElement) {
        return;
    }
    
    const storyText = storyElement.innerText || storyElement.textContent;
    const btn = event.target.closest('button');
    
    // Modern clipboard API deneme
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(storyText).then(() => {
            showCopySuccess(btn);
        }).catch(err => {
            fallbackCopy(storyText, btn);
        });
    } else {
        // Fallback copy method
        fallbackCopy(storyText, btn);
    }
}

function fallbackCopy(text, btn) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.opacity = '0';
    document.body.appendChild(textArea);
    textArea.select();
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showCopySuccess(btn);
        } else {
        }
    } catch (err) {
    } finally {
        document.body.removeChild(textArea);
    }
}

function showCopySuccess(btn) {
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check me-2"></i>Kopyalandı!';
    btn.classList.add('btn-success');
    btn.classList.remove('btn-outline-secondary');
    
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.classList.remove('btn-success');
        btn.classList.add('btn-outline-secondary');
    }, 2000);
}

{{-- LIVE BRAND STORY GENERATION --}}
@if(!$profile->hasBrandStory() && $profile->is_completed)
// Auto-start brand story generation when profile is completed but no story exists
document.addEventListener('DOMContentLoaded', function() {
    
    const loadingElement = document.getElementById('brand-story-auto-loading');
    if (loadingElement) {
        startLiveBrandStoryGeneration();
    }
});

function startLiveBrandStoryGeneration() {
    
    // Update loading state with more dynamic text
    updateLoadingProgress();
    
    // Start the AJAX request for story generation
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
            // Story generated successfully - reload page to show it
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            // Show error
            showStoryGenerationError(data.message || 'Hikaye oluşturulurken bir hata oluştu');
        }
    })
    .catch(error => {
        console.error('❌ Brand story generation error:', error);
        showStoryGenerationError('Bağlantı hatası: ' + error.message);
    });
}

function updateLoadingProgress() {
    const loadingElement = document.getElementById('brand-story-auto-loading');
    if (!loadingElement) return;
    
    const messages = [
        'Profil bilgileriniz analiz ediliyor...',
        'Marka değerleriniz belirleniyor...',
        'Sektörel özellikler değerlendiriliyor...',
        'Yaratıcı hikaye yazılıyor...',
        'Son dokunuşlar yapılıyor...'
    ];
    
    let currentIndex = 0;
    const messageElement = loadingElement.querySelector('p');
    
    const interval = setInterval(() => {
        if (currentIndex < messages.length) {
            messageElement.textContent = messages[currentIndex];
            currentIndex++;
        } else {
            messageElement.textContent = 'Hikayeniz neredeyse hazır...';
            clearInterval(interval);
        }
    }, 3000); // Change message every 3 seconds
}

function showStoryGenerationError(message) {
    const loadingElement = document.getElementById('brand-story-auto-loading');
    if (!loadingElement) return;
    
    loadingElement.innerHTML = `
        <div class="text-center py-4">
            <div class="mb-3">
                <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
            </div>
            <h5 class="text-warning mb-2">Hikaye Oluşturulamadı</h5>
            <p class="text-muted mb-3">${message}</p>
            <button class="btn btn-primary" onclick="startLiveBrandStoryGeneration()">
                <i class="fas fa-retry me-2"></i>Tekrar Dene
            </button>
        </div>
    `;
}
@endif
</script>