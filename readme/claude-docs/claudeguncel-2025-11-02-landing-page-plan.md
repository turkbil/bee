# TRANSPALET LANDING PAGE PLANI
**Tarih:** 2025-11-02
**Amaç:** Google Ads Quality Score 10/10 için optimize edilmiş landing page

---

## 📋 MEVCUT DURUM

### ✅ Hazır Olan:
- `/public/design/transpalet-f4-ads.html` - Static HTML (Google Ads optimize)
- Tüm SEO optimizasyonları yapılmış
- Keyword stratejisi hazır
- Design-10 renk paleti uygulanmış
- Responsive düzenlemeler yapılmış

### ❌ Eksik Olan:
- Hard-coded telefon numarası (`905322160754`) → `whatsapp_link()` kullanmalı
- Static HTML → Blade template olmalı (chat widget için gerekli)
- Chat widget entegrasyonu yok
- Route tanımlı değil

---

## 🎯 HEDEF

**Google Ads için optimal landing:**
1. ✅ Minimal header/footer (dikkat dağıtmaz)
2. ✅ Chat widget sağ altta (sohbet robotu)
3. ✅ Tenant-aware telefon numaraları (whatsapp_link helper)
4. ✅ Hızlı yüklenme (inline CSS, defer assets)
5. ✅ SEO optimizasyonu korunur

---

## 📐 MİMARİ KARAR

### SEÇENEK 1: Basit Blade View (ÖNERİLEN)
**Konum:** `resources/views/landing/transpalet-ads.blade.php`

**Avantajlar:**
- ✅ Manuel kontrol (kullanıcı istediği gibi)
- ✅ whatsapp_link() kullanabilir
- ✅ Chat widget include edebilir
- ✅ Minimal layout ile Google Ads uyumlu
- ✅ Hızlı geliştirme

**Dezavantajlar:**
- ❌ Her düzenleme kod değişikliği gerektirir
- ❌ Admin panelden yönetilemez

### SEÇENEK 2: Page Modülü
**Konum:** Page modülü ile dinamik sayfa

**Avantajlar:**
- ✅ Admin panelden düzenlenebilir
- ✅ Page pattern (master pattern)

**Dezavantajlar:**
- ❌ Full header/footer gelir (Google Ads puanı düşer!)
- ❌ Aşırı dinamik (gereksiz)

### SEÇENEK 3: Widget Modülü
**Konum:** WidgetManagement modülü

**Avantajlar:**
- ✅ Tenant-aware
- ✅ Header/footer kontrolü var

**Dezavantajlar:**
- ❌ Karmaşık kurulum
- ❌ Kullanıcı "dinamik olmasın" dedi

---

## ✅ SEÇİLEN YÖNTEM: SEÇENEK 1 (Basit Blade View)

Kullanıcı: "dinamik yapmasan da olur. kendin manuel düzenle tüm sistemi"

---

## 🛠️ İMPLEMENTASYON ADIMLARI

### 1️⃣ LAYOUT OLUŞTUR (Minimal)
**Dosya:** `resources/views/layouts/landing-minimal.blade.php`

**İçerik:**
- ❌ **Header YOK** (logo + telefon bile yok - dikkat dağıtır)
- ❌ **Footer YOK** (sadece copyright alt alta minimal)
- ✅ **Chat widget include:** `@include('ai::widgets.chat-widget')`
- ✅ **SEO meta:** @stack('meta'), @stack('schema')
- ✅ **Assets:** Tailwind CDN, FontAwesome, GTM
- ✅ **Critical CSS:** Inline (gold-gradient animasyonu)

**Mantık:**
- Google Ads landing için minimal = daha iyi
- Tek odak: Conversion (WhatsApp, Telefon, Form)
- Menu/navigation = dikkat dağıtır = bounce rate artar = Quality Score düşer

---

### 2️⃣ LANDING VIEW OLUŞTUR
**Dosya:** `resources/views/landing/transpalet/f4/1/index.blade.php`

**Yaklaşım:** Static HTML'i Blade'e çevir, minimal değişiklik

**Değiştirilecek Yerler:**
```blade
<!-- ÖNCE (Hard-coded): -->
<a href="https://wa.me/905322160754?text=Elektrikli%20Transpalet%20Kampanya">

<!-- SONRA (Dynamic): -->
<a href="{{ whatsapp_link('Elektrikli Transpalet Kampanya') }}">
```

```blade
<!-- ÖNCE (Hard-coded): -->
<a href="tel:02167553555">

<!-- SONRA (Dynamic): -->
<a href="tel:{{ setting('contact_phone_1', '02167553555') }}">
```

**Korunacak Yerler:**
- ✅ Tüm SEO meta tags (title, description, keywords)
- ✅ Schema.org JSON-LD
- ✅ Countdown script
- ✅ Tüm content (H1, USP, pricing, FAQ)
- ✅ Tailwind classes (renk paleti)

---

### 3️⃣ ROUTE TANIMLA (SEO-FRIENDLY URLS)
**Dosya:** `routes/web.php`

```php
// Landing Pages - Google Ads (SEO-friendly, "kampanya" kelimesi yok)
Route::name('landing.')->group(function() {

    // Transpalet F4 - Kampanya #1 (Kasım 2025 - Google Ads)
    Route::get('/elektrikli-transpalet', function() {
        return view('landing.transpalet.f4.1.index');
    })->name('transpalet.f4.1');

    // Gelecek kampanyalar (farklı keyword kombinasyonları)
    // Route::get('/akulu-transpalet', fn() => view('landing.transpalet.f4.2.index'))->name('transpalet.f4.2'); // Black Friday
    // Route::get('/transpalet-fiyatlari', fn() => view('landing.transpalet.f4.3.index'))->name('transpalet.f4.3'); // Yılbaşı
    // Route::get('/li-ion-transpalet', fn() => view('landing.transpalet.f4.4.index'))->name('transpalet.f4.4'); // Başka kampanya

    // Başka ürünler için SEO-friendly URLs
    // Route::get('/elektrikli-forklift', fn() => view('landing.forklift.elektrikli.1.index'))->name('forklift.elektrikli.1');
    // Route::get('/hidrolik-vinc', fn() => view('landing.crane.hidrolik.1.index'))->name('crane.hidrolik.1');
});
```

**URL:** `https://ixtif.com/elektrikli-transpalet`
**Route Name:** `landing.transpalet.f4.1`

**SEO Stratejisi:**
- ✅ Primary keyword URL: `elektrikli-transpalet`
- ✅ "Kampanya" kelimesi YOK → Daha natural
- ✅ Google Ads Quality Score için ideal
- ✅ Her kampanya farklı keyword kombinasyonu kullanır
- ✅ Rakip yapıyı görmez (klasör yapısı gizli)

**Kampanya URL Varyasyonları (Aynı Ürün, Farklı Kampanyalar):**
- `/elektrikli-transpalet` → Kampanya #1 (Ana keyword - Google Ads Kasım)
- `/akulu-transpalet` → Kampanya #2 (Alternatif keyword - Black Friday)
- `/transpalet-fiyatlari` → Kampanya #3 (Fiyat odaklı - Yılbaşı)
- `/li-ion-transpalet` → Kampanya #4 (Teknik keyword - Özel kampanya)
- `/terazili-transpalet` → Kampanya #5 (Farklı varyant)

**Avantajları:**
- ✅ Her URL farklı keyword hedefler (Google Ads A/B test)
- ✅ SEO-friendly (keyword-rich)
- ✅ "Kampanya" kelimesi yok → Evergreen görünüm
- ✅ Rakip klasör yapısını görmez
- ✅ Kullanıcı için anlamlı URL

**Klasör vs URL Mapping:**
```
Klasör:                          URL:
landing/transpalet/f4/1/    →    /elektrikli-transpalet
landing/transpalet/f4/2/    →    /akulu-transpalet
landing/transpalet/f4/3/    →    /transpalet-fiyatlari
landing/transpalet/f5/1/    →    /premium-transpalet
landing/forklift/diesel/1/  →    /dizel-forklift
```

**SEÇİLEN YÖNTEM: SEO-Friendly (Keyword-rich, no "kampanya")**

---

### 4️⃣ CHAT WIDGET ENTEGRASYONU
**Dosya:** Layout içinde zaten include edildi

```blade
<!-- Layout sonunda -->
@include('ai::widgets.chat-widget')
```

**Widget Özellikleri:**
- ✅ Sağ alt köşe (fixed bottom-6 right-6)
- ✅ Alpine.js ile reactive
- ✅ AI-powered assistant
- ✅ Rate limiting (10 mesaj/saat guest için)
- ✅ Session continuity

**Kullanıcı görecek:**
- Minimize: Sohbet butonu (mavi-mor gradient)
- Açınca: Chat penceresi açılır, AI ile konuşabilir

---

### 5️⃣ FOOTER MİNİMAL TASARIM
**Yaklaşım:** Mevcut footer'ı sadeleştir

**Çıkaracaklar:**
- ❌ 4 kolonlu link grid (Ürünler, Hızlı Bağlantılar, İletişim)
- ❌ Social media icons

**Kalacaklar:**
- ✅ Sadece copyright text (tek satır)
- ✅ Zorunlu legal linkler (Gizlilik, Kullanım Şartları)

**Örnek:**
```html
<footer class="bg-black py-6 border-t border-gray-800">
    <div class="container mx-auto px-4 text-center">
        <p class="text-sm text-gray-600">
            © 2025 İXTİF İç ve Dış Ticaret A.Ş. | Tüm hakları saklıdır.
        </p>
    </div>
</footer>
```

---

### 6️⃣ PERMİSSİON & CACHE

**Her dosya oluşturulduktan sonra:**
```bash
# Permission düzelt (3 seviyeli klasör yapısı)
sudo chown -R tuufi.com_:psaserv resources/views/landing/transpalet/
sudo chown -R tuufi.com_:psaserv resources/views/layouts/landing/
sudo find resources/views/landing/transpalet/ -type d -exec chmod 755 {} \;
sudo find resources/views/landing/transpalet/ -type f -exec chmod 644 {} \;
sudo find resources/views/layouts/landing/ -type d -exec chmod 755 {} \;
sudo find resources/views/layouts/landing/ -type f -exec chmod 644 {} \;

# Alternatif: Tek komut (tüm hiyerarşi)
# sudo chown -R tuufi.com_:psaserv resources/views/landing/ resources/views/layouts/landing/
# sudo find resources/views/landing/ -type d -exec chmod 755 {} \;
# sudo find resources/views/landing/ -type f -exec chmod 644 {} \;

# Cache temizle
php artisan view:clear
php artisan responsecache:clear

# OPcache reset
curl -s -k https://ixtif.com/opcache-reset.php

# Test
curl -s -k -I "https://ixtif.com/elektrikli-transpalet" | grep "HTTP"
```

---

### 7️⃣ TEST

**Kontrol Listesi:**
- ✅ URL açılıyor mu? (`/elektrikli-transpalet`)
- ✅ WhatsApp link tenant numarasını kullanıyor mu?
- ✅ Telefon numarası doğru mu?
- ✅ Chat widget sağ altta görünüyor mu?
- ✅ Chat widget çalışıyor mu? (mesaj gönder, cevap gelsin)
- ✅ Responsive çalışıyor mu? (xs/sm/md mobile, lg+ desktop)
- ✅ Countdown çalışıyor mu? (localStorage ile persist)
- ✅ Schema.org markup doğru mu? (Google Rich Results Test)
- ✅ GTM tag'ler tetikleniyor mu?

---

## 📦 DOSYA YAPISI (KLASÖRLEME - NUMARA BAZLI KAMPANYALAR)

```
/var/www/vhosts/tuufi.com/httpdocs/
│
├── resources/views/
│   ├── layouts/
│   │   └── landing/
│   │       └── minimal.blade.php         [YENİ] Minimal layout (landing'ler için ortak)
│   │
│   └── landing/
│       └── transpalet/                   [YENİ KLASÖR] Transpalet ürün grubu
│           └── f4/                       [YENİ KLASÖR] F4 ürün modeli
│               ├── 1/                    [YENİ KLASÖR] Kampanya #1 (Google Ads - Kasım 2025)
│               │   └── index.blade.php   [YENİ] Landing page
│               ├── 2/                    [GELECEK] Kampanya #2 (Black Friday 2025)
│               │   └── index.blade.php
│               └── 3/                    [GELECEK] Kampanya #3 (Yılbaşı 2025)
│                   └── index.blade.php
│
├── routes/
│   └── web.php                           [GÜNCELLE] Route ekle
│
└── Modules/AI/resources/views/widgets/
    └── chat-widget.blade.php             [MEVCUT] Chat widget
```

**Klasörleme Hiyerarşisi:**
```
landing/
├── transpalet/              [Kategori]
│   ├── f4/                 [Ürün - iXtif F4 Elektrikli Transpalet]
│   │   ├── 1/             [Kampanya #1 - Google Ads Kasım 2025]
│   │   │   └── index.blade.php
│   │   ├── 2/             [Kampanya #2 - Black Friday 2025]
│   │   │   └── index.blade.php
│   │   └── 3/             [Kampanya #3 - Yılbaşı 2025]
│   │       └── index.blade.php
│   │
│   ├── f5/                 [Başka Ürün]
│   │   └── 1/
│   │       └── index.blade.php
│   │
│   └── terazili/           [Başka Ürün]
│       └── 1/
│           └── index.blade.php
│
├── forklift/               [Başka Tenant - Kategori]
│   └── diesel-3ton/       [Ürün]
│       └── 1/             [Kampanya #1]
│           └── index.blade.php
│
└── crane/                  [Başka Tenant - Kategori]
    └── hidrolik-5ton/     [Ürün]
        └── 1/             [Kampanya #1]
            └── index.blade.php
```

**Faydası:**
- ✅ Her kategori için ayrı klasör (transpalet, forklift, crane)
- ✅ Her ürün için ayrı alt klasör (f4, f5, terazili)
- ✅ Her ürün için numaralı kampanyalar (1, 2, 3, 4...)
- ✅ Zamana bağlı kampanya yönetimi (index.blade.php standart isim)
- ✅ 4 seviyeli hiyerarşi: Kategori → Ürün → Kampanya Numarası → Dosya
- ✅ Maksimum ölçeklenebilirlik ve düzen

---

## 🎨 TASARIM KURALLARI (Korunacak)

**Renk Paleti (design-10):**
- ✅ Gold gradient (#d4af37, #f4e5a1)
- ✅ Yellow-500, Yellow-600 (badges, icons)
- ✅ Gray-400/600/700/800/950 (backgrounds, text)
- ❌ Green/Blue/Red kullanma!

**Responsive:**
- Mobile: xs (0-639px), sm (640-767px), md (768-1023px)
- Desktop: lg (1024px+)
- Breakpoint: `lg:` kullan (md: değil!)

**SEO:**
- Primary keyword: "Elektrikli Transpalet"
- Secondary: "Akülü Transpalet", "Terazili Transpalet", "Denge Tekerli Transpalet"
- Economic: "Ucuz", "Uygun Fiyatlı", "Ekonomik" (sadece FAQ ve benefits'te)

---

## ⚠️ DİKKAT EDİLECEKLER

### ❌ YAPILMAYACAKLAR:
1. Full header/footer ekleme (Google Ads puanı düşer)
2. Hard-coded telefon numarası bırakma
3. Renk paletini bozma (sadece gold/yellow/gray)
4. Responsive breakpoint'leri değiştirme (lg: kullan)
5. SEO optimizasyonları bozma

### ✅ YAPILACAKLAR:
1. whatsapp_link() helper kullan
2. setting() ile telefon numarası al
3. Chat widget include et (layout'ta)
4. Minimal footer (sadece copyright)
5. Permission her dosyada düzelt
6. Cache temizle + OPcache reset

---

## 🚀 DEPLOYMENT SIRASI

**Adım adım:**
1. ✅ Minimal layout oluştur → Permission düzelt
2. ✅ Landing view oluştur → Permission düzelt
3. ✅ Route ekle
4. ✅ Cache temizle (view + response + OPcache)
5. ✅ Test URL
6. ✅ WhatsApp link test et
7. ✅ Chat widget test et
8. ✅ Responsive test et (mobile + desktop)
9. ✅ Schema.org test et (Google Rich Results)

---

## 📊 BAŞARI KRİTERLERİ

**Landing page başarılı sayılır eğer:**
1. ✅ URL açılıyor: `https://ixtif.com/elektrikli-transpalet`
2. ✅ WhatsApp tenant numarasını kullanıyor (setting'ten alıyor)
3. ✅ Chat widget çalışıyor (sağ altta, mesajlaşma aktif)
4. ✅ Minimal tasarım (dikkat dağıtmayan)
5. ✅ Responsive (xs/sm/md mobile, lg+ desktop)
6. ✅ SEO korunmuş (schema, meta, keywords)
7. ✅ Renk paleti doğru (gold/yellow/gray)
8. ✅ Countdown çalışıyor (localStorage persist)

**Google Ads için:**
- ✅ Expected CTR: Yüksek (compelling headline + price)
- ✅ Ad Relevance: Mükemmel (exact keyword match)
- ✅ Landing Page Experience: Mükemmel (fast load, mobile-friendly, clear CTA)

---

## 🤔 KULLANICI ONAY GEREKTİREN KARARLAR

**Şu konuları kullanıcıya soracağım:**
1. ❓ Footer'ı minimal yapsam olur mu? (Sadece copyright, link grid yok)
2. ❓ Header hiç olmasın mı? (Logo bile yok, direkt kampanya başlasın)
3. ✅ SEO-friendly URL `/elektrikli-transpalet` ONAYLANDI (Kampanya kelimesi YOK)
4. ❓ Schema.org'daki telefon numarasını da setting'ten alsam olur mu?

---

## 📝 SONUÇ

**Önerilen yöntem:**
- Basit Blade view (`resources/views/landing/transpalet/f4/1/index.blade.php`)
- Minimal layout (`resources/views/layouts/landing/minimal.blade.php`)
- 4 seviyeli klasörlü yapı: Kategori → Ürün → Kampanya # → Dosya
- Numara bazlı kampanya sistemi (1, 2, 3... Google Ads için)
- Maksimum ölçeklenebilirlik (tenant-aware, product-aware, campaign-aware)
- whatsapp_link() helper kullan
- Chat widget include et
- Manuel düzenleme (admin panel yok)

**Kullanıcı istediyse bu plan ile devam:**
- Mevcut HTML'i Blade'e çevir
- Sadece telefon numaralarını dynamic yap
- Chat widget ekle
- Minimal footer tasarla
- Test et

**Plan onaylanırsa implementasyon başlasın!**
