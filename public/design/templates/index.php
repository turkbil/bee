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
        <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Hazır Taslaklar</h1>
                    <p class="text-slate-400 mt-1">Kurumsal web sitesi tasarım şablonları</p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold"><?= $totalDesigns ?></div>
                    <div class="text-sm text-slate-500">tasarım</div>
                </div>
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

Bir kurumsal web sitesinin sadece ana sayfasını üret. Ana hedef "hazır tema" gibi görünmeyen, sanki o firmaya özel sıfırdan tasarlanmış hissi veren özgün bir tasarım çıkarmak. ThemeForest/WordPress teması estetiğine düşme: klasik şablon akışlarını (ezber hero + üç kart + sayaç + logo slider + testimonial carousel + "neden biz" + her yerde gradient + hover'da büyüyen kartlar) otomatik kopyalama; bu tür parçalar gerekiyorsa bile onları standart görünmeyecek şekilde yeniden yorumla ve sayfayı aynı kalıba oturtma. Her üretimde kompozisyonu gerçekten değiştir: section akışı, grid kararları, kart dili, tipografik ölçek, boşluk kullanımı, görsel yerleşim ve CTA kurgusu tekrar etmeyecek şekilde farklılaşsın; sayfa bir "demo tema" değil, gerçek bir markanın canlı sitesi gibi dursun. Kanıt/referans gerekiyorsa aynı setleri tekrar etme; gerçek marka adı taklidi yapma; anonim ama inandırıcı formatlar kullan (ör. "Sektör Lideri A/B/C", "X şehirde Y tesis", "Z standartlarına uygun" gibi) ve mümkünse kanıtı sonuç/etki diliyle ver. İçerik Türkçe olsun; sektör neyse onun terminolojisine, güven unsurlarına ve kullanıcı beklentisine göre ton, CTA ve görsel dilini uyarla; tek bir alana sabitlenme. Kullanılacak teknolojiler: Tailwind CSS v4 (CDN) ve Alpine.js (interaktivite için). İhtiyaç duyarsan işine yarayacak farklı kütüphaneleri de CDN olarak ekleyebilirsin (slider, animasyon, ikon seti vb.). Sayfa modern, profesyonel ve premium ama sakin hissettirmeli; aşırı animasyon ve gösteriş yok.

Responsive tasarım olsun (mobil öncelikli). Mobil menü, dark/light mode toggle ve arama özelliği dahil edilsin. PC'de geniş/fluid tasarım tercih edilsin; dar ortalanmış kutular yerine ekranın genişliğini kullanan layout'lar yap. Simetri ve hizalama çok önemli; elementler arasındaki dengeye dikkat et.

Dark/Light mode renk kontrastı çok önemli: Dark modda beyaz veya çok açık arka planlar olmasın; light modda koyu arka planlar olmasın. Her modda tutarlı ve okunabilir kontrast sağla. Renk geçişleri temiz olsun.

Görseller için Pixabay, Unsplash gibi ücretsiz stok kaynaklardan gerçek görseller kullan (placeholder değil). Sektöre uygun, kaliteli ve profesyonel görseller seç.

Header çok önemli; mutlaka mega menu yapısı olsun - alt menüler açıldığında kategoriler, açıklamalar ve görseller içeren geniş dropdown paneller şeklinde tasarlansın. Dropdown menüler kendi linki altında ortalanmış olsun; tam sayfa genişliğinde dropdown ise sayfaya ortalanmış olmalı.

Hover efektleri önemli ve mutlaka olsun; ancak kartlar zıplamasın, scale ile yaklaşmasın, abartılı shadow almasın. Her şey yerinde kalsın, hover durumunda değişim kartın içinde gerçekleşsin (renk geçişi, ikon hareketi, alt çizgi, arka plan tonu değişimi gibi subtle efektler).

Göze hoş gelen modern efektler kullanılabilir: ciddi ve şık gradient text'ler, subtle glassmorphism, soft glow efektleri, zarif border gradient'ları gibi. Tasarıma ve sektöre uygun düştüğü sürece bu tarz premium detaylar eklenebilir; ancak abartıya kaçma, her efekt amaca hizmet etsin.

Sticky header olsun; scroll'da header sabit kalsın veya küçülsün. Scroll animasyonları kullanılabilir (AOS, GSAP vb.) - elementler görünürken yumuşak geçişler. Back to top butonu olsun; sayfa uzunsa sağ altta yukarı çık butonu. Sabit iletişim butonu (WhatsApp, telefon) uygun yerlerde kullanılabilir.

Tutarlılık çok önemli: Tasarımda ne seçildiyse (rounded köşeler mi, sert mi; hangi renk paleti; hangi tipografi) sayfanın tamamında aynı dil devam etmeli. Kartlar, butonlar, spacing, border-radius hep tutarlı olsun. Section geçişleri dark/light mode'a göre sırıtmamalı; light modda aniden koyu arka plana geçme, dark modda aniden beyaza geçme - geçişler yumuşak ve mantıklı olsun.

Tipografi hiyerarşisi net olmalı; başlıklar arasındaki boyut farkları belirgin olsun. Paragraf metinlerinde rahat okunabilir satır aralığı kullan. Mobilde butonlar ve tıklanabilir alanlar yeterli büyüklükte olsun. Metin kontrastı yeterli olmalı; arka plan üzerinde metin her zaman rahat okunabilsin.

Header sektöre ve tarza göre farklı yaklaşımlar deneyebilir; klasik yatay menü, hamburger menü, yan logo, orta logo gibi varyasyonlar olabilir. Zengin footer şart; sadece copyright değil - iletişim bilgileri, sosyal medya linkleri, hızlı erişim linkleri, mini site haritası, varsa sertifikalar/güven rozetleri içermeli.

Sektöre göre uygun olacak şekilde ana sayfada ihtiyaç duyulan modülleri sen seç ve kurgula; ancak her zaman gerçek kullanım hissi veren temel kullanıcı deneyimleri olsun: net bir header navigasyonu, güçlü bir hero, sektöre uygun ana içerik blokları, güven/kanıt alanları, güncel içerik (haber/duyuru/blog gibi) ve güçlü bir footer. Tüm çıktı tek dosya index.html olarak verilecek ve yalnızca kod döndürülecek. Teslim etmeden önce tasarımı gözden geçir; tutarlılık, kontrast, responsive davranış ve hover efektlerinin düzgün çalıştığından emin ol.

---

sektör: <span id="sektorValue">genel kurumsal</span>
tarz: <span id="tarzValue">profesyonel ve dengeli</span><span id="ekBilgiSection"></span></pre>
        </div>
        <p class="text-sm text-slate-500 mt-4">
            <i class="fas fa-info-circle mr-1"></i>
            Sektör ve tarz alanlarını doldurun, prompt otomatik güncellenir. Sonra kopyalayıp herhangi bir AI'ya yapıştırın.
            <a href="/design/sectors/" class="text-blue-400 hover:text-blue-300 ml-2">
                <i class="fas fa-palette mr-1"></i>Tarz & Sektör Örnekleri
            </a>
        </p>
    </section>

    <script>
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
            Son güncelleme: <?= date('d.m.Y H:i') ?>
        </div>
    </footer>

</body>
</html>
