<?php
$page_title = "Çok Projeli Sistem - Türk Bilişim Enterprise CMS";
$page_subtitle = "Çok Projeli Mimari";
$page_badge = "🏢 Çok Projeli Enterprise";

// Navigation sections
$nav_sections = [
    'hero' => 'Giriş',
    'overview' => 'Genel Bakış',
    'architecture' => 'Mimari',
    'security' => 'Güvenlik',
    'performance' => 'Performans',
    'management' => 'Yönetim'
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
                Çok Projeli<br>
                <span style="font-weight: 700; background: linear-gradient(45deg, #64b5f6, #4facfe, #64b5f6); background-size: 200% 200%; animation: textGradient 3s ease infinite; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Sistem Mimarisi</span>
            </h1>
            <p style="margin-bottom: 3rem; font-size: 1.2rem; line-height: 1.6; color: rgba(255, 255, 255, 0.8); max-width: 600px; margin-left: auto; margin-right: auto; font-weight: 400; text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);">
                Her proje farklı bir müşteri<br>
                <span style="color: #64b5f6; font-weight: 600;">Bir proje binlerce müşteri</span>
            </p>
        </div>
    </div>
</section>

<!-- Overview Section -->
<section id="overview" class="section">
    <h2 class="section-title">Çok Projeli Sistemin Gücü</h2>
    <p class="section-subtitle">
        Her müşteri için matematik olarak ayrı dijital ortam. Tamamen kendi geliştirdiğimiz 
        domain-based tenancy teknolojisi ile sektörde benzersiz güvenlik ve performans. 
        Binlerce müşteriye hizmet veren enterprise-grade mimari.
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="database"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Matematik Olarak Güvenli İzolasyon</h3>
            <p class="text-secondary">
                Her müşteri için fiziksel olarak ayrı veritabanı ile tasarladığımız sistem. <span class="tech-highlight">Cross-tenant data leak</span> 
                <span class="text-muted">(müşteri verilerinin birbirine karışması)</span><br>
                <span class="text-sm text-secondary">→ Bir müşterinin verilerini diğer müşterinin görmesi</span>
                matematik olarak imkansız. Sektörde benzersiz veritabanı seviyesinde izolasyon.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Physical database separation</span> <span class="text-muted">(fiziksel veritabanı ayrımı)</span><br><span class="text-sm text-secondary">→ Her müşteri için ayrı MySQL veritabanı oluşturma</span></li>
                        <li>• <span class="tech-highlight">Zero data leak guarantee</span> <span class="text-muted">(sıfır veri sızıntısı garantisi)</span><br><span class="text-sm text-secondary">→ Veriler hiçbir şekilde karışamaz</span></li>
                        <li>• <span class="tech-highlight">Tenant-specific connections</span> <span class="text-muted">(kiracıya özel bağlantılar)</span><br><span class="text-sm text-secondary">→ Her istek sadece kendi veritabanına bağlanır</span></li>
                        <li>• <span class="tech-highlight">Automatic database creation</span> <span class="text-muted">(otomatik veritabanı oluşturma)</span><br><span class="text-sm text-secondary">→ Yeni müşteri eklenince otomatik kurulum</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="globe"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Domain-Based Tenancy <span class="text-sm text-muted">(domain tabanlı kiracı sistemi)</span></h3>
            <p class="text-secondary">
                Tamamen kendi geliştirdiğimiz gelişmiş domain çözümleme sistemi ile her müşteri kendi benzersiz domain adresine sahip olur. <span class="tech-highlight">Domain resolution</span> 
                <span class="text-muted">(alan adı çözümlemesi)</span><br>
                <span class="text-sm text-secondary">→ Gelen isteğin hangi müşteriye ait olduğunu otomatik bulma</span>
                teknolojisi ile milisaniyelerde ultra hızlı yönlendirme ve mükemmel white-label çözüm imkanı sağlıyoruz.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Unique domain per tenant</span> <span class="text-muted">(her kiracı için özel domain)</span><br><span class="text-sm text-secondary">→ musteri1.com, musteri2.com gibi tamamen ayrı adresler</span></li>
                        <li>• <span class="tech-highlight">Cached domain resolution</span> <span class="text-muted">(önbellekli domain çözümlemesi)</span><br><span class="text-sm text-secondary">→ 15 dakika cache ile ultra hızlı tespit</span></li>
                        <li>• <span class="tech-highlight">Custom domain mapping</span> <span class="text-muted">(özel domain eşlemesi)</span><br><span class="text-sm text-secondary">→ Müşteri kendi domain'ini bağlayabilir</span></li>
                        <li>• <span class="tech-highlight">White-label solution</span> <span class="text-muted">(beyaz etiket çözümü)</span><br><span class="text-sm text-secondary">→ Markanız tamamen gizli kalır</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="layers"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">5 Katmanlı İzolasyon Sistemi <span class="text-sm text-muted">(çok seviyeli ayrım teknolojisi)</span></h3>
            <p class="text-secondary mb-3">
                Dünya standartlarının üzerinde tasarlanan beş katmanlı izolasyon sistemimiz ile her müşteriyi diğerlerinden tamamen bağımsız hale getiriyoruz. Kendi geliştirdiğimiz gelişmiş teknolojiler sayesinde maksimum güvenlik ve optimal performans garanti ediyoruz.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Database Isolation</span> <span class="text-muted">(veritabanı izolasyonu)</span><br><span class="text-sm text-secondary">→ Veritabanı bağlantısını otomatik olarak tenant'a özel veritabanına yönlendirme</span></li>
                        <li>• <span class="tech-highlight">Cache Isolation</span> <span class="text-muted">(önbellek izolasyonu)</span><br><span class="text-sm text-secondary">→ Redis cache'ini tenant prefixi ile ayırarak karışıklığı önleme</span></li>
                        <li>• <span class="tech-highlight">Filesystem Isolation</span> <span class="text-muted">(dosya sistemi izolasyonu)</span><br><span class="text-sm text-secondary">→ Upload dosyalarını tenant'a özel klasörlerde güvenli tutma</span></li>
                        <li>• <span class="tech-highlight">Queue Isolation</span> <span class="text-muted">(kuyruk sistemi izolasyonu)</span><br><span class="text-sm text-secondary">→ Arka plan işlemlerini tenant context'inde izole şekilde çalıştırma</span></li>
                        <li>• <span class="tech-highlight">Session Isolation</span> <span class="text-muted">(oturum izolasyonu)</span><br><span class="text-sm text-secondary">→ Kullanıcı oturumlarını tenant'a özel ve güvenli tutma</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="zap"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Tek Tıkla Tenant Oluşturma <span class="text-sm text-muted">(anında müşteri kurulumu)</span></h3>
            <p class="text-secondary mb-3">
                Profesyonel seviyede tasarlanan tamamen otomatik tenant kurulum sistemimiz ile yeni müşteri ekleme süreci saniyeler içinde tamamlanır. İleri teknoloji altyapımız sayesinde karmaşık kurulum işlemleri tek tıkla hallolur.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Auto Database Creation</span> <span class="text-muted">(otomatik veritabanı oluşturma)</span><br><span class="text-sm text-secondary">→ Tüm tablo yapısının ve ilişkilerin otomatik kurulması</span></li>
                        <li>• <span class="tech-highlight">Migration & Seeding</span> <span class="text-muted">(tablo ve veri kurulumu)</span><br><span class="text-sm text-secondary">→ Başlangıç verilerinin ve ayarlarının otomatik eklenmesi</span></li>
                        <li>• <span class="tech-highlight">Domain Registration</span> <span class="text-muted">(domain kaydı)</span><br><span class="text-sm text-secondary">→ Domain'in sistem tarafından otomatik tanınması ve yönlendirilmesi</span></li>
                        <li>• <span class="tech-highlight">Folder Structure Setup</span> <span class="text-muted">(klasör yapısı kurulumu)</span><br><span class="text-sm text-secondary">→ Dosya sisteminde gerekli klasörlerin ve izinlerin oluşturulması</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="shield"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Enterprise Güvenlik Katmanları <span class="text-sm text-muted">(kurumsal güvenlik sistemi)</span></h3>
            <p class="text-secondary mb-3">
                Sektörde benzersiz olan çok katmanlı güvenlik sistemimiz ile müşteri verilerinin tam korunmasını sağlıyoruz. Titizlikle geliştirilen güvenlik protokollerimiz sayesinde veri kaybı riski sıfıra indirgenmiştir.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Cross-tenant Protection</span> <span class="text-muted">(çapraz kiracı koruması)</span><br><span class="text-sm text-secondary">→ Bir müşterinin başka müşterinin verilerine erişmesinin tamamen engellenmesi</span></li>
                        <li>• <span class="tech-highlight">Tenant Activation Control</span> <span class="text-muted">(kiracı aktivasyon kontrolü)</span><br><span class="text-sm text-secondary">→ Anında aktif/pasif yapma ve bakım modu yönetimi</span></li>
                        <li>• <span class="tech-highlight">Secure File Storage</span> <span class="text-muted">(güvenli dosya depolama)</span><br><span class="text-sm text-secondary">→ Upload dosyalarının tenant'a özel klasörlerde şifreli tutulması</span></li>
                        <li>• <span class="tech-highlight">Session Security</span> <span class="text-muted">(oturum güvenliği)</span><br><span class="text-sm text-secondary">→ Kullanıcı oturumlarının tenant'a özel olarak izole edilmesi</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="brain"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Yapay Zeka Token Yönetimi <span class="text-sm text-muted">(gelişmiş token sistemi)</span></h3>
            <p class="text-secondary mb-3">
                Kusursuz tasarlanan yapay zeka token yönetim sistemimiz ile her müşteri kendi bağımsız token bakiyesine sahip olur. İleri teknoloji sayesinde gerçek zamanlı tüketim takibi ve esnek limit yönetimi sağlıyoruz.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Tenant-based Token Tracking</span> <span class="text-muted">(kiracı bazlı token takibi)</span><br><span class="text-sm text-secondary">→ Her müşterinin kendi ayrı token bakiyesi ve kullanım geçmişi</span></li>
                        <li>• <span class="tech-highlight">Real-time Consumption</span> <span class="text-muted">(gerçek zamanlı tüketim)</span><br><span class="text-sm text-secondary">→ Yapay zeka kullanımında anında token düşürme ve bildirim</span></li>
                        <li>• <span class="tech-highlight">Monthly Usage Limits</span> <span class="text-muted">(aylık kullanım limitleri)</span><br><span class="text-sm text-secondary">→ Müşteri bazında esnek limit ayarlama ve aşım kontrolü</span></li>
                        <li>• <span class="tech-highlight">Usage Analytics</span> <span class="text-muted">(kullanım analitiği)</span><br><span class="text-sm text-secondary">→ Detaylı yapay zeka kullanım raporları ve trend analizi</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Advantages -->
<section id="advantages" class="section">
    <h2 class="section-title text-center">Sistem Avantajları</h2>
    <p class="section-subtitle text-center">
        Tek tıkla tenant oluşturmadan enterprise-level güvenliğe kadar tüm avantajlar
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="zap" class="w-6 h-6"></i>
            </div>
            <h3>Tek Tıkla Tenant Oluşturma</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Otomatik veritabanı oluşturma</li>
                    <li>• Migration ve seeding otomasyonu</li>
                    <li>• Domain registration ve mapping</li>
                    <li>• Dosya sistemi klasör yapısı</li>
                    <li>• Redis cache prefix ayarlama</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="shield" class="w-6 h-6"></i>
            </div>
            <h3>Maximum Security</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Cross-tenant data leak prevention</li>
                    <li>• Database level isolation</li>
                    <li>• Filesystem isolation</li>
                    <li>• Cache isolation</li>
                    <li>• Session isolation</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="trending-up" class="w-6 h-6"></i>
            </div>
            <h3>Unlimited Scalability</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Binlerce tenant desteği</li>
                    <li>• Horizontal scaling ready</li>
                    <li>• Performance degradation yok</li>
                    <li>• Load balancing uyumlu</li>
                    <li>• Resource isolation</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="code" class="w-6 h-6"></i>
            </div>
            <h3>Developer-Friendly</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Normal Laravel code yazabilme</li>
                    <li>• Tenant context otomatik</li>
                    <li>• Debugging ve testing desteği</li>
                    <li>• Clean code architecture</li>
                    <li>• Middleware otomasyonu</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="tag" class="w-6 h-6"></i>
            </div>
            <h3>White-Label Solution</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Her müşteri kendi domain'i</li>
                    <li>• Marka bağımsızlığı</li>
                    <li>• Özel tema ve branding</li>
                    <li>• Ajans satış modeli</li>
                    <li>• Reseller friendly</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="settings" class="w-6 h-6"></i>
            </div>
            <h3>Enterprise Management</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Tenant activation/deactivation</li>
                    <li>• Maintenance mode per tenant</li>
                    <li>• Resource monitoring</li>
                    <li>• Usage analytics</li>
                    <li>• Backup strategies</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Architecture Section -->
<section id="architecture" class="section">
    <h2 class="section-title">Mimari Detayları</h2>
    <p class="section-subtitle">
        Çok projeli sistem mimarisinin teknik detayları ve çalışma prensipleri.
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="server"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Multi-Tenant Altyapısı</h3>
            <p class="text-secondary mb-3">
                Laravel için gelişmiş çok projeli sistem mimarisi:
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Domain-based routing</span> <span class="text-muted">(domain tabanlı yönlendirme)</span><br><span class="text-sm text-secondary">→ Her proje kendi domain'inde bağımsız çalışır</span></li>
                        <li>• <span class="tech-highlight">Central vs Normal</span> <span class="text-muted">(merkezi ve normal proje ayrımı)</span><br><span class="text-sm text-secondary">→ Admin paneli ayrı, müşteri siteleri ayrı yönetim</span></li>
                        <li>• <span class="tech-highlight">Automatic tenant detection</span> <span class="text-muted">(otomatik proje tespiti)</span><br><span class="text-sm text-secondary">→ Gelen isteği hangi projeye ait olduğunu anlama</span></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="hard-drive"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Bootstrapper Sistemi</h3>
            <p class="text-secondary mb-3">
                Tenant context'ini her katmanda ayarlayan sistem:
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Database Bootstrapper</span> <span class="text-muted">(veritabanı yönlendirici)</span><br><span class="text-sm text-secondary">→ Proje veritabanını otomatik bağlar ve yönlendirir</span></li>
                        <li>• <span class="tech-highlight">Cache Bootstrapper</span> <span class="text-muted">(önbellek yönlendirici)</span><br><span class="text-sm text-secondary">→ Redis'i proje prefixi ile izole eder</span></li>
                        <li>• <span class="tech-highlight">Filesystem Bootstrapper</span> <span class="text-muted">(dosya sistemi yönlendirici)</span><br><span class="text-sm text-secondary">→ Dosya yollarını proje klasörüne yönlendirir</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="database"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Veritabanı Mimarisi</h3>
            <p class="text-secondary mb-3">
                Her tenant için ayrı MySQL veritabanı:
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Özel format</span> <span class="text-muted">(veritabanı adlandırma)</span><br><span class="text-sm text-secondary">→ Her müşteri için ayrı veritabanı oluşturma sistemi</span></li>
                        <li>• <span class="tech-highlight">Automatic migrations</span> <span class="text-muted">(otomatik tablo oluşturma)</span><br><span class="text-sm text-secondary">→ Yeni tenant oluşturulduğunda tüm tablolar otomatik kurulur</span></li>
                        <li>• <span class="tech-highlight">Connection switching</span> <span class="text-muted">(bağlantı değiştirme)</span><br><span class="text-sm text-secondary">→ Her HTTP isteğinde doğru veritabanına yönlendirme</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="layers"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Middleware Yapısı</h3>
            <p class="text-secondary mb-3">
                Request lifecycle'da tenant işlemleri:
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Tenant Başlatma</span> <span class="text-muted">(sistem başlangıcı)</span><br><span class="text-sm text-secondary">→ Domain'den proje bulma ve context ayarlama</span></li>
                        <li>• <span class="tech-highlight">Domain caching</span> <span class="text-muted">(domain önbellekleme)</span><br><span class="text-sm text-secondary">→ 15 dakika cache ile performans optimizasyonu</span></li>
                        <li>• <span class="tech-highlight">Central domain check</span> <span class="text-muted">(merkezi domain kontrolü)</span><br><span class="text-sm text-secondary">→ Admin paneli ve normal site ayrımı</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Security Section -->
<section id="security" class="section">
    <h2 class="section-title text-center">Güvenlik Katmanları</h2>
    <p class="section-subtitle text-center">
        Çok katmanlı güvenlik sistemi ile %100 veri izolasyonu garantisi
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="database" class="w-6 h-6"></i>
            </div>
            <h3>Database Level Security</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Her tenant ayrı MySQL veritabanı</li>
                    <li>• Database connection switching</li>
                    <li>• Query level isolation</li>
                    <li>• Connection pool management</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="hard-drive" class="w-6 h-6"></i>
            </div>
            <h3>Filesystem Security</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Tenant'a özel klasör yapısı</li>
                    <li>• Upload file isolation</li>
                    <li>• Asset URL protection</li>
                    <li>• Storage path segregation</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="layers" class="w-6 h-6"></i>
            </div>
            <h3>Cache Security</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Redis tenant prefix sistemi</li>
                    <li>• Cache key isolation</li>
                    <li>• Session data separation</li>
                    <li>• Cache invalidation control</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="list" class="w-6 h-6"></i>
            </div>
            <h3>Queue Security</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Background job isolation</li>
                    <li>• Tenant context preservation</li>
                    <li>• Job data protection</li>
                    <li>• Queue worker tenant awareness</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="user-check" class="w-6 h-6"></i>
            </div>
            <h3>Session Security</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Tenant'a özel session store</li>
                    <li>• Cross-tenant session prevention</li>
                    <li>• Authentication isolation</li>
                    <li>• User permission boundaries</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="shield-check" class="w-6 h-6"></i>
            </div>
            <h3>Central Security Management</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Central domain protection</li>
                    <li>• Tenant activation control</li>
                    <li>• Error handling isolation</li>
                    <li>• Security monitoring</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Isolation -->
<section id="isolation" class="section">
    <h2 class="section-title text-center">İzolasyon Katmanları</h2>
    <p class="section-subtitle text-center">
        Her tenant'ın kendi dijital evinde yaşaması için 5 katmanlı izolasyon
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="database" class="w-6 h-6"></i>
            </div>
            <h3>Database Isolation</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Her müşteri için ayrı DB</li>
                    <li>• Schema isolation</li>
                    <li>• Data leak prevention</li>
                    <li>• Backup isolation</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="server" class="w-6 h-6"></i>
            </div>
            <h3>Cache Isolation</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Redis tenant prefix</li>
                    <li>• Cache key segregation</li>
                    <li>• Performance isolation</li>
                    <li>• Cache invalidation control</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="folder" class="w-6 h-6"></i>
            </div>
            <h3>Filesystem Isolation</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Tenant'a özel klasörler</li>
                    <li>• Upload path isolation</li>
                    <li>• Asset URL protection</li>
                    <li>• Storage quota control</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="activity" class="w-6 h-6"></i>
            </div>
            <h3>Queue Isolation</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Job tenant context</li>
                    <li>• Background task isolation</li>
                    <li>• Email queue separation</li>
                    <li>• Processing priority control</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
            <h3>Session Isolation</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Tenant'a özel session store</li>
                    <li>• User auth isolation</li>
                    <li>• Permission boundary control</li>
                    <li>• Login state separation</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="monitor" class="w-6 h-6"></i>
            </div>
            <h3>Resource Isolation</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• CPU usage isolation</li>
                    <li>• Memory allocation control</li>
                    <li>• Network traffic segregation</li>
                    <li>• Resource monitoring</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Performance Section -->
<section id="performance" class="section">
    <h2 class="section-title text-center">Performans Optimizasyonu</h2>
    <p class="section-subtitle text-center">
        Büyük müşterilerin performansı küçük müşterileri etkilemiyor
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="zap" class="w-6 h-6"></i>
            </div>
            <h3>Cached Domain Resolution</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• 15 dakika domain cache</li>
                    <li>• Ultra hızlı tenant detection</li>
                    <li>• Redis cache backend</li>
                    <li>• Memory efficient caching</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="trending-up" class="w-6 h-6"></i>
            </div>
            <h3>Database Query Optimization</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Tenant'a özel query scope</li>
                    <li>• Index optimization</li>
                    <li>• Connection pooling</li>
                    <li>• Query caching</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="server" class="w-6 h-6"></i>
            </div>
            <h3>Distributed Caching</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Redis cluster support</li>
                    <li>• Tenant-specific cache keys</li>
                    <li>• Cache invalidation strategies</li>
                    <li>• Memory usage optimization</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="activity" class="w-6 h-6"></i>
            </div>
            <h3>Load Balancing Ready</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Horizontal scaling desteği</li>
                    <li>• Stateless architecture</li>
                    <li>• Session sharing capability</li>
                    <li>• Auto-scaling integration</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="cpu" class="w-6 h-6"></i>
            </div>
            <h3>Resource Management</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• CPU usage monitoring</li>
                    <li>• Memory allocation tracking</li>
                    <li>• Disk space management</li>
                    <li>• Performance alerting</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="gauge" class="w-6 h-6"></i>
            </div>
            <h3>Performance Monitoring</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Real-time metrics</li>
                    <li>• Response time tracking</li>
                    <li>• Error rate monitoring</li>
                    <li>• Capacity planning</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Management Section -->
<section id="management" class="section">
    <h2 class="section-title text-center">Tenant Yönetimi</h2>
    <p class="section-subtitle text-center">
        Merkezi yönetim paneli ile binlerce tenant'ı kolayca yönetebilirsiniz
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="plus-circle" class="w-6 h-6"></i>
            </div>
            <h3>Tenant Oluşturma</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Tek tıkla tenant oluşturma</li>
                    <li>• Otomatik veritabanı kurulumu</li>
                    <li>• Domain mapping ayarları</li>
                    <li>• Başlangıç data seeding</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="power" class="w-6 h-6"></i>
            </div>
            <h3>Aktivasyon/Deaktivasyon</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Anında aktif/pasif yapma</li>
                    <li>• Maintenance mode</li>
                    <li>• Offline page gösterimi</li>
                    <li>• Admin panel erişimi korunur</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="settings" class="w-6 h-6"></i>
            </div>
            <h3>Tenant Ayarları</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Tema ve branding ayarları</li>
                    <li>• Dil ve lokalizasyon</li>
                    <li>• Modül aktif/pasif</li>
                    <li>• Yapay zeka token limitleri</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="trash-2" class="w-6 h-6"></i>
            </div>
            <h3>Tenant Silme</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Güvenli tenant silme</li>
                    <li>• Veritabanı temizleme</li>
                    <li>• Dosya sistemi temizleme</li>
                    <li>• Cache temizleme</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="bar-chart-3" class="w-6 h-6"></i>
            </div>
            <h3>İstatistikler</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Tenant usage analytics</li>
                    <li>• Performance metrics</li>
                    <li>• Resource consumption</li>
                    <li>• Yapay zeka token usage tracking</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="shield-check" class="w-6 h-6"></i>
            </div>
            <h3>Güvenlik Yönetimi</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Access control management</li>
                    <li>• Security audit logs</li>
                    <li>• Threat detection</li>
                    <li>• Backup management</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>