<?php
/**
 * Hazır Taslaklar - Otomatik Tasarım Galerisi
 * Klasörleri tarar ve tasarımları listeler
 */

$baseDir = __DIR__;
$baseUrl = '/design/templates';

// Tüm taslak kategorilerini tara
$categories = [];
foreach (glob("$baseDir/*", GLOB_ONLYDIR) as $categoryPath) {
    $categoryName = basename($categoryPath);
    $versions = [];

    // Versiyonları tara
    foreach (glob("$categoryPath/*", GLOB_ONLYDIR) as $versionPath) {
        $versionName = basename($versionPath);
        $indexFile = "$versionPath/index.html";

        if (file_exists($indexFile)) {
            // HTML'den title çek
            $html = file_get_contents($indexFile);
            preg_match('/<title>(.*?)<\/title>/i', $html, $matches);
            $title = $matches[1] ?? $versionName;

            $versions[] = [
                'name' => $versionName,
                'title' => $title,
                'url' => "$baseUrl/$categoryName/$versionName/",
                'modified' => filemtime($indexFile)
            ];
        }
    }

    if (!empty($versions)) {
        // Versiyon adına göre sırala (v1, v2, v3...)
        usort($versions, fn($a, $b) => strcmp($a['name'], $b['name']));

        $categories[] = [
            'name' => $categoryName,
            'displayName' => ucwords(str_replace('-', ' ', $categoryName)),
            'versions' => $versions,
            'count' => count($versions)
        ];
    }
}

// Alfabetik sırala
usort($categories, fn($a, $b) => strcmp($a['name'], $b['name']));

$totalDesigns = array_sum(array_column($categories, 'count'));
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hazır Taslaklar | Design Templates</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-white min-h-screen">

    <!-- Header -->
    <header class="border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-6 py-6">
            <!-- Top Nav -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-6">
                    <a href="/design/templates/" class="text-white font-medium border-b-2 border-violet-500 pb-1">
                        <i class="fas fa-layer-group mr-2 text-violet-400"></i>Taslaklar
                    </a>
                    <a href="/design/sectors/" class="text-slate-400 hover:text-white transition pb-1 border-b-2 border-transparent hover:border-slate-600">
                        <i class="fas fa-palette mr-2"></i>Tarz & Sektör
                    </a>
                </div>
                <div class="text-xs text-slate-500 bg-slate-900 px-3 py-2 rounded-lg">
                    <span class="text-2xl font-bold text-white mr-1"><?= $totalDesigns ?></span> tasarım
                </div>
            </div>
            <!-- Title -->
            <div>
                <h1 class="text-2xl font-bold">Hazır Taslaklar</h1>
                <p class="text-slate-400 mt-1">Kurumsal web sitesi tasarım şablonları</p>
            </div>
        </div>
    </header>

    <!-- Content -->
    <main class="max-w-7xl mx-auto px-6 py-12">

        <?php if (empty($categories)): ?>
            <div class="text-center py-20">
                <i class="fas fa-folder-open text-6xl text-slate-700 mb-4"></i>
                <p class="text-slate-500">Henüz tasarım eklenmemiş</p>
            </div>
        <?php else: ?>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($categories as $category): ?>
                    <div class="bg-slate-900 border border-slate-800 rounded-lg p-6">
                        <h2 class="text-lg font-semibold mb-4">
                            <?= htmlspecialchars($category['displayName']) ?>
                        </h2>
                        <div class="space-y-2">
                            <?php foreach ($category['versions'] as $version): ?>
                                <a href="<?= $version['url'] ?>" target="_blank" class="flex items-center justify-between p-3 bg-slate-800/50 hover:bg-slate-800 rounded transition group">
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs font-mono text-slate-500 bg-slate-700 px-2 py-1 rounded">
                                            <?= htmlspecialchars($version['name']) ?>
                                        </span>
                                        <span class="text-sm text-slate-300 group-hover:text-white transition truncate max-w-[180px]">
                                            <?= htmlspecialchars($version['title']) ?>
                                        </span>
                                    </div>
                                    <i class="fas fa-external-link-alt text-xs text-slate-600 group-hover:text-slate-400 transition"></i>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>

    </main>

    <!-- Prompt Section -->
    <section class="max-w-7xl mx-auto px-6 py-12 mt-8 border-t border-slate-800">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold">
                <i class="fas fa-terminal text-slate-500 mr-2"></i>
                Tasarım Prompt'u
            </h2>
        </div>

        <!-- Tasarım Stilleri (Sectors sayfasından aktarılır) -->
        <div class="mb-6" id="designStylesContainer" style="display: none;">
            <div class="flex items-center justify-between mb-2">
                <label class="text-sm text-slate-400 flex items-center gap-2">
                    <i class="fas fa-palette text-violet-400"></i>
                    Tasarım Stilleri
                    <span id="styleCountBadge" class="bg-violet-600 text-white text-xs px-2 py-0.5 rounded-full"></span>
                </label>
                <div class="flex items-center gap-2">
                    <a href="/design/sectors/" class="text-xs text-slate-500 hover:text-violet-400 transition">
                        <i class="fas fa-edit mr-1"></i>Düzenle
                    </a>
                    <button onclick="clearDesignStyles()" class="text-xs text-slate-500 hover:text-red-400 transition">
                        <i class="fas fa-times mr-1"></i>Temizle
                    </button>
                </div>
            </div>
            <textarea id="designStylesInput" readonly rows="4" class="w-full bg-slate-800/50 border border-violet-900/50 rounded-lg px-4 py-3 text-violet-200 text-sm focus:outline-none resize-none cursor-default"></textarea>
            <p class="text-xs text-slate-600 mt-1">
                <i class="fas fa-info-circle mr-1"></i>
                Bu stiller <a href="/design/sectors/" class="text-violet-400 hover:underline">Tarz & Sektör</a> sayfasından aktarıldı
            </p>
        </div>

        <!-- Sektör, Tarz ve Ek Bilgiler Input -->
        <div class="grid sm:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm text-slate-400 mb-2">Sektör</label>
                <input type="text" id="sektorInput" placeholder="örn: sağlık, belediye, fabrika..." class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:border-slate-500 transition">
            </div>
            <div>
                <label class="block text-sm text-slate-400 mb-2">Tarz</label>
                <input type="text" id="tarzInput" placeholder="örn: minimal, prestijli, samimi..." class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:border-slate-500 transition">
            </div>
        </div>
        <div class="mb-6">
            <label class="block text-sm text-slate-400 mb-2">Ek Bilgiler <span class="text-slate-600">(opsiyonel)</span></label>
            <textarea id="ekBilgiInput" rows="3" placeholder="örn: Ana faaliyet madencilik (%60), yan kollar mühendislik ve mimarlık. Hedef kitle kurumsal firmalar. Logo rengi lacivert..." class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:border-slate-500 transition resize-none"></textarea>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-lg p-6 relative">
            <button onclick="copyPrompt()" class="absolute top-4 right-4 text-sm bg-slate-800 hover:bg-slate-700 px-4 py-2 rounded transition">
                <i class="fas fa-copy mr-2"></i>Kopyala
            </button>
            <pre id="promptText" class="text-sm text-slate-300 whitespace-pre-wrap leading-relaxed pr-24">## KAYIT BİLGİLERİ

Dosya konumu: public/design/templates/{kategori-adi}/{versiyon}/index.html

Örnek:
- public/design/templates/saglik-klinigi/v1/index.html
- public/design/templates/belediye-sitesi/v2-koyu/index.html

Kurallar:
- Kategori: sektör veya proje adı (küçük harf, tire ile)
- Versiyon: v1, v2, v3 veya v1-aciklama, v2-minimal gibi
- Dosya adı: her zaman index.html

---

## TASARIM TALİMATLARI

ANA HEDEF: Profesyonel, düzenli ve sektöre uygun kurumsal web sitesi. Temiz layout, okunabilir tipografi, tutarlı spacing. İçerik Türkçe olsun.

KULLANILACAK TEKNOLOJİLER: Tailwind CSS v4 (CDN), Alpine.js (interaktivite), ve gerektiğinde ek kütüphaneler (Swiper, GSAP, AOS, Lottie vb. CDN ile). Sayfa modern, profesyonel ve premium ama sakin hissettirmeli.

FONT SEÇİMİ: Google Fonts'tan sektöre uygun font çifti seç. Başlıklar için display veya serif font, gövde için okunabilir sans-serif tercih edilebilir. Font ağırlık çeşitliliği kullan (300, 400, 500, 600, 700). Her üretimde farklı font kombinasyonu dene.

RENK SİSTEMİ: CSS custom properties (değişkenler) ile renk sistemi kur. Ana renk (--color-primary), ikincil renk (--color-secondary), vurgu rengi (--color-accent) ve nötr tonlar tanımla. Böylece logo/marka değişince sadece değişkenler güncellenir, tüm site uyum sağlar. Sektöre uygun renk psikolojisi uygula ama renkleri tutumlu kullan; her yeri renkli yapma.

İKON SİSTEMİ: FontAwesome Pro kullan (fa-thin, fa-light, fa-regular, fa-solid, fa-duotone). Varsayılan durumda ince stiller (thin/light) tercih et, hover durumunda solid'e geçiş yap - bu geçiş animasyonlu olsun (transition ile). Bu yaklaşım sayfaya hareket ve interaktivite katar.

HERO SECTION: Her üretimde farklı hero yaklaşımı dene - tam ekran görsel/video, split layout (yarı metin yarı görsel), slider/carousel, minimalist tipografik hero, overlapping elementler, paralaks efekt, animasyonlu arka plan, geometrik şekiller. Klasik "sol metin + sağ görsel" kalıbını her seferinde kullanma. Hero'da animasyon kütüphaneleri (GSAP, Lottie, CSS animations) kullanılabilir.

İMZA BÖLÜM: Sektör ve içerik uygunsa, markaya özel özgün bir bölüm ekle - klasik tema bileşenlerinin (hero, 3 kart, sayaç, logo slider) varyasyonu olmayan, o firmaya has bir section. Örnekler: Süreç/Operasyon Akışı, Hizmet Haritası, Uyum & Standartlar Paneli, Strateji Manifestosu, Mini Vaka Anlatısı, Soru-Cevap Ağacı. Bu zorunlu değil; sektöre göre karar ver. Basit bir restoran sitesi için gereksiz olabilir, endüstriyel firma için çok değerli olabilir.

LAYOUT VE BOŞLUK: Section'lar tam genişlik (fluid) olsun, içerik ise max-width container içinde ortalanabilir. Cömert boşluk (whitespace) kullan; section arası padding yeterli olsun, elementler birbirine yapışık durmasın. "Nefes alan" tasarım hedefle. Layout çeşitliliği önemli: her section grid olmasın; bazı yerlerde asimetrik layout, bazı yerlerde full-width görsel, bazı yerlerde farklı grid yapıları kullan - ama bu sektör ve tasarıma uygun olsun, zorlamayla değil.

Responsive tasarım olsun (mobil öncelikli). Mobil menü, dark/light mode toggle ve arama özelliği dahil edilsin. Dark/light mode premium bir özellik, mutlaka olsun. PC'de geniş/fluid tasarım tercih edilsin. Simetri ve hizalama çok önemli; elementler arasındaki dengeye dikkat et. Semantic HTML kullan: &lt;header&gt;, &lt;nav&gt;, &lt;main&gt;, &lt;section&gt;, &lt;article&gt;, &lt;aside&gt;, &lt;footer&gt; gibi anlamlı etiketler tercih edilsin.

Dark/Light mode renk kontrastı çok önemli: Dark modda beyaz veya çok açık arka planlar olmasın; light modda koyu arka planlar olmasın. Her modda tutarlı ve okunabilir kontrast sağla. Renk geçişleri temiz olsun. Tema tercihi ilk yüklemede flash (FOUC) olmadan uygulanacak - HTML'de class veya data attribute sayfa yüklenmeden önce set edilecek.

Görseller için Pixabay, Unsplash gibi ücretsiz stok kaynaklardan gerçek görseller kullan (placeholder değil). Sektöre uygun, kaliteli ve profesyonel görseller seç.

Header çok önemli; mümkünse mega menu yapısı olsun - alt menüler açıldığında kategoriler, açıklamalar ve görseller içeren geniş dropdown paneller şeklinde tasarlansın. Mega menu profesyonel ve kurumsal his yaratır. KRİTİK: Dropdown menüler MUTLAKA kendi parent linkinin altında ortalanmış açılmalı (left: 50%; transform: translateX(-50%);). Tam sayfa genişliğinde mega dropdown ise viewport'a ortalanmış olmalı. Dropdown asla linkin solundan veya sağından başlayıp taşmamalı - her zaman linke göre simetrik ortalanmalı. Header sektöre ve tarza göre farklı yaklaşımlar deneyebilir; klasik yatay menü, hamburger menü, yan logo, orta logo gibi varyasyonlar olabilir.

Hover efektleri önemli ve mutlaka olsun; ancak kartlar zıplamasın, scale ile yaklaşmasın, abartılı shadow almasın. Her şey yerinde kalsın, hover durumunda değişim kartın içinde gerçekleşsin (renk geçişi, ikon değişimi thin→solid, alt çizgi, arka plan tonu değişimi gibi subtle efektler).

Göze hoş gelen modern efektler kullanılabilir: ciddi ve şık gradient text'ler, subtle glassmorphism, soft glow efektleri, zarif border gradient'ları gibi. Tasarıma ve sektöre uygun düştüğü sürece bu tarz premium detaylar eklenebilir; ancak abartıya kaçma, her efekt amaca hizmet etsin.

Scroll animasyonları kullanılabilir (AOS, GSAP vb.) - elementler görünürken yumuşak geçişler. Gerekirse sticky header, back to top butonu, sabit iletişim butonu (WhatsApp, telefon) eklenebilir - bunlar opsiyonel, tasarıma uygunsa kullan.

Tutarlılık çok önemli: Tasarımda ne seçildiyse (rounded köşeler mi, sert mi; hangi renk paleti; hangi tipografi) sayfanın tamamında aynı dil devam etmeli. Kartlar, butonlar, spacing, border-radius hep tutarlı olsun. Section geçişleri dark/light mode'a göre sırıtmamalı; geçişler yumuşak ve mantıklı olsun.

Tipografi hiyerarşisi net olmalı; başlıklar arasındaki boyut farkları belirgin olsun. Paragraf metinlerinde rahat okunabilir satır aralığı kullan. Mobilde butonlar ve tıklanabilir alanlar yeterli büyüklükte olsun.

Zengin footer şart; sadece copyright değil - iletişim bilgileri, sosyal medya linkleri, hızlı erişim linkleri, mini site haritası, varsa sertifikalar/güven rozetleri içermeli.

KANIT/GÜVEN ALANI: Logo slider veya testimonial carousel "hazır tema" hissi verir. Bunlar yerine daha özgün formatlar tercih et: Mini vaka özeti (problem→yaklaşım→sonuç), Uyum & kalite maddeleri (ISO/TSE rozet + kısa açıklama), Süreç & SLA bilgisi (yanıt süreleri, çalışma kapsamı), Etki ifadeleri (sayısal olmasa bile sonuç odaklı). Sektöre göre karar ver; bazen klasik referans logoları da uygun olabilir ama carousel/slider yerine statik grid tercih et.

ÖZGÜNLÜK PRENSİBİ: Uzun sayfa = iyi sayfa değil. Sektöre ve hedefe göre karar ver. Bir restoran sitesi 3-4 section ile mükemmel olabilir; bir holding sitesi 8-10 section gerektirebilir. Gereksiz section ekleme, her bölüm bir amaca hizmet etsin. AI olarak yorumlama hakkın var: sektörün ruhuna göre neyin gerekli neyin gereksiz olduğuna sen karar ver.

💡 SON KONTROL: Teslim etmeden önce sayfayı gözden geçir - klişe kalıplara düşmüş müsün? Özgün ve sektöre özel hissediyor mu?

Sektöre göre uygun olacak şekilde ana sayfada ihtiyaç duyulan modülleri sen seç ve kurgula. Section sayısı önemli değil, hedefe uygunluk önemli. Net bir header navigasyonu, güçlü ve özgün bir hero, sektöre uygun ana içerik blokları, güven/kanıt alanları, güncel içerik (haber/duyuru/blog gibi) ve güçlü bir footer olsun. Tüm çıktı tek dosya index.html olarak verilecek ve yalnızca kod döndürülecek. Teslim etmeden önce tasarımı gözden geçir; tutarlılık, kontrast, responsive davranış ve hover efektlerinin düzgün çalıştığından emin ol.

---

<span id="designStylesSection"></span>sektör: <span id="sektorValue">genel kurumsal</span>
tarz: <span id="tarzValue">profesyonel ve dengeli</span><span id="ekBilgiSection"></span></pre>
        </div>
        <p class="text-sm text-slate-500 mt-4">
            <i class="fas fa-info-circle mr-1"></i>
            Sektör ve tarz alanlarını doldurun, prompt otomatik güncellenir. Sonra kopyalayıp herhangi bir AI'ya yapıştırın.
        </p>
    </section>

    <script>
    // Sayfa yüklendiğinde localStorage'dan tasarım stillerini oku
    document.addEventListener('DOMContentLoaded', function() {
        const designStyles = localStorage.getItem('designStyles');
        const styleCount = localStorage.getItem('designStylesCount');

        if (designStyles) {
            // Container'ı göster
            document.getElementById('designStylesContainer').style.display = 'block';

            // Textarea'ya yaz
            document.getElementById('designStylesInput').value = designStyles;

            // Badge'e sayı yaz
            if (styleCount) {
                document.getElementById('styleCountBadge').textContent = styleCount + ' stil';
            }

            // Prompt'a ekle
            updateDesignStylesInPrompt(designStyles);
        }
    });

    // Tasarım stillerini prompt'a ekle
    function updateDesignStylesInPrompt(styles) {
        const section = document.getElementById('designStylesSection');
        if (styles) {
            section.textContent = 'TASARIM STİLLERİ (Seçilen):\n' + styles + '\n\n';
        } else {
            section.textContent = '';
        }
    }

    // Tasarım stillerini temizle
    function clearDesignStyles() {
        localStorage.removeItem('designStyles');
        localStorage.removeItem('designStylesCount');
        document.getElementById('designStylesContainer').style.display = 'none';
        document.getElementById('designStylesInput').value = '';
        document.getElementById('designStylesSection').textContent = '';
    }

    // Input değişince prompt'u güncelle
    document.getElementById('sektorInput').addEventListener('input', function() {
        const value = this.value.trim() || 'genel kurumsal';
        document.getElementById('sektorValue').textContent = value;
    });

    document.getElementById('tarzInput').addEventListener('input', function() {
        const value = this.value.trim() || 'profesyonel ve dengeli';
        document.getElementById('tarzValue').textContent = value;
    });

    document.getElementById('ekBilgiInput').addEventListener('input', function() {
        const value = this.value.trim();
        const section = document.getElementById('ekBilgiSection');
        if (value) {
            section.textContent = '\nek bilgiler: ' + value;
        } else {
            section.textContent = '';
        }
    });

    function copyPrompt() {
        const text = document.getElementById('promptText').innerText;
        navigator.clipboard.writeText(text).then(() => {
            const btn = event.target.closest('button');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check mr-2"></i>Kopyalandı!';
            setTimeout(() => btn.innerHTML = originalText, 2000);
        });
    }
    </script>

    <!-- Footer -->
    <footer class="border-t border-slate-800 mt-12">
        <div class="max-w-7xl mx-auto px-6 py-6 text-center text-sm text-slate-500">
            tuufi.com/design
        </div>
    </footer>

</body>
</html>
