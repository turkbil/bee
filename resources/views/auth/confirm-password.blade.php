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
        
        <!-- Confirm Password Card -->
        <div class="login-card">
            <!-- Logo & Title -->
            <div class="login-header">
                <div class="login-logo">
                    <div class="logo-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                </div>
                <h1 class="login-title">Şifre Onayı</h1>
                <p class="login-subtitle">Bu güvenli bir alandır. Devam etmeden önce lütfen şifrenizi onaylayın.</p>
                <div class="domain-badge">
                    <i class="fas fa-globe"></i>
                    {{ request()->getHost() }}
                </div>
            </div>

            <!-- Confirm Password Form -->
            <form method="POST" action="{{ route('password.confirm') }}" class="login-form">
                @csrf
                
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <input 
                            type="password" 
                            name="password" 
                            required 
                            placeholder="Şifrenizi girin"
                            class="form-input"
                            autocomplete="current-password"
                            autofocus
                        />
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="password-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="error-message">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <button type="submit" class="login-btn">
                    <span class="btn-text">Onayla</span>
                    <i class="fas fa-check btn-icon"></i>
                </button>
            </form>

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
                
                // Toggle password visibility
                function togglePassword() {
                    const passwordInput = document.querySelector('input[name="password"]');
                    const eyeIcon = document.getElementById('password-eye');
                    
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        eyeIcon.className = 'fas fa-eye-slash';
                    } else {
                        passwordInput.type = 'password';
                        eyeIcon.className = 'fas fa-eye';
                    }
                }
            </script>
        </div>
    </div>
</x-guest-layout>