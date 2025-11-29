# 🤖 SHOP PRODUCT AI - FULL AUTOMATION PROMPT

**Tarih:** 2025-11-28
**Hedef:** Yedek Parça kategorisindeki ürünlere AI içerik ve görsel üret
**Guide:** https://ixtif.com/readme/2025/11/28/shop-product-ai-ULTRA-SIMPLE/

---

## 📋 GÖREV

Tenant 2 (ixtif.com) için **"Yedek Parça" kategorisindeki (category_id: 7) TÜM ürünleri** AI ile işle.

**⚠️ SADECE YEDEK PARÇA!** Forklift, transpalet vb. ana ürünlere DOKUNMA!

### 🔀 İKİ SENARYO

**SENARYO 1: Body Boş** (1 ürün)
- Başlık + Kategori adından içerik üret
- Leonardo AI ile görsel oluştur
- SEO ayarlarını doldur

**SENARYO 2: Body Dolu** (689 ürün) - **ANA GÖREV!**
- Mevcut yazıyı oku ve analiz et
- Yazıyı genişlet ve sisteme uyarla (V6 Perfect template)
- **Teknik özellikleri çıkar:**
  - Mevcut body'de teknik detaylar varsa → `primary_specs` array'ine ekle
  - Boyut, ağırlık, voltaj, malzeme vb. → Array of objects formatında
- Leonardo AI ile görsel oluştur
- SEO ayarlarını güncelle

### ✅ YAPILACAKLAR

1. **Sırayla TÜM ürünleri işle** (ID sırasına göre, küçükten büyüğe)
2. Her ürün için:
   - Body'yi kontrol et (boş/dolu)
   - **Mevcut body varsa:** Oku, analiz et, genişlet, teknik özellikleri çıkar
   - **Body boşsa:** Başlık + kategoriden üret
   - Leonardo AI ile **1 adet yatay (16:9) stok fotoğraf** oluştur
   - **10,000+ karakter** detaylı body içeriği yaz (TR + EN)
   - SEO ayarlarını doldur/güncelle
   - Database'e kaydet
   - Cache temizle
3. **Progress log tut:** `readme/claude-docs/shop-ai-progress.md`
4. Her ürün tamamlandığında log'a ekle

---

## ⚡ BENZERÜRÜNSTRATEJİSİ

**Aynı kategorideki benzer ürünlerde:**
1. Önceki ürünün body'sini kopyala
2. Sadece seri numarasını değiştir (örn: 422 → 423)
3. Teknik özelliklerdeki seri numarasını güncelle
4. Leonardo AI ile yeni görsel oluştur
5. Kaydet

**Örnek:**
- Product #332 (Akson Keçe Kapağı - 422) → Body oluşturuldu
- Product #333 (Akson Keçe Kapağı - 423) → 332'den kopyalandı, "422" → "423" değiştirildi

**Avantaj:** Hız + Tutarlılık ✅

---

## 🔴 KRİTİK KURALLAR

### 1. BODY TEMPLATE (NOKTASINA KADAR AYNI!)

**⚠️ Aşağıdaki HTML yapısını AYNEN kullan! Sadece içerikleri değiştir!**

```html
<div class="prose max-w-none">

<!-- 1. TANITIM + GÖRSEL -->
<section class="mb-20">
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2">
      <p class="text-lg text-gray-700 dark:text-gray-300 leading-relaxed mb-5">
        <strong class="text-gray-900 dark:text-gray-100 font-semibold">[Ürün Adı]</strong>, [tanıtım]. <strong class="text-gray-900 dark:text-gray-100 font-semibold">[Özellik]</strong> [devam].
      </p>
      <p class="text-lg text-gray-700 dark:text-gray-300 leading-relaxed mb-5">[2. paragraf]</p>
      <p class="text-lg text-gray-700 dark:text-gray-300 leading-relaxed">[3. paragraf]</p>
    </div>
    
    <div class="lg:col-span-1">
      <div class="sticky top-24">
        <a href="[IMAGE_URL]" class="glightbox" data-gallery="product-gallery">
          <img src="[IMAGE_URL]" alt="[Ürün Adı]" class="w-full rounded-xl object-cover shadow-lg hover:shadow-2xl transition-shadow duration-300" loading="lazy">
        </a>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-3 italic text-center">[Ürün Adı]</p>
      </div>
    </div>
  </div>
</section>

<!-- 2. SORUNLAR VE ÇÖZÜMLER -->
<section class="mb-20">
  <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-8">
    <i class="fas fa-lightbulb mr-3 text-blue-500"></i>Yaygın Sorunlar ve Çözümler
  </h2>
  
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <!-- 4 sorun kartı (bg-gray-50 dark:bg-gray-800) -->
    <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg border-2 border-gray-200 dark:border-gray-700 hover:border-blue-400 transition-all text-center">
      <i class="fas fa-[icon] text-red-500 text-5xl mb-4"></i>
      <h4 class="font-semibold text-lg mb-2 text-gray-900 dark:text-gray-100">[Başlık]</h4>
      <p class="text-gray-600 dark:text-gray-400">[Açıklama]</p>
    </div>
  </div>
  
  <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-8 rounded-xl text-white shadow-lg">
    <h3 class="font-semibold text-2xl mb-4 flex items-center gap-3">
      <i class="fas fa-check-circle text-3xl"></i> İXTİF Çözümü
    </h3>
    <p class="text-lg leading-relaxed">
      <span class="font-semibold">[Ürün]</span> ile [çözüm]. <span class="font-semibold">[vurgu]</span> [devam].
    </p>
  </div>
</section>

<!-- 3. ÖZELLİKLER -->
<section class="mb-20">
  <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-8">
    <i class="fas fa-fire mr-3 text-blue-500"></i>Neden Bu Ürünü Tercih Etmelisiniz?
  </h2>
  
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- 6 özellik kartı (bg-gray-50 dark:bg-gray-800) -->
  </div>
</section>

<!-- 4. KULLANIM ALANLARI -->
<section class="mb-20">
  <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-8">
    <i class="fas fa-industry mr-3 text-blue-500"></i>Kullanım Alanları
  </h2>
  
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- 4 alan kartı (GLASS EFEKTİ!) -->
    <div class="bg-white/70 dark:bg-white/5 backdrop-blur-md p-6 rounded-xl border border-white/30 dark:border-white/10 hover:border-blue-400 transition-all">
      <h4 class="font-semibold text-xl mb-3 text-gray-900 dark:text-white">
        <i class="fas fa-[icon] mr-2 text-blue-500"></i>[Başlık]
      </h4>
      <p class="text-gray-700 dark:text-gray-300">[Açıklama]</p>
    </div>
  </div>
</section>

<!-- 5. İLETİŞİM -->
<div class="bg-white/70 dark:bg-white/5 backdrop-blur-md border border-white/30 dark:border-white/10 p-8 rounded-xl">
  <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">[CTA Başlığı]</h3>
  <p class="text-gray-700 dark:text-gray-300 mb-4 leading-relaxed">[Paragraf 1]</p>
  <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
    [Paragraf 2] <span class="font-semibold text-gray-900 dark:text-gray-100">Toplu siparişlerde özel indirim</span> [devam]
  </p>
</div>

</div>
```

### 2. TASARIM KURALLARI

❌ **YASAK:**
- Gökkuşağı renkler (cyan, teal, indigo, violet, orange)
- Gradient background (from-slate-50, from-cyan-50)
- Renkli text (text-cyan-900, text-teal-900)
- "Karşılaştığınız Sorunlar" başlığı
- Dikey/kare görseller
- 2 görsel (sadece 1 tane!)

✅ **ZORUNLU:**
- Problem/Özellik kartları: `bg-gray-50 dark:bg-gray-800`
- Kullanım alanları: `bg-white/70 dark:bg-white/5 backdrop-blur-md`
- Tüm text: `text-gray-900 dark:text-white`
- Başlık: "Yaygın Sorunlar ve Çözümler" (kibar!)
- Görsel: YATAY 16:9 (1456x816)
- Görsel alt yazısı: Basit (örn: "Akson Mili Kapağı 414")

### 3. LEONARDO AI PROMPT (11 KURAL - SHOP ADAPTASYONU)

**Stok fotoğraf yaklaşımı kullan!**

```php
$prompt = "Professional stock photography of [forklift spare part name] systematically organized on industrial warehouse shelving, clean professional product display with multiple units creating depth, neutral even lighting from overhead LED panels creating shadow-free catalog presentation, shot straight-on at eye level for clear product documentation, centered composition showing organized inventory management, photographed with professional medium format camera Hasselblad H6D-100c with HC 80mm f/2.8 lens at f/8 for maximum depth of field sharpness, clean clinical white balance 5500K neutral color temperature, no artistic effects - perfectly clean sharp catalog photography, professional warehouse environment documentation style, standard commercial product photography aesthetic suitable for e-commerce catalog --ar 16:9 --style raw";

$imageData = $leonardoService->generateFromPrompt($prompt, [
    'width' => 1456,  // 16:9!
    'height' => 816,
    'style' => 'cinematic'
]);
```

### 4. PROGRESS LOG

**Dosya:** `readme/claude-docs/shop-ai-progress.md`

```php
$logFile = '/var/www/vhosts/tuufi.com/httpdocs/readme/claude-docs/shop-ai-progress.md';

// İlk çalıştırma
if (!file_exists($logFile)) {
    file_put_contents($logFile, "# Shop Product AI - Progress Log\n\nBaşlangıç: " . date('Y-m-d H:i:s') . "\n\n## Tamamlanan Ürünler\n\n");
}

// Her ürün sonrası
$logEntry = sprintf(
    "- [✅] **Product %d** - %s\n  - URL: https://ixtif.com/shop/%s\n  - Tamamlanma: %s\n  - Görsel: %s\n\n",
    $product->product_id,
    $product->getTranslation('title', 'tr'),
    $product->getTranslation('slug', 'tr'),
    date('Y-m-d H:i:s'),
    $media->getFullUrl()
);

file_put_contents($logFile, $logEntry, FILE_APPEND);
```

---

## 🚀 BAŞLA!

**İlk adım - Yedek Parça kategorisi ve TÜM alt kategorileri:**

```php
// 1. Yedek Parça kategorisi ve TÜM alt kategorilerini recursive bul
function getSparePartsCategoryIds() {
    $db = DB::connection('tenant_ixtif');

    // Recursive query ile tüm alt kategorileri bul
    $query = "
        WITH RECURSIVE CategoryTree AS (
            SELECT category_id, parent_id, 1 as depth
            FROM shop_categories
            WHERE category_id = 7

            UNION ALL

            SELECT c.category_id, c.parent_id, ct.depth + 1
            FROM shop_categories c
            INNER JOIN CategoryTree ct ON c.parent_id = ct.category_id
            WHERE ct.depth < 10
        )
        SELECT category_id FROM CategoryTree
    ";

    $results = $db->select($query);
    return collect($results)->pluck('category_id')->toArray();
}

$sparePartsCategoryIds = getSparePartsCategoryIds();

echo "📦 Yedek Parça kategorileri: " . count($sparePartsCategoryIds) . " kategori bulundu\n";
echo "📋 Kategori ID'leri: " . implode(', ', $sparePartsCategoryIds) . "\n\n";

// 2. Bu kategorilerdeki boş body'li ürünleri bul
$totalProducts = Modules\Shop\App\Models\ShopProduct::query()
    ->where('tenant_id', 2)
    ->whereIn('category_id', $sparePartsCategoryIds)
    ->count();

$emptyBodyCount = Modules\Shop\App\Models\ShopProduct::query()
    ->where('tenant_id', 2)
    ->whereIn('category_id', $sparePartsCategoryIds)
    ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(body, '$.tr')) IS NULL OR JSON_UNQUOTE(JSON_EXTRACT(body, '$.tr')) = ''")
    ->count();

echo "📊 Toplam yedek parça: {$totalProducts}\n";
echo "🔴 Body'si boş: {$emptyBodyCount}\n";
echo "✅ Body'si dolu: " . ($totalProducts - $emptyBodyCount) . "\n\n";

// 3. İlk boş ürünü bul
$nextProduct = Modules\Shop\App\Models\ShopProduct::query()
    ->where('tenant_id', 2)
    ->whereIn('category_id', $sparePartsCategoryIds)
    ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(body, '$.tr')) IS NULL OR JSON_UNQUOTE(JSON_EXTRACT(body, '$.tr')) = ''")
    ->orderBy('product_id', 'ASC')
    ->first();

if ($nextProduct) {
    $category = Modules\Shop\App\Models\ShopCategory::find($nextProduct->category_id);
    echo "📌 İlk işlenecek ürün:\n";
    echo "  - ID: {$nextProduct->product_id}\n";
    echo "  - Başlık: {$nextProduct->getTranslation('title', 'tr')}\n";
    echo "  - Kategori: {$category->getTranslation('title', 'tr')} (ID: {$category->category_id})\n";
    echo "  - URL: https://ixtif.com/shop/{$nextProduct->getTranslation('slug', 'tr')}\n";
} else {
    echo "🎉 TÜM YEDEK PARÇA ÜRÜNLERİ TAMAMLANDI!\n";
}
```

**⚠️ İSTATİSTİKLER (2025-11-28):**
- Toplam yedek parça: **690 ürün**
- Body'si dolu: **689 ürün** (genişletilecek + teknik özellikler çıkarılacak)
- Body'si boş: **1 ürün** (başlık + kategoriden üretilecek)
- Kategori sayısı: **100 alt kategori** (7, 8-106)

---

## 🔄 DETAYLI İŞLEM ADIMLARI

### SENARYO 1: Body Boş (1 ürün)

```php
if (empty($currentBody)) {
    echo "📝 Body boş, başlık ve kategoriden içerik üretiliyor...\n";

    // 1. Başlık ve kategori al
    $title = $product->getTranslation('title', 'tr');
    $category = Modules\Shop\App\Models\ShopCategory::find($product->category_id);
    $categoryName = $category->getTranslation('title', 'tr');

    // 2. İçerik üret (AI)
    $bodyContent = "Başlık: {$title}\nKategori: {$categoryName}\nİçerik üret...";

    // 3. Görsel oluştur
    // 4. Template'e göre HTML oluştur
    // 5. Kaydet
}
```

### SENARYO 2: Body Dolu (689 ürün) - **ANA GÖREV!**

```php
if (!empty($currentBody)) {
    echo "📖 Mevcut body bulundu, genişletiliyor...\n";

    // 1. MEVCUT BODY'Yİ OKU VE ANALİZ ET
    $currentBody = $product->getTranslation('body', 'tr');
    $currentBodyStripped = strip_tags($currentBody); // HTML'siz metin

    echo "📏 Mevcut body uzunluğu: " . strlen($currentBodyStripped) . " karakter\n";

    // 2. TEKNİK ÖZELLİKLERİ ÇıKAR
    // Mevcut body'de şunları ara:
    // - Boyut (mm, cm, m)
    // - Ağırlık (kg, gr)
    // - Malzeme (çelik, alüminyum, plastik)
    // - Voltaj (V, volt)
    // - Kapasite (ton, kg)
    // - Seri numarası
    // - Model numarası

    $technicalSpecs = extractTechnicalSpecs($currentBodyStripped);

    // Örnek çıktı:
    // [
    //   ['label' => 'Boyut', 'value' => '150mm x 80mm'],
    //   ['label' => 'Malzeme', 'value' => 'Döküm Çelik'],
    //   ['label' => 'Seri', 'value' => '414']
    // ]

    // 3. MEVCUT İÇERİĞİ GENİŞLET
    // - Mevcut detayları koru (teknik özellikler, boyutlar)
    // - V6 Perfect template'e uyarla
    // - Eksik bölümleri ekle (Sorunlar, Çözümler, Kullanım Alanları)
    // - 10,000+ karaktere çıkar

    $expandedBody = expandAndAdaptBody($currentBody, [
        'title' => $product->getTranslation('title', 'tr'),
        'category' => $category->getTranslation('title', 'tr'),
        'technical_specs' => $technicalSpecs
    ]);

    // 4. PRIMARY_SPECS'İ GÜNCELLE
    if (!empty($technicalSpecs)) {
        $product->primary_specs = $technicalSpecs;
        echo "✅ Teknik özellikler primary_specs'e eklendi: " . count($technicalSpecs) . " özellik\n";
    }

    // 5. GÖRSEL OLUŞTUR (mevcut body'den ipuçları al)
    $imagePrompt = generateImagePrompt($product->getTranslation('title', 'tr'), $currentBodyStripped);

    // 6. KAYDET
    $product->update([
        'body' => ['tr' => $expandedBody],
        'primary_specs' => $technicalSpecs
    ]);
}
```

### 📋 TEKNİK ÖZELLİK ÇIKARMA FONKSİYONU

```php
function extractTechnicalSpecs($bodyText) {
    $specs = [];

    // 1. Boyut/Ölçü (mm, cm, m)
    if (preg_match('/(\d+)\s*(mm|cm|m)\s*x\s*(\d+)\s*(mm|cm|m)/i', $bodyText, $matches)) {
        $specs[] = ['label' => 'Boyut', 'value' => $matches[0]];
    }

    // 2. Ağırlık (kg, gr, ton)
    if (preg_match('/(\d+(?:\.\d+)?)\s*(kg|gr|ton)/i', $bodyText, $matches)) {
        $specs[] = ['label' => 'Ağırlık', 'value' => $matches[0]];
    }

    // 3. Malzeme
    $materials = ['çelik', 'alüminyum', 'plastik', 'döküm', 'paslanmaz', 'keçe', 'kauçuk'];
    foreach ($materials as $material) {
        if (stripos($bodyText, $material) !== false) {
            // Context'i bul
            if (preg_match('/(\w+\s+)?' . preg_quote($material, '/') . '(\s+\w+)?/i', $bodyText, $matches)) {
                $specs[] = ['label' => 'Malzeme', 'value' => ucfirst(trim($matches[0]))];
                break;
            }
        }
    }

    // 4. Voltaj (V, volt)
    if (preg_match('/(\d+)\s*(V|volt)/i', $bodyText, $matches)) {
        $specs[] = ['label' => 'Voltaj', 'value' => $matches[0]];
    }

    // 5. Kapasite (ton, kg)
    if (preg_match('/(\d+(?:\.\d+)?)\s*(ton|kg)\s*(kapasite|yük)/i', $bodyText, $matches)) {
        $specs[] = ['label' => 'Kapasite', 'value' => $matches[1] . ' ' . $matches[2]];
    }

    // 6. Seri/Model numarası (başlıktan)
    if (preg_match('/\b(\d{3,4})\b/', $bodyText, $matches)) {
        $specs[] = ['label' => 'Seri', 'value' => $matches[1]];
    }

    return $specs;
}
```

**Döngü:**
1. Ürünü al (ID sırasına göre)
2. Body kontrol et (boş/dolu)
3. **Body doluysa:** Oku → Analiz et → Teknik özellik çıkar → Genişlet → Template'e uyarla
4. **Body boşsa:** Başlık + kategoriden üret
5. Görsel oluştur
6. Kaydet + Log'a ekle
7. Sonraki ürün!

---

## ✅ BAŞARI KRİTERLERİ

**Her Ürün İçin:**
- [ ] Body kontrol edildi (boş/dolu)
- [ ] **Mevcut body varsa:**
  - [ ] Teknik özellikler çıkarıldı ve `primary_specs`'e eklendi
  - [ ] Body genişletildi ve V6 Perfect template'e uyarlandı
  - [ ] Mevcut detaylar korundu (boyut, malzeme, vb.)
- [ ] **Body boşsa:**
  - [ ] Başlık + kategori adından içerik üretildi
- [ ] 1 yatay (16:9) stok fotoğraf oluşturuldu
- [ ] Body 10,000+ karakter (TR)
- [ ] Body template noktasına kadar aynı
- [ ] Gökkuşağı renk YOK, tek düzen tasarım (glass efekti)
- [ ] SEO ayarları dolduruldu/güncellendi
- [ ] Database'e kaydedildi
- [ ] Cache temizlendi
- [ ] Progress log'a eklendi

**Genel:**
- [ ] 690 ürün sırayla işlenmiş (ID sırasına göre)
- [ ] Progress log güncel
- [ ] Sadece yedek parça kategorisi işlenmiş (ID: 7 ve alt kategorileri)

**Detaylı guide:** https://ixtif.com/readme/2025/11/28/shop-product-ai-ULTRA-SIMPLE/

---

## 🎯 ŞİMDİ BAŞLA!

**Adımlar:**
1. Yedek parça kategori ID'lerini bul (recursive query)
2. İlk ürünü al (ID sırasına göre)
3. Body'yi kontrol et:
   - **Dolu mu?** → Oku, analiz et, genişlet, teknik özellik çıkar
   - **Boş mu?** → Başlık + kategoriden üret
4. Görsel oluştur
5. Template'e göre HTML oluştur
6. Kaydet
7. Log'a ekle
8. Sonraki ürüne geç!

**İlk komutu çalıştır ve başla!** 🚀
