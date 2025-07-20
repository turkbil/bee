<?php
$page_title = "Sistem Mimarisi - Türk Bilişim Enterprise CMS";
$page_subtitle = "Enterprise Architecture Overview";
$page_badge = "🏗️ Sistem Mimarisi";

// Navigation sections
$nav_sections = [
    'hero' => 'Giriş',
    'overview' => 'Genel Bakış',
    'layers' => 'Mimari Katmanları',
    'patterns' => 'Tasarım Kalıpları',
    'services' => 'Servis Mimarisi',
    'database' => 'Veritabanı Tasarımı',
    'scalability' => 'Ölçeklenebilirlik',
    'deployment' => 'Deployment Mimarisi'
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
                Enterprise<br>
                <span style="font-weight: 700; background: linear-gradient(45deg, #64b5f6, #4facfe, #64b5f6); background-size: 200% 200%; animation: textGradient 3s ease infinite; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Sistem Mimarisi</span>
            </h1>
            <p style="margin-bottom: 3rem; font-size: 1.2rem; line-height: 1.6; color: rgba(255, 255, 255, 0.8); max-width: 600px; margin-left: auto; margin-right: auto; font-weight: 400; text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);">
                Modern, ölçeklenebilir ve güvenli<br>
                <span style="color: #64b5f6; font-weight: 600;">enterprise CMS mimarisi</span>
            </p>
        </div>
    </div>
</section>

<!-- Overview Section -->
<section id="overview" class="section">
    <h2 class="section-title">Sistem Mimarisi Genel Bakış</h2>
    <p class="section-subtitle">
        Türk Bilişim Enterprise CMS, modern enterprise uygulamaları için tasarlanmış 
        çok katmanlı mimari pattern'leri kullanır. Clean Architecture <span class="text-muted">(temiz mimari)</span> 
        prensipleri ile Domain-Driven Design <span class="text-muted">(alan güdümlü tasarım)</span> yaklaşımını 
        birleştirerek maintainable <span class="text-muted">(sürdürülebilir)</span>, testable <span class="text-muted">(test edilebilir)</span> 
        ve scalable <span class="text-muted">(ölçeklenebilir)</span> bir yapı sunar.
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="layers"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Layered Architecture <span class="text-sm text-muted">(katmanlı mimari)</span></h3>
            <p class="text-secondary mb-4">
                Sistem, birbirinden bağımsız fakat birbiriyle iletişim halinde olan katmanlardan oluşur. 
                Her katman kendine özgü sorumluluklara sahiptir ve <span class="tech-highlight">Separation of Concerns</span> 
                <span class="text-muted">(sorumlulukların ayrılması)</span> prensibini takip eder. Bu yaklaşım sayesinde 
                kod maintainability <span class="text-muted">(sürdürülebilirlik)</span> artarken, bug'ların sisteme 
                yayılması önlenir.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Presentation Layer</span> <span class="text-muted">(sunum katmanı)</span><br><span class="text-sm text-secondary">→ Kullanıcı arayüzü, Livewire components ve Blade templates</span></li>
                        <li>• <span class="tech-highlight">Application Layer</span> <span class="text-muted">(uygulama katmanı)</span><br><span class="text-sm text-secondary">→ Use cases, business logic orchestration ve validation</span></li>
                        <li>• <span class="tech-highlight">Domain Layer</span> <span class="text-muted">(alan katmanı)</span><br><span class="text-sm text-secondary">→ Business entities, value objects ve domain rules</span></li>
                        <li>• <span class="tech-highlight">Infrastructure Layer</span> <span class="text-muted">(altyapı katmanı)</span><br><span class="text-sm text-secondary">→ Database, external APIs ve framework integrations</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="puzzle"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Modular Design Pattern <span class="text-sm text-muted">(modüler tasarım)</span></h3>
            <p class="text-secondary mb-4">
                Sistem, loosely coupled <span class="text-muted">(gevşek bağlı)</span> modüllerden oluşur. 
                Her modül kendi domain'ine ait business logic'i içerir ve diğer modüllerle 
                well-defined interfaces <span class="text-muted">(iyi tanımlanmış arayüzler)</span> üzerinden 
                iletişim kurar. Bu yaklaşım team scalability <span class="text-muted">(takım ölçeklenebilirliği)</span> 
                ve parallel development <span class="text-muted">(paralel geliştirme)</span> sağlar.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Module Isolation</span> <span class="text-muted">(modül izolasyonu)</span><br><span class="text-sm text-secondary">→ Her modül kendi namespace'inde çalışır</span></li>
                        <li>• <span class="tech-highlight">Service Provider Pattern</span> <span class="text-muted">(servis sağlayıcı kalıbı)</span><br><span class="text-sm text-secondary">→ Dependency injection ile servis registration</span></li>
                        <li>• <span class="tech-highlight">Event-Driven Communication</span> <span class="text-muted">(olay güdümlü iletişim)</span><br><span class="text-sm text-secondary">→ Module'ler arası loose coupling için events</span></li>
                        <li>• <span class="tech-highlight">Interface Segregation</span> <span class="text-muted">(arayüz ayrımı)</span><br><span class="text-sm text-secondary">→ Her modül sadece ihtiyacı olan interface'leri kullanır</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="server"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Multi-Tenant Architecture <span class="text-sm text-muted">(çok kiracılı mimari)</span></h3>
            <p class="text-secondary mb-4">
                Tenant isolation <span class="text-muted">(kiracı izolasyonu)</span> mimarimiz, her müşteri için 
                tamamen ayrı database, storage ve cache sistemleri sağlar. Data segregation 
                <span class="text-muted">(veri ayrımı)</span> security-first approach <span class="text-muted">(güvenlik öncelikli yaklaşım)</span> 
                ile tasarlanmıştır. Horizontal scaling <span class="text-muted">(yatay ölçekleme)</span> için optimize edilmiş 
                tenant discovery <span class="text-muted">(kiracı keşfi)</span> mekanizması kullanır.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Database Per Tenant</span> <span class="text-muted">(kiracı başına veritabanı)</span><br><span class="text-sm text-secondary">→ Tam veri izolasyonu ve güvenlik</span></li>
                        <li>• <span class="tech-highlight">Domain-Based Routing</span> <span class="text-muted">(domain tabanlı yönlendirme)</span><br><span class="text-sm text-secondary">→ Subdomain ile otomatik tenant detection</span></li>
                        <li>• <span class="tech-highlight">Isolated File Storage</span> <span class="text-muted">(izole dosya depolama)</span><br><span class="text-sm text-secondary">→ Her tenant'ın kendi storage area'sı</span></li>
                        <li>• <span class="tech-highlight">Cache Segmentation</span> <span class="text-muted">(cache bölümleme)</span><br><span class="text-sm text-secondary">→ Redis ile tenant-aware caching</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="cpu"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Microservice-Ready Design <span class="text-sm text-muted">(mikroservis hazır tasarım)</span></h3>
            <p class="text-secondary mb-4">
                Mevcut monolithic <span class="text-muted">(tekli yapı)</span> architecture, microservices'e geçiş için 
                hazır olacak şekilde tasarlanmıştır. Bounded contexts <span class="text-muted">(sınırlı bağlamlar)</span> 
                net olarak tanımlanmış, API contracts <span class="text-muted">(API sözleşmeleri)</span> standardize edilmiş 
                ve service boundaries <span class="text-muted">(servis sınırları)</span> business capabilities 
                <span class="text-muted">(iş yetenekleri)</span> etrafında çizilmiştir.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Domain Boundaries</span> <span class="text-muted">(alan sınırları)</span><br><span class="text-sm text-secondary">→ Her modül potansiyel bir microservice</span></li>
                        <li>• <span class="tech-highlight">API-First Approach</span> <span class="text-muted">(API öncelikli yaklaşım)</span><br><span class="text-sm text-secondary">→ RESTful APIs ve standardized responses</span></li>
                        <li>• <span class="tech-highlight">Event Sourcing Ready</span> <span class="text-muted">(olay kaynağı hazır)</span><br><span class="text-sm text-secondary">→ Domain events ile state changes tracking</span></li>
                        <li>• <span class="tech-highlight">Circuit Breaker Pattern</span> <span class="text-muted">(devre kesici kalıbı)</span><br><span class="text-sm text-secondary">→ Service failures için resilience mechanisms</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Architecture Layers -->
<section id="layers" class="section">
    <h2 class="section-title text-center">Mimari Katmanları</h2>
    <p class="section-subtitle text-center">
        Her katman belirli sorumlulukları üstlenir ve dependency inversion principle 
        <span class="text-muted">(bağımlılık ters çevirme prensibi)</span> ile birbirine bağlanır
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="monitor" class="w-6 h-6"></i>
            </div>
            <h3>Presentation Layer <span class="text-sm text-muted">(sunum katmanı)</span></h3>
            <p class="text-secondary mb-3">
                Kullanıcı ile sistem arasındaki etkileşimi yönetir. Livewire components ile reactive UI, 
                Blade templates ile server-side rendering ve Alpine.js ile client-side interactivity sağlar.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• <span class="tech-highlight">Livewire Components</span><br><span class="text-sm text-secondary">→ Real-time reactive user interfaces</span></li>
                    <li>• <span class="tech-highlight">Blade Templates</span><br><span class="text-sm text-secondary">→ Server-side rendering with caching</span></li>
                    <li>• <span class="tech-highlight">Alpine.js Integration</span><br><span class="text-sm text-secondary">→ Lightweight client-side reactivity</span></li>
                    <li>• <span class="tech-highlight">Responsive Design</span><br><span class="text-sm text-secondary">→ Mobile-first, accessible interfaces</span></li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="settings" class="w-6 h-6"></i>
            </div>
            <h3>Application Layer <span class="text-sm text-muted">(uygulama katmanı)</span></h3>
            <p class="text-secondary mb-3">
                Business use cases'leri orchestrate eder. Controllers, Services ve Command handlers 
                bu katmanda yer alır. Domain logic'i koordine ederken infrastructure dependencies'leri manage eder.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• <span class="tech-highlight">Use Case Controllers</span><br><span class="text-sm text-secondary">→ HTTP request/response handling</span></li>
                    <li>• <span class="tech-highlight">Application Services</span><br><span class="text-sm text-secondary">→ Business workflow orchestration</span></li>
                    <li>• <span class="tech-highlight">Command/Query Handlers</span><br><span class="text-sm text-secondary">→ CQRS pattern implementation</span></li>
                    <li>• <span class="tech-highlight">Validation Layer</span><br><span class="text-sm text-secondary">→ Input validation ve business rules</span></li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="brain" class="w-6 h-6"></i>
            </div>
            <h3>Domain Layer <span class="text-sm text-muted">(alan katmanı)</span></h3>
            <p class="text-secondary mb-3">
                Sistemin kalbi olan business logic bu katmanda yer alır. Domain entities, value objects, 
                aggregates ve domain services pure business rules'ları framework'den bağımsız olarak implement eder.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• <span class="tech-highlight">Domain Entities</span><br><span class="text-sm text-secondary">→ Business objects with behavior</span></li>
                    <li>• <span class="tech-highlight">Value Objects</span><br><span class="text-sm text-secondary">→ Immutable business concepts</span></li>
                    <li>• <span class="tech-highlight">Domain Events</span><br><span class="text-sm text-secondary">→ Business state change notifications</span></li>
                    <li>• <span class="tech-highlight">Business Rules</span><br><span class="text-sm text-secondary">→ Core business logic validation</span></li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="database" class="w-6 h-6"></i>
            </div>
            <h3>Infrastructure Layer <span class="text-sm text-muted">(altyapı katmanı)</span></h3>
            <p class="text-secondary mb-3">
                External concerns'leri handle eder. Database access, file storage, external APIs, 
                caching ve messaging systems bu katmanda implement edilir. Framework-specific code buradadır.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• <span class="tech-highlight">Repository Pattern</span><br><span class="text-sm text-secondary">→ Data access abstraction</span></li>
                    <li>• <span class="tech-highlight">External API Clients</span><br><span class="text-sm text-secondary">→ Third-party service integration</span></li>
                    <li>• <span class="tech-highlight">Caching Mechanisms</span><br><span class="text-sm text-secondary">→ Redis ve application-level caching</span></li>
                    <li>• <span class="tech-highlight">File Storage Systems</span><br><span class="text-sm text-secondary">→ Local, S3 ve CDN integration</span></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Design Patterns -->
<section id="patterns" class="section">
    <h2 class="section-title text-center">Tasarım Kalıpları</h2>
    <p class="section-subtitle text-center">
        Proven design patterns ile maintainable, extensible ve testable kod yapısı
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="git-branch" class="w-6 h-6"></i>
            </div>
            <h3>Repository Pattern</h3>
            <p class="text-secondary mb-3">
                Data access logic'ini business logic'den ayırır. Database implementations'ı abstract ederek 
                testability artırır ve data source değişikliklerinde flexibility sağlar.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Interface-based data access</li>
                    <li>• Multiple database support</li>
                    <li>• Easy unit testing with mocks</li>
                    <li>• Query optimization centralization</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="package" class="w-6 h-6"></i>
            </div>
            <h3>Service Container Pattern</h3>
            <p class="text-secondary mb-3">
                Dependency injection ile loose coupling sağlar. Laravel's service container 
                automatic resolution ve lifetime management için kullanılır.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Automatic dependency resolution</li>
                    <li>• Singleton ve transient lifetimes</li>
                    <li>• Interface binding</li>
                    <li>• Constructor injection</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="radio" class="w-6 h-6"></i>
            </div>
            <h3>Observer Pattern</h3>
            <p class="text-secondary mb-3">
                Laravel Events ile loose coupling sağlanır. Domain events business state changes'leri 
                communicate ederken system integrations decouple eder.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Domain event broadcasting</li>
                    <li>• Asynchronous event handling</li>
                    <li>• Event sourcing capabilities</li>
                    <li>• Cross-module communication</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="factory" class="w-6 h-6"></i>
            </div>
            <h3>Factory Pattern</h3>
            <p class="text-secondary mb-3">
                Complex object creation'ı encapsulate eder. Tenant-specific configurations, 
                service instances ve conditional object creation için kullanılır.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Tenant-aware object creation</li>
                    <li>• Configuration-based instantiation</li>
                    <li>• Abstract factory implementations</li>
                    <li>• Conditional service creation</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Service Architecture -->
<section id="services" class="section">
    <h2 class="section-title text-center">Servis Mimarisi</h2>
    <p class="section-subtitle text-center">
        Business capabilities etrafında organize edilmiş service-oriented architecture
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="user-check" class="w-6 h-6"></i>
            </div>
            <h3>Authentication Service</h3>
            <p class="text-secondary mb-3">
                Multi-tenant authentication, role-based access control ve session management. 
                JWT tokens ile stateless authentication ve secure user context management.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Multi-tenant user isolation</li>
                    <li>• Role-based permissions</li>
                    <li>• Session ve token management</li>
                    <li>• Two-factor authentication</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="file-text" class="w-6 h-6"></i>
            </div>
            <h3>Content Management Service</h3>
            <p class="text-secondary mb-3">
                Dynamic content creation, versioning ve publishing workflows. 
                Multi-language support, SEO optimization ve content scheduling capabilities.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Dynamic page generation</li>
                    <li>• Content versioning system</li>
                    <li>• Multi-language management</li>
                    <li>• SEO optimization tools</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="puzzle" class="w-6 h-6"></i>
            </div>
            <h3>Widget Management Service</h3>
            <p class="text-secondary mb-3">
                Reusable UI components, drag-drop interface builder ve dynamic content blocks. 
                Template inheritance ve component composition patterns.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Reusable widget components</li>
                    <li>• Drag-drop page builder</li>
                    <li>• Dynamic content rendering</li>
                    <li>• Template inheritance system</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="brain" class="w-6 h-6"></i>
            </div>
            <h3>AI Processing Service</h3>
            <p class="text-secondary mb-3">
                Machine learning model integration, natural language processing ve automated content generation. 
                Token management, rate limiting ve quality assurance.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• ML model integration</li>
                    <li>• Token-based usage control</li>
                    <li>• Quality assurance algorithms</li>
                    <li>• Performance optimization</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Database Design -->
<section id="database" class="section">
    <h2 class="section-title text-center">Veritabanı Tasarımı</h2>
    <p class="section-subtitle text-center">
        Multi-tenant database architecture ile data isolation ve performance optimization
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="database" class="w-6 h-6"></i>
            </div>
            <h3>Database Per Tenant Strategy</h3>
            <p class="text-secondary mb-3">
                Her tenant için ayrı database ile maksimum data isolation. 
                Security, compliance ve data sovereignty requirements'ları karşılar.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Complete data isolation</li>
                    <li>• Independent schema evolution</li>
                    <li>• Tenant-specific optimizations</li>
                    <li>• Backup ve restore isolation</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="link" class="w-6 h-6"></i>
            </div>
            <h3>Central Management Database</h3>
            <p class="text-secondary mb-3">
                Tenant metadata, system configurations ve cross-tenant analytics için 
                merkezi database. Tenant discovery ve routing için kritik.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Tenant registry ve metadata</li>
                    <li>• System-wide configurations</li>
                    <li>• Cross-tenant analytics</li>
                    <li>• Health monitoring data</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="activity" class="w-6 h-6"></i>
            </div>
            <h3>Query Optimization Strategy</h3>
            <p class="text-secondary mb-3">
                Database performance için indexing strategies, query optimization ve 
                connection pooling. N+1 problem prevention ve eager loading patterns.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Strategic index placement</li>
                    <li>• Query performance monitoring</li>
                    <li>• Connection pool management</li>
                    <li>• Lazy loading optimization</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="shield-check" class="w-6 h-6"></i>
            </div>
            <h3>Data Security & Compliance</h3>
            <p class="text-secondary mb-3">
                GDPR compliance, data encryption at rest ve in transit, 
                audit logging ve data retention policies implementation.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Encryption at rest ve transit</li>
                    <li>• GDPR compliance tools</li>
                    <li>• Comprehensive audit logging</li>
                    <li>• Automated data retention</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Scalability -->
<section id="scalability" class="section">
    <h2 class="section-title text-center">Ölçeklenebilirlik</h2>
    <p class="section-subtitle text-center">
        Horizontal ve vertical scaling strategies ile growing business needs'leri karşılar
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="trending-up" class="w-6 h-6"></i>
            </div>
            <h3>Horizontal Scaling</h3>
            <p class="text-secondary mb-3">
                Load balancer arkasında multiple application instances. 
                Database read replicas ve caching layers ile read performance optimization.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Multi-instance deployment</li>
                    <li>• Load balancer configuration</li>
                    <li>• Database read replicas</li>
                    <li>• Session store externalization</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="server" class="w-6 h-6"></i>
            </div>
            <h3>Caching Strategy</h3>
            <p class="text-secondary mb-3">
                Multi-level caching ile performance optimization. Redis ile distributed caching, 
                application-level cache ve CDN integration.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Redis distributed caching</li>
                    <li>• Application memory cache</li>
                    <li>• HTTP response caching</li>
                    <li>• CDN asset delivery</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="workflow" class="w-6 h-6"></i>
            </div>
            <h3>Queue System</h3>
            <p class="text-secondary mb-3">
                Asynchronous job processing ile user experience optimization. 
                Background tasks, email sending ve heavy computational work için queue system.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Background job processing</li>
                    <li>• Email queue management</li>
                    <li>• Heavy computation offloading</li>
                    <li>• Failed job retry mechanisms</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="monitor" class="w-6 h-6"></i>
            </div>
            <h3>Performance Monitoring</h3>
            <p class="text-secondary mb-3">
                Real-time application monitoring, performance metrics collection ve 
                automated alerting systems. Proactive performance issue detection.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Real-time performance metrics</li>
                    <li>• Automated alerting system</li>
                    <li>• Database query monitoring</li>
                    <li>• Resource usage tracking</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Deployment Architecture -->
<section id="deployment" class="section">
    <h2 class="section-title text-center">Deployment Mimarisi</h2>
    <p class="section-subtitle text-center">
        Modern DevOps practices ile automated deployment ve infrastructure management
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="box" class="w-6 h-6"></i>
            </div>
            <h3>Containerization Strategy</h3>
            <p class="text-secondary mb-3">
                Docker containers ile consistent deployment environments. 
                Multi-stage builds, image optimization ve security scanning integration.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Docker multi-stage builds</li>
                    <li>• Container image optimization</li>
                    <li>• Security vulnerability scanning</li>
                    <li>• Environment consistency</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="git-merge" class="w-6 h-6"></i>
            </div>
            <h3>CI/CD Pipeline</h3>
            <p class="text-secondary mb-3">
                Automated testing, building ve deployment pipeline. 
                Code quality checks, security scans ve automated rollback capabilities.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Automated testing pipeline</li>
                    <li>• Code quality enforcement</li>
                    <li>• Security scan integration</li>
                    <li>• Zero-downtime deployment</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="cloud" class="w-6 h-6"></i>
            </div>
            <h3>Cloud Infrastructure</h3>
            <p class="text-secondary mb-3">
                Cloud-native deployment ile auto-scaling, managed services ve 
                high availability. Infrastructure as Code ile reproducible deployments.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Auto-scaling configuration</li>
                    <li>• Managed database services</li>
                    <li>• Load balancer setup</li>
                    <li>• Infrastructure as Code</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="shield" class="w-6 h-6"></i>
            </div>
            <h3>Security & Monitoring</h3>
            <p class="text-secondary mb-3">
                Production environment security hardening, monitoring ve logging. 
                Intrusion detection, vulnerability management ve incident response.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Security hardening protocols</li>
                    <li>• Comprehensive logging system</li>
                    <li>• Intrusion detection systems</li>
                    <li>• Incident response automation</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>