<?php
$page_title = "Yapay Zeka Sistemi - Türk Bilişim Enterprise CMS";
$page_subtitle = "Yapay Zeka Çalışma Sistemi";
$page_badge = "🧠 Yapay Zeka Enterprise";

// Navigation sections
$nav_sections = [
    'hero' => 'Giriş',
    'overview' => 'Genel Bakış',
    'understanding' => 'Tanıma Sistemi',
    'thinking' => 'Düşünme Sistemi',
    'features' => 'Feature Sistemi',
    'token' => 'Token Sistemi',
    'integration' => 'Entegrasyon',
    'benefits' => 'Faydalar'
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
                Yapay Zeka<br>
                <span style="font-weight: 700; background: linear-gradient(45deg, #64b5f6, #4facfe, #64b5f6); background-size: 200% 200%; animation: textGradient 3s ease infinite; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Çalışma Sistemi</span>
            </h1>
            <p style="margin-bottom: 3rem; font-size: 1.2rem; line-height: 1.6; color: rgba(255, 255, 255, 0.8); max-width: 600px; margin-left: auto; margin-right: auto; font-weight: 400; text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);">
                Sizi, firmanızı ve kullanıcıyı tanıyan<br>
                <span style="color: #64b5f6; font-weight: 600;">tamamen kendi geliştirdiğimiz yapay zeka sistemi</span>
            </p>
        </div>
    </div>
</section>

<!-- Overview Section -->
<section id="overview" class="section">
    <h2 class="section-title">Yapay Zeka Çalışma Sistemi Nasıl Çalışır</h2>
    <p class="section-subtitle">
        Tamamen kendi geliştirdiğimiz yapay zeka sistemi, sektörde benzersiz mimari ile çalışır. 
        <span class="tech-highlight">DeepSeek API entegrasyonu</span> <span class="text-muted">(gelişmiş yapay zeka bağlantısı)</span><br>
        <span class="text-sm text-secondary">→ OpenAI uyumlu API ile güçlü yapay zeka yanıtları alır</span>
        ile birleşen <span class="tech-highlight">Priority Engine</span> <span class="text-muted">(öncelik motoru)</span><br>
        <span class="text-sm text-secondary">→ Prompt'ları akıllıca sıralayarak en iyi yanıtları üretir</span>
        sayesinde dünya standartlarının üzerinde performans sağlar.
    </p>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="brain"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Bizi Tanıma Sistemi <span class="text-sm text-muted">(marka farkındalığı)</span></h3>
            <p class="text-secondary mb-3">
                Yapay zeka sistemi firmanızı derinlemesine tanır ve marka kimliğinizi bilerek yanıt verir. <span class="tech-highlight">AITenantProfile</span> <span class="text-muted">(kiracı profil sistemi)</span><br>
                <span class="text-sm text-secondary">→ Her müşterinin marka bilgilerini ayrı ayrı saklar ve kullanır</span>
                ile brand_name, industry, target_audience, brand_voice gibi kritik bilgileri analiz eder.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Brand Context Building</span> <span class="text-muted">(marka bağlamı oluşturma)</span><br><span class="text-sm text-secondary">→ Marka adı, slogan, değerleri otomatik tanıma</span></li>
                        <li>• <span class="tech-highlight">Smart Field Calculator</span> <span class="text-muted">(akıllı alan hesaplama)</span><br><span class="text-sm text-secondary">→ Eksik bilgileri tamamlama ve zenginleştirme</span></li>
                        <li>• <span class="tech-highlight">Priority-Based Context</span> <span class="text-muted">(öncelikli bağlam)</span><br><span class="text-sm text-secondary">→ İhtiyaca göre detay seviyesi ayarlama</span></li>
                        <li>• <span class="tech-highlight">Cache Optimization</span> <span class="text-muted">(önbellek optimizasyonu)</span><br><span class="text-sm text-secondary">→ Dakika cache ile hızlı marka tanıma</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="building"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Firmayı Tanıma Sistemi <span class="text-sm text-muted">(iş zekası)</span></h3>
            <p class="text-secondary mb-3">
                Sektörde benzersiz <span class="tech-highlight">Business Intelligence</span> <span class="text-muted">(iş zekası sistemi)</span><br>
                <span class="text-sm text-secondary">→ Şirketinizin sektörünü, rekabet durumunu, hedef kitlesini analiz eder</span>
                ile company_size, business_stage, unique_selling_point gibi iş verilerini kullanarak sektöre özel yanıtlar üretir.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">AIProfileSector Analysis</span> <span class="text-muted">(sektör analiz sistemi)</span><br><span class="text-sm text-secondary">→ Sektör destekli yapay zeka yanıtları</span></li>
                        <li>• <span class="tech-highlight">Dynamic Service Suggestions</span> <span class="text-muted">(dinamik hizmet önerileri)</span><br><span class="text-sm text-secondary">→ İş alanınıza özel öneriler ve stratejiler</span></li>
                        <li>• <span class="tech-highlight">Competitive Analysis</span> <span class="text-muted">(rekabet analizi)</span><br><span class="text-sm text-secondary">→ Sektördeki konumunuzu değerlendirme</span></li>
                        <li>• <span class="tech-highlight">Local Market Awareness</span> <span class="text-muted">(yerel pazar bilinci)</span><br><span class="text-sm text-secondary">→ Türkiye pazarı ve yerel trendlere hakimiyet</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="user"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Kullanıcıyı Tanıma Sistemi <span class="text-sm text-muted">(kullanıcı bağlamı)</span></h3>
            <p class="text-secondary mb-3">
                Gelişmiş <span class="tech-highlight">User Context Awareness</span> <span class="text-muted">(kullanıcı bağlam farkındalığı)</span><br>
                <span class="text-sm text-secondary">→ Her kullanıcının rolü, yetkileri, geçmiş etkileşimlerini takip eder</span>
                sistemi ile role-based responses, session tracking ve permission awareness sağlar.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Role-Based Responses</span> <span class="text-muted">(rol tabanlı yanıtlar)</span><br><span class="text-sm text-secondary">→ Admin, editor, viewer gibi rollere özel içerik</span></li>
                        <li>• <span class="tech-highlight">Session Intelligence</span> <span class="text-muted">(oturum zekası)</span><br><span class="text-sm text-secondary">→ Oturum boyunca context sürekliliği</span></li>
                        <li>• <span class="tech-highlight">Permission Awareness</span> <span class="text-muted">(yetki farkındalığı)</span><br><span class="text-sm text-secondary">→ Yetkili olmadığı alanlarda güvenli yanıtlar</span></li>
                        <li>• <span class="tech-highlight">Activity Logging</span> <span class="text-muted">(aktivite kaydı)</span><br><span class="text-sm text-secondary">→ Kullanım kalıpları ve tercihleri takibi</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="cpu"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Düşünme Sistemi <span class="text-sm text-muted">(akıllı karar verme)</span></h3>
            <p class="text-secondary mb-3">
                Kusursuz tasarlanan <span class="tech-highlight">AIPriorityEngine</span> <span class="text-muted">(yapay zeka öncelik motoru)</span><br>
                <span class="text-sm text-secondary">→ Weighted scoring algoritması ile prompt'ları akıllıca sıralar</span>
                sistemi dokuz kategori hiyerarşisi ile system_common'dan conditional_info'ya kadar mükemmel sıralama yapar.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Weighted Scoring Formula</span> <span class="text-muted">(ağırlıklı puanlama formülü)</span><br><span class="text-sm text-secondary">→ Base Weight × Priority Multiplier + Position Bonus hesaplama</span></li>
                        <li>• <span class="tech-highlight">Nine Category Hierarchy</span> <span class="text-muted">(dokuz kategori hiyerarşisi)</span><br><span class="text-sm text-secondary">→ Önem sırasına göre düzenlenmiş prompt kategorileri</span></li>
                        <li>• <span class="tech-highlight">Context Type Determination</span> <span class="text-muted">(bağlam tipi belirleme)</span><br><span class="text-sm text-secondary">→ Minimal, essential, normal, detailed seviyelerinde yanıt</span></li>
                        <li>• <span class="tech-highlight">Smart Caching</span> <span class="text-muted">(akıllı önbellekleme)</span><br><span class="text-sm text-secondary">→ Dakika cache ile hızlı karar verme süreci</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="zap"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Uygulama Sistemi <span class="text-sm text-muted">(özellik çalıştırma)</span></h3>
            <p class="text-secondary mb-3">
                Profesyonel seviyede <span class="tech-highlight">Feature Execution Engine</span> <span class="text-muted">(özellik çalıştırma motoru)</span><br>
                <span class="text-sm text-secondary">→ Token kontrolü, template sistemi, yapay zeka yanıtı ve kayıt işlemlerini otomatik yürütür</span>
                ile altı aşamalı mükemmel işlem süreci sağlar.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Token Validation</span> <span class="text-muted">(token doğrulama)</span><br><span class="text-sm text-secondary">→ Her yapay zeka çağrısından önce otomatik bakiye kontrolü</span></li>
                        <li>• <span class="tech-highlight">Template System</span> <span class="text-muted">(şablon sistemi)</span><br><span class="text-sm text-secondary">→ Quick prompt + Expert prompt birleştirme</span></li>
                        <li>• <span class="tech-highlight">DeepSeek Integration</span> <span class="text-muted">(gelişmiş yapay zeka entegrasyonu)</span><br><span class="text-sm text-secondary">→ Yüksek kaliteli yapay zeka yanıtları alma</span></li>
                        <li>• <span class="tech-highlight">Conversation Tracking</span> <span class="text-muted">(konuşma takibi)</span><br><span class="text-sm text-secondary">→ Tüm kullanımları otomatik kaydetme ve analiz etme</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="layers"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Feature Sistemi <span class="text-sm text-muted">(özellik yönetimi)</span></h3>
            <p class="text-secondary mb-3">
                Kapsamlı <span class="tech-highlight">AI Feature Management</span> <span class="text-muted">(yapay zeka özellik yönetimi)</span><br>
                <span class="text-sm text-secondary">→ İçerik üretiminden SEO analizine kadar geniş yapay zeka araç koleksiyonu</span>
                sistemi ile content-creation, seo-optimization, social-media, business-communication kategorilerinde organize edilmiş özellikler.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Category Organization</span> <span class="text-muted">(kategori organizasyonu)</span><br><span class="text-sm text-secondary">→ Yedi ana kategoride düzenlenmiş yapay zeka özellikleri</span></li>
                        <li>• <span class="tech-highlight">Dynamic Input Validation</span> <span class="text-muted">(dinamik girdi doğrulama)</span><br><span class="text-sm text-secondary">→ Her özellik için özel girdi kontrol sistemi</span></li>
                        <li>• <span class="tech-highlight">Usage Analytics</span> <span class="text-muted">(kullanım analitiği)</span><br><span class="text-sm text-secondary">→ Hangi özellikler ne sıklıkla kullanılıyor takibi</span></li>
                        <li>• <span class="tech-highlight">Rating System</span> <span class="text-muted">(değerlendirme sistemi)</span><br><span class="text-sm text-secondary">→ Kullanıcı memnuniyeti ve özellik kalitesi ölçümü</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Token System -->
<section id="token" class="section">
    <h2 class="section-title text-center">Token Sistemi Nasıl Çalışır</h2>
    <p class="section-subtitle text-center">
        Tamamen kendi geliştirdiğimiz gerçek zamanlı token yönetim sistemi ile her kullanım anında izlenir
    </p>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="coins" class="w-6 h-6"></i>
            </div>
            <h3>Token Kontrolü ve Düşürme</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• <span class="tech-highlight">Real-time Balance Check</span> <span class="text-muted">(gerçek zamanlı bakiye kontrolü)</span><br><span class="text-sm text-secondary">→ Her yapay zeka kullanımından önce otomatik bakiye doğrulama</span></li>
                    <li>• <span class="tech-highlight">Automatic Deduction</span> <span class="text-muted">(otomatik düşürme)</span><br><span class="text-sm text-secondary">→ Yapay zeka yanıtı aldıktan sonra anında token düşürme</span></li>
                    <li>• <span class="tech-highlight">Usage Logging</span> <span class="text-muted">(kullanım kaydı)</span><br><span class="text-sm text-secondary">→ Her token kullanımının detaylı kayıt altına alınması</span></li>
                    <li>• <span class="tech-highlight">Tenant Isolation</span> <span class="text-muted">(kiracı izolasyonu)</span><br><span class="text-sm text-secondary">→ Her müşterinin token bakiyesi tamamen ayrı yönetilir</span></li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="package" class="w-6 h-6"></i>
            </div>
            <h3>Paket Sistemi</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• <span class="tech-highlight">AITokenPackage Management</span> <span class="text-muted">(token paket yönetimi)</span><br><span class="text-sm text-secondary">→ Farklı token miktarları ve fiyatlarında paket seçenekleri</span></li>
                    <li>• <span class="tech-highlight">Purchase Processing</span> <span class="text-muted">(satın alma işlemi)</span><br><span class="text-sm text-secondary">→ Güvenli ödeme işlemi ve otomatik token ekleme</span></li>
                    <li>• <span class="tech-highlight">Monthly Limits</span> <span class="text-muted">(aylık limitler)</span><br><span class="text-sm text-secondary">→ Kullanıcı bazında esnek aylık kullanım limitleri</span></li>
                    <li>• <span class="tech-highlight">Usage Analytics</span> <span class="text-muted">(kullanım analitiği)</span><br><span class="text-sm text-secondary">→ Token tüketim trend'leri ve kullanım raporları</span></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Integration -->
<section id="integration" class="section">
    <h2 class="section-title text-center">Sistem Entegrasyonu ve Kullanım Alanları</h2>
    <p class="section-subtitle text-center">
        Yapay zeka sistemi platformun her noktasına entegre edilmiş durumda
    </p>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="layout" class="w-6 h-6"></i>
            </div>
            <h3>Admin Panel Entegrasyonu</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• <span class="tech-highlight">ChatPanel Component</span> <span class="text-muted">(sohbet panel bileşeni)</span><br><span class="text-sm text-secondary">→ Sol sidebar'da sürekli erişilebilir yapay zeka chat arayüzü</span></li>
                    <li>• <span class="tech-highlight">Feature Management</span> <span class="text-muted">(özellik yönetimi)</span><br><span class="text-sm text-secondary">→ Yapay zeka özelliklerini test etme ve yönetme paneli</span></li>
                    <li>• <span class="tech-highlight">Debug Dashboard</span> <span class="text-muted">(hata ayıklama panosu)</span><br><span class="text-sm text-secondary">→ Yapay zeka kullanımlarını analiz etme araçları</span></li>
                    <li>• <span class="tech-highlight">Token Monitoring</span> <span class="text-muted">(token izleme)</span><br><span class="text-sm text-secondary">→ Gerçek zamanlı token bakiyesi ve kullanım takibi</span></li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="globe" class="w-6 h-6"></i>
            </div>
            <h3>Frontend Entegrasyonu</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• <span class="tech-highlight">Prowess Showcase</span> <span class="text-muted">(yetenek vitrin sayfaları)</span><br><span class="text-sm text-secondary">→ Potansiyel müşterilere yapay zeka yetenekleri gösterimi</span></li>
                    <li>• <span class="tech-highlight">Helper Functions</span> <span class="text-muted">(yardımcı fonksiyonlar)</span><br><span class="text-sm text-secondary">→ Global olarak erişilebilir yapay zeka fonksiyonları</span></li>
                    <li>• <span class="tech-highlight">Interactive Demos</span> <span class="text-muted">(etkileşimli demolar)</span><br><span class="text-sm text-secondary">→ Ziyaretçilerin yapay zeka özelliklerini deneyebileceği alanlar</span></li>
                    <li>• <span class="tech-highlight">Content Generation</span> <span class="text-muted">(içerik üretimi)</span><br><span class="text-sm text-secondary">→ Sayfa içeriklerinin yapay zeka ile otomatik üretimi</span></li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="smartphone" class="w-6 h-6"></i>
            </div>
            <h3>Mobil Uygulama Entegrasyonu</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• <span class="tech-highlight">API Endpoints</span> <span class="text-muted">(programlama arayüzü)</span><br><span class="text-sm text-secondary">→ Flutter uygulamasından yapay zeka özelliklerine erişim</span></li>
                    <li>• <span class="tech-highlight">Mobile-Optimized UI</span> <span class="text-muted">(mobil optimize arayüz)</span><br><span class="text-sm text-secondary">→ Dokunmatik cihazlar için optimize edilmiş yapay zeka arayüzü</span></li>
                    <li>• <span class="tech-highlight">Offline Capabilities</span> <span class="text-muted">(çevrimdışı yetenekler)</span><br><span class="text-sm text-secondary">→ İnternet olmadığında bile temel yapay zeka özellikleri</span></li>
                    <li>• <span class="tech-highlight">Push Notifications</span> <span class="text-muted">(anlık bildirimler)</span><br><span class="text-sm text-secondary">→ Yapay zeka yanıtları için mobil bildirim sistemi</span></li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="activity" class="w-6 h-6"></i>
            </div>
            <h3>Log ve Geliştirme Sistemi</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• <span class="tech-highlight">Comprehensive Logging</span> <span class="text-muted">(kapsamlı kayıt sistemi)</span><br><span class="text-sm text-secondary">→ Tüm yapay zeka kullanımlarının detaylı kayıt altına alınması</span></li>
                    <li>• <span class="tech-highlight">Performance Monitoring</span> <span class="text-muted">(performans izleme)</span><br><span class="text-sm text-secondary">→ Yanıt süreleri, token kullanımı, başarı oranı takibi</span></li>
                    <li>• <span class="tech-highlight">Error Tracking</span> <span class="text-muted">(hata takibi)</span><br><span class="text-sm text-secondary">→ Yapay zeka hatalarının otomatik tespit edilmesi</span></li>
                    <li>• <span class="tech-highlight">Debug Tools</span> <span class="text-muted">(hata ayıklama araçları)</span><br><span class="text-sm text-secondary">→ Prompt debugging ve context inspection araçları</span></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Benefits -->
<section id="benefits" class="section">
    <h2 class="section-title text-center">Son Kullanıcıya Sağladığı Faydalar</h2>
    <p class="section-subtitle text-center">
        Tamamen kendi geliştirdiğimiz yapay zeka sistemi işinizi nasıl dönüştürüyor
    </p>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="zap" class="w-6 h-6"></i>
            </div>
            <h3>Üretkenlik Artışı</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• <span class="tech-highlight">Content Acceleration</span> <span class="text-muted">(içerik hızlandırma)</span><br><span class="text-sm text-secondary">→ Normal sürenin çok altında professional içerik üretimi</span></li>
                    <li>• <span class="tech-highlight">SEO Automation</span> <span class="text-muted">(SEO otomasyonu)</span><br><span class="text-sm text-secondary">→ Anahtar kelime analizi ve optimizasyon otomatik yapılır</span></li>
                    <li>• <span class="tech-highlight">Bulk Operations</span> <span class="text-muted">(toplu işlemler)</span><br><span class="text-sm text-secondary">→ Çoklu içerik üretimi ve düzenleme işlemleri</span></li>
                    <li>• <span class="tech-highlight">Template Automation</span> <span class="text-muted">(şablon otomasyonu)</span><br><span class="text-sm text-secondary">→ Standart formatları otomatik doldurma</span></li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="target" class="w-6 h-6"></i>
            </div>
            <h3>Kalite İyileştirme</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• <span class="tech-highlight">Professional Standards</span> <span class="text-muted">(profesyonel standartlar)</span><br><span class="text-sm text-secondary">→ Her içerik professional yazım standartlarında üretilir</span></li>
                    <li>• <span class="tech-highlight">Brand Consistency</span> <span class="text-muted">(marka tutarlılığı)</span><br><span class="text-sm text-secondary">→ Marka sesine uygun tutarlı içerik ton'u</span></li>
                    <li>• <span class="tech-highlight">Error Prevention</span> <span class="text-muted">(hata önleme)</span><br><span class="text-sm text-secondary">→ Yazım hataları ve tutarsızlıkları otomatik düzeltme</span></li>
                    <li>• <span class="tech-highlight">Industry Expertise</span> <span class="text-muted">(sektör uzmanlığı)</span><br><span class="text-sm text-secondary">→ Sektöre özel terminoloji ve yaklaşımlar</span></li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="trending-up" class="w-6 h-6"></i>
            </div>
            <h3>İş Büyümesi</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• <span class="tech-highlight">Enhanced Engagement</span> <span class="text-muted">(gelişmiş etkileşim)</span><br><span class="text-sm text-secondary">→ Müşteri etkileşim oranlarında kayda değer artış</span></li>
                    <li>• <span class="tech-highlight">Conversion Optimization</span> <span class="text-muted">(dönüşüm optimizasyonu)</span><br><span class="text-sm text-secondary">→ Satış dönüşüm oranlarını artıran içerik stratejileri</span></li>
                    <li>• <span class="tech-highlight">Market Expansion</span> <span class="text-muted">(pazar genişleme)</span><br><span class="text-sm text-secondary">→ Yeni pazarlara girme imkanları ve stratejiler</span></li>
                    <li>• <span class="tech-highlight">Competitive Edge</span> <span class="text-muted">(rekabet avantajı)</span><br><span class="text-sm text-secondary">→ Rakiplerden öne geçiren yapay zeka destekli yaklaşım</span></li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="shield-check" class="w-6 h-6"></i>
            </div>
            <h3>Kurumsal Faydalar</h3>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• <span class="tech-highlight">Scalable Infrastructure</span> <span class="text-muted">(ölçeklenebilir altyapı)</span><br><span class="text-sm text-secondary">→ İş büyüdükçe yapay zeka kapasitesi de büyür</span></li>
                    <li>• <span class="tech-highlight">Cost Efficiency</span> <span class="text-muted">(maliyet verimliliği)</span><br><span class="text-sm text-secondary">→ İçerik üretim maliyetlerinde önemli tasarruf</span></li>
                    <li>• <span class="tech-highlight">24/7 Availability</span> <span class="text-muted">(sürekli erişim)</span><br><span class="text-sm text-secondary">→ Gün boyu kesintisiz yapay zeka desteği</span></li>
                    <li>• <span class="tech-highlight">Security Compliance</span> <span class="text-muted">(güvenlik uyumluluğu)</span><br><span class="text-sm text-secondary">→ Kurumsal güvenlik standartlarına uygun işletim</span></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>