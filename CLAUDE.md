## 🔴 EN KRİTİK KURALLAR - MUTLAKA OKU!

### 🚨 1. TENANT AWARE SİSTEM

**⚠️⚠️⚠️ BU SİSTEM MULTI-TENANT! HER TENANT FARKLI SEKTÖR! ⚠️⚠️⚠️**

Bu sistem yüzlerce farklı sektörden tenant barındırır!

#### ❌ YAPMAMAN GEREKEN:
- **Forklift/Transpalet** → SADECE Tenant 2 (ixtif.com)!
- **Müzik/Muzibu** → SADECE Tenant 1001 (muzibu.com)!
- **E-ticaret** → SADECE ilgili tenant'lar!

**🔥 KRİTİK: Tenant'a özgü içeriği GLOBAL/UNIVERSAL kodlara ASLA ekleme!**

#### 📊 Tenant Bilgisi:
- **Tenant 1 (tuufi.com)**: Central sistem
- **Tenant 2 (ixtif.com)**: Endüstriyel ekipman (forklift, transpalet) - **VARSAYILAN**
- **Tenant 1001 (muzibu.com)**: Müzik platformu
- **Tenant 3+**: Diğer sektörler

**Kod yazarken SOR:**
1. ❓ Bu tenant'a özgü bir özellik mi?
2. ❓ Tüm tenant'lar için mi yoksa sadece biri için mi?
3. ❓ Global kod yazıyorsam, tenant-aware mı?

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
- **Modern & Minimal**: Gereksiz kutu içinde kutu YOK
- **Şık & Profesyonel**: Temiz, okunabilir, göz yormayan
- **Dark Mode**: Koyu arka plan, rahat okuma
- **Türkçe**: Tüm içerik Türkçe
- **Responsive**: Mobil uyumlu
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

#### 📐 Modern HTML Şablonu:

```html
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>[İşlem Adı] - Analiz & Plan</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #e2e8f0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            line-height: 1.7;
            padding: 40px 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        header {
            margin-bottom: 50px;
            padding-bottom: 30px;
            border-bottom: 2px solid #334155;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #60a5fa, #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .meta {
            color: #94a3b8;
            font-size: 0.95rem;
        }

        section {
            margin-bottom: 40px;
        }

        h2 {
            font-size: 1.8rem;
            margin-bottom: 25px;
            color: #60a5fa;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .plan-item {
            background: rgba(30, 41, 59, 0.5);
            padding: 25px;
            margin-bottom: 15px;
            border-radius: 12px;
            border-left: 4px solid #3b82f6;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .plan-item:hover {
            transform: translateX(5px);
            background: rgba(30, 41, 59, 0.7);
        }

        .plan-item h3 {
            color: #60a5fa;
            margin-bottom: 12px;
            font-size: 1.3rem;
        }

        .plan-item p {
            color: #cbd5e1;
            line-height: 1.8;
        }

        .tech-term {
            color: #fbbf24;
            font-weight: 500;
        }

        .explanation {
            display: inline-block;
            margin-left: 5px;
            color: #94a3b8;
            font-size: 0.9rem;
        }

        .priority {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-left: 10px;
        }

        .priority-high { background: #dc2626; color: white; }
        .priority-medium { background: #f59e0b; color: white; }
        .priority-low { background: #10b981; color: white; }

        footer {
            margin-top: 60px;
            padding-top: 30px;
            border-top: 1px solid #334155;
            color: #64748b;
            font-size: 0.9rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>📊 [İşlem Adı]</h1>
            <div class="meta">
                📅 Tarih: [YYYY-MM-DD HH:MM] |
                🎯 Tenant: [ixtif.com] |
                👤 Talep: [Kullanıcı talebi özeti]
            </div>
        </header>

        <section>
            <h2>🎯 Yapılacaklar</h2>

            <div class="plan-item">
                <h3>1. [İşlem Başlığı] <span class="priority priority-high">Yüksek Öncelik</span></h3>
                <p>
                    <span class="tech-term">SEO</span>
                    <span class="explanation">(Arama motoru optimizasyonu)</span>
                    için meta taglerini güncelleyeceğiz. Bu sayede Google'da daha iyi sıralama elde edilecek.
                </p>
                <p><strong>Beklenen Sonuç:</strong> Arama motorlarında görünürlük artışı</p>
            </div>

            <div class="plan-item">
                <h3>2. [İşlem Başlığı] <span class="priority priority-medium">Orta Öncelik</span></h3>
                <p>Açıklama...</p>
            </div>
        </section>

        <footer>
            🤖 Claude AI tarafından oluşturuldu
        </footer>
    </div>
</body>
</html>
```

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
