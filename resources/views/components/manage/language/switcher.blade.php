@php
    use Modules\LanguageManagement\app\Models\TenantLanguage;
    // İsteğe göre: Sadece aktif VE visible dilleri göster (önce aktif+visible, sonra sıralama)
    $tenantLanguages = TenantLanguage::where('is_active', true)
        ->where('is_visible', true)
        ->orderBy('sort_order')
        ->get();
    $currentLangName = $tenantLanguages->where('code', $currentLanguage)->first()?->native_name ?? strtoupper($currentLanguage);
    $languageCount = $tenantLanguages->count();
    
    // Görünüm logikası:
    // 1 dil: Hiçbiri gösterilmez
    // 2-3 dil: Tab sistemi gösterilir 
    // 4+ dil: Dropdown sistemi gösterilir
@endphp

@if ($languageCount > 1)
<li class="nav-item {{ $position ?? 'ms-auto' }}">
    <div class="d-flex align-items-center gap-2">
        @if ($languageCount >= 2 && $languageCount <= 3)
            <!-- Tab Sistemi (2-3 dil için) -->
            <div class="language-animation-container">
                <!-- Dil Butonları (Sadece aktif VE visible olanlar) -->
                <div class="language-buttons" id="languageButtons">
                    @foreach ($tenantLanguages as $lang)
                        <button class="btn btn-link p-2 language-switch-btn {{ $currentLanguage === $lang->code ? 'text-primary' : 'text-muted' }}"
                                style="border: none; border-radius: 0; {{ $currentLanguage === $lang->code ? 'border-bottom: 2px solid var(--primary-color) !important;' : 'border-bottom: 2px solid transparent;' }}"
                                data-language="{{ $lang->code }}"
                                data-native-name="{{ $lang->native_name }}"
                                onclick="window.clientSideLanguageSwitch('{{ $lang->code }}')"
                                {{ $currentLanguage === $lang->code ? 'disabled' : '' }}>
                            {{ strtoupper($lang->code) }}
                        </button>
                    @endforeach
                </div>

                <!-- Language Badge (Normal durumda gözükür) -->
                <div class="language-badge" id="languageBadge">
                    <div class="nav-link bg-primary text-white rounded header-btn-uniform">
                        <i class="fas fa-language me-2"></i>{{ $currentLangName }}
                        <i class="fas fa-chevron-down ms-2"></i>
                    </div>
                </div>
            </div>
        @endif

        @if ($languageCount >= 4)
            <!-- Dropdown Sistemi (4+ dil için) -->
            <div class="nav-item dropdown position-static">
                <a href="#" class="nav-link bg-primary text-white rounded header-btn-uniform d-flex align-items-center" 
                   data-bs-toggle="dropdown" aria-label="Open language menu" 
                   style="--tblr-primary: #066fd1 !important; --tblr-primary-rgb: 6, 111, 209 !important; border-radius: 0.25rem !important; padding: 0.5rem 0.75rem;">
                    <i class="fas fa-language me-2"></i>
                    <span id="dropdown-current-lang">{{ $currentLangName }}</span>
                    <i class="fas fa-chevron-down ms-2"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow position-absolute" 
                     style="z-index: 99999 !important; position: fixed !important; top: auto !important; left: auto !important; right: 20px !important; transform: none !important;">
                    <style>
                        /* Tabler.io standart dropdown renkleri */
                        .dropdown-item.language-switch-btn.active {
                            background-color: var(--tblr-active-bg, #e9ecef) !important;
                            color: var(--tblr-body-color, #1a1a1a) !important;
                            border-bottom: none !important;
                        }
                        .dropdown-item.language-switch-btn.active .fas.fa-check {
                            color: var(--tblr-body-color, #1a1a1a) !important;
                        }
                        .dropdown-item.language-switch-btn:not(.active):hover {
                            background-color: var(--tblr-active-bg, #e9ecef) !important;
                            color: var(--tblr-body-color, #1a1a1a) !important;
                            border-bottom: none !important;
                        }
                        .dropdown-item.language-switch-btn:not(.active):hover .flag {
                            filter: brightness(1.1);
                        }
                    </style>
                    @foreach ($tenantLanguages as $lang)
                        <a href="#" class="dropdown-item language-switch-btn {{ $currentLanguage === $lang->code ? 'active' : '' }} d-flex align-items-center justify-content-between"
                           data-language="{{ $lang->code }}"
                           data-native-name="{{ $lang->native_name }}"
                           onclick="window.clientSideLanguageSwitch('{{ $lang->code }}'); return false;"
                           {{ $currentLanguage === $lang->code ? 'disabled' : '' }}
                           style="{{ $currentLanguage === $lang->code ? 'background-color: var(--tblr-active-bg, #e9ecef) !important; color: var(--tblr-body-color, #1a1a1a) !important;' : '' }}">
                            <div class="d-flex align-items-center">
                                <span class="flag flag-{{ $lang->code === 'en' ? 'us' : $lang->code }} me-2"></span>
                                {{ $lang->native_name }}
                            </div>
                            @if ($currentLanguage === $lang->code)
                                <i class="fas fa-check ms-2" style="color: var(--tblr-body-color, #1a1a1a);"></i>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</li>
@endif