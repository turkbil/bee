// HAKKIMIZDA SAYFASI - JAVASCRIPT
// AOS (Animate On Scroll) ve CountUp.js entegrasyonu

document.addEventListener('DOMContentLoaded', function() {

    // 1. AOS (Animate On Scroll) başlat
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            once: true,
            offset: 100,
            easing: 'ease-out-cubic',
            delay: 0
        });
    }

    // 2. CountUp.js animasyonları
    // Not: Alpine.js x-data init() içinde de çalıştırılıyor
    // Ancak fallback olarak burada da ekliyoruz
    if (typeof CountUp !== 'undefined') {
        // Intersection Observer ile görünür olduğunda başlat
        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !entry.target.classList.contains('counted')) {
                    entry.target.classList.add('counted');

                    const targetId = entry.target.id;
                    let endValue = 0;

                    switch(targetId) {
                        case 'stat-products':
                            endValue = 1020;
                            break;
                        case 'stat-categories':
                            endValue = 106;
                            break;
                        case 'stat-experience':
                            endValue = 10;
                            break;
                        case 'stat-partners':
                            endValue = 250;
                            break;
                    }

                    if (endValue > 0) {
                        const counter = new CountUp(targetId, 0, endValue, 0, 2.5, {
                            separator: endValue >= 1000 ? '.' : '',
                            decimal: ',',
                            useEasing: true,
                            useGrouping: true
                        });

                        counter.start();
                    }
                }
            });
        }, {
            threshold: 0.5
        });

        // Tüm stat elementlerini gözlemle
        document.querySelectorAll('[id^="stat-"]').forEach(el => {
            statsObserver.observe(el);
        });
    }

    // 3. Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;

            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                e.preventDefault();
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });

                // URL güncelle (history)
                if (history.pushState) {
                    history.pushState(null, null, targetId);
                }
            }
        });
    });

    // 4. URL'de hash varsa otomatik scroll
    if (window.location.hash) {
        setTimeout(() => {
            const targetElement = document.querySelector(window.location.hash);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }, 100);
    }

    // 5. Parallax effect for hero section (optional)
    const heroSection = document.querySelector('.about-page section:first-child');
    if (heroSection) {
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const rate = scrolled * 0.5;

            if (scrolled <= heroSection.offsetHeight) {
                heroSection.style.transform = `translate3d(0, ${rate}px, 0)`;
            }
        });
    }

    // 6. Console log (development only)
    console.log('İXTİF Hakkımızda sayfası yüklendi ✅');
    console.log('AOS:', typeof AOS !== 'undefined' ? 'Aktif ✅' : 'Yüklenmedi ❌');
    console.log('CountUp.js:', typeof CountUp !== 'undefined' ? 'Aktif ✅' : 'Yüklenmedi ❌');
    console.log('Alpine.js:', typeof Alpine !== 'undefined' ? 'Aktif ✅' : 'Yüklenmedi ❌');
});

// 7. Window load event (tüm kaynaklar yüklendiğinde)
window.addEventListener('load', function() {
    // AOS refresh (görsel kaymalarını önle)
    if (typeof AOS !== 'undefined') {
        AOS.refresh();
    }

    console.log('Sayfa tamamen yüklendi 🚀');
});
