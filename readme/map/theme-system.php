<?php
$page_title = "Tema Sistemi - Türk Bilişim Enterprise CMS";
$page_subtitle = "Advanced Theme Management";
$page_badge = "🎨 Tema Sistemi";

// Navigation sections
$nav_sections = [
    'hero' => 'Giriş',
    'overview' => 'Genel Bakış',
    'architecture' => 'Tema Mimarisi',
    'customization' => 'Özelleştirme',
    'inheritance' => 'Tema Kalıtımı',
    'assets' => 'Asset Yönetimi',
    'responsive' => 'Responsive Tasarım',
    'performance' => 'Performans'
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
                Advanced<br>
                <span style="font-weight: 700; background: linear-gradient(45deg, #64b5f6, #4facfe, #64b5f6); background-size: 200% 200%; animation: textGradient 3s ease infinite; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Tema Sistemi</span>
            </h1>
            <p style="margin-bottom: 3rem; font-size: 1.2rem; line-height: 1.6; color: rgba(255, 255, 255, 0.8); max-width: 600px; margin-left: auto; margin-right: auto; font-weight: 400; text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);">
                Powerful theming engine ile<br>
                <span style="color: #64b5f6; font-weight: 600;">unlimited design possibilities</span>
            </p>
        </div>
    </div>
</section>

<!-- Overview Section -->
<section id="overview" class="section">
    <h2 class="section-title">Tema Sistemi Genel Bakış</h2>
    <p class="section-subtitle">
        Modern web design'ın dinamik requirements'leri için flexible theming architecture 
        <span class="text-muted">(esnek temalama mimarisi)</span> ile unlimited customization possibilities 
        <span class="text-muted">(sınırsız özelleştirme imkanları)</span> sağlanır. 
        Component-based theming approach <span class="text-muted">(bileşen tabanlı temalama yaklaşımı)</span> 
        ile design consistency <span class="text-muted">(tasarım tutarlılığı)</span>, 
        brand identity management <span class="text-muted">(marka kimliği yönetimi)</span> ve 
        multi-tenant customization <span class="text-muted">(çok kiracılı özelleştirme)</span> 
        capabilities. CSS-in-JS, design tokens ve atomic design principles 
        <span class="text-muted">(atomik tasarım prensipleri)</span> ile scalable theme ecosystem.
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="palette"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Design Token System <span class="text-sm text-muted">(tasarım token sistemi)</span></h3>
            <p class="text-secondary mb-4">
                Centralized design token management <span class="text-muted">(merkezi tasarım token yönetimi)</span> 
                ile consistent visual language <span class="text-muted">(tutarlı görsel dil)</span>. 
                Color palettes <span class="text-muted">(renk paletleri)</span>, typography scales 
                <span class="text-muted">(tipografi ölçekleri)</span>, spacing systems 
                <span class="text-muted">(boşluk sistemleri)</span> ve component variants 
                <span class="text-muted">(bileşen varyantları)</span> ile systematic design approach. 
                CSS custom properties <span class="text-muted">(CSS özel özellikleri)</span>, 
                dynamic theme switching <span class="text-muted">(dinamik tema değiştirme)</span> ve 
                real-time customization <span class="text-muted">(gerçek zamanlı özelleştirme)</span> capabilities.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Color System Management</span> <span class="text-muted">(renk sistemi yönetimi)</span><br><span class="text-sm text-secondary">→ Primary, secondary, semantic colors ve accessibility compliance</span></li>
                        <li>• <span class="tech-highlight">Typography Scaling</span> <span class="text-muted">(tipografi ölçeklendirme)</span><br><span class="text-sm text-secondary">→ Modular scales, font families, weights ve responsive typography</span></li>
                        <li>• <span class="tech-highlight">Spacing System</span> <span class="text-muted">(boşluk sistemi)</span><br><span class="text-sm text-secondary">→ Consistent margins, padding, grid systems ve component spacing</span></li>
                        <li>• <span class="tech-highlight">Component Tokens</span> <span class="text-muted">(bileşen tokenleri)</span><br><span class="text-sm text-secondary">→ Border radius, shadows, transitions ve state variations</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="layers"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Theme Inheritance System <span class="text-sm text-muted">(tema kalıtım sistemi)</span></h3>
            <p class="text-secondary mb-4">
                Hierarchical theme structure <span class="text-muted">(hiyerarşik tema yapısı)</span> 
                ile parent-child theme relationships <span class="text-muted">(ebeveyn-çocuk tema ilişkileri)</span>. 
                Base themes <span class="text-muted">(temel temalar)</span>, child themes 
                <span class="text-muted">(çocuk temalar)</span> ve custom overrides 
                <span class="text-muted">(özel geçersiz kılmalar)</span> ile maintainable theming. 
                Template inheritance <span class="text-muted">(şablon kalıtımı)</span>, 
                selective overriding <span class="text-muted">(seçici geçersiz kılma)</span> ve 
                fallback mechanisms <span class="text-muted">(geri dönüş mekanizmaları)</span> 
                ile robust theme management.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Parent-Child Architecture</span> <span class="text-muted">(ebeveyn-çocuk mimarisi)</span><br><span class="text-sm text-secondary">→ Template inheritance, asset cascading ve override mechanisms</span></li>
                        <li>• <span class="tech-highlight">Selective Overriding</span> <span class="text-muted">(seçici geçersiz kılma)</span><br><span class="text-sm text-secondary">→ Component-level customization, partial template overrides</span></li>
                        <li>• <span class="tech-highlight">Fallback System</span> <span class="text-muted">(geri dönüş sistemi)</span><br><span class="text-sm text-secondary">→ Missing template handling, asset resolution chains</span></li>
                        <li>• <span class="tech-highlight">Version Management</span> <span class="text-muted">(sürüm yönetimi)</span><br><span class="text-sm text-secondary">→ Theme versioning, migration tools ve compatibility layers</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="smartphone"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Responsive Design Engine <span class="text-sm text-muted">(duyarlı tasarım motoru)</span></h3>
            <p class="text-secondary mb-4">
                Mobile-first approach <span class="text-muted">(mobil öncelikli yaklaşım)</span> 
                ile adaptive design systems <span class="text-muted">(uyarlanabilir tasarım sistemleri)</span>. 
                Breakpoint management <span class="text-muted">(kesme noktası yönetimi)</span>, 
                fluid typography <span class="text-muted">(akışkan tipografi)</span> ve 
                container queries <span class="text-muted">(konteyner sorguları)</span> support. 
                Device-specific optimizations <span class="text-muted">(cihaza özel optimizasyonlar)</span>, 
                progressive enhancement <span class="text-muted">(aşamalı geliştirme)</span> ve 
                accessibility compliance <span class="text-muted">(erişilebilirlik uyumluluğu)</span> 
                ile universal design principles.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Mobile-First Design</span> <span class="text-muted">(mobil öncelikli tasarım)</span><br><span class="text-sm text-secondary">→ Progressive enhancement, touch optimization ve mobile performance</span></li>
                        <li>• <span class="tech-highlight">Fluid Typography</span> <span class="text-muted">(akışkan tipografi)</span><br><span class="text-sm text-secondary">→ Responsive font sizing, line height scaling ve readability optimization</span></li>
                        <li>• <span class="tech-highlight">Container Queries</span> <span class="text-muted">(konteyner sorguları)</span><br><span class="text-sm text-secondary">→ Component-level responsiveness, intrinsic web design</span></li>
                        <li>• <span class="tech-highlight">Accessibility First</span> <span class="text-muted">(erişilebilirlik öncelikli)</span><br><span class="text-sm text-secondary">→ WCAG compliance, screen reader support ve keyboard navigation</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="zap"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Dynamic Theme Switching <span class="text-sm text-muted">(dinamik tema değiştirme)</span></h3>
            <p class="text-secondary mb-4">
                Real-time theme switching <span class="text-muted">(gerçek zamanlı tema değiştirme)</span> 
                ile instant visual transformations. Dark mode support 
                <span class="text-muted">(karanlık mod desteği)</span>, user preferences 
                <span class="text-muted">(kullanıcı tercihleri)</span> ve system-based theme detection 
                <span class="text-muted">(sistem tabanlı tema tespiti)</span>. 
                CSS-in-JS integration <span class="text-muted">(CSS-in-JS entegrasyonu)</span>, 
                theme caching <span class="text-muted">(tema önbellekleme)</span> ve 
                performance optimization <span class="text-muted">(performans optimizasyonu)</span> 
                ile seamless user experience.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Real-Time Switching</span> <span class="text-muted">(gerçek zamanlı değiştirme)</span><br><span class="text-sm text-secondary">→ Instant theme application, smooth transitions ve state persistence</span></li>
                        <li>• <span class="tech-highlight">Dark Mode Support</span> <span class="text-muted">(karanlık mod desteği)</span><br><span class="text-sm text-secondary">→ Auto dark mode, manual toggle ve system preference detection</span></li>
                        <li>• <span class="tech-highlight">User Preferences</span> <span class="text-muted">(kullanıcı tercihleri)</span><br><span class="text-sm text-secondary">→ Personal theme settings, custom color schemes ve accessibility preferences</span></li>
                        <li>• <span class="tech-highlight">Performance Optimization</span> <span class="text-muted">(performans optimizasyonu)</span><br><span class="text-sm text-secondary">→ Theme preloading, lazy loading ve caching strategies</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Theme Architecture -->
<section id="architecture" class="section">
    <h2 class="section-title text-center">Tema Mimarisi</h2>
    <p class="section-subtitle text-center">
        Scalable theme architecture ile organized structure ve maintainable code
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="folder" class="w-6 h-6"></i>
            </div>
            <h3>Theme Directory Structure</h3>
            <p class="text-secondary mb-3">
                Organized file structure ile theme assets management. 
                Templates, styles, scripts ve configuration files organization.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Template files organization</li>
                    <li>• CSS/SCSS file structure</li>
                    <li>• JavaScript modules</li>
                    <li>• Asset files management</li>
                    <li>• Configuration files</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="code" class="w-6 h-6"></i>
            </div>
            <h3>Template Engine Integration</h3>
            <p class="text-secondary mb-3">
                Blade template engine ile theme template processing. 
                Template inheritance, partial includes ve dynamic content rendering.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Blade template integration</li>
                    <li>• Template inheritance system</li>
                    <li>• Partial template includes</li>
                    <li>• Dynamic content injection</li>
                    <li>• Template caching optimization</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="settings" class="w-6 h-6"></i>
            </div>
            <h3>Configuration Management</h3>
            <p class="text-secondary mb-3">
                JSON-based theme configuration ile customization options. 
                Settings validation, default values ve user override capabilities.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• JSON configuration schema</li>
                    <li>• Settings validation rules</li>
                    <li>• Default value management</li>
                    <li>• User override system</li>
                    <li>• Environment-specific configs</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="package" class="w-6 h-6"></i>
            </div>
            <h3>Asset Pipeline</h3>
            <p class="text-secondary mb-3">
                Automated asset processing ile optimization pipeline. 
                Compilation, minification, versioning ve CDN integration.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• SCSS/SASS compilation</li>
                    <li>• JavaScript bundling</li>
                    <li>• Asset minification</li>
                    <li>• Version control ve fingerprinting</li>
                    <li>• CDN deployment integration</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Customization -->
<section id="customization" class="section">
    <h2 class="section-title text-center">Tema Özelleştirme</h2>
    <p class="section-subtitle text-center">
        Visual customization tools ile brand-specific design implementations
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="color-wand" class="w-6 h-6"></i>
            </div>
            <h3>Color Scheme Editor</h3>
            <p class="text-secondary mb-3">
                Interactive color picker ile brand color customization. 
                Color harmony suggestions, accessibility checking ve preview generation.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Interactive color wheel picker</li>
                    <li>• Brand color palette creation</li>
                    <li>• Color accessibility validation</li>
                    <li>• Harmony suggestion algorithms</li>
                    <li>• Real-time preview updates</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="type" class="w-6 h-6"></i>
            </div>
            <h3>Typography Customization</h3>
            <p class="text-secondary mb-3">
                Font family selection ile typography system customization. 
                Google Fonts integration, custom font uploads ve responsive scaling.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Google Fonts integration</li>
                    <li>• Custom font upload support</li>
                    <li>• Typography scale generation</li>
                    <li>• Responsive font sizing</li>
                    <li>• Font loading optimization</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="layout" class="w-6 h-6"></i>
            </div>
            <h3>Layout Configuration</h3>
            <p class="text-secondary mb-3">
                Grid system customization ile layout structure modification. 
                Container widths, column configurations ve spacing adjustments.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Grid system configuration</li>
                    <li>• Container width settings</li>
                    <li>• Column layout options</li>
                    <li>• Spacing scale adjustments</li>
                    <li>• Breakpoint customization</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="image" class="w-6 h-6"></i>
            </div>
            <h3>Brand Asset Management</h3>
            <p class="text-secondary mb-3">
                Logo, favicon ve brand asset management system. 
                Multi-resolution support, format optimization ve variant generation.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Logo variant management</li>
                    <li>• Favicon generation</li>
                    <li>• Brand asset optimization</li>
                    <li>• Multi-resolution support</li>
                    <li>• Format conversion tools</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Theme Inheritance -->
<section id="inheritance" class="section">
    <h2 class="section-title text-center">Tema Kalıtım Sistemi</h2>
    <p class="section-subtitle text-center">
        Hierarchical theme structure ile maintainable ve scalable theme development
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="git-branch" class="w-6 h-6"></i>
            </div>
            <h3>Parent-Child Architecture</h3>
            <p class="text-secondary mb-3">
                Base theme inheritance ile child theme customizations. 
                Template override system ve selective customization capabilities.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Base theme foundation</li>
                    <li>• Child theme extensions</li>
                    <li>• Template override system</li>
                    <li>• Selective customization</li>
                    <li>• Inheritance chain management</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="layers" class="w-6 h-6"></i>
            </div>
            <h3>Template Resolution</h3>
            <p class="text-secondary mb-3">
                Smart template resolution ile fallback mechanisms. 
                Template hierarchy traversal ve automatic fallback handling.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Template hierarchy scanning</li>
                    <li>• Automatic fallback resolution</li>
                    <li>• Override detection system</li>
                    <li>• Template cache management</li>
                    <li>• Performance optimization</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="merge" class="w-6 h-6"></i>
            </div>
            <h3>Asset Cascading</h3>
            <p class="text-secondary mb-3">
                CSS/JS asset cascading ile style inheritance. 
                Asset merging, override management ve dependency resolution.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• CSS cascade management</li>
                    <li>• JavaScript inheritance</li>
                    <li>• Asset merging strategies</li>
                    <li>• Dependency resolution</li>
                    <li>• Override conflict handling</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="shield" class="w-6 h-6"></i>
            </div>
            <h3>Update Safety</h3>
            <p class="text-secondary mb-3">
                Theme update protection ile customization preservation. 
                Safe update mechanisms ve rollback capabilities.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Customization preservation</li>
                    <li>• Safe update mechanisms</li>
                    <li>• Rollback capabilities</li>
                    <li>• Conflict detection</li>
                    <li>• Backup automation</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Asset Management -->
<section id="assets" class="section">
    <h2 class="section-title text-center">Asset Yönetimi</h2>
    <p class="section-subtitle text-center">
        Comprehensive asset pipeline ile optimization ve delivery management
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="file-code" class="w-6 h-6"></i>
            </div>
            <h3>CSS Preprocessing</h3>
            <p class="text-secondary mb-3">
                SCSS/SASS compilation ile advanced CSS features. 
                Variables, mixins, functions ve modular architecture support.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• SCSS/SASS compilation</li>
                    <li>• CSS variable generation</li>
                    <li>• Mixin library management</li>
                    <li>• Function utility creation</li>
                    <li>• Modular CSS architecture</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="package" class="w-6 h-6"></i>
            </div>
            <h3>JavaScript Bundling</h3>
            <p class="text-secondary mb-3">
                Modern JavaScript bundling ile module management. 
                ES6+ support, tree shaking ve code splitting optimization.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• ES6+ module bundling</li>
                    <li>• Tree shaking optimization</li>
                    <li>• Code splitting strategies</li>
                    <li>• Dynamic import support</li>
                    <li>• Polyfill management</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="image" class="w-6 h-6"></i>
            </div>
            <h3>Image Optimization</h3>
            <p class="text-secondary mb-3">
                Automatic image processing ile format optimization. 
                WebP conversion, responsive images ve lazy loading integration.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• WebP format conversion</li>
                    <li>• Responsive image generation</li>
                    <li>• Compression optimization</li>
                    <li>• Lazy loading implementation</li>
                    <li>• Progressive JPEG support</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="globe" class="w-6 h-6"></i>
            </div>
            <h3>CDN Integration</h3>
            <p class="text-secondary mb-3">
                Content delivery network integration ile global asset distribution. 
                Edge caching, versioning ve performance optimization.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Global CDN distribution</li>
                    <li>• Edge caching strategies</li>
                    <li>• Asset versioning management</li>
                    <li>• Bandwidth optimization</li>
                    <li>• Geographic load balancing</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Responsive Design -->
<section id="responsive" class="section">
    <h2 class="section-title text-center">Responsive Tasarım</h2>
    <p class="section-subtitle text-center">
        Mobile-first approach ile adaptive design systems ve cross-device optimization
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="smartphone" class="w-6 h-6"></i>
            </div>
            <h3>Mobile-First Development</h3>
            <p class="text-secondary mb-3">
                Progressive enhancement ile mobile-optimized base design. 
                Touch optimization, gesture support ve mobile performance focus.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Mobile-first CSS approach</li>
                    <li>• Touch-optimized interactions</li>
                    <li>• Gesture recognition support</li>
                    <li>• Mobile performance optimization</li>
                    <li>• Progressive enhancement layers</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="monitor" class="w-6 h-6"></i>
            </div>
            <h3>Breakpoint Management</h3>
            <p class="text-secondary mb-3">
                Custom breakpoint system ile device-specific adaptations. 
                Fluid breakpoints, container queries ve adaptive layouts.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Custom breakpoint definitions</li>
                    <li>• Fluid breakpoint scaling</li>
                    <li>• Container query support</li>
                    <li>• Adaptive layout systems</li>
                    <li>• Device-specific optimizations</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="type" class="w-6 h-6"></i>
            </div>
            <h3>Fluid Typography</h3>
            <p class="text-secondary mb-3">
                Responsive typography scaling ile optimal readability. 
                Viewport-based sizing, modular scales ve accessibility compliance.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Viewport-based font sizing</li>
                    <li>• Modular typography scales</li>
                    <li>• Line height optimization</li>
                    <li>• Reading distance adaptation</li>
                    <li>• Accessibility compliance</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="grid" class="w-6 h-6"></i>
            </div>
            <h3>Flexible Grid Systems</h3>
            <p class="text-secondary mb-3">
                CSS Grid ve Flexbox integration ile adaptive layouts. 
                Dynamic column adjustments ve content-aware responsiveness.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• CSS Grid layout systems</li>
                    <li>• Flexbox integration</li>
                    <li>• Dynamic column adjustment</li>
                    <li>• Content-aware responsiveness</li>
                    <li>• Intrinsic web design principles</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Performance -->
<section id="performance" class="section">
    <h2 class="section-title text-center">Tema Performansı</h2>
    <p class="section-subtitle text-center">
        Performance optimization ile fast loading times ve efficient resource usage
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="zap" class="w-6 h-6"></i>
            </div>
            <h3>Asset Optimization</h3>
            <p class="text-secondary mb-3">
                Comprehensive asset optimization ile minimal bundle sizes. 
                Minification, compression ve critical path optimization.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• CSS/JS minification</li>
                    <li>• Gzip/Brotli compression</li>
                    <li>• Critical CSS extraction</li>
                    <li>• Unused code elimination</li>
                    <li>• Asset bundling optimization</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="layers" class="w-6 h-6"></i>
            </div>
            <h3>Lazy Loading Implementation</h3>
            <p class="text-secondary mb-3">
                Progressive content loading ile improved page speed. 
                Image lazy loading, component splitting ve on-demand resource loading.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Image lazy loading</li>
                    <li>• Component code splitting</li>
                    <li>• On-demand resource loading</li>
                    <li>• Intersection Observer API</li>
                    <li>• Progressive enhancement</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="database" class="w-6 h-6"></i>
            </div>
            <h3>Caching Strategies</h3>
            <p class="text-secondary mb-3">
                Multi-level caching ile optimal performance delivery. 
                Browser caching, CDN integration ve template caching.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Browser cache optimization</li>
                    <li>• CDN edge caching</li>
                    <li>• Template compilation caching</li>
                    <li>• Asset fingerprinting</li>
                    <li>• Cache invalidation strategies</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="gauge" class="w-6 h-6"></i>
            </div>
            <h3>Performance Monitoring</h3>
            <p class="text-secondary mb-3">
                Real-time performance metrics ile optimization insights. 
                Core Web Vitals tracking ve automated performance auditing.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Core Web Vitals monitoring</li>
                    <li>• Real user monitoring (RUM)</li>
                    <li>• Performance budget enforcement</li>
                    <li>• Automated lighthouse audits</li>
                    <li>• Optimization recommendations</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>