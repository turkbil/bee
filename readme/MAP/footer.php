        <!-- Footer -->
        <footer class="text-center py-8 border-t" style="border-color: var(--border-color)">
            <p class="text-muted">
                © 2025 Türkbil Bee CMS - Multi-Tenant Enterprise Solution
            </p>
        </footer>
    </div>

    <script>
        function app() {
            return {
                theme: 'light',
                scrollProgress: 0,
                activeSection: '',
                
                init() {
                    this.theme = localStorage.getItem('theme') || 'light';
                    this.updateScrollProgress();
                    
                    window.addEventListener('scroll', () => {
                        this.updateScrollProgress();
                        this.updateActiveNavDot();
                    });
                    
                    lucide.createIcons();
                },
                
                updateScrollProgress() {
                    const scrollTop = window.pageYOffset;
                    const docHeight = document.body.offsetHeight - window.innerHeight;
                    this.scrollProgress = (scrollTop / docHeight) * 100;
                },
                
                updateActiveNavDot() {
                    <?php if (isset($nav_sections) && is_array($nav_sections)): ?>
                    const sections = <?php echo json_encode(array_keys($nav_sections)); ?>;
                    const navDots = document.querySelectorAll('.nav-dot');
                    
                    sections.forEach((section, index) => {
                        const element = document.getElementById(section);
                        if (element) {
                            const rect = element.getBoundingClientRect();
                            if (rect.top <= 200 && rect.bottom >= 200) {
                                navDots.forEach(dot => dot.classList.remove('active'));
                                navDots[index]?.classList.add('active');
                                this.activeSection = section;
                            }
                        }
                    });
                    <?php endif; ?>
                },
                
                scrollToSection(sectionId) {
                    const element = document.getElementById(sectionId);
                    if (element) {
                        element.scrollIntoView({ behavior: 'smooth' });
                    }
                },
                
                toggleTheme() {
                    this.theme = this.theme === 'light' ? 'dark' : 'light';
                    localStorage.setItem('theme', this.theme);
                    
                    // Re-initialize Lucide icons after theme change
                    setTimeout(() => {
                        lucide.createIcons();
                    }, 100);
                }
            }
        }
    </script>

</body>
</html>