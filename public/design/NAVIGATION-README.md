# Design System - Otomatik Navigasyon Sistemi

## 🎯 Özellikler

✅ **343 sayfa**ya otomatik navigasyon eklendi
✅ **İleri/Geri butonları** - Tüm sayfalarda
✅ **Klavye kısayolları** - Arrow keys ile hızlı gezinme
✅ **Sticky navigation bars** - Üst ve alt navigasyon
✅ **Kategori linki** - Her sayfadan kategori ana sayfasına dönüş
✅ **Sayfa sayacı** - Kaçıncı sayfada olduğunuzu gösterir

---

## 📋 Kullanım

### Otomatik Navigasyon

Her `design-*-[0-9].html` dosyası otomatik olarak navigasyona sahip:

**Üst Navigasyon:**
- 🏠 Ana Sayfa
- 📁 Kategori
- 🔢 Sayfa Numarası (1 / 10)
- ← Önceki | Sonraki →

**Alt Navigasyon (Floating):**
- ← Önceki (mavi buton)
- 📁 Kategori (mor buton)
- Sonraki → (mavi buton)

### Klavye Kısayolları

| Tuş | İşlev |
|-----|-------|
| `←` (Sol ok) | Önceki sayfaya git |
| `→` (Sağ ok) | Sonraki sayfaya git |
| `Home` | Ana sayfaya dön (index.html) |
| `Esc` | Kategori sayfasına dön (design-about.html) |

---

## 🛠️ Teknik Detaylar

### Dosyalar

**navigation-auto.js** (8.2 KB)
- Otomatik navigasyon motoru
- Her sayfada çalışır
- URL pattern'i analiz eder
- Dinamik navigasyon oluşturur

**add-navigation.sh** (1.7 KB)
- Toplu ekleme scripti
- Tüm design-*-*.html dosyalarına script'i ekler
- Backup oluşturur (.bak)

### Desteklenen Kategoriler

```
about (10), accordion (6), blog (10), breadcrumb (8),
categories (10), category (10), chatbot-inline (6),
chatbot-popup (5), contact (18), cookie-consent (10),
cta (10), faq (10), features (10), footer (10),
gallery (10), glass-compact (3), header (10), hero (10),
lazy-loading-demo (1), menu (10), menu-FULL (1),
newsletter (10), page-hero (8), partners (10),
pricing (10), product (10), product-card-premium (12),
product-card-luxe (12), products (10), promotion (6),
promotions (10), search (6), services (10),
shop-index (10), sidebar (6), stats (10), subheader (8),
subheader-shop (8), subheader-shop-index (1),
tabs (6), testimonials (10)
```

### Nasıl Çalışır?

1. **URL Algılama:**
   ```javascript
   const match = currentFile.match(/design-(.+)-(\d+)\.html$/);
   // Örnek: design-about-3.html → category: "about", number: 3
   ```

2. **Navigasyon Oluşturma:**
   ```javascript
   const prevUrl = `design-${category}-${num - 1}.html`;
   const nextUrl = `design-${category}-${num + 1}.html`;
   ```

3. **Dinamik Enjeksiyon:**
   - Üst navbar oluşturulur
   - Alt floating buttons eklenir
   - Klavye event listener'ları bağlanır

---

## 🔧 Yeni Sayfa Eklerken

Yeni bir `design-*-*.html` dosyası eklediğinizde:

**Otomatik Yöntem:**
```bash
cd /var/www/vhosts/tuufi.com/httpdocs/public/design
sudo ./add-navigation.sh
```

**Manuel Yöntem:**
Dosyanın sonuna (</body> tag'inden önce) ekleyin:
```html
<script src="navigation-auto.js"></script>
```

---

## 📊 İstatistikler

- **Toplam Sayfa:** 343
- **Kategori Sayısı:** 32
- **Ortalama Sayfa/Kategori:** ~10
- **En Fazla Sayfa:** contact (18)
- **En Az Sayfa:** lazy-loading-demo, menu-FULL (1)

---

## 🎨 Tasarım

**Navbar Renkleri:**
- Arka plan: `bg-slate-900/90` (yarı transparan)
- Hover: `bg-white/20`
- Aktif kategori: `bg-blue-600/20`
- Devre dışı: `opacity-50`

**Floating Buttons:**
- Önceki/Sonraki: Mavi (`bg-blue-600`)
- Kategori: Mor (`bg-purple-600`)
- Shadow: `shadow-lg hover:shadow-2xl`

---

## 🚀 Güncellemeler

**Son Güncelleme:** 12 Kasım 2025

**Değişiklikler:**
- ✅ 343 sayfaya navigasyon eklendi
- ✅ Klavye kısayolları eklendi
- ✅ Floating navigation buttons eklendi
- ✅ Kategori quick link eklendi
- ✅ Sayfa sayacı eklendi

---

## 🐛 Sorun Giderme

**Navigasyon görünmüyor:**
- Browser console'u kontrol edin
- `navigation-auto.js` yüklenmiş mi?
- URL pattern doğru mu? (`design-category-number.html`)

**Yanlış sayfa sayısı:**
- `navigation-auto.js` içindeki `categories` objesini güncelleyin
- İlgili kategorinin max sayfa sayısını düzeltin

**Klavye kısayolları çalışmıyor:**
- Sayfa tamamen yüklenmiş olmalı
- Input alanlarında değilken deneyin

---

## 📝 Notlar

- Script sadece `design-*-[0-9].html` pattern'ine uyan dosyalarda çalışır
- Kategori ana sayfaları (`design-about.html`) otomatik navigasyon almaz
- Özel sayfalar (F4-*, PDF-*, V4-*) otomatik navigasyon almaz

---

## 🎯 Sonraki Adımlar

- [ ] Kategoriler arası geçiş (design-about-1.html → design-blog-1.html)
- [ ] Progress bar (% tamamlanma)
- [ ] Favorilere ekleme sistemi
- [ ] Son görüntülenenler

---

**Hazırlayan:** Claude AI
**Tarih:** 12 Kasım 2025
**Versiyon:** 1.0.0
