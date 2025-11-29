/**
 * Muzibu Alpine.js Global Store
 * Modüller arası paylaşılan state ve fonksiyonlar
 */

document.addEventListener('alpine:init', () => {
    // Toast Store
    Alpine.store('toast', {
        show(message, type = 'info') {
            const toast = document.createElement('div');
            let bgClass = 'bg-blue-600';

            if (type === 'success') bgClass = 'bg-green-600';
            else if (type === 'error') bgClass = 'bg-red-600';
            else if (type === 'warning') bgClass = 'bg-orange-600';

            toast.className = `fixed bottom-24 right-6 px-6 py-3 rounded-lg shadow-lg text-white z-[9999] transition-all duration-300 ${bgClass}`;
            toast.textContent = message;
            toast.style.opacity = '0';
            document.body.appendChild(toast);

            setTimeout(() => toast.style.opacity = '1', 10);
            setTimeout(() => toast.style.opacity = '0', 3000);
            setTimeout(() => toast.remove(), 3500);
        }
    });

    // Player State Store
    Alpine.store('player', {
        isPlaying: false,
        currentSong: null,
        showToast(message, type) {
            Alpine.store('toast').show(message, type);
        }
    });
});
