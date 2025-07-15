/**
 * JavaScript Translation Helper
 * Türkçe metinleri dinamik olarak değiştirir
 */

window.jsTranslations = {
    'tr': {
        'search_placeholder': 'Arama yapın...',
        'no_results': 'Sonuç bulunamadı',
        'no_choices': 'Seçenek yok',
        'item_select': 'Seçmek için tıklayın',
        'select_placeholder': 'Seçiniz...',
        'loading': 'Yükleniyor...',
        'add_item': 'eklemek için Enter\'a basın',
        'max_items': 'Sadece {count} değer ekleyebilirsiniz',
        'duplicate_item': 'Bu değer zaten eklendi',
        'invalid_comma': 'Virgül karakteri kullanılamaz',
        'success': 'Başarılı',
        'error': 'Hata',
        'cache_cleared': 'Cache başarıyla temizlendi',
        'cache_error': 'Cache temizleme sırasında bir hata oluştu',
        'theme_updated': 'Tema başarıyla güncellendi',
        'theme_reset': 'Tema ayarları sıfırlandı',
        'saving': 'Kaydediliyor...',
        'loading_themes': 'Temalar yükleniyor...',
        'apply_theme': 'Temayı Uygula',
        'preview_theme': 'Önizle',
        'months': [
            'Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran',
            'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'
        ],
        'weekdays_short': ['Paz', 'Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt']
    },
    'en': {
        'search_placeholder': 'Search...',
        'no_results': 'No results found',
        'no_choices': 'No choices available',
        'item_select': 'Click to select',
        'select_placeholder': 'Select...',
        'loading': 'Loading...',
        'add_item': 'Press Enter to add',
        'max_items': 'Only {count} values allowed',
        'duplicate_item': 'This value already exists',
        'invalid_comma': 'Comma character not allowed',
        'success': 'Success',
        'error': 'Error',
        'cache_cleared': 'Cache cleared successfully',
        'cache_error': 'An error occurred while clearing cache',
        'theme_updated': 'Theme updated successfully',
        'theme_reset': 'Theme settings reset',
        'saving': 'Saving...',
        'loading_themes': 'Loading themes...',
        'apply_theme': 'Apply Theme',
        'preview_theme': 'Preview',
        'months': [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ],
        'weekdays_short': ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
    }
};

/**
 * JavaScript çeviri fonksiyonu
 * @param {string} key - Çeviri anahtarı
 * @param {object} params - Parametreler
 * @returns {string}
 */
window.t = function(key, params = {}) {
    const locale = document.documentElement.getAttribute('lang') || 'tr';
    const translations = window.jsTranslations[locale] || window.jsTranslations['tr'];
    
    let translation = translations[key] || key;
    
    // Parametreleri değiştir
    if (params) {
        Object.keys(params).forEach(param => {
            translation = translation.replace(`{${param}}`, params[param]);
        });
    }
    
    return translation;
};

/**
 * Dil değişikliğinde çevirileri güncelle
 */
window.updateTranslations = function() {
    // Bu fonksiyon dil değiştirildiğinde çağrılabilir
    // Mevcut tüm Choices.js instance'larını yeniden init edebilir
    if (typeof initializeChoices === 'function') {
        setTimeout(() => {
            initializeChoices();
        }, 100);
    }
};