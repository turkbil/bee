// Temel JavaScript - Minimal
document.addEventListener("DOMContentLoaded", function () {
    console.log("Blank tema yüklendi!");

    // ========================================
    // SCROLL TO TOP BUTTON
    // ========================================
    const scrollBtn = document.getElementById('scroll-to-top');

    if (scrollBtn) {
        // Scroll event handler
        function handleScroll() {
            if (window.scrollY > 300) {
                scrollBtn.classList.remove('opacity-0', 'pointer-events-none');
                scrollBtn.classList.add('opacity-100', 'pointer-events-auto');
            } else {
                scrollBtn.classList.add('opacity-0', 'pointer-events-none');
                scrollBtn.classList.remove('opacity-100', 'pointer-events-auto');
            }
        }

        // Add scroll listener
        window.addEventListener('scroll', handleScroll);

        // Initial check
        handleScroll();

        console.log('✅ Scroll-to-top button initialized');
    }
});
