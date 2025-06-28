<div class="dropdown">
    <a href="#" class="nav-link" data-bs-toggle="dropdown" aria-label="Open language menu">
        <i class="ti ti-language me-2"></i>
        @if($currentLanguage)
            {{ $currentLanguage->native_name }}
        @else
            Türkçe
        @endif
    </a>
    <div class="dropdown-menu">
        @if($systemLanguages && $systemLanguages->count() > 0)
            @foreach($systemLanguages as $language)
                <button wire:click="switchLanguage('{{ $language->code }}')" 
                        class="dropdown-item {{ $currentLocale === $language->code ? 'active' : '' }}"
                        @if($isLoading) disabled @endif>
                    @if($language->code === 'tr')
                        🇹🇷
                    @elseif($language->code === 'en')
                        🇺🇸
                    @elseif($language->code === 'ar')
                        🇸🇦
                    @endif
                    {{ $language->native_name }}
                    @if($currentLocale === $language->code)
                        <i class="ti ti-check ms-auto"></i>
                    @endif
                </button>
            @endforeach
        @else
            <span class="dropdown-item-text text-muted">
                {{ __('admin.no_language_found') }}
            </span>
        @endif
        
        @if($isLoading)
            <div class="dropdown-item text-center">
                <div class="spinner-border spinner-border-sm" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        @endif
    </div>
</div>