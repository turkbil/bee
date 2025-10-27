<?php
$page_title = "Admin Panel - Yönetim Paneli";
$page_subtitle = "Güçlü Yönetim Sistemi";
$page_badge = "Admin Panel";
$nav_sections = [
    'dashboard' => 'Dashboard',
    'management' => 'İçerik Yönetimi', 
    'users' => 'Kullanıcı Yönetimi',
    'settings' => 'Sistem Ayarları',
    'tools' => 'Araçlar',
    'mobile' => 'Mobil Panel'
];
include 'header.php';
?>
    
    <!-- Hero Section -->
    <section class="hero-section" style="background: linear-gradient(135deg, var(--accent-color), var(--success-color));">
        <div class="hero-ai-visual">
            <div class="ai-particle" style="left: 10%; animation-delay: 0s;"></div>
            <div class="ai-particle" style="left: 20%; animation-delay: 3s;"></div>
            <div class="ai-particle" style="left: 30%; animation-delay: 6s;"></div>
            <div class="ai-particle" style="left: 40%; animation-delay: 9s;"></div>
            <div class="ai-particle" style="left: 50%; animation-delay: 12s;"></div>
            <div class="ai-particle" style="left: 60%; animation-delay: 15s;"></div>
            <div class="ai-particle" style="left: 70%; animation-delay: 18s;"></div>
            <div class="ai-particle" style="left: 80%; animation-delay: 21s;"></div>
            <div class="ai-particle" style="left: 90%; animation-delay: 24s;"></div>
        </div>
        <div class="hero-bee-hive">
            <i data-lucide="settings" class="hero-bee-icon"></i>
        </div>
        <div class="relative z-10 container mx-auto px-4 py-20">
            <div class="max-w-4xl mx-auto text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mb-6">
                    <i data-lucide="shield-check" class="w-8 h-8 text-white"></i>
                </div>
                <h1 class="hero-title">
                    Admin Panel
                    <span class="block text-xl md:text-2xl font-normal opacity-90 mt-2">
                        <span class="text-muted">(yönetim paneli)</span><br>
                        <span class="text-sm text-secondary">→ Web sitenizi merkezi olarak yönetmenizi sağlayan kontrol paneli</span>
                    </span>
                </h1>
                <p class="hero-subtitle">
                    Modern, responsive ve kullanıcı dostu admin paneli ile web sitenizi kolayca yönetin. 
                    Gelişmiş dashboard, detaylı raporlama ve kapsamlı yönetim araçları.
                </p>
            </div>
        </div>
    </section>

    <!-- Dashboard Overview Section -->
    <section id="dashboard" class="section" style="background-color: var(--bg-secondary);">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="section-title">
                        Gelişmiş Dashboard
                        <span class="block text-lg text-accent">Advanced Dashboard</span>
                    </h2>
                    <p class="section-subtitle">
                        <span class="tech-highlight">Real-time analytics</span> <span class="text-muted">(gerçek zamanlı analitik)</span><br>
                        <span class="text-sm text-secondary">→ Anlık veri akışı ile sitenizin performansını canlı olarak izleyebilme</span>, 
                        <span class="tech-highlight">customizable widgets</span> <span class="text-muted">(özelleştirilebilir widget'lar)</span><br>
                        <span class="text-sm text-secondary">→ İhtiyaçlarınıza göre düzenleyebileceğiniz görsel bileşenler</span> 
                        ve <span class="tech-highlight">comprehensive reports</span> <span class="text-muted">(kapsamlı raporlar)</span><br>
                        <span class="text-sm text-secondary">→ Detaylı analiz ve raporlama sistemi</span> ile sitenizin performansını takip edin.
                    </p>
                </div>

                <div class="grid md:grid-cols-4 gap-8 mb-16">
                    <div class="stat-item">
                        <div class="feature-icon mx-auto mb-4">
                            <i data-lucide="trending-up" class="w-6 h-6"></i>
                        </div>
                        <h3 class="stat-value">Gerçek Zamanlı</h3>
                        <p class="stat-label">
                            Ziyaretçi Takibi
                            <span class="block text-xs text-muted">Real-time Visitor Tracking</span>
                        </p>
                        <div class="flex items-center justify-center mt-3">
                            <i data-lucide="activity" class="w-4 h-4 text-success mr-1"></i>
                            <span class="text-success text-sm">Canlı Veri</span>
                        </div>
                    </div>

                    <div class="stat-item">
                        <div class="feature-icon mx-auto mb-4">
                            <i data-lucide="file-text" class="w-6 h-6"></i>
                        </div>
                        <h3 class="stat-value">Sınırsız</h3>
                        <p class="stat-label">
                            İçerik Yönetimi
                            <span class="block text-xs text-muted">Content Management</span>
                        </p>
                        <div class="flex items-center justify-center mt-3">
                            <i data-lucide="infinity" class="w-4 h-4 text-success mr-1"></i>
                            <span class="text-success text-sm">Dinamik</span>
                        </div>
                    </div>

                    <div class="stat-item">
                        <div class="feature-icon mx-auto mb-4">
                            <i data-lucide="users" class="w-6 h-6"></i>
                        </div>
                        <h3 class="stat-value">Çok Katmanlı</h3>
                        <p class="stat-label">
                            Kullanıcı Yönetimi
                            <span class="block text-xs text-muted">User Management</span>
                        </p>
                        <div class="flex items-center justify-center mt-3">
                            <i data-lucide="shield-check" class="w-4 h-4 text-success mr-1"></i>
                            <span class="text-success text-sm">Güvenli</span>
                        </div>
                    </div>

                    <div class="stat-item">
                        <div class="feature-icon mx-auto mb-4">
                            <i data-lucide="shopping-cart" class="w-6 h-6"></i>
                        </div>
                        <h3 class="stat-value">Detaylı</h3>
                        <p class="stat-label">
                            Performans Analizi
                            <span class="block text-xs text-muted">Performance Analytics</span>
                        </p>
                        <div class="flex items-center justify-center mt-3">
                            <i data-lucide="zap" class="w-4 h-4 text-success mr-1"></i>
                            <span class="text-success text-sm">Optimize</span>
                        </div>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-8">
                    <div class="card">
                        <div class="gradient-bg p-6">
                            <h3 class="text-xl font-bold text-white mb-2">
                                Ziyaretçi Analizi
                                <span class="block text-sm opacity-90">Visitor Analytics</span>
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="h-48 card-accent rounded-lg flex items-center justify-center mb-4">
                                <div class="text-center text-muted">
                                    <i data-lucide="bar-chart-3" class="w-16 h-16 mb-2"></i>
                                    <p class="font-medium">Interactive Chart</p>
                                    <p class="text-sm">Real-time visitor data</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 text-center">
                                <div>
                                    <h4 class="text-lg font-semibold text-primary">Günlük</h4>
                                    <p class="text-sm text-secondary">Analiz</p>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-primary">Haftalık</h4>
                                    <p class="text-sm text-secondary">Rapor</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="gradient-bg p-6">
                            <h3 class="text-xl font-bold text-white mb-2">
                                Popüler İçerikler
                                <span class="block text-sm opacity-90">Popular Content</span>
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-3 card-accent rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-accent rounded-full flex items-center justify-center mr-3">
                                            <span class="text-white font-semibold text-sm">1</span>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-primary">Popüler İçerik</h4>
                                            <p class="text-sm text-secondary">Yüksek Etkileşim</p>
                                        </div>
                                    </div>
                                    <i data-lucide="eye" class="w-4 h-4 text-muted"></i>
                                </div>
                                <div class="flex items-center justify-between p-3 card-accent rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-accent rounded-full flex items-center justify-center mr-3">
                                            <span class="text-white font-semibold text-sm">2</span>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-primary">Optimizasyon İçeriği</h4>
                                            <p class="text-sm text-secondary">Güçlü Performans</p>
                                        </div>
                                    </div>
                                    <i data-lucide="eye" class="w-4 h-4 text-muted"></i>
                                </div>
                                <div class="flex items-center justify-between p-3 card-accent rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-accent rounded-full flex items-center justify-center mr-3">
                                            <span class="text-white font-semibold text-sm">3</span>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-primary">Dinamik Çözümler</h4>
                                            <p class="text-sm text-secondary">Etkili Sonuçlar</p>
                                        </div>
                                    </div>
                                    <i data-lucide="eye" class="w-4 h-4 text-muted"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Content Management Section -->
    <section id="management" class="section" style="background-color: var(--bg-primary);">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="section-title">
                        İçerik Yönetimi
                        <span class="block text-lg text-accent">Content Management</span>
                    </h2>
                    <p class="section-subtitle">
                        <span class="tech-highlight">Drag & drop editor</span> <span class="text-muted">(sürükle bırak editörü)</span><br>
                        <span class="text-sm text-secondary">→ İçeriği görsel olarak düzenleyebileceğiniz kolay arayüz</span>, 
                        <span class="tech-highlight">bulk operations</span> <span class="text-muted">(toplu işlemler)</span><br>
                        <span class="text-sm text-secondary">→ Çok sayıda içeriği aynı anda düzenleyebilme yeteneği</span> 
                        ve <span class="tech-highlight">advanced search</span> <span class="text-muted">(gelişmiş arama)</span><br>
                        <span class="text-sm text-secondary">→ Detaylı filtrelerle hızlı içerik bulma sistemi</span> 
                        özellikleri ile içerik yönetimini kolaylaştırın.
                    </p>
                </div>

                <div class="feature-grid">
                    <div class="feature-card">
                        <div class="feature-icon mb-6">
                            <i data-lucide="edit" class="w-6 h-6"></i>
                        </div>
                        <h3 class="text-xl font-bold text-primary mb-3">
                            <span class="tech-highlight">WYSIWYG Editor</span> <span class="text-muted">(görsel editör)</span><br>
                            <span class="text-sm text-secondary">→ Gördüğünüz şeyi alırsınız prensibiyle çalışan editör</span>
                        </h3>
                        <p class="text-secondary mb-4">
                            <span class="tech-highlight">Rich text editing</span> <span class="text-muted">(zengin metin editörü)</span><br>
                            <span class="text-sm text-secondary">→ Metnin formatını, rengini, boyutunu kolaylıkla değiştirebileceğiniz sistem</span> ile içerik oluşturun. 
                            Media embedding, table support ve HTML editing özellikleri.
                        </p>
                        <ul class="list-none space-y-2 text-sm text-secondary">
                            <li>• Drag & drop media upload</li>
                            <li>• Code syntax highlighting</li>
                            <li>• Template system</li>
                            <li>• Version control</li>
                        </ul>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon mb-6">
                            <i data-lucide="layers" class="w-6 h-6"></i>
                        </div>
                        <h3 class="text-xl font-bold text-primary mb-3">
                            <span class="tech-highlight">Page Builder</span> <span class="text-muted">(sayfa oluşturucu)</span><br>
                            <span class="text-sm text-secondary">→ Kod yazmadan görsel olarak sayfa tasarımı yapabileceğiniz araç</span>
                        </h3>
                        <p class="text-secondary mb-4">
                            <span class="tech-highlight">Visual page builder</span> <span class="text-muted">(görsel sayfa oluşturucu)</span><br>
                            <span class="text-sm text-secondary">→ Blok blok sayfa tasarımı yapabileceğiniz görsel arayüz</span> ile responsive sayfalar oluşturun. 
                            Pre-built components ve custom widgets desteği.
                        </p>
                        <ul class="list-none space-y-2 text-sm text-secondary">
                            <li>• Responsive design</li>
                            <li>• Widget library</li>
                            <li>• Custom CSS/JS</li>
                            <li>• Template inheritance</li>
                        </ul>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon mb-6">
                            <i data-lucide="image" class="w-6 h-6"></i>
                        </div>
                        <h3 class="text-xl font-bold text-primary mb-3">
                            <span class="tech-highlight">Media Management</span> <span class="text-muted">(medya yönetimi)</span><br>
                            <span class="text-sm text-secondary">→ Görsel, video ve dosyaları merkezi olarak yönetme sistemi</span>
                        </h3>
                        <p class="text-secondary mb-4">
                            <span class="tech-highlight">Media library</span> <span class="text-muted">(medya kütüphanesi)</span><br>
                            <span class="text-sm text-secondary">→ Tüm dijital içeriğin organize edildiği merkezi depolama alanı</span> ile görsel, video ve dokuman yönetimi. 
                            <span class="tech-highlight">Automatic optimization</span> <span class="text-muted">(otomatik optimizasyon)</span><br>
                            <span class="text-sm text-secondary">→ Yüklenen dosyaların otomatik olarak boyut ve kalite ayarlaması</span> ve CDN entegrasyonu.
                        </p>
                        <ul class="list-none space-y-2 text-sm text-secondary">
                            <li>• Image optimization</li>
                            <li>• Video streaming</li>
                            <li>• Cloud storage</li>
                            <li>• SEO meta tags</li>
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="gradient-bg p-8">
                        <h3 class="text-2xl font-bold text-white mb-4">
                            Gelişmiş İçerik Özellikleri
                            <span class="block text-lg opacity-90">Advanced Content Features</span>
                        </h3>
                        <p class="text-white opacity-80">
                            Professional content management için gelişmiş özellikler. 
                            <span class="tech-highlight bg-white text-accent">Workflow management</span>, content scheduling ve collaboration tools.
                        </p>
                    </div>
                    <div class="p-8">
                        <div class="grid md:grid-cols-2 gap-8">
                            <div>
                                <h4 class="text-lg font-semibold text-primary mb-4">Workflow ve Onay Sistemi</h4>
                                <ul class="list-none space-y-3 text-secondary">
                                    <li class="flex items-start">
                                        <i data-lucide="check-circle" class="w-5 h-5 text-success mt-1 mr-3"></i>
                                        <span><strong>Content Approval:</strong> Çoklu seviye onay süreci</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i data-lucide="clock" class="w-5 h-5 text-accent mt-1 mr-3"></i>
                                        <span><strong>Schedule Publishing:</strong> Zamanlanmış yayınlama</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i data-lucide="users" class="w-5 h-5 text-accent mt-1 mr-3"></i>
                                        <span><strong>Team Collaboration:</strong> Çoklu kullanıcı editörü</span>
                                    </li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-primary mb-4">SEO ve Analytics</h4>
                                <ul class="list-none space-y-3 text-secondary">
                                    <li class="flex items-start">
                                        <i data-lucide="search" class="w-5 h-5 text-success mt-1 mr-3"></i>
                                        <span><strong>SEO Optimization:</strong> Automatic meta tag generation</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i data-lucide="trending-up" class="w-5 h-5 text-accent mt-1 mr-3"></i>
                                        <span><strong>Content Analytics:</strong> Detaylı performans raporları</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i data-lucide="sitemap" class="w-5 h-5 text-accent mt-1 mr-3"></i>
                                        <span><strong>XML Sitemap:</strong> Automatic sitemap generation</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- User Management Section -->
    <section id="users" class="section" style="background-color: var(--bg-secondary);">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="section-title">
                        Kullanıcı Yönetimi
                        <span class="block text-lg text-accent">User Management</span>
                    </h2>
                    <p class="section-subtitle">
                        <span class="tech-highlight">Role-based access control</span> <span class="text-muted">(rol tabanlı erişim kontrolü)</span><br>
                        <span class="text-sm text-secondary">→ Kullanıcıların rollerine göre farklı yetkilerle sisteme erişim sağlaması</span>, 
                        <span class="tech-highlight">user permissions</span> <span class="text-muted">(kullanıcı izinleri)</span><br>
                        <span class="text-sm text-secondary">→ Her kullanıcıya özel yetki atama ve yönetme sistemi</span> 
                        ve <span class="tech-highlight">activity logging</span> <span class="text-muted">(aktivite kayıtları)</span><br>
                        <span class="text-sm text-secondary">→ Kullanıcı hareketlerinin detaylı kayıt altında tutulması</span> ile kapsamlı kullanıcı yönetimi.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 gap-8 mb-16">
                    <div class="card">
                        <div class="gradient-bg p-6">
                            <h3 class="text-xl font-bold text-white mb-2">
                                Rol ve İzin Yönetimi
                                <span class="block text-sm opacity-90">Role & Permission Management</span>
                            </h3>
                        </div>
                        <div class="p-6">
                            <p class="text-secondary mb-4">
                                <span class="tech-highlight">Granular permissions</span> <span class="text-muted">(detaylı izinler)</span><br>
                                <span class="text-sm text-secondary">→ Her işlem için ayrı ayrı yetki tanımlama sistemi</span> ile kullanıcı erişimlerini kontrol edin. 
                                Custom roles ve dynamic permissions desteği.
                            </p>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 card-accent rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-error rounded-full flex items-center justify-center mr-3">
                                            <i data-lucide="crown" class="w-4 h-4 text-white"></i>
                                        </div>
                                        <span class="font-medium text-primary">Super Admin</span>
                                    </div>
                                    <span class="text-sm text-muted">Tüm izinler</span>
                                </div>
                                <div class="flex items-center justify-between p-3 card-accent rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-accent rounded-full flex items-center justify-center mr-3">
                                            <i data-lucide="user-check" class="w-4 h-4 text-white"></i>
                                        </div>
                                        <span class="font-medium text-primary">Admin</span>
                                    </div>
                                    <span class="text-sm text-muted">Yönetim izinleri</span>
                                </div>
                                <div class="flex items-center justify-between p-3 card-accent rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-success rounded-full flex items-center justify-center mr-3">
                                            <i data-lucide="edit" class="w-4 h-4 text-white"></i>
                                        </div>
                                        <span class="font-medium text-primary">Editor</span>
                                    </div>
                                    <span class="text-sm text-muted">İçerik editörü</span>
                                </div>
                                <div class="flex items-center justify-between p-3 card-accent rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-accent rounded-full flex items-center justify-center mr-3">
                                            <i data-lucide="user" class="w-4 h-4 text-white"></i>
                                        </div>
                                        <span class="font-medium text-primary">Contributor</span>
                                    </div>
                                    <span class="text-sm text-muted">Katkı sağlayıcı</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="gradient-bg p-6">
                            <h3 class="text-xl font-bold text-white mb-2">
                                Aktivite İzleme
                                <span class="block text-sm opacity-90">Activity Monitoring</span>
                            </h3>
                        </div>
                        <div class="p-6">
                            <p class="text-secondary mb-4">
                                <span class="tech-highlight">User activities</span> <span class="text-muted">(kullanıcı aktiviteleri)</span><br>
                                <span class="text-sm text-secondary">→ Kullanıcıların sistemde yaptığı her işlemin kayda alınması</span>, 
                                <span class="tech-highlight">login attempts</span> <span class="text-muted">(giriş denemeleri)</span><br>
                                <span class="text-sm text-secondary">→ Sisteme giriş denemelerinin güvenlik açısından takip edilmesi</span> 
                                ve <span class="tech-highlight">security events</span> <span class="text-muted">(güvenlik olayları)</span><br>
                                <span class="text-sm text-secondary">→ Güvenlik ile ilgili tüm olayların izlenmesi</span> takibi.
                            </p>
                            <div class="space-y-3">
                                <div class="flex items-start p-3 card-accent rounded-lg">
                                    <div class="w-8 h-8 bg-accent rounded-full flex items-center justify-center mr-3 mt-1">
                                        <i data-lucide="log-in" class="w-4 h-4 text-white"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-primary">Ahmet Yılmaz giriş yaptı</p>
                                        <p class="text-sm text-muted">5 dakika önce</p>
                                    </div>
                                </div>
                                <div class="flex items-start p-3 card-accent rounded-lg">
                                    <div class="w-8 h-8 bg-success rounded-full flex items-center justify-center mr-3 mt-1">
                                        <i data-lucide="edit" class="w-4 h-4 text-white"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-primary">Makale güncellendi</p>
                                        <p class="text-sm text-muted">15 dakika önce</p>
                                    </div>
                                </div>
                                <div class="flex items-start p-3 card-accent rounded-lg">
                                    <div class="w-8 h-8 bg-accent rounded-full flex items-center justify-center mr-3 mt-1">
                                        <i data-lucide="user-plus" class="w-4 h-4 text-white"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-primary">Yeni kullanıcı kaydı</p>
                                        <p class="text-sm text-muted">1 saat önce</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h3 class="text-2xl font-bold text-primary mb-6">
                        Güvenlik ve Kimlik Doğrulama
                        <span class="block text-lg text-accent">Security & Authentication</span>
                    </h3>
                    <div class="grid md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="feature-icon mx-auto mb-4">
                                <i data-lucide="shield" class="w-8 h-8"></i>
                            </div>
                            <h4 class="font-semibold text-primary mb-2">
                                <span class="tech-highlight">Two-Factor Authentication</span><br>
                                <span class="text-sm text-secondary">→ İki adımlı doğrulama sistemi</span>
                            </h4>
                            <p class="text-sm text-secondary">2FA ile ekstra güvenlik katmanı</p>
                        </div>
                        <div class="text-center">
                            <div class="feature-icon mx-auto mb-4">
                                <i data-lucide="lock" class="w-8 h-8"></i>
                            </div>
                            <h4 class="font-semibold text-primary mb-2">
                                <span class="tech-highlight">Password Policy</span><br>
                                <span class="text-sm text-secondary">→ Şifre güçlülüğü kuralları</span>
                            </h4>
                            <p class="text-sm text-secondary">Güçlü şifre politikaları</p>
                        </div>
                        <div class="text-center">
                            <div class="feature-icon mx-auto mb-4">
                                <i data-lucide="clock" class="w-8 h-8"></i>
                            </div>
                            <h4 class="font-semibold text-primary mb-2">
                                <span class="tech-highlight">Session Management</span><br>
                                <span class="text-sm text-secondary">→ Oturum süre yönetimi sistemi</span>
                            </h4>
                            <p class="text-sm text-secondary">Oturum yönetimi ve timeout</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- System Settings Section -->
    <section id="settings" class="section" style="background-color: var(--bg-primary);">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="section-title">
                        Sistem Ayarları
                        <span class="block text-lg text-accent">System Settings</span>
                    </h2>
                    <p class="section-subtitle">
                        <span class="tech-highlight">Site configuration</span> <span class="text-muted">(site konfigürasyonu)</span><br>
                        <span class="text-sm text-secondary">→ Web sitesinin temel ayarlarını yönetme sistemi</span>, 
                        <span class="tech-highlight">theme customization</span> <span class="text-muted">(tema özelleştirme)</span><br>
                        <span class="text-sm text-secondary">→ Sitenin görünümünü ve tasarımını özelleştirme araçları</span> 
                        ve <span class="tech-highlight">system maintenance</span> <span class="text-muted">(sistem bakımı)</span><br>
                        <span class="text-sm text-secondary">→ Sistemin sağlıklı çalışması için bakım araçları</span> araçları.
                    </p>
                </div>

                <div class="feature-grid">
                    <div class="feature-card">
                        <div class="feature-icon mb-6">
                            <i data-lucide="settings" class="w-6 h-6"></i>
                        </div>
                        <h3 class="text-xl font-bold text-primary mb-3">
                            <span class="tech-highlight">General Settings</span> <span class="text-muted">(genel ayarlar)</span><br>
                            <span class="text-sm text-secondary">→ Sitenin temel konfigürasyon ayarları</span>
                        </h3>
                        <p class="text-secondary mb-4">
                            Site başlığı, açıklama, <span class="tech-highlight">contact information</span> <span class="text-muted">(iletişim bilgileri)</span><br>
                            <span class="text-sm text-secondary">→ Kullanıcıların size ulaşabilmesi için gerekli bilgiler</span> 
                            ve <span class="tech-highlight">basic configuration</span> <span class="text-muted">(temel konfigürasyon)</span><br>
                            <span class="text-sm text-secondary">→ Sitenin çalışması için gerekli temel ayarlar</span> ayarları.
                        </p>
                        <ul class="list-none space-y-2 text-sm text-secondary">
                            <li>• Site metadata</li>
                            <li>• Contact information</li>
                            <li>• Timezone settings</li>
                            <li>• Language configuration</li>
                        </ul>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon mb-6">
                            <i data-lucide="palette" class="w-6 h-6"></i>
                        </div>
                        <h3 class="text-xl font-bold text-primary mb-3">
                            <span class="tech-highlight">Theme Settings</span> <span class="text-muted">(tema ayarları)</span><br>
                            <span class="text-sm text-secondary">→ Sitenin görsel tasarımını yönetme sistemi</span>
                        </h3>
                        <p class="text-secondary mb-4">
                            <span class="tech-highlight">Theme selection</span> <span class="text-muted">(tema seçimi)</span><br>
                            <span class="text-sm text-secondary">→ Hazır tema şablonları arasından seçim yapma</span>, 
                            <span class="tech-highlight">color schemes</span> <span class="text-muted">(renk şemaları)</span><br>
                            <span class="text-sm text-secondary">→ Sitenin renk paletini belirleme sistemi</span> 
                            ve <span class="tech-highlight">layout customization</span> <span class="text-muted">(düzen özelleştirme)</span><br>
                            <span class="text-sm text-secondary">→ Sayfa düzenini özelleştirme seçenekleri</span> seçenekleri.
                        </p>
                        <ul class="list-none space-y-2 text-sm text-secondary">
                            <li>• Theme library</li>
                            <li>• Color customization</li>
                            <li>• Layout options</li>
                            <li>• Custom CSS/JS</li>
                        </ul>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon mb-6">
                            <i data-lucide="database" class="w-6 h-6"></i>
                        </div>
                        <h3 class="text-xl font-bold text-primary mb-3">
                            <span class="tech-highlight">System Maintenance</span> <span class="text-muted">(sistem bakımı)</span><br>
                            <span class="text-sm text-secondary">→ Sistemin performansını koruma ve iyileştirme araçları</span>
                        </h3>
                        <p class="text-secondary mb-4">
                            <span class="tech-highlight">Cache management</span> <span class="text-muted">(önbellek yönetimi)</span><br>
                            <span class="text-sm text-secondary">→ Hızlı erişim için önbellek verilerini yönetme</span>, 
                            <span class="tech-highlight">database optimization</span> <span class="text-muted">(veritabanı optimizasyonu)</span><br>
                            <span class="text-sm text-secondary">→ Veritabanının hızlı ve verimli çalışmasını sağlama</span> 
                            ve <span class="tech-highlight">backup operations</span> <span class="text-muted">(yedekleme işlemleri)</span><br>
                            <span class="text-sm text-secondary">→ Verilerinizin güvenliği için düzenli yedekleme sistemi</span>.
                        </p>
                        <ul class="list-none space-y-2 text-sm text-secondary">
                            <li>• Cache clearing</li>
                            <li>• Database backup</li>
                            <li>• System monitoring</li>
                            <li>• Performance optimization</li>
                        </ul>
                    </div>
                </div>

                <div class="gradient-bg rounded-xl p-8 text-white">
                    <div class="max-w-4xl mx-auto">
                        <h3 class="text-2xl font-bold mb-4">
                            <span class="tech-highlight bg-white text-accent">System Status Monitor</span> <span class="text-muted">(sistem durumu monitörü)</span><br>
                            <span class="text-sm opacity-90">→ Sistemin canlı durumunu izleme ve raporlama paneli</span>
                        </h3>
                        <p class="text-white opacity-80 mb-8">
                            <span class="tech-highlight bg-white text-accent">Real-time system health monitoring</span> <span class="text-muted">(gerçek zamanlı sistem sağlık izleme)</span><br>
                            <span class="text-sm opacity-90">→ Sunucu performansını anlık olarak takip etme sistemi</span> ile server performance, 
                            disk usage ve application metrics takibi.
                        </p>
                        <div class="grid md:grid-cols-4 gap-6">
                            <div class="bg-white/10 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-green-400 mb-2">Yüksek</div>
                                <div class="text-sm text-white opacity-80">Uptime</div>
                            </div>
                            <div class="bg-white/10 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-blue-400 mb-2">Hızlı</div>
                                <div class="text-sm text-white opacity-80">Response Time</div>
                            </div>
                            <div class="bg-white/10 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-yellow-400 mb-2">Optimize</div>
                                <div class="text-sm text-white opacity-80">CPU Usage</div>
                            </div>
                            <div class="bg-white/10 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-purple-400 mb-2">Verimli</div>
                                <div class="text-sm text-white opacity-80">Memory Usage</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tools and Features Section -->
    <section id="tools" class="section" style="background-color: var(--bg-secondary);">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="section-title">
                        Araçlar ve Özellikler
                        <span class="block text-lg text-accent">Tools & Features</span>
                    </h2>
                    <p class="section-subtitle">
                        Admin panelinde bulunan <span class="tech-highlight">advanced tools</span> <span class="text-muted">(gelişmiş araçlar)</span><br>
                        <span class="text-sm text-secondary">→ Profesyonel yönetim için özel olarak geliştirilmiş araçlar</span>, 
                        <span class="tech-highlight">reporting systems</span> <span class="text-muted">(raporlama sistemleri)</span><br>
                        <span class="text-sm text-secondary">→ Detaylı analiz ve raporlama imkanları</span> 
                        ve <span class="tech-highlight">automation features</span> <span class="text-muted">(otomasyon özellikleri)</span><br>
                        <span class="text-sm text-secondary">→ Tekrarlayan işlemleri otomatikleştirme sistemleri</span>.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 gap-8">
                    <div class="card">
                        <div class="gradient-bg p-6">
                            <h3 class="text-xl font-bold text-white mb-2">
                                Raporlama ve Analytics
                                <span class="block text-sm opacity-90">Reports & Analytics</span>
                            </h3>
                        </div>
                        <div class="p-6">
                            <p class="text-secondary mb-4">
                                <span class="tech-highlight">Comprehensive reporting</span> <span class="text-muted">(kapsamlı raporlama)</span><br>
                                <span class="text-sm text-secondary">→ Tüm verileri detaylı şekilde analiz eden raporlama sistemi</span> ile business intelligence 
                                ve <span class="tech-highlight">detailed analytics</span> <span class="text-muted">(detaylı analitik)</span><br>
                                <span class="text-sm text-secondary">→ Derinlemesine veri analizi ve içgörü sağlama sistemi</span> erişimi.
                            </p>
                            <div class="space-y-3">
                                <div class="flex items-center p-3 card-accent rounded-lg">
                                    <i data-lucide="bar-chart-3" class="w-5 h-5 text-accent mr-3"></i>
                                    <div>
                                        <h4 class="font-medium text-primary">Traffic Reports</h4>
                                        <p class="text-sm text-secondary">Ziyaretçi analizi ve trafik raporları</p>
                                    </div>
                                </div>
                                <div class="flex items-center p-3 card-accent rounded-lg">
                                    <i data-lucide="trending-up" class="w-5 h-5 text-success mr-3"></i>
                                    <div>
                                        <h4 class="font-medium text-primary">Performance Analytics</h4>
                                        <p class="text-sm text-secondary">Performans analizi ve optimizasyon raporları</p>
                                    </div>
                                </div>
                                <div class="flex items-center p-3 card-accent rounded-lg">
                                    <i data-lucide="users" class="w-5 h-5 text-accent mr-3"></i>
                                    <div>
                                        <h4 class="font-medium text-primary">User Behavior</h4>
                                        <p class="text-sm text-secondary">Kullanıcı davranış analizi</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="gradient-bg p-6">
                            <h3 class="text-xl font-bold text-white mb-2">
                                Otomasyon ve Entegrasyonlar
                                <span class="block text-sm opacity-90">Automation & Integrations</span>
                            </h3>
                        </div>
                        <div class="p-6">
                            <p class="text-secondary mb-4">
                                <span class="tech-highlight">Workflow automation</span> <span class="text-muted">(iş akışı otomasyonu)</span><br>
                                <span class="text-sm text-secondary">→ İş süreçlerini otomatikleştiren akıllı sistem</span> 
                                ve <span class="tech-highlight">third-party integrations</span> <span class="text-muted">(üçüncü taraf entegrasyonları)</span><br>
                                <span class="text-sm text-secondary">→ Dış servislerle entegrasyon ve veri alışverişi</span> ile efficiency artırımı.
                            </p>
                            <div class="space-y-3">
                                <div class="flex items-center p-3 card-accent rounded-lg">
                                    <i data-lucide="bot" class="w-5 h-5 text-accent mr-3"></i>
                                    <div>
                                        <h4 class="font-medium text-primary">Auto Publishing</h4>
                                        <p class="text-sm text-secondary">Otomatik içerik yayınlama</p>
                                    </div>
                                </div>
                                <div class="flex items-center p-3 card-accent rounded-lg">
                                    <i data-lucide="mail" class="w-5 h-5 text-success mr-3"></i>
                                    <div>
                                        <h4 class="font-medium text-primary">Email Automation</h4>
                                        <p class="text-sm text-secondary">Otomatik email kampanyaları</p>
                                    </div>
                                </div>
                                <div class="flex items-center p-3 card-accent rounded-lg">
                                    <i data-lucide="refresh-cw" class="w-5 h-5 text-accent mr-3"></i>
                                    <div>
                                        <h4 class="font-medium text-primary">Data Sync</h4>
                                        <p class="text-sm text-secondary">Otomatik veri senkronizasyonu</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mobile Admin Section -->
    <section id="mobile" class="section" style="background-color: var(--bg-primary);">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="section-title">
                        Mobil Admin Panel
                        <span class="block text-lg text-accent">Mobile Admin Panel</span>
                    </h2>
                    <p class="section-subtitle">
                        <span class="tech-highlight">Responsive design</span> <span class="text-muted">(duyarlı tasarım)</span><br>
                        <span class="text-sm text-secondary">→ Her cihaz boyutuna otomatik uyum sağlayan esnek tasarım sistemi</span> 
                        ile <span class="tech-highlight">mobile-first approach</span> <span class="text-muted">(mobil öncelikli yaklaşım)</span><br>
                        <span class="text-sm text-secondary">→ Önce mobil deneyim odaklı sonra desktop'a uyarlanan yaklaşım</span>. 
                        Tablet ve smartphone'larda <span class="tech-highlight">optimal user experience</span> <span class="text-muted">(kullanıcı deneyimi)</span><br>
                        <span class="text-sm text-secondary">→ Kullanıcının en rahat ve verimli şekilde çalışabileceği deneyim</span>.
                    </p>
                </div>

                <div class="feature-grid">
                    <div class="feature-card text-center">
                        <div class="feature-icon mx-auto mb-6">
                            <i data-lucide="smartphone" class="w-8 h-8"></i>
                        </div>
                        <h3 class="text-xl font-bold text-primary mb-3">
                            <span class="tech-highlight">Responsive Design</span> <span class="text-muted">(duyarlı tasarım)</span><br>
                            <span class="text-sm text-secondary">→ Her cihazda mükemmel görünüm sağlayan esnek sistem</span>
                        </h3>
                        <p class="text-secondary mb-4">
                            Tüm ekran boyutlarında <span class="tech-highlight">perfect display</span> <span class="text-muted">(mükemmel görüntü)</span><br>
                            <span class="text-sm text-secondary">→ Her cihazda keskin ve net görsel deneyim</span>. 
                            <span class="tech-highlight">Touch-friendly interfaces</span> <span class="text-muted">(dokunma dostu arayüzler)</span><br>
                            <span class="text-sm text-secondary">→ Dokunmatik cihazlar için optimize edilmiş etkileşim</span>.
                        </p>
                        <ul class="list-none space-y-2 text-sm text-secondary">
                            <li>• Adaptive layouts</li>
                            <li>• Touch gestures</li>
                            <li>• Swipe navigation</li>
                            <li>• Optimized forms</li>
                        </ul>
                    </div>

                    <div class="feature-card text-center">
                        <div class="feature-icon mx-auto mb-6">
                            <i data-lucide="wifi-off" class="w-8 h-8"></i>
                        </div>
                        <h3 class="text-xl font-bold text-primary mb-3">
                            <span class="tech-highlight">Offline Capability</span> <span class="text-muted">(çevrimdışı özellik)</span><br>
                            <span class="text-sm text-secondary">→ İnternet bağlantısı olmadan da çalışabilme yeteneği</span>
                        </h3>
                        <p class="text-secondary mb-4">
                            <span class="tech-highlight">Progressive Web App (PWA)</span> <span class="text-muted">(ilerlemeli web uygulaması)</span><br>
                            <span class="text-sm text-secondary">→ Mobil uygulama gibi çalışan web teknolojisi</span> teknolojisi ile offline access. 
                            <span class="tech-highlight">Background sync</span> <span class="text-muted">(arka plan senkronizasyonu)</span><br>
                            <span class="text-sm text-secondary">→ Bağlantı kurulduğunda otomatik veri eşleştirme</span> desteği.
                        </p>
                        <ul class="list-none space-y-2 text-sm text-secondary">
                            <li>• PWA support</li>
                            <li>• Offline editing</li>
                            <li>• Background sync</li>
                            <li>• Push notifications</li>
                        </ul>
                    </div>

                    <div class="feature-card text-center">
                        <div class="feature-icon mx-auto mb-6">
                            <i data-lucide="zap" class="w-8 h-8"></i>
                        </div>
                        <h3 class="text-xl font-bold text-primary mb-3">
                            <span class="tech-highlight">Performance</span> <span class="text-muted">(performans)</span><br>
                            <span class="text-sm text-secondary">→ Hızlı ve verimli sistem çalışma kapasitesi</span>
                        </h3>
                        <p class="text-secondary mb-4">
                            <span class="tech-highlight">Optimized loading</span> <span class="text-muted">(optimize yükleme)</span><br>
                            <span class="text-sm text-secondary">→ Sayfaların en hızlı şekilde yüklenmesi için optimizasyon</span> 
                            ve <span class="tech-highlight">efficient caching</span> <span class="text-muted">(verimli önbellekleme)</span><br>
                            <span class="text-sm text-secondary">→ Verilerin akıllı önbelleklenmesi ile hız artırımı</span>. 
                            <span class="tech-highlight">Low bandwidth</span> <span class="text-muted">(düşük bant genişliği)</span><br>
                            <span class="text-sm text-secondary">→ Yavaş internet bağlantılarında bile sorunsuz kullanım</span> desteği.
                        </p>
                        <ul class="list-none space-y-2 text-sm text-secondary">
                            <li>• Fast loading</li>
                            <li>• Lazy loading</li>
                            <li>• Image optimization</li>
                            <li>• Minified assets</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 gradient-bg">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                    Güçlü Admin Paneli ile Kontrolü Elinize Alın
                </h2>
                <p class="text-xl text-white opacity-90 mb-8 max-w-2xl mx-auto">
                    Modern admin paneli ile web sitenizi profesyonel şekilde yönetin. 
                    Gelişmiş özellikler ve kullanıcı dostu arayüz.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#dashboard" class="bg-white text-accent px-8 py-3 rounded-lg font-medium hover:bg-accent hover:text-white transition-colors">
                        Dashboard İncele
                    </a>
                    <a href="features.php" class="border-2 border-white text-white px-8 py-3 rounded-lg font-medium hover:bg-white hover:text-accent transition-colors">
                        Tüm Özellikler
                    </a>
                </div>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>