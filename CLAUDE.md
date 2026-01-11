# 🏢 MULTI-TENANT SİSTEM MİMARİSİ

**Kullanıcıya migration öncesi danış!**

---

## 🔴 STORAGE & MEDYA KORUMA - MUTLAK YASAK!

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

## 🚨 KRİTİK PERFORMANS NOTLARI - ÖNCE BU BÖLÜMÜ OKU!

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
- ✅ **Database Per Tenant** pattern - Her tenant tamamen izole edilmiş database'de çalışır
- ✅ **Central database** (tuufi_4ekim) SADECE tenant yönetimi ve merkezi AI/subscription sistemi için
- ✅ **Tenant 1 (tuufi.com)** = Central tenant (Ana sistem, diğer tenant'ları yönetir)
- ✅ **Her tenant kendi users, roles, permissions'ına sahip!**

### 🗄️ Database Dağılımı

**Central Database (tuufi_4ekim) - SADECE Sistem Yönetimi:**
- `tenants`, `domains` - Tenant yönetimi
- `ai_*` tablolar - AI modülü (merkezi AI sistemi)
- `admin_languages`, `system_languages` - Sistem dilleri
- `migrations` - Central migration kayıtları
- `users` (19 kullanıcı) - **SADECE sistem admin'leri** (tenant kullanıcıları DEĞİL!)
- `subscriptions` (7 kayıt) - **Eski test kayıtları** (gerçek subscriptions tenant'larda!)

**Tenant Database (tenant_X) - Her Tenant Tamamen Bağımsız:**
- `users`, `roles`, `permissions`, `model_has_roles`, `model_has_permissions` - **HER TENANT KENDI KULLANICILARI!**
- `subscriptions` - **HER TENANT KENDI ABONELİKLERİ!** (Muzibu: 24 kayıt)
- `pages`, `blogs`, `blog_categories` - İçerik yönetimi
- `shop_products`, `shop_categories`, `brands` - Ürün sistemi
- `media` - Medya dosyaları (tenant'a özel)
- `seo_settings`, `settings_values` - Tenant ayarları
- `migrations` - Tenant migration kayıtları
- **Muzibu için:** `muzibu_songs`, `muzibu_albums`, `muzibu_artists`, `muzibu_playlists`, `muzibu_genres`, `muzibu_sectors`
- **İxtif için:** `shop_products` (endüstriyel ekipman - forklift, transpalet)

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
   - Blog, Product, Page, User → Tenant database'e yazılmalı!

3. ❌ Tenant verilerini central'dan okuma!
   - Her şey (users dahil) tenant context'te tenant DB'den okunur!

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
   // Tenant context'te OTOMATIK tenant DB kullanılır
   User::all();      // Tenant users (tenant_muzibu_1528d0.users)
   Role::all();      // Tenant roles
   Page::all();      // Tenant pages
   Blog::all();      // Tenant blogs

   // SADECE tenant yönetimi için central DB
   // (Normal kodlarda kullanma, sistem içi işlemler)
   \App\Models\Tenant::all();  // Central DB: tenants tablosu
   ```

4. ✅ Migration oluştururken MODÜL İÇİNDE, İKİ YERDE oluştur:
   ```
   Modules/[Modül]/database/migrations/YYYY_MM_DD_xxx.php          → Central
   Modules/[Modül]/database/migrations/tenant/YYYY_MM_DD_xxx.php   → Tenant
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

**⚠️ BU SİSTEM MULTI-TENANT! HER TENANT FARKLI SEKTÖR!**

**🔥 KRİTİK: Tenant'a özgü içeriği GLOBAL/UNIVERSAL kodlara ASLA ekleme!**

#### 📊 Tenant Bilgisi:
**Detaylı bilgi için:** "🚨 ÖNCE BU BÖLÜMÜ OKU - SİSTEM TENANT AWARE!" bölümüne bak!

**Hızlı hatırlatma:**
- Tenant 2 (ixtif.com) → Endüstriyel ekipman (forklift, transpalet)
- Tenant 1001 (muzibu.com.tr) → Müzik platformu (song, album, artist)
- Her tenant farklı sektör → Global koda tenant-özel içerik EKLEME!
- Detaylı liste: `TENANT_LIST.md`

#### 🎨 TENANT-AWARE TAİLWİND CSS

**🚨 KRİTİK: Site tenant CSS kullanıyor, app.css DEĞİL!**

Her tenant kendi CSS dosyasını yükler:
- ixtif.com → `public/css/tenant-2.css`
- muzibu.com → `public/css/tenant-1001.css`
- app.css → Sadece merkezi/admin için

**✅ DOĞRU BUILD KOMUTU:**
```bash
npm run prod         # ✅ Tenant CSS + app.css (HEPSİ)
npm run build        # ✅ Aynı şey (alias)
```

**📦 Diğer Komutlar:**
```bash
npm run css:all      # Sadece tüm tenant CSS'leri
npm run css:ixtif    # Sadece tenant-2
npm run css:muzibu   # Sadece tenant-1001
npm run mix-only     # Sadece app.css (Laravel Mix)
```

**⚠️ Tailwind class eklediğinde:**
1. `tailwind.config.js` → safelist'e ekle (purge koruması)
2. `npm run prod` çalıştır (tenant CSS'leri rebuild eder)
3. Cache temizle: `php artisan view:clear && php artisan responsecache:clear`

**📁 Dosya Yapısı:**
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

### 🚨 3. SİSTEM TUTARLILIĞI - PATTERN UYUMU

**🔥 KRİTİK: Yeni dosyalar MEVCUT DOSYALARDAN ilham almalı!**

#### 🎯 ANA KURAL: "Var Olandan Öğren, Aynısını Uygula"

Yeni bir sayfa, component, tablo veya UI elementi oluştururken:
1. **ÖNCE** referans dosyayı aç ve incele
2. **AYNI** pattern, class, yapı ve spacing'i kullan
3. **FARKLI** yapma, tutarlılığı bozma!

#### 📋 REFERANS DOSYALAR

**Admin Panel (Tablo, Form, Liste, Sıralama):**
```
Modules/Page/resources/views/admin/livewire/page-component.blade.php              → Tablo pattern
Modules/Page/resources/views/admin/livewire/page-manage-component.blade.php       → Form pattern
Modules/Portfolio/resources/views/admin/livewire/portfolio-component.blade.php    → Liste pattern
Modules/Portfolio/resources/views/admin/livewire/category-component.blade.php     → Sıralama (drag & drop) pattern
```

**Frontend Theme:**
```
resources/views/themes/simple/       → Fallback tema (tüm temalar bundan türer)
resources/views/themes/[tema-adi]/   → Tenant'a özel tema (simple'dan override eder)
```

#### ⚠️ YAPILACAKLAR (Yeni Dosya Oluştururken)

1. Referans dosyayı oku (`Read` tool ile)
2. Tablo class'larını, buton spacing'lerini, ikon stillerini kopyala
3. Sadece içeriği değiştir, yapıyı değiştirme
4. `btn-group` kullanma, `d-flex gap-2` kullan

**UNUTMA:** Referans dosya değişirse, yeni dosyalar da o pattern'i alır!

---

### 🚨 4. HTML RAPOR SİSTEMİ (Ana İletişim Aracı)

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

**🚨 KRİTİK: Sembolik Link Zorunlu!**
- ❌ Auto-fixer script sadece `index.php` oluşturur (redirect için)
- ✅ README Index sistemi `index.html` arar (dinamik tarama)
- ✅ Her rapor klasöründe **MUTLAKA** sembolik link olmalı:
  ```bash
  sudo -u tuufi.com_ ln -sf v1/index.html [klasor]/index.html
  ```
- ⚠️ Sembolik link yoksa → Rapor README Index'te görünmez!
- ✅ Sistem tamamen dinamik: PHP her yüklemede otomatik tarar, yeni raporları listeler

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

**Tasarım:** Tailwind CDN, dark mode (slate palette), modern minimal, responsive, Türkçe, tek sayfa

**İçerik Kuralı:** SADECE mantık & strateji (KOD YOK! - PHP/JS/SQL yasak), Türkçe açıklama

**Yapı:** Header (başlık + meta) → Yapılacaklar (adım adım, kartlar) → Footer (Claude AI)

#### 🎓 İKİ SEVİYELİ İÇERİK ZORUNLU!

**🚨 KRİTİK: Her HTML raporda hem teknik hem basit anlatım olmalı!**

**Hedef Kitle:**
- 👨‍💻 **Teknik Ekip:** Geliştiriciler, DevOps, sistem yöneticileri
- 👤 **Amatör/Kullanıcı:** Proje sahipleri, yöneticiler, teknik bilgisi olmayan kullanıcılar

**İçerik Yapısı (Zorunlu):**

```html
<!-- 1. BASIT ANLATIM (Herkes İçin) -->
<div class="bg-green-900/20 border border-green-800 rounded-lg p-6">
    <h3>📝 Basit Anlatım (Herkes İçin)</h3>
    <p>
        Ne yapıldı, neden yapıldı, ne değişti?
        Günlük Türkçe, teknik terim YOK!
    </p>
    <ul>
        <li>✅ Kullanıcı dostu açıklama</li>
        <li>✅ Benzetmeler, örnekler</li>
        <li>✅ "Neden önemli?" sorusunun cevabı</li>
    </ul>
</div>

<!-- 2. TEKNİK DETAYLAR (Geliştiriciler İçin) -->
<div class="bg-blue-900/20 border border-blue-800 rounded-lg p-6">
    <h3>🔧 Teknik Detaylar (Geliştiriciler İçin)</h3>
    <p>
        Dosya path'leri, fonksiyon isimleri, algoritma,
        veritabanı yapısı, mimari kararlar
    </p>
    <ul>
        <li>📁 Dosya konumları</li>
        <li>⚙️ Kullanılan teknolojiler</li>
        <li>🔗 İlişkili sistemler</li>
    </ul>
</div>
```

**Örnek Karşılaştırma:**

❌ **YANLIŞ (Sadece Teknik):**
```
Payment Gateway'de webhook endpoint'ine yeni middleware eklendi.
VerifyCsrfToken exception list'ine /api/payment/webhook path'i eklendi.
```

✅ **DOĞRU (İki Seviyeli):**

**📝 Basit Anlatım:**
"Ödeme sistemi artık daha güvenli çalışıyor. Dış firmalardan gelen bildirimler
doğru şekilde işleniyor. Kullanıcılar ödeme yaptığında sistem anında
haberdar oluyor ve siparişler otomatik onaylanıyor."

**🔧 Teknik Detaylar:**
- Middleware: `app/Http/Middleware/VerifyCsrfToken.php`
- Webhook path: `/api/payment/webhook`
- Exception eklendi: CSRF korumasından muaf
- İlgili controller: `PaymentWebhookController.php`

**Zorunlu Bölümler:**

1. **📝 Basit Anlatım:**
   - Günlük dil, sade Türkçe
   - Teknik terim varsa parantez içinde açıkla
   - Örnek: "Cache (önbellek - hızlı erişim için geçici depolama)"

2. **🔧 Teknik Detaylar:**
   - Dosya path'leri
   - Fonksiyon/class isimleri
   - Veritabanı tablo/field isimleri
   - Kullanılan teknolojiler

3. **💡 Neden Önemli? (Her iki seviyede de):**
   - Basit: "Kullanıcı deneyimi nasıl iyileşti?"
   - Teknik: "Performans/güvenlik kazancı nedir?"

**UNUTMA:** Amatör kullanıcı HTML açtığında "ne yapıldığını" anlamalı!

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

#### 🔴 BİRİKİMLİ VERSİYON İÇERİĞİ (ÇOK KRİTİK!)

> **💎 "Versiyon mantığı, eskiyi silmek ve unutmak değil; yeni kurallarla eskiyi geliştirip güçlendirmektir."**

**🚨 MUTLAKA UYGULA: Her yeni versiyon önceki versiyonların BİRİKİMLİ devamıdır!**

**Problem:** v1 → v2 → v3 geçişlerinde önceki bilgiler kayboluyor, sadece son söylenenler ekleniyor.

**Çözüm:** Her versiyon = Önceki tüm versiyonlar + Yeni eklemeler

**Formül:**
```
v1 = İlk bilgiler
v2 = v1 + Yeni bilgiler
v3 = v1 + v2 + Yeni bilgiler (v2 zaten v1'i içerir)
v10 = v9 + Yeni bilgiler (v9 zaten v1-v8'i içerir)
```

**Yeni Versiyon Oluştururken:**

1️⃣ **Önceki versiyonu OKU:**
   - En son versiyonun tüm içeriğini oku
   - Tüm başlıkları, bölümleri, senaryoları not al
   - v1'den beri söylenen her şey orada olmalı

2️⃣ **Yeni bilgileri EKLE:**
   - Kullanıcının yeni söylediklerini ekle
   - Düzeltmeleri uygula
   - Çelişen bilgileri güncelle (silme, düzelt)

3️⃣ **Hiçbir şeyi SİLME:**
   - v1'de söylenen ama v5'te tekrar edilmeyen → SİLME, KORU!
   - Eski senaryolar → KORU!
   - Eski kararlar → KORU (güncellenmediyse)
   - Eski örnekler → KORU!

**Örnek:**

❌ **YANLIŞ (Bilgi Kaybı):**
```
v1: A, B, C senaryoları anlatıldı
v2: Kullanıcı D ekledi → Sadece D yazıldı (A, B, C kayboldu!)
v3: Kullanıcı E ekledi → Sadece E yazıldı (A, B, C, D kayboldu!)
```

✅ **DOĞRU (Birikimli):**
```
v1: A, B, C senaryoları
v2: A, B, C + D (hepsi var)
v3: A, B, C, D + E (hepsi var)
v10: A, B, C, D, E, F, G, H, I, J (v1'den beri HEPSİ var)
```

**❌ ASLA YAPMA:**
- Önceki versiyonu okumadan yeni versiyon oluşturma
- Sadece son söyleneni yazma (öncekiler kaybolur!)
- Eski bilgileri "zaten biliyoruz" diye atlama
- "Özet" yapma, DETAYLI yaz!
- v1'deki senaryoları v5'te unutma!

**✅ MUTLAKA YAP:**
- Yeni versiyon öncesi: Mevcut en son versiyonu MUTLAKA oku
- Tüm eski içeriği yeni versiyona KOPYALA
- Yeni bilgileri üstüne EKLE
- Çelişen/değişen bilgileri GÜNCELLE (silme, düzelt)
- Konuşmanın BAŞINDAN BERİ söylenen her şey son versiyonda olmalı

**🎯 AMAÇ:**
- Son versiyon = Tüm konuşmanın kapsamlı özeti
- v10'u okuyan biri v1-v9'u okumaya gerek duymamalı
- Hiçbir bilgi, senaryo, karar, örnek kaybolmamalı!

---

### 🎉 3B. GÖREV TAMAMLANDI RAPORU

**🎯 KRİTİK: Görev bittiğinde → "Yapılanlar" HTML raporu oluştur!**

**Tetikleyiciler:** bitti, oldu, tamam, aferin, bravo, güzel, teşekkürler, yeterli

**Dosya:** `public/readme/[YYYY]/[MM]/[DD]/task-completed-[konu]/index.html`

**Tasarım:** Yeşil tema, success badge, Yapılanlar + Sonuçlar (kod bloğu YOK!)

**Fark:** Plan HTML → "Ne yapılacak?" | Tamamlandı HTML → "Ne yapıldı?"

---

### 📝 3C. KONUŞMA BAŞLANGIÇ VE KONU DEĞİŞİKLİĞİ RAPORLARI

**🎯 KRİTİK: Her konuşmanın kaydını tutmak için otomatik rapor oluştur!**

#### 🚀 NE ZAMAN RAPOR OLUŞTUR:

**1️⃣ KONUŞMANIN İLK MESAJI (Her Zaman):**
- Kullanıcı yeni konuşmaya ilk mesajı attığında
- **MUTLAKA** planlama raporu oluştur
- Konu: Kullanıcının isteği
- İçerik: Ne yapılacak, hangi dosyalar etkilenecek, adımlar

**2️⃣ KONU DEĞİŞİKLİĞİ (Farklı İş):**
- Bir iş tamamen bittikten sonra
- Kullanıcı **çok farklı** bir konuya geçiyorsa
- Önceki işle **hiç ilgisi olmayan** yeni istek

**Örnek Senaryolar:**

**İlk Mesaj:**
```
Kullanıcı: "Müzik çalarını düzelt"
Claude:
  1. Önce planlama HTML'i oluştur (session-start-muzik-calar-duzeltme)
  2. Kullanıcıya link ver
  3. İşe başla
```

**Konu Değişikliği:**
```
Kullanıcı: "Müzik çalar tamam, şimdi ödeme sistemini incele"
Claude:
  1. Önceki konu (müzik çalar) → Tamamlandı raporu oluştur
  2. Yeni konu (ödeme sistemi) → Planlama HTML'i oluştur
  3. Kullanıcıya linkleri ver
  4. Yeni işe başla
```

**Konu Değişikliği DEĞİL (Aynı İşin Devamı):**
```
Kullanıcı: "Müzik çalarda şarkı değiştirme de çalışmıyor"
Claude:
  → YENİ RAPOR OLUŞTURMA! Aynı konunun devamı.
  → Direkt işe devam et
```

#### 📂 DOSYA YAPISI:

**İlk Mesaj Raporu:**
```
public/readme/[YYYY]/[MM]/[DD]/session-start-[konu]/v1/index.html
```

**Konu Değişikliği Raporu:**
```
public/readme/[YYYY]/[MM]/[DD]/topic-change-[yeni-konu]/v1/index.html
```

#### 📋 İÇERİK:

**Planlama Raporu İçermeli:**
- 📝 **Basit Anlatım:** Ne isteniyor? (Günlük Türkçe)
- 🔧 **Teknik Detaylar:** Hangi dosyalar etkilenecek?
- 📊 **Yapılacaklar:** Adım adım plan
- ⚠️ **Riskler:** Dikkat edilecek noktalar
- 🎯 **Beklenen Sonuç:** İş bitince ne olacak?

**Tasarım:**
- Mavi tema (planlama)
- "Planlama" badge
- İki seviyeli içerik (basit + teknik)
- Kod bloğu YOK! (sadece dosya path'leri)

#### 🎨 ÖRNEK:

```html
<!DOCTYPE html>
<html lang="tr">
<head>
    <title>Konuşma Başlangıç: Müzik Çalar Düzeltme</title>
</head>
<body class="bg-slate-900 text-white">
    <header>
        <span class="badge">📋 Planlama</span>
        <h1>Müzik Çalar Düzeltme - Planlama</h1>
        <p>24 Aralık 2025 - 15:30</p>
    </header>

    <!-- Basit Anlatım -->
    <div class="bg-green-900/20">
        <h3>📝 Basit Anlatım</h3>
        <p>Müzik çalarda şarkı geçişi ve ses kontrolü çalışmıyor.
           Bu sorunları düzelteceğiz.</p>
    </div>

    <!-- Teknik Detaylar -->
    <div class="bg-blue-900/20">
        <h3>🔧 Teknik Detaylar</h3>
        <ul>
            <li>public/themes/muzibu/js/player/core/player-core.js</li>
            <li>public/themes/muzibu/js/player/features/controls.js</li>
        </ul>
    </div>

    <!-- Yapılacaklar -->
    <div>
        <h3>📊 Yapılacaklar</h3>
        <ol>
            <li>Player core dosyasını incele</li>
            <li>Şarkı geçiş fonksiyonunu düzelt</li>
            <li>Ses kontrolünü test et</li>
            <li>Cache temizle ve production build</li>
        </ol>
    </div>
</body>
</html>
```

#### 📎 Kullanıcıya Bildir:

**İlk Mesaj:**
```
✅ Konuşma kaydı oluşturuldu!
📋 Planlama: https://ixtif.com/readme/2025/12/24/session-start-muzik-calar-duzeltme/

Şimdi işe başlıyorum...
```

**Konu Değişikliği:**
```
✅ Önceki konu tamamlandı!
📊 Tamamlanan: https://ixtif.com/readme/2025/12/24/task-completed-muzik-calar/

✅ Yeni konu için planlama hazır!
📋 Planlama: https://ixtif.com/readme/2025/12/24/topic-change-odeme-sistemi/

Yeni konuya geçiyorum...
```

#### ⚠️ DİKKAT:

**RAPOR OLUŞTUR:**
- ✅ Yeni konuşmanın ilk mesajı
- ✅ Tamamen farklı konu (müzik → ödeme)
- ✅ Farklı modül (Blog → Shop)

**RAPOR OLUŞTURMA:**
- ❌ Aynı konunun devamı (şarkı geçiş → ses kontrolü)
- ❌ Küçük değişiklikler (CSS düzeltme → text değişikliği)
- ❌ Aynı modülde farklı sayfa (Blog liste → Blog detay)

**AMAÇ:** Her konuşmayı ve major değişiklikleri görelim, ama spam yapmayalım!

---

### 📍 3D. README INDEX/MAP SAYFASI (Otomatik Rapor Listesi)

**🎯 KRİTİK: `tenant-adi.com/readme` → Tüm raporların otomatik dashboard'u!**

#### 📋 Amaç:

Kullanıcı `https://ixtif.com/readme/` veya `https://muzibu.com.tr/readme/` adresine gittiğinde:
- Tüm HTML raporlarını görsün
- Tarih sırasıyla (en yeni en üstte)
- Versiyonları görsün (v1, v2, v3...)
- Son güncelleme tarihini görsün
- Başlıklara tıklayıp rapora gitsin
- Otomatik olarak yeni raporlar listelensin

#### 📂 Dosya Konumu:

```
public/readme/index.php
```

**URL:**
```
https://ixtif.com/readme/
https://muzibu.com.tr/readme/
```

#### 🎨 README Index Mantığı:

**PHP Backend:**
- `scanReports()` fonksiyonu: YYYY/MM/DD/konu klasörlerini tarar
- `glob()` ile yıl/ay/gün/konu/versiyon klasörlerini bul
- Her versiyonun `index.html` dosyasını kontrol et
- HTML'den başlık çek (`<title>` veya `<h1>`)
- Versiyonları modification time'a göre sırala (en yeni en üstte)
- Tüm raporları `latestModified` bazında sırala

**Frontend Görünüm:**
- **Minimal Header:** Başlık + domain + istatistikler (rapor/versiyon sayısı)
- **Masonry Layout:** `columns-1 sm:columns-2 lg:columns-3 xl:columns-4`
- **Küçük Kartlar:** Kompakt tasarım, hover efekti
- **Versiyon Badge'leri:** İlk 5 versiyon, en yeni yeşil (✨)
- **Auto Refresh:** 60 saniyede bir reload (scroll korunur)

**Dosya:** `public/readme/index.php`

**UNUTMA:**
- Otomatik tarama: Klasörleri sürekli tarar, yeni raporları gösterir
- Permission: 644 dosya, 755 klasör, tuufi.com_:psaserv owner
- Her tenant ayrı index (ixtif.com/readme/, muzibu.com/readme/)

---

### 🚨 5. MARKDOWN (MD) KULLANIMI

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

### 🚨 6. GIT CHECKPOINT KURALLARI

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

### 🚨 7. DOSYA İZİNLERİ (PERMİSSİON) - STANDART WORKFLOW

**🔴 ANA KURAL: ROOT YASAK! → HER ZAMAN tuufi.com_ KULLAN!**

#### 📋 STANDART WORKFLOW (Her Dosya İşleminde Uygula)

**1. Klasör oluştur:**
```bash
sudo -u tuufi.com_ mkdir -p /path/to/directory/
```

**2. Write/Edit tool kullandıysan (root:root oluşturur, düzelt!):**
```bash
sudo chown tuufi.com_:psaserv /path/to/file
sudo chmod 644 /path/to/file  # Dosya
sudo chmod 755 /path/to/dir/  # Klasör
curl -s -k https://ixtif.com/opcache-reset.php > /dev/null  # PHP için
```

**3. HTML rapor oluşturduysan (ZORUNLU 403 KONTROLÜ!):**
```bash
# 1. İzinleri düzelt
sudo chown tuufi.com_:psaserv /path/index.html
sudo chmod 644 /path/index.html

# 2. ZORUNLU TEST - 200 OK ALMADAN LİNK VERME!
curl -s -k -I https://domain.com/path/ | head -n 1
# Beklenen: HTTP/2 200

# 3. 403 hatası alırsan → DURMA, TOPLU DÜZELT:
sudo chown -R tuufi.com_:psaserv /path/
sudo find /path/ -type f -exec chmod 644 {} \;
sudo find /path/ -type d -exec chmod 755 {} \;

# 4. TEKNİK DÜZELT VE TEKRAR TEST ET!
curl -s -k -I https://domain.com/path/ | head -n 1
# 200 OK gelene kadar devam et!
```

**🔴 403 HATA PROTOKOLÜ (ZORUNLU!):**

1. **Write/Edit tool kullandın** → root:root oluşturur → **HEMEN chown yap!**
2. **Link vermeden ÖNCE** → curl ile test et → **200 OK görmeden link VERME!**
3. **403 aldın mı?** → Kullanıcıya hata gösterme → **Önce düzelt, sonra link ver!**
4. **Symlink oluşturdun mu?** → `sudo -u tuufi.com_` ile oluştur, root ile DEĞİL!

**⚠️ KRİTİK:**
- ❌ **200 OK almadan link verme! YASAK!**
- ❌ Root ownership → Nginx okuyamaz → 403 hatası!
- ❌ Kullanıcıya 403 gösteren link verme!
- ✅ **Doğru izinler:** tuufi.com_:psaserv, 644 (dosya), 755 (klasör)
- ✅ **Symlink:** `sudo -u tuufi.com_ ln -sf` kullan

---

### 🚨 8. ANA DİZİN TEMİZ KALMALI

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

### 🚨 9. BUFFER DOSYALARI (a-console.txt, a-html.txt)

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
# 1. Cache temizle (Redis dahil)
php artisan cache:clear
php artisan view:clear
php artisan responsecache:clear

# 2. Build
npm run prod
```

**Otomatik yap, onay bekleme!**

### ☢️ NUCLEAR CACHE CLEAR

**Kullanıcı "değişiklikler yansımadı" derse:**

```bash
# Laravel cache + Redis cache temizler (Session'ları KORUR!)
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

**✅ GÜVENLİ:**
- `php artisan cache:clear` → Redis cache'ini temizler ama session'lara dokunmaz
- Kullanıcılar logout OLMAZ
- Queue job'lar korunur

**❌ ASLA KULLANMA:**
- `redis-cli FLUSHALL` → TÜM kullanıcıları logout yapar!
- `redis-cli FLUSHDB` → Session'ları silme riski!
- Redis'i manuel temizleme → Laravel'a bırak!

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

**Detaylı tenant bilgileri:** "🚨 SİSTEM TENANT AWARE" bölümüne bak!

### 🗄️ MİGRATION OLUŞTURMA

**🚨 KRİTİK: Migration'lar MODÜL İÇİNE eklenir!**

Her migration **KENDİ MODÜLÜ** içinde, **İKİ YERDE** oluşturulmalı:

```
Modules/[ModulAdı]/database/migrations/YYYY_MM_DD_xxx.php           → Central
Modules/[ModulAdı]/database/migrations/tenant/YYYY_MM_DD_xxx.php    → Tenant
```

**Örnek (Page modülü için):**
```
Modules/Page/database/migrations/2024_02_17_000001_create_pages_table.php
Modules/Page/database/migrations/tenant/2024_02_17_000001_create_pages_table.php
```

**❌ YANLIŞ:** `database/migrations/` (ana klasör) - KULLANMA!
**✅ DOĞRU:** `Modules/[Modül]/database/migrations/` (modül içi)

**🔴 KRİTİK HATA - ASLA YAPMA:**
```
❌ Sadece tenant/ klasörüne migration oluşturmak
❌ Central migration'ı unutmak
❌ İkisinden birini yazmayı atlamak
```

**✅ DOĞRU WORKFLOW:**
1. **İLK ÖNCE:** Tenant migration oluştur (`migrations/tenant/YYYY_MM_DD_xxx.php`)
2. **HEMEN ARDINDAN:** Aynı dosyayı central'a kopyala (`migrations/YYYY_MM_DD_xxx.php`)
3. **HER İKİSİNİ DE KONTROL ET:** İki dosya da mevcut mu?
4. **ÇALIŞTIR:** Hem central hem tenant migration'ları

**Migration çalıştır:**
```bash
php artisan migrate --force                    # Central
php artisan tenants:migrate --force            # Tüm tenant'lar
```

**⚠️ UNUTMA:**
- Migration = **MUTLAKA** iki yerde (Central + Tenant)
- Birini unutursan → Database uyumsuz → Sistem çöker!
- Her migration'dan sonra **HER İKİSİNİ DE** çalıştır!

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
