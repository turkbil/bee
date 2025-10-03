/**
 * UNIVERSAL SEO FUNCTIONS
 * Tüm modüller için ortak SEO JavaScript fonksiyonları
 * Pattern: A1 CMS Universal System
 *
 * Kullanım: Otomatik olarak tüm manage sayfalarında yüklenir
 * Modül bağımsız çalışır
 */

(function() {
    'use strict';

    // 🔒 SINGLETON PATTERN - Sadece bir kere yükle
    if (window.UniversalSeoFunctions) {
        console.log('ℹ️ Universal SEO Functions zaten yüklü - atlanıyor');
        return;
    }

    class UniversalSeoFunctions {
        constructor() {
            console.log('🎯 Universal SEO Functions başlatılıyor...');
            this.initializeOnDomReady();
        }

        /**
         * Karakter sayacını güncelle
         */
        updateCharCounter(input, language, type) {
            const value = input.value || '';
            const maxLength = input.getAttribute('maxlength') || 160;
            const counter = document.getElementById(`${type}_counter_${language}`);

            if (counter) {
                const remaining = maxLength - value.length;
                const small = counter.querySelector('small');
                if (small) {
                    small.textContent = `${value.length}/${maxLength}`;
                    small.style.color = remaining < 10 ? '#dc3545' : (remaining < 30 ? '#fd7e14' : '#6c757d');
                }
            }
        }

        /**
         * SEO öncelik badge güncelle
         */
        updatePriorityDisplay(rangeInput, language) {
            const value = parseInt(rangeInput.value);
            const badge = document.getElementById(`priority_badge_${language}`);

            if (badge) {
                const priorityValue = badge.querySelector('.priority-value');
                const priorityText = badge.querySelector('.priority-text');

                if (priorityValue && priorityText) {
                    priorityValue.textContent = value;

                    let priorityLabel = '';
                    let badgeClass = '';

                    if (value >= 1 && value <= 3) {
                        priorityLabel = 'Düşük';
                        badgeClass = 'bg-info';
                    } else if (value >= 4 && value <= 6) {
                        priorityLabel = 'Orta';
                        badgeClass = 'bg-warning';
                    } else if (value >= 7 && value <= 8) {
                        priorityLabel = 'Yüksek';
                        badgeClass = 'bg-success';
                    } else if (value >= 9 && value <= 10) {
                        priorityLabel = 'Kritik';
                        badgeClass = 'bg-danger';
                    }

                    badge.className = badge.className.replace(/bg-(primary|secondary|success|danger|warning|info|light|dark)/g, '');
                    badge.classList.add(badgeClass);
                    priorityText.textContent = priorityLabel;
                }
            }
        }

        /**
         * OG (Open Graph) özel alanları aç/kapat
         */
        toggleOgCustomFields(checkbox, language) {
            const customFields = document.getElementById(`og_custom_fields_${language}`);
            if (customFields) {
                // jQuery ile smooth animasyon
                if (typeof $ !== 'undefined') {
                    if (checkbox.checked) {
                        $(customFields).slideDown(200);
                    } else {
                        $(customFields).slideUp(200);
                    }
                } else {
                    // jQuery yoksa native JS
                    customFields.style.display = checkbox.checked ? 'block' : 'none';
                }
            }
        }

        /**
         * AI SEO önerisini direkt alana uygula
         */
        applyAlternativeDirectly(fieldTarget, value, element) {
            console.log('🎯 applyAlternativeDirectly:', { fieldTarget, value });

            const currentLang = window.currentLanguage || 'tr';
            let targetSelector = '';

            // Field target'a göre selector belirle
            switch(fieldTarget) {
                case 'seo_title':
                case 'title':
                case 'seoDataCache.tr.seo_title':
                case 'seoDataCache.en.seo_title':
                case 'seoDataCache.ar.seo_title':
                    targetSelector = `[wire\\:model="seoDataCache.${currentLang}.seo_title"]`;
                    break;
                case 'seo_description':
                case 'description':
                case 'seoDataCache.tr.seo_description':
                case 'seoDataCache.en.seo_description':
                case 'seoDataCache.ar.seo_description':
                    targetSelector = `[wire\\:model="seoDataCache.${currentLang}.seo_description"]`;
                    break;
                case 'og_title':
                case 'seoDataCache.tr.og_title':
                case 'seoDataCache.en.og_title':
                case 'seoDataCache.ar.og_title':
                    targetSelector = `[wire\\:model="seoDataCache.${currentLang}.og_title"]`;
                    break;
                case 'og_description':
                case 'seoDataCache.tr.og_description':
                case 'seoDataCache.en.og_description':
                case 'seoDataCache.ar.og_description':
                    targetSelector = `[wire\\:model="seoDataCache.${currentLang}.og_description"]`;
                    break;
            }

            if (targetSelector) {
                const targetField = document.querySelector(targetSelector);
                if (targetField) {
                    targetField.value = value;
                    targetField.dispatchEvent(new Event('input', { bubbles: true }));

                    // Visual feedback
                    if (element) {
                        // Diğer öğeleri passive yap
                        element.parentElement.querySelectorAll('.list-group-item').forEach(item => {
                            item.classList.remove('active');
                        });
                        // Bu öğeyi active yap
                        element.classList.add('active');
                    }

                    console.log('✅ Field updated:', fieldTarget, value);
                } else {
                    console.warn('⚠️ Target field not found:', targetSelector);
                }
            } else {
                console.warn('⚠️ Unknown field target:', fieldTarget);
            }
        }

        /**
         * Dil değişiminde SEO counter'larını başlat
         */
        initializeSeoCounters(language) {
            // Title counter
            const titleInput = document.querySelector(`[wire\\:model="seoDataCache.${language}.seo_title"]`);
            if (titleInput) {
                this.updateCharCounter(titleInput, language, 'title');
            }

            // Description counter
            const descInput = document.querySelector(`[wire\\:model="seoDataCache.${language}.seo_description"]`);
            if (descInput) {
                this.updateCharCounter(descInput, language, 'description');
            }

            // OG Title counter
            const ogTitleInput = document.querySelector(`[wire\\:model="seoDataCache.${language}.og_title"]`);
            if (ogTitleInput) {
                this.updateCharCounter(ogTitleInput, language, 'og_title');
            }

            // OG Description counter
            const ogDescInput = document.querySelector(`[wire\\:model="seoDataCache.${language}.og_description"]`);
            if (ogDescInput) {
                this.updateCharCounter(ogDescInput, language, 'og_description');
            }
        }

        /**
         * Sosyal medya switch'lerini başlat (içerik varsa otomatik aç)
         */
        initializeSocialMediaSwitches() {
            // Bu fonksiyon Blade template'den doldurulan diller için çalışır
            // Şimdilik boş, Blade tarafında handle edilecek
            console.log('📱 Social media switches initialized');
        }

        /**
         * OG Custom alanlarını toggle et
         */
        toggleOgCustomFields(checkbox, language) {
            const fieldsContainer = document.getElementById(`og_custom_fields_${language}`);
            const ogTitleInput = document.querySelector(`[wire\\:model="seoDataCache.${language}.og_title"]`);
            const ogDescInput = document.querySelector(`[wire\\:model="seoDataCache.${language}.og_description"]`);

            if (fieldsContainer) {
                if (checkbox.checked) {
                    // jQuery slideDown veya native show
                    if (typeof $ !== 'undefined') {
                        $(fieldsContainer).slideDown(300);
                    } else {
                        fieldsContainer.style.display = 'block';
                    }
                    console.log('✅ Social media fields açıldı:', language);
                } else {
                    // jQuery slideUp veya native hide
                    if (typeof $ !== 'undefined') {
                        $(fieldsContainer).slideUp(300);
                    } else {
                        fieldsContainer.style.display = 'none';
                    }
                    // Checkbox kapalıysa alanları temizle
                    if (ogTitleInput) {
                        ogTitleInput.value = '';
                        ogTitleInput.dispatchEvent(new Event('input'));
                    }
                    if (ogDescInput) {
                        ogDescInput.value = '';
                        ogDescInput.dispatchEvent(new Event('input'));
                    }
                    console.log('❌ Social media fields kapatıldı ve temizlendi:', language);
                }
            }
        }

        /**
         * OG alanları doluysa checkbox'ı otomatik işaretle
         */
        checkAndEnableSocialMedia(language) {
            // Önce checkbox'ın var olduğundan emin ol
            let checkbox = document.getElementById(`og_custom_${language}`);

            // Checkbox yoksa oluştur
            if (!checkbox) {
                this.recreateSocialMediaSwitch(language);
                checkbox = document.getElementById(`og_custom_${language}`);
            }

            const ogTitleInput = document.querySelector(`[wire\\:model="seoDataCache.${language}.og_title"]`);
            const ogDescInput = document.querySelector(`[wire\\:model="seoDataCache.${language}.og_description"]`);
            const fieldsContainer = document.getElementById(`og_custom_fields_${language}`);

            if ((ogTitleInput && ogTitleInput.value) || (ogDescInput && ogDescInput.value)) {
                if (checkbox && !checkbox.checked) {
                    checkbox.checked = true;
                    if (fieldsContainer) {
                        fieldsContainer.style.display = 'block';
                    }
                    console.log('✅ Social media checkbox otomatik işaretlendi (içerik var):', language);
                }
            }
        }

        /**
         * Social media switch'i yeniden oluştur (DOM güncellemelerinden sonra)
         */
        recreateSocialMediaSwitch(language) {
            // Switch container'ı bul
            const socialMediaCard = document.querySelector(`#og_custom_fields_${language}`)?.closest('.card-body');
            if (!socialMediaCard) return;

            // Eski boş div'i bul
            const emptyDiv = socialMediaCard.querySelector('.col-md-6.mb-3:first-child');
            if (!emptyDiv || emptyDiv.innerHTML.trim() !== '') return;

            // Yeni switch HTML'i oluştur
            const switchHTML = `
                <div class="mt-3">
                    <div class="form-check form-switch">
                        <input type="checkbox"
                            class="form-check-input"
                            wire:model.live="seoDataCache.${language}.og_custom_enabled"
                            id="og_custom_${language}"
                            onchange="toggleOgCustomFields(this, '${language}')">
                        <label class="form-check-label" for="og_custom_${language}">
                            Özel sosyal medya ayarlarını kullan
                        </label>
                    </div>
                    <div class="form-text">
                        <small class="text-muted">Kapalıysa yukarıdaki SEO bilgileri kullanılır</small>
                    </div>
                </div>
            `;

            emptyDiv.innerHTML = switchHTML;
            console.log('✅ Social media switch yeniden oluşturuldu:', language);
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
                // Dil değişimi event'i
                if (typeof Livewire !== 'undefined') {
                    Livewire.on('language-changed', (language) => {
                        console.log('🌍 Dil değişimi algılandı (SEO):', language);
                        setTimeout(() => {
                            this.initializeSeoCounters(language);
                        }, 100);
                    });

                    Livewire.on('seo-counters-refresh', (language) => {
                        this.initializeSeoCounters(language);
                    });
                }
            });

            console.log('✅ Universal SEO event listeners kuruldu');
        }
    }

    // Global instance oluştur
    window.UniversalSeoFunctions = new UniversalSeoFunctions();

    // Global fonksiyonları expose et (eski kodlarla uyumluluk için)
    window.updateCharCounter = (input, language, type) => {
        window.UniversalSeoFunctions.updateCharCounter(input, language, type);
    };

    window.updatePriorityDisplay = (rangeInput, language) => {
        window.UniversalSeoFunctions.updatePriorityDisplay(rangeInput, language);
    };

    window.toggleOgCustomFields = (checkbox, language) => {
        window.UniversalSeoFunctions.toggleOgCustomFields(checkbox, language);
    };

    window.applyAlternativeDirectly = (fieldTarget, value, element) => {
        window.UniversalSeoFunctions.applyAlternativeDirectly(fieldTarget, value, element);
    };

    window.initializeSeoCounters = (language) => {
        window.UniversalSeoFunctions.initializeSeoCounters(language);
    };

    window.initializeSocialMediaSwitches = () => {
        window.UniversalSeoFunctions.initializeSocialMediaSwitches();
    };

    window.checkAndEnableSocialMedia = (language) => {
        window.UniversalSeoFunctions.checkAndEnableSocialMedia(language);
    };

    console.log('✅ Universal SEO Functions yüklendi!');

})();