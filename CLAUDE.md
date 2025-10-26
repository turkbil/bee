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
# 1. Cache temizliği
php artisan view:clear
php artisan cache:clear
php artisan responsecache:clear

# 2. Build compile
npm run prod

# 3. Doğrulama
echo "✅ Cache temizlendi, build tamamlandı!"
```

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


### 🗑️ DOSYA TEMİZLEME
- **Log/Fotoğraf** gönderirsen: Oku → Analiz et → Boşalt → Sil
- **Otomatik temizlik** her işlem sonrası

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

