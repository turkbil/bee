/**
 * Web Share API Helper
 *
 * Modern paylaşım özelliği - Mobilde native menü açar
 *
 * Kullanım:
 * <button onclick="webShare({title: 'Başlık', text: 'Açıklama', url: window.location.href})">
 *   Paylaş
 * </button>
 */

/**
 * Web Share API ile paylaş
 * @param {Object} data - {title, text, url}
 * @returns {Promise<boolean>}
 */
async function webShare(data = {}) {
    // Web Share API destekleniyor mu?
    if (!navigator.share) {
        console.warn('[Web Share] API desteklenmiyor, fallback çalışacak');
        return webShareFallback(data);
    }

    try {
        await navigator.share({
            title: data.title || document.title,
            text: data.text || '',
            url: data.url || window.location.href
        });

        console.log('[Web Share] Başarıyla paylaşıldı');
        return true;
    } catch (error) {
        // Kullanıcı iptal etti (AbortError) - hata gösterme
        if (error.name === 'AbortError') {
            console.log('[Web Share] Kullanıcı iptal etti');
            return false;
        }

        console.error('[Web Share] Hata:', error);
        return false;
    }
}

/**
 * Fallback: Web Share API desteklenmiyorsa
 * @param {Object} data - {title, text, url}
 * @returns {boolean}
 */
function webShareFallback(data = {}) {
    const url = data.url || window.location.href;
    const text = data.text || data.title || document.title;

    // WhatsApp paylaşımı (mobilde çok kullanılıyor)
    const whatsappUrl = `https://wa.me/?text=${encodeURIComponent(text + ' ' + url)}`;

    // Yeni pencerede aç
    window.open(whatsappUrl, '_blank', 'width=600,height=400');
    return true;
}

/**
 * Clipboard'a kopyala (modern API)
 * @param {string} text
 * @returns {Promise<boolean>}
 */
async function copyToClipboard(text) {
    if (!navigator.clipboard) {
        console.warn('[Clipboard] API desteklenmiyor');
        return copyToClipboardFallback(text);
    }

    try {
        await navigator.clipboard.writeText(text);
        console.log('[Clipboard] Kopyalandı:', text);
        return true;
    } catch (error) {
        console.error('[Clipboard] Hata:', error);
        return copyToClipboardFallback(text);
    }
}

/**
 * Fallback: Clipboard API desteklenmiyorsa
 * @param {string} text
 * @returns {boolean}
 */
function copyToClipboardFallback(text) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();

    try {
        document.execCommand('copy');
        document.body.removeChild(textarea);
        console.log('[Clipboard Fallback] Kopyalandı');
        return true;
    } catch (error) {
        document.body.removeChild(textarea);
        console.error('[Clipboard Fallback] Hata:', error);
        return false;
    }
}

/**
 * Web Share API destekleniyor mu kontrol et
 * @returns {boolean}
 */
function canWebShare() {
    return 'share' in navigator;
}

// Global scope'a ekle
window.webShare = webShare;
window.copyToClipboard = copyToClipboard;
window.canWebShare = canWebShare;

console.log('[Web Share] Helper yüklendi. Destek:', canWebShare());
