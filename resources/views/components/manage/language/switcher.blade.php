@php
    use Modules\LanguageManagement\app\Models\TenantLanguage;
    // İsteğe göre: Sadece aktif VE visible dilleri göster (önce aktif+visible, sonra sıralama)
    $tenantLanguages = TenantLanguage::where('is_active', true)
        ->where('is_visible', true)
        ->orderBy('sort_order')
        ->get();
    $currentLangName = $tenantLanguages->where('code', $currentLanguage)->first()?->native_name ?? strtoupper($currentLanguage);
@endphp

<li class="nav-item {{ $position ?? 'ms-auto' }}">
    <div class="language-animation-container">
        <!-- Dil Butonları (Sadece aktif VE visible olanlar) -->
        <div class="language-buttons" id="languageButtons">
            @foreach ($tenantLanguages as $lang)
                <button class="btn btn-link p-2 language-switch-btn {{ $currentLanguage === $lang->code ? 'text-primary' : 'text-muted' }}"
                        style="border: none; border-radius: 0; {{ $currentLanguage === $lang->code ? 'border-bottom: 2px solid var(--primary-color) !important;' : 'border-bottom: 2px solid transparent;' }}"
                        data-language="{{ $lang->code }}" 
                        data-native-name="{{ $lang->native_name }}">
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
</li>