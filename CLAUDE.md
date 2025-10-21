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

#### ✅ DOĞRU KONUM:
- **Plan/Güncelleme Dökümanları**: `readme/claude-docs/claudeguncel-YYYY-MM-DD-HH-MM-description.md`
- **Teknik Dokümantasyon**: `readme/` klasörü altında
- **Test Dosyaları**: İlgili modül/klasör içinde
- **Log/Debug**: Geçici ise `/tmp/` altında

#### ❌ ANA DİZİNE DOSYA OLUŞTURMA:
- **claudeguncel-*.md** → readme/claude-docs/ içinde olmalı
- **test-*.php** → tests/ veya ilgili modül içinde
- **debug-*.txt** → /tmp/ veya geçici klasör
- **random-*.log** → storage/logs/ içinde

#### 🎯 İSTİSNALAR (Ana dizine eklenebilir):
- Core config dosyaları (tailwind.config.js, webpack.mix.js vb.)
- Deployment scriptleri (deploy.sh vb.) - ama önce sor!
- Kritik dokümantasyon (README.md, SECURITY.md vb.)

**KURAL:** Eğer dosya %100 gerekli değilse, ana dizine koyma!

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
```bash
# Sorun çıkarsa tüm değişiklikleri geri al
git reset --hard [checkpoint-hash]

# Veya reflog kullan
git reflog
git reset --hard HEAD@{3}
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

## 💾 SİSTEM HAFIZASI

### DİL SİSTEMİ
- **Admin**: `system_languages` + `admin_locale`
- **Site**: `site_languages` + `site_locale`

### PATTERN SİSTEMİ
- **Page Pattern = Master**: Yeni modüller Page pattern'i alır
- **JSON çoklu dil + SEO + Modern PHP**

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

