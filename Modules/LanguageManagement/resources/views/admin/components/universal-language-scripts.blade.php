{{--
    UNIVERSAL LANGUAGE SCRIPTS
    Tüm modüller için ortak Dil Yönetimi JavaScript kodları
    Pattern: A1 CMS Universal System

    Kullanım:
    @include('languagemanagement::admin.components.universal-language-scripts', [
        'currentLanguage' => $currentLanguage,
        'availableLanguages' => $availableLanguages
    ])
--}}

<script>
    // 🎯 UNIVERSAL LANGUAGE - Page initialization
    document.addEventListener('DOMContentLoaded', function() {
        console.log('📋 Universal Language System yüklendi');
        console.log('🌍 Current Language:', window.currentLanguage);
        console.log('🌍 Livewire Current Language:', '{{ $currentLanguage }}');
        console.log('🔍 Available Languages:', @json($availableLanguages));

        // 🚨 KRİTİK FİX: Sayfa yüklendiğinde doğru dil content'ini göster
        const livewireLanguage = '{{ $currentLanguage }}';
        if (livewireLanguage && typeof window.initializeLanguageContent === 'function') {
            window.initializeLanguageContent(livewireLanguage);
        }
    });
</script>