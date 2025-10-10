<?php
$page_title = "Teknoloji Stack - Türk Bilişim Enterprise CMS";
$page_subtitle = "Technology Architecture Overview";
$page_badge = "💻 Teknoloji Stack";

// Navigation sections
$nav_sections = [
    'hero' => 'Giriş',
    'overview' => 'Genel Bakış',
    'backend' => 'Backend Teknolojileri',
    'frontend' => 'Frontend Teknolojileri',
    'database' => 'Veritabanı Teknolojileri',
    'infrastructure' => 'Altyapı Teknolojileri',
    'devops' => 'DevOps & Deployment',
    'monitoring' => 'Monitoring & Analytics'
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
                Modern<br>
                <span style="font-weight: 700; background: linear-gradient(45deg, #64b5f6, #4facfe, #64b5f6); background-size: 200% 200%; animation: textGradient 3s ease infinite; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Teknoloji Stack</span>
            </h1>
            <p style="margin-bottom: 3rem; font-size: 1.2rem; line-height: 1.6; color: rgba(255, 255, 255, 0.8); max-width: 600px; margin-left: auto; margin-right: auto; font-weight: 400; text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);">
                Enterprise-grade teknolojiler ile<br>
                <span style="color: #64b5f6; font-weight: 600;">cutting-edge development stack</span>
            </p>
        </div>
    </div>
</section>

<!-- Overview Section -->
<section id="overview" class="section">
    <h2 class="section-title">Teknoloji Stack Genel Bakış</h2>
    <p class="section-subtitle">
        Türk Bilişim Enterprise CMS, industry-leading technologies <span class="text-muted">(sektör lideri teknolojiler)</span> 
        ile modern software development best practices <span class="text-muted">(modern yazılım geliştirme en iyi uygulamaları)</span> 
        birleştiren comprehensive technology stack <span class="text-muted">(kapsamlı teknoloji yığını)</span> kullanır. 
        Her teknoloji performance, security ve scalability <span class="text-muted">(performans, güvenlik ve ölçeklenebilirlik)</span> 
        kriterleri göz önünde bulundurularak carefully selected <span class="text-muted">(dikkatli bir şekilde seçilmiştir)</span>.
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="server"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Backend Technologies <span class="text-sm text-muted">(backend teknolojileri)</span></h3>
            <p class="text-secondary mb-4">
                PHP 8.2+ ile modern object-oriented programming <span class="text-muted">(nesne yönelimli programlama)</span>, 
                Laravel 11 framework'ün powerful features'leri ve enterprise-grade architecture patterns 
                <span class="text-muted">(kurumsal seviye mimari kalıpları)</span>. Robust API development 
                <span class="text-muted">(güçlü API geliştirme)</span> ve microservices-ready design 
                <span class="text-muted">(mikroservis hazır tasarım)</span> ile future-proof development sağlar.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">PHP 8.2+</span> <span class="text-muted">(en güncel PHP sürümü)</span><br><span class="text-sm text-secondary">→ Modern syntax, performance improvements ve type safety</span></li>
                        <li>• <span class="tech-highlight">Laravel 11</span> <span class="text-muted">(enterprise PHP framework)</span><br><span class="text-sm text-secondary">→ Eloquent ORM, Artisan CLI ve comprehensive ecosystem</span></li>
                        <li>• <span class="tech-highlight">RESTful API Design</span> <span class="text-muted">(REST API tasarımı)</span><br><span class="text-sm text-secondary">→ Standardized endpoints, proper HTTP methods ve status codes</span></li>
                        <li>• <span class="tech-highlight">Event-Driven Architecture</span> <span class="text-muted">(olay güdümlü mimari)</span><br><span class="text-sm text-secondary">→ Loose coupling, async processing ve scalable design</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="monitor"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Frontend Technologies <span class="text-sm text-muted">(frontend teknolojileri)</span></h3>
            <p class="text-secondary mb-4">
                Modern reactive UI development için Livewire 3.5 ile server-side reactivity 
                <span class="text-muted">(sunucu tarafı reaktiflik)</span>, Alpine.js ile lightweight client-side interactions 
                <span class="text-muted">(hafif istemci tarafı etkileşimler)</span> ve Tailwind CSS ile utility-first styling 
                <span class="text-muted">(fayda öncelikli stil)</span>. Progressive enhancement 
                <span class="text-muted">(aşamalı geliştirme)</span> approach ile accessibility <span class="text-muted">(erişilebilirlik)</span> garantisi.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Livewire 3.5</span> <span class="text-muted">(server-side reaktif framework)</span><br><span class="text-sm text-secondary">→ Real-time updates, component-based architecture</span></li>
                        <li>• <span class="tech-highlight">Alpine.js</span> <span class="text-muted">(minimal JavaScript framework)</span><br><span class="text-sm text-secondary">→ Declarative syntax, lightweight bundle size</span></li>
                        <li>• <span class="tech-highlight">Tailwind CSS</span> <span class="text-muted">(utility-first CSS framework)</span><br><span class="text-sm text-secondary">→ Rapid prototyping, consistent design system</span></li>
                        <li>• <span class="tech-highlight">Blade Templates</span> <span class="text-muted">(Laravel template motoru)</span><br><span class="text-sm text-secondary">→ Template inheritance, component system, caching</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="database"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Database Technologies <span class="text-sm text-muted">(veritabanı teknolojileri)</span></h3>
            <p class="text-secondary mb-4">
                MySQL 8.0+ ile enterprise-grade relational database management 
                <span class="text-muted">(kurumsal seviye ilişkisel veritabanı yönetimi)</span>, 
                Redis ile high-performance caching <span class="text-muted">(yüksek performanslı önbellekleme)</span> ve 
                session management <span class="text-muted">(oturum yönetimi)</span>. Advanced indexing strategies 
                <span class="text-muted">(gelişmiş indeksleme stratejileri)</span> ve query optimization 
                <span class="text-muted">(sorgu optimizasyonu)</span> ile performance maximization sağlar.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">MySQL 8.0+</span> <span class="text-muted">(modern ilişkisel veritabanı)</span><br><span class="text-sm text-secondary">→ JSON support, window functions, CTE support</span></li>
                        <li>• <span class="tech-highlight">Redis</span> <span class="text-muted">(in-memory veri yapısı deposu)</span><br><span class="text-sm text-secondary">→ Caching, session storage, pub/sub messaging</span></li>
                        <li>• <span class="tech-highlight">Eloquent ORM</span> <span class="text-muted">(nesne-ilişkisel eşleme)</span><br><span class="text-sm text-secondary">→ Active Record pattern, relationship management</span></li>
                        <li>• <span class="tech-highlight">Database Migrations</span> <span class="text-muted">(veritabanı sürüm kontrolü)</span><br><span class="text-sm text-secondary">→ Schema versioning, rollback capabilities</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="cloud"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Infrastructure Technologies <span class="text-sm text-muted">(altyapı teknolojileri)</span></h3>
            <p class="text-secondary mb-4">
                Cloud-native deployment <span class="text-muted">(bulut doğal dağıtım)</span> için 
                Docker containerization <span class="text-muted">(konteynerleştirme)</span>, 
                Kubernetes orchestration <span class="text-muted">(orkestrasyon)</span> ve 
                CI/CD pipelines <span class="text-muted">(sürekli entegrasyon/dağıtım hatları)</span>. 
                Auto-scaling <span class="text-muted">(otomatik ölçekleme)</span>, load balancing 
                <span class="text-muted">(yük dengeleme)</span> ve infrastructure as code 
                <span class="text-muted">(kod olarak altyapı)</span> approach ile modern DevOps practices.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Docker</span> <span class="text-muted">(konteynerleştirme platformu)</span><br><span class="text-sm text-secondary">→ Application packaging, environment consistency</span></li>
                        <li>• <span class="tech-highlight">Nginx</span> <span class="text-muted">(web sunucu ve reverse proxy)</span><br><span class="text-sm text-secondary">→ High performance, load balancing, SSL termination</span></li>
                        <li>• <span class="tech-highlight">Supervisor</span> <span class="text-muted">(süreç yöneticisi)</span><br><span class="text-sm text-secondary">→ Queue worker management, process monitoring</span></li>
                        <li>• <span class="tech-highlight">CDN Integration</span> <span class="text-muted">(içerik dağıtım ağı)</span><br><span class="text-sm text-secondary">→ Global asset delivery, performance optimization</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Backend Technologies -->
<section id="backend" class="section">
    <h2 class="section-title text-center">Backend Teknoloji Detayları</h2>
    <p class="section-subtitle text-center">
        Robust server-side development için carefully selected backend technologies
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="code" class="w-6 h-6"></i>
            </div>
            <h3>PHP 8.2+ Modern Features</h3>
            <p class="text-secondary mb-3">
                En güncel PHP sürümü ile type declarations, attributes, match expressions ve 
                performance improvements. Modern object-oriented programming patterns ile clean code.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Union ve intersection types</li>
                    <li>• Named arguments support</li>
                    <li>• Match expression syntax</li>
                    <li>• Constructor property promotion</li>
                    <li>• JIT compilation support</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="layers" class="w-6 h-6"></i>
            </div>
            <h3>Laravel 11 Framework Features</h3>
            <p class="text-secondary mb-3">
                Enterprise-grade PHP framework ile comprehensive feature set. 
                Eloquent ORM, Artisan CLI, middleware system ve extensive ecosystem.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Eloquent ORM ve Query Builder</li>
                    <li>• Artisan command-line interface</li>
                    <li>• Middleware request filtering</li>
                    <li>• Service container dependency injection</li>
                    <li>• Event broadcasting system</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="api" class="w-6 h-6"></i>
            </div>
            <h3>API Development Stack</h3>
            <p class="text-secondary mb-3">
                RESTful API design principles ile standardized endpoints. 
                API versioning, rate limiting ve comprehensive documentation support.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• RESTful resource controllers</li>
                    <li>• API versioning strategies</li>
                    <li>• Rate limiting middleware</li>
                    <li>• OpenAPI documentation</li>
                    <li>• JWT authentication</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="shield" class="w-6 h-6"></i>
            </div>
            <h3>Security Implementation</h3>
            <p class="text-secondary mb-3">
                Enterprise-grade security features ile data protection. 
                Authentication, authorization, encryption ve audit logging systems.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Multi-factor authentication</li>
                    <li>• Role-based access control</li>
                    <li>• Data encryption at rest</li>
                    <li>• CSRF protection</li>
                    <li>• SQL injection prevention</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Frontend Technologies -->
<section id="frontend" class="section">
    <h2 class="section-title text-center">Frontend Teknoloji Detayları</h2>
    <p class="section-subtitle text-center">
        Modern user experience için reactive ve performant frontend technologies
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="zap" class="w-6 h-6"></i>
            </div>
            <h3>Livewire 3.5 Reactive Components</h3>
            <p class="text-secondary mb-3">
                Server-side reactivity ile SPA-like experience. Component-based architecture, 
                real-time updates ve automatic state synchronization.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Component lifecycle management</li>
                    <li>• Real-time form validation</li>
                    <li>• File upload handling</li>
                    <li>• Event-driven interactions</li>
                    <li>• Automatic DOM updates</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="mountain" class="w-6 h-6"></i>
            </div>
            <h3>Alpine.js Minimal Framework</h3>
            <p class="text-secondary mb-3">
                Lightweight JavaScript framework ile declarative reactive behavior. 
                Vue.js-like syntax ile minimal bundle size ve easy learning curve.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Declarative reactive syntax</li>
                    <li>• Minimal JavaScript footprint</li>
                    <li>• Easy DOM manipulation</li>
                    <li>• Event handling system</li>
                    <li>• State management utilities</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="paintbrush" class="w-6 h-6"></i>
            </div>
            <h3>Tailwind CSS Utility Framework</h3>
            <p class="text-secondary mb-3">
                Utility-first CSS framework ile rapid prototyping. 
                Consistent design system, responsive design ve dark mode support.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Utility-first CSS approach</li>
                    <li>• Responsive design utilities</li>
                    <li>• Dark mode support</li>
                    <li>• Custom design tokens</li>
                    <li>• PurgeCSS optimization</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="layout" class="w-6 h-6"></i>
            </div>
            <h3>Blade Template Engine</h3>
            <p class="text-secondary mb-3">
                Laravel'in powerful template engine'i ile clean view layer. 
                Template inheritance, component system ve caching optimization.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Template inheritance system</li>
                    <li>• Reusable view components</li>
                    <li>• Conditional rendering</li>
                    <li>• Loop ve iteration helpers</li>
                    <li>• View caching optimization</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Database Technologies -->
<section id="database" class="section">
    <h2 class="section-title text-center">Veritabanı Teknoloji Detayları</h2>
    <p class="section-subtitle text-center">
        Enterprise-grade data management ile scalable database solutions
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="database" class="w-6 h-6"></i>
            </div>
            <h3>MySQL 8.0+ Advanced Features</h3>
            <p class="text-secondary mb-3">
                Modern relational database features ile JSON support, window functions, 
                ve CTE support. Performance optimization ve data integrity.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• JSON data type support</li>
                    <li>• Window functions ve CTEs</li>
                    <li>• Advanced indexing strategies</li>
                    <li>• Partitioning support</li>
                    <li>• Performance schema monitoring</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="zap" class="w-6 h-6"></i>
            </div>
            <h3>Redis In-Memory Storage</h3>
            <p class="text-secondary mb-3">
                High-performance caching, session storage ve pub/sub messaging. 
                Data structure support ve cluster configuration capabilities.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• In-memory data caching</li>
                    <li>• Session storage management</li>
                    <li>• Pub/sub messaging system</li>
                    <li>• Data expiration policies</li>
                    <li>• Redis cluster support</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="link" class="w-6 h-6"></i>
            </div>
            <h3>Eloquent ORM Advanced Features</h3>
            <p class="text-secondary mb-3">
                Active Record pattern implementation ile elegant database interactions. 
                Relationship management, query optimization ve model events.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Eloquent relationship management</li>
                    <li>• Query scope ve filtering</li>
                    <li>• Model events ve observers</li>
                    <li>• Eager loading optimization</li>
                    <li>• Database transaction support</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="git-branch" class="w-6 h-6"></i>
            </div>
            <h3>Database Migration System</h3>
            <p class="text-secondary mb-3">
                Version control for database schemas ile team collaboration. 
                Schema versioning, rollback capabilities ve seeding automation.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Schema version control</li>
                    <li>• Migration rollback support</li>
                    <li>• Database seeding automation</li>
                    <li>• Multi-environment management</li>
                    <li>• Schema comparison tools</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Infrastructure Technologies -->
<section id="infrastructure" class="section">
    <h2 class="section-title text-center">Altyapı Teknoloji Detayları</h2>
    <p class="section-subtitle text-center">
        Modern deployment ve scaling için cloud-native infrastructure technologies
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="box" class="w-6 h-6"></i>
            </div>
            <h3>Docker Containerization</h3>
            <p class="text-secondary mb-3">
                Application packaging ve environment consistency için Docker containers. 
                Multi-stage builds, image optimization ve security scanning.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Multi-stage Docker builds</li>
                    <li>• Container image optimization</li>
                    <li>• Security vulnerability scanning</li>
                    <li>• Development environment consistency</li>
                    <li>• Container orchestration ready</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="server" class="w-6 h-6"></i>
            </div>
            <h3>Nginx Web Server</h3>
            <p class="text-secondary mb-3">
                High-performance web server ve reverse proxy configuration. 
                Load balancing, SSL termination ve static asset optimization.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• High-performance request handling</li>
                    <li>• Load balancing configuration</li>
                    <li>• SSL/TLS termination</li>
                    <li>• Static asset optimization</li>
                    <li>• Rate limiting ve security headers</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="activity" class="w-6 h-6"></i>
            </div>
            <h3>Process Management</h3>
            <p class="text-secondary mb-3">
                Supervisor ile process monitoring ve queue worker management. 
                Automatic restart, log management ve health checking.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Queue worker management</li>
                    <li>• Process monitoring ve restart</li>
                    <li>• Log rotation ve management</li>
                    <li>• Health check automation</li>
                    <li>• Resource usage monitoring</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="globe" class="w-6 h-6"></i>
            </div>
            <h3>CDN Integration</h3>
            <p class="text-secondary mb-3">
                Global content delivery network integration ile performance optimization. 
                Asset distribution, caching strategies ve edge computing.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Global asset distribution</li>
                    <li>• Edge caching strategies</li>
                    <li>• Image optimization pipeline</li>
                    <li>• Bandwidth optimization</li>
                    <li>• Geographic load balancing</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- DevOps & Deployment -->
<section id="devops" class="section">
    <h2 class="section-title text-center">DevOps & Deployment Teknolojileri</h2>
    <p class="section-subtitle text-center">
        Modern software delivery için automated deployment ve continuous integration
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="git-merge" class="w-6 h-6"></i>
            </div>
            <h3>CI/CD Pipeline Automation</h3>
            <p class="text-secondary mb-3">
                Continuous integration ve deployment automation ile rapid delivery. 
                Automated testing, quality gates ve deployment strategies.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Automated testing pipeline</li>
                    <li>• Code quality enforcement</li>
                    <li>• Security scanning integration</li>
                    <li>• Blue-green deployment</li>
                    <li>• Rollback automation</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="cloud" class="w-6 h-6"></i>
            </div>
            <h3>Cloud Infrastructure Management</h3>
            <p class="text-secondary mb-3">
                Infrastructure as Code ile reproducible deployments. 
                Auto-scaling, managed services ve high availability configuration.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Infrastructure as Code (IaC)</li>
                    <li>• Auto-scaling configuration</li>
                    <li>• Managed database services</li>
                    <li>• Load balancer setup</li>
                    <li>• High availability design</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="shield-check" class="w-6 h-6"></i>
            </div>
            <h3>Security & Compliance Automation</h3>
            <p class="text-secondary mb-3">
                Automated security scanning, compliance checking ve vulnerability management. 
                Security hardening ve incident response automation.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Automated security scanning</li>
                    <li>• Compliance validation</li>
                    <li>• Vulnerability management</li>
                    <li>• Security hardening automation</li>
                    <li>• Incident response workflows</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="backup" class="w-6 h-6"></i>
            </div>
            <h3>Backup & Disaster Recovery</h3>
            <p class="text-secondary mb-3">
                Automated backup strategies, disaster recovery planning ve 
                business continuity automation with RTO/RPO optimization.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Automated backup scheduling</li>
                    <li>• Cross-region replication</li>
                    <li>• Disaster recovery automation</li>
                    <li>• RTO/RPO optimization</li>
                    <li>• Business continuity testing</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Monitoring & Analytics -->
<section id="monitoring" class="section">
    <h2 class="section-title text-center">Monitoring & Analytics Teknolojileri</h2>
    <p class="section-subtitle text-center">
        System observability ve performance optimization için comprehensive monitoring stack
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="activity" class="w-6 h-6"></i>
            </div>
            <h3>Application Performance Monitoring</h3>
            <p class="text-secondary mb-3">
                Real-time application performance tracking ile bottleneck detection. 
                Response time monitoring, error tracking ve user experience analytics.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Real-time performance metrics</li>
                    <li>• Error tracking ve alerting</li>
                    <li>• User experience monitoring</li>
                    <li>• Database query analysis</li>
                    <li>• API performance tracking</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="bar-chart" class="w-6 h-6"></i>
            </div>
            <h3>Business Intelligence Analytics</h3>
            <p class="text-secondary mb-3">
                Comprehensive business analytics ile data-driven decision making. 
                Custom dashboards, KPI tracking ve predictive analytics.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Custom dashboard creation</li>
                    <li>• KPI tracking automation</li>
                    <li>• Predictive analytics</li>
                    <li>• Data visualization tools</li>
                    <li>• Automated reporting system</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="search" class="w-6 h-6"></i>
            </div>
            <h3>Log Management & Analysis</h3>
            <p class="text-secondary mb-3">
                Centralized log collection, analysis ve correlation. 
                Log aggregation, search capabilities ve anomaly detection.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Centralized log collection</li>
                    <li>• Log search ve filtering</li>
                    <li>• Anomaly detection algorithms</li>
                    <li>• Log correlation analysis</li>
                    <li>• Retention policy management</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="bell" class="w-6 h-6"></i>
            </div>
            <h3>Alerting & Notification System</h3>
            <p class="text-secondary mb-3">
                Intelligent alerting system ile proactive issue detection. 
                Multi-channel notifications, escalation policies ve incident management.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Intelligent alert routing</li>
                    <li>• Multi-channel notifications</li>
                    <li>• Escalation policy management</li>
                    <li>• Incident tracking system</li>
                    <li>• Alert fatigue prevention</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>