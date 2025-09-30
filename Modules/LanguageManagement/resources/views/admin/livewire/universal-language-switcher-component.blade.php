{{--
    UNIVERSAL LANGUAGE SWITCHER COMPONENT VIEW
    Pattern: A1 CMS Universal System

    Bu component sadece state management yapar, UI render etmez
    UI için mevcut language switcher component'ini kullanır
--}}

<div>
    {{-- Hidden state keeper - JavaScript senkronizasyonu için --}}
    <input type="hidden" id="livewire-current-language" value="{{ $currentLanguage }}">

    {{-- JavaScript variables --}}
    @push('scripts')
    <script>
        // Universal Language Switcher State
        window.livewireCurrentLanguage = '{{ $currentLanguage }}';
        window.availableLanguages = @json($availableLanguages);
        window.languageStorageKey = '{{ $storageKey ?? 'manage_language' }}';

        console.log('🌍 Universal Language Switcher initialized', {
            currentLanguage: window.livewireCurrentLanguage,
            availableLanguages: window.availableLanguages,
            storageKey: window.languageStorageKey
        });
    </script>
    @endpush
</div>