{{-- Tek dil varsa language switcher'ı gizle --}}
@if(count($languages) > 1)
@if($context === 'admin')
<!-- Admin Panel - Tabler.io Bootstrap Style -->
<div class="nav-item dropdown me-2">
    @if($style === 'dropdown')
        <!-- Admin Desktop Language Switcher Style -->
        <a href="#" class="nav-link d-flex align-items-center justify-content-center" 
           data-bs-toggle="dropdown" tabindex="-1" aria-expanded="false" 
           data-bs-toggle="tooltip" data-bs-placement="bottom" 
           title="Admin Dili Değiştir" 
           style="width: 40px; height: 40px; border-radius: 0.375rem;">
           
            <div class="position-relative">
                <div style="transition: opacity 0.3s ease;">
                    @if($currentLanguageFlag)
                        <span style="font-size: 20px;">{{ $currentLanguageFlag }}</span>
                    @else
                        <i class="fa-solid fa-language" style="font-size: 18px;"></i>
                    @endif
                </div>
            </div>
        </a>
        
        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="min-width: 180px;">
            <h6 class="dropdown-header">Admin Dili Seçin</h6>
            <div class="dropdown-divider"></div>
            
            @forelse($languages as $language)
                <button type="button" 
                        class="dropdown-item language-switch-link {{ $language['code'] === $currentLanguage ? 'active' : '' }}"
                        wire:click="switchLanguage('{{ $language['code'] }}')"
                        onclick="return handleLanguageSwitch(event, this)">
                    <span class="me-2" style="font-size: 16px;">
                        {{ $language['flag'] ?? '🌐' }}
                    </span>
                    {{ $language['name'] }}
                    @if($language['code'] === $currentLanguage)
                        <i class="fa-solid fa-check ms-auto text-success"></i>
                    @endif
                </button>
            @empty
                <div class="dropdown-item text-muted">
                    <i class="fa-solid fa-info-circle me-2"></i>
                    Dil bulunamadı
                </div>
            @endforelse
        </div>
        
    @elseif($style === 'mobile')
        <!-- Admin Mobile Style -->
        <div class="dropdown">
            <a href="#" class="d-flex flex-column text-center p-2 border rounded mobile-quick-action" 
               data-bs-toggle="dropdown">
                @if($currentLanguageFlag)
                    <span class="mb-1" style="font-size: 18px;">{{ $currentLanguageFlag }}</span>
                @else
                    <i class="fa-solid fa-language mb-1 text-primary" style="font-size: 18px;"></i>
                @endif
                <small class="fw-bold">Admin Dili</small>
            </a>
            <div class="dropdown-menu dropdown-menu-end">
                @forelse($languages as $language)
                    <button type="button" 
                            class="dropdown-item {{ $language['code'] === $currentLanguage ? 'active' : '' }}"
                            wire:click="switchLanguage('{{ $language['code'] }}')"
                            onclick="return handleLanguageSwitch(event, this)">
                        <span class="me-2">
                            {{ $language['flag'] ?? '🌐' }}
                        </span>
                        {{ $language['name'] }}
                        @if($language['code'] === $currentLanguage)
                            <i class="fa-solid fa-check ms-auto text-success"></i>
                        @endif
                    </button>
                @empty
                    <div class="dropdown-item text-muted">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        Dil bulunamadı
                    </div>
                @endforelse
            </div>
        </div>
    @endif
</div>

@else
<!-- Site Frontend - Tailwind + Alpine.js Style -->
<div x-data="{ open: false }" class="relative inline-block">
    @if($style === 'dropdown')
        <!-- Site Dropdown Style -->
        <button @click="open = !open" 
                class="flex items-center justify-center w-10 h-10 text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white transition-colors duration-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800"
                :class="{ 'bg-gray-100 dark:bg-gray-800': open }">
            @if($currentLanguageFlag)
                <span class="text-xl">{{ $currentLanguageFlag }}</span>
            @else
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                </svg>
            @endif
        </button>
        
        <div x-show="open" 
             @click.away="open = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="absolute right-0 top-full mt-2 w-44 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
            
            <div class="px-3 py-2 border-b border-gray-200 dark:border-gray-700">
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Site Dili Seçin</p>
            </div>
            
            @forelse($languages as $language)
                <button type="button" 
                        class="w-full flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ $language['code'] === $currentLanguage ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : '' }}"
                        @click="
                            if (this.disabled) return false;
                            this.disabled = true;
                            console.log('Alpine click:', '{{ $language['code'] }}'); 
                            fetch('/language/{{ $language['code'] }}').then(() => {
                                window.location.reload(true);
                            });
                            open = false;
                        "
                        {{ $language['code'] === $currentLanguage ? 'disabled' : '' }}>
                    <span class="mr-2 text-base">{{ $language['flag'] ?? '🌐' }}</span>
                    <span class="flex-1 text-left">{{ $language['name'] }}</span>
                    @if($language['code'] === $currentLanguage)
                        <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    @endif
                </button>
            @empty
                <div class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">
                    <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    Dil bulunamadı
                </div>
            @endforelse
        </div>
        
    @elseif($style === 'buttons')
        <!-- Site Button Group Style -->
        <div class="flex gap-1" role="group">
            @if($languages && count($languages) > 0)
                @foreach($languages as $language)
                    <button type="button" 
                            class="px-2 py-1 text-sm font-medium transition-all duration-200 rounded-lg
                                   {{ $language['code'] === $currentLanguage 
                                      ? 'bg-primary-600 text-white shadow-md dark:bg-primary-500' 
                                      : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700' }}"
                            onclick="
                                if (this.disabled) return false;
                                this.disabled = true;
                                this.style.opacity = '0.5';
                                console.log('Button clicked:', '{{ $language['code'] }}'); 
                                fetch('/language/{{ $language['code'] }}').then(() => {
                                    window.location.reload(true);
                                });
                            "
                            {{ $language['code'] === $currentLanguage ? 'disabled' : '' }}>
                        @if($showFlags)
                            <span class="text-base">{{ $language['flag'] ?? '🌐' }}</span>
                        @endif
                        @if($showText)
                            <span class="ml-1">{{ $language['name'] }}</span>
                        @endif
                        @if($language['code'] === $currentLanguage)
                            <span class="ml-1 text-xs">✓</span>
                        @endif
                    </button>
                @endforeach
            @else
                <span class="text-sm text-gray-500 dark:text-gray-400">Dil bulunamadı</span>
            @endif
        </div>
    @endif
</div>
@endif

<script>
console.log('LanguageSwitcher script loaded');

function handleLanguageSwitch(event, element) {
    console.log('handleLanguageSwitch called', event, element);
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
</script>

@endif {{-- count($languages) > 1 --}}

