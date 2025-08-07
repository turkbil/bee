// Tailwind CDN Local Fallback
// Bu dosya internet bağlantısı olmadığında veya CDN erişilemediğinde kullanılır

(function() {
    // Tailwind yüklenmemişse bildir
    window.addEventListener('DOMContentLoaded', function() {
        if (!window.tailwind) {
            console.warn('Tailwind CSS CDN yüklenemedi. Lütfen internet bağlantınızı kontrol edin.');
            // Fallback CSS ekle
            document.body.style.fontFamily = 'system-ui, -apple-system, sans-serif';
        }
    });
})();