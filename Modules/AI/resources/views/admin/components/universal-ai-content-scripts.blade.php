{{--
    UNIVERSAL AI CONTENT SCRIPTS
    Tüm modüller için ortak AI Content JavaScript kodları
    Pattern: A1 CMS Universal System

    Kullanım:
    @include('ai::admin.components.universal-ai-content-scripts')

    Not: Bu component herhangi bir parametre almaz.
    window.currentModelId ve window.currentModuleName zaten her modülde set edilmiş olmalı.
--}}

{{-- AI Content Functions universal JS'den gelir, ek script gerekmez --}}
<script>
    // AI Content System hazır olduğunu logla
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof window.updateTinyMCEContent === 'function' &&
            typeof window.receiveGeneratedContent === 'function') {
            console.log('✅ Universal AI Content System hazır!');
        } else {
            console.warn('⚠️ Universal AI Content System henüz yüklenmedi');
        }
    });
</script>