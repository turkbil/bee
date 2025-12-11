function homepage() {
    return {
        loaded: false,
        showX: false,
        showModal: false,
        selectedProduct: null,

        init() {
            this.$nextTick(() => {
                this.loaded = true;
            });

            // İSTİF ↔ İXTİF animasyonu (S ↔ X değişimi)
            setInterval(() => {
                this.showX = !this.showX;
            }, 2000);

            // Ken Burns + Parallax Hybrid Effect
            this.initAboutPhotoEffect();

            // Hero Swiper Slider
            this.initHeroSwiper();
        },

        initHeroSwiper() {
            const progressBar = document.querySelector('.hero-progress-bar');
            const paginationContainer = document.querySelector('.swiper-pagination-custom');
            let pausedProgressWidth = 0;
            let pausedBulletWidth = 0;
            let remainingTime = 7000;

            const heroSwiper = new Swiper('.heroSwiper', {
                effect: 'fade',
                fadeEffect: {
                    crossFade: true
                },
                speed: 800,
                loop: false, // Loop kapalı - temiz navigation
                grabCursor: false,
                allowTouchMove: false,
                preloadImages: false,
                lazy: {
                    loadPrevNext: true,
                },
                autoplay: {
                    delay: 7000,
                    disableOnInteraction: false
                },
                on: {
                    init: function() {
                        createCustomPagination(this);
                        startProgressBar();
                    },
                    slideChange: function() {
                        updateCustomPagination(this);
                        startProgressBar();
                        remainingTime = 7000;
                    },
                    reachEnd: function() {
                        // Son slayta gelince başa dön
                        setTimeout(() => {
                            this.slideTo(0);
                        }, 7000);
                    }
                }
            });

            function createCustomPagination(swiper) {
                paginationContainer.innerHTML = '';
                for (let i = 0; i < swiper.slides.length; i++) {
                    const bullet = document.createElement('div');
                    bullet.className = 'hero-pagination-bullet';
                    bullet.dataset.index = i;
                    bullet.innerHTML = '<div class="bullet-progress"></div>';
                    bullet.addEventListener('click', () => {
                        swiper.slideTo(i);
                    });
                    paginationContainer.appendChild(bullet);
                }
                updateCustomPagination(swiper);
            }

            function updateCustomPagination(swiper) {
                const bullets = paginationContainer.querySelectorAll('.hero-pagination-bullet');
                bullets.forEach((bullet, index) => {
                    const progress = bullet.querySelector('.bullet-progress');
                    if (index === swiper.activeIndex) {
                        bullet.classList.add('hero-pagination-bullet-active');
                        progress.style.transition = 'none';
                        progress.style.width = '0%';
                        progress.offsetHeight;
                        progress.style.transition = 'width 7s linear';
                        setTimeout(() => {
                            if (progress) progress.style.width = '100%';
                        }, 50);
                    } else {
                        bullet.classList.remove('hero-pagination-bullet-active');
                        if (progress) progress.style.width = '0%';
                    }
                });
            }

            function startProgressBar(fromPaused = false) {
                if (fromPaused && pausedProgressWidth > 0) {
                    // Resume from paused position
                    progressBar.style.transition = `width ${remainingTime}ms linear`;
                    progressBar.style.width = '100%';
                } else {
                    // Start from beginning
                    remainingTime = 7000;
                    progressBar.style.transition = 'none';
                    progressBar.style.width = '0%';
                    progressBar.offsetHeight; // Force reflow
                    progressBar.style.transition = 'width 7s linear';
                    setTimeout(() => {
                        progressBar.style.width = '100%';
                    }, 50);
                }
            }

            // Manuel Navigation Buttons
            const navNext = document.querySelector('.hero-nav-next');
            const navPrev = document.querySelector('.hero-nav-prev');

            if (navNext) {
                navNext.addEventListener('click', () => {
                    const nextIndex = heroSwiper.activeIndex + 1;
                    if (nextIndex >= heroSwiper.slides.length) {
                        heroSwiper.slideTo(0);
                    } else {
                        heroSwiper.slideTo(nextIndex);
                    }
                });
            }

            if (navPrev) {
                navPrev.addEventListener('click', () => {
                    const prevIndex = heroSwiper.activeIndex - 1;
                    if (prevIndex < 0) {
                        heroSwiper.slideTo(heroSwiper.slides.length - 1);
                    } else {
                        heroSwiper.slideTo(prevIndex);
                    }
                });
            }

            // Play/Pause Button Control
            const playPauseBtn = document.getElementById('heroPlayPause');
            const playPauseIcon = playPauseBtn.querySelector('i');
            let isPlaying = true;
            let pauseTime = 0;

            playPauseBtn.addEventListener('click', () => {
                if (isPlaying) {
                    // PAUSE
                    heroSwiper.autoplay.stop();
                    playPauseIcon.classList.remove('fa-pause');
                    playPauseIcon.classList.add('fa-play');
                    pauseTime = Date.now();

                    // Calculate current progress percentage
                    const currentProgressWidth = progressBar.offsetWidth;
                    const maxProgressWidth = progressBar.parentElement.offsetWidth;
                    const progressPercent = (currentProgressWidth / maxProgressWidth) * 100;

                    // Calculate remaining time
                    remainingTime = 7000 * ((100 - progressPercent) / 100);

                    // Stop progress bar animation - freeze at current position
                    progressBar.style.transition = 'none';
                    pausedProgressWidth = progressPercent;
                    progressBar.style.width = progressPercent + '%';

                    // Stop active bullet progress animation
                    const activeBullet = paginationContainer.querySelector('.hero-pagination-bullet-active');
                    if (activeBullet) {
                        const bulletProgress = activeBullet.querySelector('.bullet-progress');
                        if (bulletProgress) {
                            const currentBulletWidth = bulletProgress.offsetWidth;
                            const maxBulletWidth = activeBullet.offsetWidth;
                            const bulletPercent = (currentBulletWidth / maxBulletWidth) * 100;

                            bulletProgress.style.transition = 'none';
                            pausedBulletWidth = bulletPercent;
                            bulletProgress.style.width = bulletPercent + '%';
                        }
                    }
                } else {
                    // PLAY - Resume from paused position
                    heroSwiper.autoplay.start();
                    playPauseIcon.classList.remove('fa-play');
                    playPauseIcon.classList.add('fa-pause');

                    // Resume progress bar from paused position
                    if (pausedProgressWidth > 0) {
                        progressBar.style.transition = `width ${remainingTime}ms linear`;
                        progressBar.style.width = '100%';
                    } else {
                        startProgressBar(false);
                    }

                    // Resume active bullet progress animation
                    const activeBullet = paginationContainer.querySelector('.hero-pagination-bullet-active');
                    if (activeBullet) {
                        const bulletProgress = activeBullet.querySelector('.bullet-progress');
                        if (bulletProgress && pausedBulletWidth > 0) {
                            bulletProgress.style.transition = `width ${remainingTime}ms linear`;
                            bulletProgress.style.width = '100%';
                        }
                    }
                }
                isPlaying = !isPlaying;
            });
        },

        initAboutPhotoEffect() {
            const photo = document.getElementById('aboutHeroPhoto');
            const container = document.getElementById('aboutPhotoContainer');

            if (!photo || !container) return;

            const handleScroll = () => {
                const rect = container.getBoundingClientRect();
                const windowHeight = window.innerHeight;

                // Parallax hesaplama (container ekranda görünürken)
                if (rect.top < windowHeight && rect.bottom > 0) {
                    const scrollPercent = (windowHeight - rect.top) / (windowHeight + rect.height);
                    const parallaxY = (scrollPercent - 0.5) * 40; // -20px to +20px

                    // CSS variable güncelle (Ken Burns animasyonu bunu kullanacak)
                    photo.style.setProperty('--parallax-y', `${parallaxY}px`);
                }
            };

            // Scroll event listener
            window.addEventListener('scroll', handleScroll, { passive: true });

            // Initial call
            handleScroll();
        },

        openProductModal(productData) {
            this.selectedProduct = productData;
            this.showModal = true;
        },

        closeModal() {
            this.showModal = false;
        }
    }
}
