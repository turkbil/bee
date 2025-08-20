<!-- Admin Panel - Tabler.io Bootstrap Style -->
<div class="nav-item dropdown me-2">
    <!-- Admin Desktop Language Switcher Style -->
    <a href="#" class="nav-link d-flex align-items-center justify-content-center" 
       data-bs-toggle="dropdown" tabindex="-1" aria-expanded="false" 
       data-bs-toggle="tooltip" data-bs-placement="bottom" 
       title="{{ $currentAdminLocale === $currentSiteLocale ? 'Dil Değiştir' : 'Üst: Admin | Alt: Veri' }}" 
       style="width: 40px; height: 40px; border-radius: 0.375rem;">
       
        <div class="position-relative lang-icon-container">
            <!-- Loading state: Spinner dönüyor -->
            <div wire:loading wire:target="switchLanguage,switchSiteLanguage">
                <i class="fa-solid fa-spinner fa-spin" style="font-size: 18px;"></i>
            </div>
            
            <!-- Normal state: Flag gösterimi -->
            <div wire:loading.remove wire:target="switchLanguage,switchSiteLanguage">
                @php
                    $adminFlag = $currentAdminLanguage->flag_icon ?? '🌐';
                    $siteFlag = $currentSiteLanguage ? ($currentSiteLanguage->flag_icon ?? '🌐') : '🌐';
                    $isSameLanguage = $currentAdminLocale === $currentSiteLocale;
                    
                    // Fallback: Site languages listesinden flag'i bul
                    if (!$currentSiteLanguage && $currentSiteLocale) {
                        foreach($siteLanguages as $lang) {
                            if ($lang->code === $currentSiteLocale) {
                                $siteFlag = $lang->flag_icon ?? '🌐';
                                break;
                            }
                        }
                    }
                @endphp
                
                @if($isSameLanguage)
                    <!-- Aynı dil: Tek bayrak göster -->
                    <span style="font-size: 20px; font-family: 'Apple Color Emoji', 'Segoe UI Emoji', 'Noto Color Emoji', sans-serif;">{{ $adminFlag }}</span>
                @else
                    <!-- Farklı diller: Alt alta iki bayrak göster -->
                    <div style="font-family: 'Apple Color Emoji', 'Segoe UI Emoji', 'Noto Color Emoji', sans-serif; line-height: 1.1;">
                        <div style="font-size: 14px; text-align: center;">{{ $adminFlag }}</div>
                        <div style="font-size: 14px; text-align: center; font-weight: bold; color: #666;">
                            @php
                                // Debug: Site language flag'i bul
                                $debugSiteFlag = '🌐';
                                $debugText = strtoupper($currentSiteLocale);
                                
                                foreach($siteLanguages as $lang) {
                                    if ($lang->code === $currentSiteLocale) {
                                        $debugSiteFlag = $lang->flag_icon ?? '🌐';
                                        break;
                                    }
                                }
                                
                                // Eğer flag bulunursa emoji göster, bulunamazsa text göster
                                echo ($debugSiteFlag !== '🌐') ? $debugSiteFlag : $debugText;
                            @endphp
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </a>
    
    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="min-width: 220px;">
        <!-- Admin Dili Bölümü -->
        <h6 class="dropdown-header">{{ __('admin.languages') }}</h6>
        
        @forelse($adminLanguages as $language)
            <button type="button" 
                    class="dropdown-item language-switch-link {{ $language->code === $currentAdminLocale ? 'active' : '' }}"
                    wire:click="switchLanguage('{{ $language->code }}')"
                    wire:loading.attr="disabled">
                <span class="me-2" style="font-size: 16px;">
                    {{ $language->flag_icon ?? '🌐' }}
                </span>
                {{ $language->native_name }}
                @if($language->code === $currentAdminLocale)
                    <i class="fa-solid fa-check ms-auto text-success"></i>
                @endif
            </button>
        @empty
            <div class="dropdown-item text-muted">
                <i class="fa-solid fa-info-circle me-2"></i>
                Admin dili bulunamadı
            </div>
        @endforelse
        
        <div class="dropdown-divider"></div>
        
        <!-- Veri Dili Bölümü -->
        <h6 class="dropdown-header">{{ __('admin.data_language') }}</h6>
        
        @forelse($siteLanguages as $language)
            <button type="button" 
                    class="dropdown-item language-switch-link {{ $language->code === $currentSiteLocale ? 'active' : '' }}"
                    wire:click="switchSiteLanguage('{{ $language->code }}')"
                    wire:loading.attr="disabled">
                <span class="me-2" style="font-size: 16px;">
                    {{ $language->flag_icon ?? '🌐' }}
                </span>
                {{ $language->native_name }}
                @if($language->code === $currentSiteLocale)
                    <i class="fa-solid fa-check ms-auto text-success"></i>
                @endif
            </button>
        @empty
            <div class="dropdown-item text-muted">
                <i class="fa-solid fa-info-circle me-2"></i>
                Veri dili bulunamadı
            </div>
        @endforelse
        
    </div>
    
</div>

<script>
// Livewire event listener - sayfa yenileme
document.addEventListener('livewire:init', () => {
    Livewire.on('refreshPage', (event) => {
        console.log('🔄 Dil değişti, sayfa yenileniyor...');
        
        // Kısa bir delay ile sayfayı yenile (session save için)
        setTimeout(() => {
            window.location.reload();
        }, 200);
    });
    
    Livewire.on('reloadPage', (event) => {
        console.log('🔄 Veri dili değişti, sayfa yenileniyor...');
        
        // Kısa bir delay ile sayfayı yenile (session save için)
        setTimeout(() => {
            window.location.reload();
        }, 200);
    });
});
</script>