# 🏢 MULTI-TENANT SİSTEM MİMARİSİ

## 🔵🔵🔵 SUBSCRIPTION SİSTEMİ - DENEME AŞAMASI 🔵🔵🔵

### 📋 ÖNEMLİ NOT:

**Subscription plan tablolarında veri olmasına gerek yok!**

- Şu anda **denemeler yapıyoruz**
- **Hedef:** Tüm tabloları aynı tarza/yapıya getirmek
- **Strateji:** Manage sayfasında kullanılan field'leri baz alarak 3 DB'yi birleştirmek
- **Plan Dosyası:** https://ixtif.com/readme/2025/12/05/subscription-database-migration-plan/

**Migration Yapılacak:**
- Central (tuufi_4ekim) → +tax_rate, +price_display_mode, +is_trial | -legacy fields
- Tenant İxtif → +is_trial, +sort_order | -unused fields
- Tenant Muzibu → +tax_rate, +price_display_mode, +is_trial, +sort_order | -legacy price fields

**Kullanıcıya migration öncesi danış!**

---

## 🔴🔴🔴 STORAGE & MEDYA KORUMA - MUTLAK YASAK! 🔴🔴🔴

### ⛔ ASLA, KESİNLİKLE, HİÇBİR ZAMAN YAPMA:

**🚨 STORAGE SİLME YASAK:**
1. ❌ `storage/` klasörünü SİLME!
2. ❌ `storage/app/public/` klasörünü SİLME!
3. ❌ `storage/tenantX/app/public/` klasörünü SİLME!
4. ❌ Media dosyalarını SİLME!
5. ❌ Görsel klasörlerini SİLME!
6. ❌ `public/storage/` içeriğini SİLME!

**🚨 TEHLİKELİ KOMUTLAR YASAK:**
```bash
❌ php artisan app:clear-all           # DEVRE DIŞI BIRAKILDI!
❌ php artisan media-library:clear     # MEDYA SİLER!
❌ php artisan db:wipe                 # DB SİLER!
❌ php artisan migrate:fresh           # TABLO SİLER!
❌ php artisan tenants:migrate-fresh   # TENANT SİLER!
❌ rm -rf storage/                     # HER ŞEYİ SİLER!
❌ rm -rf storage/app/public/          # MEDYALARI SİLER!
```

**✅ GÜVENLİ CACHE TEMİZLEME:**
```bash
✅ php artisan cache:clear
✅ php artisan config:clear
✅ php artisan route:clear
✅ php artisan view:clear
✅ php artisan responsecache:clear
✅ php artisan optimize:clear
```

**⚠️ NEDEN YASAK?**
- Bu komutlar **268 medya dosyası sildi!** (2025-11-30)
- Backup yoksa **KALICI KAYIP!**
- Site fotoğrafları 403 Forbidden veriyor
- Müşteri içeriği geri gelmez!

**🛡️ KORUMA KURALLARI:**
1. Media silme işlemi → **KULLANICI İZNİ ZORUNLU!**
2. Storage temizleme → **KULLANICI İZNİ ZORUNLU!**
3. Migration fresh → **KULLANICI İZNİ ZORUNLU!**
4. Şüpheli komut → **KULLANICI İZNİ ZORUNLU!**

---

## 🚨🚨🚨 KRİTİK PERFORMANS NOTLARI - ÖNCE BU BÖLÜMÜ OKU! 🚨🚨🚨

### ⚡ PERFORMANS OPTİMİZASYONLARI (2025-11-30)

**❌ ASLA YAPMA:**
1. **Horizon Auto-Restart Cron İle Yapma!**
   - `app/Console/Kernel.php` içinde `horizon-auto-restart` DEVRE DIŞI!
   - Sebep: Her 5 dakikada pkill → Orphan process → CPU %100
   - Çözüm: Supervisor kullan veya systemd service

2. **Background Process'leri `exec(...&)` İle Başlatma!**
   - `&` ile başlatılan process'ler orphan olur
   - Supervisor veya systemd kullan!

3. **maxProcesses'leri Agresif Ayarlama!**
   - ❌ Yanlış: ai-supervisor maxProcesses=8
   - ✅ Doğru: ai-supervisor maxProcesses=2
   - Her process spawn eder, CPU patlama yapar!

**✅ YAPILAN OPTİMİZASYONLAR:**
- ✅ Currency N+1 fixed (1,440 query → 0 query)
- ✅ Settings global cache (700+ query → 2 query)
- ✅ Database indexes: `shop_products_optimized_idx`, `blogs_active_published_deleted_idx`
- ✅ Horizon maxProcesses: 8→2, 6→2, 2→1
- ✅ Horizon auto-restart disabled (orphan process sorunu çözüldü)

**📊 SONUÇLAR:**
- CPU Load: 18.44 → 7.09 (%61 azalma)
- Horizon Process: 112 → 38 (%66 azalma)
- Site Hızı: 45s → 2-3s (15-22x hızlanma)

**📄 Detaylı Rapor:**
https://ixtif.com/readme/2025/11/30/horizon-cpu-sorunu-analiz/

---

## 🚨 ÖNCE BU BÖLÜMÜ OKU - SİSTEM TENANT AWARE!

**⚠️ KRİTİK: Bu sistem MULTI-TENANT mimarisindedir!**

### 📋 Temel Bilgiler

**Her tenant tamamen bağımsız çalışır:**
- ✅ **Her tenant'ın kendi database'i var** (tenant_ixtif, tenant_muzibu_1528d0 vb.)
- ✅ **Central database** (tuufi_4ekim) ortak tablolar için kullanılır (users, roles, permissions)
- ✅ **Tenant 1 (tuufi.com)** = Central tenant (Ana sistem, diğer tenant'ları yönetir)
- ✅ **Bazı tablolar central'da, bazıları tenant database'lerinde**

### 🗄️ Database Dağılımı

**Central Database (tuufi_4ekim) - Tüm Tenant'lar İçin Ortak:**
- `tenants`, `domains` - Tenant yönetimi
- `users`, `roles`, `permissions` - Kullanıcı & yetki sistemi
- `ai_credits`, `subscriptions`, `invoices` - Faturalandırma
- `migrations` - Central migration kayıtları

**Tenant Database (tenant_X) - Her Tenant'a Özel:**
- `pages`, `blogs`, `blog_categories` - İçerik yönetimi
- `products`, `categories`, `brands` - Ürün sistemi
- `media` - Medya dosyaları (tenant'a özel)
- `seo_meta`, `settings` - Tenant ayarları
- **Muzibu için:** `songs`, `albums`, `artists`, `playlists`, `genres`, `sectors`
- **İxtif için:** `products` (endüstriyel ekipman - forklift, transpalet)

### 🎯 Aktif Tenant'lar

**📊 Detaylı liste için:** `TENANT_LIST.md` dosyasını oku!

| ID | Domain | Database | Sektör | Premium |
|----|--------|----------|--------|---------|
| 1 | tuufi.com | tuufi_4ekim | Central | ✅ |
| 2 | ixtif.com | tenant_ixtif | Endüstriyel Ekipman | ✅ |
| 1001 | muzibu.com.tr | tenant_muzibu_1528d0 | Müzik Platformu | ❌ |

### 🚨 KRİTİK KURALLAR - ASLA UNUTMA!

**❌ YAPMA:**
1. ❌ Tenant'a özel içeriği global kodlara ekleme!
   - **Forklift/Transpalet** → SADECE Tenant 2 (ixtif.com)!
   - **Müzik/Song/Album/Artist** → SADECE Tenant 1001 (muzibu.com)!

2. ❌ Central database'e tenant verisi yazma!
   - Blog, Product, Page → Tenant database'e yazılmalı!

3. ❌ Tenant database'e user bilgisi yazma!
   - User, Role, Permission → Central database'de!

**✅ YAP:**
1. ✅ Kod yazmadan önce SOR:
   - Bu tenant'a özel mi, yoksa tüm tenant'lar için mi?
   - Hangi database'e yazılacak? (Central mi, Tenant mi?)
   - Tenant ID kontrolü gerekli mi?

2. ✅ Tenant kontrolü yap:
   ```php
   if (tenant()->id === 2) {
       // Sadece İxtif için
   }

   if (tenant()->id === 1001) {
       // Sadece Muzibu için
   }
   ```

3. ✅ Database bağlantısını doğru kullan:
   ```php
   // Tenant verisi (otomatik tenant DB)
   Page::all();
   Blog::all();

   // Central verisi (zorunlu $connection = 'central')
   User::all();
   Role::all();
   ```

4. ✅ Migration oluştururken İKİ YERDE oluştur:
   ```bash
   # Central
   database/migrations/YYYY_MM_DD_create_table.php

   # Tenant
   database/migrations/tenant/YYYY_MM_DD_create_table.php
   ```

### 📚 Detaylı Döküman

**Tüm tenant detayları için:** `TENANT_LIST.md` dosyasını oku!

---

## 🔴 EN KRİTİK KURALLAR - MUTLAKA OKU!

> **⚠️ WRITE/EDIT TOOL KULLANDIKTAN SONRA MUTLAKA:**
> ```bash
> sudo chown tuufi.com_:psaserv /path/to/file
> sudo chmod 644 /path/to/file
> ```
> **UNUTMA! Her dosya işleminden sonra permission düzelt!**

---

### 🚨 1. TENANT AWARE SİSTEM

**⚠️⚠️⚠️ BU SİSTEM MULTI-TENANT! HER TENANT FARKLI SEKTÖR! ⚠️⚠️⚠️**

**🔥 KRİTİK: Tenant'a özgü içeriği GLOBAL/UNIVERSAL kodlara ASLA ekleme!**

#### 📊 Tenant Bilgisi:
- **Tenant 1 (tuufi.com)**: Central sistem (Ana tenant, diğerlerini yönetir)
- **Tenant 2 (ixtif.com)**: Endüstriyel ekipman (forklift, transpalet) - **VARSAYILAN**
- **Tenant 1001 (muzibu.com.tr)**: Müzik platformu (song, album, artist, playlist)
- **Tenant 3+**: Gelecekte eklenecek diğer sektörler

**Detaylı tenant listesi:** `TENANT_LIST.md`

**Kod yazarken SOR:**
1. ❓ Bu tenant'a özgü bir özellik mi?
2. ❓ Tüm tenant'lar için mi yoksa sadece biri için mi?
3. ❓ Global kod yazıyorsam, tenant-aware mı?
4. ❓ Hangi database'e yazılacak? (Central mi, Tenant mi?)

#### 🎨 TENANT-AWARE TAİLWİND CSS

```bash
npm run css:all      # Tüm tenant CSS'lerini build et
npm run css:ixtif    # Sadece tenant-2
npm run css:muzibu   # Sadece tenant-1001
```

- Config: `tailwind/tenants/tenant-X.config.js`
- Output: `public/css/tenant-X.css`
- Layout: `{{ tenant_css() }}` helper kullan

---

### 🚨 2. VERİTABANI KORUMA

**BU GERÇEK CANLI SİSTEMDİR!**

#### ❌ KESİNLİKLE YAPMA:
1. `php artisan migrate:fresh` - ASLA!
2. `php artisan db:wipe` - ASLA!
3. Veritabanı truncate/DELETE/DROP - ASLA!
4. Sunucu ayarlarını rastgele değiştirme!
5. Apache/Nginx restart kafana göre yapma!

#### ⚠️ KULLANICI İZNİ GEREKIR:
- Veritabanına INSERT/UPDATE
- Migration dosyası oluşturma
- Mevcut kayıtları değiştirme

---

### 🚨 3. HTML RAPOR SİSTEMİ (Ana İletişim Aracı)

**🎯 KRİTİK: Analiz, rapor, planlama, sunum → DAIMA HTML!**

#### 📍 Ne Zaman HTML Oluştur - TETİKLEYİCİ KELİMELER:

**🎯 Aşağıdaki kelimeler kullanıcı mesajında geçiyorsa → HTML rapor oluştur:**

**1. Analiz & İnceleme:**
`analiz`, `analiz yap`, `analiz et`, `incele`, `inceleme`, `araştır`, `araştırma yap`, `değerlendir`, `değerlendirme`, `kontrol et`, `gözden geçir`, `tetkik et`

**2. Rapor & Dokümantasyon:**
`rapor`, `rapor hazırla`, `raporla`, `rapor oluştur`, `dokümante et`, `dokümantasyon`, `doküman hazırla`, `belge oluştur`, `kaydet`, `kayıt altına al`

**3. Planlama & Tasarım:**
`plan`, `plan oluştur`, `planla`, `planlama yap`, `tasarım`, `tasarla`, `taslak`, `taslak hazırla`, `strateji`, `strateji oluştur`, `yol haritası`, `roadmap`

**4. Sunum & Görselleştirme:**
`sunum`, `sunum hazırla`, `sun`, `detaylı sunum`, `görselleştir`, `göster`, `özetle`, `özet çıkar`, `özet hazırla`

**5. Detaylı İnceleme:**
`detaylı`, `detaylı analiz`, `detaylandır`, `derinlemesine`, `kapsamlı`, `geniş`, `gözat`, `tara`, `keşfet`

**6. Karşılaştırma:**
`karşılaştır`, `kıyasla`, `fark analizi`, `öneri sun`, `öneri listesi`

**7. Listeleme:**
`listele`, `liste çıkar`, `envanter`, `katalog`, `topla`, `derle`, `grupla`

**❌ HTML OLUŞTURMA (Direkt işlem yap):**
`düzelt`, `fix et`, `ekle`, `sil`, `değiştir`, `güncelle`, `oluştur` (kod için), `migration yap`, `migrate et`

**💡 Örnekler:**
- "Blog modülünü **incele**" → HTML oluştur ✅
- "SEO durumunu **raporla**" → HTML oluştur ✅
- "Modül yapısını **gözat**" → HTML oluştur ✅
- "**Detaylı sunum** hazırla" → HTML oluştur ✅
- "Bu hatayı **düzelt**" → Direkt kod yaz ❌
- "Yeni field **ekle**" → Direkt kod yaz ❌

#### 📂 Dosya Konumu - HİYERARŞİK SİSTEM:

**🎯 ANA KURAL:** Yıl → Ay → Gün → Konu → Versiyon

**📊 HTML Raporlar (Analiz, Plan, Sunum):**
```
public/readme/[YYYY]/[MM]/[DD]/[ana-konu]/[versiyon]/index.html
```

**Versiyon Mantığı:**
- **İlk rapor:** `v1/index.html` oluştur
- **Aynı konuya güncelleme:** Mevcut klasörü kontrol et, sonraki versiyon ekle (v2, v3...)
- **Farklı konu:** Yeni ana klasör aç
- **Ana klasör:** En güncel versiyona sembolik link

**Örnek Yapı:**
```
public/readme/2025/11/18/blog-detay/
├── v1/index.html          ← İlk tasarım analizi
├── v2/index.html          ← TOC ekleme planı
├── v3/index.html          ← Responsive düzenleme
└── index.html             ← Sembolik link (v3'e işaret eder)

URL: https://ixtif.com/readme/2025/11/18/blog-detay/
     (Her zaman en güncel versiyon gösterilir)
```

**📝 MD Dosyalar (Sadece TODO):**
```
readme/claude-docs/todo/[YYYY]/[MM]/[DD]/todo-[HH-MM]-[konu].md
```

**Örnek:**
```
readme/claude-docs/todo/2025/11/18/todo-14-30-payment-fix.md
readme/claude-docs/todo/2025/11/18/todo-15-00-blog-ai.md
```

**❌ KRİTİK:**
- TODO dosyaları ASLA `public/` altında değil!
- TODO dosyaları ASLA HTML klasörü içinde değil!
- MD ve HTML tamamen ayrı konumlarda!

**🔍 Versiyon Kontrolü (Otomatik Yap):**
```bash
# Tarih ayır
YYYY=$(date +%Y)
MM=$(date +%m)
DD=$(date +%d)

# Klasör var mı kontrol et
if [ -d "public/readme/$YYYY/$MM/$DD/blog-detay" ]; then
    # Varsa: Son versiyon numarasını bul, +1 ekle
    # v1, v2 varsa → v3 oluştur
else
    # Yoksa: v1 ile başla
fi
```

#### 🎨 HTML Tasarım Standartları:

**✅ ZORUNLU ÖZELLİKLER:**
- **Tailwind CSS Only**: SADECE Tailwind CDN kullan, custom CSS YASAK!
- **Modern & Minimal**: Gereksiz kutu içinde kutu YOK, nefes alan tasarım
- **Şık & Profesyonel**: Temiz, okunabilir, göz yormayan
- **Dark Mode**: Slate color palette (bg-slate-900, slate-800, slate-700)
- **Türkçe**: Tüm içerik Türkçe
- **Responsive**: Mobil uyumlu (grid md:grid-cols-X)
- **Tek Sayfa**: Scroll ile akıcı okuma

#### ❌ HTML İÇERİK KURALLARI:

**ASLA KOD YAZMA!**
- ❌ PHP kod blokları YASAK
- ❌ JavaScript kod blokları YASAK
- ❌ SQL sorguları YASAK
- ❌ Teknik implementation detayları YASAK

**SADECE MANTIK & STRATEJİ!**
- ✅ Nasıl çalışacak? (mantık)
- ✅ Hangi yaklaşım? (strateji)
- ✅ Ne yapılacak? (plan)
- ✅ Neden bu yöntem? (gerekçe)
- ✅ Beklenen sonuç? (hedef)
- ✅ Teknik terimler için Türkçe açıklama

#### 🎯 HTML Yapısı:

**TEK SEKME - SADECE YAPILACAKLAR!**
- ✅ Yapılacaklar listesi (ana odak)
- ✅ Adım adım plan
- ✅ Öncelik sıralaması
- ✅ Beklenen sonuçlar

**Yapılanlar ASLA kabak gibi önde olmasın!**
- ✅ Eğer gerekirse: Sayfanın en altında küçük bir özet
- ✅ Minimal, dikkat dağıtmayan
- ✅ Kullanıcı isterse ekle, istemezse ekleme!

#### 📐 Modern HTML Şablonu (Tailwind CSS):

```html
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>[İşlem Adı] - Analiz & Plan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-slate-100 min-h-screen">
    <div class="max-w-6xl mx-auto px-4 py-12">
        <!-- Header -->
        <header class="mb-16 pb-8 border-b border-slate-700">
            <h1 class="text-4xl font-bold mb-4 bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                📊 [İşlem Adı]
            </h1>
            <div class="text-slate-400 text-lg">
                [Kısa açıklama buraya]
            </div>
            <div class="mt-4 flex gap-4 text-sm text-slate-500">
                <span>📅 [Tarih]</span>
                <span>🎯 Tenant: [tenant.com]</span>
                <span>👤 [Talep özeti]</span>
            </div>
        </header>

        <!-- Ana İçerik -->
        <section class="mb-16">
            <h2 class="text-3xl font-bold mb-8 text-blue-400">🎯 Yapılacaklar</h2>

            <!-- Adım 1 -->
            <div class="bg-slate-800/50 border-l-4 border-blue-500 rounded-lg p-6 mb-4">
                <div class="flex items-start gap-4">
                    <div class="bg-blue-500 text-white font-bold rounded-full w-10 h-10 flex items-center justify-center flex-shrink-0">1</div>
                    <div>
                        <h3 class="text-xl font-bold text-blue-300 mb-2">
                            [İşlem Başlığı]
                            <span class="ml-3 px-3 py-1 bg-red-600 text-white text-xs rounded-full">Yüksek Öncelik</span>
                        </h3>
                        <p class="text-slate-300 leading-relaxed">
                            <span class="text-yellow-300 font-semibold">SEO</span>
                            <span class="text-slate-400 text-sm">(Arama motoru optimizasyonu)</span>
                            için meta taglerini güncelleyeceğiz.
                        </p>
                        <p class="mt-3 text-slate-400"><strong class="text-white">Beklenen Sonuç:</strong> Arama motorlarında görünürlük artışı</p>
                    </div>
                </div>
            </div>

            <!-- Adım 2 -->
            <div class="bg-slate-800/50 border-l-4 border-green-500 rounded-lg p-6 mb-4">
                <div class="flex items-start gap-4">
                    <div class="bg-green-500 text-white font-bold rounded-full w-10 h-10 flex items-center justify-center flex-shrink-0">2</div>
                    <div>
                        <h3 class="text-xl font-bold text-green-300 mb-2">
                            [İşlem Başlığı]
                            <span class="ml-3 px-3 py-1 bg-yellow-600 text-white text-xs rounded-full">Orta Öncelik</span>
                        </h3>
                        <p class="text-slate-300">Açıklama buraya...</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Alternatif: Grid Kartlar (3 tenant gibi karşılaştırma için) -->
        <section class="mb-16">
            <h2 class="text-3xl font-bold mb-8 text-purple-400">📊 [Başlık]</h2>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-slate-800/50 rounded-lg p-6 border border-slate-700">
                    <h3 class="text-xl font-bold mb-4 text-blue-400">[Başlık]</h3>
                    <div class="space-y-2 text-sm text-slate-300">
                        <p>✅ [Bilgi]</p>
                        <p>❌ [Bilgi]</p>
                    </div>
                </div>
                <!-- Diğer kartlar... -->
            </div>
        </section>

        <!-- Footer -->
        <footer class="mt-20 pt-8 border-t border-slate-700 text-center text-slate-500 text-sm">
            <p>🤖 Claude AI tarafından oluşturuldu - Tailwind CSS</p>
        </footer>
    </div>
</body>
</html>
```

**🎨 Tailwind Renk Paleti:**
- **Background:** `bg-slate-900`, `bg-slate-800/50` (opacity ile)
- **Border:** `border-slate-700`, `border-l-4 border-blue-500`
- **Text:** `text-slate-100` (ana), `text-slate-300` (paragraf), `text-slate-400` (açıklama), `text-slate-500` (footer)
- **Accent:** `text-blue-400`, `text-green-400`, `text-purple-400`, `text-red-400`, `text-yellow-300`
- **Badge/Priority:** `bg-red-600`, `bg-yellow-600`, `bg-green-600` + `text-white`
- **Gradient:** `bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent`

**📐 Tailwind Layout:**
- **Container:** `max-w-6xl mx-auto px-4 py-12`
- **Grid:** `grid md:grid-cols-3 gap-6` (responsive)
- **Spacing:** `mb-4`, `mb-8`, `mb-16` (4=1rem, 8=2rem, 16=4rem)
- **Rounded:** `rounded-lg` (large), `rounded-full` (circle)
- **Flex:** `flex items-start gap-4` (adım numarası için)

**UNUTMA:** Custom CSS YASAK! Sadece Tailwind class'ları kullan!

#### 📎 Kullanıcıya Link Verme:

**❌ ASLA PATH VERME:**
```
public/readme/2025/11/18/analiz/v1/index.html  # YANLIŞ!
```

**✅ MUTLAKA WEB LİNKİ VER (Versiyonlu):**
```
✅ Blog Detay Analizi (v2) hazır!
📊 Raporu görüntüle: https://ixtif.com/readme/2025/11/18/blog-detay/

📌 Önceki versiyon:
   v1 (İlk tasarım): https://ixtif.com/readme/2025/11/18/blog-detay/v1/
```

**💡 İPUCU:**
- Ana link → En güncel versiyon (sembolik link sayesinde)
- Kullanıcı önceki versiyonları görmek isterse → /v1/, /v2/ linkleri ver

#### 🔄 Sonraki Güncellemeler - VERSİYON YÖNETİMİ:

**Kullanıcı aynı konu için güncelleme isterse:**

1️⃣ **Klasör kontrolü yap:**
```bash
ls public/readme/2025/11/18/blog-detay/
# v1, v2 varsa → v3 oluştur
```

2️⃣ **Yeni versiyon oluştur:**
```bash
mkdir -p public/readme/2025/11/18/blog-detay/v3/
# v3/index.html oluştur (güncellenen içerikle)
```

3️⃣ **Sembolik linki güncelle:**
```bash
cd public/readme/2025/11/18/blog-detay/
ln -sf v3/index.html index.html
```

4️⃣ **Kullanıcıya bildir:**
```
✅ Blog Detay Analizi güncellendi! (v2 → v3)
📊 Güncel rapor: https://ixtif.com/readme/2025/11/18/blog-detay/
📌 v2: https://ixtif.com/readme/2025/11/18/blog-detay/v2/
```

**❌ YAPMA:**
- Yeni klasör açma (blog-detay-redesign, blog-detay-fix gibi)
- Eski HTML'i silme (versiyonları sakla!)
- Aynı HTML'i güncelleme (yeni versiyon oluştur!)

**UNUTMA:** HTML = Rapor, Analiz, Plan, Sunum (KOD YOK!)

---

### 🚨 4. MARKDOWN (MD) KULLANIMI

**📝 MD = Sadece TODO!**

#### 🎯 TETİKLEYİCİ KELİMELER (MD için):

**Sadece bu kelimeler kullanıcı mesajında geçerse → MD oluştur:**
- `todo`
- `todo oluştur`
- `todo listesi`
- `yapılacaklar`
- `yapılacaklar listesi`
- `checklist`
- `checklist oluştur`
- `md dosyası oluştur`

**❌ DİĞER TÜM DURUMLAR → HTML OLUŞTUR (MD değil!)**
- "Plan hazırla" → HTML oluştur (MD değil!)
- "Analiz et" → HTML oluştur (MD değil!)
- "Rapor hazırla" → HTML oluştur (MD değil!)
- "İncele" → HTML oluştur (MD değil!)

#### 📂 MD Dosya Konumu (Hiyerarşik):
```
readme/claude-docs/todo/[YYYY]/[MM]/[DD]/todo-[HH-MM]-[konu].md
```

**Örnek:**
```
readme/claude-docs/todo/2025/11/18/todo-14-30-payment-fix.md
readme/claude-docs/todo/2025/11/18/todo-15-00-blog-ai.md
```

**❌ KRİTİK:**
- TODO dosyaları ASLA `public/` altında değil!
- TODO dosyaları ASLA HTML klasörü içinde değil!
- MD ve HTML tamamen ayrı konumlarda!

#### 📋 MD İçerik (Sadece TODO formatı):
- ✅ Teknik todo listesi
- ✅ Checkbox'lar (- [ ] format)
- ✅ Dosya path'leri
- ✅ Komutlar
- ✅ Kod referansları
- ✅ Teknik notlar

**Örnek MD:**
```markdown
# Payment Fix - TODO

## Backend
- [ ] `Modules/Payment/app/Services/PaymentService.php` - Timeout artır
- [ ] `Modules/Payment/app/Jobs/ProcessPaymentJob.php` - Retry logic ekle

## Migration
- [ ] `php artisan make:migration add_status_to_payments`
- [ ] Migration çalıştır: `php artisan migrate`

## Test
- [ ] Cache temizle: `php artisan view:clear`
- [ ] Test: `curl https://ixtif.com/admin/payment/process`
- [ ] Production deploy

## Notlar
- API timeout: 180 saniye
- Retry count: 3
```

**UNUTMA:** MD = Sadece TODO! Plan/Analiz/Rapor → HTML!

---

### 🚨 5. GIT CHECKPOINT KURALLARI

**🔐 Önemli İşlem Öncesi Git Checkpoint**

#### ✅ Ne Zaman Checkpoint Yap:
- **Büyük refactor** yapacaksan
- **Çok dosya** değişikliği olacaksa
- **Riskli işlem** yapacaksan
- **Karmaşık modül** geliştirme

#### ❌ Ne Zaman Checkpoint YAPMA:
- Küçük bug fix
- Tek dosya değişikliği
- Typo düzeltme
- CSS/Tailwind değişikliği
- Basit view güncellemesi

#### 📋 Checkpoint Workflow:
```bash
# Sadece büyük işlemler için!
git add .
git commit -m "🔧 CHECKPOINT: Before [işlem özeti]"
git log -1 --oneline  # Hash'i kaydet
```

#### 🚨 Git Reset İçin İZİN AL:
```bash
# ❌ ASLA otomatik yapma!
git reset --hard [hash]

# ✅ Önce kullanıcıya sor!
"Git checkpoint'e geri döneyim mi? (hash: abc123)"
```

**UNUTMA:** Küçük işleri git'e atma, kullanıcı isterse yükle!

---

### 🚨 6. DOSYA İZİNLERİ (PERMİSSİON) - KRİTİK!

**🔴 ANA KURAL: ROOT KULLANIMI YASAK!**

**❌ ASLA ROOT KULLANMA!**
- Root ile dosya oluşturma → YASAK!
- Root ile klasör oluşturma → YASAK!
- Root olarak komut çalıştırma → YASAK!

**✅ HER ZAMAN tuufi.com_ KULLANICISI İLE ÇALIŞ!**

#### 🎯 Doğru Kullanım:

**Yöntem 1: Bash kullanırken (ÖNERİLEN):**
```bash
# ✅ DOĞRU: tuufi.com_ kullanıcısı ile işlem yap
sudo -u tuufi.com_ mkdir -p /path/to/directory/
sudo -u tuufi.com_ touch /path/to/file.php
sudo -u tuufi.com_ bash -c 'echo "content" > /path/to/file.php'
```

**Yöntem 2: Claude Write/Edit tool kullanırsan:**
```bash
# ⚠️ Write/Edit tool root:root oluşturur, MUTLAKA düzelt!

# 1. Owner değiştir (ZORUNLU!)
sudo chown tuufi.com_:psaserv /path/to/file.php

# 2. İzin ver (ZORUNLU!)
sudo chmod 644 /path/to/file.php  # Dosyalar için
sudo chmod 755 /path/to/directory/  # Klasörler için

# 3. OPcache reset (PHP dosyaları için)
curl -s -k https://ixtif.com/opcache-reset.php > /dev/null

# 4. Test et (ZORUNLU!)
curl -s -k -I https://ixtif.com/path/to/file | grep HTTP
# Beklenen: HTTP/2 200
# Eğer 403 Forbidden → Permission hatası!
# Eğer 500 Error → Ownership/Permission hatası!
```

#### ❌ NEDEN ROOT YASAK?

**Problem 1: Ownership Hatası**
- Root ile oluşturulan dosyalar → `root:root` owner
- Nginx/PHP-FPM → Bu dosyaları okuyamaz!
- Sonuç → **500 Internal Server Error** veya **403 Forbidden**

**Problem 2: Permission Cascade**
- Root ile klasör oluşturursan → İçindeki TÜM dosyalar root:root!
- Tek bir root dosyası → Tüm klasörü bozar!

**Problem 3: Güvenlik & Deployment**
- Root dosyaları sadece root değiştirebilir
- Deployment sırasında sorun çıkar
- Git pull/push çalışmaz

#### 📋 Toplu Klasör Düzeltme:

```bash
# Yanlışlıkla root ile oluşturduysan düzelt:
sudo chown -R tuufi.com_:psaserv /path/to/directory/
sudo find /path/to/directory/ -type f -exec chmod 644 {} \;
sudo find /path/to/directory/ -type d -exec chmod 755 {} \;
```

#### 🎯 Doğru İzinler:

✅ **Owner:** `tuufi.com_:psaserv` (ZORUNLU! Root değil!)
✅ **Dosya:** `644` (-rw-r--r--) → PHP, HTML, Blade dosyaları
✅ **Klasör:** `755` (drwxr-xr-x) → Dizinler

❌ **YANLIŞ (Site çöker!):**
- `root:root` ownership → Nginx/PHP-FPM okuyamaz!
- `600` permission → Sadece owner okur, grup/others okuyamaz!
- `700` klasör → Nginx klasöre giremez!

#### 💡 Pratik Örnekler:

**HTML Rapor Oluşturma:**
```bash
# ✅ DOĞRU
sudo -u tuufi.com_ mkdir -p public/readme/2025/11/18/blog-analiz/v1/

# ❌ YANLIŞ
mkdir -p public/readme/2025/11/18/blog-analiz/v1/  # Root kullanma!
```

**MD TODO Oluşturma:**
```bash
# ✅ DOĞRU
sudo -u tuufi.com_ mkdir -p readme/claude-docs/todo/2025/11/18/
sudo -u tuufi.com_ touch readme/claude-docs/todo/2025/11/18/todo-14-30-payment.md

# ❌ YANLIŞ
touch readme/claude-docs/todo/2025/11/18/todo-14-30-payment.md  # Root kullanma!
```

**⚠️ BASH mkdir KULLANIRKEN DİKKAT!**

```bash
# ❌ YANLIŞ: Bash mkdir kullanırsan → root:root klasör oluşturur!
mkdir -p public/readme/2025/11/18/test/

# ✅ DOĞRU: MUTLAKA sudo -u tuufi.com_ kullan!
sudo -u tuufi.com_ mkdir -p public/readme/2025/11/18/test/

# 🔧 Yanlışlıkla root ile oluşturduysan toplu düzelt:
sudo chown -R tuufi.com_:psaserv public/readme/2025/
sudo find public/readme/2025/ -type d -exec chmod 755 {} \;
sudo find public/readme/2025/ -type f -exec chmod 644 {} \;
```

**UNUTMA:**
- ✅ Her zaman `sudo -u tuufi.com_` kullan!
- ✅ Write/Edit tool kullandıysan → chown + chmod + test!
- ✅ Bash mkdir kullandıysan → chown + chmod + test!
- ❌ ASLA root olarak dosya/klasör oluşturma!
- ❌ Bash mkdir bile root:root oluşturur → sudo -u tuufi.com_ zorunlu!

---

### 🚨 7. ANA DİZİN TEMİZ KALMALI

**❌ Ana Dizine ASLA Dosya Açma:**
- test-*.php
- debug-*.txt
- setup-*.php
- fix-*.php
- GUIDE-*.md

**✅ Doğru Konum:**
- `readme/[klasör]/` altında
- `/tmp/` geçici dosyalar için
- `tests/` test dosyaları için

**İstisnalar:** CLAUDE.md, README.md, .env, composer.json (core dosyalar)

#### 📸 GÖRSEL/SCREENSHOT TEMİZLİĞİ

**🎯 Kullanıcı ana dizine görsel attıysa:**
- ✅ Görsel → Referans/örnek amaçlıdır
- ✅ İşlem tamamlandıktan sonra → Otomatik sil!
- ✅ Ana dizin → Her zaman temiz

**Örnek Senaryo:**
```bash
# Kullanıcı: "ekran-goruntusu.png" gönderir
# 1. Görseli analiz et
# 2. Tasarım/kodu oluştur
# 3. İş bitince:
sudo rm "ekran-goruntusu.png"
# 4. Kullanıcıya bildir: "✅ Görsel silindi, ana dizin temiz"
```

**UNUTMA:** Ana dizine atılan görseller geçicidir, iş bitince temizle!

---

### 🚨 8. BUFFER DOSYALARI (a-console.txt, a-html.txt)

**⚠️ Bu dosyaları ASLA silme!**

#### 📋 İKİ MOD SİSTEMİ:

**PASİF MOD (Varsayılan):**
- Kullanıcı bahsetmezse → Hiç dokunma!

**AKTİF MOD (Kullanıcı tetikleyince):**
- Kullanıcı "a-console.txt" derse → Aktif ol
- Kullanıcı "console" derse → Aktif ol
- Kullanıcı "debug" derse → Aktif ol

**Aktif olunca:** O konuşma boyunca otomatik takip et, analiz et

**UNUTMA:** Her konuşma yeni başlangıç, yeniden tetikleyici gerekli!

---

## 📋 ÇALIŞMA YÖNTEMİ

### 🧠 TEMEL YAKLAŞIM
- **Extended Think**: Her mesajı derin analiz et
- **Türkçe İletişim**: Daima Türkçe yanıt ver
- **Otomatik Devam**: Sorma, direkt hareket et
- **HTML İlk Öncelik**: Analiz/rapor → HTML oluştur

### 🎨 OTOMATİK CACHE & BUILD

**⚡ Tailwind/View değişikliğinden SONRA otomatik yap:**

```bash
# 1. Cache temizle
php artisan view:clear
php artisan responsecache:clear

# 2. Build
npm run prod
```

**Otomatik yap, onay bekleme!**

### ☢️ NUCLEAR CACHE CLEAR

**Kullanıcı "değişiklikler yansımadı" derse:**

```bash
php artisan cache:clear && \
php artisan config:clear && \
php artisan route:clear && \
php artisan view:clear && \
php artisan responsecache:clear && \
find storage/framework/views -type f -name "*.php" -delete && \
curl -s -k https://ixtif.com/opcache-reset.php && \
php artisan config:cache && \
php artisan route:cache
```

### 🗑️ DOSYA TEMİZLEME

**İş bittikten sonra otomatik temizle:**
- Geçici test dosyaları
- Debug script'leri
- /tmp/ altındaki dosyalar
- Yanlış konumdaki dosyalar

**UNUTMA:** Her işlem sonrası temizlik yap!

---

## 🎨 TASARIM STANDARTLARI

### 🎯 GENEL STANDARTLAR
- **Admin**: Tabler.io + Bootstrap + Livewire
- **Frontend**: Alpine.js + Tailwind CSS
- **Icon**: SADECE FontAwesome (`fas`, `far`, `fab`)
- **Renkler**: Framework renkleri (custom yok)

### 📐 TASARIMSAL DEĞİŞİKLİKLERDE HTML TASLAK

**🔴 KRİTİK KURAL: Tasarımsal değişikliklerde ÖNCE HTML taslak göster!**

#### Ne Zaman Taslak Zorunlu:
- Yeni UI component oluşturma
- Mevcut sayfaya yeni bölüm/panel ekleme
- Liste görünümü değişikliği
- Form tasarımı değişikliği
- Dashboard/widget ekleme
- Toplu işlem panelleri (bulk upload, bulk edit vb.)

#### Taslak Süreci:
1. **HTML taslak oluştur** → `public/readme/[tarih]/[konu]/v1/index.html`
2. **Kullanıcıya link ver** → Onay bekle
3. **"UYGUNDUR" alınca** → Kodu yaz
4. **Değişiklik isterse** → v2, v3... oluştur

#### Örnek:
```
Kullanıcı: "Albüme toplu şarkı yükleme ekle"
Claude: Taslağı hazırladım: https://ixtif.com/readme/2025/11/22/album-bulk-upload/
        Onay verirseniz uygulamaya geçerim.
Kullanıcı: "UYGUNDUR" veya "şunu değiştir..."
```

**UNUTMA:** Tasarımsal işlerde önce göster, sonra yap!

### 🎨 RENK KONTRAST (WCAG AA)

**Minimum kontrast oranı: 4.5:1**

**✅ Doğru Kullanım:**
- `bg-white` → `text-gray-900`
- `bg-blue-600` → `text-white`
- `dark:bg-gray-900` → `dark:text-white`

**❌ Yanlış:**
- Mavi üstüne mavi
- Koyu üstüne koyu
- Açık üstüne açık

**UNUTMA:** Kullanıcı "okunmuyor" derse → SEN HATA YAPTIN!

### 🏗️ ADMIN PANEL PATTERN

**YENİ PATTERN (Zorunlu):**
- `index.blade.php` - Liste sayfası
- `manage.blade.php` - Create/Edit tek sayfa

**ESKİ PATTERN (Kullanma):**
- create.blade.php ❌
- edit.blade.php ❌

---

## 🚨 ACİL DURUM ÇÖZÜMLER

### BLADE @ DİRECTİVE ÇAKIŞMASI

```blade
# ❌ HATALI:
"@context": "https://schema.org"

# ✅ DOĞRU:
"@@context": "https://schema.org"  # @@ ile escape
```

### ARRAY → STRING HATASI

```blade
# ❌ HATALI:
{{ $item->category->title }}  # Array döner!

# ✅ DOĞRU:
@json($item->category->title)  # JSON'a çevirir
```

---

## 💾 SİSTEM HAFIZASI

### DİL SİSTEMİ
- **Admin**: `system_languages` + `admin_locale`
- **Site**: `site_languages` + `site_locale`

### PATTERN SİSTEMİ
- **Page Pattern = Master**: Yeni modüller Page pattern'i alır
- **JSON çoklu dil + SEO + Modern PHP**

### ⚙️ SETTINGS SİSTEMİ

**Site bilgileri Settings modülünden çekilir:**

```php
// Setting value çekme
setting('site_name'); // "İxtif"
setting('site_phone'); // "+90 212 123 45 67"
```

**Yeni Setting Group oluşturmadan ÖNCE kullanıcı onayı al!**

### THUMBMAKER SİSTEMİ

**Görsel oluştururken MUTLAKA Thumbmaker kullan:**

```blade
<img src="{{ thumb($media, 400, 300) }}" alt="Thumbnail" loading="lazy">
```

**Best Practices:**
- WebP kullan
- loading="lazy" ekle
- Kalite 80-90

---

## 🏢 TENANT YÖNETİMİ

### 🚨 TENANT SİSTEMİ

**⚠️ BU BİR MULTI-TENANT SİSTEMDİR!**

#### Sistem Yapısı:
- **Tenant 1 (tuufi.com)**: Central sistem
- **Tenant 2 (ixtif.com)**: Endüstriyel ekipman - **VARSAYILAN**
- **Tenant 1001 (muzibu.com)**: Müzik platformu
- **Tenant 3+**: Diğer sektörler

#### Database Yapısı:
- Her tenant **tamamen bağımsız database**
- Central: `tuufi_db`
- Tenant 2: `tenant_2_db`

### 🗄️ MİGRATION OLUŞTURMA

**🚨 ÇİFTE MİGRATION ZORUNLU!**

Her migration **İKİ YERDE** oluşturulmalı:

```bash
# 1. Central
database/migrations/YYYY_MM_DD_create_table.php

# 2. Tenant
database/migrations/tenant/YYYY_MM_DD_create_table.php

# Migration çalıştır
php artisan migrate  # Central
php artisan tenants:migrate  # Tüm tenant'lar
```

**UNUTURSAN:** Tenant database'ler çalışmaz!

### YENİ TENANT EKLEME

**Detaylı kılavuz:** `readme/tenant-olusturma.md`

1. Plesk Panel: Domain alias ekle (SEO redirect KAPALI!)
2. Laravel Tenant: Tinker ile oluştur
3. Config: `plesk repair web tuufi.com -y`
4. Test: `curl -I https://domain.com/`

**⚠️ KRİTİK:** NGINX custom config oluşturma! (Livewire bozar)

---

## 📝 ÖNEMLİ NOT

**Proje Giriş:** nurullah@nurullah.net / test
**URL:** www.laravel.test/login

**İşlemler bittikten sonra Siri ile seslendir!**

**Detaylı Dökümanlar:** `readme/claude-docs/` klasöründe

---

**UNUTMA:**
- 🎯 Analiz/Rapor → HTML oluştur (KOD YOK!)
- 📝 TODO → MD oluştur (sadece gerekirse)
- 🔐 Önemli işlem → Git checkpoint
- 🗑️ İş bitti → Temizlik yap
- 👔 Her şey basit, minimal, profesyonel!
