<!-- Admin Panel - Tabler.io Bootstrap Style -->
<div class="nav-item dropdown me-2">
    <!-- Admin Desktop Language Switcher Style -->
    <a href="#" class="nav-link d-flex align-items-center justify-content-center" 
       data-bs-toggle="dropdown" tabindex="-1" aria-expanded="false" 
       data-bs-toggle="tooltip" data-bs-placement="bottom" 
       title="Admin Dili Değiştir" 
       style="width: 40px; height: 40px; border-radius: 0.375rem;">
       
        <div class="position-relative">
            <div style="transition: opacity 0.3s ease;">
                @if($currentLanguageObject && $currentLanguageObject->flag_icon)
                    <span style="font-size: 20px;">{{ $currentLanguageObject->flag_icon }}</span>
                @else
                    <i class="fa-solid fa-language" style="font-size: 18px;"></i>
                @endif
            </div>
        </div>
    </a>
    
    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="min-width: 220px;">
        <!-- Admin Dili Bölümü -->
        <h6 class="dropdown-header">{{ __('languagemanagement::admin.admin_language') }}</h6>
        
        @forelse($adminLanguages as $language)
            <button type="button" 
                    class="dropdown-item language-switch-link {{ $language->code === $currentAdminLocale ? 'active' : '' }}"
                    wire:click="switchLanguage('{{ $language->code }}')"
                    onclick="return handleLanguageSwitch(event, this)">
                <span class="me-2" style="font-size: 16px;">
                    {{ $language->flag_icon ?? '🌐' }}
                </span>
                {{ $language->native_name }}
                <small class="text-muted ms-1">(Admin)</small>
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
        <h6 class="dropdown-header">{{ __('languagemanagement::admin.data_language') }}</h6>
        
        @forelse($siteLanguages as $language)
            <button type="button" 
                    class="dropdown-item language-switch-link {{ $language->code === $currentSiteLocale ? 'active' : '' }}"
                    wire:click="switchSiteLanguage('{{ $language->code }}')"
                    onclick="return handleLanguageSwitch(event, this)">
                <span class="me-2" style="font-size: 16px;">
                    {{ $language->flag_icon ?? '🌐' }}
                </span>
                {{ $language->native_name }}
                <small class="text-muted ms-1">(Veri)</small>
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
        
        @if($isLoading)
            <div class="dropdown-item text-center">
                <div class="spinner-border spinner-border-sm" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function handleLanguageSwitch(event, element) {
    // Aktif dil seçiliyse hiçbir şey yapma (sadece dropdown'lar için)
    if (element.classList.contains('active') && element.closest('.dropdown')) {
        event.preventDefault();
        return false;
    }
    
    // Dropdown kapat (sadece bootstrap varsa)
    if (typeof bootstrap !== 'undefined') {
        const dropdownElement = element.closest('.dropdown');
        if (dropdownElement) {
            const dropdownToggle = dropdownElement.querySelector('.dropdown-toggle');
            if (dropdownToggle) {
                const dropdown = bootstrap.Dropdown.getInstance(dropdownToggle);
                if (dropdown) {
                    dropdown.hide();
                }
            }
        }
    }
    
    // Hızlı top loading bar - Tabler Native
    const loadingBar = document.createElement('div');
    loadingBar.id = 'language-loading-bar';
    loadingBar.className = 'progress progress-sm';
    loadingBar.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        z-index: 99999;
        opacity: 1;
    `;
    loadingBar.innerHTML = '<div class="progress-bar progress-bar-indeterminate"></div>';
    document.body.appendChild(loadingBar);
    
    // Livewire click olayını çalıştır
    return true;
}

// Sayfa yüklendiğinde loading bar'ı kaldır
document.addEventListener('DOMContentLoaded', function() {
    const loadingBar = document.getElementById('language-loading-bar');
    if (loadingBar) {
        setTimeout(() => {
            loadingBar.style.opacity = '0';
            setTimeout(() => loadingBar.remove(), 200);
        }, 100);
    }
});

// Livewire event listener - sayfa yenileme
document.addEventListener('livewire:init', () => {
    Livewire.on('reloadPage', (event) => {
        console.log('🔄 Veri dili değişti, sayfa yenileniyor...');
        
        // Kısa bir delay ile sayfayı yenile (session save için)
        setTimeout(() => {
            window.location.reload();
        }, 100);
    });
});
</script>