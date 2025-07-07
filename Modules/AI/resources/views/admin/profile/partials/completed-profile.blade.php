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
    
    {{-- Brand Story Section --}}
    <div class="col-12 mt-4">
        <div class="card" style="border-radius: 15px; border: 1px solid rgba(147, 51, 234, 0.2);">
            <div class="card-header" style="background: linear-gradient(135deg, rgba(147, 51, 234, 0.1), rgba(147, 51, 234, 0.05)); border-radius: 15px 15px 0 0;">
                <h3 class="card-title d-flex align-items-center">
                    <i class="fas fa-book-open me-2" style="color: #9333ea;"></i>
                    Marka Hikayeniz
                </h3>
            </div>
            <div class="card-body">
                @if($profile->hasBrandStory())
                    {{-- Hikaye mevcut --}}
                    <div class="brand-story-content">
                        <div class="brand-story-text" style="
                            font-size: 1.1rem;
                            line-height: 1.8;
                            color: #374151;
                            text-align: justify;
                            padding: 1.5rem;
                            background: rgba(147, 51, 234, 0.05);
                            border-radius: 10px;
                            border-left: 4px solid #9333ea;
                        ">
                            {!! nl2br(e($profile->brand_story)) !!}
                        </div>
                        
                        @if($profile->brand_story_created_at)
                            <div class="mt-3 text-muted small">
                                <i class="fas fa-clock me-1"></i>
                                {{ $profile->brand_story_created_at->format('d.m.Y H:i') }} tarihinde oluşturuldu
                            </div>
                        @endif
                        
                        <div class="mt-3">
                            <button class="btn btn-outline-primary" onclick="regenerateBrandStory()">
                                <i class="fas fa-sync-alt me-2"></i>
                                Hikayeyi Yeniden Oluştur
                            </button>
                            <button class="btn btn-outline-secondary ms-2" onclick="copyBrandStory()">
                                <i class="fas fa-copy me-2"></i>
                                Kopyala
                            </button>
                        </div>
                    </div>
                @elseif(isset($brandStoryGenerating) && $brandStoryGenerating)
                    {{-- Hikaye oluşturuluyor --}}
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary mb-3" role="status">
                            <span class="visually-hidden">Yükleniyor...</span>
                        </div>
                        <h5 class="mb-2">Marka Hikayeniz Oluşturuluyor...</h5>
                        <p class="text-muted">Yapay zeka asistanınız profil bilgilerinize göre özel bir hikaye hazırlıyor. Bu işlem 2-3 dakika sürebilir.</p>
                        <button class="btn btn-primary" onclick="location.reload()" disabled>
                            <i class="fas fa-hourglass-half me-2"></i>
                            İşlem devam ediyor...
                        </button>
                    </div>
                @else
                    {{-- Hikaye yok --}}
                    <div class="text-center py-4">
                        <i class="fas fa-book-open mb-3" style="font-size: 3rem; color: #e9ecef;"></i>
                        <h5 class="mb-2">Marka Hikayeniz Oluşturuluyor</h5>
                        <p class="text-muted mb-3">Profil bilgilerinize göre otomatik olarak bir marka hikayesi hazırlanacak.</p>
                        <div class="spinner-border text-muted" role="status">
                            <span class="visually-hidden">Yükleniyor...</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

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