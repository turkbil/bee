<?php
$page_title = "Widget Sistemi - Türk Bilişim Enterprise CMS";
$page_subtitle = "Modular Widget Architecture";
$page_badge = "🧩 Widget Sistemi";

// Navigation sections
$nav_sections = [
    'hero' => 'Giriş',
    'overview' => 'Genel Bakış',
    'architecture' => 'Widget Mimarisi',
    'builder' => 'Görsel Editör',
    'components' => 'Widget Bileşenleri',
    'rendering' => 'Render Sistemi',
    'customization' => 'Özelleştirme',
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
                Modular<br>
                <span style="font-weight: 700; background: linear-gradient(45deg, #64b5f6, #4facfe, #64b5f6); background-size: 200% 200%; animation: textGradient 3s ease infinite; -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Widget Sistemi</span>
            </h1>
            <p style="margin-bottom: 3rem; font-size: 1.2rem; line-height: 1.6; color: rgba(255, 255, 255, 0.8); max-width: 600px; margin-left: auto; margin-right: auto; font-weight: 400; text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);">
                Drag-and-drop interface ile<br>
                <span style="color: #64b5f6; font-weight: 600;">dynamic content creation</span>
            </p>
        </div>
    </div>
</section>

<!-- Overview Section -->
<section id="overview" class="section">
    <h2 class="section-title">Widget Sistemi Genel Bakış</h2>
    <p class="section-subtitle">
        Modern content management'ın temel taşı olan widget architecture 
        <span class="text-muted">(widget mimarisi)</span> ile reusable components 
        <span class="text-muted">(yeniden kullanılabilir bileşenler)</span> ve dynamic page building 
        <span class="text-muted">(dinamik sayfa oluşturma)</span> imkanı sağlanır. 
        Component-based design pattern <span class="text-muted">(bileşen tabanlı tasarım kalıbı)</span> 
        ile modular development approach <span class="text-muted">(modüler geliştirme yaklaşımı)</span> 
        benimsenmiştir. Visual page builder <span class="text-muted">(görsel sayfa oluşturucu)</span> 
        ile non-technical users'lar bile professional layouts 
        <span class="text-muted">(profesyonel düzenlemeler)</span> oluşturabilir.
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="puzzle"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Modular Component Architecture <span class="text-sm text-muted">(modüler bileşen mimarisi)</span></h3>
            <p class="text-secondary mb-4">
                Atomic design principles <span class="text-muted">(atomik tasarım prensipleri)</span> 
                ile hierarchical component structure <span class="text-muted">(hiyerarşik bileşen yapısı)</span>. 
                Atoms, molecules, organisms ve templates pattern'leri ile scalable widget ecosystem 
                <span class="text-muted">(ölçeklenebilir widget ekosistemi)</span> oluşturulur. 
                Component inheritance <span class="text-muted">(bileşen kalıtımı)</span>, 
                composition patterns <span class="text-muted">(kompozisyon kalıpları)</span> ve 
                dependency injection <span class="text-muted">(bağımlılık enjeksiyonu)</span> 
                ile maintainable widget development sağlanır.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Atomic Design Pattern</span> <span class="text-muted">(atomik tasarım kalıbı)</span><br><span class="text-sm text-secondary">→ Atoms → Molecules → Organisms → Templates hierarchy</span></li>
                        <li>• <span class="tech-highlight">Component Inheritance</span> <span class="text-muted">(bileşen kalıtımı)</span><br><span class="text-sm text-secondary">→ Base widget classes, shared behaviors ve properties</span></li>
                        <li>• <span class="tech-highlight">Composition Patterns</span> <span class="text-muted">(kompozisyon kalıpları)</span><br><span class="text-sm text-secondary">→ Nested widgets, slot systems ve dynamic composition</span></li>
                        <li>• <span class="tech-highlight">Dependency Injection</span> <span class="text-muted">(bağımlılık enjeksiyonu)</span><br><span class="text-sm text-secondary">→ Service resolution, interface binding ve lifecycle management</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="mouse-pointer"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Visual Drag-and-Drop Builder <span class="text-sm text-muted">(görsel sürükle-bırak oluşturucu)</span></h3>
            <p class="text-secondary mb-4">
                Intuitive drag-and-drop interface <span class="text-muted">(sezgisel sürükle-bırak arayüzü)</span> 
                ile WYSIWYG content creation experience. Real-time preview 
                <span class="text-muted">(gerçek zamanlı önizleme)</span>, grid system alignment 
                <span class="text-muted">(ızgara sistemi hizalaması)</span> ve responsive design tools 
                <span class="text-muted">(duyarlı tasarım araçları)</span> ile professional page layouts. 
                Undo/redo functionality <span class="text-muted">(geri al/yinele işlevselliği)</span>, 
                version control <span class="text-muted">(sürüm kontrolü)</span> ve collaborative editing 
                <span class="text-muted">(işbirlikli düzenleme)</span> capabilities.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">WYSIWYG Editor</span> <span class="text-muted">(gördüğün aldığın editörü)</span><br><span class="text-sm text-secondary">→ Real-time editing, instant preview ve live updates</span></li>
                        <li>• <span class="tech-highlight">Grid System</span> <span class="text-muted">(ızgara sistemi)</span><br><span class="text-sm text-secondary">→ CSS Grid, Flexbox layouts ve responsive breakpoints</span></li>
                        <li>• <span class="tech-highlight">Version Control</span> <span class="text-muted">(sürüm kontrolü)</span><br><span class="text-sm text-secondary">→ Change tracking, rollback capabilities ve branch management</span></li>
                        <li>• <span class="tech-highlight">Collaborative Editing</span> <span class="text-muted">(işbirlikli düzenleme)</span><br><span class="text-sm text-secondary">→ Real-time collaboration, conflict resolution ve user presence</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="layers"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Smart Rendering Engine <span class="text-sm text-muted">(akıllı render motoru)</span></h3>
            <p class="text-secondary mb-4">
                High-performance rendering engine <span class="text-muted">(yüksek performanslı render motoru)</span> 
                ile optimized output generation. Component caching 
                <span class="text-muted">(bileşen önbellekleme)</span>, lazy loading 
                <span class="text-muted">(tembel yükleme)</span> ve progressive enhancement 
                <span class="text-muted">(aşamalı geliştirme)</span> techniques. 
                Server-side rendering (SSR), client-side hydration 
                <span class="text-muted">(istemci tarafı hidratasyon)</span> ve static generation 
                <span class="text-muted">(statik üretim)</span> support ile optimal performance.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">Server-Side Rendering</span> <span class="text-muted">(sunucu tarafı render)</span><br><span class="text-sm text-secondary">→ SEO optimization, fast initial load ve progressive enhancement</span></li>
                        <li>• <span class="tech-highlight">Component Caching</span> <span class="text-muted">(bileşen önbellekleme)</span><br><span class="text-sm text-secondary">→ Fragment caching, cache invalidation ve dependency tracking</span></li>
                        <li>• <span class="tech-highlight">Lazy Loading</span> <span class="text-muted">(tembel yükleme)</span><br><span class="text-sm text-secondary">→ Intersection Observer API, dynamic imports ve code splitting</span></li>
                        <li>• <span class="tech-highlight">Static Generation</span> <span class="text-muted">(statik üretim)</span><br><span class="text-sm text-secondary">→ Build-time pre-rendering, CDN optimization ve edge caching</span></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="settings"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">Dynamic Configuration System <span class="text-sm text-muted">(dinamik konfigürasyon sistemi)</span></h3>
            <p class="text-secondary mb-4">
                Flexible configuration management <span class="text-muted">(esnek konfigürasyon yönetimi)</span> 
                ile widget behavior customization. JSON schema validation 
                <span class="text-muted">(JSON şema doğrulaması)</span>, conditional logic 
                <span class="text-muted">(koşullu mantık)</span> ve dynamic property binding 
                <span class="text-muted">(dinamik özellik bağlama)</span>. 
                Template engine integration <span class="text-muted">(şablon motoru entegrasyonu)</span>, 
                data binding <span class="text-muted">(veri bağlama)</span> ve event handling 
                <span class="text-muted">(olay işleme)</span> ile interactive widgets creation.
            </p>
            <div class="code-block">
                <div class="text-xs">
                    <ul class="list-none space-y-2">
                        <li>• <span class="tech-highlight">JSON Schema Validation</span> <span class="text-muted">(JSON şema doğrulaması)</span><br><span class="text-sm text-secondary">→ Configuration validation, type checking ve error reporting</span></li>
                        <li>• <span class="tech-highlight">Conditional Logic</span> <span class="text-muted">(koşullu mantık)</span><br><span class="text-sm text-secondary">→ Dynamic behavior, rule engine ve decision trees</span></li>
                        <li>• <span class="tech-highlight">Data Binding</span> <span class="text-muted">(veri bağlama)</span><br><span class="text-sm text-secondary">→ Two-way binding, reactive updates ve change detection</span></li>
                        <li>• <span class="tech-highlight">Event Handling</span> <span class="text-muted">(olay işleme)</span><br><span class="text-sm text-secondary">→ Custom events, event bubbling ve action triggers</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Widget Architecture -->
<section id="architecture" class="section">
    <h2 class="section-title text-center">Widget Mimarisi</h2>
    <p class="section-subtitle text-center">
        Component-based architecture ile scalable ve maintainable widget ecosystem
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="box" class="w-6 h-6"></i>
            </div>
            <h3>Widget Base Class</h3>
            <p class="text-secondary mb-3">
                Abstract base widget class ile common functionality inheritance. 
                Lifecycle hooks, property management ve rendering pipeline.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Abstract widget base class</li>
                    <li>• Lifecycle method hooks</li>
                    <li>• Property getter/setter methods</li>
                    <li>• Rendering pipeline integration</li>
                    <li>• Event system implementation</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="layers" class="w-6 h-6"></i>
            </div>
            <h3>Component Hierarchy</h3>
            <p class="text-secondary mb-3">
                Hierarchical widget structure ile parent-child relationships. 
                Nested widgets, slot systems ve component composition patterns.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Parent-child relationships</li>
                    <li>• Nested widget support</li>
                    <li>• Slot-based composition</li>
                    <li>• Component tree traversal</li>
                    <li>• Dependency resolution</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="database" class="w-6 h-6"></i>
            </div>
            <h3>Data Management</h3>
            <p class="text-secondary mb-3">
                Widget data persistence ile configuration storage. 
                JSON schema validation, versioning ve migration support.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• JSON configuration storage</li>
                    <li>• Schema validation rules</li>
                    <li>• Version control system</li>
                    <li>• Migration tools</li>
                    <li>• Backup ve restore</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="refresh-cw" class="w-6 h-6"></i>
            </div>
            <h3>Lifecycle Management</h3>
            <p class="text-secondary mb-3">
                Widget lifecycle hooks ile initialization, update ve cleanup processes. 
                Memory management ve resource optimization.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Initialization hooks</li>
                    <li>• Update lifecycle methods</li>
                    <li>• Cleanup procedures</li>
                    <li>• Memory management</li>
                    <li>• Resource optimization</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Visual Builder -->
<section id="builder" class="section">
    <h2 class="section-title text-center">Görsel Sayfa Editörü</h2>
    <p class="section-subtitle text-center">
        Drag-and-drop interface ile intuitive page building ve real-time editing
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="mouse-pointer" class="w-6 h-6"></i>
            </div>
            <h3>Drag-and-Drop Interface</h3>
            <p class="text-secondary mb-3">
                Intuitive drag-and-drop functionality ile widget placement. 
                Grid snapping, alignment tools ve visual feedback systems.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Drag-and-drop widget placement</li>
                    <li>• Grid snapping alignment</li>
                    <li>• Visual drop zone indicators</li>
                    <li>• Collision detection</li>
                    <li>• Undo/redo functionality</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="eye" class="w-6 h-6"></i>
            </div>
            <h3>Real-Time Preview</h3>
            <p class="text-secondary mb-3">
                Live preview functionality ile WYSIWYG editing experience. 
                Responsive breakpoint testing ve cross-browser compatibility.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Live editing preview</li>
                    <li>• Responsive breakpoint testing</li>
                    <li>• Cross-browser preview</li>
                    <li>• Device simulation modes</li>
                    <li>• Performance preview metrics</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="grid" class="w-6 h-6"></i>
            </div>
            <h3>Layout Grid System</h3>
            <p class="text-secondary mb-3">
                CSS Grid ve Flexbox integration ile responsive layout creation. 
                Custom breakpoints, spacing controls ve alignment options.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• CSS Grid layout system</li>
                    <li>• Flexbox alignment controls</li>
                    <li>• Custom breakpoint management</li>
                    <li>• Spacing ve margin controls</li>
                    <li>• Responsive design tools</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="sliders" class="w-6 h-6"></i>
            </div>
            <h3>Property Panel</h3>
            <p class="text-secondary mb-3">
                Dynamic property panel ile widget configuration. 
                Context-sensitive controls, validation ve instant preview updates.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Dynamic property controls</li>
                    <li>• Context-sensitive options</li>
                    <li>• Real-time validation</li>
                    <li>• Instant preview updates</li>
                    <li>• Advanced configuration modes</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Widget Components -->
<section id="components" class="section">
    <h2 class="section-title text-center">Widget Bileşenleri</h2>
    <p class="section-subtitle text-center">
        Comprehensive widget library ile reusable content components
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="type" class="w-6 h-6"></i>
            </div>
            <h3>Content Widgets</h3>
            <p class="text-secondary mb-3">
                Text, rich content ve media widgets ile comprehensive content creation. 
                Typography controls, media embedding ve content formatting options.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Rich text editor widget</li>
                    <li>• Image ve gallery widgets</li>
                    <li>• Video embedding widget</li>
                    <li>• Typography control widget</li>
                    <li>• Content formatting tools</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="layout" class="w-6 h-6"></i>
            </div>
            <h3>Layout Widgets</h3>
            <p class="text-secondary mb-3">
                Structural layout widgets ile page organization. 
                Containers, columns, spacers ve responsive layout components.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Container ve section widgets</li>
                    <li>• Multi-column layout widgets</li>
                    <li>• Spacer ve divider widgets</li>
                    <li>• Responsive grid widgets</li>
                    <li>• Card ve panel layouts</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="mouse-pointer-click" class="w-6 h-6"></i>
            </div>
            <h3>Interactive Widgets</h3>
            <p class="text-secondary mb-3">
                User interaction widgets ile dynamic functionality. 
                Forms, buttons, navigation ve interactive elements.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Form ve input widgets</li>
                    <li>• Button ve CTA widgets</li>
                    <li>• Navigation menu widgets</li>
                    <li>• Tab ve accordion widgets</li>
                    <li>• Modal ve popup widgets</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="bar-chart" class="w-6 h-6"></i>
            </div>
            <h3>Data Visualization Widgets</h3>
            <p class="text-secondary mb-3">
                Charts, graphs ve data display widgets ile information visualization. 
                Dynamic data binding ve real-time updates.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Chart ve graph widgets</li>
                    <li>• Data table widgets</li>
                    <li>• Statistics display widgets</li>
                    <li>• Progress indicator widgets</li>
                    <li>• Real-time data widgets</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Rendering System -->
<section id="rendering" class="section">
    <h2 class="section-title text-center">Render Sistemi</h2>
    <p class="section-subtitle text-center">
        High-performance rendering ile optimized output generation ve caching
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="cpu" class="w-6 h-6"></i>
            </div>
            <h3>Server-Side Rendering</h3>
            <p class="text-secondary mb-3">
                PHP server-side rendering ile SEO-optimized output generation. 
                Blade template integration ve component-based rendering.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• PHP server-side rendering</li>
                    <li>• Blade template integration</li>
                    <li>• Component-based rendering</li>
                    <li>• SEO optimization</li>
                    <li>• Fast initial page load</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="zap" class="w-6 h-6"></i>
            </div>
            <h3>Fragment Caching</h3>
            <p class="text-Secondary mb-3">
                Widget-level caching ile performance optimization. 
                Intelligent cache invalidation ve dependency tracking.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Widget-level fragment caching</li>
                    <li>• Intelligent cache invalidation</li>
                    <li>• Dependency tracking system</li>
                    <li>• Cache warming strategies</li>
                    <li>• Performance metrics tracking</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="layers" class="w-6 h-6"></i>
            </div>
            <h3>Progressive Enhancement</h3>
            <p class="text-secondary mb-3">
                Base HTML rendering ile JavaScript enhancement. 
                Graceful degradation ve accessibility compliance.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Base HTML rendering</li>
                    <li>• JavaScript enhancement layers</li>
                    <li>• Graceful degradation</li>
                    <li>• Accessibility compliance</li>
                    <li>• Cross-browser compatibility</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="smartphone" class="w-6 h-6"></i>
            </div>
            <h3>Responsive Rendering</h3>
            <p class="text-secondary mb-3">
                Device-specific rendering ile optimal mobile experience. 
                Adaptive loading ve progressive image delivery.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Device-specific rendering</li>
                    <li>• Adaptive content loading</li>
                    <li>• Progressive image delivery</li>
                    <li>• Mobile optimization</li>
                    <li>• Touch-friendly interfaces</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Customization -->
<section id="customization" class="section">
    <h2 class="section-title text-center">Widget Özelleştirme</h2>
    <p class="section-subtitle text-center">
        Advanced customization options ile unique widget development ve branding
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="code" class="w-6 h-6"></i>
            </div>
            <h3>Custom Widget Development</h3>
            <p class="text-secondary mb-3">
                Widget SDK ile custom component development. 
                API documentation, scaffolding tools ve development guidelines.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Widget development SDK</li>
                    <li>• API documentation</li>
                    <li>• Scaffolding tools</li>
                    <li>• Development guidelines</li>
                    <li>• Testing framework</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="palette" class="w-6 h-6"></i>
            </div>
            <h3>Theme Integration</h3>
            <p class="text-secondary mb-3">
                Theme-aware widget styling ile consistent design language. 
                CSS custom properties, design tokens ve brand integration.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Theme-aware styling</li>
                    <li>• CSS custom properties</li>
                    <li>• Design token system</li>
                    <li>• Brand color integration</li>
                    <li>• Typography consistency</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="settings" class="w-6 h-6"></i>
            </div>
            <h3>Configuration Schema</h3>
            <p class="text-secondary mb-3">
                JSON schema-based configuration ile dynamic property panels. 
                Validation rules, conditional fields ve type safety.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• JSON schema configuration</li>
                    <li>• Dynamic property panels</li>
                    <li>• Validation rule engine</li>
                    <li>• Conditional field display</li>
                    <li>• Type safety enforcement</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="package" class="w-6 h-6"></i>
            </div>
            <h3>Widget Marketplace</h3>
            <p class="text-secondary mb-3">
                Community widget sharing ile ecosystem expansion. 
                Version management, security scanning ve quality assurance.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Community widget sharing</li>
                    <li>• Version management</li>
                    <li>• Security scanning</li>
                    <li>• Quality assurance testing</li>
                    <li>• Rating ve review system</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Performance -->
<section id="performance" class="section">
    <h2 class="section-title text-center">Widget Performansı</h2>
    <p class="section-subtitle text-center">
        Performance optimization ile fast loading times ve efficient resource usage
    </p>
    
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="zap" class="w-6 h-6"></i>
            </div>
            <h3>Lazy Loading Strategy</h3>
            <p class="text-secondary mb-3">
                Intersection Observer API ile smart widget loading. 
                Priority-based loading ve resource optimization.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Intersection Observer API</li>
                    <li>• Priority-based loading</li>
                    <li>• Viewport detection</li>
                    <li>• Resource optimization</li>
                    <li>• Loading state management</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="database" class="w-6 h-6"></i>
            </div>
            <h3>Caching Strategy</h3>
            <p class="text-secondary mb-3">
                Multi-level caching ile optimal performance. 
                Fragment caching, full-page caching ve edge caching integration.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Multi-level caching system</li>
                    <li>• Fragment-level caching</li>
                    <li>• Full-page cache integration</li>
                    <li>• Edge caching support</li>
                    <li>• Cache invalidation strategies</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="minimize" class="w-6 h-6"></i>
            </div>
            <h3>Asset Optimization</h3>
            <p class="text-secondary mb-3">
                CSS/JS bundling ile optimized asset delivery. 
                Code splitting, tree shaking ve compression techniques.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• CSS/JS bundling optimization</li>
                    <li>• Code splitting strategies</li>
                    <li>• Tree shaking implementation</li>
                    <li>• Compression techniques</li>
                    <li>• Asset versioning</li>
                </ul>
            </div>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i data-lucide="activity" class="w-6 h-6"></i>
            </div>
            <h3>Performance Monitoring</h3>
            <p class="text-secondary mb-3">
                Real-time performance metrics ile optimization insights. 
                Widget-level analytics ve bottleneck identification.
            </p>
            <div class="code-block">
                <ul class="list-none space-y-2">
                    <li>• Real-time performance metrics</li>
                    <li>• Widget-level analytics</li>
                    <li>• Bottleneck identification</li>
                    <li>• Loading time optimization</li>
                    <li>• User experience metrics</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>