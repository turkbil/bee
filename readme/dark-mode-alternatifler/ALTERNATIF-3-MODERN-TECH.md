# ⚡ ALTERNATİF 3: MODERN TECH DARK MODE

**Konsept:** Gold Gradient + Navy + Glassmorphism - **TEKNOLOJİK & KESKİN**
**Yaklaşım:** Sharp edges, tech patterns, data visualization, futuristic UI, neon accents

---

## 🎨 RENK PALETI (AYNI + NEON ACCENTS)

```css
/* Navy Tonları (Siyah yerine) */
--navy-950: #0a0e27;  /* Ana arka plan */
--navy-900: #0f1629;  /* Kartlar için */
--navy-800: #1a1f3a;  /* Hover durumları */
--navy-700: #252b4a;  /* Border */

/* Gold Gradient (Animasyonlu) */
--gold-gradient: linear-gradient(90deg, #d4af37, #f4e5a1, #d4af37);
--gold-dark: #d4af37;
--gold-light: #f4e5a1;

/* Tech Neon Accents */
--neon-cyan: #00ffff;
--neon-green: #00ff88;
```

---

## 🧩 COMPONENT TASARIMLARI

### 1️⃣ NAVBAR + MEGAMENU (Tech Style)

```blade
{{-- Modern Tech Navbar --}}
<nav class="
    fixed w-full top-0 z-50
    bg-navy-950/90
    backdrop-blur-xl
    border-b border-gold-dark/20
    tech-grid-pattern
">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-20">
            {{-- Logo - Tech Style --}}
            <a href="/" class="flex items-center gap-2">
                <div class="relative">
                    {{-- Animated Tech Box --}}
                    <div class="w-10 h-10 border-2 border-gold-dark rotate-45">
                        <div class="absolute inset-0 border border-gold-light/30 rotate-12 animate-pulse"></div>
                    </div>
                </div>
                <span class="text-2xl font-black text-white uppercase tracking-wider">
                    iX<span class="text-gold-dark">TIF</span>
                </span>
            </a>

            {{-- Desktop Menu - Tech Style --}}
            <ul class="hidden lg:flex items-center gap-2">
                <li class="group relative">
                    <button class="
                        px-6 py-3
                        text-gray-300 hover:text-white
                        relative
                        overflow-hidden
                        transition-all duration-300
                        tech-border-animation
                    ">
                        <span class="relative z-10">ÜRÜNLER</span>
                        <i class="fa-solid fa-caret-down ml-2 text-xs text-gold-dark"></i>

                        {{-- Tech Hover Effect --}}
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-gold-dark/20 to-transparent
                            transform -skew-x-12 translate-x-[-200%] group-hover:translate-x-[200%]
                            transition-transform duration-700"></div>
                    </button>

                    {{-- Mega Menu - Tech Grid --}}
                    <div class="
                        absolute left-0 right-0 top-full mt-0
                        invisible group-hover:visible
                        opacity-0 group-hover:opacity-100
                        transition-all duration-300
                        pointer-events-none group-hover:pointer-events-auto
                    ">
                        <div class="container mx-auto px-4 py-8">
                            <div class="
                                bg-navy-900/95
                                backdrop-blur-xl
                                clip-path-tech
                                p-8
                                border border-gold-dark/30
                                shadow-[0_0_50px_rgba(0,255,255,0.1)]
                                relative
                            ">
                                {{-- Tech Pattern Overlay --}}
                                <div class="absolute inset-0 tech-circuit-pattern opacity-5"></div>

                                <div class="grid grid-cols-4 gap-8 relative z-10">
                                    {{-- Category with Tech Icons --}}
                                    <div>
                                        <h3 class="text-xs font-bold text-gold-dark uppercase tracking-widest mb-4 flex items-center gap-2">
                                            <span class="w-8 h-[2px] bg-gradient-to-r from-gold-dark to-transparent"></span>
                                            FORKLIFT
                                        </h3>
                                        <ul class="space-y-3">
                                            <li>
                                                <a href="#" class="
                                                    flex items-center gap-3 p-3
                                                    text-gray-400 hover:text-white
                                                    border-l-2 border-transparent
                                                    hover:border-gold-dark
                                                    hover:bg-gradient-to-r hover:from-gold-dark/10 hover:to-transparent
                                                    transition-all duration-300
                                                    group/item
                                                ">
                                                    <div class="w-8 h-8 border border-gold-dark/30 flex items-center justify-center
                                                        group-hover/item:border-gold-dark group-hover/item:bg-gold-dark/10">
                                                        <i class="fa-solid fa-battery-full text-xs text-gold-dark"></i>
                                                    </div>
                                                    <div>
                                                        <span class="block">Akülü Forklift</span>
                                                        <span class="text-[10px] text-gray-600">48V System</span>
                                                    </div>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="
                                                    flex items-center gap-3 p-3
                                                    text-gray-400 hover:text-white
                                                    border-l-2 border-transparent
                                                    hover:border-gold-dark
                                                    hover:bg-gradient-to-r hover:from-gold-dark/10 hover:to-transparent
                                                    transition-all duration-300
                                                    group/item
                                                ">
                                                    <div class="w-8 h-8 border border-gold-dark/30 flex items-center justify-center
                                                        group-hover/item:border-gold-dark group-hover/item:bg-gold-dark/10">
                                                        <i class="fa-solid fa-gas-pump text-xs text-gold-dark"></i>
                                                    </div>
                                                    <div>
                                                        <span class="block">Dizel Forklift</span>
                                                        <span class="text-[10px] text-gray-600">Euro 5</span>
                                                    </div>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>

                                    {{-- Tech Stats --}}
                                    <div class="border-l border-gold-dark/20 pl-8">
                                        <h3 class="text-xs font-bold text-cyan-400 uppercase tracking-widest mb-4">
                                            CANLI VERİLER
                                        </h3>
                                        <div class="space-y-4">
                                            <div>
                                                <div class="flex justify-between text-xs mb-1">
                                                    <span class="text-gray-500">Stok Durumu</span>
                                                    <span class="text-green-400">%87</span>
                                                </div>
                                                <div class="h-1 bg-navy-800 overflow-hidden">
                                                    <div class="h-full bg-gradient-to-r from-green-400 to-cyan-400" style="width: 87%"></div>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="flex justify-between text-xs mb-1">
                                                    <span class="text-gray-500">Teslimat Hızı</span>
                                                    <span class="text-gold-dark">2-3 Gün</span>
                                                </div>
                                                <div class="h-1 bg-navy-800 overflow-hidden">
                                                    <div class="h-full bg-gradient-to-r from-gold-dark to-gold-light animate-pulse" style="width: 95%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Featured Product - Tech Card --}}
                                    <div class="col-span-2">
                                        <h3 class="text-xs font-bold text-gold-dark uppercase tracking-widest mb-4">
                                            ÖNE ÇIKAN TEKNOLOJİ
                                        </h3>
                                        <div class="
                                            bg-gradient-to-br from-navy-800 to-navy-900
                                            border border-gold-dark/20
                                            p-4
                                            relative
                                            overflow-hidden
                                            group/card
                                        ">
                                            {{-- Tech Corner Decorations --}}
                                            <div class="absolute top-0 left-0 w-4 h-4 border-t-2 border-l-2 border-gold-dark"></div>
                                            <div class="absolute top-0 right-0 w-4 h-4 border-t-2 border-r-2 border-gold-dark"></div>
                                            <div class="absolute bottom-0 left-0 w-4 h-4 border-b-2 border-l-2 border-gold-dark"></div>
                                            <div class="absolute bottom-0 right-0 w-4 h-4 border-b-2 border-r-2 border-gold-dark"></div>

                                            <div class="flex gap-4">
                                                <img src="/product.jpg" class="w-32 h-32 object-cover">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <span class="text-[10px] text-cyan-400 animate-pulse">● ONLINE</span>
                                                        <span class="text-[10px] text-gray-500">ID: FRK-2024-001</span>
                                                    </div>
                                                    <h4 class="font-bold text-white mb-2">Quantum Forklift Pro</h4>
                                                    <div class="grid grid-cols-2 gap-4 text-xs">
                                                        <div>
                                                            <span class="text-gray-500">Kapasite</span>
                                                            <span class="block text-gold-dark font-mono">3000 KG</span>
                                                        </div>
                                                        <div>
                                                            <span class="text-gray-500">Güç</span>
                                                            <span class="block text-gold-dark font-mono">48V / 775Ah</span>
                                                        </div>
                                                    </div>
                                                    <div class="mt-3 flex items-center justify-between">
                                                        <span class="text-2xl font-bold gold-gradient bg-clip-text text-transparent">
                                                            ₺45.000
                                                        </span>
                                                        <button class="px-4 py-1 border border-gold-dark text-gold-dark text-xs hover:bg-gold-dark hover:text-navy-950 transition-all">
                                                            İNCELE →
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Scanning Effect --}}
                                            <div class="absolute inset-0 bg-gradient-to-b from-transparent via-gold-dark/10 to-transparent
                                                transform translate-y-[-100%] group-hover/card:translate-y-[100%]
                                                transition-transform duration-2000"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>

                <li><a href="#" class="px-4 py-2 text-gray-300 hover:text-white transition-colors">HAKKIMIZDA</a></li>
                <li><a href="#" class="px-4 py-2 text-gray-300 hover:text-white transition-colors">TEKNOLOJİ</a></li>
                <li><a href="#" class="px-4 py-2 text-gray-300 hover:text-white transition-colors">DESTEK</a></li>

                {{-- CTA Button - Tech Style --}}
                <li>
                    <a href="/teklif-al" class="
                        ml-4 px-8 py-3
                        bg-gradient-to-r from-gold-dark to-gold-light
                        text-navy-950 font-bold
                        clip-path-button
                        relative
                        overflow-hidden
                        hover:shadow-[0_0_30px_rgba(212,175,55,0.5)]
                        transition-all duration-300
                        group
                    ">
                        <span class="relative z-10">SİSTEM ENTEGRASYONU</span>
                        {{-- Glitch Effect --}}
                        <div class="absolute inset-0 bg-gradient-to-r from-cyan-400/20 via-transparent to-cyan-400/20
                            transform scale-x-0 group-hover:scale-x-100
                            transition-transform duration-500"></div>
                    </a>
                </li>
            </ul>

            {{-- System Status Indicator --}}
            <div class="flex items-center gap-4">
                <div class="hidden lg:flex items-center gap-2 px-4 py-2 bg-navy-800/50 border border-gold-dark/20">
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    <span class="text-[10px] text-gray-400 font-mono">SYSTEM ONLINE</span>
                </div>

                {{-- Menu Toggle - Tech Style --}}
                <button class="lg:hidden relative w-8 h-8">
                    <span class="absolute inset-0 border border-gold-dark"></span>
                    <span class="absolute inset-1 border border-gold-dark/50 rotate-45"></span>
                    <i class="fa-solid fa-bars text-white relative z-10"></i>
                </button>
            </div>
        </div>
    </div>
</nav>
```

### 2️⃣ HERO SECTION (Tech Style)

```blade
{{-- Modern Tech Hero --}}
<section class="
    min-h-screen
    bg-navy-950
    flex items-center
    relative
    overflow-hidden
">
    {{-- Animated Tech Background --}}
    <div class="absolute inset-0">
        {{-- Moving Grid --}}
        <div class="tech-grid-animated"></div>
        {{-- Data Stream Effect --}}
        <div class="data-stream-vertical"></div>
        <div class="data-stream-horizontal"></div>
    </div>

    {{-- Hexagon Pattern --}}
    <div class="absolute inset-0 hexagon-pattern opacity-5"></div>

    <div class="container mx-auto px-4 relative z-10">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            {{-- Content --}}
            <div>
                {{-- Tech Badge --}}
                <div class="inline-flex items-center gap-3 mb-8 px-4 py-2 border border-gold-dark/30 bg-navy-900/50 backdrop-blur">
                    <div class="flex gap-1">
                        <span class="w-1 h-4 bg-gold-dark animate-pulse"></span>
                        <span class="w-1 h-4 bg-gold-dark/70 animate-pulse delay-100"></span>
                        <span class="w-1 h-4 bg-gold-dark/40 animate-pulse delay-200"></span>
                    </div>
                    <span class="text-xs font-mono text-gold-dark uppercase">NEXT-GEN SOLUTIONS</span>
                    <span class="text-[10px] text-gray-500">v2.0.24</span>
                </div>

                {{-- Title with Glitch Effect --}}
                <h1 class="text-5xl lg:text-7xl font-black mb-8 uppercase relative">
                    <span class="block text-white mb-2 glitch-text" data-text="TÜRKİYE'NİN">
                        TÜRKİYE'NİN
                    </span>
                    <span class="block gold-gradient bg-clip-text text-transparent relative">
                        İSTİF PAZARI
                        <span class="absolute -right-2 -top-2 text-xs text-cyan-400 font-mono">™</span>
                    </span>
                </h1>

                {{-- Tech Description --}}
                <div class="mb-12 p-4 border-l-2 border-gold-dark/30 bg-gradient-to-r from-gold-dark/5 to-transparent">
                    <p class="text-lg text-gray-300 font-light">
                        <span class="text-gold-dark font-mono">AI-Powered</span> istif çözümleri.
                        <span class="text-cyan-400">%99.9 uptime</span> garantisi.
                    </p>
                </div>

                {{-- CTA Buttons - Tech Style --}}
                <div class="flex flex-wrap gap-4 mb-16">
                    <a href="#" class="
                        group relative px-8 py-4
                        bg-gradient-to-r from-gold-dark to-gold-light
                        text-navy-950 font-bold
                        clip-path-button
                        hover:shadow-[0_0_40px_rgba(212,175,55,0.5)]
                        transition-all duration-300
                        overflow-hidden
                    ">
                        <span class="relative z-10 flex items-center gap-2">
                            <i class="fa-solid fa-microchip"></i>
                            SİSTEME BAĞLAN
                        </span>
                        {{-- Scan Effect --}}
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent
                            transform -skew-x-12 translate-x-[-200%] group-hover:translate-x-[200%]
                            transition-transform duration-1000"></div>
                    </a>

                    <a href="#" class="
                        px-8 py-4
                        border border-cyan-400/30
                        text-cyan-400
                        hover:bg-cyan-400/10
                        hover:border-cyan-400/50
                        transition-all duration-300
                        relative
                        overflow-hidden
                    ">
                        <span class="flex items-center gap-2">
                            <i class="fa-solid fa-play"></i>
                            3D DEMO
                        </span>
                    </a>
                </div>

                {{-- Tech Stats Grid --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="
                        border border-gold-dark/20
                        bg-navy-900/30
                        p-4
                        relative
                        overflow-hidden
                        group
                    ">
                        {{-- Corner Accent --}}
                        <div class="absolute top-0 right-0 w-0 h-0 border-t-[20px] border-t-gold-dark/20 border-l-[20px] border-l-transparent"></div>

                        <div class="text-2xl font-mono text-gold-dark mb-1">500+</div>
                        <div class="text-[10px] text-gray-500 uppercase tracking-wider">Active Units</div>

                        {{-- Pulse Line --}}
                        <div class="absolute bottom-0 left-0 right-0 h-[1px] bg-gradient-to-r from-transparent via-gold-dark to-transparent
                            transform scale-x-0 group-hover:scale-x-100 transition-transform duration-1000"></div>
                    </div>

                    <div class="
                        border border-gold-dark/20
                        bg-navy-900/30
                        p-4
                        relative
                        overflow-hidden
                        group
                    ">
                        <div class="absolute top-0 right-0 w-0 h-0 border-t-[20px] border-t-cyan-400/20 border-l-[20px] border-l-transparent"></div>
                        <div class="text-2xl font-mono text-cyan-400 mb-1">24/7</div>
                        <div class="text-[10px] text-gray-500 uppercase tracking-wider">Support AI</div>
                        <div class="absolute bottom-0 left-0 right-0 h-[1px] bg-gradient-to-r from-transparent via-cyan-400 to-transparent
                            transform scale-x-0 group-hover:scale-x-100 transition-transform duration-1000"></div>
                    </div>

                    <div class="
                        border border-gold-dark/20
                        bg-navy-900/30
                        p-4
                        relative
                        overflow-hidden
                        group
                    ">
                        <div class="absolute top-0 right-0 w-0 h-0 border-t-[20px] border-t-green-400/20 border-l-[20px] border-l-transparent"></div>
                        <div class="text-2xl font-mono text-green-400 mb-1">99.9%</div>
                        <div class="text-[10px] text-gray-500 uppercase tracking-wider">Uptime</div>
                        <div class="absolute bottom-0 left-0 right-0 h-[1px] bg-gradient-to-r from-transparent via-green-400 to-transparent
                            transform scale-x-0 group-hover:scale-x-100 transition-transform duration-1000"></div>
                    </div>

                    <div class="
                        border border-gold-dark/20
                        bg-navy-900/30
                        p-4
                        relative
                        overflow-hidden
                        group
                    ">
                        <div class="absolute top-0 right-0 w-0 h-0 border-t-[20px] border-t-gold-light/20 border-l-[20px] border-l-transparent"></div>
                        <div class="text-2xl font-mono text-gold-light mb-1">5★</div>
                        <div class="text-[10px] text-gray-500 uppercase tracking-wider">Rating</div>
                        <div class="absolute bottom-0 left-0 right-0 h-[1px] bg-gradient-to-r from-transparent via-gold-light to-transparent
                            transform scale-x-0 group-hover:scale-x-100 transition-transform duration-1000"></div>
                    </div>
                </div>
            </div>

            {{-- Hero Visual - Tech Style --}}
            <div class="relative">
                {{-- HUD Frame --}}
                <div class="absolute inset-0 hud-frame">
                    <div class="absolute top-0 left-0 w-20 h-20 border-t-2 border-l-2 border-gold-dark"></div>
                    <div class="absolute top-0 right-0 w-20 h-20 border-t-2 border-r-2 border-gold-dark"></div>
                    <div class="absolute bottom-0 left-0 w-20 h-20 border-b-2 border-l-2 border-gold-dark"></div>
                    <div class="absolute bottom-0 right-0 w-20 h-20 border-b-2 border-r-2 border-gold-dark"></div>
                </div>

                {{-- Main Image with Hologram Effect --}}
                <div class="relative">
                    <img src="/hero.png" class="w-full relative z-10 hologram-effect">

                    {{-- Tech Overlay Info --}}
                    <div class="absolute top-4 left-4 bg-navy-900/90 backdrop-blur border border-gold-dark/30 p-3">
                        <div class="text-[10px] text-gray-500 mb-1">MODEL SCAN</div>
                        <div class="text-xs font-mono text-gold-dark">FRK-QUANTUM-3000</div>
                        <div class="flex gap-2 mt-2">
                            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse delay-100"></span>
                            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse delay-200"></span>
                        </div>
                    </div>

                    {{-- Data Points --}}
                    <div class="absolute top-1/3 right-4 flex items-center gap-2">
                        <div class="w-3 h-3 bg-gold-dark rounded-full animate-ping"></div>
                        <div class="bg-navy-900/90 backdrop-blur border border-gold-dark/30 px-3 py-1">
                            <span class="text-[10px] text-gold-dark font-mono">3000 KG</span>
                        </div>
                    </div>

                    <div class="absolute bottom-1/3 left-4 flex items-center gap-2">
                        <div class="w-3 h-3 bg-cyan-400 rounded-full animate-ping"></div>
                        <div class="bg-navy-900/90 backdrop-blur border border-cyan-400/30 px-3 py-1">
                            <span class="text-[10px] text-cyan-400 font-mono">48V SYSTEM</span>
                        </div>
                    </div>
                </div>

                {{-- Rotating Tech Ring --}}
                <div class="absolute -inset-10 border border-gold-dark/10 rounded-full animate-spin-slow"></div>
                <div class="absolute -inset-20 border border-gold-dark/5 rounded-full animate-spin-slow-reverse"></div>
            </div>
        </div>
    </div>
</section>
```

### 3️⃣ PRODUCT CARDS (Tech Style)

```blade
{{-- Modern Tech Product Card --}}
<div class="group relative">
    <div class="
        bg-navy-900/50
        backdrop-blur
        border border-gold-dark/20
        clip-path-card
        overflow-hidden
        hover:border-gold-dark/50
        transition-all duration-500
        relative
    ">
        {{-- Tech Corner Decorations --}}
        <div class="absolute top-0 left-0 w-8 h-8 border-t-2 border-l-2 border-gold-dark z-10"></div>
        <div class="absolute top-0 right-0 w-8 h-8 border-t-2 border-r-2 border-gold-dark z-10"></div>

        {{-- Status Badge --}}
        <div class="absolute top-4 left-4 z-20 flex items-center gap-2 px-3 py-1 bg-navy-950/90 backdrop-blur border border-green-400/30">
            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
            <span class="text-[10px] font-mono text-green-400">IN STOCK</span>
        </div>

        {{-- Product ID --}}
        <div class="absolute top-4 right-4 z-20 text-[10px] font-mono text-gray-500">
            #PRD-2024-087
        </div>

        {{-- Image with Scan Lines --}}
        <div class="relative h-64 bg-gradient-to-br from-navy-800 to-navy-900 overflow-hidden">
            <img src="/product.jpg" class="w-full h-full object-cover">

            {{-- Scan Line Animation --}}
            <div class="absolute inset-0 scan-lines"></div>

            {{-- Hover Data Overlay --}}
            <div class="absolute inset-0 bg-navy-950/90 backdrop-blur opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex items-center justify-center">
                <div class="text-center">
                    <div class="mb-4">
                        <div class="inline-flex items-center justify-center w-16 h-16 border-2 border-gold-dark rounded-full">
                            <i class="fa-solid fa-cube-3d text-gold-dark text-xl"></i>
                        </div>
                    </div>
                    <button class="px-6 py-2 border border-gold-dark text-gold-dark text-xs font-mono hover:bg-gold-dark hover:text-navy-950 transition-all">
                        3D MODEL GÖRÜNTÜLE
                    </button>
                </div>
            </div>
        </div>

        {{-- Content --}}
        <div class="p-6">
            {{-- Category with Tech Icon --}}
            <div class="flex items-center gap-2 mb-3">
                <div class="w-6 h-6 border border-gold-dark/30 flex items-center justify-center">
                    <i class="fa-solid fa-microchip text-[10px] text-gold-dark"></i>
                </div>
                <span class="text-[10px] font-mono text-gray-500 uppercase">QUANTUM SERIES</span>
            </div>

            {{-- Title --}}
            <h3 class="text-lg font-bold text-white mb-4">
                Akülü Forklift <span class="text-gold-dark">Q-3000</span>
            </h3>

            {{-- Tech Specs Grid --}}
            <div class="grid grid-cols-2 gap-3 mb-4 p-3 bg-navy-800/30 border border-gold-dark/10">
                <div>
                    <div class="text-[10px] text-gray-500 uppercase">Power</div>
                    <div class="font-mono text-sm text-gold-dark">48V</div>
                </div>
                <div>
                    <div class="text-[10px] text-gray-500 uppercase">Load</div>
                    <div class="font-mono text-sm text-gold-dark">3000kg</div>
                </div>
                <div>
                    <div class="text-[10px] text-gray-500 uppercase">Height</div>
                    <div class="font-mono text-sm text-cyan-400">6.0m</div>
                </div>
                <div>
                    <div class="text-[10px] text-gray-500 uppercase">Speed</div>
                    <div class="font-mono text-sm text-cyan-400">20km/h</div>
                </div>
            </div>

            {{-- Performance Bar --}}
            <div class="mb-4">
                <div class="flex justify-between text-[10px] mb-1">
                    <span class="text-gray-500">PERFORMANCE</span>
                    <span class="text-gold-dark font-mono">95%</span>
                </div>
                <div class="h-2 bg-navy-800 relative overflow-hidden">
                    <div class="absolute inset-y-0 left-0 bg-gradient-to-r from-gold-dark to-gold-light" style="width: 95%"></div>
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent
                        transform translate-x-[-100%] animate-scan"></div>
                </div>
            </div>

            {{-- Price with Digital Effect --}}
            <div class="flex items-end justify-between mb-6">
                <div>
                    <div class="text-xs text-gray-500 line-through font-mono">₺50.000</div>
                    <div class="text-2xl font-bold font-mono gold-gradient bg-clip-text text-transparent">
                        ₺45.000
                    </div>
                </div>
                <div class="flex items-center gap-1">
                    <span class="text-[10px] text-green-400">-10%</span>
                    <i class="fa-solid fa-arrow-trend-down text-green-400 text-xs"></i>
                </div>
            </div>

            {{-- Actions - Tech Buttons --}}
            <div class="grid grid-cols-2 gap-3">
                <button class="
                    py-3 px-4
                    bg-gradient-to-r from-gold-dark to-gold-light
                    text-navy-950 font-bold text-xs
                    clip-path-button
                    hover:shadow-[0_0_20px_rgba(212,175,55,0.5)]
                    transition-all duration-300
                    relative
                    overflow-hidden
                    group/btn
                ">
                    <span class="relative z-10">SATIN AL</span>
                    <div class="absolute inset-0 bg-white/20 transform scale-x-0 group-hover/btn:scale-x-100 transition-transform duration-300"></div>
                </button>

                <button class="
                    py-3 px-4
                    border border-cyan-400/30
                    text-cyan-400 text-xs
                    hover:bg-cyan-400/10
                    hover:border-cyan-400/50
                    transition-all duration-300
                ">
                    <i class="fa-solid fa-chart-line mr-1"></i>
                    ANALİZ
                </button>
            </div>
        </div>

        {{-- Bottom Tech Line --}}
        <div class="absolute bottom-0 left-0 right-0 h-[1px] bg-gradient-to-r from-transparent via-gold-dark to-transparent"></div>
    </div>
</div>
```

### 4️⃣ FOOTER (Tech Style)

```blade
{{-- Modern Tech Footer --}}
<footer class="relative bg-navy-950 pt-20 pb-10 overflow-hidden">
    {{-- Tech Grid Background --}}
    <div class="absolute inset-0 tech-grid opacity-5"></div>

    {{-- Top Tech Line --}}
    <div class="absolute top-0 left-0 right-0">
        <div class="h-[1px] bg-gradient-to-r from-transparent via-gold-dark to-transparent"></div>
        <div class="flex justify-center -mt-3">
            <div class="px-6 py-1 bg-navy-950 border border-gold-dark/30">
                <span class="text-[10px] font-mono text-gold-dark">SYSTEM FOOTER v1.0</span>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 relative z-10 mt-12">
        {{-- Main Grid --}}
        <div class="grid lg:grid-cols-12 gap-8 mb-12">
            {{-- Brand Column --}}
            <div class="lg:col-span-4">
                <div class="mb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 border-2 border-gold-dark rotate-45 relative">
                            <div class="absolute inset-2 border border-gold-light/30 animate-pulse"></div>
                        </div>
                        <span class="text-2xl font-black text-white">
                            iX<span class="text-gold-dark">TIF</span>
                        </span>
                    </div>
                    <p class="text-sm text-gray-400 font-light">
                        Next-generation forklift technology.
                    </p>
                </div>

                {{-- System Status --}}
                <div class="p-4 bg-navy-900/30 border border-gold-dark/20">
                    <div class="text-[10px] font-mono text-gray-500 mb-3">SYSTEM STATUS</div>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400">API</span>
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                                <span class="text-[10px] text-green-400 font-mono">ONLINE</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400">Database</span>
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                                <span class="text-[10px] text-green-400 font-mono">CONNECTED</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400">CDN</span>
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-gold-dark rounded-full animate-pulse"></span>
                                <span class="text-[10px] text-gold-dark font-mono">OPTIMIZED</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Links - Tech Grid --}}
            <div class="lg:col-span-4">
                <div class="text-[10px] font-mono text-gray-500 mb-6">QUICK ACCESS</div>
                <div class="grid grid-cols-2 gap-4">
                    <a href="#" class="
                        p-3 bg-navy-900/20 border border-gold-dark/10
                        hover:bg-navy-900/40 hover:border-gold-dark/30
                        transition-all duration-300
                        group
                    ">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-cube text-gold-dark text-sm"></i>
                            <span class="text-xs text-gray-300 group-hover:text-white">Ürünler</span>
                        </div>
                    </a>
                    <a href="#" class="
                        p-3 bg-navy-900/20 border border-gold-dark/10
                        hover:bg-navy-900/40 hover:border-gold-dark/30
                        transition-all duration-300
                        group
                    ">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-rocket text-gold-dark text-sm"></i>
                            <span class="text-xs text-gray-300 group-hover:text-white">Teknoloji</span>
                        </div>
                    </a>
                    <a href="#" class="
                        p-3 bg-navy-900/20 border border-gold-dark/10
                        hover:bg-navy-900/40 hover:border-gold-dark/30
                        transition-all duration-300
                        group
                    ">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-headset text-gold-dark text-sm"></i>
                            <span class="text-xs text-gray-300 group-hover:text-white">Destek</span>
                        </div>
                    </a>
                    <a href="#" class="
                        p-3 bg-navy-900/20 border border-gold-dark/10
                        hover:bg-navy-900/40 hover:border-gold-dark/30
                        transition-all duration-300
                        group
                    ">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-file-code text-gold-dark text-sm"></i>
                            <span class="text-xs text-gray-300 group-hover:text-white">API Docs</span>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Contact Terminal --}}
            <div class="lg:col-span-4">
                <div class="text-[10px] font-mono text-gray-500 mb-6">CONTACT TERMINAL</div>
                <div class="bg-navy-900/30 border border-gold-dark/20 p-4 font-mono text-xs">
                    <div class="text-green-400 mb-2">$ contact --info</div>
                    <div class="text-gray-400 ml-4">
                        <div>→ phone: 0850 123 45 67</div>
                        <div>→ email: tech@ixtif.com</div>
                        <div>→ location: Istanbul, TR</div>
                        <div>→ support: 24/7 available</div>
                    </div>
                    <div class="flex items-center mt-3">
                        <span class="text-gold-dark animate-pulse">_</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom Bar --}}
        <div class="pt-8 border-t border-gold-dark/20">
            <div class="flex flex-col lg:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-4">
                    <span class="text-[10px] font-mono text-gray-500">© 2024 iXTIF TECHNOLOGIES</span>
                    <span class="text-gray-600">|</span>
                    <span class="text-[10px] font-mono text-cyan-400">BUILD: 2024.10.26</span>
                </div>

                <div class="flex items-center gap-6">
                    <a href="#" class="text-[10px] font-mono text-gray-500 hover:text-gold-dark transition-colors">
                        PRIVACY.md
                    </a>
                    <a href="#" class="text-[10px] font-mono text-gray-500 hover:text-gold-dark transition-colors">
                        TERMS.pdf
                    </a>
                    <a href="#" class="text-[10px] font-mono text-gray-500 hover:text-gold-dark transition-colors">
                        API_DOCS.json
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom Tech Decoration --}}
    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-gold-dark/50 to-transparent"></div>
</footer>
```

---

## 🎨 TECH ANIMATIONS CSS

```css
/* Tech Grid Animation */
@keyframes tech-grid-move {
    0% { transform: translateY(0); }
    100% { transform: translateY(50px); }
}

.tech-grid-animated {
    background-image:
        linear-gradient(rgba(212, 175, 55, 0.1) 1px, transparent 1px),
        linear-gradient(90deg, rgba(212, 175, 55, 0.1) 1px, transparent 1px);
    background-size: 50px 50px;
    animation: tech-grid-move 10s linear infinite;
}

/* Data Stream Effect */
.data-stream-vertical {
    position: absolute;
    top: -100%;
    left: 20%;
    width: 1px;
    height: 200%;
    background: linear-gradient(to bottom, transparent, #d4af37, transparent);
    animation: data-flow-vertical 8s linear infinite;
}

@keyframes data-flow-vertical {
    0% { transform: translateY(-100%); }
    100% { transform: translateY(100%); }
}

/* Scan Lines Effect */
.scan-lines::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, rgba(212, 175, 55, 0.5), transparent);
    animation: scan-line 3s linear infinite;
}

@keyframes scan-line {
    0% { transform: translateY(0); }
    100% { transform: translateY(256px); }
}

/* Glitch Text Effect */
.glitch-text {
    position: relative;
}

.glitch-text::before,
.glitch-text::after {
    content: attr(data-text);
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.glitch-text::before {
    animation: glitch-1 0.5s infinite;
    color: #00ffff;
    z-index: -1;
}

.glitch-text::after {
    animation: glitch-2 0.5s infinite;
    color: #ff00ff;
    z-index: -2;
}

@keyframes glitch-1 {
    0%, 100% { clip: rect(0, 9999px, 0, 0); }
    25% { clip: rect(random(100)px, 9999px, random(100)px, 0); }
    50% { clip: rect(random(100)px, 9999px, random(100)px, 0); }
    75% { clip: rect(random(100)px, 9999px, random(100)px, 0); }
}

/* Hologram Effect */
.hologram-effect {
    filter: contrast(1.1);
    animation: hologram 4s ease-in-out infinite;
}

@keyframes hologram {
    0%, 100% {
        filter: contrast(1.1) brightness(1);
    }
    50% {
        filter: contrast(1.15) brightness(1.1) hue-rotate(10deg);
    }
}

/* Clip Path Shapes */
.clip-path-button {
    clip-path: polygon(0 0, calc(100% - 10px) 0, 100% 50%, calc(100% - 10px) 100%, 0 100%);
}

.clip-path-card {
    clip-path: polygon(0 0, calc(100% - 20px) 0, 100% 20px, 100% 100%, 20px 100%, 0 calc(100% - 20px));
}

.clip-path-tech {
    clip-path: polygon(20px 0, 100% 0, 100% calc(100% - 20px), calc(100% - 20px) 100%, 0 100%, 0 20px);
}

/* Spinning Animations */
@keyframes spin-slow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-spin-slow {
    animation: spin-slow 30s linear infinite;
}

.animate-spin-slow-reverse {
    animation: spin-slow 25s linear infinite reverse;
}

/* Tech Border Animation */
.tech-border-animation {
    position: relative;
}

.tech-border-animation::before {
    content: '';
    position: absolute;
    inset: 0;
    padding: 1px;
    background: linear-gradient(90deg, transparent, #d4af37, transparent);
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: exclude;
    mask-composite: exclude;
    opacity: 0;
    transition: opacity 0.3s;
}

.tech-border-animation:hover::before {
    opacity: 1;
}
```

---

## 🚀 ÖZET

**Modern Tech Dark Mode Özellikleri:**
- ⚡ **Tech/Sci-fi yaklaşım** - Futuristik tasarım
- 🔲 **Sharp edges & clip-paths** - Keskin köşeler
- 💻 **Mono fonts & terminal style** - Kod/terminal görünümü
- 🌐 **Data visualizations** - Grafikler, progress bars
- 🎯 **HUD elements** - Holografik UI elementleri
- 📊 **Live stats & indicators** - Canlı veri göstergeleri
- 🔄 **Scan & glitch effects** - Teknolojik animasyonlar
- 🎨 **Navy + Gold + Cyan** accent kombinasyonu

Bu alternatif **teknolojik ve futuristik** bir görünüm sunuyor. Gaming/tech sektörü hissi veriyor.