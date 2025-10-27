<?php
$page_title = "Mobil Uygulama Sistemi - Türk Bilişim Enterprise CMS";
$page_subtitle = "Mobile App Development";
$page_badge = "📱 Mobil Enterprise";

// Navigation sections
$nav_sections = [
    'hero' => 'Giriş',
    'overview' => 'Genel Bakış',
    'architecture' => 'Mimari',
    'tenant' => 'Tenant Sistemi',
    'platforms' => 'Platform Desteği',
    'features' => 'Özellikler',
    'development' => 'Geliştirme',
    'integration' => 'Entegrasyon'
];

include 'header.php';
?>

<!-- Hero Section -->
<section id="hero" class="hero-section" style="position: relative; background: linear-gradient(135deg, #1a1a2e 0%, #16213e 40%, #0f3460 100%); background-size: 400% 400%; animation: gradientMove 8s ease infinite; overflow: hidden;">
    <!-- Animated Background -->
    <div class="hero-ai-visual" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, rgba(100, 181, 246, 0.1) 0%, rgba(79, 172, 254, 0.1) 25%, rgba(58, 123, 213, 0.1) 50%, rgba(26, 35, 126, 0.1) 100%); background-size: 400% 400%; animation: gradientMove 12s ease infinite reverse;"></div>
    
    <!-- Floating Particles -->
    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; pointer-events: none;">
        <div class="ai-particle" style="position: absolute; width: 4px; height: 4px; background: rgba(100, 181, 246, 0.6); border-radius: 50%; top: 20%; left: 10%; animation: float-ai 15s linear infinite;"></div>
        <div class="ai-particle" style="position: absolute; width: 6px; height: 6px; background: rgba(79, 172, 254, 0.4); border-radius: 50%; top: 60%; left: 80%; animation: float-ai 20s linear infinite 5s;"></div>
        <div class="ai-particle" style="position: absolute; width: 3px; height: 3px; background: rgba(58, 123, 213, 0.7); border-radius: 50%; top: 40%; left: 70%; animation: float-ai 18s linear infinite 2s;"></div>
        <div class="ai-particle" style="position: absolute; width: 5px; height: 5px; background: rgba(100, 181, 246, 0.5); border-radius: 50%; top: 80%; left: 20%; animation: float-ai 22s linear infinite 8s;"></div>
    </div>
    
    <!-- Subtle Glow Effect -->
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 800px; height: 400px; background: radial-gradient(circle, rgba(100, 181, 246, 0.1) 0%, transparent 70%); border-radius: 50%; filter: blur(40px);"></div>
    
    <!-- Hero Content -->
    <div style="position: relative; z-index: 10; max-width: 1100px; margin: 0 auto; padding: 0 2rem;">
        <!-- Main Hero Text -->
        <div style="text-align: center; margin-bottom: 4rem;">
            <div style="display: inline-block; margin-bottom: 2rem; padding: 0.5rem 1.5rem; background: rgba(100, 181, 246, 0.1); border: 1px solid rgba(100, 181, 246, 0.2); border-radius: 30px; backdrop-filter: blur(10px);">
                <span style="color: #64b5f6; font-size: 0.9rem; font-weight: 600;"><?php echo $page_badge; ?></span>
            </div>
            <h1 style="margin-bottom: 2rem; color: white; font-size: 3.5rem; line-height: 1.2; font-weight: 300; letter-spacing: -0.02em; text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);">
                Mobil Uygulama<br>
                <span style="font-weight: 700; background: linear-gradient(45deg, #64b5f6, #4facfe, #64b5f6); background-size: 200% 200%; animation: textGradient 3s ease infinite; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Geliştirme Sistemi</span>
            </h1>
            <p style="margin-bottom: 3rem; font-size: 1.2rem; line-height: 1.6; color: rgba(255, 255, 255, 0.8); max-width: 600px; margin-left: auto; margin-right: auto; font-weight: 400; text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);">
                Tenant bazlı mobil uygulamalar<br>
                <span style="color: #64b5f6; font-weight: 600;">Cross-platform development</span>
            </p>
        </div>
    </div>
</section>

<!-- Overview Section -->
<section id="overview" class="section">
    <h2 class="section-title">Mobil Uygulama Sistemi Gücü</h2>
    <p class="section-subtitle">
        Flutter teknolojisi ile geliştirilmiş tenant-aware mobil uygulama sistemi. 
        Android ve iOS platformları için native performance, modern UI/UX tasarım 
        ve enterprise-grade backend entegrasyonu. Multi-tenant architecture ile 
        her müşteri için özelleştirilmiş mobil deneyim.
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="smartphone"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Flutter Cross-Platform <span class="text-sm text-muted">(çapraz platform geliştirme)</span></h3>
            <p class="text-secondary">
                Google'ın Flutter framework'ü ile tek kod tabanından hem Android hem iOS uygulamaları geliştiriyoruz. <span class="tech-highlight">Single codebase</span> 
                <span class="text-muted">(tek kod tabanı)</span><br>
                <span class="text-sm text-secondary">→ Aynı kod hem Android hem iOS'ta çalışır</span>
                ile %50 daha hızlı geliştirme ve native performance garantisi.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Cross-Platform Compatibility</span> <span class="text-muted">(çapraz platform uyumluluğu)</span><br><span class="text-sm text-secondary">→ Android ve iOS için tek geliştirme süreci</span></li>
                        <li>• <span class="tech-highlight">Native Performance</span> <span class="text-muted">(native performans)</span><br><span class="text-sm text-secondary">→ Native uygulamalar kadar hızlı çalışma</span></li>
                        <li>• <span class="tech-highlight">Hot Reload Development</span> <span class="text-muted">(anında geliştirme)</span><br><span class="text-sm text-secondary">→ Değişiklikleri anında görebilme ve test etme</span></li>
                        <li>• <span class="tech-highlight">Modern UI Components</span> <span class="text-muted">(modern arayüz bileşenleri)</span><br><span class="text-sm text-secondary">→ Material Design ve Cupertino widget'ları</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="layers"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Tenant-Aware Mobile Architecture <span class="text-sm text-muted">(tenant destekli mobil mimari)</span></h3>
            <p class="text-secondary">
                Her tenant için özelleştirilmiş mobil uygulama deneyimi. <span class="tech-highlight">Dynamic Configuration</span> 
                <span class="text-muted">(dinamik konfigürasyon)</span><br>
                <span class="text-sm text-secondary">→ Uygulama tenant'a göre otomatik ayarlanır</span>
                sistemi ile branding, theme, feature set'ler tenant bazında özelleştirilebilir.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Dynamic Tenant Detection</span> <span class="text-muted">(dinamik tenant tespiti)</span><br><span class="text-sm text-secondary">→ Kullanıcı giriş yaptığında tenant otomatik belirlenir</span></li>
                        <li>• <span class="tech-highlight">Custom Branding Per Tenant</span> <span class="text-muted">(tenant bazında özel markalama)</span><br><span class="text-sm text-secondary">→ Logo, renkler, tema her tenant için farklı</span></li>
                        <li>• <span class="tech-highlight">Feature Toggle System</span> <span class="text-muted">(özellik açma/kapama sistemi)</span><br><span class="text-sm text-secondary">→ Her tenant için hangi özellikler aktif olacağı</span></li>
                        <li>• <span class="tech-highlight">Isolated Data Sync</span> <span class="text-muted">(izole veri senkronizasyonu)</span><br><span class="text-sm text-secondary">→ Her tenant'ın verisi ayrı endpoint'lerden gelir</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="code"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Laravel_Tuufi Project Ecosystem <span class="text-sm text-muted">(geliştirilmekte olan proje)</span></h3>
            <p class="text-secondary mb-3">
                Flutter 3.8+ teknolojisi ile geliştirilmekte olan laravel_tuufi projesi. Modern state management, secure authentication, advanced theming ve comprehensive logging sistemi ile enterprise-ready mobil platform.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Flutter 3.8+ Framework</span> <span class="text-muted">(güncel Flutter teknolojisi)</span><br><span class="text-sm text-secondary">→ En son Flutter özellikleri ve performance iyileştirmeleri</span></li>
                        <li>• <span class="tech-highlight">Provider State Management</span> <span class="text-muted">(durum yönetimi)</span><br><span class="text-sm text-secondary">→ Uygulama durumunun etkin yönetimi</span></li>
                        <li>• <span class="tech-highlight">HTTP API Integration</span> <span class="text-muted">(API entegrasyonu)</span><br><span class="text-sm text-secondary">→ Laravel backend ile seamless iletişim</span></li>
                        <li>• <span class="tech-highlight">Secure Token Management</span> <span class="text-muted">(güvenli token yönetimi)</span><br><span class="text-sm text-secondary">→ JWT token'lar ile güvenli authentication</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="palette"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Advanced Theming & UI System <span class="text-sm text-muted">(gelişmiş tema sistemi)</span></h3>
            <p class="text-secondary mb-3">
                FlexColorScheme ve Dynamic Color desteği ile modern Material Design 3 theming sistemi. Dark/Light mode, custom color schemes ve tenant-specific branding ile kişiselleştirilmiş kullanıcı deneyimi.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Material Design 3</span> <span class="text-muted">(modern tasarım sistemi)</span><br><span class="text-sm text-secondary">→ Google'ın en son tasarım rehberleri</span></li>
                        <li>• <span class="tech-highlight">Dynamic Color Adaptation</span> <span class="text-muted">(dinamik renk adaptasyonu)</span><br><span class="text-sm text-secondary">→ Sistem renklerine otomatik uyum</span></li>
                        <li>• <span class="tech-highlight">FlexColorScheme Integration</span> <span class="text-muted">(gelişmiş renk yönetimi)</span><br><span class="text-sm text-secondary">→ 40+ built-in tema ve custom color schemes</span></li>
                        <li>• <span class="tech-highlight">Tenant-Specific Branding</span> <span class="text-muted">(tenant özel markalama)</span><br><span class="text-sm text-secondary">→ Her müşteri için özel logo, renk ve tema</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="shield"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Enterprise Security & Authentication <span class="text-sm text-muted">(kurumsal güvenlik)</span></h3>
            <p class="text-secondary mb-3">
                Kapsamlı güvenlik katmanları ile enterprise-grade mobile security. JWT token authentication, secure storage, API security ve tenant-based access control sistemi.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">JWT Token Authentication</span> <span class="text-muted">(güvenli kimlik doğrulama)</span><br><span class="text-sm text-secondary">→ Stateless, secure token-based authentication</span></li>
                        <li>• <span class="tech-highlight">Secure Local Storage</span> <span class="text-muted">(güvenli yerel depolama)</span><br><span class="text-sm text-secondary">→ SharedPreferences ile encrypted data storage</span></li>
                        <li>• <span class="tech-highlight">API Security Headers</span> <span class="text-muted">(API güvenlik başlıkları)</span><br><span class="text-sm text-secondary">→ HTTPS, CORS, rate limiting korumaları</span></li>
                        <li>• <span class="tech-highlight">Tenant Access Control</span> <span class="text-muted">(tenant erişim kontrolü)</span><br><span class="text-sm text-secondary">→ Kullanıcı sadece kendi tenant'ın verilerine erişir</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="activity"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Comprehensive Logging & Analytics <span class="text-sm text-muted">(kapsamlı kayıt sistemi)</span></h3>
            <p class="text-secondary mb-3">
                Advanced logging framework ile development ve production environment'larda comprehensive monitoring. Error tracking, performance analytics ve user behavior insights.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Multi-Level Logging</span> <span class="text-muted">(çok seviyeli kayıt)</span><br><span class="text-sm text-secondary">→ Debug, Info, Warning, Error seviyelerinde logging</span></li>
                        <li>• <span class="tech-highlight">Crash Analytics</span> <span class="text-muted">(çökme analitiği)</span><br><span class="text-sm text-secondary">→ Uygulama çökmelerini otomatik tespit ve raporlama</span></li>
                        <li>• <span class="tech-highlight">Performance Monitoring</span> <span class="text-muted">(performans izleme)</span><br><span class="text-sm text-secondary">→ API response time, UI performance tracking</span></li>
                        <li>• <span class="tech-highlight">User Analytics</span> <span class="text-muted">(kullanıcı analitiği)</span><br><span class="text-sm text-secondary">→ Feature usage, user journey ve engagement metrics</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Architecture -->
<section id="architecture" class="section">
    <h2 class="section-title text-center">Mobil Uygulama Mimarisi</h2>
    <p class="section-subtitle text-center">
        Modern Flutter architecture pattern'leri ile scalable ve maintainable mobile development
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="layers" class="w-6 h-6"></i>
            </div>
            <h3>Clean Architecture Pattern</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Presentation Layer (UI/Screens)</li>
                    <li>• Business Logic Layer (Providers)</li>
                    <li>• Data Layer (Services/APIs)</li>
                    <li>• Infrastructure Layer (Utils/Helpers)</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="database" class="w-6 h-6"></i>
            </div>
            <h3>State Management</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Provider pattern implementation</li>
                    <li>• ChangeNotifier for reactive UI</li>
                    <li>• Global state management</li>
                    <li>• Local widget state optimization</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="plug" class="w-6 h-6"></i>
            </div>
            <h3>API Integration Layer</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• HTTP client configuration</li>
                    <li>• Request/Response interceptors</li>
                    <li>• Error handling strategies</li>
                    <li>• Retry mechanisms</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="smartphone" class="w-6 h-6"></i>
            </div>
            <h3>Platform Optimization</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• iOS specific configurations</li>
                    <li>• Android specific optimizations</li>
                    <li>• Platform adaptive UI components</li>
                    <li>• Native plugin integrations</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Tenant System -->
<section id="tenant" class="section">
    <h2 class="section-title text-center">Tenant Bazlı Mobil Sistemler</h2>
    <p class="section-subtitle text-center">
        Her müşteri için özelleştirilmiş mobil uygulama deneyimi
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="settings" class="w-6 h-6"></i>
            </div>
            <h3>Dynamic Configuration</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Tenant-specific app configurations</li>
                    <li>• Remote feature flag management</li>
                    <li>• API endpoint customization</li>
                    <li>• Real-time config updates</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="palette" class="w-6 h-6"></i>
            </div>
            <h3>Custom Branding</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Tenant-specific logos ve assets</li>
                    <li>• Custom color schemes</li>
                    <li>• Font ve typography customization</li>
                    <li>• App icon ve splash screen variants</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="database" class="w-6 h-6"></i>
            </div>
            <h3>Isolated Data Management</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Tenant-specific API endpoints</li>
                    <li>• Secure data isolation</li>
                    <li>• Offline data caching per tenant</li>
                    <li>• Sync conflict resolution</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
            <h3>User Management</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Tenant-aware authentication</li>
                    <li>• Role-based access control</li>
                    <li>• Multi-tenant user profiles</li>
                    <li>• Permission-based UI rendering</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Platform Support -->
<section id="platforms" class="section">
    <h2 class="section-title text-center">Platform Desteği</h2>
    <p class="section-subtitle text-center">
        Android ve iOS için native-level performance ve özellikleri
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="smartphone" class="w-6 h-6"></i>
            </div>
            <h3>Android Development</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Android 7.0+ (API Level 24+) support</li>
                    <li>• Material Design 3 components</li>
                    <li>• Android-specific permissions</li>
                    <li>• Google Play Store optimization</li>
                    <li>• ProGuard ve R8 code optimization</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="tablet" class="w-6 h-6"></i>
            </div>
            <h3>iOS Development</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• iOS 12.0+ compatibility</li>
                    <li>• Cupertino design language</li>
                    <li>• iOS-specific security features</li>
                    <li>• App Store submission ready</li>
                    <li>• Swift interoperability</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="monitor" class="w-6 h-6"></i>
            </div>
            <h3>Desktop Extensions</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Windows desktop support</li>
                    <li>• macOS native application</li>
                    <li>• Linux compatibility</li>
                    <li>• Responsive desktop UI</li>
                    <li>• Cross-platform data sync</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="globe" class="w-6 h-6"></i>
            </div>
            <h3>Web Progressive App</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Progressive Web App (PWA)</li>
                    <li>• Offline functionality</li>
                    <li>• Web push notifications</li>
                    <li>• Service worker caching</li>
                    <li>• Responsive web design</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Features -->
<section id="features" class="section">
    <h2 class="section-title text-center">Mobil Uygulama Özellikleri</h2>
    <p class="section-subtitle text-center">
        Modern mobil uygulama geliştirme için gerekli tüm özellikler
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="user-check" class="w-6 h-6"></i>
            </div>
            <h3>Authentication & Security</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• JWT token authentication</li>
                    <li>• Biometric authentication (Touch/Face ID)</li>
                    <li>• Two-factor authentication (2FA)</li>
                    <li>• Secure token storage</li>
                    <li>• Auto-logout on inactivity</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="wifi-off" class="w-6 h-6"></i>
            </div>
            <h3>Offline Capabilities</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Local database caching</li>
                    <li>• Offline data synchronization</li>
                    <li>• Conflict resolution strategies</li>
                    <li>• Background sync processes</li>
                    <li>• Cached UI components</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="bell" class="w-6 h-6"></i>
            </div>
            <h3>Push Notifications</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Firebase Cloud Messaging (FCM)</li>
                    <li>• Rich notification content</li>
                    <li>• Action buttons ve deep linking</li>
                    <li>• Notification scheduling</li>
                    <li>• Custom notification sounds</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="camera" class="w-6 h-6"></i>
            </div>
            <h3>Media & File Handling</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Camera ve gallery integration</li>
                    <li>• Image editing ve filtering</li>
                    <li>• File upload/download</li>
                    <li>• Document scanner</li>
                    <li>• Media compression optimization</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="map" class="w-6 h-6"></i>
            </div>
            <h3>Location Services</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• GPS location tracking</li>
                    <li>• Google Maps integration</li>
                    <li>• Geofencing capabilities</li>
                    <li>• Location-based notifications</li>
                    <li>• Address geocoding</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="share-2" class="w-6 h-6"></i>
            </div>
            <h3>Social Integration</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Social media sharing</li>
                    <li>• Contact book integration</li>
                    <li>• In-app messaging</li>
                    <li>• Video calling support</li>
                    <li>• Social authentication</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Development -->
<section id="development" class="section">
    <h2 class="section-title text-center">Geliştirme Süreci</h2>
    <p class="section-subtitle text-center">
        Profesyonel mobil uygulama geliştirme workflow'u
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="code" class="w-6 h-6"></i>
            </div>
            <h3>Development Environment</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Flutter SDK 3.8+ setup</li>
                    <li>• Android Studio / VS Code IDE</li>
                    <li>• Device simulators ve emulators</li>
                    <li>• Hot reload development</li>
                    <li>• Debug ve profiling tools</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="git-branch" class="w-6 h-6"></i>
            </div>
            <h3>Version Control & CI/CD</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Git repository management</li>
                    <li>• Automated testing pipelines</li>
                    <li>• Continuous integration setup</li>
                    <li>• Automated deployment</li>
                    <li>• Release management</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="test-tube" class="w-6 h-6"></i>
            </div>
            <h3>Testing Strategy</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Unit testing for business logic</li>
                    <li>• Widget testing for UI components</li>
                    <li>• Integration testing</li>
                    <li>• End-to-end testing</li>
                    <li>• Performance testing</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="rocket" class="w-6 h-6"></i>
            </div>
            <h3>Deployment & Distribution</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Google Play Store deployment</li>
                    <li>• Apple App Store submission</li>
                    <li>• Enterprise distribution</li>
                    <li>• Beta testing programs</li>
                    <li>• OTA updates</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Integration -->
<section id="integration" class="section">
    <h2 class="section-title text-center">Backend Entegrasyonu</h2>
    <p class="section-subtitle text-center">
        Laravel CMS ile seamless entegrasyon ve API connectivity
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="server" class="w-6 h-6"></i>
            </div>
            <h3>Laravel API Integration</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• RESTful API consumption</li>
                    <li>• Laravel Sanctum authentication</li>
                    <li>• API resource transformations</li>
                    <li>• Error handling ve retry logic</li>
                    <li>• Request caching strategies</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="database" class="w-6 h-6"></i>
            </div>
            <h3>Real-time Data Sync</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• WebSocket connections</li>
                    <li>• Laravel Echo integration</li>
                    <li>• Real-time notifications</li>
                    <li>• Live data updates</li>
                    <li>• Conflict resolution</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="cloud" class="w-6 h-6"></i>
            </div>
            <h3>Cloud Services</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Firebase integration</li>
                    <li>• AWS services connectivity</li>
                    <li>• Cloud storage solutions</li>
                    <li>• CDN integration</li>
                    <li>• Backup ve recovery</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="shield-check" class="w-6 h-6"></i>
            </div>
            <h3>Security & Compliance</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• End-to-end encryption</li>
                    <li>• GDPR compliance</li>
                    <li>• Data privacy protection</li>
                    <li>• Security auditing</li>
                    <li>• Penetration testing</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>