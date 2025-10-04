    <!-- Footer -->
    <footer class="bg-gray-900 text-white">
        <div class="container mx-auto px-4 py-16">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">Türk Bilişim</h3>
                    <p class="text-gray-400 mb-4">
                        Enterprise CMS çözümleri ile dijital dönüşümünüzü destekliyoruz.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-linkedin"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-github"></i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Ürünler</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="features.php" class="hover:text-white transition-colors">CMS Özellikleri</a></li>
                        <li><a href="ai-features.php" class="hover:text-white transition-colors">AI Sistemi</a></li>
                        <li><a href="widget-system.php" class="hover:text-white transition-colors">Widget Sistemi</a></li>
                        <li><a href="theme-system.php" class="hover:text-white transition-colors">Tema Sistemi</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Geliştirici</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="api-documentation.php" class="hover:text-white transition-colors">API Dökümantasyonu</a></li>
                        <li><a href="technology.php" class="hover:text-white transition-colors">Teknoloji Stack</a></li>
                        <li><a href="architecture.php" class="hover:text-white transition-colors">Sistem Mimarisi</a></li>
                        <li><a href="security.php" class="hover:text-white transition-colors">Güvenlik</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Destek</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">Dokümantasyon</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Yardım Merkezi</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">İletişim</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Topluluk</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
                <p>&copy; 2024 Türk Bilişim. Tüm hakları saklıdır.</p>
            </div>
        </div>
    </footer>
    
    <!-- Scripts -->
    <script>
        // Alpine.js app
        function app() {
            return {
                theme: localStorage.getItem('theme') || 'light',
                scrollProgress: 0,
                activeSection: '',
                
                init() {
                    this.updateTheme();
                    this.updateScrollProgress();
                    window.addEventListener('scroll', () => this.updateScrollProgress());
                    
                    // Close dropdowns on escape key
                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape') {
                            // Close all dropdowns
                            document.querySelectorAll('.nav-dropdown').forEach(dropdown => {
                                const alpine = Alpine.getStore ? Alpine.getStore(dropdown) : null;
                                if (alpine && alpine.open) {
                                    alpine.open = false;
                                }
                            });
                        }
                    });
                },
                
                toggleTheme() {
                    this.theme = this.theme === 'light' ? 'dark' : 'light';
                    this.updateTheme();
                    localStorage.setItem('theme', this.theme);
                },
                
                updateTheme() {
                    document.documentElement.className = `theme-${this.theme}`;
                },
                
                updateScrollProgress() {
                    const scrolled = window.pageYOffset;
                    const maxScroll = document.documentElement.scrollHeight - window.innerHeight;
                    this.scrollProgress = (scrolled / maxScroll) * 100;
                },
                
                scrollToSection(sectionId) {
                    const section = document.getElementById(sectionId);
                    if (section) {
                        section.scrollIntoView({ behavior: 'smooth' });
                    }
                }
            }
        }
        
        // Initialize Lucide icons
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>