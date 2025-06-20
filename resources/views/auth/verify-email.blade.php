<x-guest-layout>
    <div class="login-container">
        <!-- Theme Switcher -->
        <div class="theme-switcher">
            <button type="button" class="theme-btn" id="dayMode" onclick="setTheme('day')">
                <i class="fas fa-sun"></i>
            </button>
            <button type="button" class="theme-btn active" id="autoMode" onclick="setTheme('auto')">
                <i class="fas fa-clock"></i>
            </button>
            <button type="button" class="theme-btn" id="nightMode" onclick="setTheme('night')">
                <i class="fas fa-moon"></i>
            </button>
        </div>
        
        <!-- Background Gradient -->
        <div class="login-bg"></div>
        
        <!-- Geometric Background -->
        <div class="geometric-bg">
            <div class="geometric-shape shape-1"></div>
            <div class="geometric-shape shape-2"></div>
            <div class="geometric-shape shape-3"></div>
            <div class="geometric-shape shape-4"></div>
        </div>
        
        <!-- Verify Email Card -->
        <div class="login-card">
            <!-- Logo & Title -->
            <div class="login-header">
                <div class="login-logo">
                    <div class="logo-icon">
                        <i class="fas fa-envelope-circle-check"></i>
                    </div>
                </div>
                <h1 class="login-title">Email Doğrulama</h1>
                <p class="login-subtitle">
                    Kayıt olduğunuz için teşekkürler! Başlamadan önce, size gönderdiğimiz bağlantıya tıklayarak email adresinizi doğrulayabilir misiniz?
                </p>
                <div class="domain-badge">
                    <i class="fas fa-globe"></i>
                    {{ request()->getHost() }}
                </div>
            </div>

            <!-- Status Messages -->
            @if (session('status') == 'verification-link-sent')
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Kayıt sırasında verdiğiniz email adresine yeni bir doğrulama bağlantısı gönderildi.
                </div>
            @endif

            <p class="text-muted text-center mb-4">
                Email'i almadıysanız, size yeni bir tane göndermekten mutluluk duyarız.
            </p>

            <div class="d-flex gap-3">
                <form method="POST" action="{{ route('verification.send') }}" class="flex-fill">
                    @csrf
                    <button type="submit" class="login-btn w-100">
                        <span class="btn-text">Doğrulama Emailini Tekrar Gönder</span>
                        <i class="fas fa-paper-plane btn-icon"></i>
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-sign-out-alt me-2"></i>
                        Çıkış Yap
                    </button>
                </form>
            </div>

            <!-- JavaScript -->
            <script>
                // Theme management
                let currentTheme = localStorage.getItem('theme') || 'auto';
                
                // Initialize theme on page load
                document.addEventListener('DOMContentLoaded', function() {
                    initializeTheme();
                    updateThemeButtons();
                });
                
                function initializeTheme() {
                    if (currentTheme === 'auto') {
                        setAutoTheme();
                    } else {
                        applyTheme(currentTheme);
                    }
                }
                
                function setAutoTheme() {
                    const now = new Date();
                    const hour = now.getHours();
                    
                    // Gece 20:00 - 06:00 arası gece modu
                    if (hour >= 20 || hour < 6) {
                        applyTheme('night');
                    } else {
                        applyTheme('day');
                    }
                }
                
                function setTheme(theme) {
                    currentTheme = theme;
                    localStorage.setItem('theme', theme);
                    
                    if (theme === 'auto') {
                        setAutoTheme();
                    } else {
                        applyTheme(theme);
                    }
                    
                    updateThemeButtons();
                }
                
                function applyTheme(theme) {
                    const body = document.body;
                    
                    if (theme === 'night') {
                        body.classList.add('dark-mode');
                    } else {
                        body.classList.remove('dark-mode');
                    }
                }
                
                function updateThemeButtons() {
                    const buttons = document.querySelectorAll('.theme-btn');
                    buttons.forEach(btn => btn.classList.remove('active'));
                    
                    if (currentTheme === 'day') {
                        document.getElementById('dayMode').classList.add('active');
                    } else if (currentTheme === 'night') {
                        document.getElementById('nightMode').classList.add('active');
                    } else {
                        document.getElementById('autoMode').classList.add('active');
                    }
                }
                
                // Auto theme güncellemesi için timer
                setInterval(() => {
                    if (currentTheme === 'auto') {
                        setAutoTheme();
                    }
                }, 60000); // Her dakika kontrol et
            </script>
        </div>
    </div>
</x-guest-layout>