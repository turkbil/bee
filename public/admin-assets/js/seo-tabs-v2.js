/**
 * SEO TABS V2 - İki Aşamalı Temiz Sistem
 * Aşama 1: Language Tab (TR/EN/AR)
 * Aşama 2: Content Tab (Genel/SEO)
 */

window.SeoTabsV2 = {
    // Sistem başlatma
    init: function() {
        console.log('🚀 SEO Tabs V2 başlatılıyor...');
        
        // Eğer page ID yoksa yeni sayfa modunda çalış
        if (!window.currentPageId) {
            console.log('🆕 Yeni sayfa modu - SEO sistem temel özelliklerle başlatılıyor');
            // Yeni sayfalarda da temel SEO özellikleri çalışsın
        } else {
            console.log('✅ Mevcut sayfa - SEO sistem tam özelliklerle başlatılıyor');
        }
        
        this.bindEvents();
        this.loadInitialData();
    },
    
    // Event listener'ları bağla
    bindEvents: function() {
        const self = this;
        
        // Language butonlarını dinle (Aşama 1)
        $(document).on('click', '.language-switch-btn', function() {
            const language = $(this).data('language');
            console.log('🌍 Dil değişimi:', language);
            
            // Mevcut aktif tab'ı kontrol et
            const activeTab = $('.nav-link.active').attr('href');
            
            if (activeTab && activeTab.includes('seo')) {
                // SEO tab aktifse verilerini güncelle
                console.log('🎯 SEO tab aktif, veriler güncelleniyor');
                setTimeout(() => {
                    self.updateSeoFields(language);
                }, 150); // Tab geçişi için bekle
            }
        });
        
        // SEO tab tıklamasını dinle (Aşama 2)
        $(document).on('click', 'a[href*="seo"]', function() {
            console.log('🎯 SEO tab tıklandı');
            
            // Mevcut dili al
            const currentLang = $('.language-switch-btn.text-primary').data('language') || 'tr';
            
            setTimeout(() => {
                self.updateSeoFields(currentLang);
            }, 150); // Tab geçişi için bekle
        });
    },
    
    // İlk veri yükleme
    loadInitialData: function() {
        console.log('📡 İlk SEO verileri yükleniyor...');
        
        const languages = ['tr', 'en', 'ar'];
        window.seoDataCache = window.seoDataCache || {};
        
        // Backend'den gelen hazır veriyi kullan
        if (window.allLanguagesSeoData) {
            console.log('✅ Backend verisi kullanılıyor:', window.allLanguagesSeoData);
            window.seoDataCache = window.allLanguagesSeoData;
            return;
        }
        
        // Backend verisi yoksa API'den çek
        languages.forEach(lang => {
            this.fetchSeoData(lang);
        });
    },
    
    // SEO veri çekme
    fetchSeoData: function(language) {
        const self = this;
        
        $.ajax({
            url: '/admin/seo/get-data',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                model_type: 'Page',
                model_id: window.currentPageId,
                language: language
            },
            success: function(response) {
                console.log(`✅ ${language} SEO verisi alındı:`, response);
                
                // Veriyi normalize et
                const seoData = response.seoData || response.data || response;
                window.seoDataCache = window.seoDataCache || {};
                window.seoDataCache[language] = seoData;
                
                console.log(`📊 ${language} verisi cache'e kaydedildi:`, seoData);
            },
            error: function(xhr) {
                console.error(`❌ ${language} SEO verisi alınamadı:`, xhr);
            }
        });
    },
    
    // SEO field'larını güncelle
    updateSeoFields: function(language) {
        console.log('🔄 SEO alanları güncelleniyor:', language);
        
        // Cache'den veriyi al
        const seoData = window.seoDataCache && window.seoDataCache[language];
        
        if (!seoData) {
            console.log(`❌ ${language} için SEO verisi yok`);
            this.fetchSeoData(language);
            return;
        }
        
        console.log('📝 SEO alanları doldurluyor:', seoData);
        
        // Title
        const titleField = $('#seo-title');
        if (titleField.length) {
            const value = seoData.seo_title || '';
            titleField.val(value);
            console.log('✅ Title:', value);
        }
        
        // Description
        const descField = $('#seo-description');
        if (descField.length) {
            const value = seoData.seo_description || '';
            descField.val(value);
            console.log('✅ Description:', value);
        }
        
        // Keywords
        const keywordsField = $('#seo-keywords-hidden');
        if (keywordsField.length) {
            const value = seoData.seo_keywords || '';
            keywordsField.val(value);
            this.updateKeywordDisplay(value);
            console.log('✅ Keywords:', value);
        }
        
        // Canonical URL - Language specific
        const canonicalField = $(`[wire\\\\:model=\"seoDataCache.${language}.canonical_url\"]`);
        if (canonicalField.length) {
            const value = seoData.canonical_url || '';
            canonicalField.val(value);
            console.log('✅ Canonical URL:', value);
        }
        
        // Livewire'a bildir
        if (window.Livewire) {
            window.Livewire.dispatch('seo-data-loaded', {
                language: language,
                data: seoData
            });
        }
        
        console.log('🎯 SEO alanları başarıyla güncellendi!');
    },
    
    // Keyword görünümünü güncelle
    updateKeywordDisplay: function(keywordsString) {
        const display = $('#keyword-display');
        if (!display.length || !keywordsString) return;
        
        display.empty();
        
        const keywords = keywordsString.split(',')
            .map(k => k.trim())
            .filter(k => k);
        
        keywords.forEach(keyword => {
            const badge = $(`
                <span class="badge badge-secondary me-1 mb-1" style="padding: 6px 8px;">
                    <span class="keyword-text">${keyword}</span>
                    <span class="keyword-remove ms-1" style="cursor: pointer;">&times;</span>
                </span>
            `);
            
            badge.find('.keyword-remove').click(() => {
                this.removeKeyword(keyword);
            });
            
            display.append(badge);
        });
        
        console.log('🏷️ Keyword display güncellendi:', keywords.length + ' adet');
    },
    
    // Keyword kaldır
    removeKeyword: function(keywordToRemove) {
        const field = $('#seo-keywords-hidden');
        if (!field.length) return;
        
        const keywords = field.val().split(',')
            .map(k => k.trim())
            .filter(k => k && k !== keywordToRemove);
        
        field.val(keywords.join(', '));
        field.trigger('input');
        
        this.updateKeywordDisplay(field.val());
        console.log('🗑️ Keyword kaldırıldı:', keywordToRemove);
    }
};

// Sistem başlatma
$(document).ready(function() {
    // Sadece page manage sayfasında çalıştır
    if (window.location.pathname.includes('/page/manage')) {
        SeoTabsV2.init();
        console.log('✅ SEO Tabs V2 başlatıldı!');
    }
});

// Global erişim için
window.SeoTabsV2 = window.SeoTabsV2;