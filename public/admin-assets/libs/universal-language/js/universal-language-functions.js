/**
 * UNIVERSAL LANGUAGE FUNCTIONS
 * Tüm modüller için ortak Dil Yönetimi JavaScript fonksiyonları
 * Pattern: A1 CMS Universal System
 *
 * Kullanım: Otomatik olarak tüm manage sayfalarında yüklenir
 * Modül bağımsız çalışır
 */

(function() {
    'use strict';

    // 🔒 SINGLETON PATTERN - Sadece bir kere yükle
    if (window.UniversalLanguageFunctions) {
        console.log('ℹ️ Universal Language Functions zaten yüklü - atlanıyor');
        return;
    }

    class UniversalLanguageFunctions {
        constructor() {
            console.log('🎯 Universal Language Functions başlatılıyor...');
            this.currentLanguage = window.currentLanguage || 'tr';
            this.initializeOnDomReady();
        }

        /**
         * Dil içeriğini göster/gizle
         */
        switchLanguageContent(language) {
            console.log('🌍 Dil içeriği değiştiriliyor:', language);

            // Tüm language content'leri gizle
            document.querySelectorAll('.language-content').forEach(content => {
                content.style.display = 'none';
            });

            document.querySelectorAll('.seo-language-content').forEach(content => {
                content.style.display = 'none';
            });

            // Seçili dil content'ini göster
            const targetContent = document.querySelector(`.language-content[data-language="${language}"]`);
            if (targetContent) {
                targetContent.style.display = 'block';
            }

            const targetSeoContent = document.querySelector(`.seo-language-content[data-language="${language}"]`);
            if (targetSeoContent) {
                targetSeoContent.style.display = 'block';
            }

            // Global language değişkenini güncelle
            window.currentLanguage = language;

            // Livewire event'i tetikle
            if (typeof Livewire !== 'undefined') {
                try {
                    Livewire.dispatch('language-changed', language);
                } catch (e) {
                    console.warn('⚠️ Livewire language-changed event gönderilemedi:', e);
                }
            }

            console.log('✅ Dil içeriği değiştirildi:', language);
        }

        /**
         * Sayfa yüklendiğinde doğru dil içeriğini göster
         */
        initializeLanguageContent(initialLanguage) {
            const language = initialLanguage || this.currentLanguage;

            console.log('🔧 İlk yükleme: Dil içeriği düzenleniyor:', language);

            this.switchLanguageContent(language);

            // SEO counters'ı başlat (Universal SEO Functions varsa)
            if (typeof window.initializeSeoCounters === 'function') {
                setTimeout(() => {
                    window.initializeSeoCounters(language);
                }, 100);
            }

            // Social media switches'i başlat
            if (typeof window.initializeSocialMediaSwitches === 'function') {
                setTimeout(() => {
                    window.initializeSocialMediaSwitches();
                }, 100);
            }
        }

        /**
         * DOM ready olduğunda otomatik başlat
         */
        initializeOnDomReady() {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => this.setupEventListeners());
            } else {
                this.setupEventListeners();
            }
        }

        /**
         * Event listener'ları kur
         */
        setupEventListeners() {
            // Livewire initialized event
            document.addEventListener('livewire:initialized', () => {
                if (typeof Livewire !== 'undefined') {
                    // Refresh component event
                    Livewire.on('refreshComponent', (data) => {
                        if (!data || !data.source || data.source !== 'seo-analysis') {
                            console.log('🔄 Çeviri tamamlandı - component yenileniyor...', data);

                            // Component'i yenile (modül adı dinamik olabilir)
                            try {
                                const components = Livewire.components.all();
                                if (components.length > 0) {
                                    components[0].$refresh();
                                }
                            } catch (e) {
                                console.warn('⚠️ Component refresh failed:', e);
                            }
                        }
                    });
                }
            });

            console.log('✅ Universal Language event listeners kuruldu');
        }
    }

    // Global instance oluştur
    window.UniversalLanguageFunctions = new UniversalLanguageFunctions();

    // Global fonksiyonları expose et (eski kodlarla uyumluluk için)
    window.switchLanguageContent = (language) => {
        window.UniversalLanguageFunctions.switchLanguageContent(language);
    };

    window.initializeLanguageContent = (initialLanguage) => {
        window.UniversalLanguageFunctions.initializeLanguageContent(initialLanguage);
    };

    console.log('✅ Universal Language Functions yüklendi!');

})();