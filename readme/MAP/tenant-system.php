<?php
// Sayfa ayarları
$page_title = "Çok Kiracılı Sistem - Türk Bilişim Enterprise CMS";
$page_subtitle = "Multi-Tenant Architecture";
$page_badge = "Multi-Tenant";

// Navigation sections
$nav_sections = [
    'overview' => 'Genel Bakış',
    'architecture' => 'Mimari',
    'security' => 'Güvenlik',
    'performance' => 'Performans',
    'management' => 'Yönetim'
];

// Header include
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
                        <span style="color: #64b5f6; font-size: 0.9rem; font-weight: 600;">🏢 Multi-Tenant Enterprise</span>
                    </div>
                    <h1 style="margin-bottom: 2rem; color: white; font-size: 3.5rem; line-height: 1.2; font-weight: 300; letter-spacing: -0.02em; text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);">
                        Çok Kiracılı<br>
                        <span style="font-weight: 700; background: linear-gradient(45deg, #64b5f6, #4facfe, #64b5f6); background-size: 200% 200%; animation: textGradient 3s ease infinite; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Sistem Mimarisi</span>
                    </h1>
                    <p style="margin-bottom: 3rem; font-size: 1.2rem; line-height: 1.6; color: rgba(255, 255, 255, 0.8); max-width: 600px; margin-left: auto; margin-right: auto; font-weight: 400; text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);">
                        Domain-based tenancy ile<br>
                        <span style="color: #64b5f6; font-weight: 600;">matematik olarak güvenli</span> platform
                    </p>
                </div>
            </div>
        </section>

        <!-- Overview Section -->
        <section id="overview" class="section">
            <h2 class="section-title">Çok Kiracılı Sistemin Gücü</h2>
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

<?php include 'footer.php'; ?>