/* SEO Basit Sistem - jQuery ile */

// Global SEO data storage
window.seoAllLanguagesData = {};

$(document).ready(function() {
    console.log('🚀 SEO Basit Sistem başlatılıyor...');
    
    // Sayfa yüklendiğinde tüm dillerin SEO verisini çek
    loadAllSeoData();
    
    // Dil değişim butonlarını dinle
    $(document).on('click', '.language-switch-btn', function() {
        const language = $(this).data('language');
        setTimeout(() => {
            updateSeoFormFields(language);
        }, 100);
    });
});

// Tüm dillerin SEO verisini yükle
function loadAllSeoData() {
    if (!window.currentPageId) {
        console.log('❌ PageId yok, SEO data yüklenmiyor');
        return;
    }
    
    console.log('📡 Tüm dillerin SEO verisi yükleniyor...');
    
    $.ajax({
        url: '/admin/seo/get-data',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            model_type: 'Page',
            model_id: window.currentPageId,
            language: 'tr' // İlk dil olarak tr kullan
        },
        success: function(response) {
            console.log('✅ SEO data başarıyla yüklendi:', response);
            
            // Tüm diller için veri çek
            const languages = ['tr', 'en', 'ar'];
            let loadedCount = 0;
            
            languages.forEach(lang => {
                $.ajax({
                    url: '/admin/seo/get-data',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        model_type: 'Page',
                        model_id: window.currentPageId,
                        language: lang
                    },
                    success: function(langResponse) {
                        // Server response yapısını normalize et
                        const normalizedData = langResponse.seoData || langResponse.data || langResponse;
                        window.seoAllLanguagesData[lang] = normalizedData;
                        loadedCount++;
                        
                        console.log(`✅ ${lang} dili SEO data yüklendi:`, normalizedData);
                        console.log(`📊 ${lang} server response structure:`, Object.keys(langResponse));
                        
                        if (loadedCount === languages.length) {
                            console.log('🎉 Tüm dillerin SEO verisi hazır:', window.seoAllLanguagesData);
                            // İlk dil için form'u güncelle
                            updateSeoFormFields('tr');
                        }
                    },
                    error: function(xhr) {
                        console.error(`❌ ${lang} dili SEO data yüklenemedi:`, xhr);
                    }
                });
            });
        },
        error: function(xhr) {
            console.error('❌ SEO data yüklenemedi:', xhr);
        }
    });
}

// Form field'larını güncelle
function updateSeoFormFields(language) {
    console.log('🔄 Form field\'ları güncelleniyor:', language);
    
    const seoData = window.seoAllLanguagesData[language];
    if (!seoData) {
        console.log('❌ Bu dil için SEO data yok:', language);
        return;
    }
    
    console.log('📝 Form güncelleniyor:', seoData);
    
    // Debug: SEO data yapısını kontrol et
    console.log('🔍 SEO Data Keys:', Object.keys(seoData));
    console.log('🔍 Raw SEO Data Values:', {
        title: seoData.seo_title,
        description: seoData.seo_description,
        keywords: seoData.seo_keywords,
        canonical: seoData.canonical_url
    });

    // Title field
    const titleField = $('#seo-title');
    if (titleField.length) {
        const titleValue = seoData.seo_title || seoData.title || '';
        titleField.val(titleValue);
        console.log('✅ Title güncellendi:', titleValue);
    }
    
    // Description field  
    const descField = $('#seo-description');
    if (descField.length) {
        const descValue = seoData.seo_description || seoData.description || '';
        descField.val(descValue);
        console.log('✅ Description güncellendi:', descValue);
    }
    
    // Keywords field
    const keywordsField = $('#seo-keywords-hidden');
    if (keywordsField.length) {
        const keywordsValue = seoData.seo_keywords || seoData.keywords || '';
        keywordsField.val(keywordsValue);
        updateKeywordDisplay(keywordsValue);
        console.log('✅ Keywords güncellendi:', keywordsValue);
    }
    
    // Canonical URL field
    const canonicalField = $('#canonical-url');
    if (canonicalField.length) {
        const canonicalValue = seoData.canonical_url || seoData.canonical || '';
        canonicalField.val(canonicalValue);
        console.log('✅ Canonical URL güncellendi:', canonicalValue);
    }
    
    // Livewire'e bildir
    if (window.Livewire) {
        window.Livewire.dispatch('seo-language-changed', {
            language: language,
            seoData: seoData
        });
    }
    
    console.log('🎯 Form field\'ları başarıyla güncellendi!');
}

// Keyword display güncelle (mevcut fonksiyonu kullan)
function updateKeywordDisplay(keywordsString) {
    const keywordDisplay = $('#keyword-display');
    if (!keywordDisplay.length || !keywordsString) return;
    
    keywordDisplay.empty();
    
    const keywords = keywordsString.split(',').map(k => k.trim()).filter(k => k !== '');
    
    keywords.forEach(keyword => {
        const badge = $(`
            <span class="badge badge-secondary me-1 mb-1" style="padding: 6px 8px;">
                <span class="keyword-text">${keyword}</span>
                <span class="keyword-remove" style="cursor: pointer; padding: 2px 4px; border-radius: 2px; transition: background-color 0.2s;">&times;</span>
            </span>
        `);
        
        badge.find('.keyword-remove').on('click', function() {
            removeKeyword(keyword);
        });
        
        keywordDisplay.append(badge);
    });
}

// Keyword kaldır
function removeKeyword(keywordToRemove) {
    const hiddenInput = $('#seo-keywords-hidden');
    if (!hiddenInput.length) return;
    
    const currentKeywords = hiddenInput.val().split(',').map(k => k.trim()).filter(k => k !== '');
    const updatedKeywords = currentKeywords.filter(k => k !== keywordToRemove);
    
    hiddenInput.val(updatedKeywords.join(', '));
    hiddenInput.trigger('input');
    
    updateKeywordDisplay(hiddenInput.val());
}

console.log('📦 SEO Simple System yüklendi!');