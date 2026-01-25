# CLAUDE.md - Çalışma Kuralları

**Sormaktan çekinme. Netlik için AskUserQuestion tool kullan.**

---

## BÖLÜM 1: CLAUDE ÇALIŞMA KURALLARI

### 1.1 DOSYA İZİNLERİ (Her Dosya İşleminde!)

**Write/Edit tool kullandıktan sonra MUTLAKA:**
```bash
sudo chown tuufi.com_:psaserv /path/to/file
sudo chmod 644 /path/to/file  # Dosya
sudo chmod 755 /path/to/dir/  # Klasör
```

**HTML rapor oluşturduysan:**
```bash
# 1. İzinleri düzelt
sudo chown tuufi.com_:psaserv /path/index.html
sudo chmod 644 /path/index.html

# 2. 200 OK testi (ZORUNLU!)
curl -s -k -I https://domain.com/path/ | head -n 1

# 3. 403 alırsan toplu düzelt:
sudo chown -R tuufi.com_:psaserv /path/
sudo find /path/ -type f -exec chmod 644 {} \;
sudo find /path/ -type d -exec chmod 755 {} \;
```

**Kurallar:**
- ❌ 200 OK almadan link verme!
- ✅ Symlink: `sudo -u tuufi.com_ ln -sf` kullan

---

### 1.2 GIT CHECKPOINT

**Ne Zaman Yap:** Büyük refactor, çok dosya değişikliği, riskli işlem
**Ne Zaman YAPMA:** Küçük bug fix, tek dosya, typo, CSS değişikliği

```bash
git add .
git commit -m "🔧 CHECKPOINT: Before [özet]"
```

**git reset --hard için KULLANICI İZNİ gerekli!**

---

### 1.3 ANA DİZİN TEMİZLİĞİ

**Ana dizine ASLA:** test-*.php, debug-*.txt, setup-*.php, fix-*.php
**Doğru konum:** readme/, /tmp/, tests/
**İstisnalar:** CLAUDE.md, README.md, .env, composer.json

Kullanıcı görsel attıysa → İş bitince sil, ana dizin temiz kalsın.

---

### 1.4 CACHE & BUILD

**Tailwind/View değişikliğinden sonra otomatik:**
```bash
php artisan cache:clear && php artisan view:clear && php artisan responsecache:clear
npm run prod
```

**"Değişiklik yansımadı" denirse (Nuclear Cache):**
```bash
php artisan cache:clear && php artisan config:clear && php artisan route:clear && \
php artisan view:clear && php artisan responsecache:clear && \
curl -s -k https://muzibu.com/opcache-reset.php && \
php artisan config:cache && php artisan route:cache
```

**❌ ASLA:** `redis-cli FLUSHALL` (kullanıcıları logout yapar!)

---

### 1.5 BUFFER DOSYALARI

**a-console.txt, a-html.txt → ASLA silme!**
- Pasif mod: Kullanıcı bahsetmezse dokunma
- Aktif mod: "console" veya "debug" derse takip et

---

## BÖLÜM 2: HTML RAPOR SİSTEMİ

### 2.1 TETİKLEYİCİ KELİMELER

**HTML Oluştur (bu kelimeler geçerse):**
- analiz, incele, araştır, değerlendir, kontrol et
- rapor, dokümante et, belge oluştur
- plan, tasarım, strateji, taslak, yol haritası
- sunum, göster, özetle, özet hazırla
- detaylı, kapsamlı, gözat, tara, keşfet
- karşılaştır, kıyasla, listele, grupla

**HTML Oluşturma (direkt kod yaz):**
- düzelt, fix et, ekle, sil, değiştir, güncelle, migration yap

---

### 2.2 DOSYA KONUMU & VERSİYON

**Konum:** `public/readme/YYYY/MM/DD/konu/v1/index.html`

**Versiyon Mantığı:**
- İlk rapor → v1/index.html
- Güncelleme → v2, v3... (eski silinmez!)
- Ana klasörde index.html → En güncel versiyonun KOPYASI (symlink değil!)

**Örnek:**
```
public/readme/2026/01/14/blog-analiz/
├── v1/index.html   ← İlk
├── v2/index.html   ← Güncelleme
└── index.html      ← v2'nin kopyası
```

**Link verirken:** `https://muzibu.com/readme/2026/01/14/blog-analiz/`

---

### 2.3 İKİ SEVİYELİ İÇERİK (ZORUNLU!)

Her raporda iki bölüm olmalı:

**📝 Basit Anlatım (Herkes İçin):**
- Günlük Türkçe, teknik terim yok
- Teknik terim varsa parantez içinde açıkla
- "Neden önemli?" sorusunun cevabı

**🔧 Teknik Detaylar (Geliştiriciler İçin):**
- Dosya path'leri
- Fonksiyon/class isimleri
- Veritabanı tablo/field
- Kullanılan teknolojiler

---

### 2.4 BİRİKİMLİ VERSİYON (ÇOK KRİTİK!)

**Kural:** Her yeni versiyon = Önceki TÜM bilgiler + Yeni eklemeler

```
v1: A, B, C
v2: A, B, C + D (hepsi var!)
v3: A, B, C, D + E (hepsi var!)
```

**❌ YANLIŞ:** v2'de sadece D yazmak (A, B, C kaybolur!)
**✅ DOĞRU:** v3'ü okuyan biri v1-v2'yi okumak zorunda kalmamalı

**Yeni versiyon öncesi:** Mevcut son versiyonu OKU, tüm içeriği KOPYALA, yeni bilgileri EKLE

---

### 2.5 HTML TASARIM

**Tasarım:** Tailwind CDN, dark mode (slate), minimal, responsive, Türkçe

**Yapı:** Header → İçerik (kartlar) → Footer

**🚨 FOOTER KURALI:**
- ❌ "Claude AI tarafından oluşturuldu" YAZMA!
- ❌ "Claude AI", "AI tarafından", "Yapay zeka" YASAK!
- ✅ Sadece: "14 Ocak 2026 • Muzibu.com.tr"

---

### 2.6 GÖREV TAMAMLANDI RAPORU

**Tetikleyiciler:** bitti, tamam, teşekkürler, güzel, yeterli
**Dosya:** `task-completed-[konu]/index.html`
**Tasarım:** Yeşil tema, "Ne yapıldı?" içeriği

---

### 2.7 KONUŞMA RAPORLARI

**İlk mesaj:** `session-start-[konu]/` planlama raporu oluştur
**Konu değişikliği:** Önceki tamamlandı + yeni planlama raporu
**Aynı konunun devamı:** Rapor oluşturma, devam et

---

### 2.8 MD KULLANIMI

**MD = Sadece TODO!**

Tetikleyiciler: "todo", "checklist", "yapılacaklar listesi"
Konum: `readme/claude-docs/todo/YYYY/MM/DD/todo-HH-MM-konu.md`

**Diğer her şey → HTML!**

---

## BÖLÜM 3: GÜVENLİK & KORUMA

### 3.1 YASAK KOMUTLAR

**🚨 ASLA ÇALIŞTIRMA:**
```bash
❌ php artisan media-library:clear     # 268 medya sildi!
❌ php artisan db:wipe
❌ php artisan migrate:fresh
❌ php artisan tenants:migrate-fresh
❌ rm -rf storage/
❌ rm -rf storage/app/public/
```

**✅ GÜVENLİ:**
```bash
✅ php artisan cache:clear
✅ php artisan config:clear
✅ php artisan route:clear
✅ php artisan view:clear
✅ php artisan responsecache:clear
```

---

### 3.2 VERİTABANI KORUMA (EN KRİTİK BÖLÜM!)

**🚨🚨🚨 BU CANLI SİSTEM! VERİTABANI DEĞİŞİKLİĞİ = FELAKET RİSKİ! 🚨🚨🚨**

**❌ ASLA (KULLANICI İSTESE BİLE UYARI VER):**
- migrate:fresh, db:wipe, truncate, DELETE, DROP

**🛑 MUTLAK YASAK - KENDİ BAŞINA ASLA YAPMA:**
```
❌ Migration dosyası oluşturma
❌ Tabloya kolon ekleme
❌ Tablodan kolon silme
❌ Yeni tablo oluşturma
❌ Tablo silme
❌ Kolon tipini değiştirme
❌ Index ekleme/silme
❌ Foreign key ekleme/silme
❌ php artisan migrate çalıştırma
❌ php artisan tenants:migrate çalıştırma
```

**⚠️ ÇOKLU ONAY GEREKLİ (3 AŞAMALI):**

Migration gerektiren bir iş için şu adımları takip et:

**AŞAMA 1 - İLK ONAY:**
```
"Bu işlem için migration gerekiyor. Migration şunları yapacak:
- [Tablo adı]: [Yapılacak değişiklik]
Migration oluşturmamı onaylıyor musunuz?"
```

**AŞAMA 2 - DOSYA OLUŞTURMA ONAYI:**
```
"Migration dosyası şu içerikle oluşturulacak:
[Migration içeriği göster]
Bu dosyayı oluşturmamı onaylıyor musunuz?"
```

**AŞAMA 3 - ÇALIŞTIRMA ONAYI:**
```
"Migration dosyası oluşturuldu. Şimdi çalıştırmamı istiyor musunuz?
⚠️ DİKKAT: Bu işlem geri alınamaz!
php artisan migrate --force (Central)
php artisan tenants:migrate --force (Tenant'lar)"
```

**❌ YANLIŞ DAVRANIŞLAR:**
- Kullanıcı "şu alanı ekle" dediğinde direkt migration oluşturmak
- "Tamam" cevabını 3 aşamanın hepsi için geçerli saymak
- Migration'ı oluşturup otomatik çalıştırmak
- "Küçük bir değişiklik" diye onaysız yapmak

**✅ DOĞRU DAVRANIŞLAR:**
- Her aşama için AYRI onay almak
- Onay almadan ASLA migration dosyası oluşturmamak
- Onay almadan ASLA migrate komutu çalıştırmamak
- Kullanıcıya riskleri açıkça anlatmak

---

### 3.3 SİLME İŞLEMLERİ İÇİN ONAY (KRİTİK!)

**🚨 HER SİLME İŞLEMİNDEN ÖNCE KULLANICI ONAYI ZORUNLU!**

**❌ ASLA kendin karar verme, MUTLAKA sor:**
```bash
❌ DELETE FROM users WHERE ...
❌ DROP TABLE ...
❌ TRUNCATE ...
❌ rm -rf storage/...
❌ unlink() / File::delete()
❌ Media::delete() / $media->delete()
```

**⚠️ ÖZELLİKLE DİKKAT:**
- 📷 Görseller (storage/app/public/, media tablosu)
- 🗄️ Veritabanı tabloları (DROP, TRUNCATE, DELETE)
- 📁 Storage dosyaları (avatarlar, kapaklar, yüklemeler)
- 👥 Kullanıcı verileri
- 💳 Ödeme kayıtları
- 📝 İçerik kayıtları (şarkılar, albümler, vs.)

**✅ DOĞRU YÖNTEM:**
1. Silmek istediğin şeyi kullanıcıya açıkla
2. "Bu işlem X adet kayıt silecek, onaylıyor musunuz?" diye sor
3. Onay aldıktan SONRA işlemi yap
4. İşlem sonrası rapor ver

**Örnek:**
```
❌ YANLIŞ: Direkt DELETE FROM users WHERE id > 2 komutu çalıştırmak
✅ DOĞRU: "1,496 kullanıcı silinecek (ID 3-1565). Onaylıyor musunuz?" diye sormak
```

**İstisna:** Sadece cache temizleme işlemleri onaysız yapılabilir (cache:clear vs.)

---

## BÖLÜM 4: PROJE KURALLARI (TENANT-AWARE)

### 4.1 MULTI-TENANT MİMARİ

**Her tenant bağımsız database:**
| ID | Domain | Database | Sektör |
|----|--------|----------|--------|
| 1 | tuufi.com | tuufi_4ekim | Central |
| 2 | ixtif.com | tenant_ixtif | Endüstriyel |
| 1001 | muzibu.com | tenant_muzibu_1528d0 | Müzik |

**Detaylı bilgi:** `TENANT_LIST.md`

**❌ YAPMA:**
- Forklift/Transpalet kodunu Muzibu'ya ekleme
- Müzik/Album kodunu İxtif'e ekleme
- Central DB'ye tenant verisi yazma

**✅ Tenant kontrolü:**
```php
if (tenant()->id === 1001) {
    // Sadece Muzibu
}
```

---

### 4.2 MİGRATION KURALLARI (MUTLAKA OKU!)

**🚨🚨🚨 MİGRATION = 3 AŞAMALI ONAY GEREKLİ (Bkz: 3.2) 🚨🚨🚨**

**KENDİ BAŞINA MİGRATION OLUŞTURMA!**
**KENDİ BAŞINA KOLON EKLEME!**
**KENDİ BAŞINA TABLO OLUŞTURMA!**

Kullanıcı "X alanı ekle" veya "Y tablosu oluştur" dese bile:
1. Önce AŞAMA 1 onayı al
2. Sonra AŞAMA 2 onayı al
3. Son olarak AŞAMA 3 onayı al

**Migration Dosya Konumları (ONAY ALINDIKTAN SONRA):**

```
Modules/[Modül]/database/migrations/xxx.php           → Central
Modules/[Modül]/database/migrations/tenant/xxx.php   → Tenant
```

**❌ YANLIŞ:** `database/migrations/` ana klasör
**✅ DOĞRU:** Modül içinde, hem central hem tenant

**Çalıştır (SADECE AŞAMA 3 ONAYI ALINDIKTAN SONRA):**
```bash
php artisan migrate --force              # Central
php artisan tenants:migrate --force      # Tenant'lar
```

**⚠️ HATIRLATMA:**
- "Tamam" = Sadece o aşamanın onayı, diğerleri için tekrar sor
- Migration içeriğini GÖSTERMEDEN dosya oluşturma
- Dosya oluşturduktan sonra SORMADAN migrate çalıştırma

---

### 4.3 CSS BUILD

Her tenant kendi CSS'i: `public/css/tenant-X.css`

```bash
npm run prod         # Tüm tenant CSS + app.css
npm run css:muzibu   # Sadece tenant-1001
```

Tailwind class eklediysen → safelist'e ekle → npm run prod

---

### 4.4 PATTERN UYUMU

**Yeni dosya oluştururken mevcut dosyalardan örnek al!**

**Referanslar:**
- Tablo: `Modules/Page/.../page-component.blade.php`
- Form: `page-manage-component.blade.php`
- Sıralama: `category-component.blade.php`

---

## BÖLÜM 5: TASARIM & SİSTEM

### 5.1 TASARIM STANDARTLARI

- **Admin:** Tabler.io + Bootstrap + Livewire
- **Frontend:** Alpine.js + Tailwind CSS
- **Icon:** SADECE FontAwesome (fas, far, fab)

**Admin Pattern:**
- `index.blade.php` → Liste
- `manage.blade.php` → Create/Edit

**Tasarımsal değişiklik:** Önce HTML taslak göster, "UYGUNDUR" al, sonra kodla

**🚨 TEMA DOSYALARI KONUMU:**
```
✅ DOĞRU: resources/views/themes/t-{id}/
❌ YANLIŞ: Modules/*/resources/views/themes/
```

- Tema dosyaları (homepage, header, footer, layouts) SADECE `resources/views/themes/` altında
- Modules içine tema dosyası AÇMA (kullanıcı özellikle istemediği sürece)
- Header/Footer tek dosya olmalı, tüm sayfalar `@include` ile kullanmalı
- Homepage dahil hiçbir sayfa inline header/footer içermemeli

---

### 5.2 SİSTEM BİLGİLERİ

**Settings:** `setting('site_name')`
**Thumbmaker:** `thumb($media, 400, 300)`
**Dil:** Admin = system_languages, Site = site_languages

---

### 5.3 PERFORMANS

**❌ YAPMA:**
- Horizon auto-restart cron ile
- maxProcesses agresif (8 değil 2)
- exec(...&) ile background process

**Detay:** `https://ixtif.com/readme/2025/11/30/horizon-cpu-sorunu-analiz/`

---

## HATIRLATMALAR

- 🎯 Analiz/Rapor → HTML (kod yok!)
- 📝 TODO → MD
- 🔐 Büyük işlem → Git checkpoint
- 🗑️ İş bitti → Temizlik
- 📁 Her dosya → Permission düzelt
- 🚫 Footer'da "Claude AI" yazma!
- 🚨 **MİGRATION = 3 AŞAMALI ONAY! Kafana göre tablo/kolon ekleme!**
- 🔤 **TÜRKÇE KARAKTER ZORUNLU! ASCII Türkçe YASAK!**

---

## BÖLÜM 6: TÜRKÇE KARAKTER KURALI (KRİTİK!)

### 6.1 MUTLAK KURAL

**🚨 TÜM içeriklerde DOĞRU Türkçe karakterler kullanılmalı!**

**Türkçe Karakterler:** ş, Ş, ğ, Ğ, ü, Ü, ö, Ö, ç, Ç, ı, I, i, İ

**❌ YANLIŞ (ASCII Türkçe) - ASLA KULLANMA:**
```
Olusturma → Oluşturma
Kilavuz   → Kılavuz
Ozellik   → Özellik
Icerik    → İçerik
Calisma   → Çalışma
Islem     → İşlem
Uretim    → Üretim
Gorunum   → Görünüm
Surec     → Süreç
Dokuman   → Doküman
```

**✅ DOĞRU (UTF-8 Türkçe) - HER ZAMAN KULLAN:**
- Oluşturma, Kılavuz, Özellik, İçerik
- Çalışma, İşlem, Üretim, Görünüm
- Süreç, Doküman, Değişiklik, Bağımsız

### 6.2 NEREDE UYGULANIR?

| Alan | Örnek |
|------|-------|
| HTML/Blade dosyaları | `<h1>Hakkımızda</h1>` |
| Veritabanı içerikleri | `INSERT INTO pages (title) VALUES ('İletişim')` |
| Dokümantasyon | README, CLAUDE.md, HTML raporları |
| Kod yorumları | `// Oluşturma işlemi` |
| Commit mesajları | `🔧 Türkçe karakter düzeltmeleri` |
| Settings değerleri | `site_name = 'Örnek Şirket'` |

### 6.3 KONTROL LİSTESİ

Her dosya oluştururken/düzenlerken şunları kontrol et:

- [ ] "olustur" yerine "oluştur" mu?
- [ ] "icerik" yerine "içerik" mi?
- [ ] "ozellik" yerine "özellik" mi?
- [ ] "calisma" yerine "çalışma" mı?
- [ ] Büyük İ doğru mu? (I değil İ)
- [ ] Küçük ı doğru mu? (i değil ı)
