<?php
$page_title = "Sistem Özellikleri - Türk Bilişim Enterprise CMS";
$page_subtitle = "Advanced Features Overview";
$page_badge = "⭐ Sistem Özellikleri";

// Navigation sections
$nav_sections = [
    'hero' => 'Giriş',
    'overview' => 'Genel Bakış',
    'content' => 'İçerik Yönetimi',
    'automation' => 'Otomasyon',
    'integration' => 'Entegrasyon',
    'developer' => 'Geliştirici Araçları',
    'enterprise' => 'Kurumsal Özellikler',
    'customization' => 'Özelleştirme'
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
                Enterprise CMS<br>
                <span style="font-weight: 700; background: linear-gradient(45deg, #64b5f6, #4facfe, #64b5f6); background-size: 200% 200%; animation: textGradient 3s ease infinite; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Özellikleri</span>
            </h1>
            <p style="margin-bottom: 3rem; font-size: 1.2rem; line-height: 1.6; color: rgba(255, 255, 255, 0.8); max-width: 600px; margin-left: auto; margin-right: auto; font-weight: 400; text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);">
                Modern enterprise ihtiyaçları için<br>
                <span style="color: #64b5f6; font-weight: 600;">kapsamlı özellik seti</span>
            </p>
        </div>
    </div>
</section>

<!-- Overview Section -->
<section id="overview" class="section">
    <h2 class="section-title">Özellikler Genel Bakış</h2>
    <p class="section-subtitle">
        Türk Bilişim Enterprise CMS, modern işletmelerin digital transformation 
        <span class="text-muted">(dijital dönüşüm)</span> süreçlerini destekleyen 
        comprehensive feature set <span class="text-muted">(kapsamlı özellik seti)</span> ile donatılmıştır. 
        Her özellik enterprise-grade requirements <span class="text-muted">(kurumsal seviye gereksinimler)</span> 
        göz önünde bulundurularak tasarlanmış ve real-world business scenarios 
        <span class="text-muted">(gerçek iş senaryoları)</span> için optimize edilmiştir.
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="star"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Core Features <span class="text-sm text-muted">(temel özellikler)</span></h3>
            <p class="text-secondary mb-4">
                Sistem temel işlevselliği content management <span class="text-muted">(içerik yönetimi)</span>, 
                user management <span class="text-muted">(kullanıcı yönetimi)</span>, 
                multi-tenant architecture <span class="text-muted">(çok kiracılı mimari)</span> ve 
                advanced security features <span class="text-muted">(gelişmiş güvenlik özellikleri)</span> 
                üzerine kurulmuştur. Bu temel özelliklerin her biri enterprise scalability 
                <span class="text-muted">(kurumsal ölçeklenebilirlik)</span> için tasarlanmıştır.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Dynamic Content Management</span> <span class="text-muted">(dinamik içerik yönetimi)</span><br><span class="text-sm text-secondary">→ WYSIWYG editor, media library ve content scheduling</span></li>
                        <li>• <span class="tech-highlight">Multi-Tenant Isolation</span> <span class="text-muted">(çok kiracılı izolasyon)</span><br><span class="text-sm text-secondary">→ Complete data separation ve tenant-specific configurations</span></li>
                        <li>• <span class="tech-highlight">Role-Based Access Control</span> <span class="text-muted">(rol tabanlı erişim kontrolü)</span><br><span class="text-sm text-secondary">→ Granular permissions ve hierarchical role management</span></li>
                        <li>• <span class="tech-highlight">Enterprise Security</span> <span class="text-muted">(kurumsal güvenlik)</span><br><span class="text-sm text-secondary">→ Two-factor auth, audit logging ve compliance tools</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="zap"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Performance Features <span class="text-sm text-muted">(performans özellikleri)</span></h3>
            <p class="text-secondary mb-4">
                High-performance computing <span class="text-muted">(yüksek performanslı hesaplama)</span> 
                prensipleriyle tasarlanmış caching strategies <span class="text-muted">(önbellekleme stratejileri)</span>, 
                database optimization <span class="text-muted">(veritabanı optimizasyonu)</span> ve 
                content delivery mechanisms <span class="text-muted">(içerik dağıtım mekanizmaları)</span>. 
                Load balancing <span class="text-muted">(yük dengeleme)</span> ve horizontal scaling 
                <span class="text-muted">(yatay ölçekleme)</span> desteği ile enterprise traffic'i handle eder.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Multi-Layer Caching</span> <span class="text-muted">(çok katmanlı önbellekleme)</span><br><span class="text-sm text-secondary">→ Redis, application cache ve CDN integration</span></li>
                        <li>• <span class="tech-highlight">Database Optimization</span> <span class="text-muted">(veritabanı optimizasyonu)</span><br><span class="text-sm text-secondary">→ Query optimization, indexing strategies ve connection pooling</span></li>
                        <li>• <span class="tech-highlight">Asset Optimization</span> <span class="text-muted">(varlık optimizasyonu)</span><br><span class="text-sm text-secondary">→ Image compression, lazy loading ve minification</span></li>
                        <li>• <span class="tech-highlight">Performance Monitoring</span> <span class="text-muted">(performans izleme)</span><br><span class="text-sm text-secondary">→ Real-time metrics, bottleneck detection ve auto-scaling</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="brain"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">AI-Powered Features <span class="text-sm text-muted">(yapay zeka destekli özellikler)</span></h3>
            <p class="text-secondary mb-4">
                Machine Learning algorithms <span class="text-muted">(makine öğrenmesi algoritmaları)</span> 
                ile intelligent content generation <span class="text-muted">(akıllı içerik üretimi)</span>, 
                automated SEO optimization <span class="text-muted">(otomatik SEO optimizasyonu)</span> ve 
                smart analytics <span class="text-muted">(akıllı analitik)</span>. Natural Language Processing 
                <span class="text-muted">(doğal dil işleme)</span> teknolojileri ile content quality assurance 
                <span class="text-muted">(içerik kalite güvencesi)</span> sağlar.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Content Generation</span> <span class="text-muted">(içerik üretimi)</span><br><span class="text-sm text-secondary">→ AI-powered writing assistance ve automated content creation</span></li>
                        <li>• <span class="tech-highlight">Smart SEO</span> <span class="text-muted">(akıllı SEO)</span><br><span class="text-sm text-secondary">→ Keyword optimization, meta generation ve content analysis</span></li>
                        <li>• <span class="tech-highlight">Intelligent Analytics</span> <span class="text-muted">(akıllı analitik)</span><br><span class="text-sm text-secondary">→ Behavior prediction, trend analysis ve recommendation engine</span></li>
                        <li>• <span class="tech-highlight">Quality Assurance</span> <span class="text-muted">(kalite güvencesi)</span><br><span class="text-sm text-secondary">→ Content validation, grammar checking ve brand consistency</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="puzzle"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Extensibility Features <span class="text-sm text-muted">(genişletilebilirlik özellikleri)</span></h3>
            <p class="text-secondary mb-4">
                Modular architecture <span class="text-muted">(modüler mimari)</span> ile unlimited customization 
                <span class="text-muted">(sınırsız özelleştirme)</span> imkanı. Plugin system 
                <span class="text-muted">(eklenti sistemi)</span>, API extensibility <span class="text-muted">(API genişletilebilirliği)</span> 
                ve third-party integrations <span class="text-muted">(üçüncü taraf entegrasyonları)</span> ile 
                business requirements <span class="text-muted">(iş gereksinimleri)</span> için özelleştirme yapılabilir.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Module System</span> <span class="text-muted">(modül sistemi)</span><br><span class="text-sm text-secondary">→ Hot-swappable modules, dependency management</span></li>
                        <li>• <span class="tech-highlight">API Framework</span> <span class="text-muted">(API çerçevesi)</span><br><span class="text-sm text-secondary">→ RESTful APIs, GraphQL support ve webhook system</span></li>
                        <li>• <span class="tech-highlight">Theme Engine</span> <span class="text-muted">(tema motoru)</span><br><span class="text-sm text-secondary">→ Custom themes, template inheritance ve design tokens</span></li>
                        <li>• <span class="tech-highlight">Integration Hub</span> <span class="text-muted">(entegrasyon merkezi)</span><br><span class="text-sm text-secondary">→ Pre-built connectors, custom integrations ve data sync</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Content Management -->
<section id="content" class="section">
    <h2 class="section-title text-center">İçerik Yönetimi Özellikleri</h2>
    <p class="section-subtitle text-center">
        Professional content creation, management ve publishing workflows
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="edit" class="w-6 h-6"></i>
            </div>
            <h3>Advanced Content Editor</h3>
            <p class="text-secondary mb-3">
                WYSIWYG editor ile professional content creation. Rich text formatting, 
                image management, code syntax highlighting ve collaborative editing capabilities.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Rich text WYSIWYG editing</li>
                    <li>• Markdown support ve preview</li>
                    <li>• Collaborative real-time editing</li>
                    <li>• Media library integration</li>
                    <li>• Code syntax highlighting</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="calendar" class="w-6 h-6"></i>
            </div>
            <h3>Content Scheduling & Workflow</h3>
            <p class="text-secondary mb-3">
                Editorial workflow ile content approval process. Scheduled publishing, 
                content versioning ve automated content lifecycle management.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Editorial workflow system</li>
                    <li>• Scheduled publishing</li>
                    <li>• Content versioning ve history</li>
                    <li>• Approval process automation</li>
                    <li>• Content archiving policies</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="globe" class="w-6 h-6"></i>
            </div>
            <h3>Multi-Language Support</h3>
            <p class="text-secondary mb-3">
                Comprehensive internationalization features. Content translation management, 
                locale-specific customizations ve right-to-left language support.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Multi-language content management</li>
                    <li>• Translation workflow system</li>
                    <li>• Locale-specific configurations</li>
                    <li>• RTL language support</li>
                    <li>• Automated translation integration</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="search" class="w-6 h-6"></i>
            </div>
            <h3>Advanced Search & SEO</h3>
            <p class="text-secondary mb-3">
                Elasticsearch-powered search capabilities. SEO optimization tools, 
                meta management ve search engine ranking improvements.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Full-text search capabilities</li>
                    <li>• SEO optimization tools</li>
                    <li>• Meta tag management</li>
                    <li>• Search analytics</li>
                    <li>• Sitemap generation</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Automation Features -->
<section id="automation" class="section">
    <h2 class="section-title text-center">Otomasyon Özellikleri</h2>
    <p class="section-subtitle text-center">
        Workflow automation ile efficiency artırımı ve manual task reduction
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="bot" class="w-6 h-6"></i>
            </div>
            <h3>Workflow Automation</h3>
            <p class="text-secondary mb-3">
                Visual workflow builder ile custom automation creation. 
                Trigger-based actions, conditional logic ve multi-step processes.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Visual workflow designer</li>
                    <li>• Event-driven automation</li>
                    <li>• Conditional logic support</li>
                    <li>• Multi-step process chains</li>
                    <li>• Error handling ve retry logic</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="mail" class="w-6 h-6"></i>
            </div>
            <h3>Email Marketing Automation</h3>
            <p class="text-secondary mb-3">
                Automated email campaigns, drip sequences ve personalized content delivery. 
                Segmentation, A/B testing ve performance analytics.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Automated email campaigns</li>
                    <li>• Drip sequence management</li>
                    <li>• User segmentation tools</li>
                    <li>• A/B testing framework</li>
                    <li>• Campaign performance analytics</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="database" class="w-6 h-6"></i>
            </div>
            <h3>Data Processing Automation</h3>
            <p class="text-secondary mb-3">
                Automated data import/export, transformation pipelines ve 
                scheduled data synchronization with external systems.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Automated data import/export</li>
                    <li>• Data transformation pipelines</li>
                    <li>• Scheduled synchronization</li>
                    <li>• Data validation rules</li>
                    <li>• Error notification system</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="backup" class="w-6 h-6"></i>
            </div>
            <h3>Backup & Maintenance Automation</h3>
            <p class="text-secondary mb-3">
                Automated backup scheduling, system maintenance tasks ve 
                proactive health monitoring with self-healing capabilities.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Automated backup scheduling</li>
                    <li>• System maintenance automation</li>
                    <li>• Health monitoring alerts</li>
                    <li>• Self-healing mechanisms</li>
                    <li>• Disaster recovery automation</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Integration Features -->
<section id="integration" class="section">
    <h2 class="section-title text-center">Entegrasyon Özellikleri</h2>
    <p class="section-subtitle text-center">
        Third-party systems ile seamless connectivity ve data synchronization
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="plug" class="w-6 h-6"></i>
            </div>
            <h3>API Integration Hub</h3>
            <p class="text-secondary mb-3">
                RESTful ve GraphQL APIs ile external systems integration. 
                Pre-built connectors, custom API endpoints ve webhook management.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• RESTful API framework</li>
                    <li>• GraphQL query interface</li>
                    <li>• Pre-built API connectors</li>
                    <li>• Webhook management system</li>
                    <li>• API rate limiting ve security</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="credit-card" class="w-6 h-6"></i>
            </div>
            <h3>Payment Gateway Integration</h3>
            <p class="text-secondary mb-3">
                Multiple payment processors support. Secure transaction handling, 
                subscription management ve automated billing systems.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Multiple payment gateways</li>
                    <li>• Secure transaction processing</li>
                    <li>• Subscription billing automation</li>
                    <li>• Invoice generation system</li>
                    <li>• Payment analytics ve reporting</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="share-2" class="w-6 h-6"></i>
            </div>
            <h3>Social Media Integration</h3>
            <p class="text-secondary mb-3">
                Social platforms ile automated content sharing. 
                Social login, feed integration ve social analytics tracking.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Social media auto-posting</li>
                    <li>• Social login integration</li>
                    <li>• Feed aggregation system</li>
                    <li>• Social analytics tracking</li>
                    <li>• Cross-platform content sync</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="cloud" class="w-6 h-6"></i>
            </div>
            <h3>Cloud Services Integration</h3>
            <p class="text-secondary mb-3">
                AWS, Google Cloud ve Azure services integration. 
                Cloud storage, CDN, messaging services ve serverless functions.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Multi-cloud support</li>
                    <li>• Cloud storage integration</li>
                    <li>• CDN configuration</li>
                    <li>• Serverless function deployment</li>
                    <li>• Cloud monitoring integration</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Developer Tools -->
<section id="developer" class="section">
    <h2 class="section-title text-center">Geliştirici Araçları</h2>
    <p class="section-subtitle text-center">
        Development efficiency için powerful tools ve debugging capabilities
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="code" class="w-6 h-6"></i>
            </div>
            <h3>Advanced Development Environment</h3>
            <p class="text-secondary mb-3">
                Integrated development tools, real-time debugging ve 
                performance profiling capabilities with comprehensive logging.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Real-time debugging tools</li>
                    <li>• Performance profiling</li>
                    <li>• Comprehensive logging system</li>
                    <li>• Code quality metrics</li>
                    <li>• Automated testing framework</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="terminal" class="w-6 h-6"></i>
            </div>
            <h3>CLI Tools & Commands</h3>
            <p class="text-secondary mb-3">
                Powerful command-line interface ile development automation. 
                Custom commands, scaffolding tools ve deployment automation.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Custom Artisan commands</li>
                    <li>• Code scaffolding tools</li>
                    <li>• Migration management</li>
                    <li>• Seeding automation</li>
                    <li>• Deployment scripts</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="git-branch" class="w-6 h-6"></i>
            </div>
            <h3>Version Control Integration</h3>
            <p class="text-secondary mb-3">
                Git integration ile automated workflows. Branch management, 
                conflict resolution ve continuous integration support.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Git workflow automation</li>
                    <li>• Branch management tools</li>
                    <li>• Conflict resolution helpers</li>
                    <li>• CI/CD pipeline integration</li>
                    <li>• Code review automation</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="package" class="w-6 h-6"></i>
            </div>
            <h3>Package Management</h3>
            <p class="text-secondary mb-3">
                Composer ve NPM integration ile dependency management. 
                Package versioning, security scanning ve automated updates.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Dependency management</li>
                    <li>• Security vulnerability scanning</li>
                    <li>• Automated package updates</li>
                    <li>• Package conflict resolution</li>
                    <li>• Custom package repository</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Enterprise Features -->
<section id="enterprise" class="section">
    <h2 class="section-title text-center">Kurumsal Özellikler</h2>
    <p class="section-subtitle text-center">
        Enterprise-grade capabilities ile business continuity ve compliance
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="shield-check" class="w-6 h-6"></i>
            </div>
            <h3>Enterprise Security & Compliance</h3>
            <p class="text-secondary mb-3">
                GDPR, HIPAA compliance tools ile enterprise security standards. 
                Audit logging, data encryption ve access control mechanisms.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• GDPR compliance tools</li>
                    <li>• Comprehensive audit logging</li>
                    <li>• Data encryption at rest/transit</li>
                    <li>• Access control matrices</li>
                    <li>• Security incident response</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
            <h3>Team Collaboration Features</h3>
            <p class="text-secondary mb-3">
                Advanced team management, project collaboration tools ve 
                real-time communication features with workflow integration.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Team workspace management</li>
                    <li>• Real-time collaboration</li>
                    <li>• Project management integration</li>
                    <li>• Communication channels</li>
                    <li>• Task assignment automation</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="bar-chart" class="w-6 h-6"></i>
            </div>
            <h3>Advanced Analytics & Reporting</h3>
            <p class="text-secondary mb-3">
                Business intelligence tools, custom dashboards ve 
                automated reporting with data visualization capabilities.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Business intelligence dashboard</li>
                    <li>• Custom report generation</li>
                    <li>• Data visualization tools</li>
                    <li>• Performance metrics tracking</li>
                    <li>• Automated report scheduling</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="server" class="w-6 h-6"></i>
            </div>
            <h3>High Availability & Disaster Recovery</h3>
            <p class="text-secondary mb-3">
                Business continuity planning, automated failover systems ve 
                comprehensive backup strategies with recovery point objectives.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Automated failover systems</li>
                    <li>• Multi-region deployment</li>
                    <li>• Backup ve recovery automation</li>
                    <li>• Business continuity planning</li>
                    <li>• Service level monitoring</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Customization Features -->
<section id="customization" class="section">
    <h2 class="section-title text-center">Özelleştirme Seçenekleri</h2>
    <p class="section-subtitle text-center">
        Unlimited customization possibilities ile unique business requirements
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="palette" class="w-6 h-6"></i>
            </div>
            <h3>Theme Customization Engine</h3>
            <p class="text-secondary mb-3">
                Visual theme builder, custom CSS/JS injection ve 
                responsive design templates with brand consistency tools.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Visual theme builder</li>
                    <li>• Custom CSS/JS injection</li>
                    <li>• Responsive design templates</li>
                    <li>• Brand consistency tools</li>
                    <li>• Theme inheritance system</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="sliders" class="w-6 h-6"></i>
            </div>
            <h3>Advanced Configuration Management</h3>
            <p class="text-secondary mb-3">
                Environment-specific configurations, feature flags ve 
                runtime configuration changes without system downtime.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Environment-specific configs</li>
                    <li>• Feature flag management</li>
                    <li>• Runtime configuration updates</li>
                    <li>• Configuration versioning</li>
                    <li>• Rollback capabilities</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="puzzle" class="w-6 h-6"></i>
            </div>
            <h3>Custom Module Development</h3>
            <p class="text-secondary mb-3">
                Module scaffolding tools, custom business logic implementation ve 
                third-party module marketplace with quality assurance.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Module scaffolding tools</li>
                    <li>• Custom business logic support</li>
                    <li>• Module marketplace</li>
                    <li>• Quality assurance testing</li>
                    <li>• Dependency management</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="settings" class="w-6 h-6"></i>
            </div>
            <h3>Workflow Customization</h3>
            <p class="text-secondary mb-3">
                Business process modeling, custom approval workflows ve 
                conditional logic implementation with user-friendly interfaces.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Business process modeling</li>
                    <li>• Custom approval workflows</li>
                    <li>• Conditional logic builder</li>
                    <li>• User-friendly workflow designer</li>
                    <li>• Workflow performance analytics</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>