/**
 * AI SEO Integration System
 * Real AI-powered SEO functionality
 */


(function() {
    'use strict';

    console.log('🔥 AI-SEO-INTEGRATION.JS YÜKLENDI!');
    console.log('🚀 AI SEO INTEGRATION SYSTEM LOADING...');
    console.log('📍 File Path: assets/js/ai-seo-integration.js');
    console.log('⏰ Load Time:', new Date().toISOString());

    // Global debug check
    window.aiSeoDebug = function() {
        console.log('🧪 AI SEO Integration System Status');
        console.log('✅ JavaScript file loaded successfully');
        console.log('🔍 Button count:', document.querySelectorAll('.ai-seo-comprehensive-btn, .ai-seo-recommendations-btn').length);
        return 'AI SEO Integration is active';
    };

    console.log('🎯 Global debug function window.aiSeoDebug() ready!');

    // Livewire DOM error handling
    window.addEventListener('error', (event) => {
        if (event.message && event.message.includes('before')) {
            console.warn('🔧 AI-SEO Livewire DOM error suppressed:', event.message);
            event.preventDefault();
            return false;
        }
    });

    window.addEventListener('unhandledrejection', (event) => {
        if (event.reason && event.reason.toString().includes('before')) {
            console.warn('🔧 AI-SEO Livewire DOM promise rejection suppressed:', event.reason);
            event.preventDefault();
            return false;
        }
    });

    // ===== OVERLAY HELPER FUNCTIONS =====
    function getOverlayTitle(cleanupTarget) {
        switch (cleanupTarget) {
            case 'ai-recommendations':
                return 'AI SEO Önerileri';
            default:
                return 'AI SEO İşlemleri';
        }
    }

    function getOverlaySubtitle(cleanupTarget) {
        switch (cleanupTarget) {
            case 'ai-recommendations':
                return 'Kişiselleştirilmiş SEO önerileri yapay zeka tarafından oluşturuluyor';
            default:
                return 'AI ile SEO işlemleriniz gerçekleştiriliyor';
        }
    }

    // ===== INLINE OVERLAY SİSTEMİ REMOVED - YENİ showInlineLoadingOverlay KULLANILIYOR =====
    /*function showInlineLoading(containerId, message = 'Yükleniyor...', cleanupTarget = 'seo-data') {
        // Container'ı bul veya oluştur
        let container = document.getElementById(containerId);

        if (!container) {
            // Container yoksa oluştur - TOOLBAR'IN HEMEN ALTINA
            const toolbar = document.querySelector('.ai-seo-toolbar');
            const targetLocation = toolbar ? toolbar.parentElement : (
                document.querySelector('.seo-language-content[style*="display: block"]') ||
                document.querySelector('.tab-pane.active .card-body') ||
                document.querySelector('.tab-pane.active')
            );

            if (targetLocation) {
                container = document.createElement('div');
                container.id = containerId;
                container.className = 'inline-results-container mt-4';
                container.style.position = 'relative'; // Overlay için
                container.style.minHeight = '300px'; // Minimum yükseklik

                // TOOLBAR'IN HEMEN ALTINA EKLE
                if (toolbar) {
                    toolbar.insertAdjacentElement('afterend', container);
                    console.log('✅ Container toolbar altına eklendi:', containerId);
                } else {
                    targetLocation.appendChild(container);
                    console.log('✅ Container fallback location\'a eklendi:', containerId);
                }
            } else {
                console.warn('⚠️ Target location bulunamadı');
                return;
            }
        }

        // Container'ı hazırla
        container.style.position = 'relative';
        container.style.minHeight = '300px';

        // ÖNCE: Belirtilen tipte SEO alanlarını temizle
        cleanupExistingSeoAreas(cleanupTarget);

        // Önce içerik alanını oluştur (overlay altında kalacak) - Mevcut tasarım placeholder
        const contentArea = document.createElement('div');
        contentArea.className = 'inline-content-area';
        contentArea.innerHTML = `
            <div class="mt-4">
                <div class="row">
                    <div class="col-12">
                        <div class="bg-light border p-3 rounded-3 mb-3 position-relative" style="border-radius: 0.25rem !important;">
                            <h3 class="mb-0">
                                <i class="fas fa-chart-line me-2"></i>
                                SEO Analiz Raporu
                            </h3>
                            <small class="position-absolute text-muted" style="right: 1rem; top: 50%; transform: translateY(-50%);">
                                Hazırlanıyor...
                            </small>
                        </div>
                    </div>
                </div>
                <div class="text-center text-muted py-5">
                    <i class="fas fa-robot fa-3x mb-3 opacity-25"></i>
                    <h5 class="text-muted">AI Analiz Sonuçları</h5>
                    <p>Analiz tamamlandığında detaylı rapor burada görünecek...</p>
                </div>
            </div>
        `;

        // AI MODAL TARZI INLINE OVERLAY
        const overlay = document.createElement('div');
        overlay.className = 'inline-loading-overlay';
        overlay.innerHTML = `
            <div class="overlay-content">
                <!-- AI İşlem Başlık -->
                <div class="mb-4 text-center">
                    <h4 class="fw-normal mb-2">🤖 ${getOverlayTitle(cleanupTarget)}</h4>
                    <p class="text-muted mb-0">${getOverlaySubtitle(cleanupTarget)}</p>
                </div>

                <!-- Progress Bar - Modal ile aynı -->
                <div class="mb-3">
                    <div class="progress mb-2" style="height: 6px;">
                        <div class="progress-bar progress-bar-indeterminate bg-primary progress-bar-animated" style="width: 100%"></div>
                    </div>
                    <small class="d-block text-center text-muted">${message}</small>
                </div>

                <!-- İşleme Durumu -->
                <div class="d-flex align-items-center justify-content-center">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <span class="text-muted">Lütfen bekleyin...</span>
                </div>

                <!-- Bilgi Mesajı -->
                <div class="alert alert-info py-2 mt-3" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle me-2"></i>
                        <small><strong>Bilgi:</strong> Analiz tamamlandığında sonuçlar bu alanda görüntülenecektir.</small>
                    </div>
                </div>
            </div>
        `;

        // CSS stilleri (bir kez ekle)
        if (!document.getElementById('inline-overlay-styles')) {
            const style = document.createElement('style');
            style.id = 'inline-overlay-styles';
            style.textContent = `
                .inline-loading-overlay {
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(255, 255, 255, 0.98);
                    backdrop-filter: blur(6px);
                    z-index: 1000;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    min-height: 400px;
                    border-radius: 8px;
                }

                .overlay-content {
                    background: white;
                    padding: 2rem;
                    border-radius: 0.5rem;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
                    border: 1px solid rgba(0, 0, 0, 0.05);
                    max-width: 500px;
                    width: 90%;
                }

                .overlay-content .progress {
                    background-color: rgba(32, 107, 196, 0.1);
                }

                .overlay-content .progress-bar-indeterminate {
                    background: linear-gradient(45deg,
                        rgba(32, 107, 196, 0.5) 25%,
                        transparent 25%,
                        transparent 50%,
                        rgba(32, 107, 196, 0.5) 50%,
                        rgba(32, 107, 196, 0.5) 75%,
                        transparent 75%
                    );
                    background-size: 20px 20px;
                    animation: progress-indeterminate 1s linear infinite;
                }

                @keyframes progress-indeterminate {
                    0% { background-position: 0 0; }
                    100% { background-position: 20px 0; }
                }

                .inline-results-container {
                    transition: all 0.3s ease;
                }

                .overlay-content .spinner-border {
                    width: 2.5rem;
                    height: 2.5rem;
                }

                .overlay-content h6 {
                    color: #1e293b;
                    font-weight: 600;
                }

                .overlay-content p {
                    color: #64748b;
                    font-size: 14px;
                }
            `;
            document.head.appendChild(style);
        }

        // Container'ı temizle ve yeniden oluştur
        container.innerHTML = '';
        container.appendChild(contentArea);
        container.appendChild(overlay);

        container.style.display = 'block';
        container.classList.remove('d-none');

        // Smooth scroll
        setTimeout(() => {
            container.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }, 100);

        console.log('✅ Inline overlay gösterildi:', message);
    }*/

    function cleanupExistingSeoAreas(targetType = 'all') {
        console.log('🧹 SEO alanları temizleniyor - Hedef: ' + targetType);

        // 1. Target type'a göre sadece ilgili container'ı temizle (PARALEL ÇALIŞMA İÇİN)
        if (targetType === 'all' || targetType === 'analysis') {
            const analysisContainer = document.getElementById('seoUniversalResults');
            if (analysisContainer) {
                analysisContainer.innerHTML = '';
                console.log('🧹 seoUniversalResults içeriği temizlendi (container korundu)');
            }
        }

        if (targetType === 'all' || targetType === 'ai-recommendations' || targetType === 'recommendations') {
            const recommendationsContainer = document.getElementById('aiRecommendationsContainer');
            if (recommendationsContainer) {
                recommendationsContainer.innerHTML = '';
                console.log('🧹 aiRecommendationsContainer içeriği temizlendi (container korundu)');
            }
        }

        // 2. Hedef tipine göre spesifik temizlik
        if (targetType === 'all') {
            // SADECE ANALİZ SONUÇLARINI temizle, FORM ALANLARI KORUNACAK

            // 1. SEO Analiz Raporu başlık alanları
            const seoReportHeaders = document.querySelectorAll('h3');
            seoReportHeaders.forEach(header => {
                if (header.textContent.trim().includes('SEO Analiz Raporu')) {
                    let parentContainer = header.closest('.bg-light.border.p-3.rounded-3.mb-3') ||
                                        header.closest('.mt-4') ||
                                        header.closest('div[class*="bg-light"]') ||
                                        header.parentElement?.parentElement;

                    if (parentContainer) {
                        console.log('🗑️ SEO Data container kaldırılıyor');
                        parentContainer.remove();
                    }
                }
            });

            // 2. SEO SKORLARINI temizle - ama form alanlarına dokunma
            const scoreSections = document.querySelectorAll('.row.mb-4');
            scoreSections.forEach(section => {
                const text = section.textContent || '';
                // SADECE skor gösteren alanları temizle, form içermeyenleri
                if ((text.includes('Genel SEO Skoru') ||
                     text.includes('/100') ||
                     text.includes('İyi') ||
                     text.includes('Orta') ||
                     text.includes('Kötü')) &&
                    !section.querySelector('input') &&
                    !section.querySelector('select') &&
                    !section.querySelector('textarea') &&
                    !section.querySelector('button[type="button"]') &&
                    !section.querySelector('.form-floating') &&
                    !section.querySelector('.form-control')) {
                    console.log('🗑️ SEO skor alanı kaldırılıyor (form korunuyor)');
                    section.remove();
                }
            });

            // 3. ANALİZ ACCORDION'LARINI temizle - SADECE analiz olanları
            const analysisAccordionIds = ['realTimeSeoAccordion', 'metaAnalysis', 'contentQualityAnalysis', 'socialMediaAnalysis', 'actionPlan'];
            analysisAccordionIds.forEach(accordionId => {
                const accordionElement = document.getElementById(accordionId);
                if (accordionElement) {
                    // Accordion'un parent container'ını bul ama FORM ALANLARINI KONTROL ET
                    const accordionParent = accordionElement.closest('.mt-4');
                    if (accordionParent &&
                        !accordionParent.querySelector('input') &&
                        !accordionParent.querySelector('textarea') &&
                        !accordionParent.querySelector('select') &&
                        !accordionParent.querySelector('.form-floating')) {
                        console.log('🗑️ Analiz Accordion kaldırılıyor (form korunuyor):', accordionId);
                        accordionParent.remove();
                    } else if (accordionElement && !accordionElement.querySelector('.form-control')) {
                        // Sadece accordion'u sil, parent'ını değil
                        console.log('🗑️ Sadece accordion kaldırılıyor:', accordionId);
                        accordionElement.remove();
                    }
                }
            });
        }

        if (targetType === 'all' || targetType === 'ai-recommendations') {
            // AI Öneriler alanlarını temizle
            const aiHeaders = document.querySelectorAll('h3, h4, h5');
            aiHeaders.forEach(header => {
                const text = header.textContent.trim();
                if (text.includes('AI SEO İçerik Önerileri') ||
                    text.includes('AI SEO Önerileri') ||
                    text.includes('Öncelikli Eylem Planı')) {

                    let parentToRemove = header.closest('.mt-4') ||
                                       header.closest('.bg-light') ||
                                       header.closest('.card') ||
                                       header.closest('[class*="accordion"]') ||
                                       header.parentElement;

                    if (parentToRemove) {
                        console.log('🗑️ AI Öneriler container kaldırılıyor:', text);
                        parentToRemove.remove();
                    }
                }
            });

            // AI Accordion'ları temizle
            const aiAccordionIds = ['seoSuggestionsAccordion', 'aiSeoAccordion'];
            aiAccordionIds.forEach(accordionId => {
                const accordion = document.getElementById(accordionId);
                if (accordion) {
                    const accordionParent = accordion.closest('.mt-4') ||
                                          accordion.closest('.bg-light') ||
                                          accordion.parentElement;
                    if (accordionParent) {
                        console.log('🗑️ AI Accordion kaldırılıyor:', accordionId);
                        accordionParent.remove();
                    }
                }
            });
        }

        if (targetType === 'all') {
            // Tüm kalan SEO accordion'ları
            const allAccordionIds = ['realTimeSeoAccordion', 'seoGenerateAccordion'];
            allAccordionIds.forEach(accordionId => {
                const accordion = document.getElementById(accordionId);
                if (accordion) {
                    const accordionParent = accordion.closest('.mt-4') || accordion.parentElement;
                    if (accordionParent) {
                        console.log('🗑️ Genel Accordion kaldırılıyor:', accordionId);
                        accordionParent.remove();
                    }
                }
            });

            // Fallback temizlik
            const potentialContainers = document.querySelectorAll('.mt-4, .inline-results-container');
            potentialContainers.forEach(container => {
                const text = container.textContent || '';
                if (text.includes('SEO Analiz') || text.includes('AI SEO') || text.includes('Öncelikli Eylem')) {
                    console.log('🗑️ Fallback temizlik');
                    container.remove();
                }
            });
        }

        console.log('✅ ' + targetType + ' temizliği tamamlandı');
    }


    function showInlineError(message) {
        const container = document.getElementById('seoUniversalResults');
        if (!container) {
            console.warn('⚠️ Container bulunamadı, error gösterilemiyor');
            return;
        }

        // Loading'i gizle
        hideInlineLoading('seoUniversalResults');

        // Error HTML'i
        const errorHTML = `
            <div class="mt-4">
                <div class="alert alert-danger" role="alert">
                    <h4 class="alert-heading">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Hata Oluştu
                    </h4>
                    <p>${message}</p>
                    <hr>
                    <p class="mb-0">
                        <button class="btn btn-outline-danger btn-sm" onclick="location.reload()">
                            <i class="fas fa-sync-alt me-1"></i>
                            Sayfayı Yenile
                        </button>
                    </p>
                </div>
            </div>
        `;

        container.innerHTML = errorHTML;
        console.log('❌ Inline error gösterildi:', message);
    }

    // Buton durumu yönetimi
    function setButtonLoading(button, isLoading = true, loadingText = 'Yükleniyor...') {
        if (isLoading) {
            console.log('🔍 setButtonLoading DEBUG:', {
                currentHTML: button.innerHTML,
                currentText: button.textContent.trim(),
                loadingText: loadingText,
                isLoading: isLoading
            });

            button.dataset.originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = `
                <div class="spinner-border spinner-border-sm me-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                ${loadingText}
            `;
            button.classList.add('btn-loading');
        } else {
            console.log('🔍 setButtonLoading RESET DEBUG:', {
                storedOriginalText: button.dataset.originalText,
                currentHTML: button.innerHTML,
                willResetTo: button.dataset.originalText || button.innerHTML
            });

            button.disabled = false;

            // DEBUG: Neden "true" yazdığını anlayalım
            if (button.dataset.originalText) {
                console.log('✅ Using stored originalText:', button.dataset.originalText);
                button.innerHTML = button.dataset.originalText;
            } else {
                console.warn('⚠️ No originalText found, keeping current HTML');
                // Fallback: Butonun class'ından orijinal text'i tahmin et
                if (button.classList.contains('ai-seo-comprehensive-btn')) {
                    button.innerHTML = `<i class="fas fa-chart-bar me-1"></i>Verileri Yenile`;
                } else if (button.classList.contains('ai-seo-recommendations-btn')) {
                    button.innerHTML = `<i class="fas fa-lightbulb me-1"></i>AI Önerileri`;
                }
            }

            button.classList.remove('btn-loading');
        }
    }
    
    // CSRF token for API calls
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // Dinamik dil sistemi - tenant_languages tablosundan
    let availableLanguages = null;
    let defaultLanguage = null;

    /**
     * tenant_languages tablosundan dinamik dil listesini yükle
     */
    async function loadAvailableLanguages() {
        if (availableLanguages !== null) {
            return availableLanguages;
        }

        const response = await fetch('/admin/ai/seo/languages', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });

        const result = await response.json();
        if (result.success) {
            availableLanguages = result.data.languages;
            defaultLanguage = result.data.default_language;
            console.log('✅ Dinamik dil sistemi yüklendi:', {
                languages: availableLanguages,
                default: defaultLanguage,
                total: result.data.total_count
            });
            return availableLanguages;
        }

        throw new Error('tenant_languages tablosundan dil listesi alınamadı');
    }

    /**
     * Mevcut sayfadaki aktif dili al (dinamik sistem ile)
     */
    function getCurrentLanguage() {
        // Sayfa SEO tab'ından aktif dili al
        const activeLanguageTab = document.querySelector('.seo-language-content[style*="display: block"]');
        if (activeLanguageTab) {
            return activeLanguageTab.getAttribute('data-language') || defaultLanguage;
        }

        // Page yönetim sayfasındaki dil seçicisinden al
        const languageSelector = document.querySelector('[data-language-code]');
        if (languageSelector) {
            return languageSelector.getAttribute('data-language-code') || defaultLanguage;
        }

        // HTML lang attribute'undan al
        const htmlLang = document.documentElement.lang;
        if (htmlLang && availableLanguages?.some(lang => lang.code === htmlLang)) {
            return htmlLang;
        }

        return defaultLanguage;
    }
    
    let listenersAttached = false;

    function attachButtonListeners() {
        if (listenersAttached) {
            console.log('🔒 attachButtonListeners SKIP - already attached');
            return;
        }

        console.log('🔗 attachButtonListeners ÇAĞRILDI');
        const seoButtons = document.querySelectorAll('.ai-seo-comprehensive-btn, .ai-seo-recommendations-btn, .seo-generator-btn, .seo-suggestions-btn, [data-seo-feature], [data-action]');
        console.log('🔍 Bulunan buton sayısı:', seoButtons.length);

        seoButtons.forEach((button) => {
            // 🔍 DEBUG: Buton orijinal text'ini logla
            console.log('🔍 Buton DEBUG:', {
                classes: button.className,
                originalHTML: button.innerHTML,
                originalText: button.textContent.trim()
            });

            // Remove existing listeners
            const newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);

            newButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // AI SEO Analysis removed - only recommendations allowed

                if (this.classList.contains('seo-generator-btn') ||
                    this.getAttribute('data-action') === 'generate-seo') {
                    handleSeoGenerate(this);
                    return;
                }

                if (this.classList.contains('ai-seo-recommendations-btn') ||
                    this.getAttribute('data-seo-feature') === 'seo-smart-recommendations') {
                    handleSeoRecommendations(this);
                    return;
                }

                if (this.classList.contains('seo-suggestions-btn') ||
                    this.getAttribute('data-action') === 'get-suggestions') {
                    handleSeoSuggestions(this);
                    return;
                }
            });
        });

        // AI Recommendations section handlers
        attachRecommendationHandlers();

        // Mark listeners as attached to prevent infinite loop
        listenersAttached = true;
        console.log('✅ Button listeners attached successfully');
    }

    function attachRecommendationHandlers() {
        // Close recommendations
        document.querySelectorAll('.ai-close-recommendations').forEach(button => {
            const newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);
            newButton.addEventListener('click', function(e) {
                e.preventDefault();
                const language = this.getAttribute('data-language');
                const section = document.getElementById(`aiSeoRecommendationsSection_${language}`);
                if (section) {
                    section.style.display = 'none';
                }
            });
        });

        // Select all recommendations
        document.querySelectorAll('.ai-select-all-recommendations').forEach(button => {
            const newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);
            newButton.addEventListener('click', function(e) {
                e.preventDefault();
                const checkboxes = document.querySelectorAll('.ai-recommendation-checkbox');
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);

                checkboxes.forEach(cb => {
                    cb.checked = !allChecked;
                });

                updateApplyButton();

                // Update button text
                this.innerHTML = allChecked ?
                    '<i class="fas fa-check-double me-1"></i>Tümünü Seç' :
                    '<i class="fas fa-square me-1"></i>Seçimi Kaldır';
            });
        });

        // Apply selected recommendations
        document.querySelectorAll('.ai-apply-selected-recommendations').forEach(button => {
            const newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);
            newButton.addEventListener('click', function(e) {
                e.preventDefault();
                applySelectedRecommendations(this);
            });
        });

        // Retry recommendations
        document.querySelectorAll('.ai-retry-recommendations').forEach(button => {
            const newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);
            newButton.addEventListener('click', function(e) {
                e.preventDefault();
                const recommendationsBtn = document.querySelector('.ai-seo-recommendations-btn');
                if (recommendationsBtn) {
                    handleSeoRecommendations(recommendationsBtn);
                }
            });
        });
    }

    // 🎨 RENDER RECOMMENDATIONS HTML - Blade template ile aynı format
    function renderRecommendationsHTML(data, language) {
        console.log('🎨 Rendering recommendations HTML:', data);

        // recommendations array'ini çıkar
        const recommendations = data.recommendations || [];

        if (!recommendations || recommendations.length === 0) {
            return `
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    AI önerileri bulunamadı. Yeni öneriler oluşturmak için "AI Önerileri" butonunu kullanın.
                </div>
            `;
        }

        // SEO ve Social media önerilerini ayır
        const seoRecs = recommendations.filter(rec => ['title', 'description'].includes(rec.type));
        const socialRecs = recommendations.filter(rec => ['og_title', 'og_description'].includes(rec.type));

        let html = '';

        // SEO RECOMMENDATIONS
        if (seoRecs.length > 0) {
            html += '<div class="row mb-4">';
            seoRecs.forEach(rec => {
                const title = rec.type === 'title' ? 'SEO Başlığı' : 'SEO Açıklaması';
                html += `
                    <div class="col-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">${title}</h3>
                            </div>
                            <div class="list-group list-group-flush">`;

                if (rec.alternatives && rec.alternatives.length > 0) {
                    rec.alternatives.forEach((alt, index) => {
                        const activeClass = index === 0 ? ' active' : '';
                        const escapedValue = (alt.value || '').replace(/'/g, "\\'");
                        html += `
                            <a href="#" class="list-group-item list-group-item-action${activeClass}"
                               onclick="applyAlternativeDirectly('${rec.field_target}', '${escapedValue}', this); return false;">
                                ${alt.value}
                            </a>`;
                    });
                }

                html += `
                            </div>
                        </div>
                    </div>`;
            });
            html += '</div>';
        }

        // SOCIAL MEDIA RECOMMENDATIONS
        if (socialRecs.length > 0) {
            html += '<div class="row mb-4">';
            socialRecs.forEach(rec => {
                const title = rec.type === 'og_title' ? 'Sosyal Medya Başlığı' : 'Sosyal Medya Açıklaması';
                html += `
                    <div class="col-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">${title}</h3>
                            </div>
                            <div class="list-group list-group-flush">`;

                if (rec.alternatives && rec.alternatives.length > 0) {
                    rec.alternatives.forEach((alt, index) => {
                        const activeClass = index === 0 ? ' active' : '';
                        const escapedValue = (alt.value || '').replace(/'/g, "\\'");
                        html += `
                            <a href="#" class="list-group-item list-group-item-action${activeClass}"
                               onclick="applyAlternativeDirectly('${rec.field_target}', '${escapedValue}', this); return false;">
                                ${alt.value}
                            </a>`;
                    });
                }

                html += `
                            </div>
                        </div>
                    </div>`;
            });
            html += '</div>';
        }

        return html;
    }

    // Real AI API handlers
    async function handleSeoRecommendations(button) {
        console.log('🚀 SEO RECOMMENDATIONS START');
        const language = button.getAttribute('data-language') || getCurrentActiveLanguage();

        // DİREKT YENİLE - Confirm dialog yok
        console.log('🔄 AI Önerileri direkt yenileniyor (alert yok)');

        try {
            // 🚨 BUTON DISABLE: İşlem bitene kadar devre dışı
            button.disabled = true;
            console.log('🔒 Buton disable edildi');

            // 🎯 SADECE AI ÖNERİLERİ ALANINDA LOADER GÖSTER
            // Unique container ID kullan
            const containerId = `ai-recommendations-container-${language}`;
            const activeLanguageContent = document.querySelector(`.seo-language-content[data-language="${language}"]`);

            if (activeLanguageContent) {
                // Loader ekle (eğer yoksa)
                let loaderDiv = activeLanguageContent.querySelector('.ai-recommendations-loader');
                if (!loaderDiv) {
                    loaderDiv = document.createElement('div');
                    loaderDiv.className = 'ai-recommendations-loader text-center p-4 mb-4 bg-light rounded';
                    loaderDiv.innerHTML = `
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">AI önerileri üretiliyor...</span>
                        </div>
                        <h5 class="mt-3 mb-1">🤖 AI Önerileri Hazırlanıyor</h5>
                        <p class="text-muted">Sayfanız analiz ediliyor ve özelleştirilmiş öneriler üretiliyor...</p>
                    `;

                    // ✅ LOADER BUTONUN HEMEN ALTINA EKLE
                    const aiToolbar = activeLanguageContent.querySelector('.ai-seo-toolbar');
                    if (aiToolbar) {
                        aiToolbar.insertAdjacentElement('afterend', loaderDiv);
                        console.log('📍 Loader butonun hemen altına eklendi');
                    } else {
                        activeLanguageContent.insertBefore(loaderDiv, activeLanguageContent.firstChild);
                        console.log('📍 Toolbar bulunamadı - loader language content başına eklendi');
                    }
                }
                loaderDiv.style.display = 'block';
                console.log('✅ Loader gösterildi');
            }

            // YENİ SİSTEM: Inline loading zaten gösteriliyor
            
            const collectedData = collectFormData();
            // DEBUG: Model ID kontrolü (Global - herhangi bir modül olabilir)
            console.log('🔍 Model ID Debug - JavaScript:', {
                windowCurrentModelId: window.currentModelId,
                typeOfModelId: typeof window.currentModelId,
                finalModelId: window.currentModelId || null
            });

            const formData = {
                feature_slug: 'seo-smart-recommendations', // Keep original for now
                form_content: collectedData,
                language: language,
                page_id: window.currentModelId || null,  // page_id parametresi universal olarak kullanılıyor
                force_regenerate: true,  // Her zaman yeni veri al (test için)
                include_all_fields: true // Request all fields: title, description, and social media
            };

            // Force regenerate flag'ini temizle
            window.forceRegenerateRecommendations = false;
            console.log('📋 Recommendations Form data:', formData);
            
            console.log('🔗 Sending request to:', '/admin/seo/ai/recommendations');
            const response = await fetch('/admin/seo/ai/recommendations', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(formData)
            });
            
            console.log('📡 Response status:', response.status);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('❌ HTTP Error Response:', errorText);
                throw new Error(`HTTP ${response.status}: ${errorText.substring(0, 200)}...`);
            }
            
            const responseText = await response.text();
            console.log('📄 Raw response:', responseText.substring(0, 500));
            
            let result;
            try {
                result = JSON.parse(responseText);
                console.log('✅ Parsed JSON result:', result);
            } catch (jsonError) {
                console.error('❌ JSON Parse Error:', jsonError);
                console.error('❌ Response was not JSON:', responseText.substring(0, 1000));
                throw new Error('Server returned HTML instead of JSON. Check if endpoint exists.');
            }
            
            if (result.success) {
                console.log('✅ Success - displaying recommendations:', result.data);

                // 🎯 DİREKT DOM RENDER - Livewire kullanmıyoruz
                const activeLanguageContent = document.querySelector(`.seo-language-content[data-language="${language}"]`);
                if (activeLanguageContent) {
                    // 🗑️ ESKİ VERİTABANI STATIK HTML'İNİ SİL (Blade'den gelen .mt-3 container'lar)
                    const oldStaticContainers = activeLanguageContent.querySelectorAll('.mt-3:not(.ai-generated-recommendations)');
                    console.log(`🗑️ Eski statik container sayısı: ${oldStaticContainers.length}`);
                    oldStaticContainers.forEach((oldContainer, index) => {
                        console.log(`🗑️ Eski container ${index} siliniyor...`);
                        oldContainer.remove();
                    });

                    // Mevcut recommendations container'ı bul (UNIQUE ID kullan)
                    const containerId = `ai-recommendations-container-${language}`;
                    let recommendationsContainer = document.getElementById(containerId);

                    // Eğer yoksa oluştur
                    if (!recommendationsContainer) {
                        recommendationsContainer = document.createElement('div');
                        recommendationsContainer.id = containerId;
                        recommendationsContainer.className = 'mt-3 ai-generated-recommendations';
                        // TEMEL SEO BİLGİLERİ'nden önce ekle
                        const seoBasicCard = activeLanguageContent.querySelector('.card.mb-4');
                        if (seoBasicCard) {
                            seoBasicCard.insertAdjacentElement('beforebegin', recommendationsContainer);
                        } else {
                            // Eğer card bulunamazsa, language content'in başına ekle
                            activeLanguageContent.insertBefore(recommendationsContainer, activeLanguageContent.firstChild);
                        }
                        console.log(`✅ Yeni AI container oluşturuldu: ${containerId}`);
                    } else {
                        console.log(`✅ Mevcut AI container kullanılıyor: ${containerId}`);
                    }

                    // Yeni HTML'i render et
                    console.log('🎨 Render başlıyor - result.data:', result.data);
                    const newHTML = renderRecommendationsHTML(result.data, language);
                    console.log('🎨 Render edilen HTML uzunluğu:', newHTML.length);
                    console.log('🎨 Render edilen HTML preview:', newHTML.substring(0, 200));

                    recommendationsContainer.innerHTML = newHTML;

                    // ✅ CONTAINER STİLLERİNİ DÜZELT: cssText ile tüm inline stilleri override et
                    recommendationsContainer.style.cssText = 'opacity: 1 !important; pointer-events: auto !important;';
                    console.log('✅ Container stilleri düzeltildi (cssText ile override edildi)');

                    // 🎯 Unique ID sayesinde sadece bu container etkileniyor - eski Blade HTML'i bozulmuyor
                    console.log(`✅ Sadece AI container güncellendi: ${containerId}`);

                    // Loader'ı gizle (container düzeltildikten SONRA)
                    const loaderDiv = activeLanguageContent.querySelector('.ai-recommendations-loader');
                    if (loaderDiv) {
                        loaderDiv.style.display = 'none';
                        console.log('✅ Loader gizlendi');
                    }

                    console.log('✅ DOM direkt güncellendi - Livewire kullanılmadı');
                    console.log('📍 Container innerHTML uzunluğu:', recommendationsContainer.innerHTML.length);
                    console.log('📍 Container görünür mü?', window.getComputedStyle(recommendationsContainer).display);

                    // ✅ BUTON ENABLE: İşlem başarılı, butonu aktif et
                    // Butonu yeniden bul (DOM güncellenmiş olabilir)
                    const refreshedButton = document.querySelector(`.ai-seo-recommendations-btn[data-language="${language}"]`);
                    if (refreshedButton) {
                        refreshedButton.disabled = false;
                        console.log('🔓 Buton enable edildi (success) - refreshed button');
                    } else {
                        button.disabled = false;
                        console.log('🔓 Buton enable edildi (success) - original button');
                    }

                    // 🎯 TEMPLATE-BASED AUTO-APPLY: Template'deki 1. sıra önerilerini uygula
                    setTimeout(() => {
                        console.log('🎯 AUTO-APPLY: 1. sıra öneriler uygulanacak');
                        applyFirstAlternativesFromTemplate();
                    }, 300);
                }

                // Cache mesajı göster
                if (result.data.from_cache) {
                    console.log('💾 Öneri kaydedilmiş verilerden yüklendi');
                }
            } else {
                console.error('❌ API Error:', result.message);

                // ✅ BUTON ENABLE: Hata durumunda butonu aktif et
                button.disabled = false;
                console.log('🔓 Buton enable edildi (error)');

                // Loader'ı gizle ve içeriği geri getir
                const activeLanguageContent = document.querySelector(`.seo-language-content[data-language="${language}"]`);
                if (activeLanguageContent) {
                    const loaderDiv = activeLanguageContent.querySelector('.ai-recommendations-loader');
                    if (loaderDiv) loaderDiv.style.display = 'none';
                    const recommendationsContainer = activeLanguageContent.querySelector('.mt-3');
                    if (recommendationsContainer) {
                        recommendationsContainer.style.opacity = '1';
                        recommendationsContainer.style.pointerEvents = 'auto';
                    }
                }
                showInlineError('Öneri alınırken hata: ' + result.message);
            }
        } catch (error) {
            console.error('💥 RECOMMENDATIONS ERROR:', error);
            console.error('💥 Error stack:', error.stack);

            // ✅ BUTON ENABLE: Hata durumunda butonu aktif et
            button.disabled = false;
            console.log('🔓 Buton enable edildi (catch)');

            // Loader'ı gizle ve içeriği geri getir
            const activeLanguageContent = document.querySelector(`.seo-language-content[data-language="${language}"]`);
            if (activeLanguageContent) {
                const loaderDiv = activeLanguageContent.querySelector('.ai-recommendations-loader');
                if (loaderDiv) loaderDiv.style.display = 'none';
                const recommendationsContainer = activeLanguageContent.querySelector('.mt-3');
                if (recommendationsContainer) {
                    recommendationsContainer.style.opacity = '1';
                    recommendationsContainer.style.pointerEvents = 'auto';
                }
            }

            showInlineError('Bağlantı hatası: ' + error.message);
        } finally {
            // 🔓 BUTON ENABLE: Güvenlik için finally'de de enable et
            // Butonu yeniden bul (DOM güncellenmiş olabilir)
            const finalButton = document.querySelector(`.ai-seo-recommendations-btn[data-language="${language}"]`);
            if (finalButton) {
                finalButton.disabled = false;
                console.log('🔓 Buton enable edildi (finally) - refreshed button');
            } else {
                button.disabled = false;
                console.log('🔓 Buton enable edildi (finally) - original button');
            }
        }
    }
    
    // handleSeoAnalysis function removed - AI SEO Analysis no longer supported
    function handleSeoAnalysis_REMOVED(button) {
        console.log('❌ This function is disabled');
        return;
        /*
        console.log('🚀 SEO ANALYSIS START');

        // DİREKT ANALİZ - Confirm dialog yok
        console.log('🔄 SEO Analizi direkt başlatılıyor (alert yok)');

        try {
            const language = button.getAttribute('data-language') || getCurrentActiveLanguage();

            // Livewire loading state aktif et
            if (typeof Livewire !== 'undefined') {
                Livewire.dispatch('setAnalysisLoader', {loading: true, language: language});
            }

            // Sadece buton loading - global overlay yok
            setButtonLoading(button, true, 'Oluşturuluyor...');
            
            const collectedData = collectFormData();
            console.log('🚨 DEBUG CHECKPOINT 1: collectFormData called');
            const formData = {
                feature_slug: 'seo-smart-recommendations',
                form_content: collectedData,
                language: collectedData.language || 'tr',
                force_regenerate: true  // Her zaman yeni veri al (test için)
            };
            console.log('📋 Form data:', formData);
            console.log('🚨 DEBUG CHECKPOINT 2: collected data keys:', Object.keys(collectedData));
            
            console.log('🔗 Sending request to:', '/admin/seo/ai/analyze');
            // REMOVED - this function should not be called
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(formData)
            });
            
            console.log('📡 Response status:', response.status);
            console.log('📡 Response headers:', response.headers);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('❌ HTTP Error Response:', errorText);
                throw new Error(`HTTP ${response.status}: ${errorText.substring(0, 200)}...`);
            }
            
            const responseText = await response.text();
            console.log('📄 Raw response:', responseText.substring(0, 500));
            
            let result;
            try {
                result = JSON.parse(responseText);
                console.log('✅ Parsed JSON result:', result);
            } catch (jsonError) {
                console.error('❌ JSON Parse Error:', jsonError);
                console.error('❌ Response was not JSON:', responseText.substring(0, 1000));
                throw new Error('Server returned HTML instead of JSON. Check if endpoint exists.');
            }
            
            if (result.success) {
                console.log('✅ Success result:', result);
                // FULL RESPONSE'u kaydet - detailed_scores root level'de!
                window.lastSeoResponse = result;
                console.log('💾 SAVED TO WINDOW:', window.lastSeoResponse);

                // Livewire'a sonucu gönder
                if (typeof Livewire !== 'undefined') {
                    console.log('🚀 Dispatching aiAnalysisCompleted with FULL result:', result);
                    console.log('🚀 result.data:', result.data);
                    console.log('🚀 Language:', language);
                    // Livewire 3.x dispatch - object parameter format with #[On] attribute
                    Livewire.dispatch('aiAnalysisCompleted', {
                        analysisData: result,
                        language: language
                    });
                    console.log('✅ aiAnalysisCompleted dispatched successfully');
                } else {
                    console.warn('⚠️ Livewire not available, using fallback');
                    // Fallback: Eski display sistemi
                    displayComprehensiveAnalysis(result.data);
                }
            } else {
                console.error('❌ API Error:', result.message);
                showError('Analiz sırasında hata: ' + result.message);
            }
        } catch (error) {
            console.error('💥 FULL ERROR:', error);
            console.error('💥 Error stack:', error.stack);

            // Livewire error handler
            if (typeof Livewire !== 'undefined') {
                Livewire.dispatch('setAnalysisLoader', {loading: false, language: language});
            }

            showError('Bağlantı hatası: ' + error.message);
        } finally {
            // Sadece buton durumunu sıfırla - global overlay yok
            setButtonLoading(button, false);
        }
        */
    }
    
    async function handleSeoGenerate(button) {
        console.log('🚀 SEO GENERATE START');
        try {
            setButtonLoading(button, true, 'Oluşturuluyor...');
            showInlineLoading('seoUniversalResults', 'SEO içeriği oluşturuluyor, lütfen bekleyin...', 'ai-recommendations');
            
            const collectedData = collectFormData();
            const formData = {
                form_content: collectedData,
                language: collectedData.language || 'tr'
            };
            console.log('📋 Form data:', formData);
            
            console.log('🔗 Sending request to:', '/admin/seo/ai/generate');
            const response = await fetch('/admin/seo/ai/generate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(formData)
            });
            
            console.log('📡 Response status:', response.status);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('❌ HTTP Error Response:', errorText);
                throw new Error(`HTTP ${response.status}: ${errorText.substring(0, 200)}...`);
            }
            
            const responseText = await response.text();
            console.log('📄 Raw response:', responseText.substring(0, 500));
            
            let result;
            try {
                result = JSON.parse(responseText);
                console.log('✅ Parsed JSON result:', result);
            } catch (jsonError) {
                console.error('❌ JSON Parse Error:', jsonError);
                console.error('❌ Response was not JSON:', responseText.substring(0, 1000));
                throw new Error('Server returned HTML instead of JSON. Check if endpoint exists.');
            }
            
            if (result.success) {
                console.log('✅ Success - updating fields:', result.data);

                // Overlay'i kaldır ve sonuçları göster
                hideInlineLoading('seoUniversalResults');
                displayGenerateResults(result.data);
                showSuccess('SEO içeriği başarıyla oluşturuldu!');
            } else {
                console.error('❌ API Error:', result.message);
                showError('Oluşturma sırasında hata: ' + result.message);
            }
        } catch (error) {
            console.error('💥 FULL ERROR:', error);
            console.error('💥 Error stack:', error.stack);
            showError('Bağlantı hatası: ' + error.message);
        } finally {
            setButtonLoading(button, false);
        }
    }
    
    async function handleSeoSuggestions(button) {
        console.log('🚀 SEO SUGGESTIONS START');
        try {
            setButtonLoading(button, true, 'Oluşturuluyor...');
            showInlineLoading('aiRecommendationsContainer', 'SEO önerileri hazırlanıyor, lütfen bekleyin...', 'ai-recommendations');
            
            const collectedData = collectFormData();
            const formData = {
                form_content: collectedData,
                language: collectedData.language || 'tr'
            };
            console.log('📋 Form data:', formData);
            
            console.log('🔗 Sending request to:', '/admin/seo/ai/suggestions');
            const response = await fetch('/admin/seo/ai/suggestions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(formData)
            });
            
            console.log('📡 Response status:', response.status);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('❌ HTTP Error Response:', errorText);
                throw new Error(`HTTP ${response.status}: ${errorText.substring(0, 200)}...`);
            }
            
            const responseText = await response.text();
            console.log('📄 Raw response:', responseText.substring(0, 500));
            
            let result;
            try {
                result = JSON.parse(responseText);
                console.log('✅ Parsed JSON result:', result);
            } catch (jsonError) {
                console.error('❌ JSON Parse Error:', jsonError);
                console.error('❌ Response was not JSON:', responseText.substring(0, 1000));
                throw new Error('Server returned HTML instead of JSON. Check if endpoint exists.');
            }
            
            if (result.success) {
                console.log('✅ Success - displaying suggestions:', result.data);
                hideInlineLoading('aiRecommendationsContainer');
                displaySuggestions(result.data);
            } else {
                console.error('❌ API Error:', result.message);
                hideInlineLoading('aiRecommendationsContainer');
                showError('Öneri alınırken hata: ' + result.message);
            }
        } catch (error) {
            console.error('💥 FULL ERROR:', error);
            console.error('💥 Error stack:', error.stack);
            hideInlineLoading('aiRecommendationsContainer');
            showError('Bağlantı hatası: ' + error.message);
        } finally {
            setButtonLoading(button, false);
        }
    }
    
    // Utility functions
    function collectFormData() {
        console.log('🔍 collectFormData() BAŞLADI');
        const data = {};
        
        // Collect Livewire model data
        const livewireInputs = document.querySelectorAll('[wire\\:model]');
        console.log('🔍 Bulunan Livewire input sayısı:', livewireInputs.length);
        
        livewireInputs.forEach(input => {
            const model = input.getAttribute('wire:model');
            if (model) {
                // Include all fields, even empty ones - important for SEO scoring
                data[model] = input.value || '';
                console.log('📝 Field eklendi:', model, '=', input.value || 'BOŞ');
            }
        });
        
        // Collect QuillHtml/Hugerte content - include empty editors too
        const quillEditors = document.querySelectorAll('.ql-editor');
        console.log('🔍 Bulunan Quill editor sayısı:', quillEditors.length);
        
        quillEditors.forEach(editor => {
            const parentContainer = editor.closest('[wire\\:ignore]') || editor.closest('.quill-container');
            if (parentContainer) {
                const wireModelElement = parentContainer.querySelector('[wire\\:model]');
                if (wireModelElement) {
                    const model = wireModelElement.getAttribute('wire:model');
                    if (model) {
                        // Include all editors, even empty ones - important for SEO scoring
                        data[model] = editor.innerHTML || '';
                        console.log('📝 Editor eklendi:', model, '=', (editor.innerHTML || 'BOŞ').substring(0, 50));
                    }
                }
            }
        });
        
        // 🚀 SAYFA TİPİ ALGILAMA SİSTEMİ - 2025 AI ENHANCED
        const pageType = detectPageType();
        console.log('🎯 Algılanan sayfa tipi:', pageType);
        
        // DEBUG: Form field mapping - logda hangi fieldlar var görelim
        console.log('🔍 COLLECTED FORM DATA:', data);
        console.log('🔍 Form data keys:', Object.keys(data));
        
        // Backend için mapping - logda görülen field yapısı
        const mappedData = {};
        Object.keys(data).forEach(key => {
            mappedData[key] = data[key];
            
            // SEO field mapping for backend compatibility
            if (key.includes('multiLangInputs.tr.title')) {
                mappedData['title'] = data[key]; // Backend'de title arıyor
                console.log('✅ MAPPING: title =', data[key]);
            }
            if (key.includes('seoDataCache.tr.seo_description')) {
                mappedData['meta_description'] = data[key]; // Backend'de meta_description arıyor
                console.log('✅ MAPPING: meta_description =', data[key]);
            }
        });
        
        // 🎯 SAYFA TİPİ VE CONTEXT BİLGİLERİ EKLE
        mappedData.page_type = pageType.type;
        mappedData.page_context = pageType.context;
        mappedData.content_category = pageType.category;
        mappedData.seo_priority = pageType.seo_priority;
        
        // Add current page context
        mappedData.current_url = window.location.href;
        mappedData.language = document.documentElement.lang || 'tr';
        
        console.log('🔍 MAPPED DATA FOR BACKEND:', mappedData);
        console.log('🎯 PAGE TYPE CONTEXT:', {
            type: pageType.type,
            context: pageType.context,
            category: pageType.category,
            priority: pageType.seo_priority
        });
        
        return mappedData;
    }
    
    // 🚀 SAYFA TİPİ ALGILAMA SİSTEMİ - 2025 AI ENHANCED
    function detectPageType() {
        const url = window.location.pathname.toLowerCase();
        const title = document.querySelector('[wire\\:model*="title"]')?.value?.toLowerCase() || '';
        const content = document.querySelector('.ql-editor')?.textContent?.toLowerCase() || '';
        
        console.log('🔍 Page detection inputs:', { url, title: title.substring(0, 50), content: content.substring(0, 100) });
        
        // URL Pattern Analysis
        if (url.includes('/contact') || url.includes('/iletisim')) {
            return {
                type: 'contact',
                category: 'business_essential',
                context: 'Contact ve iletişim sayfası - müşteri dostu dil, güven inşası, yerel SEO odaklı',
                seo_priority: 'high',
                keywords_focus: ['iletişim', 'adres', 'telefon', 'email', 'randevu', 'harita'],
                content_style: 'professional_friendly'
            };
        }
        
        if (url.includes('/about') || url.includes('/hakkimizda') || url.includes('/hakkinda')) {
            return {
                type: 'about',
                category: 'brand_identity',
                context: 'Hakkımızda sayfası - marka hikayesi, güvenilirlik, uzmanlık alanları, kurumsal kimlik',
                seo_priority: 'high',
                keywords_focus: ['hakkımızda', 'hikaye', 'misyon', 'vizyon', 'takım', 'deneyim', 'uzmanlık'],
                content_style: 'authoritative_storytelling'
            };
        }
        
        if (url.includes('/service') || url.includes('/hizmet')) {
            return {
                type: 'service',
                category: 'conversion_focused',
                context: 'Hizmet tanıtım sayfası - değer önerisi, faydalar, call-to-action odaklı',
                seo_priority: 'very_high',
                keywords_focus: ['hizmet', 'çözüm', 'avantaj', 'fiyat', 'başvuru', 'randevu'],
                content_style: 'persuasive_professional'
            };
        }
        
        if (url.includes('/portfolio') || url.includes('/galeri') || url.includes('/work')) {
            return {
                type: 'portfolio',
                category: 'showcase',
                context: 'Portfolio ve çalışma örnekleri - görsel odaklı, başarı hikayeleri, teknik detaylar',
                seo_priority: 'high',
                keywords_focus: ['portfolio', 'proje', 'çalışma', 'örnek', 'başarı', 'referans'],
                content_style: 'visual_storytelling'
            };
        }
        
        if (url.includes('/blog') || url.includes('/makale') || url.includes('/news')) {
            return {
                type: 'blog',
                category: 'content_marketing',
                context: 'Blog ve içerik pazarlama - bilgi verici, SEO odaklı, okuyucu etkileşimi',
                seo_priority: 'very_high',
                keywords_focus: ['blog', 'makale', 'rehber', 'ipucu', 'bilgi', 'uzman görüşü'],
                content_style: 'informative_engaging'
            };
        }
        
        if (url.includes('/product') || url.includes('/urun')) {
            return {
                type: 'product',
                category: 'ecommerce',
                context: 'Ürün tanıtım sayfası - özellikler, faydalar, satış odaklı, karşılaştırma',
                seo_priority: 'very_high',
                keywords_focus: ['ürün', 'özellik', 'fiyat', 'satın al', 'inceleme', 'karşılaştır'],
                content_style: 'sales_optimized'
            };
        }
        
        // Content-Based Detection
        if (title.includes('iletişim') || title.includes('contact') || content.includes('adres') || content.includes('telefon')) {
            return {
                type: 'contact',
                category: 'business_essential',
                context: 'İletişim sayfası - başlık/içerik analizi ile tespit edildi',
                seo_priority: 'high',
                keywords_focus: ['iletişim', 'adres', 'telefon', 'email'],
                content_style: 'professional_friendly'
            };
        }
        
        if (title.includes('hakkımızda') || title.includes('about') || content.includes('hikaye') || content.includes('misyon')) {
            return {
                type: 'about',
                category: 'brand_identity', 
                context: 'Hakkımızda sayfası - başlık/içerik analizi ile tespit edildi',
                seo_priority: 'high',
                keywords_focus: ['hakkımızda', 'hikaye', 'misyon', 'vizyon'],
                content_style: 'authoritative_storytelling'
            };
        }
        
        // Default case - general page
        return {
            type: 'general',
            category: 'informational',
            context: 'Genel bilgi sayfası - dengeli SEO yaklaşımı, kullanıcı deneyimi odaklı',
            seo_priority: 'medium',
            keywords_focus: ['bilgi', 'detay', 'rehber', 'açıklama'],
            content_style: 'balanced_informative'
        };
    }
    
    
    // YENİ KOMPREHENSİF ANALİZ EKRANI
    function displayComprehensiveAnalysis(analysis) {
        console.log('🔍 COMPREHENSIVE ANALYSIS DEBUG START');
        console.log('📄 AI analizi tamamlandı:', analysis);

        try {
            // 1) Overlay'i kaldır
            hideInlineLoading('seoUniversalResults');

            // 2) Success mesajı göster
            showSuccess('SEO analizi tamamlandı! Sonuçları inceleyebilirsiniz.');

            // 3) TEK ALAN - Container'daki content area'yı bul
            const analysisContainer = document.getElementById('seoUniversalResults');
            const contentArea = analysisContainer?.querySelector('.inline-content-area');

            if (contentArea) {
                // 3) Mevcut tasarımla aynı analiz raporu oluştur
                let analysisHTML = `
                    <div class="mt-4">
                        <div class="row">
                            <div class="col-12">
                                <div class="bg-light border p-3 rounded-3 mb-3 position-relative" style="border-radius: 0.25rem !important;">
                                    <h3 class="mb-0">
                                        <i class="fas fa-chart-line me-2"></i>
                                        SEO Analiz Raporu
                                    </h3>
                                    <small class="position-absolute text-muted" style="right: 1rem; top: 50%; transform: translateY(-50%);">
                                        Az önce
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div>
                `;

                // Overall Score ve Detailed Scores - Mevcut tasarım
                const score = analysis.overall_score || 75; // Fallback score
                const scoreColor = score >= 80 ? 'success' : score >= 60 ? 'warning' : 'danger';
                const scoreColorBg = score >= 80 ? 'bg-success' : score >= 60 ? 'bg-warning' : 'bg-danger';
                const scoreText = score >= 80 ? 'Mükemmel' : score >= 60 ? 'İyi' : 'Geliştirilmeli';

                analysisHTML += `
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="avatar avatar-xl ${scoreColorBg} text-white mb-2" style="border-radius: 0.25rem !important;">
                                    ${score}
                                </div>
                                <h5>Genel SEO Skoru</h5>
                                <p>${scoreText}</p>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="row g-3">
                `;

                // Detailed Scores - Mevcut kart tasarımı ile
                const defaultScores = {
                    meta_title: analysis.detailed_scores?.meta_title || 55,
                    meta_description: analysis.detailed_scores?.meta_description || 60,
                    content_quality: analysis.detailed_scores?.content_quality || 70,
                    social_media: analysis.detailed_scores?.social_media || 60
                };

                const scoreItems = [
                    { key: 'meta_title', label: 'Meta Title', icon: 'fas fa-heading' },
                    { key: 'meta_description', label: 'Meta Description', icon: 'fas fa-align-left' },
                    { key: 'content_quality', label: 'İçerik Kalitesi', icon: 'fas fa-file-alt' },
                    { key: 'social_media', label: 'Sosyal Medya', icon: 'fas fa-share-alt' }
                ];

                scoreItems.forEach(item => {
                    const itemScore = defaultScores[item.key];
                    const itemColor = itemScore >= 80 ? 'success' : itemScore >= 60 ? 'warning' : 'danger';
                    const itemColorBorder = itemScore >= 80 ? 'border-success' : itemScore >= 60 ? 'border-warning' : 'border-danger';
                    const itemColorProgress = itemScore >= 80 ? 'bg-success' : itemScore >= 60 ? 'bg-warning' : 'bg-danger';

                    analysisHTML += `
                        <div class="col-md-3">
                            <div class="card ${itemColorBorder} hover-card" style="--tblr-primary: #EF4444 !important; border-radius: 0.25rem !important; transition: border-radius 0.15s;">
                                <div class="card-body text-center p-3" style="border-radius: 0.25rem !important;">
                                    <i class="${item.icon} fa-2x mb-2"></i>
                                    <h6>${item.label}</h6>
                                    <div class="progress mb-1" style="border-radius: 0.25rem !important;">
                                        <div class="progress-bar ${itemColorProgress}" style="width: ${itemScore}%; border-radius: 0.25rem !important;"></div>
                                    </div>
                                    <div>${itemScore}/100</div>
                                </div>
                            </div>
                        </div>
                    `;
                });

                analysisHTML += `
                            </div>
                        </div>
                    </div>
                `;

                // Accordions - mevcut yapıyı koru
                analysisHTML += `
                    <div class="accordion" id="realTimeSeoAccordion">
                        <!-- Meta Etiket Analizi -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed position-relative" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#metaAnalysis" aria-expanded="false">
                                    <i class="fas fa-tags me-2"></i>
                                    Meta Etiket Analizi
                                    <span class="badge bg-${defaultScores.meta_title >= 80 ? 'success' : defaultScores.meta_title >= 60 ? 'warning' : 'danger'} position-absolute"
                                          style="right: 2.5rem; top: 50%; transform: translateY(-50%); border-radius: 0.25rem !important;">
                                        ${defaultScores.meta_title}/100
                                    </span>
                                </button>
                            </h2>
                            <div id="metaAnalysis" class="accordion-collapse collapse" data-bs-parent="#realTimeSeoAccordion">
                                <div class="accordion-body pt-4">
                                    <div class="mb-4">
                                        <h5 class="mb-3">Meta Title</h5>
                                        <div class="p-3 rounded border" style="border-radius: 0.25rem !important;">
                                            <p class="mb-1">"${analysis.current_meta_title || 'Meta title bulunamadı'}"</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>${(analysis.current_meta_title || '').length} karakter</span>
                                                <span class="badge bg-${(analysis.current_meta_title || '').length >= 30 ? 'success' : 'danger'}" style="border-radius: 0.25rem !important;">
                                                    Meta Title
                                                </span>
                                            </div>
                                        </div>
                                        ${(analysis.current_meta_title || '').length < 30 ? '<p class="mb-0 mt-2">Başlığı genişletin (en az 30 karakter)</p>' : ''}
                                    </div>
                                    <div>
                                        <h5 class="mb-3">Meta Description</h5>
                                        <div class="p-3 rounded border" style="border-radius: 0.25rem !important;">
                                            <p class="mb-1">"${analysis.current_meta_description || 'Meta description bulunamadı'}"</p>
                                            <span>${(analysis.current_meta_description || '').length} karakter</span>
                                        </div>
                                        ${(analysis.current_meta_description || '').length < 120 ? '<p class="mb-0 mt-2">Açıklamayı genişletin (120-160 karakter arası ideal)</p>' : ''}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- İçerik Kalite Analizi -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed position-relative" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#contentQualityAnalysis" aria-expanded="false">
                                    <i class="fas fa-file-alt me-2"></i>
                                    İçerik Kalite Analizi
                                    <span class="badge bg-${defaultScores.content_quality >= 80 ? 'success' : defaultScores.content_quality >= 60 ? 'warning' : 'danger'} position-absolute"
                                          style="right: 2.5rem; top: 50%; transform: translateY(-50%); border-radius: 0.25rem !important;">
                                        ${defaultScores.content_quality}/100
                                    </span>
                                </button>
                            </h2>
                            <div id="contentQualityAnalysis" class="accordion-collapse collapse" data-bs-parent="#realTimeSeoAccordion">
                                <div class="accordion-body pt-4">
                                    <div class="mb-4">
                                        <h5 class="mb-3">İçerik İstatistikleri</h5>
                                        <div class="row g-3">
                                            <div class="col-6">
                                                <div class="p-3 rounded border text-center hover-element" style="border-radius: 0.25rem !important;">
                                                    <div class="h4 mb-1">${analysis.word_count || 0}</div>
                                                    <div>Kelime</div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="p-3 rounded border text-center hover-element" style="border-radius: 0.25rem !important;">
                                                    <div class="h4 mb-1">${analysis.reading_time || '1 dk'}</div>
                                                    <div>Okuma Süresi</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="mb-3">Başlık Yapısı</h5>
                                        <ul class="list-unstyled mb-0">
                                            <li class="mb-2">
                                                <span class="badge bg-${analysis.has_h1 ? 'success' : 'danger'} me-2" style="border-radius: 0.25rem !important;">${analysis.has_h1 ? 'Var' : 'Yok'}</span>
                                                H1 Başlığı
                                            </li>
                                            <li>
                                                <span class="badge bg-${analysis.has_h2 ? 'success' : 'warning'} me-2" style="border-radius: 0.25rem !important;">${analysis.has_h2 ? 'Yeterli' : 'Az'}</span>
                                                H2 Başlıkları
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sosyal Medya ve Yapısal Veri -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed position-relative" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#socialAndSchema" aria-expanded="false">
                                    <i class="fas fa-share-nodes me-2"></i>
                                    Sosyal Medya ve Yapısal Veri
                                    <span class="badge bg-${defaultScores.social_media >= 80 ? 'success' : defaultScores.social_media >= 60 ? 'warning' : 'danger'} position-absolute"
                                          style="right: 2.5rem; top: 50%; transform: translateY(-50%); border-radius: 0.25rem !important;">
                                        ${defaultScores.social_media}/100
                                    </span>
                                </button>
                            </h2>
                            <div id="socialAndSchema" class="accordion-collapse collapse" data-bs-parent="#realTimeSeoAccordion">
                                <div class="accordion-body pt-4">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <h5 class="mb-3">Open Graph Etiketleri</h5>
                                            <div class="p-3 rounded border" style="border-radius: 0.25rem !important;">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>OG Title</span>
                                                    <span class="badge bg-${analysis.og_title ? 'success' : 'danger'}" style="border-radius: 0.25rem !important;">
                                                        ${analysis.og_title ? 'Hazır' : 'Eksik'}
                                                    </span>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span>OG Description</span>
                                                    <span class="badge bg-${analysis.og_description ? 'success' : 'danger'}" style="border-radius: 0.25rem !important;">
                                                        ${analysis.og_description ? 'Hazır' : 'Eksik'}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h5 class="mb-3">Schema İşaretlemeleri</h5>
                                            <div class="p-3 rounded border" style="border-radius: 0.25rem !important;">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Schema</span>
                                                    <span class="badge bg-${analysis.has_schema ? 'success' : 'danger'}" style="border-radius: 0.25rem !important;">
                                                        ${analysis.has_schema ? 'Var' : 'Yok'}
                                                    </span>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span>Rich Snippets</span>
                                                    <span class="badge bg-${analysis.rich_snippets ? 'success' : 'warning'}" style="border-radius: 0.25rem !important;">
                                                        ${analysis.rich_snippets ? 'Aktif' : 'Eksik'}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Öncelikli Eylem Planı -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed position-relative" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#actionPlan" aria-expanded="false">
                                    <i class="fas fa-bullseye me-2"></i>
                                    Öncelikli Eylem Planı
                                    <span class="badge bg-primary position-absolute"
                                          style="right: 2.5rem; top: 50%; transform: translateY(-50%); --tblr-primary: #EF4444 !important; --tblr-primary-rgb: 239, 68, 68 !important; border-radius: 0.25rem !important;">
                                        ${(analysis.issues?.length || 3)} eylem
                                    </span>
                                </button>
                            </h2>
                            <div id="actionPlan" class="accordion-collapse collapse" data-bs-parent="#realTimeSeoAccordion">
                                <div class="accordion-body pt-4">
                                    <h5 class="mb-3">Yapılacaklar Listesi</h5>
                                    <div class="p-3 rounded border" style="border-radius: 0.25rem !important;">
                `;

                const actionItems = Array.isArray(analysis.issues) && analysis.issues.length > 0 ? analysis.issues : [
                    { priority: 'high', title: 'Meta Title iyileştir', suggestions: ['En az 30 karakter olmalı'] },
                    { priority: 'high', title: 'İçeriği geliştir', suggestions: ['En az 300 kelime yazın'] },
                    { priority: 'medium', title: 'Meta Description iyileştir', suggestions: ['En az 120 karakter olmalı'] }
                ];

                actionItems.forEach((item, index) => {
                    const priorityRaw = item?.priority ?? 'medium';
                    const priority = typeof priorityRaw === 'string' ? priorityRaw.toLowerCase() : String(priorityRaw).toLowerCase();
                    const priorityColor = priority === 'high' ? 'danger' : priority === 'medium' ? 'warning' : 'info';
                    const priorityText = priority === 'high' ? 'KRİTİK' : priority === 'medium' ? 'YÜKSEK' : 'ORTA';

                    const borderClass = index < actionItems.length - 1 ? 'border-bottom' : '';

                    analysisHTML += `
                        <div class="mb-3 pb-3 ${borderClass}">
                            <h6 class="mb-2">
                                <span class="badge bg-${priorityColor} me-2" style="border-radius: 0.25rem !important;">${priorityText}</span>
                                ${item.title || 'SEO İyileştirmesi'}
                            </h6>
                            <ul class="mb-0">
                    `;

                    const suggestionsSource = Array.isArray(item.suggestions) && item.suggestions.length
                        ? item.suggestions
                        : item.description
                            ? [item.description]
                            : ['İyileştirme önerileri hazırlanıyor...'];

                    suggestionsSource.forEach(suggestion => {
                        analysisHTML += `<li>${suggestion}</li>`;
                    });

                    analysisHTML += `</ul></div>`;
                });

                analysisHTML += `
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Kapanış divleri
                analysisHTML += `
                        </div>
                    </div>
                `;

                // Content area'ya HTML'i ekle
                contentArea.innerHTML = analysisHTML;

                console.log('✅ Analiz sonuçları dinamik olarak gösterildi');

                // Smooth scroll to results
                setTimeout(() => {
                    contentArea.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 500);

            } else {
                console.warn('⚠️ Analiz container bulunamadı, sayfa yenileniyor...');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }

        } catch (error) {
            console.error('❌ displayComprehensiveAnalysis error:', error);
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    }

    function displayGenerateResults(data) {
        console.log('📄 SEO Generate sonuçları gösteriliyor:', data);

        const container = document.getElementById('seoUniversalResults');
        const contentArea = container?.querySelector('.inline-content-area');

        if (contentArea && data) {
            // Mevcut tasarım ile SEO Generate sonuçları
            let generateHTML = `
                <div class="mt-4">
                    <div class="row">
                        <div class="col-12">
                            <div class="bg-light border p-3 rounded-3 mb-3 position-relative" style="border-radius: 0.25rem !important;">
                                <h3 class="mb-0">
                                    <i class="fas fa-magic me-2"></i>
                                    AI SEO İçerik Önerileri
                                </h3>
                                <small class="position-absolute text-muted" style="right: 1rem; top: 50%; transform: translateY(-50%);">
                                    Az önce
                                </small>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="accordion mt-4" id="seoGenerateAccordion">
            `;

            // Meta Title Accordion
            if (data.meta_title) {
                generateHTML += `
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#generateMetaTitle" aria-expanded="true">
                                <i class="fas fa-heading me-2"></i>
                                Önerilen Meta Title
                                <span class="badge bg-success position-absolute" style="right: 2.5rem; top: 50%; transform: translateY(-50%); border-radius: 0.25rem !important;">
                                    ${data.meta_title.length} karakter
                                </span>
                            </button>
                        </h2>
                        <div id="generateMetaTitle" class="accordion-collapse collapse show" data-bs-parent="#seoGenerateAccordion">
                            <div class="accordion-body pt-4">
                                <div class="p-3 rounded border mb-3" style="border-radius: 0.25rem !important;">
                                    <p class="mb-1">"${data.meta_title}"</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>${data.meta_title.length} karakter</span>
                                        <button class="btn btn-outline-primary btn-sm" onclick="navigator.clipboard.writeText('${data.meta_title}')">
                                            <i class="fas fa-copy me-1"></i>Kopyala
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            // Meta Description Accordion
            if (data.meta_description) {
                generateHTML += `
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#generateMetaDescription" aria-expanded="false">
                                <i class="fas fa-align-left me-2"></i>
                                Önerilen Meta Description
                                <span class="badge bg-success position-absolute" style="right: 2.5rem; top: 50%; transform: translateY(-50%); border-radius: 0.25rem !important;">
                                    ${data.meta_description.length} karakter
                                </span>
                            </button>
                        </h2>
                        <div id="generateMetaDescription" class="accordion-collapse collapse" data-bs-parent="#seoGenerateAccordion">
                            <div class="accordion-body pt-4">
                                <div class="p-3 rounded border mb-3" style="border-radius: 0.25rem !important;">
                                    <p class="mb-1">"${data.meta_description}"</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>${data.meta_description.length} karakter</span>
                                        <button class="btn btn-outline-primary btn-sm" onclick="navigator.clipboard.writeText('${data.meta_description}')">
                                            <i class="fas fa-copy me-1"></i>Kopyala
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            // Keywords/Anahtar Kelimeler
            if (data.keywords && Array.isArray(data.keywords) && data.keywords.length > 0) {
                generateHTML += `
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#generateKeywords" aria-expanded="false">
                                <i class="fas fa-tags me-2"></i>
                                Önerilen Anahtar Kelimeler
                                <span class="badge bg-info position-absolute" style="right: 2.5rem; top: 50%; transform: translateY(-50%); border-radius: 0.25rem !important;">
                                    ${data.keywords.length} kelime
                                </span>
                            </button>
                        </h2>
                        <div id="generateKeywords" class="accordion-collapse collapse" data-bs-parent="#seoGenerateAccordion">
                            <div class="accordion-body pt-4">
                                <div class="p-3 rounded border mb-3" style="border-radius: 0.25rem !important;">
                                    <div class="d-flex flex-wrap gap-2">
                                        ${data.keywords.map(keyword =>
                                            `<span class="badge bg-primary" style="border-radius: 0.25rem !important;">${keyword}</span>`
                                        ).join('')}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            generateHTML += `
                        </div>
                    </div>
                </div>
            `;

            contentArea.innerHTML = generateHTML;

            // Smooth scroll
            setTimeout(() => {
                contentArea.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }, 300);

            console.log('✅ Generate sonuçları mevcut tasarımla gösterildi');
        }
    }

    function displaySuggestions(data) {
        console.log('🎯 SUGGESTIONS Results data:', data);
        console.log('🔍 SUGGESTIONS DATA STRUCTURE CHECK:');
        console.log('  data.suggestions:', data.suggestions);
        console.log('  data.suggestions type:', typeof data.suggestions);
        if (data.suggestions && typeof data.suggestions === 'object') {
            console.log('  data.suggestions keys:', Object.keys(data.suggestions));
            console.log('  data.suggestions.title_suggestions:', data.suggestions.title_suggestions);
            console.log('  data.suggestions.description_suggestions:', data.suggestions.description_suggestions);
        }

        // AI ÖNERİLERİ - Container'ı temizle ve kullan
        const container = document.getElementById('aiRecommendationsContainer');
        if (!container) {
            console.warn('⚠️ aiRecommendationsContainer bulunamadı');
            return;
        }

        // ÖNCEKİ İÇERİĞİ TEMİZLE
        container.innerHTML = '';
        console.log('🧹 AI önerileri container temizlendi');

        // Mevcut tasarım ile SEO Suggestions
        let html = `
            <div class="mt-4">
                <div class="row">
                    <div class="col-12">
                        <div class="bg-light border p-3 rounded-3 mb-3 position-relative" style="border-radius: 0.25rem !important;">
                            <h3 class="mb-0">
                                <i class="fas fa-lightbulb me-2"></i>
                                AI SEO Önerileri
                            </h3>
                            <small class="position-absolute text-muted" style="right: 1rem; top: 50%; transform: translateY(-50%);">
                                Az önce
                            </small>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="accordion mt-4" id="seoSuggestionsAccordion">`;
        
        // SUGGESTIONS YAPISINI PARSE ET - backend'den obje olarak geliyor
        let hasContent = false;

        if (data && data.suggestions && typeof data.suggestions === 'object') {
            // Title Suggestions Accordion
            if (data.suggestions.title_suggestions && Array.isArray(data.suggestions.title_suggestions)) {
                html += `
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#titleSuggestions" aria-expanded="true">
                                <i class="fas fa-heading me-2"></i>
                                Title Önerileri
                                <span class="badge bg-primary position-absolute" style="right: 2.5rem; top: 50%; transform: translateY(-50%); border-radius: 0.25rem !important;">
                                    ${data.suggestions.title_suggestions.length} öneri
                                </span>
                            </button>
                        </h2>
                        <div id="titleSuggestions" class="accordion-collapse collapse show" data-bs-parent="#seoSuggestionsAccordion">
                            <div class="accordion-body pt-4">
                                <div class="list-group list-group-flush">
                `;

                data.suggestions.title_suggestions.forEach((suggestion, index) => {
                    html += `
                        <div class="list-group-item border-0 px-0 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-primary me-2" style="border-radius: 0.25rem !important;">${index + 1}</span>
                                ${suggestion}
                            </div>
                            <button class="btn btn-outline-primary btn-sm" onclick="navigator.clipboard.writeText('${suggestion}')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    `;
                });

                html += `
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                hasContent = true;
            }

            // Description Suggestions Accordion
            if (data.suggestions.description_suggestions && Array.isArray(data.suggestions.description_suggestions)) {
                html += `
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#descriptionSuggestions" aria-expanded="false">
                                <i class="fas fa-align-left me-2"></i>
                                Description Önerileri
                                <span class="badge bg-success position-absolute" style="right: 2.5rem; top: 50%; transform: translateY(-50%); border-radius: 0.25rem !important;">
                                    ${data.suggestions.description_suggestions.length} öneri
                                </span>
                            </button>
                        </h2>
                        <div id="descriptionSuggestions" class="accordion-collapse collapse" data-bs-parent="#seoSuggestionsAccordion">
                            <div class="accordion-body pt-4">
                                <div class="list-group list-group-flush">
                `;

                data.suggestions.description_suggestions.forEach((suggestion, index) => {
                    html += `
                        <div class="list-group-item border-0 px-0 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-success me-2" style="border-radius: 0.25rem !important;">${index + 1}</span>
                                ${suggestion}
                            </div>
                            <button class="btn btn-outline-primary btn-sm" onclick="navigator.clipboard.writeText('${suggestion}')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    `;
                });

                html += `
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                hasContent = true;
            }

            // Content Improvements Accordion
            if (data.suggestions.content_improvements && Array.isArray(data.suggestions.content_improvements)) {
                html += `
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#contentImprovements" aria-expanded="false">
                                <i class="fas fa-tools me-2"></i>
                                İçerik İyileştirmeleri
                                <span class="badge bg-warning position-absolute" style="right: 2.5rem; top: 50%; transform: translateY(-50%); border-radius: 0.25rem !important;">
                                    ${data.suggestions.content_improvements.length} öneri
                                </span>
                            </button>
                        </h2>
                        <div id="contentImprovements" class="accordion-collapse collapse" data-bs-parent="#seoSuggestionsAccordion">
                            <div class="accordion-body pt-4">
                                <div class="list-group list-group-flush">
                `;

                data.suggestions.content_improvements.forEach((suggestion, index) => {
                    html += `
                        <div class="list-group-item border-0 px-0 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-warning me-2" style="border-radius: 0.25rem !important;">${index + 1}</span>
                                ${suggestion}
                            </div>
                            <button class="btn btn-outline-primary btn-sm" onclick="navigator.clipboard.writeText('${suggestion}')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    `;
                });

                html += `
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                hasContent = true;
            }
        }
        
        if (!hasContent) {
            html += `
                <div class="accordion-item">
                    <div class="accordion-body text-center py-5">
                        <i class="fas fa-robot fa-3x mb-3 opacity-25"></i>
                        <h5 class="text-muted">Öneri Bulunamadı</h5>
                        <p>AI önerileri alınamadı veya beklenmedik bir hata oluştu.</p>
                    </div>
                </div>
            `;
        }

        html += `
                    </div>
                </div>
            </div>
        `;

        container.innerHTML = html;

        // Smooth scroll
        setTimeout(() => {
            container.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }, 300);

        console.log('✅ Professional Suggestions shown');
    }
    
    function showSuccess(message) {
        console.log('✅ SUCCESS:', message);
    }
    
    function showError(message) {
        console.error('❌ ERROR:', message);
    }

    // AI RECOMMENDATIONS HELPER FUNCTIONS
    function displayRecommendations(data, language) {
        console.log('🎯 RECOMMENDATIONS Results data:', data);

        // AI ÖNERİLERİ - aiRecommendationsContainer kullan
        let container = document.getElementById('aiRecommendationsContainer');
        if (!container) {
            console.warn('⚠️ aiRecommendationsContainer container bulunamadı');
            // Container oluştur - TOOLBAR'IN ALTINA
            const toolbar = document.querySelector('.ai-seo-toolbar');
            const targetLocation = toolbar ? toolbar.parentElement : (
                document.querySelector('.seo-language-content[style*="display: block"]') ||
                document.querySelector('.tab-pane.active')
            );

            if (targetLocation) {
                const newContainer = document.createElement('div');
                newContainer.id = 'seoUniversalResults';
                newContainer.className = 'inline-results-container mt-4';

                // TOOLBAR'IN HEMEN ALTINA EKLE
                if (toolbar) {
                    toolbar.insertAdjacentElement('afterend', newContainer);
                    console.log('✅ Recommendations container toolbar altına eklendi');
                } else {
                    targetLocation.appendChild(newContainer);
                    console.log('✅ Recommendations container fallback location\'a eklendi');
                }

                // YENİ CONTAINER REFERANSINI AL
                container = newContainer;
            } else {
                return;
            }
        }

        // Loading'i gizle
        hideInlineLoading('seoUniversalResults');
        
        // Parse recommendations data
        const recommendations = data.recommendations || [];
        console.log('📝 Parsed recommendations:', recommendations);

        // YENİ INLINE SİSTEM HTML - SEO ANALİZİ FORMATI
        let fullHTML = `
            <div class="mt-4">
                <div class="row">
                    <div class="col-12">
                        <div class="bg-light border p-3 rounded-3 mb-3 position-relative" style="border-radius: 0.25rem !important;">
                            <h3 class="mb-0">
                                <i class="fas fa-lightbulb me-2"></i>
                                AI SEO Önerileri
                            </h3>
                            <small class="position-absolute text-muted" style="right: 1rem; top: 50%; transform: translateY(-50%);">
                                Az önce
                            </small>
                        </div>
                    </div>
                </div>
                <div>`;

        // Separate SEO and Social recommendations - FIXED LOGIC
        const seoRecs = recommendations.filter(r => r.type === 'title' || r.type === 'description');
        const socialRecs = recommendations.filter(r => r.type === 'og_title' || r.type === 'og_description');

        console.log('🔍 Filtering recommendations:', {
            total: recommendations.length,
            seoRecs: seoRecs.length,
            socialRecs: socialRecs.length,
            allTypes: recommendations.map(r => r.type)
        });

        // SEO Önerileri Section
        if (seoRecs.length > 0) {
            fullHTML += `<div class="row mb-4">`;

            seoRecs.forEach((rec, index) => {
                const hasAlternatives = rec.alternatives && rec.alternatives.length > 0;

                fullHTML += `
                    <div class="col-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">${rec.title || 'SEO Önerisi'}</h3>
                            </div>
                            <div class="list-group list-group-flush">`;

                if (hasAlternatives) {
                    rec.alternatives.forEach((alt, altIndex) => {
                        fullHTML += `
                            <a href="#" class="list-group-item list-group-item-action${altIndex === 0 ? ' active' : ''}"
                               onclick="applyAlternativeDirectly('${rec.field_target}', '${alt.value.replace(/'/g, "\\'")}', this); return false;">
                                ${alt.value}
                            </a>`;
                    });
                } else {
                    fullHTML += `
                        <a href="#" class="list-group-item list-group-item-action">
                            ${rec.value || rec.suggested_value || ''}
                        </a>`;
                }

                fullHTML += `
                            </div>
                        </div>
                    </div>`;
            });

            fullHTML += `</div>`;
        }

        // Sosyal Medya Önerileri Section
        if (socialRecs.length > 0) {
            fullHTML += `<div class="row mb-4">`;

            socialRecs.forEach((rec, index) => {
                const hasAlternatives = rec.alternatives && rec.alternatives.length > 0;

                fullHTML += `
                    <div class="col-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">${rec.title || 'Sosyal Medya Önerisi'}</h3>
                            </div>
                            <div class="list-group list-group-flush">`;

                if (hasAlternatives) {
                    rec.alternatives.forEach((alt, altIndex) => {
                        fullHTML += `
                            <a href="#" class="list-group-item list-group-item-action${altIndex === 0 ? ' active' : ''}"
                               onclick="applyAlternativeDirectly('${rec.field_target}', '${alt.value.replace(/'/g, "\\'")}', this); return false;">
                                ${alt.value}
                            </a>`;
                    });
                } else {
                    fullHTML += `
                        <a href="#" class="list-group-item list-group-item-action">
                            ${rec.value || rec.suggested_value || ''}
                        </a>`;
                }

                fullHTML += `
                            </div>
                        </div>
                    </div>`;
            });

            fullHTML += `</div>`;
        }

        // HTML'i kapat
        fullHTML += `
                </div>
            </div>`;

        // YENİ SİSTEM: Inline container'a yazdır (sayfa yenileme YOK)
        container.innerHTML = fullHTML;

        // Auto-apply first recommendations
        autoApplyFirstRecommendations(recommendations);

        console.log('✅ Recommendations displayed successfully in inline system');

        // Smooth scroll
        setTimeout(() => {
            container.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }, 100);
    }

    // Auto-apply first recommendations - ENHANCED
    function autoApplyFirstRecommendations(recommendations) {
        console.log('🔄 Auto-applying first recommendations...');
        console.log('📝 Recommendations structure:', recommendations);

        if (!recommendations || recommendations.length === 0) {
            console.warn('⚠️ No recommendations to auto-apply');
            return;
        }

        let appliedCount = 0;
        recommendations.forEach((rec, index) => {
            console.log('🔍 Processing recommendation ' + (index + 1) + ':', rec);

            if (!rec.alternatives || rec.alternatives.length === 0) {
                console.warn(`⚠️ Recommendation ${index + 1} has no alternatives:`, rec);
                return;
            }

            const firstAlternative = rec.alternatives[0];
            if (!firstAlternative || !firstAlternative.value) {
                console.warn(`⚠️ First alternative is invalid:`, firstAlternative);
                return;
            }

            console.log('✅ Auto-applying ' + (rec.type || 'unknown') + ': ' + rec.field_target + ' = "' + firstAlternative.value.substring(0, 50) + '..."');

            try {
                // Apply directly to form fields
                applyAlternativeDirectly(rec.field_target, firstAlternative.value);
                appliedCount++;
                console.log('✅ Successfully applied recommendation ' + (index + 1));
            } catch (error) {
                console.error(`❌ Failed to apply recommendation ${index + 1}:`, error);
            }
        });

        // Auto-enable social media customization toggle when OG fields are applied (SADECE TR için)
        const hasOgFields = recommendations.some(rec =>
            rec.field_target && (rec.field_target.includes('og_title') || rec.field_target.includes('og_description'))
        );

        if (hasOgFields) {
            console.log('🔄 Enabling OG custom fields for TR language...');
            try {
                enableOgCustomFields('tr');
                console.log('✅ OG custom fields enabled');
            } catch (error) {
                console.error('❌ Failed to enable OG custom fields:', error);
            }
        }

        console.log('✅ Auto-apply completed: ' + appliedCount + '/' + recommendations.length + ' recommendations applied');
    }

    function showRecommendationsError(message, language) {
        const section = document.getElementById(`aiSeoRecommendationsSection_${language}`);
        if (!section) return;
        
        const loading = section.querySelector('.ai-recommendations-loading');
        const content = section.querySelector('.ai-recommendations-content');
        const error = section.querySelector('.ai-recommendations-error');
        
        loading.style.display = 'none';
        content.style.display = 'none';
        error.style.display = 'block';
        
        // Update error message if needed
        const errorMsg = error.querySelector('p');
        if (errorMsg) {
            errorMsg.textContent = message;
        }
        
        console.error('❌ Recommendations error shown:', message);
    }
    
    function updateApplyButton() {
        // RADIO BUTTON: Seçili radio buttonları sayıyoruz
        const radioButtons = document.querySelectorAll('.alternative-radio:checked');
        const applyBtn = document.querySelector('.ai-apply-selected-recommendations');
        
        if (!applyBtn) return;
        
        const selectedCount = radioButtons.length;
        
        if (selectedCount > 0) {
            applyBtn.disabled = false;
            applyBtn.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-1">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M9 7m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"/>
                    <path d="M9 17l0 -10"/>
                    <path d="M19 16.5c0 .667 -.167 1.167 -.5 1.5s-.833 .333 -1.5 .333s-1.167 -.167 -1.5 -.5s-.333 -.833 -.333 -1.5c0 -.667 .167 -1.167 .5 -1.5s.833 -.333 1.5 -.333s1.167 .167 1.5 .5s.333 .833 .333 1.5z"/>
                </svg>
                Seçilenleri Uygula (${selectedCount})
            `;
        } else {
            applyBtn.disabled = true;
            applyBtn.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-1">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M9 7m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"/>
                    <path d="M9 17l0 -10"/>
                    <path d="M19 16.5c0 .667 -.167 1.167 -.5 1.5s-.833 .333 -1.5 .333s-1.167 -.167 -1.5 -.5s-.333 -.833 -.333 -1.5c0 -.667 .167 -1.167 .5 -1.5s.833 -.333 1.5 -.333s1.167 .167 1.5 .5s.333 .833 .333 1.5z"/>
                </svg>
                Seçilenleri Uygula
            `;
        }
    }
    
    async function applySelectedRecommendations(button) {
        // RADIO BUTTON: Seçili radio buttonları al  
        const selectedRadios = document.querySelectorAll('.alternative-radio:checked');
        
        if (selectedRadios.length === 0) {
            showError('Lütfen uygulamak istediğiniz önerileri seçin.');
            return;
        }
        
        setButtonLoading(button, 'Uygulanıyor...');
        
        try {
            console.log(`🚀 Applying ${selectedRadios.length} selected recommendations...`);
            
            // Her seçili radio button için doğrudan uygula
            let successCount = 0;
            selectedRadios.forEach(radio => {
                try {
                    // Radio button'un onclick fonksiyonunu çağır
                    const onclickAttr = radio.getAttribute('onclick');
                    if (onclickAttr) {
                        // Extract fieldTarget and value from onclick
                        const match = onclickAttr.match(/applyAlternativeDirectly\('([^']+)',\s*'([^']+)'/);
                        if (match) {
                            const fieldTarget = match[1];
                            const value = match[2].replace(/\\'/g, "'");
                            
                            // Direct apply
                            applyAlternativeDirectly(fieldTarget, value, radio);
                            successCount++;
                            console.log(`✅ Applied: ${fieldTarget} = ${value.substring(0, 50)}...`);
                        }
                    }
                } catch (error) {
                    console.error('Error applying radio selection:', error);
                }
            });
            
            // Show success feedback
            if (successCount > 0) {
                showSuccess(`${successCount} öneri başarıyla uygulandı!`);
                
                // Clear all radio selections after applying
                selectedRadios.forEach(radio => {
                    radio.checked = false;
                });
                updateApplyButton();
            }
            
        } catch (error) {
            console.error('💥 Apply recommendations error:', error);
            showError('Öneri uygulanırken hata oluştu: ' + error.message);
        } finally {
            resetButton(button, `
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-1">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M9 7m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"/>
                    <path d="M9 17l0 -10"/>
                    <path d="M19 16.5c0 .667 -.167 1.167 -.5 1.5s-.833 .333 -1.5 .333s-1.167 -.167 -1.5 -.5s-.333 -.833 -.333 -1.5c0 -.667 .167 -1.167 .5 -1.5s.833 -.333 1.5 -.333s1.167 .167 1.5 .5s.333 .833 .333 1.5z"/>
                </svg>
                Seçilenleri Uygula
            `);
        }
    }
    
    async function applyRecommendation(rec) {
        console.log('🔧 Applying single recommendation:', rec);
        
        const language = document.querySelector('.seo-language-content[style*="display: block"]')?.getAttribute('data-language') || 'tr';
        let valueToApply = rec.value || rec.suggested_value;
        
        // CHECK FOR SELECTED ALTERNATIVE
        if (rec.alternatives && rec.alternatives.length > 0) {
            // Find the selected alternative for this recommendation
            const recItem = document.querySelector(`[data-recommendation*='"id":${rec.id}']`);
            if (recItem) {
                const selectedRadio = recItem.querySelector('input.alternative-radio:checked');
                if (selectedRadio) {
                    // Get the selected alternative data
                    const altOption = selectedRadio.closest('.alternative-option');
                    if (altOption) {
                        const altData = JSON.parse(altOption.getAttribute('data-alternative'));
                        valueToApply = altData.value;
                        console.log('✅ Using selected alternative:', altData.label, '=', valueToApply);
                    }
                } else {
                    console.warn('⚠️ No alternative selected for recommendation with alternatives');
                    return Promise.reject(new Error('Lütfen bir seçenek belirleyin'));
                }
            }
        }
        
        // Apply the selected value
        if (rec.type === 'title' || rec.field_target === 'seoDataCache.tr.seo_title') {
            const titleField = document.querySelector(`input[wire\\:model*="seoDataCache.${language}.seo_title"]`);
            if (titleField) {
                titleField.value = valueToApply;
                titleField.dispatchEvent(new Event('input', { bubbles: true }));
                console.log('✅ Title updated:', valueToApply);
            }
        } else if (rec.type === 'description' || rec.field_target === 'seoDataCache.tr.seo_description') {
            const descField = document.querySelector(`textarea[wire\\:model*="seoDataCache.${language}.seo_description"]`);
            if (descField) {
                descField.value = valueToApply;
                descField.dispatchEvent(new Event('input', { bubbles: true }));
                console.log('✅ Description updated:', valueToApply);
            }
        } else if (rec.type === 'og_title' || rec.field_target === 'seoDataCache.tr.og_title') {
            const ogTitleField = document.querySelector(`input[wire\\:model*="seoDataCache.${language}.og_title"]`);
            if (ogTitleField) {
                ogTitleField.value = valueToApply;
                ogTitleField.dispatchEvent(new Event('input', { bubbles: true }));
                console.log('✅ OG Title updated:', valueToApply);
            }
        } else if (rec.type === 'og_description' || rec.field_target === 'seoDataCache.tr.og_description') {
            const ogDescField = document.querySelector(`textarea[wire\\:model*="seoDataCache.${language}.og_description"]`);
            if (ogDescField) {
                ogDescField.value = valueToApply;
                ogDescField.dispatchEvent(new Event('input', { bubbles: true }));
                console.log('✅ OG Description updated:', valueToApply);
            }
        } else if (rec.type === 'keywords') {
            // For keywords, show as info for now (can be implemented later)
            console.log('ℹ️ Keywords suggestion:', valueToApply);
        }
        
        return Promise.resolve();
    }

    // Save SEO Content to Database
    window.saveSeoToDatabase = function(type, data) {
        console.log('💾 Saving SEO data to database:', type, data);
        
        // Get current page/model info
        const currentUrl = window.location.pathname;
        const modelMatch = currentUrl.match(/\/admin\/(\w+)\/manage\/(\d+)/);
        
        if (!modelMatch) {
            showError('Model bilgisi alınamadı');
            return;
        }
        
        const modelType = modelMatch[1];
        const modelId = modelMatch[2];
        
        // Prepare save data
        const saveData = {
            model_type: modelType,
            model_id: modelId,
            language: 'tr',
            data: data,
            type: type
        };
        
        // Save to database via AJAX
        fetch('/admin/seo/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(saveData)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showSuccess('SEO verileri kaydedildi');
                
                // Update form fields if needed
                if (data.meta_title) {
                    const titleField = document.querySelector('input[name="seoDataCache[tr][seo_title]"]');
                    if (titleField) titleField.value = data.meta_title;
                }
                
                if (data.meta_description) {
                    const descField = document.querySelector('textarea[name="seoDataCache[tr][seo_description]"]');
                    if (descField) descField.value = data.meta_description;
                }
            } else {
                showError('Kayıt başarısız: ' + (result.error || 'Bilinmeyen hata'));
            }
        })
        .catch(error => {
            console.error('Save error:', error);
            showError('Kayıt hatası: ' + error.message);
        });
    };
    
    // Selection UI for Suggestions
    window.applySeoSuggestion = function(field, value) {
        console.log('✅ Applying suggestion:', field, value);
        
        if (field === 'title') {
            const titleField = document.querySelector('input[name="seoDataCache[tr][seo_title]"]');
            if (titleField) {
                titleField.value = value;
                showSuccess('Title güncellendi');
            }
        } else if (field === 'description') {
            const descField = document.querySelector('textarea[name="seoDataCache[tr][seo_description]"]');
            if (descField) {
                descField.value = value;
                showSuccess('Description güncellendi');
            }
        }
    };
    
    // CLICK-TO-FILL functionality for SEO recommendations - ENHANCED
    window.applyAlternativeDirectly = function(fieldTarget, value, element) {
        console.log('🎯 Direct apply:', fieldTarget, value.substring(0, 50));
        console.log('🔍 Element:', element);

        // DIRECT WIRE MODEL TARGETING - Backend sends full wire:model path
        let selector;

        if (fieldTarget.includes('seoDataCache.')) {
            // Direct wire:model targeting - properly escape dots for CSS selectors
            const escapedFieldTarget = fieldTarget.replace(/\./g, '\\\\.');
            selector = `[wire\\:model="${escapedFieldTarget}"]`;
            console.log('🔧 Original field target:', fieldTarget);
            console.log('🔧 Escaped field target:', escapedFieldTarget);
            console.log('🔧 Final selector:', selector);
        } else {
            // Fallback mappings for simple field names
            const fieldMappings = {
                'seo_title': 'input[wire\\:model="seoDataCache.tr.seo_title"]',
                'seo_description': 'textarea[wire\\:model="seoDataCache.tr.seo_description"]',
                'content_type': 'select[wire\\:model="seoDataCache.tr.content_type"]',
                'og_title': 'input[wire\\:model="seoDataCache.tr.og_title"]',
                'og_description': 'textarea[wire\\:model="seoDataCache.tr.og_description"]',
                'priority_score': 'input[wire\\:model="seoDataCache.tr.priority_score"]'
            };
            selector = fieldMappings[fieldTarget];
        }

        if (!selector) {
            console.error('❌ Field mapping failed for:', fieldTarget);
            console.error('❌ Available selectors would be:', {
                'direct': `[wire\\:model="${fieldTarget}"]`,
                'fallback': 'Not available'
            });
            showError('Alan bulunamadı: ' + fieldTarget);
            return false;
        }

        console.log('🔍 Using selector:', selector);

        const field = document.querySelector(selector);
        if (!field) {
            console.error('❌ Field not found with selector:', selector);

            // Debug: Show available SEO fields
            const allSeoFields = document.querySelectorAll('[wire\\:model*="seo"]');
            console.error('❌ Available SEO fields:', Array.from(allSeoFields).map(f => f.getAttribute('wire:model')));

            // Try alternative selectors
            const altSelectors = [
                `input[wire\\:model="${fieldTarget}"]`,
                `textarea[wire\\:model="${fieldTarget}"]`,
                `select[wire\\:model="${fieldTarget}"]`
            ];

            for (const altSelector of altSelectors) {
                const altField = document.querySelector(altSelector);
                if (altField) {
                    console.log('✅ Found field with alternative selector:', altSelector);
                    return applyToField(altField, value, fieldTarget, element);
                }
            }

            showError('Form alanı bulunamadı: ' + selector);
            return false;
        }

        console.log('✅ Field found:', field.tagName, field.type || field.nodeName, field.getAttribute('wire:model'));

        return applyToField(field, value, fieldTarget, element);
    };

    // Helper function to apply value to field
    function applyToField(field, value, fieldTarget, element) {
        try {
            // Special handling for content_type (select vs custom input)
            if (fieldTarget === 'content_type' || fieldTarget.includes('content_type')) {
                handleContentTypeSelection(value);
            } else {
                // Update field value
                const oldValue = field.value;
                field.value = value;

                // Trigger Livewire update events
                field.dispatchEvent(new Event('input', { bubbles: true }));
                field.dispatchEvent(new Event('change', { bubbles: true }));

                console.log(`✅ Field updated: "${oldValue}" → "${value}"`);
            }

            // Auto-enable OG custom fields if OG fields are filled
            if (fieldTarget.includes('og_title') || fieldTarget.includes('og_description')) {
                console.log('🔄 Auto-enabling OG custom fields...');
                const language = extractLanguageFromFieldTarget(fieldTarget);
                enableOgCustomFields(language);
            }

            // Visual feedback
            field.style.backgroundColor = '#d4edda';
            field.style.border = '2px solid #28a745';

            // Mark the clicked alternative as selected
            if (element) {
                const parent = element.closest('.ai-recommendation-item');
                if (parent) {
                    const alternatives = parent.querySelectorAll('.form-check');
                    alternatives.forEach(alt => alt.classList.remove('bg-success', 'text-white'));
                    element.classList.add('bg-success', 'text-white');
                }
            }

            // Reset visual feedback after 3 seconds
            setTimeout(() => {
                field.style.backgroundColor = '';
                field.style.border = '';
            }, 3000);

            showSuccess('Öneri uygulandı: ' + getFieldDisplayName(fieldTarget));
            return true;

        } catch (error) {
            console.error('❌ Error applying value to field:', error);
            showError('Öneri uygulanırken hata: ' + error.message);
            return false;
        }
    }
    
    // Handle content type selection (dropdown vs custom input)
    function handleContentTypeSelection(value) {
        const selectField = document.querySelector('select[wire\\:model="seoDataCache.tr.content_type"]');
        if (!selectField) return;
        
        // Check if value exists in select options
        const optionExists = Array.from(selectField.options).some(option => option.value === value);
        
        if (optionExists) {
            // Select from dropdown
            selectField.value = value;
            selectField.dispatchEvent(new Event('change', { bubbles: true }));
        } else {
            // Use custom input
            selectField.value = 'custom';
            selectField.dispatchEvent(new Event('change', { bubbles: true }));
            
            // Fill custom input
            setTimeout(() => {
                const customInput = document.querySelector('input[wire\\:model="seoDataCache.tr.content_type_custom"]');
                if (customInput) {
                    customInput.value = value;
                    customInput.dispatchEvent(new Event('input', { bubbles: true }));
                }
            }, 100);
        }
    }
    
    // Auto-enable OG custom fields when OG values are set
    function enableOgCustomFields(language = 'tr') {
        console.log(`🔄 Enabling OG custom fields for language: ${language}`);

        // Multiple selector fallbacks for checkbox
        const selectors = [
            `input[wire\\:model="seoDataCache.${language}.og_custom_enabled"]`,
            `input[wire\\:model\\.defer="seoDataCache.${language}.og_custom_enabled"]`,
            `input[id="og_custom_${language}"]`,
            `#og_custom_${language}`
        ];

        let checkbox = null;
        for (const selector of selectors) {
            checkbox = document.querySelector(selector);
            if (checkbox) {
                console.log(`✅ OG checkbox found with selector: ${selector}`);
                break;
            }
        }

        if (!checkbox) {
            console.error(`❌ OG custom checkbox not found for language: ${language}. Tried selectors:`, selectors);
            return;
        }

        if (!checkbox.checked) {
            console.log(`✅ Activating OG custom checkbox for ${language}...`);
            checkbox.checked = true;

            // Trigger both Livewire and native change events
            checkbox.dispatchEvent(new Event('change', { bubbles: true }));
            checkbox.dispatchEvent(new Event('input', { bubbles: true }));

            // Call the existing toggleOgCustomFields function if available
            if (typeof window.toggleOgCustomFields === 'function') {
                console.log(`✅ Calling toggleOgCustomFields function for ${language}...`);
                window.toggleOgCustomFields(checkbox, language);
            }

            // Manual field showing as fallback
            setTimeout(() => {
                const customFields = document.getElementById(`og_custom_fields_${language}`);
                if (customFields) {
                    customFields.style.display = 'block';
                    customFields.style.maxHeight = 'none';
                    customFields.style.overflow = 'visible';
                    customFields.classList.remove('d-none');
                    console.log(`✅ OG custom fields manually shown for ${language}`);
                }
            }, 100);

            console.log(`✅ OG custom fields enabled successfully for ${language}`);
        } else {
            console.log(`ℹ️ OG custom fields already enabled for ${language}`);
        }
    }

    // Get current active language
    function getCurrentActiveLanguage() {
        // Try to get from AI recommendations button data-language
        const aiButton = document.querySelector('.ai-seo-recommendations-btn[data-language]');
        if (aiButton) {
            return aiButton.getAttribute('data-language');
        }

        // Try to get from active language button
        const activeLanguageBtn = document.querySelector('.language-switch-btn.text-primary, .language-switch-btn.active');
        if (activeLanguageBtn) {
            return activeLanguageBtn.getAttribute('data-language') ||
                   activeLanguageBtn.textContent.trim().toLowerCase();
        }

        // Try to get from visible content section
        const activeContent = document.querySelector('.seo-language-content[style*="display: block"]');
        if (activeContent) {
            return activeContent.getAttribute('data-language');
        }

        // Try to get from Livewire component data (tenant languages)
        if (window.livewire && window.Livewire.all().length > 0) {
            const component = window.Livewire.all().find(c => c.get('currentLanguage'));
            if (component) {
                return component.get('currentLanguage');
            }
        }

        // Try to get tenant default locale from meta tag or global config
        const metaDefaultLocale = document.querySelector('meta[name="tenant-default-locale"]');
        if (metaDefaultLocale) {
            return metaDefaultLocale.getAttribute('content');
        }

        // Try from global JS tenant config if available
        if (typeof window.tenantConfig !== 'undefined' && window.tenantConfig.default_locale) {
            return window.tenantConfig.default_locale;
        }

        // Try from page language detection
        const htmlLang = document.documentElement.lang;
        if (htmlLang && htmlLang.length >= 2) {
            return htmlLang.substring(0, 2);
        }

        // Final fallback - use tenant system default (typically 'tr')
        return getTenantSystemDefaultLanguage();
    }

    // Get tenant system default language (integrated with tenant system)
    function getTenantSystemDefaultLanguage() {
        // Check if tenant default is available in global scope
        if (typeof window.APP_TENANT_DEFAULT_LOCALE !== 'undefined') {
            return window.APP_TENANT_DEFAULT_LOCALE;
        }

        // Check available languages to find default
        const availableLanguages = getAvailableTenantLanguages();
        if (availableLanguages.length > 0) {
            // First language is usually the default in tenant system
            return availableLanguages[0];
        }

        // Ultimate fallback
        return 'tr';
    }

    // Get available tenant languages from DOM or component
    function getAvailableTenantLanguages() {
        // Try to get from Livewire component
        if (window.Livewire && window.Livewire.all().length > 0) {
            const component = window.Livewire.all().find(c => c.get('availableLanguages'));
            if (component) {
                return component.get('availableLanguages') || [];
            }
        }

        // Try to get from language switch buttons
        const langButtons = document.querySelectorAll('.language-switch-btn[data-language]');
        if (langButtons.length > 0) {
            return Array.from(langButtons).map(btn => btn.getAttribute('data-language'));
        }

        // Fallback to common tenant languages
        return ['tr', 'en', 'ar'];
    }

    // Extract language from field target (seoDataCache.tr.og_title -> tr)
    function extractLanguageFromFieldTarget(fieldTarget) {
        const match = fieldTarget.match(/seoDataCache\.([a-z]{2})\./);
        return match ? match[1] : getCurrentActiveLanguage();
    }
    
    // Get user-friendly field names
    function getFieldDisplayName(fieldTarget) {
        const displayNames = {
            'seo_title': 'Meta Başlık',
            'seo_description': 'Meta Açıklama',
            'content_type': 'İçerik Türü',
            'og_title': 'Sosyal Medya Başlığı',
            'og_description': 'Sosyal Medya Açıklaması',
            'priority_score': 'SEO Önceliği'
        };
        return displayNames[fieldTarget] || fieldTarget;
    };
    
    // APPLY ALL functionality
    window.applyAllRecommendations = function() {
        console.log('🔥 Applying all #1 recommendations...');
        
        const recommendationItems = document.querySelectorAll('.recommendation-item');
        let appliedCount = 0;
        
        recommendationItems.forEach(item => {
            const firstAlternative = item.querySelector('.form-check:first-child .form-check-label[onclick]');
            if (firstAlternative) {
                const onclickAttr = firstAlternative.getAttribute('onclick');
                if (onclickAttr) {
                    // Extract parameters from onclick
                    const match = onclickAttr.match(/applyAlternativeDirectly\('([^']+)',\s*'([^']+)'/);
                    if (match) {
                        const fieldTarget = match[1];
                        const value = match[2].replace(/\\'/g, "'");
                        applyAlternativeDirectly(fieldTarget, value, firstAlternative);
                        appliedCount++;
                    }
                }
            }
        });
        
        showSuccess(appliedCount + ' öneri otomatik uygulandı!');
    };
    
    // TOGGLE ALL CHECKBOXES functionality
    window.toggleAllCheckboxes = function() {
        console.log('🔄 Toggling all checkboxes...');
        
        const checkboxes = document.querySelectorAll('.ai-recommendation-checkbox');
        if (checkboxes.length === 0) {
            showError('Öneri bulunamadı');
            return;
        }
        
        // Check if all are currently selected
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        
        // Toggle all checkboxes
        checkboxes.forEach(cb => {
            cb.checked = !allChecked;
        });
        
        // Update apply button
        updateApplyButton();
        
        // Update button text
        const toggleButton = document.querySelector('button[onclick="toggleAllCheckboxes()"]');
        if (toggleButton) {
            toggleButton.innerHTML = allChecked ? 
                '<i class="fas fa-check-square me-1"></i>Tümünü Seç' : 
                '<i class="fas fa-square me-1"></i>Seçimi Kaldır';
        }
        
        const action = allChecked ? 'kaldırıldı' : 'seçildi';
        showSuccess(`${checkboxes.length} öneri ${action}`);
    };
    
    // Auto-load AI recommendations if they exist
    async function autoLoadRecommendations() {
        console.log('🔍 Checking for existing AI recommendations...');

        // Skip if we don't have a page ID
        if (!window.currentModelId) {
            console.log('ℹ️ No page ID found, skipping auto-load');
            return;
        }

        try {
            const formData = {
                feature_slug: 'seo-smart-recommendations',
                form_content: {},
                language: 'tr',
                page_id: window.currentModelId
            };

            const response = await fetch('/admin/seo/ai/recommendations', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(formData)
            });

            if (response.ok) {
                const result = await response.json();

                // Only display if recommendations exist and came from cache (previously saved)
                if (result.success && result.data && result.data.from_cache && result.data.recommendations && result.data.recommendations.length > 0) {
                    console.log('✅ Found existing recommendations, displaying automatically...');

                    // Show the recommendations section
                    const section = document.getElementById('aiSeoRecommendationsSection_tr');
                    if (section) {
                        section.style.display = 'block';
                        displayRecommendations(result.data, 'tr');
                        console.log('✅ AI recommendations auto-loaded successfully');
                    }
                } else {
                    console.log('ℹ️ No existing recommendations found or not from cache');
                }
            }
        } catch (error) {
            console.log('ℹ️ Auto-load check failed (normal for new pages):', error.message);
        }
    }

    // Initialize the system
    function init() {
        console.log('🚀 AI SEO Integration system başlatılıyor...');
        console.log('🔍 DOM ready state:', document.readyState);

        // Immediate attachment
        attachButtonListeners();

        // DOM ready fallback
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', attachButtonListeners);
        }

        // Delayed fallback for dynamic content
        setTimeout(attachButtonListeners, 500);

        // Window load fallback
        window.addEventListener('load', attachButtonListeners);

        // ===== A1 PATTERN: AUTO-APPLY EVENT LISTENER =====
        // AI Recommendations tamamlandığında otomatik uygula
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('aiRecommendationsCompleted', (event) => {
                console.log('🎯 aiRecommendationsCompleted event alındı, otomatik uygulama başlatılıyor');
                console.log('🔍 Event data:', event);

                // Event data'yı farklı yollarla extract etmeye çalış
                let recommendationsData = null;

                // Method 1: Direct access
                if (event.recommendationsData) {
                    recommendationsData = event.recommendationsData;
                    console.log('✅ Found via event.recommendationsData');
                }
                // Method 2: Array access
                else if (Array.isArray(event) && event[0]?.recommendationsData) {
                    recommendationsData = event[0].recommendationsData;
                    console.log('✅ Found via event[0].recommendationsData');
                }
                // Method 3: Direct array access
                else if (Array.isArray(event) && event[0]) {
                    recommendationsData = event[0];
                    console.log('✅ Using event[0] directly');
                }
                // Method 4: Direct event
                else if (event && typeof event === 'object') {
                    recommendationsData = event;
                    console.log('✅ Using event directly');
                }

                console.log('🔍 Final recommendationsData:', recommendationsData);

                if (recommendationsData && (recommendationsData.data || recommendationsData.recommendations)) {
                    // Extract the actual data
                    const actualData = recommendationsData.data || recommendationsData;
                    console.log('🔍 Actual data to use:', actualData);

                    // Kısa delay ile otomatik uygula (UI render'ının bitmesini bekle)
                    setTimeout(() => {
                        window.autoApplyFirstAlternatives(actualData);
                    }, 500);
                } else {
                    console.warn('❌ aiRecommendationsCompleted event\'inde recommendationsData bulunamadı');
                }
            });

            // Language changed events için re-attach
            Livewire.on('refreshTabs', () => {
                console.log('🔄 refreshTabs event - re-attaching button listeners');
                setTimeout(attachButtonListeners, 100);
            });

            Livewire.on('tabsRefreshed', () => {
                console.log('🔄 tabsRefreshed event - re-attaching button listeners');
                setTimeout(attachButtonListeners, 100);
            });
        });

        // Auto-loading is now handled by PHP/Blade template
        // setTimeout(() => {
        //     autoLoadRecommendations();
        // }, 1000);

        // ===== AUTO-APPLY EVENT LISTENER =====
        // AI Recommendations tamamlandığında otomatik uygula
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('aiRecommendationsCompleted', (event) => {
                console.log('🎯 aiRecommendationsCompleted event alındı, otomatik uygulama başlatılıyor');
                console.log('🔍 Event data:', event);

                // Event data'yı farklı yollarla extract etmeye çalış
                let recommendationsData = null;

                // Method 1: Direct access
                if (event.recommendationsData) {
                    recommendationsData = event.recommendationsData;
                    console.log('✅ Found via event.recommendationsData');
                }
                // Method 2: Array access
                else if (Array.isArray(event) && event[0]?.recommendationsData) {
                    recommendationsData = event[0].recommendationsData;
                    console.log('✅ Found via event[0].recommendationsData');
                }
                // Method 3: Direct array access
                else if (Array.isArray(event) && event[0]) {
                    recommendationsData = event[0];
                    console.log('✅ Using event[0] directly');
                }
                // Method 4: Direct event
                else if (event && typeof event === 'object') {
                    recommendationsData = event;
                    console.log('✅ Using event directly');
                }

                console.log('🔍 Final recommendationsData:', recommendationsData);

                if (recommendationsData && (recommendationsData.data || recommendationsData.recommendations)) {
                    // Extract the actual data
                    const actualData = recommendationsData.data || recommendationsData;
                    console.log('🔍 Actual data to use:', actualData);

                    // Kısa delay ile otomatik uygula (UI render'ının bitmesini bekle)
                    setTimeout(() => {
                        window.autoApplyFirstAlternatives(actualData);
                    }, 500);
                } else {
                    console.warn('❌ aiRecommendationsCompleted event\'inde recommendationsData bulunamadı');
                    console.warn('❌ Event structure:', Object.keys(event || {}));
                }
            });
        });

        console.log('✅ AI SEO Integration system hazır!');

        // 🔥 DEBUG: Overlay sistem test fonksiyonu ekle
        window.testAIOverlay = function(type = 'ai-recommendations') {
            console.log(`🧪 TEST: AI Overlay sistemi test ediliyor - Type: ${type}`);
            showInlineLoadingOverlay(type);

            // 3 saniye sonra overlay'i kapat
            setTimeout(() => {
                hideInlineLoading('seoUniversalResults');
                console.log('✅ TEST: Overlay kapatıldı');
            }, 3000);
        };
        console.log('🧪 DEBUG: window.testAIOverlay() fonksiyonu hazır!');

        // DEBUG: Test Button Click Functionality
        window.testButtonClicks = function() {
            console.log('🧪 TEST: AI Button Click Functionality');
            const buttons = document.querySelectorAll('.ai-seo-comprehensive-btn, .ai-seo-recommendations-btn');
            console.log('🔍 Found buttons:', buttons.length);

            buttons.forEach((btn, index) => {
                console.log(`🔍 Button ${index + 1}:`, {
                    classes: btn.className,
                    text: btn.textContent.trim(),
                    hasClickEvent: btn.onclick !== null,
                    dataAttributes: {
                        language: btn.getAttribute('data-language'),
                        seoFeature: btn.getAttribute('data-seo-feature')
                    }
                });
            });

            return { buttonCount: buttons.length, buttons: Array.from(buttons) };
        };

        // DEBUG: Force re-attach button listeners
        window.forceReattachButtons = function() {
            console.log('🔄 FORCE: Re-attaching button listeners');
            attachButtonListeners();
        };

        console.log('🧪 DEBUG: window.testButtonClicks() ve window.forceReattachButtons() fonksiyonları hazır!');
    }

    // AI MODAL-STYLE INLINE OVERLAY SYSTEM
    function showInlineLoadingOverlay(cleanupTarget) {
        console.log(`🎯 showInlineLoadingOverlay çağırıldı - Target: ${cleanupTarget}`);

        // Container'ı bul - artık her zaman orada olmalı
        const container = document.getElementById('seoUniversalResults');
        if (!container) {
            console.error('❌ seoUniversalResults container bulunamadı! Blade template\'te olmalı.');
            return;
        }

        // Önce eski içerikleri temizle
        cleanupExistingSeoAreas(cleanupTarget);

        // AI Modal tarzında overlay oluştur
        const overlay = document.createElement('div');
        overlay.className = 'inline-overlay-wrapper';
        overlay.innerHTML = `
            <div class="card mt-3" style="border: 1px solid #e6e7e9; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);">
                <div class="card-body text-center py-5">
                    <div class="overlay-content">
                        <div class="mb-4 text-center">
                            <h4 class="fw-normal mb-2">🤖 ${getOverlayTitle(cleanupTarget)}</h4>
                            <p class="text-muted mb-0">${getOverlaySubtitle(cleanupTarget)}</p>
                        </div>
                        <div class="progress mb-2" style="height: 6px;">
                            <div class="progress-bar progress-bar-indeterminate bg-primary progress-bar-animated"></div>
                        </div>
                        <div class="text-center text-muted small">
                            <i class="fas fa-cog fa-spin me-2"></i>İşleniyor...
                        </div>
                    </div>
                </div>
            </div>
        `;

        container.appendChild(overlay);
        console.log('✅ AI Modal-style inline overlay gösterildi');
    }

    function showInlineLoadingOverlayRecommendations() {
        console.log(`🎯 showInlineLoadingOverlayRecommendations çağırıldı`);

        // Container'ı bul - artık her zaman orada olmalı
        const container = document.getElementById('aiRecommendationsContainer');
        if (!container) {
            console.error('❌ aiRecommendationsContainer bulunamadı! Blade template\'te olmalı.');
            return;
        }

        // Önce eski içerikleri temizle
        cleanupExistingSeoAreas('ai-recommendations');

        // AI Modal tarzında overlay oluştur
        const overlay = document.createElement('div');
        overlay.className = 'inline-overlay-wrapper';
        overlay.innerHTML = `
            <div class="card mt-3" style="border: 1px solid #e6e7e9; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);">
                <div class="card-body text-center py-5">
                    <div class="overlay-content">
                        <div class="mb-4 text-center">
                            <h4 class="fw-normal mb-2">🤖 AI SEO Önerileri</h4>
                            <p class="text-muted mb-0">Kişiselleştirilmiş SEO önerileri yapay zeka tarafından oluşturuluyor</p>
                        </div>
                        <div class="progress mb-2" style="height: 6px;">
                            <div class="progress-bar progress-bar-indeterminate bg-primary progress-bar-animated"></div>
                        </div>
                        <div class="text-center text-muted small">
                            <i class="fas fa-cog fa-spin me-2"></i>İşleniyor...
                        </div>
                    </div>
                </div>
            </div>
        `;

        container.appendChild(overlay);
        console.log('✅ AI Recommendations Modal-style inline overlay gösterildi');
    }

    function hideInlineLoading(containerId) {
        const container = document.getElementById(containerId);
        if (container) {
            // Sadece overlay wrapper'ı kaldır, container'ı bırak
            const overlay = container.querySelector('.inline-overlay-wrapper');
            if (overlay) {
                overlay.remove();
                console.log('✅ Inline loading overlay kaldırıldı');
            }
        }
    }

    // ===== AUTO-APPLY FIRST ALTERNATIVES FUNCTION =====
    window.autoApplyFirstAlternatives = function(recommendations) {
        console.log('🎯 Auto-applying first alternatives for each recommendation type');

        if (!recommendations || !recommendations.recommendations || !Array.isArray(recommendations.recommendations)) {
            console.warn('❌ No valid recommendations data for auto-apply');
            return;
        }

        let appliedCount = 0;
        recommendations.recommendations.forEach((recommendation) => {
            // Her recommendation için ilk alternatifi al
            if (recommendation.alternatives && Array.isArray(recommendation.alternatives) && recommendation.alternatives.length > 0) {
                const firstAlternative = recommendation.alternatives[0];
                const fieldTarget = recommendation.field_target;

                if (fieldTarget && firstAlternative && firstAlternative.value) {
                    console.log(`📝 Auto-applying: ${fieldTarget} = "${firstAlternative.value}"`);

                    // applyAlternativeDirectly fonksiyonunu kullan
                    if (window.applyAlternativeDirectly) {
                        window.applyAlternativeDirectly(fieldTarget, firstAlternative.value);
                        appliedCount++;

                        // OG alanlarına veri yazıldığında toggle'ı otomatik aç
                        if (fieldTarget && (fieldTarget.includes('og_') || fieldTarget.includes('social'))) {
                            const language = extractLanguageFromFieldTarget(fieldTarget);
                            console.log(`🎯 OG field detected (${fieldTarget}), enabling custom fields for ${language}`);
                            enableOgCustomFields(language);
                        }
                    } else {
                        console.error('❌ window.applyAlternativeDirectly function not found');
                    }
                }
            }
        });

        if (appliedCount > 0) {
            showSuccess(`✨ ${appliedCount} AI önerisi otomatik olarak uygulandı! Dilediğiniz alternatife tıklayarak değiştirebilirsiniz.`);
        }
    }

    // 🎯 TEMPLATE-BASED AUTO-APPLY: DOM'dan 1. sıra önerilerini al ve uygula
    window.applyFirstAlternativesFromTemplate = function() {
        console.log('🔍 Template-based auto-apply başlıyor...');

        // Template'deki .active olan list-group-item'leri bul
        const activeItems = document.querySelectorAll('.list-group-item.active[onclick]');
        console.log('🔍 Bulunan aktif öneri sayısı: ' + activeItems.length);

        let appliedCount = 0;

        activeItems.forEach((item, index) => {
            const onclickAttr = item.getAttribute('onclick');
            console.log('📝 Item ' + (index + 1) + ' onclick:', onclickAttr);

            // onclick'ten field ve value'yu parse et
            // Format: applyAlternativeDirectly('seoDataCache.tr.seo_title', 'Hikayemizi ve Bizi Tanıyın', this);
            const match = onclickAttr.match(/applyAlternativeDirectly\('([^']+)',\s*'([^']+)'/);

            if (match) {
                const fieldTarget = match[1];
                const value = match[2];

                console.log('✅ Parse başarılı: ' + fieldTarget + ' = "' + value + '"');

                // applyAlternativeDirectly fonksiyonunu çağır
                if (window.applyAlternativeDirectly) {
                    window.applyAlternativeDirectly(fieldTarget, value);
                    appliedCount++;
                    console.log('📝 Uygulandı: ' + fieldTarget);

                    // OG alanları dolduysa checkbox'ı otomatik işaretle
                    if (fieldTarget.includes('.og_title') || fieldTarget.includes('.og_description')) {
                        const lang = fieldTarget.split('.')[1]; // seoDataCache.tr.og_title -> tr

                        // Biraz bekle ve checkbox'ı kontrol et (DOM güncellenmesi için)
                        setTimeout(() => {
                            // Önce switch'in varlığını kontrol et ve yoksa oluştur
                            if (window.checkAndEnableSocialMedia) {
                                window.checkAndEnableSocialMedia(lang);
                            } else {
                                // Fallback
                                const checkbox = document.getElementById(`og_custom_${lang}`);
                                const fieldsContainer = document.getElementById(`og_custom_fields_${lang}`);
                                if (checkbox && !checkbox.checked) {
                                    checkbox.checked = true;
                                    checkbox.dispatchEvent(new Event('change'));
                                    if (fieldsContainer) {
                                        fieldsContainer.style.display = 'block';
                                    }
                                    console.log('✅ Social media checkbox otomatik işaretlendi (fallback):', lang);
                                }
                            }
                        }, 500); // Timeout'u artırdık DOM'un tamamen güncellenmesi için
                    }
                } else {
                    console.error('❌ window.applyAlternativeDirectly function not found');
                }
            } else {
                console.error('❌ onclick parse edilemedi:', onclickAttr);
            }
        });

        if (appliedCount > 0) {
            console.log('✅ Template-based auto-apply tamamlandı: ' + appliedCount + ' öneri uygulandı');
            showSuccess(`✨ ${appliedCount} AI önerisi otomatik olarak uygulandı! Dilediğiniz alternatife tıklayarak değiştirebilirsiniz.`);
        } else {
            console.warn('⚠️ Hiçbir öneri uygulanamadı');
        }
    };

    // Start the system
    init();

})();
