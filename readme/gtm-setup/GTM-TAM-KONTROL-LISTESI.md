# 🔍 GTM SİSTEM TAM KONTROL LİSTESİ

**Tarih:** 2025-10-26
**Sistem:** Dinamik GTM Entegrasyonu (Setting-Based)

---

## 📋 KONTROL ADIMLARI

### ✅ AŞAMA 1: DATABASE KONTROLÜ

#### 1.1. Settings Tanımı Kontrolü
```bash
mariadb -e "USE tuufi_4ekim; SELECT id, label, \`key\`, type, default_value FROM settings WHERE \`key\` = 'seo_google_tag_manager_id';"
```

**Beklenen Sonuç:**
```
id: 95
label: Google Tag Manager Container ID
key: seo_google_tag_manager_id
type: text
default_value: NULL
```

**✅ Başarılı:** Kayıt var
**❌ Hata:** Kayıt yoksa setting tanımı eksik!

---

#### 1.2. Tenant Değer Kontrolü (Tenant 2 - ixtif.com)
```bash
php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

tenancy()->initialize(2);
echo 'GTM ID: ' . setting('seo_google_tag_manager_id', 'YOK!') . PHP_EOL;
"
```

**Beklenen Sonuç:**
```
GTM ID: GTM-P8HKHCG9
```

**✅ Başarılı:** GTM ID var
**❌ Hata:** "YOK!" yazıyorsa → settings_values'da kayıt yok

**Düzeltme:**
```bash
php artisan tinker
tenancy()->initialize(2);
setting_update('seo_google_tag_manager_id', 'GTM-P8HKHCG9');
exit
```

---

### ✅ AŞAMA 2: FRONTEND KONTROLÜ

#### 2.1. Tarayıcı Testi (Manuel)

1. **URL Aç:**
   ```
   https://ixtif.com
   ```

2. **Developer Tools Aç:** (F12 veya Sağ Tık → İncele)

3. **Console Tab:**
   ```javascript
   dataLayer
   ```
   **✅ Başarılı:** Array döner, içinde GTM eventi var
   **❌ Hata:** `undefined` dönerse GTM yüklenmemiş

4. **Network Tab:**
   - Filter: `gtm.js`
   - **✅ Başarılı:** `gtm.js?id=GTM-P8HKHCG9` request var (Status: 200)
   - **❌ Hata:** Request yoksa GTM script çalışmamış

5. **Elements Tab:**
   - Ctrl+F → `GTM-P8HKHCG9` ara
   - **✅ Başarılı:** 2 sonuç bulur (head + body)
   - **❌ Hata:** Bulamazsa layout render sorunu

---

#### 2.2. Curl Testi (Otomatik)
```bash
curl -s https://ixtif.com | grep -c "GTM-P8HKHCG9"
```

**Beklenen Sonuç:**
```
2
```

**✅ Başarılı:** 2 adet bulunur (head script + body noscript)
**❌ Hata:** 0 ise GTM yok, cache sorunu olabilir

**Düzeltme:**
```bash
php artisan view:clear
php artisan cache:clear
php artisan responsecache:clear
```

---

### ✅ AŞAMA 3: ADMIN PANEL KONTROLÜ

#### 3.1. Login ve Sayfa Kontrolü

1. **Login:**
   ```
   https://ixtif.com/admin
   ```

2. **Herhangi Bir Admin Sayfası Aç:**
   ```
   https://ixtif.com/admin/shop
   https://ixtif.com/admin/dashboard
   ```

3. **Developer Tools → Console:**
   ```javascript
   dataLayer
   ```
   **✅ Başarılı:** Array döner
   **❌ Hata:** `undefined` dönerse admin layout'ta GTM yüklenmemiş

4. **Network Tab:**
   - `gtm.js?id=GTM-P8HKHCG9` kontrolü
   - **✅ Başarılı:** Request var
   - **❌ Hata:** Request yoksa layout render sorunu

---

#### 3.2. Blade Render Kontrolü (Sunucu Tarafı)
```bash
php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

tenancy()->initialize(2);

\$gtmId = setting('seo_google_tag_manager_id');

if (\$gtmId) {
    echo '✅ Admin layout GTM render edilecek: ' . \$gtmId . PHP_EOL;
} else {
    echo '❌ GTM ID yok, admin layout GTM render EDİLMEYECEK!' . PHP_EOL;
}
"
```

---

### ✅ AŞAMA 4: STATIC HTML KONTROLÜ

#### 4.1. Static HTML Dosya Kontrolü
```bash
# Tüm static HTML'lerde GTM var mı?
for file in public/design/hakkimizda-alternatifler/*.html; do
    count=$(grep -c "GTM-P8HKHCG9" "$file" 2>/dev/null || echo "0")
    if [ "$count" -eq "2" ]; then
        echo "✅ $(basename $file): GTM var"
    else
        echo "❌ $(basename $file): GTM eksik ($count/2)"
    fi
done
```

**Beklenen Sonuç:**
```
✅ design-hakkimizda-1.html: GTM var
✅ design-hakkimizda-2.html: GTM var
...
✅ design-hakkimizda-10.html: GTM var
✅ index.html: GTM var
```

---

#### 4.2. Canlı URL Testi
```bash
curl -s https://ixtif.com/design/hakkimizda-alternatifler/design-hakkimizda-10.html | grep -c "GTM-P8HKHCG9"
```

**Beklenen Sonuç:**
```
2
```

**Düzeltme:**
```bash
# GTM kodları eksikse tekrar inject et
php readme/gtm-setup/add-gtm-to-static-html.php --force
```

---

### ✅ AŞAMA 5: AYAR YÖNETİMİ KONTROLÜ

#### 5.1. Admin Ayar Sayfası

1. **URL Aç:**
   ```
   https://ixtif.com/admin/settingmanagement/values/8
   ```

2. **"Google Tag Manager Container ID" Alanını Bul**

3. **Mevcut Değeri Kontrol Et:**
   - **✅ Başarılı:** `GTM-P8HKHCG9` yazıyor
   - **❌ Hata:** Boş veya farklı değer

4. **Değer Değiştirme Testi:**
   - Değeri `GTM-TEST-ID` yap
   - Kaydet
   - Frontend'e git: https://ixtif.com
   - View Source → `GTM-TEST-ID` ara
   - **✅ Başarılı:** Yeni ID görünüyor
   - **❌ Hata:** Eski ID hala var → Cache sorunu

5. **Eski Değeri Geri Al:**
   - `GTM-P8HKHCG9` yap
   - Kaydet
   - Cache temizle (gerekirse)

---

### ✅ AŞAMA 6: CROSS-TENANT KONTROLÜ

#### 6.1. Tenant 3 Test (ixtif.com.tr)

**Eğer ixtif.com.tr için farklı GTM istiyorsan:**

```bash
# Tenant 3'e farklı GTM ekle
php artisan tinker
tenancy()->initialize(3);
setting_update('seo_google_tag_manager_id', 'GTM-TENANT3-ID');
exit

# Static HTML'leri güncelle (tenant 3 için)
php readme/gtm-setup/add-gtm-to-static-html.php --tenant=3 --force
```

**Test:**
```bash
curl -s https://ixtif.com.tr | grep "GTM-TENANT3-ID"
```

---

### ✅ AŞAMA 7: GTM DASHBOARD KONTROLÜ

#### 7.1. Tag Manager Preview Mode

1. **GTM Dashboard Aç:**
   ```
   https://tagmanager.google.com
   ```

2. **Container Seç:** GTM-P8HKHCG9

3. **Preview Butonuna Tıkla**

4. **URL Gir:**
   ```
   https://ixtif.com
   ```

5. **Tag Assistant Açılır:**
   - ✅ **Tags Fired:** Hangi etiketler çalıştı
   - ✅ **Variables:** dataLayer değişkenleri
   - ✅ **Data Layer:** Gönderilen eventler

6. **Admin Panel Test:**
   - Preview mode açıkken: https://ixtif.com/admin/shop
   - Tag Assistant'ta admin sayfası tracking'i gör

7. **Static HTML Test:**
   - Preview mode açıkken: https://ixtif.com/design/hakkimizda-alternatifler/design-hakkimizda-10.html
   - Tag Assistant'ta static HTML tracking'i gör

---

#### 7.2. Tag Coverage (Etiket Kapsamı)

1. **GTM Dashboard → Workspace → Tag Coverage**

2. **URL'leri Test Et:**
   - `ixtif.com/admin/shop` → ✅ Etiketlenmeli
   - `ixtif.com/design/hakkimizda-alternatifler/design-hakkimizda-10.html` → ✅ Etiketlenmeli

3. **Sorun Görürsen:**
   - 24-48 saat bekle (GTM verilerini toplama süresi)
   - Veya Preview Mode kullan (anlık test)

---

### ✅ AŞAMA 8: PERFORMANS KONTROLÜ

#### 8.1. GTM Script Yüklenme Hızı

**Chrome DevTools → Network → gtm.js:**
- **Yüklenme Süresi:** <500ms (iyi)
- **Status:** 200 OK
- **Cache:** Evet (2. ziyarette cache'den yüklenir)

---

#### 8.2. Console Error Kontrolü

**F12 → Console:**
- ❌ GTM ile ilgili hata olmamalı
- ⚠️ `Failed to load resource: gtm.js` varsa:
  - CSP (Content Security Policy) sorunu olabilir
  - Firewall/AdBlock sorunu olabilir

**CSP Kontrolü:**
```bash
curl -I https://ixtif.com | grep -i "content-security-policy"
```

**Düzeltme:** `app/Http/Middleware/SecurityHeaders.php` içinde GTM domain'leri eklenmeli (zaten var)

---

## 🎯 HIZLI KONTROL SCRIPT'İ

Tüm kontrolleri otomatik yap:

```bash
#!/bin/bash

echo "🔍 GTM SİSTEM TAM KONTROLÜ"
echo "=============================="
echo ""

# 1. Database
echo "1️⃣ Database Kontrolü..."
php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
tenancy()->initialize(2);
\$gtm = setting('seo_google_tag_manager_id', 'YOK');
echo '   GTM Setting: ' . \$gtm . PHP_EOL;
if (\$gtm === 'YOK') exit(1);
"
[ $? -eq 0 ] && echo "   ✅ Başarılı" || echo "   ❌ HATA: GTM setting yok!"

echo ""

# 2. Frontend
echo "2️⃣ Frontend Kontrolü..."
COUNT=$(curl -s https://ixtif.com | grep -c "GTM-P8HKHCG9")
echo "   GTM Bulundu: $COUNT adet"
[ "$COUNT" -eq "2" ] && echo "   ✅ Başarılı" || echo "   ❌ HATA: GTM eksik!"

echo ""

# 3. Static HTML
echo "3️⃣ Static HTML Kontrolü..."
COUNT=$(curl -s https://ixtif.com/design/hakkimizda-alternatifler/design-hakkimizda-10.html | grep -c "GTM-P8HKHCG9")
echo "   GTM Bulundu: $COUNT adet"
[ "$COUNT" -eq "2" ] && echo "   ✅ Başarılı" || echo "   ❌ HATA: GTM eksik!"

echo ""

# 4. Admin Layout Test
echo "4️⃣ Admin Layout Kontrolü..."
php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
tenancy()->initialize(2);
\$gtm = setting('seo_google_tag_manager_id');
echo '   GTM Render: ' . (\$gtm ? 'EVET' : 'HAYIR') . PHP_EOL;
if (!\$gtm) exit(1);
"
[ $? -eq 0 ] && echo "   ✅ Başarılı" || echo "   ❌ HATA: Admin layout GTM yok!"

echo ""
echo "🎉 Kontrol Tamamlandı!"
```

**Kullanım:**
```bash
chmod +x readme/gtm-setup/gtm-full-check.sh
bash readme/gtm-setup/gtm-full-check.sh
```

---

## 📊 BAŞARILI BİR KONTROL SONUCU

```
🔍 GTM SİSTEM TAM KONTROLÜ
==============================

1️⃣ Database Kontrolü...
   GTM Setting: GTM-P8HKHCG9
   ✅ Başarılı

2️⃣ Frontend Kontrolü...
   GTM Bulundu: 2 adet
   ✅ Başarılı

3️⃣ Static HTML Kontrolü...
   GTM Bulundu: 2 adet
   ✅ Başarılı

4️⃣ Admin Layout Kontrolü...
   GTM Render: EVET
   ✅ Başarılı

🎉 Kontrol Tamamlandı!
```

---

## 🚨 SORUN GİDERME

### Sorun: Frontend'de GTM yok

**Çözüm:**
```bash
php artisan view:clear
php artisan cache:clear
php artisan responsecache:clear
```

### Sorun: Admin'de GTM yok

**Kontrol:**
```bash
php -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
tenancy()->initialize(2);
echo setting('seo_google_tag_manager_id', 'YOK') . PHP_EOL;
"
```

**"YOK" dönerse:**
```bash
php artisan tinker
tenancy()->initialize(2);
setting_update('seo_google_tag_manager_id', 'GTM-P8HKHCG9');
exit
```

### Sorun: Static HTML'de GTM yok

**Çözüm:**
```bash
php readme/gtm-setup/add-gtm-to-static-html.php --force
```

---

## ✅ KONTROL LİSTESİ ÖZETİ

- [ ] Database'de setting tanımı var (settings: ID 95)
- [ ] Tenant 2'de değer var (settings_values: ID 40)
- [ ] Frontend'de GTM yükleniyor (2 adet)
- [ ] Admin panel'de GTM yükleniyor
- [ ] Static HTML'lerde GTM var (11 dosya x 2 = 22 adet)
- [ ] Admin ayar sayfası çalışıyor
- [ ] GTM Preview Mode test edildi
- [ ] Tag Coverage kontrol edildi
- [ ] Console'da hata yok
- [ ] Network'te gtm.js yükleniyor

**Tüm kutular işaretliyse: ✅ SİSTEM TAM ÇALIŞIYOR!**

---

**Hazırlayan:** Claude AI
**Son Güncelleme:** 2025-10-26
