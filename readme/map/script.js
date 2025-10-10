function presentationApp() {
    return {
        theme: localStorage.getItem('theme') || 'theme-light',
        activeSection: 'hero',
        scrollProgress: 0,
        
        init() {
            this.updateScrollProgress();
            this.updateActiveSection();
            this.initAIParticles();
            // Initialize Lucide icons
            lucide.createIcons();
        },
        
        initAIParticles() {
            const container = document.querySelector('.hero-ai-visual');
            if (!container) return;
            
            const createParticle = () => {
                const particle = document.createElement('div');
                particle.className = 'ai-particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 2 + 's';
                particle.style.animationDuration = (10 + Math.random() * 10) + 's';
                container.appendChild(particle);
                
                // Remove particle after animation
                setTimeout(() => {
                    particle.remove();
                }, 20000);
            };
            
            // Create particles continuously
            setInterval(createParticle, 800);
            
            // Create initial particles
            for (let i = 0; i < 5; i++) {
                setTimeout(createParticle, i * 200);
            }
        },
        
        toggleTheme() {
            this.theme = this.theme === 'theme-light' ? 'theme-dark' : 'theme-light';
            localStorage.setItem('theme', this.theme);
            // Re-initialize icons after theme change
            this.$nextTick(() => {
                lucide.createIcons();
            });
        },
        
        updateScrollProgress() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
            this.scrollProgress = Math.min(100, Math.max(0, (scrollTop / scrollHeight) * 100));
        },
        
        updateActiveSection() {
            const sections = ['hero', 'overview', 'architecture', 'features', 'technology', 'performance', 'pages', 'roadmap'];
            const scrollPosition = window.pageYOffset + 200;
            
            let newActiveSection = 'hero';
            
            for (let i = sections.length - 1; i >= 0; i--) {
                const section = document.getElementById(sections[i]);
                if (section && section.offsetTop <= scrollPosition) {
                    newActiveSection = sections[i];
                    break;
                }
            }
            
            if (this.activeSection !== newActiveSection) {
                this.activeSection = newActiveSection;
            }
        },
        
        scrollToSection(sectionId) {
            const element = document.getElementById(sectionId);
            if (element) {
                element.scrollIntoView({ behavior: 'smooth' });
            }
        }
    }
}

// Listen for scroll events
window.addEventListener('scroll', () => {
    // Get the Alpine.js app instance
    const appEl = document.querySelector('[x-data]');
    if (appEl && appEl._x_dataStack && appEl._x_dataStack[0]) {
        const app = appEl._x_dataStack[0];
        app.updateScrollProgress();
        app.updateActiveSection();
    }
});

// Initialize icons when page loads
document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();
});