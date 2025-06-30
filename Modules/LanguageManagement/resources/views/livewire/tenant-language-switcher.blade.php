{{-- Veri Dili Değiştirici (Tenant Languages) --}}
<div class="nav-item dropdown position-relative">
    <a class="nav-link dropdown-toggle p-1" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside" 
       role="button" aria-expanded="false" 
       data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ __('languagemanagement::admin.change_data_language') }}">
        <span class="avatar avatar-sm">
            @if($currentLanguage && $availableLanguages->where('code', $currentLanguage)->first())
                @php $currentLang = $availableLanguages->where('code', $currentLanguage)->first(); @endphp
                @if($currentLang->flag_icon)
                    <span style="font-size: 1.2em;">{{ $currentLang->flag_icon }}</span>
                @else
                    <span class="badge bg-secondary text-white" style="font-size: 0.7em;">{{ strtoupper($currentLanguage) }}</span>
                @endif
            @else
                <span class="badge bg-secondary text-white" style="font-size: 0.7em;">TR</span>
            @endif
        </span>
        <small class="d-none d-lg-inline ms-1 text-muted">{{ __('languagemanagement::admin.data_lang') }}</small>
    </a>
    
    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
        <h6 class="dropdown-header">{{ __('languagemanagement::admin.data_language') }}</h6>
        @foreach($availableLanguages as $language)
            <button type="button" 
                    wire:click="switchLanguage('{{ $language->code }}')"
                    class="dropdown-item d-flex align-items-center {{ $currentLanguage === $language->code ? 'active' : '' }}">
                <span class="me-2">
                    @if($language->flag_icon)
                        {{ $language->flag_icon }}
                    @else
                        <span class="badge bg-secondary text-white" style="font-size: 0.6em;">{{ strtoupper($language->code) }}</span>
                    @endif
                </span>
                <span class="flex-fill">{{ $language->native_name ?? $language->name }}</span>
                @if($currentLanguage === $language->code)
                    <i class="fas fa-check text-green ms-auto"></i>
                @endif
            </button>
        @endforeach
        
        @if($availableLanguages->isEmpty())
            <div class="dropdown-item text-muted">
                <em>{{ __('languagemanagement::admin.no_data_languages') }}</em>
            </div>
        @endif
    </div>
</div>