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
        
        <!-- Reset Password Card -->
        <div class="login-card">
            <!-- Logo & Title -->
            <div class="login-header">
                <div class="login-logo">
                    <div class="logo-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                </div>
                <h1 class="login-title">Şifre Sıfırla</h1>
                <p class="login-subtitle">Yeni şifrenizi belirleyin</p>
                <div class="domain-badge">
                    <i class="fas fa-globe"></i>
                    {{ request()->getHost() }}
                </div>
            </div>

            <!-- Reset Password Form -->
            <form method="POST" action="{{ route('password.store') }}" class="login-form">
                @csrf
                
                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">
                
                <div class="form-group">
                    <div class="input-group">
                        <div class="input-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <input 
                            type="email" 
                            name="email" 
                            value="{{ old('email', $request->email) }}" 
                            required 
                            placeholder="Email adresiniz"
                            class="form-input"
                            autocomplete="username"
                            autofocus
                        />
                    </div>
                    @error('email')
                        <div class="error-message">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <input 
                            type="password" 
                            name="password" 
                            required 
                            placeholder="Yeni şifreniz"
                            class="form-input"
                            autocomplete="new-password"
                        />
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
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

                <div class="form-group">
                    <div class="input-group">
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            required 
                            placeholder="Yeni şifrenizi tekrar girin"
                            class="form-input"
                            autocomplete="new-password"
                        />
                        <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                            <i class="fas fa-eye" id="password_confirmation-eye"></i>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <div class="error-message">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <button type="submit" class="login-btn">
                    <span class="btn-text">Şifreyi Sıfırla</span>
                    <i class="fas fa-check btn-icon"></i>
                </button>
            </form>

            <!-- Back to Login -->
            <div class="demo-users">
                <div class="demo-header">
                    <p class="demo-subtitle">Şifre sıfırlama işlemini iptal etmek ister misiniz?</p>
                </div>
                
                <a href="{{ route('login') }}" class="demo-user-item admin" style="text-decoration: none;">
                    <div class="user-icon admin">
                        <i class="fas fa-sign-in-alt"></i>
                    </div>
                    <div class="user-details">
                        <div class="user-email">Giriş Sayfasına Dön</div>
                        <div class="user-role">Mevcut hesabınızla giriş yapın</div>
                    </div>
                </a>
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
                
                // Toggle password visibility
                function togglePassword(fieldName) {
                    const passwordInput = document.querySelector(`input[name="${fieldName}"]`);
                    const eyeIcon = document.getElementById(`${fieldName}-eye`);
                    
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