<div>
    <div class="row" id="dashboard-cards" style="opacity: 0; transition: opacity 0.3s ease;">
        {{-- AI Profil Durumu Widget (AI modülü aktifse) --}}
        @if(in_array('ai', $activeModules))
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <div class="card">
                <div class="card-header drag-handle">
                    <h3 class="card-title">
                        <i class="fas fa-robot me-2"></i>
                        Yapay Zeka Durumu
                    </h3>
                </div>
                <div class="card-body">
                    @if($aiProfile)
                        <div class="row align-items-center mb-3">
                            <div class="col-auto">
                                <div class="avatar avatar-md">
                                    <i class="fas fa-{{ $aiIsCompleted ? 'check' : ($aiCompletionPercentage > 50 ? 'clock' : 'times') }}"></i>
                                </div>
                            </div>
                            <div class="col">
                                <div class="text-truncate">
                                    <strong>Profil {{ $aiIsCompleted ? 'Tamamlandı' : 'Beklemede' }}</strong>
                                </div>
                                <div class="text-muted">%{{ $aiCompletionPercentage }} tamamlanmış</div>
                            </div>
                        </div>
                        
                        {{-- Progress Bar --}}
                        <div class="mb-3">
                            <div class="progress progress-sm">
                                <div class="progress-bar" 
                                     style="width: {{ $aiCompletionPercentage }}%" role="progressbar" 
                                     aria-valuenow="{{ $aiCompletionPercentage }}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                            <div class="text-muted text-sm mt-1">{{ $aiCompletionPercentage }}% / 100%</div>
                        </div>

                        {{-- Hızlı Aksiyonlar --}}
                        <div class="row g-2">
                            @if(!$aiIsCompleted)
                                <div class="col-12">
                                    <a href="{{ route('admin.ai.profile.jquery-edit', 1) }}" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-cog me-1"></i>
                                        Profili Tamamla
                                    </a>
                                </div>
                            @endif
                            
                            <div class="col-6">
                                <a href="{{ route('admin.ai.profile.show') }}" class="btn btn-outline-secondary btn-sm w-100">
                                    <i class="fas fa-eye me-1"></i>
                                    Görüntüle
                                </a>
                            </div>
                            
                            @if($aiIsCompleted)
                                <div class="col-6">
                                    <a href="{{ route('admin.ai.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                                        <i class="fas fa-comments me-1"></i>
                                        Yapay Zeka Chat
                                    </a>
                                </div>
                            @endif
                        </div>
                    @else
                        {{-- AI Profil Yok --}}
                        <div class="text-center py-4">
                            <i class="fas fa-robot text-muted" style="font-size: 3rem;"></i>
                            <h4 class="mt-3">Yapay Zeka Profili Bulunamadı</h4>
                            <p class="text-muted">Yapay zeka özelliklerini kullanmak için profil oluşturun.</p>
                            <a href="{{ route('admin.ai.profile.jquery-edit', 1) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>
                                Profil Oluştur
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Mini AI Chat Widget --}}
        @if(in_array('ai', $activeModules) && $aiProfile && $aiIsCompleted)
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <div class="card">
                <div class="card-header drag-handle">
                    <h3 class="card-title">
                        <i class="fas fa-comments me-2"></i>
                        Hızlı Yapay Zeka Chat
                    </h3>
                </div>
                <div class="card-body">
                    <div id="chat-messages" style="height: 250px; overflow-y: auto; border: 1px solid var(--tblr-border-color); border-radius: 4px; padding: 12px; margin-bottom: 12px;">
                        <div class="text-center text-muted mb-3">
                            <i class="fas fa-robot" style="font-size: 2rem;"></i>
                        </div>
                        <div class="mb-2">
                            <span class="badge text-secondary mb-1">
                                <i class="fas fa-robot me-1"></i>
                                Yapay Zeka Asistan
                            </span>
                            <div class="p-2 rounded border border-secondary-subtle">
                                @php
                                    $brandName = 'Yapay Zeka Asistan';
                                    $welcomeMessage = 'Merhaba! Size nasıl yardımcı olabilirim?';
                                    try {
                                        if ($aiProfile && isset($aiProfile->company_info['business_name'])) {
                                            $brandName = $aiProfile->company_info['business_name'];
                                            $welcomeMessage = "Merhaba! Ben {$brandName} yapay zeka asistanıyım. Size nasıl yardımcı olabilirim? Her türlü sorunuzda yanınızdayım.";
                                        }
                                    } catch (\Exception $e) {
                                        // Hata durumunda basit mesaj kullan
                                    }
                                @endphp
                                {{ $welcomeMessage }}
                            </div>
                        </div>
                    </div>
                    <div class="input-group">
                        <input type="text" id="chat-input" class="form-control" placeholder="Mesajınızı yazın..." onkeypress="handleChatEnter(event)">
                        <button class="btn btn-primary" type="button" onclick="sendChatMessage()">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- AI Token Durumu Widget (AI modülü aktifse) --}}
        @if(in_array('ai', $activeModules))
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <div class="card">
                <div class="card-header drag-handle">
                    <h3 class="card-title">
                        <i class="fas fa-coins me-2"></i>
                        AI Token Durumu
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row align-items-center mb-3">
                        <div class="col-auto">
                            <div class="avatar avatar-md bg-primary text-white">
                                <i class="fas fa-coins"></i>
                            </div>
                        </div>
                        <div class="col">
                            <div class="text-truncate">
                                <strong>{{ $remainingTokensFormatted }} Token</strong>
                            </div>
                            <div class="text-muted">{{ function_exists('ai_format_token_count') ? ai_format_token_count($totalTokens) : number_format($totalTokens) }} toplam token</div>
                        </div>
                    </div>
                    
                    {{-- Progress Bar --}}
                    <div class="mb-3">
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-{{ $statusColor }}" 
                                 style="width: {{ $usagePercentage }}%" role="progressbar" 
                                 aria-valuenow="{{ $usagePercentage }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <div class="text-muted text-sm mt-1">{{ $statusText }} - %{{ $usagePercentage }} kullanıldı</div>
                    </div>

                    {{-- Token İstatistikleri - Sadece kalan token'lara odaklan --}}
                    <div class="text-center">
                        <div class="p-3 bg-{{ $statusColor }}-lt rounded">
                            <div class="h4 mb-0 text-{{ $statusColor }}">{{ $remainingTokensFormatted }}</div>
                            <div class="text-muted small">Kalan Token</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        {{-- Sayfalar Widget (Page modülü aktifse) --}}
        @if(in_array('page', $activeModules))
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <div class="card">
                <div class="card-header drag-handle">
                    <h3 class="card-title">
                        <i class="fas fa-file-alt me-2"></i>
                        Sayfalar
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row align-items-center mb-3">
                        <div class="col-auto">
                            <div class="avatar avatar-md bg-primary text-white">
                                <i class="fas fa-file-alt"></i>
                            </div>
                        </div>
                        <div class="col">
                            <div class="text-truncate">
                                <strong>{{ number_format($totalPages) }} Sayfa</strong>
                            </div>
                            <div class="text-muted">Toplam sayfa sayısı</div>
                        </div>
                    </div>
                    
                    {{-- Son Eklenen Sayfalar --}}
                    @if($recentPages && count($recentPages) > 0)
                        <div class="mb-3">
                            <h6 class="mb-2">Son Eklenenler</h6>
                            @foreach($recentPages as $page)
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar avatar-xs bg-primary-lt me-2">
                                        <i class="fas fa-file text-primary"></i>
                                    </div>
                                    <div class="flex-fill">
                                        <div class="text-truncate text-sm">{{ is_array($page->title) ? ($page->title[app()->getLocale()] ?? $page->title['tr'] ?? $page->title['en'] ?? 'Başlık') : $page->title }}</div>
                                        <div class="text-muted text-xs">{{ $page->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    
                    <div class="mt-3">
                        <a href="{{ route('admin.page.index') }}" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-eye me-1"></i>
                            Tüm Sayfaları Görüntüle
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        {{-- Portfolio Widget (Portfolio modülü aktifse) --}}
        @if(in_array('portfolio', $activeModules))
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <div class="card">
                <div class="card-header drag-handle">
                    <h3 class="card-title">
                        <i class="fas fa-briefcase me-2"></i>
                        Portfolio
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row align-items-center mb-3">
                        <div class="col-auto">
                            <div class="avatar avatar-md bg-primary text-white">
                                <i class="fas fa-briefcase"></i>
                            </div>
                        </div>
                        <div class="col">
                            <div class="text-truncate">
                                <strong>{{ number_format($totalPortfolios) }} Proje</strong>
                            </div>
                            <div class="text-muted">Toplam portfolio sayısı</div>
                        </div>
                    </div>
                    
                    {{-- Son Eklenen Portfoliolar --}}
                    @if($recentPortfolios && count($recentPortfolios) > 0)
                        <div class="mb-3">
                            <h6 class="mb-2">Son Projeler</h6>
                            @foreach($recentPortfolios as $portfolio)
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar avatar-xs bg-primary-lt me-2">
                                        <i class="fas fa-folder text-primary"></i>
                                    </div>
                                    <div class="flex-fill">
                                        <div class="text-truncate text-sm">{{ is_array($portfolio->title) ? ($portfolio->title[app()->getLocale()] ?? $portfolio->title['tr'] ?? $portfolio->title['en'] ?? 'Proje') : $portfolio->title }}</div>
                                        <div class="text-muted text-xs">{{ $portfolio->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    
                    <div class="mt-3">
                        <a href="{{ route('admin.portfolio.index') }}" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-eye me-1"></i>
                            Tüm Projeleri Görüntüle
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        {{-- Duyurular Widget (Announcement modülü aktifse) --}}
        @if(in_array('announcement', $activeModules))
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <div class="card">
                <div class="card-header drag-handle">
                    <h3 class="card-title">
                        <i class="fas fa-bullhorn me-2"></i>
                        Duyurular
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row align-items-center mb-3">
                        <div class="col-auto">
                            <div class="avatar avatar-md bg-primary text-white">
                                <i class="fas fa-bullhorn"></i>
                            </div>
                        </div>
                        <div class="col">
                            <div class="text-truncate">
                                <strong>{{ number_format($totalAnnouncements) }} Duyuru</strong>
                            </div>
                            <div class="text-muted">Toplam duyuru sayısı</div>
                        </div>
                    </div>
                    
                    {{-- Son Eklenen Duyurular --}}
                    @if($recentAnnouncements && count($recentAnnouncements) > 0)
                        <div class="mb-3">
                            <h6 class="mb-2">Son Duyurular</h6>
                            @foreach($recentAnnouncements as $announcement)
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar avatar-xs bg-primary-lt me-2">
                                        <i class="fas fa-megaphone text-primary"></i>
                                    </div>
                                    <div class="flex-fill">
                                        <div class="text-truncate text-sm">{{ is_array($announcement->title) ? ($announcement->title[app()->getLocale()] ?? $announcement->title['tr'] ?? $announcement->title['en'] ?? 'Duyuru') : $announcement->title }}</div>
                                        <div class="text-muted text-xs">{{ $announcement->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    
                    <div class="mt-3">
                        <a href="{{ route('admin.announcement.index') }}" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-eye me-1"></i>
                            Tüm Duyuruları Görüntüle
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        {{-- Son Giriş Yapan Kullanıcılar Widget (UserManagement modülü aktifse) --}}
        @if(in_array('usermanagement', $activeModules))
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <div class="card">
                <div class="card-header drag-handle">
                    <h3 class="card-title">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Son Girişler
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="avatar avatar-md bg-primary text-white">
                                    <i class="fas fa-sign-in-alt"></i>
                                </div>
                            </div>
                            <div class="col">
                                <div class="text-truncate">
                                    <strong>{{ count($recentLogins) }} Kullanıcı</strong>
                                </div>
                                <div class="text-muted">Son giriş yapanlar</div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Son Giriş Yapan Kullanıcılar --}}
                    @if($recentLogins && count($recentLogins) > 0)
                        <div class="mb-3">
                            @foreach($recentLogins as $user)
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar avatar-xs bg-primary-lt me-2">
                                        <i class="fas fa-user text-primary"></i>
                                    </div>
                                    <div class="flex-fill">
                                        <div class="text-truncate text-sm">{{ $user->name }}</div>
                                        <div class="text-muted text-xs">{{ $user->last_login_at ? (is_string($user->last_login_at) ? \Carbon\Carbon::parse($user->last_login_at)->diffForHumans() : $user->last_login_at->diffForHumans()) : 'Hiç giriş yapmamış' }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-users" style="font-size: 2rem;"></i>
                            <p class="mt-2 mb-0">Henüz giriş yapan kullanıcı yok</p>
                        </div>
                    @endif
                    
                    <div class="mt-3">
                        <a href="{{ route('admin.usermanagement.index') }}" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-eye me-1"></i>
                            Tüm Kullanıcıları Görüntüle
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        {{-- Son Üye Olan Kullanıcılar Widget (UserManagement modülü aktifse) --}}
        @if(in_array('usermanagement', $activeModules))
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <div class="card">
                <div class="card-header drag-handle">
                    <h3 class="card-title">
                        <i class="fas fa-user-plus me-2"></i>
                        Yeni Üyeler
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="avatar avatar-md bg-primary text-white">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                            </div>
                            <div class="col">
                                <div class="text-truncate">
                                    <strong>{{ count($newUsers) }} Yeni Üye</strong>
                                </div>
                                <div class="text-muted">Son katılanlar</div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Son Üye Olan Kullanıcılar --}}
                    @if($newUsers && count($newUsers) > 0)
                        <div class="mb-3">
                            @foreach($newUsers as $user)
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar avatar-xs bg-primary-lt me-2">
                                        <i class="fas fa-user-check text-primary"></i>
                                    </div>
                                    <div class="flex-fill">
                                        <div class="text-truncate text-sm">{{ $user->name }}</div>
                                        <div class="text-muted text-xs">{{ $user->created_at->diffForHumans() }} katıldı</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-user-plus" style="font-size: 2rem;"></i>
                            <p class="mt-2 mb-0">Henüz yeni üye yok</p>
                        </div>
                    @endif
                    
                    <div class="mt-3">
                        <a href="{{ route('admin.usermanagement.index') }}" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-eye me-1"></i>
                            Tüm Üyeleri Görüntüle
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('styles')
<style>
/* Drag Handle */
.drag-handle {
    cursor: move;
    user-select: none;
}

.drag-handle:hover {
    background-color: var(--tblr-gray-50);
}

.dark .drag-handle:hover {
    background-color: var(--tblr-gray-900);
}

/* PORTFOLIO MODÜLÜ TAM ANIMASYON SİSTEMİ */
.sortable-ghost {
    opacity: 0.3;
    background: var(--tblr-primary-lt);
    border: 2px dashed var(--tblr-primary);
    transform: scale(0.95);
    border-radius: 8px;
}

.sortable-chosen {
    transform: scale(1.02);
    box-shadow: 0 8px 24px rgba(var(--tblr-primary-rgb), 0.2);
    z-index: 1000;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    background: var(--tblr-body-bg);
}

.sortable-drag {
    opacity: 0.95;
    transform: rotate(2deg) scale(1.02);
    z-index: 9999;
    transition: none;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
}

/* Smooth transitions for all cards - ENHANCED */
.col-12 {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    transform: translateY(0);
    opacity: 1;
}

/* Dragging state - disable transitions on dragged item */
.dragging {
    transition: none !important;
}

/* Enhanced drag handle */
.drag-handle {
    transition: all 0.2s ease;
}

.drag-handle:hover {
    background-color: var(--tblr-gray-50);
    cursor: grab;
}

.drag-handle:active {
    cursor: grabbing;
    background-color: var(--tblr-gray-100);
}


/* Grabbing cursor for body during drag */
body.sortable-grabbing {
    cursor: grabbing !important;
}

/* Smooth card transitions during sorting */
.col-12:not(.sortable-chosen):not(.sortable-ghost) {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    transform: translateY(0);
}

/* Cards moving up/down smoothly */
.col-12.sortable-fallback {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

/* Enhanced smooth transitions for all widgets */
#dashboard-cards .col-12 {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    transform: translateY(0) translateX(0);
}

/* Disable transitions only on actively dragged item */
#dashboard-cards .col-12.dragging {
    transition: none !important;
}

/* Better visual feedback during drag */
#dashboard-cards .col-12:not(.dragging) {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Smooth repositioning animation */
#dashboard-cards .col-12:not(.sortable-chosen):not(.sortable-ghost):not(.dragging) {
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), 
                opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}


/* Dark mode support */
.dark .sortable-ghost {
    background: var(--tblr-primary-dark);
    border-color: var(--tblr-primary);
}

.dark .sortable-chosen {
    background: var(--tblr-dark-bg);
    box-shadow: 0 8px 24px rgba(var(--tblr-primary-rgb), 0.3);
}

.dark .drag-handle:hover {
    background-color: var(--tblr-gray-900);
}

.dark .drag-handle:active {
    background-color: var(--tblr-gray-800);
}
</style>
@endpush

@push('scripts')
<!-- Sortable Library -->
<script src="/admin-assets/libs/sortable/sortable.min.js"></script>

<script>
// PORTFOLIO MODÜLÜ TAM ANIMASYON SİSTEMİ
$(document).ready(function() {
    var dashboardContainer = $('#dashboard-cards')[0];
    if (!dashboardContainer) {
        console.log('⚠️ Dashboard container bulunamadı');
        return;
    }
    
    console.log('🚀 Dashboard sortable initialize ediliyor...');
    
    // Initialize Sortable - Portfolio modülü exact copy
    window.dashboardSortable = Sortable.create(dashboardContainer, {
        animation: 250,
        delay: 50,
        delayOnTouchOnly: true,
        handle: '.drag-handle',
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        dragClass: 'sortable-drag',
        forceFallback: false,
        fallbackClass: 'sortable-fallback',
        easing: 'cubic-bezier(0.4, 0, 0.2, 1)',
        
        onStart: function(evt) {
            document.body.style.cursor = 'grabbing';
            document.body.classList.add('sortable-grabbing');
            $(evt.item).addClass('dragging');
            
            console.log('🚀 Dashboard widget drag başladı:', $(evt.item).find('.card-title').text().trim());
        },
        
        onEnd: function(evt) {
            document.body.style.cursor = 'default';
            document.body.classList.remove('sortable-grabbing');
            $(evt.item).removeClass('dragging');
            
            console.log('🔄 Dashboard widget sıralandı:', {
                oldIndex: evt.oldIndex,
                newIndex: evt.newIndex,
                item: $(evt.item).find('.card-title').text().trim()
            });
            
            // Layout kaydetme
            saveDashboardLayout();
        },
        
        onMove: function(evt) {
            // Smooth transition during move
            return true;
        }
    });
    
    // Sayfa yüklendiğinde kaydedilmiş sıralamayı geri yükle
    console.log('🔄 Dashboard sortable başlatıldı, layout yükleniyor...');
    setTimeout(function() {
        loadDashboardLayout();
        // Layout uygulandıktan sonra container'ı görünür yap
        setTimeout(function() {
            $('#dashboard-cards').css('opacity', '1');
        }, 50);
    }, 100);
    
    
    function saveDashboardLayout() {
        var layout = [];
        $('#dashboard-cards .col-12').each(function(index) {
            var title = $(this).find('.card-title').text().trim();
            layout.push(title);
        });
        
        // localStorage'e kaydet
        try {
            localStorage.setItem('dashboard_layout', JSON.stringify(layout));
            console.log('✅ Dashboard layout localStorage\'e kaydedildi:', layout);
            
            // Session'a da kaydet (AJAX ile, sayfa render'ı tetiklemeyen)
            fetch('/admin/dashboard/save-layout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ layout: layout })
            }).then(response => {
                if (response.ok) {
                    console.log('✅ Dashboard layout session\'a kaydedildi');
                } else {
                    console.log('⚠️ Dashboard layout session kayıt hatası');
                }
            }).catch(e => {
                console.log('⚠️ Dashboard layout session kayıt network hatası:', e);
            });
        } catch (e) {
            console.error('❌ Dashboard layout kaydetme hatası:', e);
        }
    }
    
    function loadDashboardLayout() {
        try {
            var savedLayout = localStorage.getItem('dashboard_layout');
            if (savedLayout) {
                var layout = JSON.parse(savedLayout);
                if (layout && Array.isArray(layout) && layout.length > 0) {
                    applyDashboardLayout(layout);
                    console.log('✅ Dashboard layout localStorage\'den yüklendi:', layout);
                } else {
                    console.log('⚠️ Dashboard layout boş veya geçersiz');
                }
            } else {
                console.log('ℹ️ Dashboard layout localStorage\'de bulunamadı, varsayılan sıralama kullanılacak');
            }
        } catch(e) {
            console.error('❌ Dashboard layout yükleme hatası:', e);
            // Bozuk veri varsa temizle
            localStorage.removeItem('dashboard_layout');
        }
    }
    
    function applyDashboardLayout(layout) {
        var container = $('#dashboard-cards');
        
        if (!window.dashboardSortable) {
            console.log('⚠️ Dashboard sortable henüz hazır değil, layout uygulanmayacak');
            return;
        }
        
        console.log('🔄 Dashboard layout uygulanıyor...', layout);
        
        // Animasyon için geçici olarak disabled
        window.dashboardSortable.option('disabled', true);
        
        var successCount = 0;
        var failCount = 0;
        
        layout.forEach(function(title, targetIndex) {
            var widget = container.find('.col-12').filter(function() {
                return $(this).find('.card-title').text().trim() === title;
            }).first();
            
            if (widget.length > 0) {
                var currentIndex = widget.index();
                
                // Eğer widget farklı pozisyonda ise taşı
                if (currentIndex !== targetIndex) {
                    if (targetIndex === 0) {
                        container.prepend(widget);
                    } else {
                        var targetWidget = container.find('.col-12').eq(targetIndex);
                        if (targetWidget.length > 0) {
                            widget.insertBefore(targetWidget);
                        } else {
                            container.append(widget);
                        }
                    }
                    successCount++;
                } else {
                    console.log('ℹ️ Widget zaten doğru pozisyonda:', title);
                }
            } else {
                console.log('⚠️ Widget bulunamadı:', title);
                failCount++;
            }
        });
        
        console.log('✅ Dashboard layout uygulandı:', { 
            moved: successCount, 
            failed: failCount, 
            total: layout.length 
        });
        
        // Animasyon tamamlandıktan sonra sortable'ı yeniden etkinleştir
        setTimeout(function() {
            window.dashboardSortable.option('disabled', false);
        }, 300);
    }
});

// AI Chat Functions
function handleChatEnter(event) {
    if (event.key === 'Enter') {
        sendChatMessage();
    }
}

function sendChatMessage() {
    const input = document.getElementById('chat-input');
    const message = input.value.trim();
    
    if (!message) return;
    
    // Add user message to chat
    addMessageToChat('user', message);
    input.value = '';
    
    // Add loading indicator
    const loadingId = addMessageToChat('ai', '<i class="spinner-border spinner-border-sm me-2"></i>Düşünüyor...');
    
    // Send message to AI
    fetch('{{ route("admin.ai.profile.chat") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            message: message
        })
    })
    .then(response => response.json())
    .then(data => {
        // Remove loading message
        document.getElementById(loadingId).remove();
        
        if (data.success) {
            let responseText = data.response;
            
            // Akıllı feature detection bilgisi varsa ekle
            if (data.feature_used && data.confidence) {
                responseText += `\n\n<small class="text-muted">🤖 ${data.feature_used} (${Math.round(data.confidence * 100)}% güven)</small>`;
            }
            
            addMessageToChat('ai', responseText);
        } else {
            addMessageToChat('ai', 'Üzgünüm, bir hata oluştu: ' + (data.message || 'Bilinmeyen hata'));
        }
    })
    .catch(error => {
        // Remove loading message
        document.getElementById(loadingId).remove();
        addMessageToChat('ai', 'Bağlantı hatası oluştu. Lütfen tekrar deneyin.');
        console.error('AI Chat Error:', error);
    });
}

function addMessageToChat(type, message) {
    const chatMessages = document.getElementById('chat-messages');
    const messageId = 'msg-' + Date.now();
    
    const messageDiv = document.createElement('div');
    messageDiv.id = messageId;
    messageDiv.className = `mb-2 ${type === 'user' ? 'text-end' : ''}`;
    
    const badge = type === 'user' ? 'text-primary' : 'text-secondary';
    const icon = type === 'user' ? 'ti-user' : 'ti-robot';
    
    messageDiv.innerHTML = `
        <div class="d-inline-block">
            <span class="badge ${badge} mb-1">
                <i class="ti ${icon} me-1"></i>
                ${type === 'user' ? 'Siz' : 'AI Asistan'}
            </span>
            <div class="p-2 rounded ${type === 'user' ? 'border border-primary-subtle' : 'border border-secondary-subtle'}">
                ${message}
            </div>
        </div>
    `;
    
    chatMessages.appendChild(messageDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
    
    return messageId;
}
</script>
@endpush
