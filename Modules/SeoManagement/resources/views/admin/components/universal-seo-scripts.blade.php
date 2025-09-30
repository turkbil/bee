{{--
    UNIVERSAL SEO SCRIPTS
    Tüm modüller için ortak SEO JavaScript kodları
    Pattern: A1 CMS Universal System

    Kullanım:
    @include('seomanagement::admin.components.universal-seo-scripts', [
        'availableLanguages' => $availableLanguages
    ])
--}}

<script>
    // 🎯 UNIVERSAL SEO - Initialize social media switches
    (function() {
        function initializeSocialMediaSwitches() {
            @foreach($availableLanguages as $lang)
                // {{ $lang }} dili için kontrol et
                const ogTitleInput_{{ $lang }} = document.querySelector(`[wire\\:model*="seoDataCache.{{ $lang }}.og_title"]`);
                const ogDescInput_{{ $lang }} = document.querySelector(`[wire\\:model*="seoDataCache.{{ $lang }}.og_description"]`);
                const switchElement_{{ $lang }} = document.getElementById('og_custom_{{ $lang }}');
                const customFields_{{ $lang }} = document.getElementById('og_custom_fields_{{ $lang }}');

                if (ogTitleInput_{{ $lang }} && ogDescInput_{{ $lang }} && switchElement_{{ $lang }} && customFields_{{ $lang }}) {
                    const hasOgTitle = ogTitleInput_{{ $lang }}.value && ogTitleInput_{{ $lang }}.value.trim().length > 0;
                    const hasOgDesc = ogDescInput_{{ $lang }}.value && ogDescInput_{{ $lang }}.value.trim().length > 0;

                    if (hasOgTitle || hasOgDesc) {
                        console.log('🎯 {{ strtoupper($lang) }} dili için sosyal medya alanları dolu, switch aktifleştiriliyor');
                        switchElement_{{ $lang }}.checked = true;
                        customFields_{{ $lang }}.style.display = 'block';

                        // Livewire model'i de güncelle
                        if (window.Livewire) {
                            switchElement_{{ $lang }}.dispatchEvent(new Event('input', { bubbles: true }));
                        }
                    }
                }
            @endforeach
        }

        // DOM ready olduğunda çalıştır
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeSocialMediaSwitches);
        } else {
            initializeSocialMediaSwitches();
        }

        // Global olarak erişilebilir yap
        window.initializeSocialMediaSwitchesForModule = initializeSocialMediaSwitches;
    })();
</script>