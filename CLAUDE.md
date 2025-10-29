## ⛔ KRİTİK UYARILAR - MUTLAKA OKU!

### 🚨 VERİTABANI KORUMA KURALLARI

**BU GERÇEK CANLI SİSTEMDİR!**

#### ❌ KESİNLİKLE YAPMA:
1. **`php artisan migrate:fresh`** - ASLA!
2. **`php artisan migrate:fresh --seed`** - ASLA!
3. **`php artisan db:wipe`** - ASLA!
4. **Veritabanı tablosunu truncate** - ASLA!
5. **Manuel SQL DELETE/DROP komutları** - ASLA!
6. **Tenant database silme** - ASLA!
7. **Sunucuda ayarlarıyla bir işlem için defalarca sor, sunucu ayarlarını rastgele değiştirme** 
8. **Sunucuyu apacheyi restart kafana göre yapma. Özellikle onaylar iste. Gerekmedikçe de yapma.**

#### ⚠️ KULLANICI İZNİ GEREKIR:
- **Veritabanına INSERT/UPDATE**: Önce kullanıcıya sor, onay al
- **Mevcut kayıtları değiştirme**: Önce kullanıcıya sor, onay al
- **Migration dosyası oluşturma**: Önce kullanıcıya sor, içeriğini göster

#### ✅ SERBEST İŞLEMLER:
- Kod okuma, analiz yapma
- SELECT sorguları (readonly)
- Log dosyalarını okuma
- Config dosyalarını okuma
- Test ortamında çalışma (eğer varsa)

---

**UNUTMA:** Eğer bir işlem "veritabanındaki mevcut verileri etkileyecekse" → **ÖNCE KULLANICIYA SOR!**

---

### 🎨 RENK KONTRAST KURALLARI

**⚠️ KRİTİK: WCAG AA STANDARDI ZORUNLU**

Her renk seçiminde **kontrast oranı minimum 4.5:1** olmalı!

#### ❌ ASLA YAPMA:
- **Mavi üstüne mavi text** (bg-blue-600 + text-blue-700)
- **Koyu üstüne koyu** (bg-gray-800 + text-gray-700)
- **Açık üstüne açık** (bg-white + text-gray-100)
- **Transparan üstüne aynı renk** (bg-blue-500/50 + text-blue-600)

#### ✅ DOĞRU KONTRAST ÖRNEKLERİ:

**Light Mode:**
- `bg-white` → `text-gray-900` (koyu siyah)
- `bg-gray-50` → `text-gray-900` (koyu siyah)
- `bg-blue-600` → `text-white` (beyaz)
- `bg-blue-500` → `text-white` (beyaz)
- `bg-gray-100` → `text-gray-900` (koyu siyah)

**Dark Mode:**
- `dark:bg-gray-900` → `dark:text-white` (beyaz)
- `dark:bg-gray-800` → `dark:text-white` (beyaz)
- `dark:bg-blue-600` → `dark:text-white` (beyaz)
- `dark:bg-gray-700` → `dark:text-gray-100` (açık gri)

#### 📋 KONTRAST KONTROL ADIMLARI:

**Her UI elementi oluştururken:**
1. **Arka plan rengini belirle** (bg-* class)
2. **Kontrast text rengi seç:**
   - Koyu bg → Açık text (white, gray-100)
   - Açık bg → Koyu text (gray-900, gray-800)
3. **Hem light hem dark mode kontrol et**
4. **Ekran görüntüsü iste veya canlı test yap**

#### 🚨 ÖZEL DURUMLAR:

**Mavi/Renkli Butonlar/Kartlar:**
```html
<!-- ✅ DOĞRU -->
<a href="#" class="bg-blue-600 text-white">
  <h3 class="text-white">Başlık</h3>
  <p class="text-white/90">Açıklama</p>
  <i class="text-white"></i>
</a>

<!-- ❌ YANLIŞ -->
<a href="#" class="bg-blue-600">
  <h3>Başlık</h3> <!-- text-gray-900 inherit olur, okunmaz! -->
  <p class="text-blue-100">Açıklama</p> <!-- Kontrast düşük! -->
</a>
```

**Glassmorphism/Transparan:**
```html
<!-- ✅ DOĞRU: Belirgin arka plan -->
<section class="bg-gray-50/95 dark:bg-gray-800/95">
  <h1 class="text-gray-900 dark:text-white">Başlık</h1>
</section>

<!-- ❌ YANLIŞ: Çok transparan -->
<section class="bg-white/20 dark:bg-white/5">
  <h1 class="text-gray-900">Başlık</h1> <!-- Arka plan görünmez! -->
</section>
```

#### 🔍 TEST ZORUNLULUĞU:

**Kod yazdıktan sonra MUTLAKA:**
1. Light mode screenshot iste → Kontrast kontrol et
2. Dark mode screenshot iste → Kontrast kontrol et
3. Okunmuyorsa → Hemen düzelt
4. Cache clear + Build yap
5. Tekrar test et

**UNUTMA:** Eğer kullanıcı "okunmuyor" derse → **SEN HATA YAPTIN!** Özür dile ve hemen düzelt.

---

# 🤖 CLAUDE ÇALIŞMA TALİMATLARI

**Proje Giriş**: nurullah@nurullah.net / test
**URL**: www.laravel.test/login


işlemler bittikten sonra tamamlandığına dair siri ile seslendir.




---
Standard Workflow

First think through the problem, read the codebase for relevant files, and write a plan to claudeguncel.md

The plan should have a list of todo items that you can check off as you complete them

Before you begin working, check in with me and I will verify the plan

Then, begin working on the todo items, marking them as complete as you go

Please every step of the way just give me a high level explanation of what changes you made

Make every task and code change you do as simple as possible. We want to avoid making any massive or complex changes. Every change should impact as little code as possible. Everything is about simplicity

Finally, add a review section to the projectplan.md file with a summary of the changes you made and any other relevant information


- Önce sorunları iyice düşünün
- Planları `readme/claude-docs/claudeguncel-YYYY-MM-DD-HH-MM-description.md` formatında yaz
- Başlamadan önce giriş yapın
- Yapılacaklar'ı tamamlanmış olarak işaretle
- Değişiklikleri basit tutun

### 📁 DOSYA OLUŞTURMA KURALLARI

**⚠️ ANA DİZİN TEMİZ KALMALI!**

#### 🚨 MUTLAK KURAL: ANA DİZİNE KAFANA GÖRE DOSYA AÇMA!

**Claude, sen dosya açmadan ÖNCE DUR ve düşün:**
1. ❓ Bu dosya gerçekten ana dizinde mi olmalı?
2. ❓ readme/ veya başka klasörde durabilir mi?
3. ❓ Bu geçici bir script/test mi? → O zaman ana dizine değil!

#### ✅ DOĞRU KONUM:
- **Plan/Güncelleme Dökümanları**: `readme/claude-docs/claudeguncel-YYYY-MM-DD-HH-MM-description.md`
- **Teknik Dokümantasyon**: `readme/` klasörü altında (alt klasör oluştur!)
- **Setup Script'leri**: `readme/[özellik-adı]-setup/` klasörü içinde
- **Test Dosyaları**: İlgili modül/klasör içinde veya `tests/` altında
- **Log/Debug**: Geçici ise `/tmp/` altında
- **Tinker Komutları**: `readme/tinker-commands/` veya ilgili dokümantasyon klasöründe

#### ❌ ANA DİZİNE ASLA EKLEME:
- **claudeguncel-*.md** → readme/claude-docs/ içinde olmalı
- **test-*.php** → tests/ veya ilgili modül içinde
- **debug-*.txt** → /tmp/ veya geçici klasör
- **random-*.log** → storage/logs/ içinde
- **setup-*.php** → readme/[feature]-setup/ klasöründe
- **update-*.php** → readme/[feature]-setup/ klasöründe
- **fix-*.php** → readme/[feature]-setup/ klasöründe
- **GUIDE-*.md** → readme/ altında ilgili klasörde
- **TINKER-*.md** → readme/tinker-commands/ veya ilgili klasörde

#### 🎯 İSTİSNALAR (Sadece bunlar ana dizine eklenebilir):
- **Core Laravel config**: tailwind.config.js, webpack.mix.js, vite.config.js
- **Framework dosyaları**: .env.example, .gitignore, composer.json, package.json
- **Ana dokümantasyon**: README.md, CLAUDE.md, SECURITY.md
- **Deployment**: deploy.sh - ama ÖNCE SOR!

#### 🛡️ BUFFER DOSYALARI (DOKUNMA!):
- `a-console.txt` - Console/Debugbar buffer (ana dizinde kalmalı)
- `a-html.txt` - HTML output buffer (ana dizinde kalmalı)

#### 📋 ÖRNEK YOL GÖSTERİCİ:

**YANLIŞ:**
```bash
# ❌ Ana dizine setup script açma!
/var/www/vhosts/tuufi.com/httpdocs/update-seo-layout.php
/var/www/vhosts/tuufi.com/httpdocs/MARKETING-PLATFORMS-TINKER.md
```

**DOĞRU:**
```bash
# ✅ İlgili klasörde oluştur!
/var/www/vhosts/tuufi.com/httpdocs/readme/marketing-setup/update-seo-layout.php
/var/www/vhosts/tuufi.com/httpdocs/readme/marketing-setup/MARKETING-PLATFORMS-TINKER.md
```

**KURALLAR:**
1. **Varsayılan**: Ana dizin değil, alt klasör!
2. **Geçici script**: readme/ altında özel klasör oluştur
3. **Dokümantasyon**: readme/ altında kategorize et
4. **Şüphen varsa**: Kullanıcıya sor: "readme/[klasör]/ altına mı oluşturayım?"

**UNUTMA:** Eğer dosya **core framework dosyası** değilse → **Ana dizine koyma!**

## 📋 ÇALIŞMA YÖNTEMİ

### 🧠 TEMEL YAKLAŞIM
- **Extended Think**: Her mesajı ultra deep analiz et, reasoning yap
- **Türkçe İletişim**: Daima Türkçe yanıt ver
- **Otomatik Devam**: Sorma, direkt hareket et
- **Veritabanı Koruma**: Üstteki kritik uyarılara mutlaka uy!

### 🎨 OTOMATİK CACHE & BUILD (TAİLWİND/FRONTEND)

**⚡ KURAL:** Tailwind/View değişikliğinden SONRA otomatik cache temizle + build compile - **ONAY İSTEME!**

#### ✅ Otomatik Cache+Build Tetikleyicileri:
- **Tailwind class** değişiklikleri (view/blade dosyalarında)
- **CSS/SCSS** dosyası değişiklikleri
- **Frontend asset** değişiklikleri (JS, Alpine.js)
- **Blade/View** dosyası değişiklikleri
- **Layout/Component** değişiklikleri

#### 📋 Otomatik Komutlar (Sırayla):
```bash
# 1. Cache temizliği (SAFE - config cache'i korur)
php artisan view:clear
php artisan responsecache:clear

# 2. Build compile
npm run prod

# 3. Doğrulama
echo "✅ Cache temizlendi, build tamamlandı!"
```

**🚨 KRİTİK UYARI: Config Cache ASLA Temizleme!**
- ❌ **ASLA kullanma**: `php artisan cache:clear` (config cache'i siler, site çöker!)
- ❌ **ASLA kullanma**: `php artisan config:clear` (tek başına sistem bozar!)
- ✅ **Kullan**: `composer config-refresh` (gerekirse, ama nadiren!)
- ✅ **Kullan**: Sadece `view:clear` + `responsecache:clear`

#### ⚠️ KRİTİK:
- **ONAY BEKLEME!** Her view/tailwind değişikliğinde direkt yap
- **Todo'ya ekle**: "🎨 Cache+Build" (kullanıcı takip etsin)
- **Hata varsa bildir**: Build hatası varsa kullanıcıya göster

#### 📝 Todo Örneği:
```markdown
- [x] Navbar responsive düzelt
- [ ] 🎨 Cache temizle + Build compile
- [ ] Test et
```

#### 🚫 İstisna:
- **Sadece PHP logic** değişirse gerekli değil
- **Backend/Controller** değişikliklerinde gerekli değil
- **Sadece txt/md** dosyası değişirse gerekli değil

### ⚡ PRODUCTION CACHE KURALLARI (KRİTİK!)

**🚨 ASLA YAPMA: `php artisan config:clear` TEK BAŞINA!**

**Problem:** Config cache olmadan Laravel her istekte `.env` parse eder → Bir hata olursa site çöker (404)

#### ✅ DOĞRU KULLANIM:

**Composer Script ile (ÖNERİLEN):**
```bash
# Cache yenileme (tek komut)
composer config-refresh

# Production cache oluşturma
composer cache-production
```

**Manuel kullanım (gerekirse):**
```bash
# ❌ ASLA TEK BAŞINA YAPMA:
php artisan config:clear

# ✅ DAIMA BİRLİKTE YAP:
php artisan config:clear && php artisan config:cache

# ✅ TAM CACHE YENİLEME:
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 🛡️ HATIRLATMA:

**Her config değişikliğinden sonra:**
1. `.env` veya `config/*.php` değiştirdiysen
2. `composer config-refresh` veya `php artisan config:cache` yap
3. OPcache varsa reset et: `curl https://domain.com/public/opcache-reset.php`

**Production'da cache ZORUNLU:**
- Config cache yoksa → DB bağlantısı patlayabilir
- Route cache yoksa → Performans düşer
- View cache yoksa → Her istekte Blade compile eder

**UNUTMA:** Cache olmadan production = 💣 bomba!

---

### 🔐 OTOMATİK GIT CHECKPOINT

**⚡ KURAL:** Kullanıcı Claude'u çağırıp ilk talep/brief verdiğinde BİR KEZ checkpoint commit yap - **ONAY İSTEME!**

#### ✅ Checkpoint Zamanlaması:
- **İlk talep geldiğinde**: Kullanıcı "Claude" yazıp ilk brief/talep verdiğinde
- **Sadece BİR KEZ**: O konuşma boyunca tek checkpoint (her mesajda değil!)
- **Geri dönüş noktası**: Tüm değişiklikler bu noktaya göre
- **Basit sorularda YAPMA**: Sadece kod değişikliği gerektiren taleplerde

#### 📋 Otomatik Workflow:
1. **Kullanıcı talep gelir**
2. **İlk checkpoint**: `git add . && git commit -m "🔧 CHECKPOINT: Before [talep özeti]"`
3. **Hash'i kaydet**: Todo'da commit hash'ini yaz
4. **Tüm değişiklikleri yap**: Rahatça çalış
5. **En sonda final commit**: Tüm değişiklikleri içeren asıl commit

#### 🎯 Commit Formatı:
```bash
# Konuşma başında (bir kez)
git add .
git commit -m "🔧 CHECKPOINT: Before [kullanıcı talebinin özeti]"
git log -1 --oneline  # Hash'i al, todo'ya yaz
```

#### 📝 Todo Örneği:
```markdown
- [x] 🔐 Git checkpoint (hash: bed66c0a)
- [ ] Portfolio modülünü refactor et
- [ ] Migration oluştur
- [ ] Cache+Build
- [ ] Test et
- [ ] Final commit yap
```

#### ⚠️ KRİTİK:
- **SADECE BİR KEZ**: Konuşma başında, sonra bir daha yapma!
- **Basit işler için gereksiz**: Tek dosya değişikliği, typo düzeltme
- **Karışıklık yaratma**: Sürekli checkpoint = kötü git history

#### 🔄 Geri Dönüş:

**🚨 KRİTİK UYARI: GIT RESET İÇİN MUTLAKA KULLANICI İZNİ AL!**

```bash
# ❌ ASLA YAPMA - Kullanıcı izni olmadan:
git reset --hard [checkpoint-hash]

# ✅ YAPILACAK İŞLEM:
1. Kullanıcıya SOR: "Git checkpoint'e geri döneyim mi? (hash: XXXXX)"
2. Kullanıcı ONAYLARSA: git reset --hard [checkpoint-hash]
3. ONAYLAMAZSA: Alternatif çözüm bul
```

**NEDEN ÖNEMLİ:**
- Arkaplanda başka işler yapılıyor olabilir
- Commit'ler başka dosyaları da içerebilir
- Hard reset GERİ ALINAMAZ - tüm değişiklikler kaybolur
- Kullanıcı manuel değişiklik yapmış olabilir

**GÜVENLİ ALTERNATİFLER:**
```bash
# Sadece belirli dosyaları geri al
git checkout [checkpoint-hash] -- path/to/file.php

# Veya reflog ile inceleyip sor
git reflog
git show HEAD@{3}  # Önce göster, sonra sor
```

### 📝 ÖNEMLİ NOT
Bu dosya **sadece çalışma yöntemi ve temel talimatları** içerir.
**Detaylı teknik dökümanlar**: `readme/claude-docs/` klasöründe


### 🗑️ DOSYA & VERİTABANI TEMİZLEME

**⚡ KURAL: İş bittikten sonra gereksiz dosya/kayıtları MUTLAKA temizle!**

#### ✅ Otomatik Temizlenmesi Gerekenler:

**Dosya Sistemi:**
- **Log/Fotoğraf**: Oku → Analiz et → Boşalt → Sil
- **Test Sayfaları**: /tmp/ altında veya geçici klasörde oluşturulan test*.html, debug*.php
- **Debug Dosyaları**: Geçici debug script'leri, test komutları
- **Temporary Script'ler**: Sorun çözme için oluşturduğun geçici PHP/Bash dosyaları
- **Yanlış konuma açılan dosyalar**: Ana dizine açılan gereksiz dosyalar

**Veritabanı:**
- **Yanlış kayıtlar**: Test amaçlı eklenen kayıtlar
- **Yanlış DB'ye eklenen kayıtlar**: Farklı tenant'a yanlışlıkla eklenmiş veriler
- **Duplicate kayıtlar**: Hata sonucu oluşan çift kayıtlar
- **Test verileri**: Debug için eklenen dummy data

#### 📋 Temizlik Workflow:

**İş Başında:**
1. Geçici dosya/kayıt oluşturacaksan → Todo'ya "🗑️ Temizlik" ekle
2. Test kayıtları oluşturacaksan → ID'lerini not al

**İş Bitiminde:**
1. Todo'daki "🗑️ Temizlik" maddesini kontrol et
2. Oluşturduğun geçici dosyaları sil
3. Test veritabanı kayıtlarını sil (ÖNCE KULLANICI ONAYINI AL!)
4. Yanlış konumdaki dosyaları doğru yere taşı veya sil
5. Temizlik yaptığını todo'da işaretle

#### ⚠️ KRİTİK:
- **UNUTMA!** Her iş bitişinde temizlik yap
- **Sistemde yer kaplama!** Gereksiz dosya/kayıt bırakma
- **Veritabanı temizliğinde**: MUTLAKA kullanıcı onayı al!
- **Otomatik temizlik** her işlem sonrası

#### 📝 Todo Örneği:
```markdown
- [x] Test sayfası oluştur (/tmp/test-navbar.html)
- [x] Debug script yaz (debug-category.php)
- [x] Navbar sorununu düzelt
- [ ] 🗑️ Geçici dosyaları temizle
```

#### 🚫 Asla Temizleme:
- **Buffer dosyaları**: a-console.txt, a-html.txt (sadece boşalt)
- **Core dosyalar**: CLAUDE.md, README.md, .env
- **Canlı veriler**: Production kayıtları, kullanıcı verileri

### 🛡️ BUFFER DOSYALARI (a-console.txt, a-html.txt)

**⚠️ Bu dosyaları ASLA silme!**
- `a-console.txt` - Console/Debugbar çıktıları için buffer
- `a-html.txt` - HTML output için buffer

**🚨 KRİTİK KURAL: Konuşma BAŞINDA dosya path/anahtar kelime görürsen aktif ol!**

#### 📋 İKİ MOD SİSTEMİ:

**1️⃣ PASİF MOD (Default):**
- Konuşma başında tetikleyici YOK → Hiç dokunma
- Görmezden gel, varsayım yapma
- Sadece kullanıcı açıkça isterse oku

**2️⃣ AKTİF MOD Tetikleyicileri (Konuşma başında):**
Kullanıcı şunları kullanırsa otomatik aktif ol:

**Dosya Path:**
- `a-console.txt` → Console buffer takip et
- `a-html.txt` → HTML buffer takip et

**Anahtar Kelimeler:**
- `console` → a-console.txt takip et
- `debug` → a-console.txt takip et
- `debugbar` → a-console.txt takip et
- `html çıktı` → a-html.txt takip et
- `html output` → a-html.txt takip et

**Aktif Mod Açıldığında:**
- ✅ O konuşma boyunca otomatik takip et
- ✅ Her mesajda ilgili dosyayı oku
- ✅ Değişiklikleri analiz et
- ✅ Sorunları tespit et
- ✅ Todo'da işaretle: "📄 a-console.txt aktif mod ON"

**Her yeni konuşmada sıfırlanır** - Yeniden tetikleyici gerekli

#### ✅ AKTİF MOD Workflow:
```bash
Kullanıcı (Konuşma başında): "Claude, a-console.txt navbar hatası var"
# veya: "Claude, console'da hata görüyorum"
# veya: "Claude, debug çıktısına bak"

Sen:
  1. ✅ Tetikleyici tespit edildi: "a-console.txt" / "console" / "debug"
  2. cat a-console.txt  # İlk okuma
  3. Analiz et ve raporla
  4. ✅ Aktif mod ON - Todo'ya ekle: "📄 a-console.txt aktif mod ON"

Kullanıcı (Sonraki mesajlarda): "Navbar'ı düzelt"
Sen:
  1. cat a-console.txt  # Otomatik oku (aktif mod ON)
  2. Değişiklikleri kontrol et
  3. Navbar düzelt
  4. cat a-console.txt  # Tekrar oku
  5. Sorun varsa raporla
```

#### ❌ PASİF MOD (Tetikleyici yok):
```bash
Kullanıcı: "Claude, navbar'ı düzelt"
# Tetikleyici yok: path yok, anahtar kelime yok

Sen:
  - a-console.txt'ye DOKUNMA (aktif mod OFF)
  - Sadece navbar'ı düzelt
  - Buffer dosyalarını görmezden gel
```

#### 📝 Aktif Mod Todo Örneği:
```markdown
- [x] 📄 a-console.txt aktif mod ON
- [ ] Navbar düzelt
- [ ] a-console.txt kontrol et
- [ ] Sorunları tespit et
```

#### ⚠️ KRİTİK:
- **Her konuşma yeni başlangıç**: Aktif mod her konuşmada manuel aktifleştirilmeli
- **Başta söyle**: "Oku" denmezse → Pasif mod, hiç dokunma
- **Silme, temizleme**: Bunlar için hala onay gerekli

### 🌐 WEB İÇERİK OKUMA

**✅ Kullanıcı link vermeden direkt okuyabilirim!**

**Metod 1: curl ile HTML okuma (Tercih edilen)**
```bash
# SSL bypass ile HTML içeriği oku
curl -s -k https://ixtif.com

# Sadece head/meta taglerini kontrol
curl -s -k https://ixtif.com | head -200

# Buffer dosyasına kaydet ve analiz et
curl -s -k https://URL > a-html.txt
cat a-html.txt
# Analiz yap...
echo "" > a-html.txt  # Temizle
```

**Metod 2: WebFetch tool (SSL sorunlu siteler için çalışmayabilir)**
```
WebFetch tool kullan (genelde çalışır ama SSL hatası verebilir)
```

**Kullanım:**
- ❌ "Link verirsen bakayım" DEME
- ✅ Direkt linki al ve curl ile oku
- ✅ HTML'i analiz et, sorunları tespit et
- ✅ a-html.txt'e kaydet, temizle

**Örnek:**
```
Kullanıcı: "ixtif.com anasayfasına bak, responsive çalışıyor mu?"
Sen: curl -s -k https://ixtif.com > a-html.txt
     (HTML'i analiz et)
     "Viewport meta tag var, Tailwind responsive classları kullanılmış..."
     echo "" > a-html.txt
```

---

## 🎨 TASARIM STANDARTLARI

- **Admin**: Tabler.io + Bootstrap + Livewire
- **Frontend**: Alpine.js + Tailwind CSS
- **Framework renkleri kullan** (custom renk yok)

---

## 🚨 ACİL DURUM ÇÖZÜMLER (EMERGENCY FIXES)

### BLADE @ DİRECTİVE ÇAKIŞMASI (JSON-LD)

**Sorun:** JSON-LD içinde `"@context"` ve `"@type"` Blade directive olarak parse ediliyor
**Belirti:** ParseError - "unexpected end of file, expecting endif"
**Compiled PHP:** Binlerce kapanmamış `if` bloğu oluşuyor

**Çözüm:**
```blade
# ❌ HATALI:
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Product"
}
</script>

# ✅ DOĞRU:
<script type="application/ld+json">
{
    "@@context": "https://schema.org",  # @@ ile escape
    "@@type": "Product"
}
</script>
```

### ARRAY → STRING HATASI (getTranslated)

**Sorun:** `getTranslated()` çoklu dil array'i döndürüyor, `{{ }}` htmlspecialchars() hatası veriyor
**Belirti:** `htmlspecialchars(): Argument #1 must be of type string, array given`
**Örnek Data:** `category->title = {"en":"Pallet Truck","tr":"Transpalet"}`

**Çözüm:**
```blade
# ❌ HATALI:
<script>
    trackProductView(
        '{{ $item->id }}',
        '{{ $item->getTranslated('title', app()->getLocale()) }}',
        '{{ $item->category->title }}'
    );
</script>

# ✅ DOĞRU:
<script>
    trackProductView(
        {{ $item->id }},                                        # String quote'suz
        @json($item->getTranslated('title', app()->getLocale())), # @json() kullan
        @json($item->category->title ?? 'Uncategorized')         # @json() kullan
    );
</script>
```

**@json() vs {{ }} Farkı:**
- `{{ $var }}`: String beklenir, htmlspecialchars() uygular
- `@json($var)`: Array/Object'i JSON'a çevirir, safe encode

**Kullanım Kuralı:**
- ✅ **JavaScript değişken**: `@json($array)` kullan
- ✅ **JSON-LD içinde**: `@json($value)` kullan
- ✅ **HTML içinde**: `{{ $string }}` kullan

**Debug Adımları:**
1. `php -l compiled_file.php` → Syntax kontrol
2. PHP tokenizer ile if/endif say
3. Geçici olarak blade kısmını yorum yap, test et
4. Array değişken bulunca `@json()` ile düzelt

---

## 💾 SİSTEM HAFIZASI

### DİL SİSTEMİ
- **Admin**: `system_languages` + `admin_locale`
- **Site**: `site_languages` + `site_locale`

### PATTERN SİSTEMİ
- **Page Pattern = Master**: Yeni modüller Page pattern'i alır
- **JSON çoklu dil + SEO + Modern PHP**

### THUMBMAKER SİSTEMİ
**⚡ Kod yazarken görsel oluştururken MUTLAKA Thumbmaker kullan!**

**Detaylı kılavuz:** `readme/thumbmaker/README.md`

#### Hızlı Kullanım:
```blade
{{-- Basit kullanım: 400x300 WebP --}}
<img src="{{ thumb($media, 400, 300) }}" alt="Thumbnail" loading="lazy">

{{-- Detaylı kullanım --}}
<img src="{{ thumb($media, 800, 600, [
    'quality' => 90,
    'scale' => 1,
    'alignment' => 'c',
    'format' => 'webp'
]) }}" alt="Optimized" loading="lazy">
```

#### Parametreler:
- `w/h` - Genişlik/Yükseklik (px)
- `q` - Kalite (85 varsayılan)
- `s` - Scale: 0=fit, 1=fill, 2=stretch
- `a` - Alignment: c, t, b, l, r, tl, tr, bl, br
- `f` - Format: webp, jpg, png, gif

#### ✅ Best Practices:
- **WebP kullan** (daha küçük dosya)
- **loading="lazy" ekle** (sayfa hızı)
- **Kalite 80-90** aralığında
- **Scale=1** kare thumbnail'ler için
- **Orijinal boyuttan büyütme!**

#### Admin Guide:
`/admin/mediamanagement/thumbmaker-guide` - Detaylı dokümantasyon

---

## 🏢 TENANT YÖNETİMİ

### 🚨 TENANT SİSTEMİ - KRİTİK BİLGİLER

**⚠️ BU BİR MULTI-TENANT SİSTEMDİR!**

#### 📊 Sistem Yapısı:
- **Merkezi Sistem**: `tuufi.com` (Central domain - tenant değil, sadece yönetim merkezi)
- **Tenant Sayısı**: Yüzlerce farklı tenant (sürekli artacak)
- **Her Tenant**: Farklı sektör, farklı konu, tamamen bağımsız site

#### 🎯 VARSAYILAN ÇALIŞMA TENANT'I (Özellikle belirtilmezse):
- **Domain**: `ixtif.com`
- **Tenant ID**: 2
- **Sektör**: Endüstriyel ekipman (forklift, transpalet vb.)
- **Not**: Kullanıcı başka tenant belirtmezse, işlemler bu tenant için yapılır. Bu değer kullanıcı tarafından güncellenebilir.

#### ⚠️ KRİTİK KURAL: TENANT ODAKLI ÇALIŞMA

**❌ YANLIŞ YAKLAŞIM:**
```php
// Central domain'e özgü çalışma
// Tüm sistem için tek bir çözüm üretme
// Tenant context'ini göz ardı etme
```

**✅ DOĞRU YAKLAŞIM:**
```php
// Her zaman tenant context'inde çalış
// İşlemleri aktif tenant için yap
// Tenant-spesifik verileri kullan
```

#### 📋 Tenant Context Kontrolü:
```php
// Mevcut tenant bilgisi
$tenant = tenant();  // Tenant ID: 2 (ixtif.com)
$tenantId = tenant('id');  // 2

// Tenant database
// Her tenant'ın kendi database'i var
```

#### 🗄️ MİGRATION OLUŞTURMA KURALLARI

**🚨 ÇİFTE MİGRATION ZORUNLULUĞU!**

Her migration dosyası **İKİ YERDE** oluşturulmalı:

**1. Central Migration:**
```bash
database/migrations/YYYY_MM_DD_HHMMSS_create_table_name.php
```

**2. Tenant Migration:**
```bash
database/migrations/tenant/YYYY_MM_DD_HHMMSS_create_table_name.php
```

**⚠️ UNUTURSAN:** Tenant database'ler çalışmaz, sistem bozulur!

#### 📝 Migration Workflow:
```bash
# 1. Migration oluştur (otomatik olarak tenant/ klasörüne de kopyalanmalı)
php artisan make:migration create_products_table

# 2. MANUEL KONTROL: İki dosya da var mı?
ls database/migrations/*create_products_table.php
ls database/migrations/tenant/*create_products_table.php

# 3. Eğer tenant/ klasöründe yoksa, MUTLAKA kopyala!
cp database/migrations/YYYY_MM_DD_HHMMSS_create_products_table.php \
   database/migrations/tenant/YYYY_MM_DD_HHMMSS_create_products_table.php

# 4. Migration çalıştır
php artisan migrate  # Central için
php artisan tenants:migrate  # Tüm tenant'lar için
```

#### ⚠️ DIKKAT EDILMESI GEREKENLER:

**Data İşlemleri:**
- ✅ Tenant-spesifik veriyi oku/yaz
- ❌ Central data ile tenant data'yı karıştırma
- ✅ Her zaman aktif tenant context'inde çalış

**Test/Debug:**
- ✅ ixtif.com üzerinde test et (Tenant ID: 2)
- ❌ tuufi.com'da tenant işlemlerini test etme
- ✅ Tenant database'ini kullandığını doğrula

**Modül Geliştirme:**
- ✅ Tenant-aware modüller yaz
- ✅ Her tenant için bağımsız çalışsın
- ❌ Hard-coded tenant ID kullanma
- ✅ `tenant()` helper'ı kullan

#### 🔍 Tenant Kontrol Komutları:
```bash
# Aktif tenant'ı göster
php artisan tinker
>>> tenant()
>>> tenant('id')

# Tüm tenant'ları listele
php artisan tenants:list

# Tenant migration durumu
php artisan tenants:migrate --pretend
```

---

### YENİ TENANT EKLEME
**Detaylı kılavuz:** `readme/tenant-olusturma.md`

#### Hızlı Adımlar:
1. **Plesk Panel**: Domain alias olarak ekle (SEO redirect KAPALI!)
2. **Laravel Tenant**: Tinker ile tenant + domain oluştur
3. **Config Güncelle**: `plesk repair web tuufi.com -y`
4. **Test**: `curl -I https://yenidomain.com/`

#### Kritik Kontroller:
```bash
# SEO redirect kontrol (false olmalı!)
plesk db "SELECT name, seoRedirect FROM domain_aliases WHERE name = 'domain.com'"

# Gerekirse kapat
plesk db "UPDATE domain_aliases SET seoRedirect = 'false' WHERE name = 'domain.com'"
```

#### Mevcut Tenant'lar:
- **tuufi.com**: Central domain (tenant değil)
- **ixtif.com**: Tenant ID: 2
- **ixtif.com.tr**: Tenant ID: 3

#### ⚠️ KRİTİK: NGINX CUSTOM CONFIG YASAK!
**ASLA custom nginx config oluşturma!** (`/etc/nginx/plesk.conf.d/vhosts/00-*.conf`)

**Sebep:** Custom SSL proxy config Livewire upload'ı bozuyor (ERR_SSL_BAD_RECORD_MAC_ALERT)

**Çözüm:** Default Plesk config kullan, vhost_nginx.conf'da ortak ayarlar yap

**NOT:** Yeni tenant eklerken mutlaka dökümanı takip et!

---

