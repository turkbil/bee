<div class="nav-item dropdown me-2">
    <!-- Desktop Language Switcher -->
    <a href="#" class="nav-link d-flex align-items-center justify-content-center" 
       data-bs-toggle="dropdown" tabindex="-1" aria-expanded="false" 
       data-bs-toggle="tooltip" data-bs-placement="bottom" 
       title="{{ t('common.change_language') }}" 
       style="width: 40px; height: 40px; border-radius: 0.375rem;">
       
        <div class="position-relative">
            <!-- Language Icon/Flag -->
            <div style="transition: opacity 0.3s ease;">
                @if($currentLanguage && $currentLanguage->flag_icon)
                    <span style="font-size: 20px;">{!! $currentLanguage->flag_icon !!}</span>
                @else
                    <i class="fa-solid fa-language" style="font-size: 18px;"></i>
                @endif
            </div>
        </div>
    </a>
    
    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="min-width: 180px;">
        <h6 class="dropdown-header">{{ t('common.select_language') }}</h6>
        <div class="dropdown-divider"></div>
        
        @forelse($systemLanguages as $language)
            <a href="{{ route('admin.language.switch', $language->code) }}" 
               class="dropdown-item language-switch-link {{ $currentLocale == $language->code ? 'active' : '' }}"
               onclick="return handleLanguageSwitch(event, this)">
                <span class="me-2" style="font-size: 16px;">
                    @if($language->flag_icon)
                        {!! $language->flag_icon !!}
                    @else
                        <i class="fa-solid fa-flag"></i>
                    @endif
                </span>
                {{ $language->native_name }}
                @if($currentLocale == $language->code)
                    <i class="fa-solid fa-check ms-auto text-success"></i>
                @endif
            </a>
        @empty
            <div class="dropdown-item text-muted">
                <i class="fa-solid fa-info-circle me-2"></i>
                {{ t('common.no_language_found') }}
            </div>
        @endforelse
    </div>
</div>

<script>
function handleLanguageSwitch(event, element) {
    // Aktif dil seçiliyse hiçbir şey yapma
    if (element.classList.contains('active')) {
        event.preventDefault();
        return false;
    }
    
    // Dropdown kapat
    const dropdown = bootstrap.Dropdown.getInstance(element.closest('.dropdown').querySelector('.dropdown-toggle'));
    if (dropdown) {
        dropdown.hide();
    }
    
    // Hızlı top loading bar
    const loadingBar = document.createElement('div');
    loadingBar.id = 'language-loading-bar';
    loadingBar.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 0%;
        height: 3px;
        background: linear-gradient(90deg, #0054a6, #0ea5e9);
        z-index: 99999;
        transition: width 0.3s ease;
        box-shadow: 0 0 10px rgba(0, 84, 166, 0.5);
    `;
    document.body.appendChild(loadingBar);
    
    // Animasyon başlat
    setTimeout(() => loadingBar.style.width = '30%', 10);
    setTimeout(() => loadingBar.style.width = '60%', 150);
    setTimeout(() => loadingBar.style.width = '90%', 300);
    
    // Normal link davranışı
    return true;
}

// Sayfa yüklendiğinde loading bar'ı tamamla ve kaldır
document.addEventListener('DOMContentLoaded', function() {
    const loadingBar = document.getElementById('language-loading-bar');
    if (loadingBar) {
        loadingBar.style.width = '100%';
        setTimeout(() => {
            loadingBar.style.opacity = '0';
            setTimeout(() => loadingBar.remove(), 200);
        }, 100);
    }
});
</script>

