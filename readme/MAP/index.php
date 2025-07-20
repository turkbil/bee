<?php
// Sayfa ayarları
$page_title = "Türk Bilişim Enterprise CMS - Yapay Zeka Destekli";
$page_subtitle = "AI-Powered Enterprise";
$page_badge = "AI Enterprise";

// Navigation sections
$nav_sections = [
    'overview' => 'Genel Bakış',
    'features' => 'Özellikler',
    'technology' => 'Teknoloji',
    'comparison' => 'Karşılaştırma',
    'roadmap' => 'Yol Haritası'
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
                        <span style="color: #64b5f6; font-size: 0.9rem; font-weight: 600;">🤖 AI-Powered Enterprise</span>
                    </div>
                    <h1 style="margin-bottom: 2rem; color: white; font-size: 3.5rem; line-height: 1.2; font-weight: 300; letter-spacing: -0.02em; text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);">
                        Yapay Zeka Destekli<br>
                        <span style="font-weight: 700; background: linear-gradient(45deg, #64b5f6, #4facfe, #64b5f6); background-size: 200% 200%; animation: textGradient 3s ease infinite; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Enterprise CMS</span>
                    </h1>
                    <p style="margin-bottom: 3rem; font-size: 1.2rem; line-height: 1.6; color: rgba(255, 255, 255, 0.8); max-width: 600px; margin-left: auto; margin-right: auto; font-weight: 400; text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);">
                        Tamamen kendi geliştirdiğimiz teknoloji ile<br>
                        <span style="color: #64b5f6; font-weight: 600;">dünya standartlarının üzerinde</span> akıllı platform
                    </p>
                </div>
            </div>
        </section>

        <!-- Overview Section -->
        <section id="overview" class="section">
            <h2 class="section-title">Neden Farklıyız?</h2>
            <p class="section-subtitle">
                Geleneksel CMS sistemlerinin çok ötesinde, tamamen kendi geliştirdiğimiz ileri teknoloji 
                mimarisi ile dünya standartlarında bir platform. Her satır kod, üstün performans ve 
                maksimum güvenlik odaklı olarak özenle yazılmıştır.
            </p>
            
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i data-lucide="database"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Gelişmiş Güvenlik Mimarisi</h3>
                    <p class="text-secondary">
                        Her müşteri için tamamen ayrı veritabanı ile tasarladığımız sistem. <span class="tech-highlight">Cross-tenant data leak</span> 
                        <span class="text-muted">(müşteri verilerinin birbirine karışması)</span><br>
                        <span class="text-sm text-secondary">→ Bir müşterinin verilerini diğer müşterinin görmesi</span>
                        matematik olarak imkansız. Sektörde benzersiz veritabanı seviyesinde izolasyon.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i data-lucide="brain"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Özel Geliştirilen Yapay Zeka</h3>
                    <p class="text-secondary">
                        Tamamen kendi geliştirdiğimiz yapay zeka, müşterinin marka kimliğini, sektörünü ve sesini kusursuz anlıyor. 
                        <span class="tech-highlight">Context-aware responses</span> 
                        <span class="text-muted">(duruma göre akıllı yanıtlar)</span><br>
                        <span class="text-sm text-secondary">→ Yapay zeka önceki konuşmaları hatırlayıp uygun cevaplar verme</span>
                        ile sektörde benzersiz kişiselleştirilmiş içerik.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i data-lucide="zap"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Üstün Performans Mimarisi</h3>
                    <p class="text-secondary mb-3">
                        Kendi geliştirdiğimiz üstün performans teknolojileri:
                    </p>
                    <div class="code-block">
                        <div class="text-xs">
                            <ul class="list-none space-y-2">
                                <li>• <span class="tech-highlight">Multi-layered caching</span> <span class="text-muted">(çok katmanlı önbellekleme)</span><br><span class="text-sm text-secondary">→ Sık kullanılan verileri farklı seviyelerde saklama</span></li>
                                <li>• <span class="tech-highlight">Advanced query optimization</span> <span class="text-muted">(gelişmiş sorgu hızlandırma)</span><br><span class="text-sm text-secondary">→ Veritabanı sorgularını optimize etme</span></li>
                                <li>• <span class="tech-highlight">Intelligent lazy loading</span> <span class="text-muted">(akıllı yükleme)</span><br><span class="text-sm text-secondary">→ Sadece gerekli verileri yükleme</span></li>
                                <li>• <span class="tech-highlight">Sektörde benzersiz &lt;1.2s</span> <span class="text-muted">(sayfa yükleme süresi)</span><br><span class="text-sm text-secondary">→ Sitenin tamamen yüklenmesi için geçen süre</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i data-lucide="code"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Profesyonel Kod Mimarisi</h3>
                    <p class="text-secondary mb-3">
                        Kendi tasarladığımız dünya standartlarında kod mimarisi:
                    </p>
                    <div class="code-block">
                        <div class="text-xs">
                            <ul class="list-none space-y-2">
                                <li>• <span class="tech-highlight">SOLID principles</span> <span class="text-muted">(temiz kod kuralları)</span><br><span class="text-sm text-secondary">→ Kodun okunabilir ve sürdürülebilir olması</span></li>
                                <li>• <span class="tech-highlight">Advanced repository pattern</span> <span class="text-muted">(gelişmiş veri erişim katmanı)</span><br><span class="text-sm text-secondary">→ Veritabanı işlemlerini organize etme</span></li>
                                <li>• <span class="tech-highlight">Enterprise service layer</span> <span class="text-muted">(kurumsal iş mantığı katmanı)</span><br><span class="text-sm text-secondary">→ İş kurallarını ayrı yönetme</span></li>
                                <li>• <span class="tech-highlight">Maintainable kod yapısı</span> <span class="text-muted">(sürdürülebilir kod)</span><br><span class="text-sm text-secondary">→ Kodun bakım ve geliştirme işlemlerini kolaylaştırır</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i data-lucide="shield"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Güvenlik Odaklı <span class="text-sm text-muted">(çok katmanlı güvenlik sistemi)</span></h3>
                    <p class="text-secondary mb-3">
                        Profesyonel güvenlik katmanları ile tam koruma:
                    </p>
                    <div class="code-block">
                        <div class="text-xs">
                            <ul class="list-none space-y-2">
                                <li>• <span class="tech-highlight">SQL Injection Protection</span> <span class="text-muted">(zararlı veri girişi koruması)</span><br><span class="text-sm text-secondary">→ Kötü amaçlı SQL kodlarını veritabanına girmesini engelleme</span></li>
                                <li>• <span class="tech-highlight">CSRF Protection</span> <span class="text-muted">(sahte istek koruması)</span><br><span class="text-sm text-secondary">→ Yetkisiz form gönderimlerini ve sahte istekleri engelleme</span></li>
                                <li>• <span class="tech-highlight">XSS Filtering</span> <span class="text-muted">(zararlı script engelleme)</span><br><span class="text-sm text-secondary">→ Site içi script saldırılarını ve zararlı kod enjeksiyonunu önleme</span></li>
                                <li>• <span class="tech-highlight">Input Validation</span> <span class="text-muted">(veri doğrulama)</span><br><span class="text-sm text-secondary">→ Gelen verilerin güvenli ve doğru olup olmadığını kontrol etme</span></li>
                                <li>• <span class="tech-highlight">Authentication Security</span> <span class="text-muted">(kimlik doğrulama güvenliği)</span><br><span class="text-sm text-secondary">→ Kullanıcı giriş işlemlerini güvenli hale getirme</span></li>
                                <li>• <span class="tech-highlight">Data Encryption</span> <span class="text-muted">(veri şifreleme)</span><br><span class="text-sm text-secondary">→ Hassas verileri şifreleyerek saklama ve iletme</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i data-lucide="layers"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Modüler Yapı</h3>
                    <p class="text-secondary mb-3">
                        Esnek ve ölçeklenebilir modüler sistem:
                    </p>
                    <div class="code-block">
                        <div class="text-xs">
                            <ul class="list-none space-y-2">
                                <li>• <span class="tech-highlight">Plug-and-play modules</span> <span class="text-muted">(tak-çalıştır modüller)</span><br><span class="text-xs text-muted">→ Yeni özellikler kolay ekleme</span></li>
                                <li>• <span class="tech-highlight">Independent deployment</span> <span class="text-muted">(bağımsız güncellemeler)</span><br><span class="text-xs text-muted">→ Her modülü ayrı güncelleme</span></li>
                                <li>• <span class="tech-highlight">Scalable architecture</span> <span class="text-muted">(büyüyebilir mimari)</span><br><span class="text-xs text-muted">→ Büyük projelere uyum sağlama</span></li>
                                <li>• <span class="tech-highlight">Flexible system</span> <span class="text-muted">(esnek sistem)</span><br><span class="text-xs text-muted">→ Farklı ihtiyaçlara kolayca uyum</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

<?php include 'footer.php'; ?>