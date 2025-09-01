/**
 * AI SEO Integration System
 * Real AI-powered SEO functionality
 */

(function() {
    'use strict';
    
    // CSRF token for API calls
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    function attachButtonListeners() {
        const seoButtons = document.querySelectorAll('.ai-seo-comprehensive-btn, .ai-seo-recommendations-btn, .seo-generator-btn, .seo-suggestions-btn, [data-seo-feature], [data-action]');
        
        seoButtons.forEach((button) => {
            // Remove existing listeners
            const newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);
            
            newButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Check button type and handle accordingly
                if (this.classList.contains('ai-seo-comprehensive-btn') || 
                    this.getAttribute('data-seo-feature') === 'seo-comprehensive-audit') {
                    handleSeoAnalysis(this);
                    return;
                }
                
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
    
    // Real AI API handlers
    async function handleSeoRecommendations(button) {
        console.log('🚀 SEO RECOMMENDATIONS START');
        const language = button.getAttribute('data-language') || 'tr';
        
        try {
            // Show the recommendations section
            const section = document.getElementById(`aiSeoRecommendationsSection_${language}`);
            if (section) {
                section.style.display = 'block';
                
                // Show loading state
                const loading = section.querySelector('.ai-recommendations-loading');
                const content = section.querySelector('.ai-recommendations-content');
                const error = section.querySelector('.ai-recommendations-error');
                
                loading.style.display = 'block';
                content.style.display = 'none';
                error.style.display = 'none';
            }
            
            setButtonLoading(button, 'Öneriler Üretiliyor...');
            
            const collectedData = collectFormData();
            const formData = {
                feature_slug: 'seo-smart-recommendations',
                form_content: collectedData,
                language: language
            };
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
                displayRecommendations(result.data, language);
            } else {
                console.error('❌ API Error:', result.message);
                showRecommendationsError(result.message, language);
            }
        } catch (error) {
            console.error('💥 RECOMMENDATIONS ERROR:', error);
            console.error('💥 Error stack:', error.stack);
            showRecommendationsError('Bağlantı hatası: ' + error.message, language);
        } finally {
            resetButton(button, '<i class="fas fa-magic me-1"></i>AI Önerileri');
        }
    }
    
    async function handleSeoAnalysis(button) {
        console.log('🚀 SEO ANALYSIS START');
        try {
            setButtonLoading(button, 'Analiz Ediliyor...');
            
            const collectedData = collectFormData();
            console.log('🚨 DEBUG CHECKPOINT 1: collectFormData called');
            const formData = {
                feature_slug: 'seo-comprehensive-audit',
                form_content: collectedData,
                language: collectedData.language || 'tr'
            };
            console.log('📋 Form data:', formData);
            console.log('🚨 DEBUG CHECKPOINT 2: collected data keys:', Object.keys(collectedData));
            
            console.log('🔗 Sending request to:', '/admin/seo/ai/analyze');
            const response = await fetch('/admin/seo/ai/analyze', {
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
                displayComprehensiveAnalysis(result.data);
                
                // Blade accordion'unu da görünür hale getir - real-time
                setTimeout(() => {
                    // Force göster - PHP @if koşulunu JavaScript ile aşalım
                    const accordionSection = document.getElementById('seoAnalysisAccordion');
                    if (accordionSection) {
                        // Accordion'un kendisini göster
                        accordionSection.style.display = 'block';
                        accordionSection.classList.remove('d-none');
                        
                        // Parent card'ı da force göster (@if($hasAnalysisResults) için)
                        let parent = accordionSection.parentElement;
                        while (parent && parent !== document.body) {
                            if (parent.classList.contains('card') || parent.classList.contains('mt-3')) {
                                parent.style.display = 'block';
                                parent.classList.remove('d-none');
                                console.log('✅ Parent container görünür yapıldı:', parent.className);
                            }
                            parent = parent.parentElement;
                        }
                        
                        // En üstteki card container'ı spesifik olarak bul ve göster
                        const cardContainer = accordionSection.closest('.card.mt-3');
                        if (cardContainer) {
                            cardContainer.style.display = 'block';
                            cardContainer.classList.remove('d-none');
                            console.log('✅ Card container force gösterildi');
                        }
                        
                        console.log('✅ Blade accordion real-time gösterildi');
                    } else {
                        console.warn('⚠️ seoAnalysisAccordion bulunamadı - accordion henüz DOM\'a eklenmemiş olabilir');
                    }
                }, 500);
            } else {
                console.error('❌ API Error:', result.message);
                showError('Analiz sırasında hata: ' + result.message);
            }
        } catch (error) {
            console.error('💥 FULL ERROR:', error);
            console.error('💥 Error stack:', error.stack);
            showError('Bağlantı hatası: ' + error.message);
        } finally {
            resetButton(button, '<i class="fas fa-chart-bar me-1"></i>SEO Analizi');
        }
    }
    
    async function handleSeoGenerate(button) {
        console.log('🚀 SEO GENERATE START');
        try {
            setButtonLoading(button, 'Oluşturuluyor...');
            
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
                updateFormFields(result.data);
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
            resetButton(button, '<i class="fas fa-magic me-1"></i>SEO Oluştur');
        }
    }
    
    async function handleSeoSuggestions(button) {
        console.log('🚀 SEO SUGGESTIONS START');
        try {
            setButtonLoading(button, 'Öneriler Alınıyor...');
            
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
                displaySuggestions(result.data);
            } else {
                console.error('❌ API Error:', result.message);
                showError('Öneri alınırken hata: ' + result.message);
            }
        } catch (error) {
            console.error('💥 FULL ERROR:', error);
            console.error('💥 Error stack:', error.stack);
            showError('Bağlantı hatası: ' + error.message);
        } finally {
            resetButton(button, '<i class="fas fa-lightbulb me-1"></i>Öneriler');
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
    
    function setButtonLoading(button, text) {
        button.innerHTML = `<i class="fas fa-spinner fa-spin me-1"></i>${text}`;
        button.disabled = true;
    }
    
    function resetButton(button, originalHtml) {
        button.innerHTML = originalHtml;
        button.disabled = false;
    }
    
    // YENİ KOMPREHENSİF ANALİZ EKRANI
    function displayComprehensiveAnalysis(analysis) {
        console.log('🔍 COMPREHENSIVE ANALYSIS DEBUG START');
        console.log('📄 Full analysis object:', analysis);
        console.log('📄 analysis.data:', analysis.data);
        console.log('📄 analysis.detailed_scores:', analysis.detailed_scores);
        console.log('📄 analysis.metrics:', analysis.metrics);
        
        let panel = document.getElementById('seo-analysis-results-panel');
        if (!panel) {
            panel = document.createElement('div');
            panel.id = 'seo-analysis-results-panel';
            panel.className = 'card mt-3';
            
            const buttonRow = document.querySelector('.ai-seo-comprehensive-btn').parentElement;
            if (buttonRow) {
                buttonRow.insertAdjacentElement('afterend', panel);
            }
        }
        
        // Veri yapısını düzelt - analysis.data içinde asıl veriler var
        const actualData = analysis.data || analysis;
        console.log('🔧 Using actualData:', actualData);
        
        const score = actualData.overall_score || analysis.metrics?.overall_score || 0;
        let badgeClass = score >= 80 ? 'bg-success' : score >= 60 ? 'bg-warning' : 'bg-danger';
        
        panel.innerHTML = `
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    Kapsamlı SEO Analizi
                </h3>
                <button type="button" 
                        class="btn btn-outline-danger btn-sm"
                        onclick="if(confirm('SEO analizi verileri silinecek. Emin misiniz?')) { Livewire.dispatch('clearSeoAnalysis') }"
                        title="SEO analizi verilerini sıfırla">
                    <i class="fas fa-trash-alt me-1"></i>
                    Verileri Sıfırla
                </button>
            </div>
            <div class="card-body">
                <!-- GENEL SKOR -->
                <div class="row mb-4">
                    <div class="col-auto">
                        <div class="avatar avatar-xl ${badgeClass} text-white" style="font-size: 1.5rem; font-weight: bold;">
                            ${score}
                        </div>
                    </div>
                    <div class="col">
                        <h4>Genel SEO Skoru</h4>
                        <p class="text-secondary">${actualData.health_status || analysis.metrics?.health_status}</p>
                    </div>
                </div>
                
                <!-- SKOR DETAYLARI -->
                <div class="row g-3 mb-4">
                    ${['title', 'description', 'content', 'technical', 'social', 'priority'].map(key => {
                        const detailedScores = actualData.detailed_scores || analysis.detailed_scores || {};
                        const scoreData = detailedScores[key] || {};
                        const val = scoreData.score || scoreData.overall_score || 0;
                        const cls = val >= 80 ? 'success' : val >= 60 ? 'warning' : 'danger';
                        return `
                        <div class="col-md-6 col-lg-4">
                            <div class="card card-sm">
                                <div class="card-body p-2">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-fill">
                                            <div class="font-weight-medium">${key.replace('_score', '').toUpperCase()}</div>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-${cls}" style="width: ${val}%"></div>
                                            </div>
                                        </div>
                                        <div class="ms-2 text-${cls}">${val}/100</div>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    }).join('')}
                </div>
                
                <!-- OLUMLU YANLAR -->
                <div class="mb-4">
                    <h5 class="text-success"><i class="fas fa-check-circle me-2"></i>Güçlü Yanlar</h5>
                    <div class="list-group list-group-flush">
                        ${(actualData.strengths && actualData.strengths.length) ? actualData.strengths.map(item => `
                            <div class="list-group-item border-0 px-0 py-2">
                                <i class="fas fa-plus-circle text-success me-2"></i>${typeof item === 'string' ? item : (item.text || item.title || item.description || JSON.stringify(item))}
                            </div>
                        `).join('') : '<div class="text-muted">AI henüz yeni prompt formatını kullanmıyor - strengths eksik</div>'}
                    </div>
                </div>
                
                <!-- İYİLEŞTİRME ÖNERİLERİ -->
                <div class="mb-4">
                    <h5 class="text-warning"><i class="fas fa-exclamation-triangle me-2"></i>İyileştirme Alanları</h5>
                    <div class="list-group list-group-flush">
                        ${(actualData.improvements && actualData.improvements.length) ? actualData.improvements.map(item => `
                            <div class="list-group-item border-0 px-0 py-2">
                                <i class="fas fa-arrow-up text-warning me-2"></i>${typeof item === 'string' ? item : (item.text || item.title || item.description || JSON.stringify(item))}
                            </div>
                        `).join('') : '<div class="text-muted">AI henüz yeni prompt formatını kullanmıyor - improvements eksik</div>'}
                    </div>
                </div>
                
                <!-- EYLEM ÖNERİLERİ -->
                <div>
                    <h5 class="text-primary"><i class="fas fa-tasks me-2"></i>Öncelikli Eylemler</h5>
                    <div class="list-group list-group-flush">
                        ${(actualData.action_items && actualData.action_items.length) ? actualData.action_items.map((item, i) => `
                            <div class="list-group-item border-0 px-0 py-2">
                                <span class="badge bg-primary me-2">${i+1}</span>
                                <strong>${typeof item === 'string' ? item : (item.task || item.text || item.title || item.description || 'Eylem tanımı bulunamadı')}</strong>
                                ${typeof item === 'object' && item.urgency ? `<span class="badge bg-danger ms-2">${item.urgency}</span>` : ''}
                                ${typeof item === 'object' && item.area ? `<br><small class="text-muted">Alan: ${item.area}</small>` : ''}
                                ${typeof item === 'object' && item.expected_impact ? `<small class="text-muted"> • Etki: ${item.expected_impact}</small>` : ''}
                            </div>
                        `).join('') : '<div class="text-muted">AI henüz yeni prompt formatını kullanmıyor - action_items eksik</div>'}
                    </div>
                </div>
            </div>
        `;
    }
    
    function updateFormFields(data) {
        console.log('📝 GENERATE Results data:', data);
        
        // Create UNIQUE panel for generate
        let panel = document.getElementById('seo-generate-results-panel');
        if (!panel) {
            panel = document.createElement('div');
            panel.id = 'seo-generate-results-panel';
            panel.className = 'card mt-3';
            
            // Find the button row and put panel right after it
            const buttonRow = document.querySelector('.row .col-12:has(.seo-generator-btn)') ||
                             document.querySelector('.row:has(.seo-generator-btn)') ||
                             document.querySelector('.seo-generator-btn').closest('.row') ||
                             document.querySelector('.seo-generator-btn').parentElement;
            
            if (buttonRow) {
                buttonRow.insertAdjacentElement('afterend', panel);
            } else {
                // Fallback to button parent
                const anyButton = document.querySelector('.seo-generator-btn, .ai-seo-comprehensive-btn, .seo-suggestions-btn');
                if (anyButton) {
                    anyButton.parentElement.insertAdjacentElement('afterend', panel);
                }
            }
        }
        
        // Professional Generate Panel
        let html = `
            <div class="card-header">
                <h3 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-magic me-2">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M5 12l2 0l10 -10l-2 -2l-10 10l0 2z"/>
                        <path d="M19 4l2 2"/>
                        <path d="M9 7h4"/>
                        <path d="M9 11h4"/>
                    </svg>
                    SEO İçerik Oluşturma
                </h3>
            </div>
            <div class="card-body">`;
        
        if (data && (data.meta_title || data.meta_description || data.generated_content)) {
            html += `<div class="row g-3">`;
            
            if (data.meta_title) {
                html += `
                    <div class="col-12">
                        <div class="form-label">Oluşturulan Meta Title</div>
                        <div class="input-group">
                            <input type="text" class="form-control" value="${data.meta_title}" readonly>
                            <button class="btn btn-outline-primary" onclick="navigator.clipboard.writeText('${data.meta_title}')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>`;
            }
            
            if (data.meta_description) {
                html += `
                    <div class="col-12">
                        <div class="form-label">Oluşturulan Meta Description</div>
                        <div class="input-group">
                            <textarea class="form-control" rows="3" readonly>${data.meta_description}</textarea>
                            <button class="btn btn-outline-primary" onclick="navigator.clipboard.writeText('${data.meta_description}')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>`;
            }
            
            if (data.generated_content) {
                html += `
                    <div class="col-12">
                        <div class="form-label">Oluşturulan İçerik</div>
                        <div class="card">
                            <div class="card-body">
                                ${typeof data.generated_content === 'object' ? JSON.stringify(data.generated_content, null, 2) : data.generated_content}
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-outline-primary btn-sm" onclick="navigator.clipboard.writeText('${typeof data.generated_content === 'object' ? JSON.stringify(data.generated_content, null, 2) : data.generated_content}')">
                                    <i class="fas fa-copy me-1"></i>Kopyala
                                </button>
                            </div>
                        </div>
                    </div>`;
            }
            
            html += `</div>`;
        } else {
            html += `
                <div class="empty">
                    <div class="empty-img"><img src="./static/illustrations/undraw_printing_invoices_5r4r.svg" height="128" alt="">
                    </div>
                    <p class="empty-title">İçerik oluşturulamadı</p>
                    <p class="empty-subtitle text-secondary">
                        AI içerik oluşturma işlemi tamamlanamadı veya beklenmedik bir hata oluştu.
                    </p>
                </div>`;
        }
        
        html += `</div>`;
        panel.innerHTML = html;
        console.log('✅ Professional Generate Results shown');
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
        
        // Create UNIQUE panel for suggestions
        let panel = document.getElementById('seo-suggestions-results-panel');
        if (!panel) {
            panel = document.createElement('div');
            panel.id = 'seo-suggestions-results-panel';
            panel.className = 'card mt-3';
            
            // Find the button row and put panel right after it
            const buttonRow = document.querySelector('.row .col-12:has(.seo-suggestions-btn)') ||
                             document.querySelector('.row:has(.seo-suggestions-btn)') ||
                             document.querySelector('.seo-suggestions-btn').closest('.row') ||
                             document.querySelector('.seo-suggestions-btn').parentElement;
            
            if (buttonRow) {
                buttonRow.insertAdjacentElement('afterend', panel);
            } else {
                // Fallback to button parent
                const anyButton = document.querySelector('.seo-suggestions-btn, .ai-seo-comprehensive-btn, .seo-generator-btn');
                if (anyButton) {
                    anyButton.parentElement.insertAdjacentElement('afterend', panel);
                }
            }
        }
        
        // Professional Suggestions Panel
        let html = `
            <div class="card-header">
                <h3 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-lightbulb me-2">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M9 21h6"/>
                        <path d="M6 21v-1a1 1 0 0 1 1 -1h10a1 1 0 0 1 1 1v1"/>
                        <path d="M12 17v-11"/>
                        <path d="M12 6a4 4 0 1 0 4 4"/>
                    </svg>
                    SEO Önerileri
                </h3>
            </div>
            <div class="card-body">`;
        
        // SUGGESTIONS YAPISINI PARSE ET - backend'den obje olarak geliyor
        let hasContent = false;
        
        if (data && data.suggestions && typeof data.suggestions === 'object') {
            html += `<div class="row g-3">`;
            
            // Title Suggestions
            if (data.suggestions.title_suggestions && Array.isArray(data.suggestions.title_suggestions)) {
                html += `
                    <div class="col-12">
                        <h5><i class="fas fa-heading me-2"></i>Title Önerileri</h5>
                        <div class="list-group">`;
                data.suggestions.title_suggestions.forEach((suggestion, index) => {
                    html += `
                        <div class="list-group-item">
                            <span class="badge bg-blue me-2">${index + 1}</span>
                            ${suggestion}
                        </div>`;
                });
                html += `</div></div>`;
                hasContent = true;
            }
            
            // Description Suggestions  
            if (data.suggestions.description_suggestions && Array.isArray(data.suggestions.description_suggestions)) {
                html += `
                    <div class="col-12">
                        <h5><i class="fas fa-file-text me-2"></i>Description Önerileri</h5>
                        <div class="list-group">`;
                data.suggestions.description_suggestions.forEach((suggestion, index) => {
                    html += `
                        <div class="list-group-item">
                            <span class="badge bg-green me-2">${index + 1}</span>
                            ${suggestion}
                        </div>`;
                });
                html += `</div></div>`;
                hasContent = true;
            }
            
            // Content Improvements
            if (data.suggestions.content_improvements && Array.isArray(data.suggestions.content_improvements)) {
                html += `
                    <div class="col-12">
                        <h5><i class="fas fa-tools me-2"></i>İçerik İyileştirmeleri</h5>
                        <div class="list-group">`;
                data.suggestions.content_improvements.forEach((suggestion, index) => {
                    html += `
                        <div class="list-group-item">
                            <span class="badge bg-orange me-2">${index + 1}</span>
                            ${suggestion}
                        </div>`;
                });
                html += `</div></div>`;
                hasContent = true;
            }
            
            html += `</div>`;
        }
        
        if (!hasContent) {
            html += `
                <div class="empty">
                    <div class="empty-img"><img src="./static/illustrations/undraw_printing_invoices_5r4r.svg" height="128" alt="">
                    </div>
                    <p class="empty-title">Öneri bulunamadı</p>
                    <p class="empty-subtitle text-secondary">
                        AI önerileri alınamadı veya beklenmedik bir hata oluştu.
                    </p>
                </div>`;
        }
        
        html += `</div>`;
        panel.innerHTML = html;
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
        
        const section = document.getElementById(`aiSeoRecommendationsSection_${language}`);
        if (!section) return;
        
        const loading = section.querySelector('.ai-recommendations-loading');
        const content = section.querySelector('.ai-recommendations-content');
        const list = section.querySelector('.ai-recommendations-list');
        const count = section.querySelector('.ai-recommendations-count');
        
        // Hide loading, show content
        loading.style.display = 'none';
        content.style.display = 'block';
        
        // Parse recommendations data
        const recommendations = data.recommendations || [];
        console.log('📝 Parsed recommendations:', recommendations);
        
        // Update count
        if (count) {
            count.textContent = recommendations.length;
        }
        
        // Add Apply All button and controls at the top
        let controlsHTML = `
            <div class="d-flex justify-content-between align-items-center mb-3 p-3 border rounded">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-primary btn-sm" onclick="applyAllRecommendations()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-1">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l2 0l10 -10l-2 -2l-10 10l0 2z"/>
                            <path d="M19 4l2 2"/>
                        </svg>
                        Tümünü Uygula (#1 Seçenekleri)
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-sm ai-apply-selected-recommendations" disabled onclick="applySelectedRecommendations(this)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-1">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M9 7m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"/>
                            <path d="M9 17l0 -10"/>
                            <path d="M19 16.5c0 .667 -.167 1.167 -.5 1.5s-.833 .333 -1.5 .333s-1.167 -.167 -1.5 -.5s-.333 -.833 -.333 -1.5c0 -.667 .167 -1.167 .5 -1.5s.833 -.333 1.5 -.333s1.167 .167 1.5 .5s.333 .833 .333 1.5z"/>
                        </svg>
                        Seçilenleri Uygula
                    </button>
                </div>
                <small class="text-muted">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon me-1">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/>
                        <path d="M12 9h.01"/>
                        <path d="M11 12h1v4h1"/>
                    </svg>
                    Radio butonları tek seçim, direkt tıklama da desteklenir
                </small>
            </div>
        `;
        
        // Generate recommendation items with alternatives
        let recommendationsHTML = controlsHTML;
        recommendations.forEach((rec, index) => {
            const id = `rec_${index}`;
            const hasAlternatives = rec.alternatives && rec.alternatives.length > 0;
            
            recommendationsHTML += `
                <div class="card mb-3 ai-recommendation-item" data-recommendation='${JSON.stringify(rec)}'>
                    <div class="card-body p-3">
                        <div class="d-flex align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-1">
                                        <i class="fas ${rec.type === 'title' ? 'fa-heading' : rec.type === 'description' ? 'fa-align-left' : rec.type === 'keywords' ? 'fa-tags' : 'fa-magic'} me-2"></i>
                                        ${rec.title || rec.field || 'SEO Önerisi'}
                                    </h6>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-${rec.priority === 'high' ? 'danger' : rec.priority === 'medium' ? 'warning' : 'success'}">
                                            ${rec.priority === 'high' ? 'Yüksek' : rec.priority === 'medium' ? 'Orta' : 'Düşük'}
                                        </span>
                                        ${rec.impact_score ? `<small class="text-success"><i class="fas fa-chart-line me-1"></i>${rec.impact_score}%</small>` : ''}
                                    </div>
                                </div>
                                <p class="text-muted mb-3">${rec.description || rec.reason || ''}</p>
                                
                                ${hasAlternatives ? `
                                    <div class="alternatives-compact">
                                        <small class="text-muted d-block mb-2">
                                            <i class="fas fa-magic me-1"></i>${rec.alternatives.length} AI Alternatifi
                                        </small>
                                        <div class="alternatives-list">
                                            <div class="btn-group${rec.alternatives.length > 3 ? '-vertical' : ''} ${rec.alternatives.length > 3 ? 'w-100' : 'w-100'}" role="group">
                                                ${rec.alternatives.map((alt, altIndex) => `
                                                    <input type="radio" class="btn-check alternative-radio" 
                                                           name="alt_${rec.type}" value="${alt.id}" id="alt_${id}_${altIndex}" 
                                                           autocomplete="off"
                                                           onchange="updateApplyButton()" 
                                                           onclick="applyAlternativeDirectly('${rec.field_target}', '${alt.value.replace(/'/g, "\\'")}', this)">
                                                    <label for="alt_${id}_${altIndex}" type="button" class="btn${rec.alternatives.length > 3 ? '' : ' flex-fill'} text-start">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <div class="flex-grow-1 min-w-0">
                                                                <div class="fw-medium small text-truncate mb-1">${alt.label}</div>
                                                                <div class="text-muted small text-truncate" style="font-family: 'SF Mono', Monaco, 'Cascadia Code', monospace;">
                                                                    ${alt.value.length > 50 ? alt.value.substring(0, 50) + '...' : alt.value}
                                                                </div>
                                                            </div>
                                                            <span class="badge bg-azure ms-2">${alt.score}</span>
                                                        </div>
                                                    </label>
                                                `).join('')}
                                            </div>
                                        </div>
                                    </div>
                                ` : `
                                    <div class="ai-single-suggestion p-2 bg-light rounded">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <small class="text-muted"><i class="fas fa-robot me-1"></i>AI Önerisi</small>
                                            <span class="badge bg-primary">${rec.impact_score || 85}%</span>
                                        </div>
                                        <code class="text-primary small">${rec.value || rec.suggested_value || ''}</code>
                                    </div>
                                `}
                            </div>
                        </div>
                    </div>
                </div>`;
        });
        
        if (list) {
            list.innerHTML = recommendationsHTML;
        }
        
        // Update apply button state
        updateApplyButton();
        
        console.log('✅ Recommendations displayed successfully');
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
    
    // CLICK-TO-FILL functionality for SEO recommendations
    window.applyAlternativeDirectly = function(fieldTarget, value, element) {
        console.log('🎯 Direct apply:', fieldTarget, value);
        
        // Enhanced field mappings with wire:model targeting
        const fieldMappings = {
            'seo_title': 'input[wire\\:model="seoDataCache.tr.seo_title"]',
            'seo_description': 'textarea[wire\\:model="seoDataCache.tr.seo_description"]', 
            'content_type': 'select[wire\\:model="seoDataCache.tr.content_type"]',
            'og_title': 'input[wire\\:model="seoDataCache.tr.og_title"]',
            'og_description': 'textarea[wire\\:model="seoDataCache.tr.og_description"]',
            'priority_score': 'input[wire\\:model="seoDataCache.tr.priority_score"]'
        };
        
        const selector = fieldMappings[fieldTarget];
        if (!selector) {
            showError('Alan bulunamadı: ' + fieldTarget);
            return;
        }
        
        const field = document.querySelector(selector);
        if (!field) {
            showError('Form alanı bulunamadı: ' + selector);
            return;
        }
        
        // Special handling for content_type (select vs custom input)
        if (fieldTarget === 'content_type') {
            handleContentTypeSelection(value);
        } else {
            // Update field value
            field.value = value;
            
            // Trigger Livewire update
            field.dispatchEvent(new Event('input', { bubbles: true }));
            field.dispatchEvent(new Event('change', { bubbles: true }));
        }
        
        // Auto-enable OG custom fields if OG fields are filled
        if (fieldTarget === 'og_title' || fieldTarget === 'og_description') {
            enableOgCustomFields();
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
        
        // Reset visual feedback after 2 seconds
        setTimeout(() => {
            field.style.backgroundColor = '';
            field.style.border = '';
        }, 2000);
        
        showSuccess('Öneri uygulandı: ' + getFieldDisplayName(fieldTarget));
    };
    
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
    function enableOgCustomFields() {
        const checkbox = document.querySelector('input[wire\\:model="seoDataCache.tr.og_custom_enabled"]');
        if (checkbox && !checkbox.checked) {
            checkbox.checked = true;
            checkbox.dispatchEvent(new Event('change', { bubbles: true }));
            
            // Show custom fields
            const customFields = document.getElementById('og_custom_fields_tr');
            if (customFields) {
                customFields.style.display = 'block';
                customFields.style.maxHeight = 'none';
                customFields.style.overflow = 'visible';
            }
        }
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
    
    // Initialize the system
    function init() {
        console.log('🚀 AI SEO Integration system başlatılıyor...');
        
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
        
        console.log('✅ AI SEO Integration system hazır!');
    }
    
    // Start the system
    init();
    
})();