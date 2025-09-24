/**
 * AI Content Generation System - Global Universal Editor Support
 * Tüm modüllerde çalışır (Page, Blog, Portfolio, etc.)
 * HugeRTE + TinyMCE + Textarea universal detection
 */

class AIContentGenerationSystem {
    constructor(config = {}) {
        this.config = {
            module: config.module || 'page',
            baseUrl: config.baseUrl || '/admin',
            csrfToken: document.querySelector('meta[name="csrf-token"]')?.content || '',
            targetComponent: config.targetComponent || null,
            ...config
        };

        this.modal = null;
        this.jobId = null;
        this.progressInterval = null;
        this.startTime = null;
        this.isGenerating = false;

        // 🆕 File Upload Properties
        this.uploadedFiles = [];
        this.analysisResults = {};

        this.init();
    }

    /**
     * Config güncelle (modal açılırken)
     */
    configure(newConfig = {}) {
        this.config = {
            ...this.config,
            ...newConfig
        };
        console.log('🔧 AI Content System config güncellendi:', this.config);
    }

    /**
     * Sistemi başlat
     */
    async init() {
        console.log('🚀 AI Content Generation System başlatılıyor...');

        // Modal'ı bul
        this.modal = document.getElementById('aiContentModal');

        if (!this.modal) {
            console.warn('⚠️ AI Content Modal bulunamadı');
            return;
        }

        this.setupEventListeners();
        console.log('✅ AI Content Generation System hazır!');
    }

    /**
     * Event listener'ları kur
     */
    setupEventListeners() {
        // Generate button
        const generateBtn = document.getElementById('startGeneration');
        if (generateBtn) {
            // DEBOUNCE VE DUPLICATE ÖNLEME İÇİN ONCE EVENT HANDLER KULLAN
            generateBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();

                // Butonu anında devre dışı bırak
                if (generateBtn.disabled) {
                    console.warn('⚠️ Button zaten disabled!');
                    return;
                }

                // Double-click önleme
                if (this.isGenerating) {
                    console.warn('⚠️ Generation zaten aktif!');
                    return;
                }

                // Butonu hemen disable et
                generateBtn.disabled = true;
                generateBtn.classList.add('disabled', 'opacity-50');

                // startGeneration'ı çağır
                this.startGeneration().catch(error => {
                    console.error('Generation error:', error);
                    // Hata durumunda butonu tekrar enable et
                    generateBtn.disabled = false;
                    generateBtn.classList.remove('disabled', 'opacity-50');
                });
            }, { once: false }); // Her click için çalışsın ama kontrol etsin
        }

        // Cancel button
        const cancelBtn = document.getElementById('cancelButton');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => this.closeModal());
        }

        // Modal events
        if (this.modal) {
            this.modal.addEventListener('show.bs.modal', () => {
                console.log('📖 AI Content modal açıldı');
            });

            this.modal.addEventListener('hidden.bs.modal', () => {
                this.resetModal();
                // ÖNEMLI: analysisResults'u temizleme! PDF analizi bir sonraki açılışta kullanılabilir olmalı
                // this.analysisResults = {}; // DEVRE DIŞI BIRAKILIYOR
                console.log('📖 AI Content modal kapandı, PDF analizi korundu');
            });

            // Modal kapatılmadan ÖNCE focus'u düzelt
            this.modal.addEventListener('hide.bs.modal', () => {
                // Ultra agresif focus management
                const activeEl = document.activeElement;
                if (activeEl && (
                    activeEl.id === 'cancelButton' ||
                    activeEl.closest('#aiContentModal') ||
                    activeEl.closest('.modal')
                )) {
                    // Focus'u body'ye taşı
                    document.body.focus();

                    // Backup: Focus'u modal dışındaki ilk focusable element'e taşı
                    setTimeout(() => {
                        const focusable = document.querySelector('input:not([disabled]), button:not([disabled]), [tabindex]:not([tabindex="-1"]):not([disabled])');
                        if (focusable && !focusable.closest('.modal')) {
                            focusable.focus();
                        }
                    }, 50);
                }

                // Backdrop temizleme - global function kullan
                if (typeof window.cleanModalBackdrop === 'function') {
                    setTimeout(() => {
                        window.cleanModalBackdrop();
                    }, 100);
                }
            });
        }

        console.log('📡 Event listeners kuruldu');
    }

    /**
     * İçerik üretimi başlat - Enhanced with file support
     */
    async startGeneration() {
        // DUPLICATE ÖNLEME: Zaten generation yapılıyorsa durdur
        if (this.isGenerating) {
            console.warn('⚠️ İçerik üretimi zaten devam ediyor!');
            return;
        }

        // ÖNCE: Aktif progress interval varsa temizle (duplicate job önleme)
        if (this.progressInterval) {
            console.log('⚠️ Önceki progress tracking durduruluyor...');
            clearInterval(this.progressInterval);
            this.progressInterval = null;
        }

        let contentTopic = document.getElementById('contentTopic')?.value?.trim();
        const replaceExisting = document.getElementById('replaceExisting')?.checked || false;

        // GLOBAL STORAGE'DAN AL - KAYBOLMASIN!
        if (window.aiPdfAnalysisResults && Object.keys(window.aiPdfAnalysisResults).length > 0) {
            this.analysisResults = window.aiPdfAnalysisResults;
            console.log('📦 Analysis results restored from global storage');
        }

        // File analysis varsa onu da kontrol et
        const hasFileAnalysis = this.analysisResults && Object.keys(this.analysisResults).length > 0;

        // 🚨 ZORUNLU KONTROL: En az biri dolu olmalı - yazı alanı VEYA dosya
        const hasContentTopic = contentTopic && contentTopic.length > 0;

        if (!hasContentTopic && !hasFileAnalysis) {
            console.warn('⚠️ Hem yazı alanı hem dosya alanı boş!');
            this.showInlineWarning('Lütfen içerik konusu yazın veya dosya yükleyin!');
            // BUTON DISABLE OLMAYACAK - sadece uyarı ver ve devam et
            this.setGeneratingState(false); // Buton'u tekrar enable et
            return;
        }

        // Eğer PDF analizi devam ediyorsa ve henüz sonuç yoksa, DAİMA beklet (brief olsa bile)
        if (!hasFileAnalysis && this.analysisId) {
            console.warn('⏳ PDF analizi devam ediyor; içerik üretimi beklemeye alındı (brief olsa bile).');
            this.pendingAutoGenerate = true;
            // ÖNEMLI: isGenerating false kalsın, sadece UI'da waiting göster
            this.updateProgress(5, 'PDF analizi sürüyor, bittiğinde otomatik başlayacak...', 'waiting');

            // Progress göster ama generating state'i set etme
            const progressArea = document.getElementById('contentProgress');
            if (progressArea) progressArea.style.display = 'block';

            return;
        }

        console.log('🔍 ENHANCED DEBUG startGeneration:', {
            contentTopic,
            hasContentTopic,
            hasFileAnalysis,
            validationPassed: hasContentTopic || hasFileAnalysis,
            analysisResults: this.analysisResults,
            analysisResultsKeys: this.analysisResults ? Object.keys(this.analysisResults) : [],
            analysisResultsType: typeof this.analysisResults,
            analysisResultsStringified: this.analysisResults ? JSON.stringify(this.analysisResults).substring(0, 500) : 'null',
            // Ekstra debug bilgileri
            analysisResultsLength: this.analysisResults ? Object.keys(this.analysisResults).length : 0,
            hasContent: this.analysisResults && this.analysisResults.content ? 'YES' : 'NO',
            fileType: this.analysisResults && this.analysisResults.file_type ? this.analysisResults.file_type : 'NONE',
            willSendToBackend: this.analysisResults && Object.keys(this.analysisResults).length > 0 ? 'YES' : 'NO'
        });

        // 🆕 VARSAYILAN PDF PROMPT - Eğer sadece PDF yüklendi ve prompt girilmediyse
        if (!contentTopic && hasFileAnalysis) {
            // PDF'in türüne göre otomatik prompt oluştur
            const fileType = this.analysisResults.file_type || 'pdf';
            const isLayoutPreserve = this.getAnalysisType() === 'layout_preserve';

            if (fileType.toLowerCase() === 'pdf') {
                // 🚀 ULTRA PREMIUM PDF→LANDING CONVERTER - DIRECT & SIMPLE
                contentTopic = `⚠️ HİÇBİR AÇIKLAMA/YORUM YAPMA! DİREKT HTML KOD ÜRETİLECEK!

PDF → ULTRA PREMIUM LANDING PAGE

STEP 1: HERO SECTION (Full screen)
<section class="relative min-h-screen flex items-center py-20 lg:py-40 bg-gradient-to-br from-orange-500 via-amber-500 to-yellow-600">
  <div class="max-w-7xl mx-auto px-4 lg:px-8 text-center text-white">
    <h1 class="text-4xl lg:text-8xl font-black tracking-tighter mb-6 lg:mb-8">PDF'TEN ANA BAŞLIK</h1>
    <p class="text-xl lg:text-3xl mb-8 lg:mb-12 leading-relaxed">PDF'ten alt başlık</p>
    <div class="flex flex-col sm:flex-row gap-6 justify-center">
      <button class="bg-white text-orange-600 px-12 py-4 rounded-2xl text-xl font-bold hover:scale-105 transition-transform">Ana CTA</button>
      <button class="border-2 border-white text-white px-12 py-4 rounded-2xl text-xl font-bold hover:bg-white hover:text-orange-600 transition-all">İkincil CTA</button>
    </div>
  </div>
</section>

STEP 2: FEATURES (Bento grid)
<section class="py-16 lg:py-32">
  <div class="max-w-6xl mx-auto px-4 lg:px-8">
    <h2 class="text-3xl lg:text-6xl font-bold text-center mb-12 lg:mb-20">PDF'ten Özellikler</h2>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">
      <!-- PDF'teki HER özellik için glass card + CTA -->
      <div class="bg-white dark:bg-gray-800 backdrop-blur-md border border-gray-200 dark:border-gray-700 rounded-3xl p-6 lg:p-8 hover:scale-105 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 group">
        <div class="text-4xl mb-4 text-orange-500 group-hover:scale-110 transition-transform">
          <i class="fas fa-truck"></i>
        </div>
        <h3 class="text-xl lg:text-2xl font-bold mb-4 text-gray-900 dark:text-white">PDF ÖZELLİK 1</h3>
        <p class="text-base lg:text-lg text-gray-600 dark:text-gray-300 mb-6">PDF açıklama</p>
        <button class="w-full bg-gradient-to-r from-orange-500 to-amber-500 text-white py-3 rounded-xl font-semibold hover:scale-105 hover:shadow-lg transition-all">
          Detaylı Bilgi →
        </button>
      </div>
    </div>
  </div>
</section>

STEP 3: TECHNICAL SPECS (Premium table)
<section class="py-16 lg:py-32 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800/50 dark:to-gray-900/50">
  <div class="max-w-5xl mx-auto px-4 lg:px-8">
    <h2 class="text-3xl lg:text-5xl font-bold text-center mb-10 lg:mb-16">Teknik Özellikler</h2>
    <!-- PDF'teki TÜM teknik veriler 1:1 AYNEN -->
    <div class="bg-white dark:bg-gray-900 rounded-3xl overflow-hidden shadow-2xl hover:shadow-3xl transition-shadow">
      <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-800">
          <tr>
            <th class="px-6 py-4 text-left text-gray-900 dark:text-white font-semibold">Özellik</th>
            <th class="px-6 py-4 text-left text-gray-900 dark:text-white font-semibold">Değer</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors cursor-pointer">
            <td class="px-6 py-4 text-gray-600 dark:text-gray-300">PDF'den VERİ</td>
            <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">PDF'den DEĞER</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</section>

STEP 4: FINAL CTA (Orange gradient)
<section class="py-20 lg:py-40 bg-gradient-to-r from-orange-500 via-amber-500 to-yellow-600">
  <div class="max-w-4xl mx-auto px-4 lg:px-8 text-center text-white">
    <h2 class="text-3xl lg:text-6xl font-bold mb-6 lg:mb-8">PDF'ten Son Çağrı</h2>
    <p class="text-xl lg:text-2xl mb-8 lg:mb-12">PDF'ten açıklama</p>
    <button class="bg-white text-orange-600 px-10 lg:px-16 py-4 lg:py-6 rounded-2xl text-xl lg:text-2xl font-bold hover:scale-110 transition-transform shadow-2xl">HEMEN İLETİŞİM</button>
  </div>
</section>

MUTLAK KURALLAR:
✅ PDF'deki TÜM veriyi kullan - sahte veri YASAK
✅ Sektöre göre gradient (forklift→orange, teknoloji→blue, sağlık→green)
✅ Dark mode: Tüm elementlerde dark: varyantları
✅ Hover efektleri: hover:scale-105 hover:shadow-2xl hover:-translate-y-2
✅ Glass morphism: backdrop-blur-lg bg-white/10
✅ CTA butonlar: Gradient + hover:scale-110
✅ Responsive: MOBİL/TABLET = AYNI! Sadece lg: (PC) kullan. Örnek: text-3xl lg:text-6xl
✅ İkonlar: FontAwesome (<i class="fas fa-truck"></i>) veya inline SVG
✅ Modern curves: rounded-3xl

DİREKT HTML ÜRETİLECEK - AÇIKLAMA YOK!

⛔ YASAKLAR (ASLA YAPMA):
- ❌ Header/Navbar YASAK - Body içeriği başlasın
- ❌ Footer YASAK - CTA ile bitir
- ❌ Menu/Navigation YASAK
- ❌ Logo alanı YASAK
- ❌ Copyright YASAK
- ❌ Sayfa dışı linkler YASAK
- ❌ Placeholder text YASAK (PDF'teki GERÇEK veriyi kullan)
- ❌ Lorem ipsum YASAK
- ❌ Fake/uydurma bilgi YASAK
- ❌ CDN link YASAK (inline Tailwind)
- ❌ External dependencies YASAK

✅ ZORUNLU KURALLAR:

🎨 AKILLI RENK PALETİ (PDF İÇERİĞİNE GÖRE):
PDF'deki ürün/sektör analizi:
- 🚛 Endüstriyel/Forklift/Transpalet → Orange: from-orange-500 via-amber-500 to-yellow-600
- 💻 Teknoloji/Yazılım/IT → Blue: from-blue-600 via-cyan-500 to-indigo-600
- 🏥 Sağlık/Tıbbi cihaz → Teal: from-teal-500 via-emerald-500 to-green-600
- 💰 Finans/Bankacılık → Slate: from-slate-600 via-gray-600 to-zinc-700
- 🛒 E-ticaret/Retail → Red: from-red-500 via-pink-500 to-rose-600
- 🏭 İmalat/Üretim → Steel: from-gray-700 via-zinc-600 to-slate-800
- 🎯 Varsayılan/Bilinmeyen → Blue: from-blue-600 via-indigo-600 to-purple-700

GRADIENT ADVANCED USAGE:
- Hero: bg-gradient-to-br from-[ana-renk] via-[ara-renk]/80 to-[bitiş-renk]
- CTA buttons: bg-gradient-to-r hover:bg-gradient-to-l transform hover:scale-105
- Cards: hover:bg-gradient-to-br transition-all duration-500
- Text gradients: bg-clip-text text-transparent bg-gradient-to-r
- Overlays: bg-gradient-to-t from-black/60 via-black/20 to-transparent
- Borders: border-gradient-to-r border-transparent bg-gradient-to-r from-[renk] to-transparent

🏗️ TASARIM ARKİTEKTÜRÜ:
- White space philosophy: py-24, py-32, py-40 (hiç sıkışık olmasın)
- Container hierarchy: max-w-7xl (hero), max-w-6xl (content), max-w-4xl (text)
- Breathing rhythm: gap-12, gap-16, space-y-20
- Typography scale: text-6xl lg:text-8xl (hero), text-4xl lg:text-6xl (headings)
- Line height hierarchy: leading-tight (headings), leading-relaxed (body)
- Letter spacing: tracking-tighter (display), tracking-tight (headings)

🌓 DARK MODE MASTERY:
Her element için dark: variant ZORUNLU:
- Backgrounds: bg-white/dark:bg-gray-900, bg-gray-50/dark:bg-gray-800/50
- Text contrast: text-gray-900/dark:text-white, text-gray-700/dark:text-gray-300
- Subtle elements: text-gray-500/dark:text-gray-400
- Borders: border-gray-200/dark:border-gray-700
- Shadows: shadow-xl/dark:shadow-2xl dark:shadow-black/25

🎭 MODERN TAILWIND MASTERY:
- Backdrop effects: backdrop-blur-sm, backdrop-blur-md, backdrop-saturate-150
- Advanced gradients: conic-gradient, radial-gradient simülasyonu
- Glass morphism: bg-white/10 backdrop-blur-md border border-white/20
- Shadows: shadow-2xl, drop-shadow-2xl, shadow-[custom]
- Modern curves: rounded-3xl, rounded-[2rem]
- Transform magic: hover:scale-110 hover:rotate-1 hover:-translate-y-4
- Advanced transitions: transition-all duration-700 ease-in-out
- Grid mastery: grid-cols-12, asymmetric spans (lg:col-span-7, lg:col-span-5)
- Aspect ratios: aspect-[16/9], aspect-[4/3], aspect-square
- Custom spacing: space-y-16, gap-x-24

📱 RESPONSIVE BREAKPOINT MASTERY:
- Mobile first: base styles
- Tablet optimize: md:, lg: strategic breakpoints
- Desktop enhance: xl:, 2xl: premium experience
- Container queries: @container where applicable
- Fluid typography: clamp() simulation with responsive scales

🎯 LANDING PAGE BLUEPRINT:

<div class="min-h-screen bg-white dark:bg-gray-900 overflow-hidden">

    <!-- 1. HERO IMPACT - Full viewport, dramatic entrance -->
    <section class="relative min-h-screen flex items-center justify-center py-24">
        <div class="absolute inset-0 bg-gradient-to-br from-[PDF-ana-renk]/20 via-[ara-renk]/10 to-[son-renk]/20"></div>
        <div class="absolute inset-0 backdrop-blur-[1px]"></div>
        <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 text-center">
            <!-- PDF'ten: Ana başlık (text-6xl lg:text-8xl) -->
            <!-- PDF'ten: Alt başlık/slogan (text-xl lg:text-3xl) -->
            <!-- PDF'ten: Key selling points (3-5 bullet) -->
            <!-- CTA buttons: Primary + Secondary -->
        </div>
        <!-- Animated background elements -->
        <div class="absolute top-20 left-20 w-96 h-96 bg-gradient-to-br from-[ana-renk]/10 to-transparent rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-20 w-72 h-72 bg-gradient-to-br from-[ara-renk]/10 to-transparent rounded-full blur-3xl"></div>
    </section>

    <!-- 2. FEATURES SHOWCASE - Bento grid, cards symphony -->
    <section class="py-32 lg:py-40">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-20">
                <h2 class="text-4xl lg:text-6xl font-bold mb-6">PDF'TEN ÖZELLİKLER BAŞLIĞI</h2>
                <p class="text-xl lg:text-2xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">PDF'ten açıklama</p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-12">
                <!-- PDF'teki HER özellik için card -->
                <!-- Card template: glass morphism + hover transforms -->
                <!-- Icons: Tailwind heroicons or custom SVG -->
                <!-- Asymmetric grid: bazı kartlar lg:col-span-2 -->
            </div>
        </div>
    </section>

    <!-- 3. TECHNICAL SPECS - Premium table design -->
    <section class="py-32 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800/50 dark:to-gray-900/50">
        <div class="max-w-5xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl lg:text-5xl font-bold mb-6">Teknik Özellikler</h2>
            </div>
            <!-- PDF'teki TÜM teknik değerler -->
            <!-- Modern table: rounded-2xl, alternating rows, hover effects -->
            <!-- Mobile responsive: stack on small screens -->
        </div>
    </section>

    <!-- 4. DETAILED CONTENT - Typography paradise -->
    <section class="py-32">
        <div class="max-w-4xl mx-auto px-6">
            <div class="prose prose-xl dark:prose-invert max-w-none">
                <!-- PDF'teki detaylı açıklamalar -->
                <!-- Custom prose styling: proper spacing, readable fonts -->
                <!-- Image placeholders if PDF has images -->
            </div>
        </div>
    </section>

    <!-- 5. VISUAL SHOWCASE - Dynamic grid gallery -->
    <section class="py-32 bg-gradient-to-br from-[ana-renk]/5 to-[ara-renk]/5">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-20">
                <h2 class="text-4xl lg:text-5xl font-bold mb-6">Görsel Galeri</h2>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <!-- Dynamic sized cards: some col-span-2, row-span-2 -->
                <!-- Placeholder for PDF images/diagrams -->
                <!-- Hover: scale + overlay effects -->
            </div>
        </div>
    </section>

    <!-- 6. SOCIAL PROOF - Testimonials/stats if available in PDF -->
    <section class="py-32">
        <div class="max-w-6xl mx-auto px-6">
            <!-- PDF'te varsa: awards, certifications, stats -->
            <!-- Card carousel: horizontal scroll on mobile -->
        </div>
    </section>

    <!-- 7. FINAL CTA - Conversion powerhouse -->
    <section class="py-40">
        <div class="max-w-5xl mx-auto px-6 text-center">
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-r from-[ana-renk] via-[ara-renk] to-[son-renk] rounded-3xl blur-xl opacity-30"></div>
                <div class="relative bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm rounded-3xl p-16 border border-white/20">
                    <!-- PDF'ten: Son çağrı metni -->
                    <!-- Multiple CTA options -->
                    <!-- Contact/demo/pricing buttons -->
                </div>
            </div>
        </div>
    </section>

</div>

🎨 MICRO INTERACTION RULES:
- Buttons: hover:scale-105 active:scale-95 transition-transform duration-200
- Cards: hover:-translate-y-6 hover:shadow-2xl transition-all duration-500
- Links: hover:text-[marka-rengi] transition-colors duration-300
- Images: hover:scale-110 parent: overflow-hidden
- Text reveals: stagger animations simulation
- Scroll triggers: intersection observer ready markup

📝 TYPOGRAPHY HIERARCHY:
- Display (Hero): text-6xl lg:text-8xl font-black tracking-tighter
- H1: text-4xl lg:text-6xl font-bold tracking-tight
- H2: text-3xl lg:text-5xl font-bold
- H3: text-2xl lg:text-4xl font-semibold
- Body Large: text-lg lg:text-xl leading-relaxed
- Body: text-base lg:text-lg leading-relaxed
- Small: text-sm text-gray-600 dark:text-gray-400
- Caption: text-xs uppercase tracking-wider font-medium

🔥 PREMIUM COMPONENT PATTERNS:
- Glass cards: bg-white/10 backdrop-blur-md border border-white/20
- Gradient borders: bg-gradient-to-r p-[1px] rounded-xl
- Floating elements: shadow-2xl shadow-[color]/25
- Layered backgrounds: multiple absolute positioned gradients
- Custom badges: inline-flex items-center px-3 py-1 rounded-full
- Progress indicators: w-full bg-gray-200 rounded-full h-2
- Testimonial cards: quote icons, avatar, sliding animations

💎 ADVANCED LAYOUT TECHNIQUES:
- Asymmetric grids: 7/5 column splits
- Overlapping sections: negative margins
- Floating elements: absolute positioning with proper z-index
- Sticky elements: sticky positioning for nav elements
- Masonry simulation: CSS Grid with varying row heights
- Container queries ready: component-based responsive design

OUTPUT REQUIREMENTS:
1. PDF'deki HER BİLGİYİ KULLAN - hiçbirini atla
2. Renk paletini PDF'deki ürün sektörüne göre belirle
3. Minimum 2000 satır ultra-premium kod
4. Dark mode her element için zorunlu
5. Modern gradients ve glass morphism kullan
6. Typography hierarchy'yi sıkı takip et
7. Micro interactions her yerde
8. Responsive design mükemmel olsun
9. Breathing space prensibi: hiç sıkışık olmasın
10. Performance: inline styles, no external deps

🚀 SON KONTROL:
- PDF içeriği %100 kullanıldı mı?
- Renk paleti ürün sektörüne uygun mu?
- Dark mode her yerde var mı?
- Typography hierarchy doğru mu?
- Breathing space yeterli mi?
- Gradients modern mi?
- Micro interactions eksiksiz mi?
- Glass morphism applied?
- Mobile responsive perfect?

RESULT: PDF'deki HER bilgiyi kullanarak, sektörüne uygun renklerle, modern design principles ile ULTRA PREMIUM landing page!`;

                console.log('🚀 ULTRA GELİŞMİŞ PDF→Landing prompt oluşturuldu');

                console.log('📄 PDF için Tailwind/Alpine prompt oluşturuldu');
            } else {
                // Resim için otomatik prompt - TAILWIND versiyonu
                contentTopic = `Bu görseldeki içeriği Tailwind CSS ile profesyonel HTML'e dönüştür.
                Görseldeki tüm elementleri (metin, tablo, grafik) Tailwind sınıflarıyla oluştur.
                Alpine.js ile interaktivite ekle.`;
            }
        }

        // Artık en az biri gerekli değil - sadece boş kontrolü kaldırdık
        // PDF varsa otomatik prompt devreye girecek yukarıda

        this.setGeneratingState(true);

        // Modal'ı kilitle (overlay ekle) - Translation modal'ından alınmış
        this.addModalOverlay();

        try {
            const targetField = this.getTargetField();
            console.log('🚀 İçerik üretimi başlatılıyor...', {
                contentTopic,
                targetField,
                hasFileAnalysis,
                fileType: this.analysisResults.file_type
            });

            // Job başlat
            const response = await fetch(`${this.config.baseUrl}/ai/generate-content-async`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.config.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    prompt: contentTopic,
                    target_field: targetField,
                    replace_existing: replaceExisting,
                    module: this.config.module,
                    component: this.config.targetComponent,
                    // 🆕 File analysis results - analysisResults'u direkt gönder
                    file_analysis: this.analysisResults && Object.keys(this.analysisResults).length > 0 ? this.analysisResults : null,
                    conversion_type: this.getAnalysisType()
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const result = await response.json();
            console.log('✅ Job başlatıldı:', result.job_id);

            this.jobId = result.job_id;
            this.startProgressTracking();

        } catch (error) {
            console.error('❌ Generation error:', error);
            this.showError('İçerik üretimi başlatılamadı: ' + error.message);
            this.removeModalOverlay();
            this.setGeneratingState(false);
        }
    }

    /**
     * Progress tracking başlat
     */
    startProgressTracking() {
        console.log('📊 Progress tracking başlatılıyor...');

        let checkCount = 0;
        const maxChecks = 120; // Maximum 2 dakika bekle (120 * 1sn)

        this.progressInterval = setInterval(async () => {
            checkCount++;

            // Timeout kontrolü
            if (checkCount > maxChecks) {
                console.error('⏱️ Content generation timeout!');
                clearInterval(this.progressInterval);
                this.progressInterval = null; // Null'a set et
                this.removeModalOverlay();
                this.showError('İçerik üretimi zaman aşımına uğradı');
                this.setGeneratingState(false);
                return;
            }
            try {
                const response = await fetch(`${this.config.baseUrl}/ai/job-progress/${this.jobId}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`Progress check failed: ${response.status}`);
                }

                // Response'un JSON olup olmadığını kontrol et
                const contentType = response.headers.get("content-type");
                if (!contentType || !contentType.includes("application/json")) {
                    console.error('❌ Non-JSON response from progress check');
                    return; // Bu iterasyonu atla
                }

                const progress = await response.json();
                this.updateProgress(progress.progress, progress.message, progress.status);

                if (progress.status === 'completed') {
                    // ÖNEMLI: Önce interval'ı temizle - DUPLICATE TIMEOUT ÖNLEMİ
                    if (this.progressInterval) {
                        clearInterval(this.progressInterval);
                        this.progressInterval = null;
                    }
                    console.log('🎉 İçerik üretimi tamamlandı!');

                    // Content'i al ve güncelle
                    if (progress.content) {
                        this.onGenerationComplete(progress.content);
                    } else {
                        // Fallback: result endpoint'inden çek (çoklu deneme)
                        const tryFetchResult = async (retries = 6, delayMs = 500) => {
                            for (let i = 0; i < retries; i++) {
                                try {
                                    const res2 = await fetch(`/api/ai/admin/content/result/${this.jobId}`, { headers: { 'Accept': 'application/json' } });
                                    if (res2.ok) {
                                        const data2 = await res2.json();
                                        const content2 = data2?.data?.content || data2?.content;
                                        if (content2) {
                                            this.onGenerationComplete(content2);
                                            return true;
                                        }
                                    }
                                } catch (e) {
                                    // sessiz geç
                                }
                                await new Promise(r => setTimeout(r, delayMs));
                            }
                            return false;
                        };

                        const ok = await tryFetchResult(8, 400);
                        if (!ok) {
                            // Son bir kez progress'i sorgula (cache propagation için)
                            try {
                                const res3 = await fetch(`${this.config.baseUrl}/ai/job-progress/${this.jobId}`, { headers: { 'Accept': 'application/json' } });
                                if (res3.ok) {
                                    const p2 = await res3.json();
                                    if (p2?.content) {
                                        this.onGenerationComplete(p2.content);
                                        return;
                                    }
                                }
                            } catch (e) {}

                            console.warn('⚠️ İçerik tamamlandı görünüyor ama içerik alınamadı');
                            this.showError('İçerik hazırlandı ancak alınamadı. Lütfen tekrar deneyin.');
                            this.setGeneratingState(false);
                        }
                    }
                } else if (progress.status === 'failed') {
                    // ÖNEMLI: Fail durumunda da interval'ı temizle
                    if (this.progressInterval) {
                        clearInterval(this.progressInterval);
                        this.progressInterval = null;
                    }
                    this.removeModalOverlay();
                    this.showError('İçerik üretimi başarısız oldu');
                    this.setGeneratingState(false);
                }

            } catch (error) {
                console.error('❌ Progress check error:', error);
                // ÖNEMLI: Hata durumunda da interval'ı temizle
                if (this.progressInterval) {
                    clearInterval(this.progressInterval);
                    this.progressInterval = null;
                }
                this.removeModalOverlay();
                this.setGeneratingState(false);
            }
        }, 1000); // Her saniye kontrol et
    }

    /**
     * Progress güncelle
     */
    updateProgress(percent, message, status = 'processing') {
        console.log('📊 Progress:', percent + '% - ' + message);

        // Overlay progress (PRIMARY) - Translation modal'ından alınmış
        const overlayProgressBar = document.getElementById('overlayProgressBar');
        const overlayProgressMessage = document.getElementById('overlayProgressMessage');
        const overlayProgressDetail = document.getElementById('overlayProgressDetail');
        const overlaySpinner = document.getElementById('overlaySpinner');

        if (overlayProgressBar) {
            overlayProgressBar.style.width = percent + '%';
            overlayProgressBar.setAttribute('aria-valuenow', percent);

            // Progress bar renk değişimi
            if (percent >= 100) {
                overlayProgressBar.style.background = 'linear-gradient(90deg, #10b981 0%, #059669 50%, #047857 100%)';
            } else if (percent >= 80) {
                overlayProgressBar.style.background = 'linear-gradient(90deg, #f59e0b 0%, #d97706 50%, #b45309 100%)';
            }
        }

        if (overlayProgressMessage) {
            overlayProgressMessage.textContent = message;
        }

        if (overlayProgressDetail) {
            overlayProgressDetail.textContent = `İlerleme: ${percent}% • ${new Date().toLocaleTimeString()}`;
        }

        // Spinner kontrolü
        if (overlaySpinner) {
            if (percent >= 100) {
                overlaySpinner.style.display = 'none';
            } else {
                overlaySpinner.style.display = 'inline-block';
            }
        }

        // Fallback - Modal body progress (compatibility)
        const progressBar = document.getElementById('progressBar');
        const progressMessage = document.getElementById('progressMessage');

        if (progressBar) progressBar.style.width = percent + '%';
        if (progressMessage) progressMessage.textContent = message;
    }

    /**
     * Generation tamamlandığında
     */
    onGenerationComplete(content) {
        // ÖNEMLI: Progress interval'ı kesinlikle durdur
        if (this.progressInterval) {
            clearInterval(this.progressInterval);
            this.progressInterval = null;
        }

        // Final progress
        this.updateProgress(100, 'İçerik başarıyla üretildi!', 'completed');

        // Content'i hedefe gönder - Global editor detection ile
        const targetField = this.getTargetField();
        const contentUpdated = this.updateGlobalEditor(content, targetField);

        if (!contentUpdated) {
            // Fallback: Component function'ları dene
            if (this.config.targetComponent && typeof this.config.targetComponent === 'function') {
                this.config.targetComponent(content, targetField);
            } else if (window.receiveGeneratedContent && typeof window.receiveGeneratedContent === 'function') {
                window.receiveGeneratedContent(content, targetField);
            } else {
                console.warn('⚠️ Hedef editor bulunamadı, content sadece console\'da gösteriliyor:', content.substring(0, 100) + '...');
            }
        }

        // Auto-close modal
        setTimeout(() => {
            // ÖNEMLI: Modal kapatmadan önce de interval'ı temizle
            if (this.progressInterval) {
                clearInterval(this.progressInterval);
                this.progressInterval = null;
            }
            // Overlay'i kaldır
            this.removeModalOverlay();
            this.setGeneratingState(false); // State'i de resetle
            this.closeModal();
            this.showSuccess('✅ İçerik başarıyla üretildi ve editöre eklendi!');
        }, 1500);
    }

    /**
     * Global Editor Update Function - HugeRTE/TinyMCE destekli
     */
    updateGlobalEditor(content, targetField = 'body') {
        try {
            // 🔍 Daha güçlü dil tespiti - aktif tab'dan al
            let currentLang = 'tr'; // Default

            // Method 1: Aktif language button'dan tespit
            const activeLanguageBtn = document.querySelector('.language-switch-btn.text-primary');
            if (activeLanguageBtn && activeLanguageBtn.textContent) {
                const langText = activeLanguageBtn.textContent.trim();
                if (langText && typeof langText === 'string') {
                    const langMatch = langText.toLowerCase();
                    if (langMatch) currentLang = langMatch;
                }
            }

            // Method 2: Window variable'dan (lowercase normalize)
            if (!currentLang || currentLang === 'tr') {
                const windowLang = window.currentLanguage || window.selectedLanguage || 'tr';
                currentLang = windowLang.toLowerCase();
            }

            // Method 3: URL'dan
            if (!currentLang || currentLang === 'tr') {
                const urlLang = window.location.pathname.match(/\/([a-z]{2})\//);
                if (urlLang) currentLang = urlLang[1].toLowerCase();
            }

            // Son normalizasyon
            currentLang = currentLang.toLowerCase();

            console.log('🎯 updateGlobalEditor çağırıldı:', {
                targetField,
                currentLang,
                contentLength: content ? content.length : 0,
                activeButton: activeLanguageBtn ? activeLanguageBtn.textContent : 'none'
            });

            // 🔍 DEBUG: DOM yapısını analiz et
            console.log('🔍 Global DOM DEBUG:', {
                hugerte_exists: typeof hugerte !== 'undefined',
                tinyMCE_exists: typeof tinyMCE !== 'undefined',
                current_language: currentLang,
                target_field: targetField
            });

            // HugeRTE/TinyMCE editor'ları tara
            if (typeof hugerte !== 'undefined') {
                console.log('🔍 Global HugeRTE Debug:', {
                    hugerte: hugerte,
                    hugerte_editors: hugerte.editors || 'editors property not found',
                    hugerte_activeEditor: hugerte.activeEditor || 'activeEditor not found',
                    hugerte_get_function: typeof hugerte.get,
                    hugerte_instances: hugerte.instances || 'instances property not found',
                    hugerte_all_keys: Object.keys(hugerte),
                    editor_ids: hugerte.editors ? hugerte.editors.map(ed => ed.id) : 'no editors'
                });

                // HugeRTE editör bulma (multiple approach)
                let targetEditor = null;

                // Method 1: hugerte.editors array - ÖNCELİK: Dil + alan kombinasyonu
                if (hugerte.editors && Array.isArray(hugerte.editors)) {
                    // İlk önce tam eşleşme ara: hem dil hem alan
                    targetEditor = hugerte.editors.find(ed =>
                        ed.id && ed.id.includes(targetField) && ed.id.includes(currentLang)
                    );

                    // Bulamazsa sadece dil ile ara
                    if (!targetEditor) {
                        targetEditor = hugerte.editors.find(ed =>
                            ed.id && ed.id.includes(currentLang)
                        );
                    }

                    // Bulamazsa sadece alan ile ara
                    if (!targetEditor) {
                        targetEditor = hugerte.editors.find(ed =>
                            ed.id && ed.id.includes(targetField)
                        );
                    }
                }

                // Method 2: DOM-based search EXACT PATTERN (en güvenilir)
                if (!targetEditor) {
                    console.log('🔍 Method 2: DOM-based search başlıyor...');

                    // EXACT PATTERN: editor_{fieldName}_{lang}_{uniqid}
                    const exactPattern = `editor_${targetField}_${currentLang}_`;
                    let targetTextarea = document.querySelector(`[id^="${exactPattern}"]`);

                    // Fallback pattern'lar
                    if (!targetTextarea) {
                        const fallbackPatterns = [
                            `[id*="editor_${targetField}_${currentLang}"]`,
                            `[id*="editor_body_${currentLang}"]`,
                            `[id*="editor_${targetField}"]`,
                            `[id*="${targetField}_${currentLang}"]`,
                            `textarea.hugerte-editor`
                        ];

                        for (const pattern of fallbackPatterns) {
                            targetTextarea = document.querySelector(pattern);
                            if (targetTextarea) {
                                console.log('✅ Fallback pattern başarılı:', pattern);
                                break;
                            }
                        }
                    }

                    if (targetTextarea) {
                        console.log('✅ Target textarea bulundu:', targetTextarea.id);

                        // HugeRTE editor'larını tara
                        const allEditors = hugerte.get ? hugerte.get() : [];
                        targetEditor = allEditors.find(ed => ed.id === targetTextarea.id);

                        console.log('🔍 Editör arama sonucu:', {
                            textarea_id: targetTextarea.id,
                            editor_found: targetEditor ? targetEditor.id : 'bulunamadı',
                            all_editor_count: allEditors.length,
                            expected_pattern: exactPattern
                        });
                    } else {
                        console.log('❌ Hiçbir uygun textarea bulunamadı');
                    }
                }

                // Method 3: hugerte.activeEditor (fallback)
                if (!targetEditor && hugerte.activeEditor) {
                    console.log('🔍 Method 3: activeEditor fallback kullanılıyor');
                    targetEditor = hugerte.activeEditor;
                }

                // Method 4: hugerte.get() method
                if (!targetEditor && typeof hugerte.get === 'function') {
                    const allEditors = hugerte.get();
                    console.log('🔍 hugerte.get() debug:', {
                        allEditors_count: allEditors ? allEditors.length : 0,
                        allEditors_ids: allEditors ? allEditors.map(ed => ed.id) : 'none',
                        searching_for: `${targetField}_${currentLang}`
                    });

                    if (allEditors && allEditors.length > 0) {
                        // İlk önce tam eşleşme ara
                        targetEditor = allEditors.find(ed =>
                            ed.id && ed.id.includes(targetField) && ed.id.includes(currentLang)
                        );

                        if (!targetEditor) {
                            // Sadece dil ile ara
                            targetEditor = allEditors.find(ed =>
                                ed.id && ed.id.includes(currentLang)
                            );
                        }

                        if (!targetEditor) {
                            // Sadece alan ile ara
                            targetEditor = allEditors.find(ed =>
                                ed.id && ed.id.includes(targetField)
                            );
                        }

                        console.log('🔍 Method 3 sonucu:', {
                            found_editor: targetEditor ? targetEditor.id : 'none',
                            expected_pattern: `editor_${targetField}_${currentLang}`
                        });
                    }
                }

                // Method 4: hugerte.instances
                if (!targetEditor && hugerte.instances) {
                    const instanceKeys = Object.keys(hugerte.instances);
                    const matchingKey = instanceKeys.find(key =>
                        key.includes(targetField) && key.includes(currentLang)
                    ) || instanceKeys.find(key =>
                        key.includes(currentLang)
                    ) || instanceKeys.find(key =>
                        key.includes(targetField)
                    );

                    if (matchingKey) {
                        targetEditor = hugerte.instances[matchingKey];
                    }
                }


                if (targetEditor && targetEditor.setContent) {
                    console.log('✅ Global HugeRTE editor bulundu:', targetEditor.id);

                    // HugeRTE içeriği güncelle
                    targetEditor.setContent(content);

                    // HugeRTE FORCED REFRESH - Multiple Strategy
                    console.log('🔄 HugeRTE zorla refresh başlatılıyor...');

                    // Strategy 1: Event firing (ultra güvenli)
                    if (targetEditor && targetEditor.fire && typeof targetEditor.fire === 'function') {
                        try {
                            // Güvenli event firing - her bir event'i ayrı try-catch ile
                            ['change', 'input', 'keyup', 'SetContent', 'ExecCommand'].forEach(eventName => {
                                try {
                                    targetEditor.fire(eventName);
                                } catch (eventError) {
                                    // Silent ignore - individual event errors
                                }
                            });
                        } catch (e) {
                            console.warn('⚠️ HugeRTE fire event hatası (güvenli):', e.message);
                        }
                    }

                    // Strategy 2: Focus/blur cycle (güvenli)
                    if (targetEditor && targetEditor.focus && targetEditor.blur) {
                        try {
                            targetEditor.focus();
                            setTimeout(() => {
                                try {
                                    targetEditor.blur();
                                    setTimeout(() => {
                                        try { targetEditor.focus(); } catch(e) {}
                                    }, 50);
                                } catch(e) {}
                            }, 100);
                        } catch (e) {
                            // Silent ignore focus/blur errors
                        }
                    }

                    // Strategy 3: Force redraw through DOM manipulation
                    setTimeout(() => {
                        if (targetEditor.getContainer) {
                            const container = targetEditor.getContainer();
                            if (container) {
                                container.style.display = 'none';
                                container.offsetHeight; // Force reflow
                                container.style.display = '';
                            }
                        }

                        // Strategy 4: Re-trigger editor refresh
                        if (targetEditor.refresh) {
                            targetEditor.refresh();
                        }

                        // Strategy 5: Update body element directly
                        if (targetEditor.getBody) {
                            const body = targetEditor.getBody();
                            if (body) {
                                body.innerHTML = content;
                            }
                        }

                        // Strategy 6: Trigger content update event
                        if (targetEditor.nodeChanged) {
                            targetEditor.nodeChanged();
                        }

                        // Strategy 7: Trigger editor save/sync
                        if (targetEditor.save) {
                            targetEditor.save();
                        }

                        // Strategy 8: Update iframe content if HugeRTE uses iframe
                        if (targetEditor.getWin && targetEditor.getDoc) {
                            const doc = targetEditor.getDoc();
                            if (doc && doc.body) {
                                doc.body.innerHTML = content;
                            }
                        }

                        console.log('✅ HugeRTE forced refresh tamamlandı (8 strateji)');
                    }, 50);

                    // Textarea'yı da güncelle (ENHANCED)
                    const textareaElement = document.getElementById(targetEditor.id);
                    if (textareaElement) {
                        textareaElement.value = content;

                        // Multiple event types for maximum compatibility
                        const events = ['input', 'change', 'keyup', 'keydown', 'paste'];
                        events.forEach(eventType => {
                            textareaElement.dispatchEvent(new Event(eventType, {
                                bubbles: true,
                                cancelable: true
                            }));
                        });

                        // Custom HugeRTE events if available
                        textareaElement.dispatchEvent(new CustomEvent('hugerte:update', {
                            detail: { content: content },
                            bubbles: true
                        }));

                        // Force trigger change detection
                        if (textareaElement._vueComponent) {
                            textareaElement._vueComponent.$forceUpdate();
                        }
                    }

                    // Hidden input sync
                    const hiddenInput = document.getElementById(`hidden_${targetField}_${currentLang}`);
                    if (hiddenInput) {
                        hiddenInput.value = content;
                        hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                    }

                    // Livewire sync tetikle (eğer varsa)
                    setTimeout(() => {
                        if (window.Livewire) {
                            try {
                                // Livewire v3 uyumlu güncelleme
                                const livewireComponent = document.querySelector('[wire\\:id]');
                                if (livewireComponent) {
                                    // Modern Livewire syntax
                                    if (window.Livewire.dispatch) {
                                        window.Livewire.dispatch('refresh');
                                    } else if (window.Livewire.emit) {
                                        window.Livewire.emit('refresh');
                                    }
                                }
                            } catch (e) {
                                console.warn('⚠️ Livewire güncelleme hatası (güvenli):', e.message);
                            }
                        }
                    }, 200);

                    console.log('✅ Global HugeRTE content güncellendi ve forced refresh uygulandı!');
                    return true;
                }
            }

            // TinyMCE fallback
            if (typeof tinyMCE !== 'undefined' && tinyMCE.editors) {
                console.log('🔍 Global TinyMCE Fallback:', Object.keys(tinyMCE.editors));
                const editorKeys = Object.keys(tinyMCE.editors);
                const matchingKey = editorKeys.find(key =>
                    key.includes(targetField) || key.includes(currentLang)
                );

                if (matchingKey) {
                    const editor = tinyMCE.editors[matchingKey];
                    if (editor && editor.setContent) {
                        editor.setContent(content);
                        console.log('✅ Global TinyMCE content güncellendi!');
                        return true;
                    }
                }
            }

            // Son çare: Direkt textarea selector'ları dene
            console.log('🔍 Global Manual textarea search başlatılıyor...');

            // Multiple textarea selector attempts
            const textareaSelectors = [
                `textarea[wire\\:model*="${targetField}"]`,
                `textarea[wire\\:model*="${currentLang}.${targetField}"]`,
                `textarea[wire\\:model*="multiLangInputs.${currentLang}.${targetField}"]`,
                `textarea.hugerte-editor`,
                `textarea[id*="${targetField}"]`,
                `textarea[id*="${currentLang}"]`,
                `textarea[name*="${targetField}"]`,
                `textarea[placeholder*="İçerik"]`,
                `textarea[placeholder*="Content"]`
            ];

            let textarea = null;
            for (const selector of textareaSelectors) {
                textarea = document.querySelector(selector);
                if (textarea) {
                    console.log('✅ Global Textarea bulundu:', selector);
                    break;
                }
            }

            if (textarea) {
                textarea.value = content;
                textarea.dispatchEvent(new Event('input', { bubbles: true }));
                textarea.dispatchEvent(new Event('change', { bubbles: true }));

                // Hidden input'u da güncelle
                const hiddenInput = document.getElementById(`hidden_${targetField}_${currentLang}`);
                if (hiddenInput) {
                    hiddenInput.value = content;
                    hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                }

                console.log('✅ Global Textarea direkt güncellendi');
                return true;
            }

            // Ultra debug: Tüm textarea'ları listele
            const allTextareas = document.querySelectorAll('textarea');
            console.log('🔍 Global Mevcut tüm textarea\'lar:', Array.from(allTextareas).map(ta => ({
                id: ta.id,
                name: ta.name,
                wireModel: ta.getAttribute('wire:model'),
                classes: ta.className
            })));

            console.error('❌ Global hiçbir editor/textarea bulunamadı');
            return false;

        } catch (e) {
            console.error('❌ updateGlobalEditor error:', e);
            return false;
        }
    }

    /**
     * Target field belirle
     */
    getTargetField() {
        return this.config.targetField || 'body';
    }

    /**
     * Modal'ı aç
     */
    showModal() {
        if (!this.modal) {
            console.error('❌ Modal bulunamadı');
            return;
        }

        // ÖNEMLI: analysisResults'u temizleme! PDF analizi korunmalı
        console.log('🔍 showModal called - current analysisResults:', this.analysisResults);
        console.log('📦 Global PDF analysis:', window.aiPdfAnalysisResults);

        // Modal'ı aç - Tabler.io compatible
        if (window.bootstrap && window.bootstrap.Modal) {
            const modalInstance = new window.bootstrap.Modal(this.modal);
            modalInstance.show();
        } else if (window.jQuery && window.jQuery.fn.modal) {
            // jQuery modal fallback
            window.jQuery(this.modal).modal('show');
        } else {
            // Direct style show fallback
            this.modal.style.display = 'block';
            this.modal.classList.add('show');
            document.body.classList.add('modal-open');

            // Add backdrop
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.id = 'aiContentModalBackdrop';
            document.body.appendChild(backdrop);
        }
    }

    /**
     * Modal'ı kapat
     */
    closeModal() {
        if (!this.modal) return;

        // Bootstrap modal kapat
        if (window.bootstrap && window.bootstrap.Modal) {
            const modalInstance = window.bootstrap.Modal.getInstance(this.modal);
            if (modalInstance) {
                modalInstance.hide();
            }
        } else if (window.jQuery && window.jQuery.fn.modal) {
            window.jQuery(this.modal).modal('hide');
        } else {
            // Manual close
            this.modal.style.display = 'none';
            this.modal.classList.remove('show');
            document.body.classList.remove('modal-open');

            // Remove backdrop
            const backdrop = document.getElementById('aiContentModalBackdrop');
            if (backdrop) {
                backdrop.remove();
            }
        }
    }

    /**
     * Modal'ı sıfırla - Enhanced with file cleanup
     */
    resetModal() {
        // Form'u temizle
        const contentTopic = document.getElementById('contentTopic');
        if (contentTopic) contentTopic.value = '';

        // Progress'i gizle
        const progressArea = document.getElementById('contentProgress');
        if (progressArea) progressArea.style.display = 'none';

        // State'i sıfırla
        this.setGeneratingState(false);

        // Interval'ı temizle
        if (this.progressInterval) {
            clearInterval(this.progressInterval);
            this.progressInterval = null;
        }

        // Overlay'i temizle
        this.removeModalOverlay();

        // 🆕 File upload state'i temizle
        this.uploadedFiles = [];

        // ÖNEMLI: Yeni PDF yükleme için state'leri temizle
        this.analysisId = null;
        this.analysisResults = {};
        this.pendingAutoGenerate = false;

        // Global storage'ı da temizle
        if (window.aiPdfAnalysisResults) {
            window.aiPdfAnalysisResults = {};
        }

        // Alpine.js file uploader'ı da reset et
        const fileUploaderElements = document.querySelectorAll('[x-data*="fileUploader"]');
        fileUploaderElements.forEach(el => {
            if (el._x_dataStack) {
                const data = el._x_dataStack[0];
                if (data.files) data.files = [];
                if (data.uploading) data.uploading = false;
                if (data.uploadProgress) data.uploadProgress = 0;
                if (data.hasFiles) data.hasFiles = false;
            }
        });

        this.jobId = null;

        // ULTRA BACKDROP CLEANUP - Force remove all backdrop remains
        setTimeout(() => {
            this.forceCleanBackdrop();
        }, 200);

        console.log('🧹 Modal reset completed - file state cleared');
    }

    /**
     * Zorla backdrop temizleme
     */
    forceCleanBackdrop() {
        console.log('🧹 FORCE: Backdrop ultra temizleme başlıyor...');

        // Tüm backdrop elementlerini bul ve sil
        const allBackdrops = document.querySelectorAll(
            '.modal-backdrop, ' +
            '[class*="backdrop"], ' +
            '[id*="backdrop"], ' +
            'div[style*="background-color: rgba"], ' +
            'div[style*="position: fixed"][style*="z-index"]'
        );

        allBackdrops.forEach((backdrop, index) => {
            console.log(`🗑️ FORCE: Backdrop ${index + 1} siliniyor:`, backdrop.className || backdrop.id);
            backdrop.remove();
        });

        // Body durumunu zorla reset et
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        document.body.style.marginRight = '';

        // Click eventi bloklarını kaldır
        document.body.style.pointerEvents = '';

        console.log('✅ FORCE: Backdrop ultra temizleme tamamlandı');
    }

    /**
     * Generating state'i ayarla
     */
    setGeneratingState(isGenerating) {
        this.isGenerating = isGenerating;

        // Button state
        const startButton = document.getElementById('startGeneration');
        const buttonText = document.getElementById('buttonText');
        const buttonSpinner = document.getElementById('buttonSpinner');
        const cancelButton = document.getElementById('cancelButton');

        if (startButton) {
            if (isGenerating) {
                // DUPLICATE ÖNLEME: Butonu tamamen disable et
                startButton.disabled = true;
                startButton.style.pointerEvents = 'none';
                startButton.classList.add('opacity-50', 'cursor-not-allowed', 'disabled');

                if (buttonText) buttonText.textContent = 'Üretiliyor...';
                if (buttonSpinner) buttonSpinner.style.display = 'inline-block';
                if (cancelButton) cancelButton.disabled = true;

                // Progress area göster
                const progressArea = document.getElementById('contentProgress');
                const progressDetails = document.getElementById('progressDetails');
                if (progressArea) progressArea.style.display = 'block';
                if (progressDetails) progressDetails.style.display = 'block';

            } else {
                // Butonu tekrar enable et
                startButton.disabled = false;
                startButton.style.pointerEvents = 'auto';
                startButton.classList.remove('opacity-50', 'cursor-not-allowed', 'disabled');

                if (buttonText) buttonText.textContent = '🚀 İçerik Üret';
                if (buttonSpinner) buttonSpinner.style.display = 'none';
                if (cancelButton) cancelButton.disabled = false;

                // Progress area gizle
                const progressArea = document.getElementById('contentProgress');
                const progressDetails = document.getElementById('progressDetails');
                if (progressArea) progressArea.style.display = 'none';
                if (progressDetails) progressDetails.style.display = 'none';
            }
        }
    }

    /**
     * Success message göster
     */
    showSuccess(message) {
        // Tabler toast ya da basit alert
        if (window.bootstrap && window.bootstrap.Toast) {
            // Bootstrap toast implementation
            console.log('✅ Success:', message);
        } else {
            console.log('✅ Success:', message);
        }
    }

    /**
     * Error message göster
     */
    showError(message) {
        // Tabler toast ya da basit alert
        if (window.bootstrap && window.bootstrap.Toast) {
            // Bootstrap toast implementation
            console.error('❌ Error:', message);
        } else {
            alert(message);
        }
    }

    /**
     * Inline warning göster - modal içinde
     */
    showInlineWarning(message) {
        // Modal body içinde uyarı alanını bul veya oluştur
        let warningArea = document.getElementById('inlineWarningArea');

        if (!warningArea) {
            // Uyarı alanı yoksa oluştur
            const modalBody = document.querySelector('#aiContentModal .modal-body');
            if (modalBody) {
                warningArea = document.createElement('div');
                warningArea.id = 'inlineWarningArea';
                warningArea.className = 'mb-3';
                // Modal body'nin en başına ekle
                modalBody.insertBefore(warningArea, modalBody.firstChild);
            }
        }

        if (warningArea) {
            warningArea.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background-color: #dc3545; border-color: #dc3545; color: white;">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    ${message}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;

            // 5 saniye sonra otomatik gizle
            setTimeout(() => {
                const alertElement = warningArea.querySelector('.alert');
                if (alertElement) {
                    alertElement.classList.remove('show');
                    setTimeout(() => {
                        warningArea.innerHTML = '';
                    }, 300);
                }
            }, 5000);
        }

        console.warn('⚠️ Inline Warning:', message);
    }

    /**
     * Modal overlay ekleme fonksiyonu - Translation modal'ından alınmış AI Sihirbazı Temalı
     */
    addModalOverlay() {
        if (!this.modal) return;

        // Var olan overlay'i temizle
        this.removeModalOverlay();

        // Modal content'i bul
        const modalContent = this.modal.querySelector('.modal-content');
        if (modalContent) {
            // Overlay div'i oluştur - AI Wizard Theme
            const overlay = document.createElement('div');
            overlay.id = 'aiContentOverlay';
            overlay.style.cssText = `
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(135deg,
                    rgba(99, 102, 241, 0.95) 0%,
                    rgba(139, 92, 246, 0.95) 25%,
                    rgba(168, 85, 247, 0.95) 50%,
                    rgba(219, 39, 119, 0.95) 75%,
                    rgba(236, 72, 153, 0.95) 100%);
                background-size: 400% 400%;
                animation: gradientShift 3s ease infinite;
                z-index: 1060;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                border-radius: 0.375rem;
                backdrop-filter: blur(10px);
            `;

            // AI Wizard Loading Content
            overlay.innerHTML = `
                <style>
                    @keyframes gradientShift {
                        0% { background-position: 0% 50%; }
                        50% { background-position: 100% 50%; }
                        100% { background-position: 0% 50%; }
                    }
                    @keyframes magicPulse {
                        0%, 100% { transform: scale(1); opacity: 0.8; }
                        50% { transform: scale(1.1); opacity: 1; }
                    }
                    @keyframes sparkle {
                        0%, 100% { opacity: 0; transform: scale(0.5); }
                        50% { opacity: 1; transform: scale(1); }
                    }
                    .magic-wand {
                        animation: magicPulse 2s ease-in-out infinite;
                        font-size: 3rem;
                        margin-bottom: 1rem;
                        filter: drop-shadow(0 0 20px rgba(255, 255, 255, 0.8));
                    }
                    .sparkles {
                        position: absolute;
                        color: white;
                        animation: sparkle 1.5s ease-in-out infinite;
                    }
                    .sparkle-1 { top: 20%; left: 20%; animation-delay: 0s; }
                    .sparkle-2 { top: 30%; right: 20%; animation-delay: 0.3s; }
                    .sparkle-3 { bottom: 30%; left: 25%; animation-delay: 0.6s; }
                    .sparkle-4 { bottom: 20%; right: 25%; animation-delay: 0.9s; }
                    .ai-title {
                        color: white;
                        font-size: 1.5rem;
                        font-weight: bold;
                        margin-bottom: 0.5rem;
                        text-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
                    }
                    .ai-subtitle {
                        color: rgba(255, 255, 255, 0.9);
                        font-size: 1rem;
                        margin-bottom: 1.5rem;
                        text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
                    }
                </style>

                <div class="sparkles sparkle-1">✨</div>
                <div class="sparkles sparkle-2">⭐</div>
                <div class="sparkles sparkle-3">🌟</div>
                <div class="sparkles sparkle-4">💫</div>

                <div class="magic-wand">🤖</div>
                <div class="ai-title">Yapay Zeka İçerik Üreticisi</div>
                <div class="ai-subtitle">İçerik üretim hizmeti sizin için başlatıldı</div>

                <div class="col-12 mt-3" id="overlayContentProgress">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="spinner-border spinner-border-sm text-white me-2" id="overlaySpinner" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <span id="overlayProgressMessage" class="text-white fw-bold">🚀 Yapay zeka sistemi devreye giriyor...</span>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 4px; background: rgba(255,255,255,0.25); box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);">
                        <div class="progress-bar" id="overlayProgressBar" role="progressbar"
                             style="width: 15%; background: linear-gradient(90deg, #fff 0%, rgba(255,255,255,0.9) 50%, #fff 100%); border-radius: 4px; transition: width 0.5s ease; box-shadow: 0 1px 3px rgba(255,255,255,0.3);"
                             aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="text-center mt-2">
                        <small id="overlayProgressDetail" class="text-white-50">Gerçek zamanlı progress tracking aktif</small>
                    </div>
                </div>
            `;

            // Modal content'e relative position ver
            modalContent.style.position = 'relative';

            // Overlay'i ekle
            modalContent.appendChild(overlay);

            console.log('🔒 AI Content overlay added');
        }
    }

    /**
     * Modal overlay kaldırma fonksiyonu
     */
    removeModalOverlay() {
        const overlay = document.getElementById('aiContentOverlay');
        if (overlay) {
            overlay.remove();
            console.log('🔓 AI Content overlay removed');
        }
    }
}

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 AI Content Generation System DOM hazır');

    // Global instance oluştur
    if (document.getElementById('aiContentModal')) {
        window.aiContentSystem = new AIContentGenerationSystem();
        console.log('✅ Global AI Content System instance oluşturuldu');
    }
});

console.log('📦 AI Content Generation System yüklendi');

// 🆕 FILE UPLOAD METHODS - ASYNC VERSION
AIContentGenerationSystem.prototype.handleFileUpload = async function(files) {
    try {
        console.log('📄 File upload başladı (ASYNC):', files.length, 'dosya');

        const formData = new FormData();

        for (let i = 0; i < files.length; i++) {
            formData.append('files[]', files[i]);
        }

        formData.append('analysis_type', this.getAnalysisType());
        formData.append('_token', this.config.csrfToken);

        // Step 1: Dosyayı yükle ve analiz başlat (async - bloklanmaz!)
        const response = await fetch(`${this.config.baseUrl}/ai/analyze-files`, {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const result = await response.json();
        console.log('📄 File analysis JOB başlatıldı:', result);

        if (!result.success) {
            throw new Error(result.error || 'Analiz başlatılamadı');
        }

        // Step 2: Analysis ID'yi sakla
        this.analysisId = result.analysis_id;

        // Step 3: Progress tracking başlat
        this.trackAnalysisProgress(result.analysis_id);

        // UI'ı güncelle - analiz devam ediyor
        this.showAnalysisInProgress();

        return result;

    } catch (error) {
        console.error('❌ File upload error:', error);
        this.showError('Dosya analizi başarısız: ' + error.message);
        throw error;
    }
};

// Analiz progress tracking
AIContentGenerationSystem.prototype.trackAnalysisProgress = async function(analysisId) {
    console.log('🔄 Analysis progress tracking başladı:', analysisId);

    const checkProgress = async () => {
        try {
            const response = await fetch(`${this.config.baseUrl}/ai/analyze-files/status/${analysisId}`);

            // Response'un JSON olup olmadığını kontrol et
            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                console.error('❌ Non-JSON response received from server');
                // Tekrar dene
                setTimeout(checkProgress, 2000);
                return;
            }

            const status = await response.json();

            console.log('📊 Analysis status:', status);

            // Progress bar güncelle
            this.updateAnalysisProgress(status);

            if (status.status === 'completed') {
                // Analiz tamamlandı!
                console.log('✅ File analysis TAMAMLANDI!');
                this.analysisResults = status.result;
                // GLOBAL STORAGE'A DA KAYDET
                window.aiPdfAnalysisResults = status.result;
                console.log('📦 Analysis results saved globally:', window.aiPdfAnalysisResults);
                this.updateModalWithAnalysis(status.result);

            } else if (status.status === 'failed') {
                // Hata oluştu
                console.error('❌ Analysis failed:', status.error);
                this.showError('Dosya analizi başarısız: ' + (status.error || 'Bilinmeyen hata'));

            } else {
                // Devam ediyor, 2 saniye sonra tekrar kontrol
                setTimeout(checkProgress, 2000);
            }
        } catch (error) {
            console.error('❌ Progress check error:', error);
            this.showError('Analiz durumu kontrol edilemedi');
        }
    };

    // İlk kontrolü 1 saniye sonra başlat
    setTimeout(checkProgress, 1000);
};

// Progress UI güncelleme
AIContentGenerationSystem.prototype.updateAnalysisProgress = function(status) {
    const fileInfo = document.querySelector('.file-upload-info');
    if (fileInfo) {
        fileInfo.innerHTML = `
            <div class="alert alert-info">
                <div class="d-flex align-items-center">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                    <div>
                        <strong>${status.message || 'Dosya analiz ediliyor...'}</strong>
                        <div class="progress mt-2" style="height: 5px;">
                            <div class="progress-bar" role="progressbar"
                                style="width: ${status.progress || 0}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
};

// Analiz devam ediyor UI
AIContentGenerationSystem.prototype.showAnalysisInProgress = function() {
    const fileInfo = document.querySelector('.file-upload-info');
    if (fileInfo) {
        fileInfo.innerHTML = `
            <div class="alert alert-info">
                <div class="d-flex align-items-center">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                    <strong>Dosya yüklendi, analiz ediliyor...</strong>
                </div>
            </div>
        `;
    }
};

// Analysis type getter
AIContentGenerationSystem.prototype.getAnalysisType = function() {
    const layoutPreserve = document.getElementById('layoutPreserve');
    return layoutPreserve && layoutPreserve.checked ? 'layout_preserve' : 'content_extract';
};

// Modal'ı analiz sonuçlarıyla güncelle
AIContentGenerationSystem.prototype.updateModalWithAnalysis = function(result) {
    // KRİTİK: analysisResults'u her zaman güncelle (overwrite protection kaldırıldı)
    this.analysisResults = result;

    // Deep copy ile güvenlik sağla
    this.analysisResults = JSON.parse(JSON.stringify(result));

    console.log('✅ PDF analizi tamamlandı ve GÜVENLE saklandı', {
        analysisResults: this.analysisResults,
        analysisResultsKeys: Object.keys(this.analysisResults),
        file_type: result.file_type,
        hasContent: result.content ? 'YES' : 'NO',
        timestamp: new Date().toISOString()
    });

    // Kullanıcıya PDF yüklendiğini göster
    const fileInfo = document.querySelector('.file-upload-info');
    if (fileInfo) {
        fileInfo.innerHTML = `<div class="alert alert-success">
            <i class="ti ti-file-check"></i>
            ${result.file_type.toUpperCase()} dosyası analiz edildi - hazır!
            <br><small>İçerik konusu girmezseniz, PDF otomatik olarak HTML'e dönüştürülecek.</small>
        </div>`;
    }

    // İçerik konusu alanını temizle ve placeholder güncelle
    const contentTopicInput = document.getElementById('contentTopic');
    if (contentTopicInput) {
        contentTopicInput.placeholder = 'Boş bırakırsanız PDF otomatik dönüştürülür';
    }

    // 💰 PDF kredi uyarısını ekle
    this.showPdfCreditWarning();

    // Eğer kullanıcı analizi beklerken üretim başlattıysa, şimdi otomatik başlat
    if (this.pendingAutoGenerate) {
        console.log('🚀 PDF analizi bitti, bekleyen içerik üretimi otomatik başlıyor...');
        this.pendingAutoGenerate = false;

        // ÖNEMLI: State'i reset et ki startGeneration çalışabilsin
        this.isGenerating = false;

        // Kısa bir nefes verip başlat
        setTimeout(() => {
            this.startGeneration();
        }, 300);
    }
};

/**
 * PDF kullanımı için kredi uyarısı göster
 */
AIContentGenerationSystem.prototype.showPdfCreditWarning = function() {
    const modalFooter = document.querySelector('#aiContentModal .modal-footer');
    if (modalFooter) {
        // Önceki uyarıyı kaldır
        const existingWarning = modalFooter.querySelector('.pdf-credit-warning');
        if (existingWarning) {
            existingWarning.remove();
        }

        // Kısa not ekle
        const warningDiv = document.createElement('div');
        warningDiv.className = 'pdf-credit-warning text-muted small mb-2';
        warningDiv.innerHTML = `<i class="ti ti-info-circle"></i> PDF yüklemek daha fazla kredi tüketir.`;

        // Modal footer'ın başına ekle
        modalFooter.insertBefore(warningDiv, modalFooter.firstChild);
    }
};

/**
 * Global AI Content Modal açma fonksiyonu
 * Page component'lerinden çağrılabilir
 */
/**
 * Alpine.js File Uploader Component
 */
window.fileUploader = function() {
    return {
        files: [],
        uploading: false,
        uploadProgress: 0,

        get hasFiles() {
            return this.files.length > 0;
        },

        handleDrop(event) {
            const files = Array.from(event.dataTransfer.files);
            this.handleFiles(files);
        },

        handleFiles(files) {
            console.log('📁 Files selected:', files.length);

            this.files = Array.from(files).filter(file => {
                const validTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                const isValid = validTypes.includes(file.type);
                if (!isValid) {
                    console.warn('⚠️ Invalid file type:', file.type);
                }
                return isValid;
            });

            console.log('✅ Valid files:', this.files.length);

            if (this.files.length > 0) {
                this.uploadFiles();
            }
        },

        async uploadFiles() {
            this.uploading = true;
            this.uploadProgress = 0;

            try {
                // Upload progress simulation
                const progressInterval = setInterval(() => {
                    if (this.uploadProgress < 90) {
                        this.uploadProgress += 10;
                    }
                }, 200);

                console.log('🚀 Starting file upload to AI system...');

                // Actual upload
                const result = await window.aiContentSystem.handleFileUpload(this.files);

                clearInterval(progressInterval);
                this.uploadProgress = 100;

                setTimeout(() => {
                    this.uploading = false;
                }, 500);

                console.log('✅ File upload completed');

            } catch (error) {
                this.uploading = false;
                this.uploadProgress = 0;
                console.error('❌ Upload failed:', error);
            }
        },

        removeFile(fileToRemove) {
            console.log('🗑️ Removing file:', fileToRemove.name);
            this.files = this.files.filter(file => file !== fileToRemove);
            if (this.files.length === 0) {
                // KRİTİK FİX: analysisResults'u güvenli şekilde temizle
                if (window.aiContentSystem) {
                    console.log('🧹 Tüm dosyalar silindi, analysis results temizleniyor');
                    window.aiContentSystem.analysisResults = {};
                    // GLOBAL STORAGE'I DA TEMİZLE
                    window.aiPdfAnalysisResults = {};
                }

                // Clear content topic if it was auto-generated
                const contentTopic = document.getElementById('contentTopic');
                if (contentTopic && contentTopic.value.includes('[DOSYADAN ÇIKARILAN İÇERİK]')) {
                    contentTopic.value = '';
                }

                // File info alanını da temizle
                const fileInfo = document.querySelector('.file-upload-info');
                if (fileInfo) {
                    fileInfo.innerHTML = '';
                }
            }
        },

        getFileIcon(type) {
            if (type === 'application/pdf') return '📄';
            if (type.startsWith('image/')) return '🖼️';
            return '📎';
        },

        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    }
};

/**
 * Global AI Content Modal açma fonksiyonu
 * Page component'lerinden çağrılabilir
 */
window.openAIContentModal = function(config = {}) {
    console.log('🚀 openAIContentModal çağırıldı:', config);

    if (window.aiContentSystem) {
        window.aiContentSystem.configure(config);
        window.aiContentSystem.showModal();
    } else {
        console.error('❌ AI Content System henüz yüklenmemiş');
    }
};

/**
 * Close content modal helper
 */
window.closeContentModal = function() {
    if (window.aiContentSystem) {
        window.aiContentSystem.closeModal();
    }
};

/**
 * 🎯 GLOBAL CONTENT RECEIVER - Tüm modüllerde çalışır
 * AI'dan üretilen içeriği herhangi bir editöre yerleştirir
 */
window.receiveGeneratedContent = function(content, targetConfig = {}) {
    console.log('📝 receiveGeneratedContent çağırıldı:', { content: content.substring(0, 100) + '...', targetConfig });

    try {
        // Editor ID'si varsa doğrudan ona yaz
        if (targetConfig.editorId) {
            const editorElement = document.getElementById(targetConfig.editorId);
            if (editorElement) {
                // HugeRTE editor mi kontrol et
                if (window.hugerte && window.hugerte.get) {
                    const editor = window.hugerte.get(targetConfig.editorId);
                    if (editor) {
                        editor.setContent(content);
                        console.log('✅ HugeRTE editor içeriği güncellendi:', targetConfig.editorId);
                        return;
                    }
                }

                // TinyMCE editor mi kontrol et
                if (window.tinymce && window.tinymce.get) {
                    const editor = window.tinymce.get(targetConfig.editorId);
                    if (editor) {
                        editor.setContent(content);
                        console.log('✅ TinyMCE editor içeriği güncellendi:', targetConfig.editorId);
                        return;
                    }
                }

                // Normal textarea
                editorElement.value = content;

                // Livewire sync için event trigger et
                const event = new Event('input', { bubbles: true });
                editorElement.dispatchEvent(event);

                console.log('✅ Textarea içeriği güncellendi:', targetConfig.editorId);
                return;
            }
        }

        // Generic editor detection fallback
        const possibleSelectors = [
            'textarea.hugerte-editor',
            'textarea[data-hugerte]',
            '.hugerte-content',
            'textarea.tinymce',
            'textarea[id*="content"]',
            'textarea[id*="body"]',
            'textarea[id*="description"]'
        ];

        for (const selector of possibleSelectors) {
            const elements = document.querySelectorAll(selector);
            if (elements.length > 0) {
                const element = elements[0]; // İlkini al

                // Editor ID'si varsa özel işlem
                if (element.id) {
                    // HugeRTE
                    if (window.hugerte && window.hugerte.get) {
                        const editor = window.hugerte.get(element.id);
                        if (editor) {
                            editor.setContent(content);
                            console.log('✅ HugeRTE fallback editor güncellendi:', element.id);
                            return;
                        }
                    }

                    // TinyMCE
                    if (window.tinymce && window.tinymce.get) {
                        const editor = window.tinymce.get(element.id);
                        if (editor) {
                            editor.setContent(content);
                            console.log('✅ TinyMCE fallback editor güncellendi:', element.id);
                            return;
                        }
                    }
                }

                // Normal textarea fallback
                element.value = content;
                const event = new Event('input', { bubbles: true });
                element.dispatchEvent(event);
                console.log('✅ Fallback textarea güncellendi:', selector);
                return;
            }
        }

        console.warn('⚠️ Hiçbir uygun editor bulunamadı, içerik console\'da gösteriliyor');
        console.log('Generated Content:', content);

    } catch (error) {
        console.error('❌ receiveGeneratedContent hatası:', error);
        console.log('Generated Content (error fallback):', content);
    }
};
