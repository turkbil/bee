/* SEO Tabs JavaScript - ZERO API SYSTEM */

// SEO Tabs Namespace - Çakışma önleyici
window.SeoTabsSystem = window.SeoTabsSystem || {};

if (!window.SeoTabsSystem.initialized) {
    // SEO Cache - Backend'den gelen veriler (ZERO API CALLS)
    window.SeoTabsSystem.cache = new Map();
    window.SeoTabsSystem.initialized = false;
    
    // Cache'i hemen initialize et (event bekleme)
    initializeSeoCache();
    
    // Event listener'ları hemen bağla
    bindSeoEventsImmediate();

// Backend verilerini cache'e yükle (ZERO API CALLS)
function initializeSeoCache() {
    if (window.SeoTabsSystem.initialized || !window.allLanguagesSeoData) return;
    
    for (const [language, seoData] of Object.entries(window.allLanguagesSeoData)) {
        const cacheKey = `Page_${window.currentPageId}_${language}`;
        window.SeoTabsSystem.cache.set(cacheKey, {
            success: true,
            seoData: seoData
        });
    }
    
    window.SeoTabsSystem.initialized = true;
}

// Debounce için global variable
let isLanguageUpdateInProgress = false;
let isUserTyping = false; // CRITICAL FIX: User input sırasında cache update engellemek için

// Global window'a isUserTyping export et
window.isUserTyping = isUserTyping;

// SEO Dil Değişimi Sistemi - ZERO API CALLS (DÜZELTİLMİŞ)
async function updateSeoDataForLanguage(language) {
    // CRITICAL FIX: User typing sırasında cache update yapma  
    if (window.isUserTyping) {
        console.log('⌨️ User yazıyor, cache update atlanıyor:', language);
        return;
    }
    
    // Debounce kontrolü
    if (isLanguageUpdateInProgress) {
        console.log('⏸️ Language update zaten devam ediyor, atlanıyor:', language);
        return;
    }
    
    isLanguageUpdateInProgress = true;
    console.log('🔍 updateSeoDataForLanguage cagrildi:', {
        language,
        currentPageId: window.currentPageId,
        cacheInitialized: window.SeoTabsSystem.initialized,
        globalCurrentLanguage: window.currentLanguage
    });
    
    // YENİ SAYFA İÇİN ÖZEL KONTROL - currentPageId null olabilir
    if (!language) {
        console.error('❌ Language eksik:', {
            currentPageId: window.currentPageId,
            language: language
        });
        isLanguageUpdateInProgress = false;
        return;
    }
    
    // Yeni sayfa ise (currentPageId null) özel işlem
    if (window.currentPageId === null || window.currentPageId === 'null') {
        console.log('🆕 Yeni sayfa için SEO dil değişimi:', language);
        
        // Global currentLanguage'ı güncelle
        window.currentLanguage = language;
        
        // AJAX ile session'a dil kaydet
        try {
            const response = await fetch('/admin/set-js-language', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({ language: language })
            });
            
            if (response.ok) {
                console.log('✅ Yeni sayfa için dil kaydedildi:', language);
                
                // YENİ SAYFA İÇİN SEO alanlarını temizleme - SADECE DİL DEĞİŞİMİ YAPALIM
                console.log('💾 Yeni sayfa için SEO alanları korunuyor, sadece dil değişiyor');
                
                // Language badge güncelle
                const nativeName = document.querySelector(`[data-language="${language}"]`)?.getAttribute('data-native-name');
                const badge = document.getElementById('current-language-badge');
                if (badge && nativeName) {
                    badge.textContent = nativeName;
                    console.log('🏷️ Language badge güncellendi:', nativeName);
                }
                
                // Livewire'a dil değişikliğini bildir
                if (window.Livewire) {
                    window.Livewire.dispatch('languageChanged', { language: language });
                }
                
                isLanguageUpdateInProgress = false;
                return; // Yeni sayfa için burada çık
            }
        } catch (error) {
            console.error('❌ Yeni sayfa dil kaydetme hatası:', error);
        }
        
        isLanguageUpdateInProgress = false;
        return;
    }
    
    // Cache'i initialize et
    initializeSeoCache();
    
    // CRITICAL FIX: Global currentLanguage'ı güncelle
    window.currentLanguage = language;
    console.log('🌍 Global currentLanguage force guncellendi:', language);
    
    // CRITICAL FIX: AJAX ile doğrudan session'a dil kaydet
    try {
        const response = await fetch('/admin/set-js-language', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({ language: language })
        });
        
        if (response.ok) {
            console.log('✅ AJAX: JavaScript language session\'a kaydedildi:', language);
            
            // CRITICAL FIX: Tüm cache'i temizle - Redis cache senkronizasyon sorunu
            window.SeoTabsSystem.cache.clear();
            console.log('🗑️ TÜM JavaScript cache temizlendi - dil değişimi için');
        } else {
            console.warn('⚠️ AJAX session kaydetme başarısız');
        }
    } catch (error) {
        console.error('❌ AJAX session kaydetme hatası:', error);
    }
    
    // Backup: Livewire dispatch (çalışmasa da olur)
    if (window.Livewire) {
        window.Livewire.dispatch('js-language-sync', { language: language });
        window.Livewire.dispatch('set-js-language', { language: language });
        console.log('🔄 Livewire backup dispatch gonderildi:', language);
    }
    
    try {
        const cacheKey = `Page_${window.currentPageId}_${language}`;
        console.log('🔍 Cache key:', cacheKey);
        
        const seoData = window.SeoTabsSystem.cache.get(cacheKey);
        console.log('🗂️ Cache\'ten alınan SEO data (dil: ' + language + '):', seoData);
        
        if (seoData && seoData.seoData) {
            console.log('✅ SEO data mevcut, form guncelleniyor... Dil:', language);
            applySeoDataToForm(seoData.seoData, language, true);
        } else {
            console.log('⚠️ Cache\'te veri yok, server\'dan çekiliyor... Dil:', language);
            
            // Server'dan SEO data çek
            try {
                const response = await fetch('/admin/seo/get-data', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        model_type: 'Page',
                        model_id: window.currentPageId,
                        language: language
                    })
                });
                
                if (response.ok) {
                    const serverData = await response.json();
                    console.log('📡 Server\'dan SEO data alındı:', serverData);
                    console.log('🔍 Server response full structure:', JSON.stringify(serverData, null, 2));
                    
                    // Response yapısını kontrol et ve normalize et
                    let normalizedSeoData = {};
                    
                    if (serverData.success && serverData.seoData) {
                        normalizedSeoData = serverData.seoData;
                    } else if (serverData.data) {
                        normalizedSeoData = serverData.data;
                    } else if (serverData.seo_data) {
                        normalizedSeoData = serverData.seo_data;
                    } else {
                        // Response'un kendisi SEO data olabilir
                        normalizedSeoData = serverData;
                    }
                    
                    console.log('🔧 Normalized SEO data:', normalizedSeoData);
                    console.log('🔧 Normalized keys:', Object.keys(normalizedSeoData));
                    
                    // Cache'e kaydet
                    window.SeoTabsSystem.cache.set(cacheKey, {
                        success: true,
                        seoData: normalizedSeoData
                    });
                    
                    // Form'u güncelle - force update ile (dil değişimi)
                    applySeoDataToForm(normalizedSeoData, language, true);
                } else {
                    console.warn('⚠️ Server\'dan SEO data çekilemedi');
                }
            } catch (error) {
                console.error('❌ Server SEO data çekme hatası:', error);
            }
        }
    } catch (error) {
        console.error('❌ SEO veri guncelleme hatasi:', error);
    } finally {
        // Debounce flag'ini sıfırla
        isLanguageUpdateInProgress = false;
        console.log('🔓 Language update tamamlandı, flag sıfırlandı');
    }
}

// Event listener'ları immediate bind et
function bindSeoEventsImmediate() {
    // Global switchLanguage eventi dinle - SEO tab aktifse güncelle  
    window.addEventListener('switchLanguage', function(event) {
        const language = event.detail?.language;
        if (language && window.location.pathname.includes('/manage')) {
            // SEO tab aktif mi kontrol et
            const activeTabPane = document.querySelector('.tab-pane.active');
            const activeSeoTab = document.querySelector('.nav-link.active');
            const hasSeoTitle = activeTabPane && activeTabPane.querySelector('#seo-title');
            const isSeoTabActive = (activeSeoTab && activeSeoTab.textContent.toLowerCase().includes('seo')) ||
                                  hasSeoTitle;
            
            if (isSeoTabActive) {
                updateSeoDataForLanguage(language).catch(console.error);
            }
        }
    });

    // SEO tab tıklamasında mevcut dil için veri çek
    document.addEventListener('click', function(event) {
        const target = event.target;
        const navLink = target.matches('.nav-link') ? target : target.closest('.nav-link');
        
        if (navLink && navLink.textContent.toLowerCase().includes('seo')) {
            const currentLang = document.querySelector('.language-switch-btn.text-primary')?.dataset.language || 'tr';
            setTimeout(async () => {
                await updateSeoDataForLanguage(currentLang);
            }, 100);
        }
    });
}

// SEO verilerini form'a uygula
function applySeoDataToForm(seoData, language, forceUpdate = false) {
    console.log('🔧 applySeoDataToForm cagrildi:', { seoData, language, forceUpdate });
    console.log('🔍 SEO Data Keys:', Object.keys(seoData));
    console.log('🔍 Raw SEO Data Values:', {
        title: seoData.seo_title,
        description: seoData.seo_description,
        keywords: seoData.seo_keywords,
        canonical: seoData.canonical_url
    });
    
    const seoTitle = document.getElementById('seo-title');
    const seoDescription = document.getElementById('seo-description');
    const seoKeywordsHidden = document.getElementById('seo-keywords-hidden');
    const canonicalUrl = document.getElementById('canonical-url');
    
    console.log('🔍 Form elementleri kontrol:', {
        seoTitle: !!seoTitle,
        seoDescription: !!seoDescription,  
        seoKeywordsHidden: !!seoKeywordsHidden,
        canonicalUrl: !!canonicalUrl
    });
    
    if (seoTitle) {
        const titleValue = seoData.seo_title || seoData.title || '';
        console.log('📝 SEO Title guncelleniyor:', titleValue);
        
        // ULTRA FORCE UPDATE - Multiple methods to ensure DOM update
        console.log('🔧 DOM UPDATE BAŞLADI - Element:', seoTitle, 'Value:', titleValue);
        
        // Method 1: Direct value assignment
        seoTitle.value = titleValue;
        
        // Method 2: Attribute setting
        seoTitle.setAttribute('value', titleValue);
        
        // Method 3: jQuery fallback (if available)
        if (window.jQuery) {
            window.jQuery('#seo-title').val(titleValue);
        }
        
        // Method 4: Livewire BYPASS - Zorla Livewire state'ini güncelle
        if (window.Livewire && window.Livewire.find) {
            try {
                // Multiple component ismi dene
                let component = null;
                const possibleNames = ['page-manage-component', 'PageManageComponent', 'admin.page-manage-component'];
                
                for (const name of possibleNames) {
                    try {
                        component = window.Livewire.find(name);
                        if (component) break;
                    } catch (e) {
                        continue;
                    }
                }
                
                // Eğer find çalışmazsa, tüm component'leri kontrol et
                if (!component && window.Livewire.all) {
                    const components = window.Livewire.all();
                    component = components.find(c => c.data && ('seo_title' in c.data || 'seo_description' in c.data));
                }
                
                if (component) {
                    component.set('seo_title', titleValue);
                    console.log('🔄 Livewire seo_title zorla güncellendi:', titleValue);
                }
            } catch (e) {
                console.log('⚠️ Livewire component bulunamadı, normal DOM update devam ediyor');
            }
        }
        
        // Method 5: Force reflow
        seoTitle.style.display = 'none';
        seoTitle.offsetHeight; // Force reflow
        seoTitle.style.display = 'block';
        
        // Method 6: Event dispatching
        seoTitle.dispatchEvent(new Event('input', { bubbles: true }));
        seoTitle.dispatchEvent(new Event('change', { bubbles: true }));
        seoTitle.dispatchEvent(new Event('keyup', { bubbles: true }));
        
        // Method 7: Focus/blur to trigger updates
        seoTitle.focus();
        seoTitle.blur();
        
        console.log('✅ DOM UPDATE TAMAMLANDI - Final value:', seoTitle.value);
        
        // 🔥 CRITICAL TEST: DOM elementinin gerçekten güncellenip güncellenmediğini kontrol et
        setTimeout(() => {
            const actualValue = document.getElementById('seo-title').value;
            const expectedValue = titleValue;
            
            if (actualValue === expectedValue) {
                console.log('✅ DOM BAŞARI: Element değeri gerçekten güncellendi:', actualValue);
                // Visual feedback - green border
                seoTitle.style.borderColor = '#28a745';
                setTimeout(() => seoTitle.style.borderColor = '', 1000);
            } else {
                console.error('❌ DOM HATA: Element değeri güncellenmedi!', {
                    expected: expectedValue,
                    actual: actualValue,
                    element: document.getElementById('seo-title')
                });
                
                // Visual feedback - red border
                seoTitle.style.borderColor = '#dc3545';
                setTimeout(() => seoTitle.style.borderColor = '', 2000);
                
                // EMERGENCY FIX: Zorla güncelle
                document.getElementById('seo-title').value = expectedValue;
                console.log('🚨 EMERGENCY FIX: Zorla değer atandı');
            }
        }, 50);
        
        // Livewire'e field bazında bildir
        if (window.Livewire) {
            window.Livewire.dispatch('seo-field-updated', {
                field: 'seo_title',
                value: titleValue,
                language: language
            });
        }
    }
    
    if (seoDescription) {
        const descValue = seoData.seo_description || seoData.description || '';
        console.log('📝 SEO Description guncelleniyor:', descValue);
        
        // ULTRA FORCE UPDATE - Multiple methods to ensure DOM update
        console.log('🔧 DOM UPDATE BAŞLADI - Element:', seoDescription, 'Value:', descValue);
        
        // 🚨 CRITICAL: Textarea için innerHTML ve value ikisini de güncelle
        // Method 1: Direct value assignment
        seoDescription.value = descValue;
        
        // Method 2: innerHTML için de güncelle (textarea'lar için kritik)
        seoDescription.innerHTML = descValue;
        seoDescription.textContent = descValue;
        
        // Method 3: Attribute setting
        seoDescription.setAttribute('value', descValue);
        
        // Method 4: jQuery fallback (if available)
        if (window.jQuery) {
            window.jQuery('#seo-description').val(descValue);
            window.jQuery('#seo-description').text(descValue);
        }
        
        // Method 5: Livewire BYPASS - Zorla Livewire state'ini güncelle
        if (window.Livewire && window.Livewire.find) {
            try {
                // Multiple component ismi dene
                let component = null;
                const possibleNames = ['page-manage-component', 'PageManageComponent', 'admin.page-manage-component'];
                
                for (const name of possibleNames) {
                    try {
                        component = window.Livewire.find(name);
                        if (component) break;
                    } catch (e) {
                        continue;
                    }
                }
                
                // Eğer find çalışmazsa, tüm component'leri kontrol et
                if (!component && window.Livewire.all) {
                    const components = window.Livewire.all();
                    component = components.find(c => c.data && ('seo_title' in c.data || 'seo_description' in c.data));
                }
                
                if (component) {
                    component.set('seo_description', descValue);
                    console.log('🔄 Livewire seo_description zorla güncellendi:', descValue);
                }
            } catch (e) {
                console.log('⚠️ Livewire component bulunamadı, normal DOM update devam ediyor');
            }
        }
        
        // Method 6: Force reflow
        seoDescription.style.display = 'none';
        seoDescription.offsetHeight; // Force reflow
        seoDescription.style.display = 'block';
        
        // Method 7: Event dispatching
        seoDescription.dispatchEvent(new Event('input', { bubbles: true }));
        seoDescription.dispatchEvent(new Event('change', { bubbles: true }));
        seoDescription.dispatchEvent(new Event('keyup', { bubbles: true }));
        
        // Method 8: Focus/blur to trigger updates
        seoDescription.focus();
        seoDescription.blur();
        
        console.log('✅ DOM UPDATE TAMAMLANDI - Final value:', seoDescription.value);
        
        // 🔥 CRITICAL TEST: DOM elementinin gerçekten güncellenip güncellenmediğini kontrol et
        setTimeout(() => {
            const actualValue = document.getElementById('seo-description').value;
            const expectedValue = descValue;
            
            if (actualValue === expectedValue) {
                console.log('✅ DOM BAŞARI: Description değeri gerçekten güncellendi:', actualValue);
                // Visual feedback - green border
                seoDescription.style.borderColor = '#28a745';
                setTimeout(() => seoDescription.style.borderColor = '', 1000);
            } else {
                console.error('❌ DOM HATA: Description değeri güncellenmedi!', {
                    expected: expectedValue,
                    actual: actualValue,
                    element: document.getElementById('seo-description')
                });
                
                // Visual feedback - red border
                seoDescription.style.borderColor = '#dc3545';
                setTimeout(() => seoDescription.style.borderColor = '', 2000);
                
                // EMERGENCY FIX: Zorla güncelle
                document.getElementById('seo-description').value = expectedValue;
                console.log('🚨 EMERGENCY FIX: Description zorla değer atandı');
            }
        }, 50);
        
        // Livewire'e field bazında bildir
        if (window.Livewire) {
            window.Livewire.dispatch('seo-field-updated', {
                field: 'seo_description',
                value: descValue,
                language: language
            });
        }
    }
    
    if (seoKeywordsHidden) {
        const keywordsValue = seoData.seo_keywords || seoData.keywords || '';
        console.log('📝 SEO Keywords guncelleniyor:', keywordsValue);
        
        // ULTRA FORCE UPDATE - Multiple methods to ensure DOM update
        console.log('🔧 DOM UPDATE BAŞLADI - Element:', seoKeywordsHidden, 'Value:', keywordsValue);
        
        // Method 1: Direct value assignment
        seoKeywordsHidden.value = keywordsValue;
        
        // Method 2: Attribute setting
        seoKeywordsHidden.setAttribute('value', keywordsValue);
        
        // Method 3: jQuery fallback (if available)
        if (window.jQuery) {
            window.jQuery('#seo-keywords-hidden').val(keywordsValue);
        }
        
        // Method 4: Update keyword display
        updateKeywordDisplay(keywordsValue);
        
        // Method 5: Event dispatching
        seoKeywordsHidden.dispatchEvent(new Event('input', { bubbles: true }));
        seoKeywordsHidden.dispatchEvent(new Event('change', { bubbles: true }));
        
        console.log('✅ DOM UPDATE TAMAMLANDI - Final value:', seoKeywordsHidden.value);
        
        // Livewire'e field bazında bildir
        if (window.Livewire) {
            window.Livewire.dispatch('seo-field-updated', {
                field: 'seo_keywords',
                value: keywordsValue,
                language: language
            });
        }
    }
    
    if (canonicalUrl) {
        const canonicalValue = seoData.canonical_url || seoData.canonical || '';
        console.log('📝 Canonical URL guncelleniyor:', canonicalValue);
        
        // CRITICAL FIX: DOM force update - cache bypass
        canonicalUrl.value = '';
        setTimeout(() => {
            canonicalUrl.value = canonicalValue;
            canonicalUrl.setAttribute('value', canonicalValue);
            canonicalUrl.dispatchEvent(new Event('input', { bubbles: true }));
            canonicalUrl.dispatchEvent(new Event('change', { bubbles: true }));
            console.log('🔄 Canonical URL DOM force update:', canonicalValue);
        }, 10);
        
        // Livewire'e field bazında bildir
        if (window.Livewire) {
            window.Livewire.dispatch('seo-field-updated', {
                field: 'canonical_url',
                value: canonicalValue,
                language: language
            });
        }
    }
    
    // CRITICAL FIX: TinyMCE editor cache temizleme
    setTimeout(() => {
        if (window.tinymce) {
            const editorId = `editor_${language}`;
            const editor = window.tinymce.get(editorId);
            if (editor) {
                // Force content refresh
                const currentContent = editor.getContent();
                editor.setContent('');
                setTimeout(() => {
                    editor.setContent(currentContent);
                    console.log('🔄 TinyMCE editor cache temizlendi:', editorId);
                }, 50);
            }
        }
    }, 100);
    
    // CRITICAL FIX: Livewire component force refresh
    setTimeout(() => {
        if (window.Livewire) {
            // Page component'i refresh et
            window.Livewire.dispatch('refreshComponent');
            console.log('🔄 Livewire component force refresh yapıldı');
        }
    }, 200);
    
    console.log('✅ applySeoDataToForm tamamlandi');
}

// Keyword display'i güncelle
function updateKeywordDisplay(keywordsString) {
    const keywordDisplay = document.getElementById('keyword-display');
    if (!keywordDisplay) return;
    
    // Mevcut badge'leri temizle
    keywordDisplay.innerHTML = '';
    
    if (!keywordsString || keywordsString.trim() === '') return;
    
    // Keyword'leri parse et
    const keywords = keywordsString.split(',').map(k => k.trim()).filter(k => k !== '');
    
    // Badge'leri oluştur
    keywords.forEach(keyword => {
        const badge = document.createElement('span');
        badge.className = 'badge badge-secondary me-1 mb-1';
        badge.style.padding = '6px 8px';
        
        badge.innerHTML = `
            <span class="keyword-text">${keyword}</span>
            <span class="keyword-remove" style="cursor: pointer; padding: 2px 4px; border-radius: 2px; transition: background-color 0.2s;" 
                  onmouseover="this.style.backgroundColor='rgba(255,255,255,0.2)'" 
                  onmouseout="this.style.backgroundColor='transparent'">&times;</span>
        `;
        
        // Remove event listener
        badge.querySelector('.keyword-remove').addEventListener('click', function() {
            removeKeyword(keyword);
        });
        
        keywordDisplay.appendChild(badge);
    });
}

// Keyword kaldır
function removeKeyword(keywordToRemove) {
    const hiddenInput = document.getElementById('seo-keywords-hidden');
    if (!hiddenInput) return;
    
    const currentKeywords = hiddenInput.value.split(',').map(k => k.trim()).filter(k => k !== '');
    const updatedKeywords = currentKeywords.filter(k => k !== keywordToRemove);
    
    hiddenInput.value = updatedKeywords.join(', ');
    hiddenInput.dispatchEvent(new Event('input'));
    
    updateKeywordDisplay(hiddenInput.value);
}

// Keyword ekle
function addKeyword(keyword) {
    if (!keyword || keyword.trim() === '') return;
    
    const hiddenInput = document.getElementById('seo-keywords-hidden');
    if (!hiddenInput) return;
    
    const currentKeywords = hiddenInput.value.split(',').map(k => k.trim()).filter(k => k !== '');
    
    // Duplicate kontrolü
    if (currentKeywords.includes(keyword.trim())) return;
    
    // Limit kontrolü (maksimum 10)
    if (currentKeywords.length >= 10) {
        alert('En fazla 10 anahtar kelime ekleyebilirsiniz.');
        return;
    }
    
    currentKeywords.push(keyword.trim());
    hiddenInput.value = currentKeywords.join(', ');
    hiddenInput.dispatchEvent(new Event('input'));
    
    updateKeywordDisplay(hiddenInput.value);
}

// Keyword sistemi kurulum
function setupKeywordSystem() {
    const keywordInput = document.getElementById('keyword-input');
    const addKeywordBtn = document.getElementById('add-keyword');
    
    if (!keywordInput || !addKeywordBtn) return;
    
    // Enter tuşu ile keyword ekleme
    keywordInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const keyword = this.value.trim();
            if (keyword) {
                addKeyword(keyword);
                this.value = '';
            }
        }
    });
    
    // Button ile keyword ekleme
    addKeywordBtn.addEventListener('click', function() {
        const keyword = keywordInput.value.trim();
        if (keyword) {
            addKeyword(keyword);
            keywordInput.value = '';
        }
    });
    
    // Mevcut keyword'leri göster
    const hiddenInput = document.getElementById('seo-keywords-hidden');
    if (hiddenInput && hiddenInput.value) {
        updateKeywordDisplay(hiddenInput.value);
    }
}

// SEO Widget slug sistemi
function setupSeoSlugSystem() {
    const slugInput = document.getElementById('page-slug');
    const slugStatus = document.getElementById('slug-status');
    const titleInput = document.querySelector('[wire\\:model*=".title"]');
    
    if (!slugInput) return;
    
    // Title'dan otomatik slug oluşturma
    if (titleInput) {
        titleInput.addEventListener('input', function() {
            if (slugInput.value.trim() === '') {
                const slug = generateSeoSlug(this.value);
                slugInput.value = slug;
                slugInput.dispatchEvent(new Event('input', { bubbles: true }));
                checkSlugUniqueness(slug);
            }
        });
    }
    
    // Slug değişikliklerini dinle ve benzersizlik kontrolü
    let slugTimeout;
    slugInput.addEventListener('input', function() {
        clearTimeout(slugTimeout);
        const slug = this.value.trim();
        
        if (slug === '') {
            slugStatus.innerHTML = '';
            slugInput.classList.remove('is-valid', 'is-invalid');
            return;
        }
        
        // Slug format kontrolü
        const cleanSlug = generateSeoSlug(slug);
        if (slug !== cleanSlug) {
            this.value = cleanSlug;
            slugInput.dispatchEvent(new Event('input', { bubbles: true }));
        }
        
        // 500ms bekle, sonra benzersizlik kontrolü yap
        slugTimeout = setTimeout(() => {
            checkSlugUniqueness(cleanSlug);
        }, 500);
    });
}

// Slug benzersizlik kontrolü
function checkSlugUniqueness(slug) {
    const slugStatus = document.getElementById('slug-status');
    const slugInput = document.getElementById('page-slug');
    
    if (!slug) return;
    
    // Loading durumu
    slugStatus.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Kontrol ediliyor...';
    slugInput.classList.remove('is-valid', 'is-invalid');
    
    // AJAX ile slug kontrolü
    fetch('/admin/check-slug', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            slug: slug,
            module: 'Page',
            exclude_id: window.currentPageId || null
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.unique) {
            slugStatus.innerHTML = '<i class="fas fa-check"></i> Kullanılabilir';
            slugInput.classList.remove('is-invalid');
            slugInput.classList.add('is-valid');
        } else {
            slugStatus.innerHTML = '<i class="fas fa-times"></i> Bu slug zaten kullanılıyor';
            slugInput.classList.remove('is-valid');
            slugInput.classList.add('is-invalid');
        }
    })
    .catch(error => {
        console.error('Slug kontrolü hatası:', error);
        slugStatus.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Kontrol edilemiyor';
    });
}

// SEO için Türkçe slug üretimi
function generateSeoSlug(text) {
    if (!text) return '';
    
    const turkishChars = {
        'Ç': 'C', 'ç': 'c', 'Ğ': 'G', 'ğ': 'g', 
        'I': 'I', 'ı': 'i', 'İ': 'I', 'i': 'i',
        'Ö': 'O', 'ö': 'o', 'Ş': 'S', 'ş': 's', 
        'Ü': 'U', 'ü': 'u'
    };
    
    return text
        .replace(/[ÇçĞğIıİiÖöŞşÜü]/g, match => turkishChars[match] || match)
        .toLowerCase()
        .replace(/[^a-z0-9\s\-]/g, '')
        .replace(/[\s\-]+/g, '-')
        .replace(/^-+|-+$/g, '');
}

// SEO sistemleri başlatma
document.addEventListener('DOMContentLoaded', function() {
    // Sadece manage sayfalarında çalıştır
    if (window.location.pathname.includes('/manage')) {
        setupKeywordSystem();
        setupSeoSlugSystem();
        
        // SEO cache sistemi kaldırıldı - normal API çağrısı kullanılıyor
    }
});

// SEO event listeners - ONE TIME BINDING
(function() {
    let seoEventsBound = false;
    
    function bindSeoEvents() {
        if (seoEventsBound) return;
        seoEventsBound = true;
        
        console.log('🔧 SEO events bağlanıyor (sadece bir kere)...');
        
        // ZERO API Cache'i initialize et
        initializeSeoCache();
        
        // Global switchLanguage eventi dinle - SEO tab aktifse güncelle  
        window.addEventListener('switchLanguage', function(event) {
            console.log('🔍 Global switchLanguage eventi alındı:', event.detail);
            const language = event.detail?.language;
            if (language && window.location.pathname.includes('/manage')) {
                // SEO tab aktif mi kontrol et - multiple yöntem
                const activeTabPane = document.querySelector('.tab-pane.active');
                const activeSeoTab = document.querySelector('.nav-link.active');
                const hasSeoTitle = activeTabPane && activeTabPane.querySelector('#seo-title');
                const isSeoTabActive = (activeSeoTab && activeSeoTab.textContent.toLowerCase().includes('seo')) ||
                                      hasSeoTitle;
                                      
                
                if (isSeoTabActive) {
                    console.log('🎯 SEO tab aktif, dil değişimi ile veriler güncelleniyor:', language);
                    console.log('🔧 updateSeoDataForLanguage fonksiyonu:', typeof updateSeoDataForLanguage);
                    
                    if (typeof updateSeoDataForLanguage === 'function') {
                        console.log('✅ Fonksiyon çağrılıyor...');
                        updateSeoDataForLanguage(language).catch(console.error);
                    } else {
                        console.error('❌ updateSeoDataForLanguage fonksiyonu bulunamadı!');
                    }
                } else {
                    console.log('⏳ SEO tab aktif değil, dil değişiminde veri çekilmeyecek');
                }
            }
        });
    }
    
    document.addEventListener('DOMContentLoaded', bindSeoEvents);
    // Livewire sonrası da çalıştır ama duplicate önle
    document.addEventListener('livewire:updated', bindSeoEvents);
    
    // Debug - immediately try to bind
    console.log('🔧 SEO-TABS.JS dosyası yüklendi!');
    
    // 🔥 DEBUG: Global test fonksiyonu - Console'dan çağırılabilir
    window.testSeoTabsSystem = function() {
        console.log('🧪 SEO Tabs System Test Başlatılıyor...');
        
        // Current language'ı al
        const currentLang = document.querySelector('.language-switch-btn.text-primary')?.dataset.language || 'tr';
        console.log('📍 Mevcut dil:', currentLang);
        
        // SEO elementlerini kontrol et
        const seoTitle = document.getElementById('seo-title');
        const seoDescription = document.getElementById('seo-description');
        
        console.log('🔍 Element durumu:', {
            seoTitle: !!seoTitle,
            seoDescription: !!seoDescription,
            currentTitleValue: seoTitle?.value,
            currentDescValue: seoDescription?.value
        });
        
        // Tüm dilleri test et
        ['tr', 'en', 'ar'].forEach(lang => {
            console.log(`🌍 ${lang} dili için test yapılıyor...`);
            updateSeoDataForLanguage(lang).then(() => {
                console.log(`✅ ${lang} dili test tamamlandı`);
            }).catch(err => {
                console.error(`❌ ${lang} dili test hatası:`, err);
            });
        });
    };
    
    // SEO tab tıklamasında mevcut dil için veri çek
    document.addEventListener('click', function(event) {
        const target = event.target;
        
        // SEO tab'ı içeriğinden tanı (text'te "SEO" geçen nav-link)
        const navLink = target.matches('.nav-link') ? target : target.closest('.nav-link');
        if (navLink && navLink.textContent.toLowerCase().includes('seo')) {
            const currentLang = document.querySelector('.language-switch-btn.text-primary')?.dataset.language || 'tr';
            console.log('🎯 SEO tab tiklandi, mevcut dil icin veri cekiliyor:', currentLang);
            setTimeout(async () => {
                await updateSeoDataForLanguage(currentLang);
            }, 100); // Tab geçişi için kısa bekleme
        }
    });
});

// Global exports
window.updateSeoDataForLanguage = updateSeoDataForLanguage;
window.applySeoDataToForm = applySeoDataToForm;
window.updateKeywordDisplay = updateKeywordDisplay;
window.setupKeywordSystem = setupKeywordSystem;
window.setupSeoSlugSystem = setupSeoSlugSystem;

} // SeoTabsSystem namespace kapatma