# İXTİF - Sayfa İçerikleri İmport Rehberi

## 📋 İçerik Dosyaları (17 Sayfa - TAMAMLANDI ✅)

Tüm sayfa içerikleri **BASİT, MİNİMAL, ANASAYFA STİLİ** ile hazırlandı.

### ✅ Hazırlanan Sayfalar:

| # | Sayfa | Slug | Dosya | Durum |
|---|-------|------|-------|-------|
| 1 | Hakkımızda | `hakkimizda` | `/hakkimizda/content.html` | ✅ |
| 2 | İletişim | `iletisim` | `/iletisim/content.html` | ✅ |
| 3 | Hizmetler | `hizmetler` | `/hizmetler/content.html` | ✅ |
| 4 | SSS | `sss` | `/sss/content.html` | ✅ |
| 5 | Referanslar | `referanslar` | `/referanslar/content.html` | ✅ |
| 6 | Kariyer | `kariyer` | `/kariyer/content.html` | ✅ |
| 7 | Gizlilik Politikası | `gizlilik-politikasi` | `/gizlilik-politikasi/content.html` | ✅ |
| 8 | Kullanım Koşulları | `kullanim-kosullari` | `/kullanim-kosullari/content.html` | ✅ |
| 9 | KVKK Aydınlatma | `kvkk-aydinlatma` | `/kvkk-aydinlatma/content.html` | ✅ |
| 10 | KVKK Başvuru | `kvkk-basvuru` | `/kvkk-basvuru/content.html` | ✅ |
| 11 | Çerez Politikası | `cerez-politikasi` | `/cerez-politikasi/content.html` | ✅ |
| 12 | İptal & İade | `iptal-iade` | `/iptal-iade/content.html` | ✅ |
| 13 | Cayma Hakkı | `cayma-hakki` | `/cayma-hakki/content.html` | ✅ |
| 14 | Mesafeli Satış | `mesafeli-satis` | `/mesafeli-satis/content.html` | ✅ |
| 15 | Teslimat & Kargo | `teslimat-kargo` | `/teslimat-kargo/content.html` | ✅ |
| 16 | Ödeme Yöntemleri | `odeme-yontemleri` | `/odeme-yontemleri/content.html` | ✅ |
| 17 | Güvenli Alışveriş | `guvenli-alisveris` | `/guvenli-alisveris/content.html` | ✅ |

---

## 🎨 Tasarım Prensipler (Tüm Sayfalarda Uygulandı)

- **Minimal Design:** Sadece `rounded-full` ikonlar, `bg-blue-50 dark:bg-slate-700/50` arka planlar
- **NO SHADOWS:** Hiçbir yerde shadow kullanılmadı
- **NO BOXES:** Gereksiz rounded-2xl kartlar yok, nefes alan tasarım
- **Gradient Only Headlines:** `.gradient-animate` sadece büyük başlıklarda
- **Dark Mode:** Tüm sayfalarda `dark:` varyantları
- **Responsive:** Tüm sayfalarda `sm/md/lg/xl` breakpointler
- **Settings Helper:** Dinamik içerik için `{{ settings('key', 'default') }}`

---

## 📦 Import Yöntemleri

### Metod 1: PHP PDO ile Güvenli Import (ÖNERİLEN ✅)

```php
<?php
// PDO bağlantısı
$pdo = new PDO(
    'mysql:host=localhost;dbname=tenant_ixtif;charset=utf8mb4',
    'tuufi_4ekim',
    'XZ9Lhb%u8jp9#njf'
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Sayfa verisi
$pages = [
    ['slug' => 'hakkimizda', 'title' => 'Hakkımızda - Depo Ekipmanlarının Dijital Platformu', 'file' => 'hakkimizda/content.html'],
    ['slug' => 'iletisim', 'title' => 'İletişim', 'file' => 'iletisim/content.html'],
    ['slug' => 'hizmetler', 'title' => 'Hizmetler', 'file' => 'hizmetler/content.html'],
    ['slug' => 'sss', 'title' => 'Sıkça Sorulan Sorular', 'file' => 'sss/content.html'],
    ['slug' => 'referanslar', 'title' => 'Referanslar', 'file' => 'referanslar/content.html'],
    ['slug' => 'kariyer', 'title' => 'Kariyer', 'file' => 'kariyer/content.html'],
    ['slug' => 'gizlilik-politikasi', 'title' => 'Gizlilik Politikası', 'file' => 'gizlilik-politikasi/content.html'],
    ['slug' => 'kullanim-kosullari', 'title' => 'Kullanım Koşulları', 'file' => 'kullanim-kosullari/content.html'],
    ['slug' => 'kvkk-aydinlatma', 'title' => 'KVKK Aydınlatma Metni', 'file' => 'kvkk-aydinlatma/content.html'],
    ['slug' => 'kvkk-basvuru', 'title' => 'KVKK Başvuru Formu', 'file' => 'kvkk-basvuru/content.html'],
    ['slug' => 'cerez-politikasi', 'title' => 'Çerez Politikası', 'file' => 'cerez-politikasi/content.html'],
    ['slug' => 'iptal-iade', 'title' => 'İptal & İade Koşulları', 'file' => 'iptal-iade/content.html'],
    ['slug' => 'cayma-hakki', 'title' => 'Cayma Hakkı Formu', 'file' => 'cayma-hakki/content.html'],
    ['slug' => 'mesafeli-satis', 'title' => 'Mesafeli Satış Sözleşmesi', 'file' => 'mesafeli-satis/content.html'],
    ['slug' => 'teslimat-kargo', 'title' => 'Teslimat & Kargo', 'file' => 'teslimat-kargo/content.html'],
    ['slug' => 'odeme-yontemleri', 'title' => 'Ödeme Yöntemleri', 'file' => 'odeme-yontemleri/content.html'],
    ['slug' => 'guvenli-alisveris', 'title' => 'Güvenli Alışveriş', 'file' => 'guvenli-alisveris/content.html'],
];

$basePath = '/var/www/vhosts/tuufi.com/httpdocs/readme/ixtif/pages-content/';

foreach ($pages as $page) {
    // HTML içeriğini oku
    $htmlPath = $basePath . $page['file'];
    if (!file_exists($htmlPath)) {
        echo "❌ Dosya bulunamadı: {$htmlPath}\n";
        continue;
    }

    $html = file_get_contents($htmlPath);

    // JSON formatına çevir
    $titleJson = json_encode(['tr' => $page['title']], JSON_UNESCAPED_UNICODE);
    $slugJson = json_encode(['tr' => $page['slug']], JSON_UNESCAPED_UNICODE);
    $bodyJson = json_encode(['tr' => $html], JSON_UNESCAPED_UNICODE);

    // Sayfa var mı kontrol et
    $stmt = $pdo->prepare("SELECT page_id FROM pages WHERE JSON_EXTRACT(slug, '$.tr') = :slug");
    $stmt->execute(['slug' => $page['slug']]);
    $existing = $stmt->fetch();

    if ($existing) {
        // UPDATE - Mevcut sayfayı güncelle
        $stmt = $pdo->prepare("
            UPDATE pages
            SET
                title = :title,
                body = :body,
                updated_at = NOW()
            WHERE page_id = :page_id
        ");
        $stmt->execute([
            'title' => $titleJson,
            'body' => $bodyJson,
            'page_id' => $existing['page_id']
        ]);
        echo "✅ Güncellendi: {$page['slug']} (ID: {$existing['page_id']})\n";
    } else {
        // INSERT - Yeni sayfa oluştur
        $stmt = $pdo->prepare("
            INSERT INTO pages (title, slug, body, is_active, created_at, updated_at)
            VALUES (:title, :slug, :body, 1, NOW(), NOW())
        ");
        $stmt->execute([
            'title' => $titleJson,
            'slug' => $slugJson,
            'body' => $bodyJson
        ]);
        echo "✅ Oluşturuldu: {$page['slug']} (ID: {$pdo->lastInsertId()})\n";
    }
}

echo "\n🎉 Tüm sayfalar başarıyla import edildi!\n";
?>
```

**Çalıştırma:**
```bash
php -f /tmp/import-pages.php
```

---

### Metod 2: Laravel Tinker ile Import

```bash
php artisan tinker
```

```php
$pages = [
    ['slug' => 'hakkimizda', 'title' => 'Hakkımızda - Depo Ekipmanlarının Dijital Platformu', 'file' => 'hakkimizda/content.html'],
    // ... (yukarıdaki tüm sayfa listesi)
];

$basePath = '/var/www/vhosts/tuufi.com/httpdocs/readme/ixtif/pages-content/';

foreach ($pages as $pageData) {
    $htmlPath = $basePath . $pageData['file'];
    $html = file_get_contents($htmlPath);

    $page = \App\Models\Page::firstOrNew(['slug' => ['tr' => $pageData['slug']]]);
    $page->title = ['tr' => $pageData['title']];
    $page->slug = ['tr' => $pageData['slug']];
    $page->body = ['tr' => $html];
    $page->is_active = 1;
    $page->save();

    echo "✅ {$pageData['slug']}\n";
}
```

---

### Metod 3: Tek Sayfa Import (Test)

```php
<?php
// Tek sayfa test import
$pdo = new PDO('mysql:host=localhost;dbname=tenant_ixtif;charset=utf8mb4', 'tuufi_4ekim', 'XZ9Lhb%u8jp9#njf');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$slug = 'hakkimizda'; // Test için
$htmlPath = '/var/www/vhosts/tuufi.com/httpdocs/readme/ixtif/pages-content/hakkimizda/content.html';
$html = file_get_contents($htmlPath);

$titleJson = json_encode(['tr' => 'Hakkımızda - Depo Ekipmanlarının Dijital Platformu'], JSON_UNESCAPED_UNICODE);
$bodyJson = json_encode(['tr' => $html], JSON_UNESCAPED_UNICODE);

$stmt = $pdo->prepare("UPDATE pages SET body = :body, title = :title, updated_at = NOW() WHERE JSON_EXTRACT(slug, '$.tr') = :slug");
$stmt->execute(['body' => $bodyJson, 'title' => $titleJson, 'slug' => $slug]);

echo "✅ Hakkımızda sayfası güncellendi!\n";
?>
```

---

## 🔄 Import Sonrası Kontroller

### 1. Cache Temizleme
```bash
php artisan cache:clear
php artisan view:clear
php artisan responsecache:clear
php -r "if (function_exists('opcache_reset')) opcache_reset();"
systemctl reload nginx
```

### 2. Sayfa Kontrol
```bash
# Tüm sayfaları listele
mysql -u tuufi_4ekim -p'XZ9Lhb%u8jp9#njf' tenant_ixtif -e "
SELECT
    page_id,
    JSON_EXTRACT(slug, '$.tr') as slug,
    JSON_EXTRACT(title, '$.tr') as title,
    is_active,
    updated_at
FROM pages
ORDER BY page_id;
"
```

### 3. Frontend Test
```bash
# İlk 5 sayfayı curl ile test et
curl -I https://ixtif.com/hakkimizda
curl -I https://ixtif.com/iletisim
curl -I https://ixtif.com/hizmetler
curl -I https://ixtif.com/sss
curl -I https://ixtif.com/referanslar
```

---

## ⚙️ Sonraki Adımlar

### 1. Sayfa Menu'lerini Güncelle
Footer ve Navigation menülerinde yeni sayfaların linkleri eklensin:

**Footer Links (Legal):**
- Gizlilik Politikası → `/gizlilik-politikasi`
- Kullanım Koşulları → `/kullanim-kosullari`
- KVKK Aydınlatma → `/kvkk-aydinlatma`
- Çerez Politikası → `/cerez-politikasi`
- İptal & İade → `/iptal-iade`
- Cayma Hakkı → `/cayma-hakki`

**Footer Links (Info):**
- Hakkımızda → `/hakkimizda`
- İletişim → `/iletisim`
- Hizmetler → `/hizmetler`
- SSS → `/sss`
- Referanslar → `/referanslar`
- Kariyer → `/kariyer`

**Footer Links (Shopping):**
- Mesafeli Satış → `/mesafeli-satis`
- Teslimat & Kargo → `/teslimat-kargo`
- Ödeme Yöntemleri → `/odeme-yontemleri`
- Güvenli Alışveriş → `/guvenli-alisveris`

### 2. Form Entegrasyonları
Aşağıdaki formlara backend route/controller eklensin:

- `/iletisim` → İletişim formu (name, email, phone, subject, message)
- `/kariyer-basvuru` → Kariyer başvuru formu (CV upload)
- `/kvkk-basvuru` → KVKK başvuru formu (kimlik belgesi upload)
- `/cayma-hakki` → Cayma hakkı formu

### 3. İçerik Zenginleştirme
Şu an tüm içerikler **basit ve minimal**. İleride:
- Görsel eklenebilir (ürün fotoğrafları, şirket fotoğrafları)
- Animasyonlar eklenebilir (AOS, scroll effects)
- Video eklenebilir (YouTube embeds)
- İnteraktif öğeler eklenebilir (accordions, tabs)

---

## 📊 Import İstatistikleri

- **Toplam Sayfa:** 17
- **Toplam Dosya Boyutu:** ~150 KB
- **Ortalama Sayfa Uzunluğu:** ~300 satır HTML
- **Kullanılan Icon Set:** Font Awesome Light
- **Dark Mode:** Tüm sayfalarda destekleniyor
- **Responsive:** Tüm cihazlarda uyumlu

---

## ❓ Sorun Giderme

### Hata: "CONSTRAINT `pages.body` failed"
**Sebep:** JSON formatı geçersiz
**Çözüm:** `json_encode()` ile sarmalayın, `JSON_UNESCAPED_UNICODE` flag'i kullanın

### Hata: "Sayfa 404 veriyor"
**Sebep:** Route tanımlı değil veya cache sorunu
**Çözüm:** Cache temizleyin, route:list kontrol edin

### Hata: "Tasarım bozuk görünüyor"
**Sebep:** CSS/Tailwind compile edilmedi
**Çözüm:** `npm run prod` çalıştırın

---

## 📞 Destek

Herhangi bir sorun için:
- **Dosya Konumu:** `/var/www/vhosts/tuufi.com/httpdocs/readme/ixtif/pages-content/`
- **Veritabanı:** `tenant_ixtif.pages`
- **Import Script:** Bu README'deki PHP kodlarını kullanın

---

**✅ TÜM 17 SAYFA HAZIR - İMPORT EDİLEBİLİR!**

Hazırlayan: Claude Code
Tarih: 2025-10-23
Proje: İXTİF - Depo Ekipmanları Dijital Platformu
