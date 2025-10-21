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

### 🔐 OTOMATİK GIT CHECKPOINT

**⚡ KURAL:** Riskli işlemlerden ÖNCE otomatik checkpoint commit yap - **ONAY İSTEME!**

#### ✅ Otomatik Checkpoint Tetikleyicileri:
- **3+ dosya** değişikliği yapılacaksa
- **Migration** oluşturma/değiştirme
- **Core/Config dosyaları** (app/config, bootstrap, routes vb.)
- **Tenant işlemleri**
- **Database schema değişiklikleri**
- **Karmaşık refactoring** (class taşıma, namespace değişikliği vb.)

#### 📋 Otomatik Workflow:
1. **Tespit et**: Yapılacak iş riskli mi? (yukarıdaki kriterlere uyuyor mu?)
2. **Todo'ya ekle**: "🔐 Git checkpoint oluştur"
3. **Direkt commit yap**: `git add . && git commit -m "🔧 CHECKPOINT: [yapılacak iş açıklaması]"`
4. **Hash'i belirt**: Todo'da commit hash'ini yaz (ilk 8 karakter)
5. **İşe başla**: Rahatça çalış, sorun olursa `git reset --hard [hash]`

#### 🎯 Commit Formatı:
```bash
git add .
git commit -m "🔧 CHECKPOINT: [ne yapacaksan kısa açıkla]"
git log -1 --oneline  # Hash'i al, todo'ya yaz
```

#### 📝 Todo Örneği:
```markdown
- [x] 🔐 Git checkpoint oluştur (hash: bed66c0a)
- [ ] Primary domain özelliğini ekle
- [ ] Migration oluştur
- [ ] Test et
```

#### ⚠️ KRİTİK:
- **ONAY BEKLEME!** Direkt yap, kullanıcıya sorma
- **Basit işler için gereksiz** (tek satır CSS, typo düzeltme vb.)
- **Her zaman geri dönülebilir**: `git reflog` var

#### 🔄 Geri Dönüş:
```bash
# Sorun çıkarsa
git reset --hard [hash]

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

### 🛡️ KORUNAN DOSYALAR
**⚠️ Bu dosyaları ASLA silme!**
- `a-console.txt` - Console/Debugbar çıktıları için (içini boşalt, dosyayı silme)
- `a-html.txt` - HTML output için (içini boşalt, dosyayı silme)

**Kullanım Senaryosu:**
1. Kullanıcı geliştirme yaparken bu dosyalara çıktı kopyalar
2. Senden bu çıktıları okumanı ve analiz etmeni ister
3. Sen okur, analiz eder, sorunları tespit edersin
4. İşlem bittikten sonra içini boşalt (`echo "" > dosya.txt`)
5. **DOSYAYI ASLA SİLME!** - Sadece içini temizle

**Örnek Workflow:**
```bash
# 1. Oku
cat a-console.txt

# 2. Analiz et ve raporla

# 3. İçini boşalt (dosyayı silme!)
echo "" > a-console.txt
```

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

