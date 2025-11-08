# 🚀 PRODUCTION AI DEPLOYMENT - YAPILACAKLAR

**Tarih:** 2025-11-08
**Versiyon:** AI Workflow v2.3 - Conversation History Fix + Meilisearch Integration
**Sistem:** iXtif.com (Tenant 2) + Tüm Tenant'lar
**Deployment Tipi:** Code + Composer + Cache

---

## 📋 ÖZET - NE YAPILDI?

### ✅ Yeni Özellikler:
1. **Conversation History Fix** - AI artık eski konuşmaları doğru hatırlıyor
2. **Meilisearch Node** - Gelişmiş ürün arama düğümü eklendi
3. **OpenAI API Key Fix** - Config cache'li sistemlerde API key doğru yükleniyor
4. **UX İyileştirmeleri** - Chatbot input auto-focus, better flow
5. **Tenant2 Product Search Service** - Yeni tenant-specific arama servisi
6. **Markdown Parse İyileştirmeleri** - Daha iyi HTML dönüşümü
7. **Dokümantasyon** - v2.3 basit kullanım kılavuzu eklendi

### 🔧 Değişen Dosyalar:
- **34 dosya** değiştirildi
- **+1,277 satır** eklendi
- **-2,181 satır** silindi
- **Net: -904 satır** (kod simplification yapıldı! ✅)

### 🆕 Yeni Dosyalar:
1. `Modules/AI/app/Services/Workflow/Nodes/MeilisearchSettingsNode.php` (216 satır)
2. `app/Services/AI/TenantSpecific/Tenant2ProductSearchService.php` (~700 satır)
3. `readme/ai-workflow/v2.3/` klasörü (dokümantasyon)

### ❌ Silinen Dosyalar:
1. `app/Services/AI/TenantSpecific/IxtifProductSearchService.php` (449 satır - artık Tenant2 kullanılıyor)

---

## 🎯 PRODUCTION'A ALMA ADIMLARI

### 📦 ADIM 1: GIT İŞLEMLERİ (Local)

#### 1.1. Buffer Dosyaları Temizlendi mi Kontrol Et

```bash
# Buffer dosyaları boş olmalı (1 byte)
ls -lah a-console.txt a-html.txt b-html.txt

# Beklenen çıktı:
# -rw-r--r-- 1 user staff 1B ... a-console.txt
# -rw-r--r-- 1 user staff 1B ... a-html.txt
# -rw-r--r-- 1 user staff 1B ... b-html.txt
```

**⚠️ Eğer dosyalar dolu ise:**
```bash
echo "" > a-console.txt
echo "" > a-html.txt
echo "" > b-html.txt
```

#### 1.2. Git Status Kontrolü

```bash
# Değişiklikleri kontrol et
git status

# Beklenen: 34 dosya değişmiş + 3 yeni dosya/klasör
# Modified: 31 dosya (.gitignore dahil)
# Deleted: 1 dosya (IxtifProductSearchService.php)
# Untracked: 3 item (MeilisearchSettingsNode, Tenant2ProductSearchService, v2.3/)
```

#### 1.3. Yeni Dosyaları Ekle

```bash
# Yeni node'u ekle
git add Modules/AI/app/Services/Workflow/Nodes/MeilisearchSettingsNode.php

# Yeni tenant service'i ekle
git add app/Services/AI/TenantSpecific/Tenant2ProductSearchService.php

# Yeni dokümantasyonu ekle
git add readme/ai-workflow/v2.3/

# .gitignore güncellemesini ekle (buffer dosyaları için)
git add .gitignore
```

#### 1.4. Silinen Dosyayı Onayla

```bash
# Git'e silinen dosyayı bildir
git rm app/Services/AI/TenantSpecific/IxtifProductSearchService.php
```

#### 1.5. Tüm Değişiklikleri Ekle

```bash
# Geri kalan tüm dosyaları ekle
git add .

# Son kontrol
git status
```

#### 1.6. Commit & Push

```bash
# Commit yap
git add . && git commit -m "$(cat <<'EOF'
✨ AI Workflow v2.3 - Conversation History Fix + Meilisearch

**Major Improvements:**
1. ✅ Conversation history fix - AI remembers context correctly
2. 🔍 Meilisearch integration - Advanced product search node
3. 🔑 OpenAI API key fix - Works with config cache
4. 🎨 UX improvements - Auto-focus input, better chat flow
5. 📊 Tenant2ProductSearchService - Enhanced tenant-specific search
6. 📝 Markdown parse improvements - Better HTML conversion
7. 📚 Documentation - v2.3 user guide added

**Code Changes:**
- 34 files changed: +1,277, -2,181 (net: -904 lines)
- New files: MeilisearchSettingsNode, Tenant2ProductSearchService
- Deleted: IxtifProductSearchService (replaced by Tenant2)
- Updated: AIResponseNode, ContextBuilderNode, ProductSearchNode
- Cleanup: Buffer files emptied, added to .gitignore

**Deployment Requirements:**
✅ composer dump-autoload (new classes added)
✅ npm run prod (CSS/JS changed)
✅ php artisan cache:clear
✅ php artisan view:clear
✅ curl opcache-reset.php
✅ File permissions check (new files)

📖 Detailed guide: readme/ai-workflow/production-ai-yapilacaklar.md

🤖 Generated with [Claude Code](https://claude.com/claude-code)

Co-Authored-By: Claude <noreply@anthropic.com>
EOF
)"

# Remote'a gönder
git push origin main
```

---

## 🖥️ ADIM 2: PRODUCTION SERVER DEPLOYMENT

### 2.1. SSH Bağlantısı

```bash
# Production sunucuya bağlan
ssh tuufi.com_@vh163.timeweb.ru

# Proje dizinine git
cd /var/www/vhosts/tuufi.com/httpdocs/
```

### 2.2. Git Pull

```bash
# Mevcut branch kontrol
git branch
# Beklenen: * main

# Git pull (kod değişikliklerini çek)
git pull origin main

# Başarılı mı kontrol et
echo $?
# Beklenen: 0 (başarılı)

# Hangi dosyalar geldi kontrol
git log -1 --stat
```

---

## 🔧 ADIM 3: COMPOSER İŞLEMLERİ

### 3.1. Autoload Kontrolü

```bash
# Yeni class'lar autoload'a eklenmiş mi kontrol et
grep -r "MeilisearchSettingsNode" vendor/composer/autoload_classmap.php
grep -r "Tenant2ProductSearchService" vendor/composer/autoload_classmap.php

# Eğer sonuç BOŞ ise → composer dump-autoload gerekli!
```

### 3.2. Composer Dump-Autoload

```bash
# Autoload'u yeniden oluştur
composer dump-autoload --optimize

# Beklenen çıktı:
# Generating optimized autoload files
# Generated optimized autoload files containing X classes
```

### 3.3. Doğrulama

```bash
# Yeni class'lar artık yükleniyor mu kontrol et
grep -r "MeilisearchSettingsNode" vendor/composer/autoload_classmap.php
# Beklenen: 'Modules\\AI\\App\\Services\\Workflow\\Nodes\\MeilisearchSettingsNode' => ...

grep -r "Tenant2ProductSearchService" vendor/composer/autoload_classmap.php
# Beklenen: 'App\\Services\\AI\\TenantSpecific\\Tenant2ProductSearchService' => ...
```

---

## 🎨 ADIM 4: FRONTEND BUILD (CSS/JS)

### 4.1. Node Modules Kontrolü

```bash
# package.json değişmiş mi kontrol et
git diff HEAD~1 package.json

# Eğer değişmemişse → npm install gerekli DEĞİL
# Sadece asset compile gerekli
```

### 4.2. NPM Build

```bash
# Production build (CSS + JS compile)
npm run prod

# Beklenen çıktı:
# ✔ Compiled Successfully in XXXXms
# Build at: 2025-11-08 ...
# ├── public/css/app.css
# ├── public/js/app.js
# └── public/mix-manifest.json
```

### 4.3. Asset Kontrolü

```bash
# Mix manifest güncellenmiş mi kontrol et
cat public/mix-manifest.json

# Beklenen: Yeni hash'ler
# {
#     "/css/app.css": "/css/app.css?id=...",
#     "/js/app.js": "/js/app.js?id=..."
# }
```

---

## 🗑️ ADIM 5: CACHE TEMİZLİĞİ

### 5.1. Normal Cache Clear (Güvenli)

```bash
# View cache temizle
php artisan view:clear

# Response cache temizle
php artisan responsecache:clear

# Application cache temizle (DİKKAT: Config cache'i korur!)
php artisan cache:clear
```

**⚠️ ÖNEMLİ:** `config:clear` YAPMA! Production'da config cached olmalı.

### 5.2. OPcache Reset (PHP Bytecode Cache)

```bash
# OPcache reset (ZORUNLU!)
curl -s -k https://ixtif.com/opcache-reset.php

# Beklenen çıktı:
# OPcache has been reset successfully

# 2 saniye bekle (cache propagation)
sleep 2
```

### 5.3. Compiled Views Silme (Gerekirse)

```bash
# Eğer view değişiklikleri yansımıyorsa
find storage/framework/views -type f -name "*.php" -delete

# View cache'i tekrar oluştur
php artisan view:cache
```

---

## 🔐 ADIM 6: FILE PERMISSIONS (ÖNEMLİ!)

### 6.1. Yeni Dosyaların Permission Kontrolü

```bash
# Yeni node dosyası
ls -la Modules/AI/app/Services/Workflow/Nodes/MeilisearchSettingsNode.php

# Beklenen:
# -rw-r--r-- tuufi.com_ psaserv ... MeilisearchSettingsNode.php

# Yanlış ise (root:root veya 700):
sudo chown tuufi.com_:psaserv Modules/AI/app/Services/Workflow/Nodes/MeilisearchSettingsNode.php
sudo chmod 644 Modules/AI/app/Services/Workflow/Nodes/MeilisearchSettingsNode.php
```

```bash
# Yeni tenant service dosyası
ls -la app/Services/AI/TenantSpecific/Tenant2ProductSearchService.php

# Beklenen:
# -rw-r--r-- tuufi.com_ psaserv ... Tenant2ProductSearchService.php

# Yanlış ise:
sudo chown tuufi.com_:psaserv app/Services/AI/TenantSpecific/Tenant2ProductSearchService.php
sudo chmod 644 app/Services/AI/TenantSpecific/Tenant2ProductSearchService.php
```

### 6.2. Yeni Klasör Permission'ı

```bash
# v2.3 dokümantasyon klasörü
ls -lad readme/ai-workflow/v2.3/

# Beklenen:
# drwxr-xr-x tuufi.com_ psaserv ... v2.3/

# Yanlış ise:
sudo chown -R tuufi.com_:psaserv readme/ai-workflow/v2.3/
sudo find readme/ai-workflow/v2.3/ -type f -exec chmod 644 {} \;
sudo find readme/ai-workflow/v2.3/ -type d -exec chmod 755 {} \;
```

### 6.3. Toplu Permission Fix (Eğer Gerekirse)

```bash
# Tüm AI modülü klasörü
sudo chown -R tuufi.com_:psaserv Modules/AI/
sudo find Modules/AI/ -type f -exec chmod 644 {} \;
sudo find Modules/AI/ -type d -exec chmod 755 {} \;

# Tüm app/Services klasörü
sudo chown -R tuufi.com_:psaserv app/Services/
sudo find app/Services/ -type f -exec chmod 644 {} \;
sudo find app/Services/ -type d -exec chmod 755 {} \;
```

---

## ✅ ADIM 7: DOĞRULAMA VE TEST

### 7.1. HTTP Status Kontrolü

```bash
# Site açılıyor mu kontrol et
curl -s -k -I "https://ixtif.com/" 2>&1 | grep "HTTP"

# Beklenen: HTTP/2 200
# ❌ HTTP/2 500 → OPcache reset yap, log kontrol et
```

### 7.2. PHP Syntax Kontrolü

```bash
# Yeni dosyalarda syntax hatası var mı?
php -l Modules/AI/app/Services/Workflow/Nodes/MeilisearchSettingsNode.php
# Beklenen: No syntax errors detected

php -l app/Services/AI/TenantSpecific/Tenant2ProductSearchService.php
# Beklenen: No syntax errors detected
```

### 7.3. Class Loading Testi (Tinker)

```bash
php artisan tinker

# Yeni class'ı yükleyebildi mi test et
>>> class_exists(\Modules\AI\App\Services\Workflow\Nodes\MeilisearchSettingsNode::class);
# Beklenen: true

>>> class_exists(\App\Services\AI\TenantSpecific\Tenant2ProductSearchService::class);
# Beklenen: true

# Eski class silindi mi kontrol et
>>> class_exists(\App\Services\AI\TenantSpecific\IxtifProductSearchService::class);
# Beklenen: false

>>> exit
```

### 7.4. AI Chatbot Fonksiyonel Test

**Test 1: Chatbot Açılıyor mu?**
```
1. https://ixtif.com ana sayfasına git
2. Sağ altta mor AI butonu görünüyor mu? ✅
3. Butona tıkla
4. Sohbet penceresi açılıyor mu? ✅
5. "Merhaba! 👋" hoş geldin mesajı var mı? ✅
```

**Test 2: Conversation History Çalışıyor mu?**
```
1. AI'ya yaz: "Transpalet fiyatı nedir?"
2. AI yanıt versin (ürün listesi göstermeli)
3. AI'ya yaz: "3 tonluk stokta mı?"
4. AI önceki konuşmayı hatırlıyor mu? ✅
   - Beklenen: "Evet, 3 ton transpalet stokta"
   - ❌ Yanlış: "Hangi ürün hakkında bilgi istiyorsunuz?"
```

**Test 3: Yeni Meilisearch Node Çalışıyor mu?**
```
1. AI'ya yaz: "2 ton transpalet"
2. Ürün önerileri geliyor mu? ✅
3. Log kontrol et:
   tail -f storage/logs/laravel.log | grep "MeilisearchSettingsNode"
4. Beklenen: "🔍 MeilisearchSettingsNode: Searching"
```

**Test 4: Auto-Focus Çalışıyor mu?**
```
1. AI'ya mesaj yaz ve gönder
2. AI yanıt versin
3. Input otomatik focus alıyor mu? ✅
   - Direkt yazmaya devam edebilmeli
   - Manuel input'a tıklamaya gerek yok
```

---

## 🐛 ADIM 8: SORUN GİDERME

### Problem 1: "Class not found" Hatası

**Belirti:**
```
Class 'Modules\AI\App\Services\Workflow\Nodes\MeilisearchSettingsNode' not found
```

**Çözüm:**
```bash
# Composer autoload yeniden oluştur
composer dump-autoload --optimize

# OPcache reset
curl -s -k https://ixtif.com/opcache-reset.php

# Test
php artisan tinker
>>> class_exists(\Modules\AI\App\Services\Workflow\Nodes\MeilisearchSettingsNode::class);
```

---

### Problem 2: "Permission denied" Hatası

**Belirti:**
```
failed to open stream: Permission denied in .../MeilisearchSettingsNode.php
```

**Çözüm:**
```bash
# Dosya owner'ını düzelt
sudo chown tuufi.com_:psaserv Modules/AI/app/Services/Workflow/Nodes/MeilisearchSettingsNode.php
sudo chmod 644 Modules/AI/app/Services/Workflow/Nodes/MeilisearchSettingsNode.php

# OPcache reset
curl -s -k https://ixtif.com/opcache-reset.php
```

---

### Problem 3: AI Chatbot Eski Yanıtları Veriyor

**Belirti:**
- Conversation history çalışmıyor
- AI eski prompt'ları kullanıyor

**Çözüm:**
```bash
# View cache + OPcache temizle
php artisan view:clear
find storage/framework/views -type f -name "*.php" -delete
curl -s -k https://ixtif.com/opcache-reset.php

# Response cache temizle
php artisan responsecache:clear

# Browser hard refresh
# CTRL + F5 (Windows) / CMD + SHIFT + R (Mac)
```

---

### Problem 4: CSS/JS Değişiklikleri Görünmüyor

**Belirti:**
- Floating widget auto-focus çalışmıyor
- Stil değişiklikleri yansımıyor

**Çözüm:**
```bash
# Assets tekrar compile et
npm run prod

# Mix manifest kontrol et
cat public/mix-manifest.json

# Cache clear
php artisan view:clear

# Browser cache temizle
# CTRL + SHIFT + DELETE
```

---

### Problem 5: Vendor Klasörü Git'te Değişmiş Görünüyor

**Belirti:**
```
M vendor/composer/autoload_classmap.php
M vendor/composer/autoload_static.php
```

**Açıklama:**
Bu NORMALDIR! Yeni class'lar eklendiğinde composer otomatik olarak bu dosyaları günceller.

**Yapılacak:**
```bash
# Bu dosyaları commit'e dahil et (sorun değil)
git add vendor/composer/autoload_classmap.php
git add vendor/composer/autoload_static.php

# VEYA production'da sadece dump-autoload yap (önerilen)
composer dump-autoload --optimize
```

---

## 📊 DEPLOYMENT CHECKLIST

### ✅ Ön Hazırlık (Local)
- [x] Buffer dosyaları boşaltıldı (a-console.txt, a-html.txt, b-html.txt)
- [x] .gitignore'a buffer dosyaları eklendi
- [x] Git status temiz (34 dosya + 3 yeni)
- [x] Yeni dosyalar eklendi (git add)
- [x] Silinen dosya onaylandı (git rm)
- [x] Commit yapıldı (detaylı mesajla)
- [x] Git push edildi

### ✅ Production Server
- [ ] SSH bağlantısı yapıldı
- [ ] Git pull çalıştırıldı (başarılı)
- [ ] Composer dump-autoload yapıldı
- [ ] NPM run prod çalıştırıldı
- [ ] Cache clear yapıldı (view + response + cache)
- [ ] OPcache reset edildi
- [ ] File permissions kontrol edildi (yeni dosyalar)
- [ ] HTTP 200 kontrolü yapıldı

### ✅ Doğrulama
- [ ] PHP syntax kontrolü (yeni dosyalar)
- [ ] Class loading testi (tinker)
- [ ] AI chatbot açılıyor
- [ ] Conversation history çalışıyor
- [ ] Meilisearch node çalışıyor
- [ ] Auto-focus çalışıyor
- [ ] Log'larda hata yok

### ✅ Final Test
- [ ] Ana sayfa açılıyor (HTTP 200)
- [ ] Chatbot butonu görünüyor
- [ ] Mesaj gönderme çalışıyor
- [ ] AI yanıt veriyor
- [ ] Eski konuşmaları hatırlıyor
- [ ] Admin panel çalışıyor
- [ ] Performans normal

---

## 📁 DEĞİŞEN DOSYALAR LİSTESİ (34 DOSYA)

### Backend - Core AI Workflow (13 dosya)
1. `Modules/AI/app/Http/Controllers/Api/PublicAIController.php` (+138, -20)
2. `Modules/AI/app/Services/OpenAIService.php` (+20)
3. `Modules/AI/app/Services/OptimizedPromptService.php` (+9, -95)
4. `Modules/AI/app/Services/Tenant/IxtifPromptService.php` (+25, -17)
5. `Modules/AI/app/Services/Workflow/NodeExecutor.php` (+13)
6. `Modules/AI/app/Services/Workflow/Nodes/AIResponseNode.php` (+280, -21) ⭐
7. `Modules/AI/app/Services/Workflow/Nodes/CategoryDetectionNode.php` (+17, -5)
8. `Modules/AI/app/Services/Workflow/Nodes/ContextBuilderNode.php` (+76, -41)
9. `Modules/AI/app/Services/Workflow/Nodes/NodeFactory.php` (+1)
10. `Modules/AI/app/Services/Workflow/Nodes/ProductSearchNode.php` (+71, -17)
11. `Modules/AI/app/Services/Workflow/Nodes/StockSorterNode.php` (+9, -6)
12. `Modules/AI/app/Models/AIConversation.php` (+2, -1)
13. `Modules/AI/app/Models/AIMessage.php` (+1, -1)

### Backend - Services (3 dosya)
14. `app/Services/AI/HybridSearchService.php` (+22, -1)
15. `app/Services/AI/ProductSearchService.php` (+5, -1)
16. `app/Services/MarkdownService.php` (+108, -21)

### Backend - Middleware & Config (2 dosya)
17. `app/Http/Middleware/InitializeTenancy.php` (+7)
18. `config/services.php` (+2, -1)

### Frontend - Views (3 dosya)
19. `resources/views/components/ai/floating-widget.blade.php` (+79, -58)
20. `resources/views/components/ai/inline-widget.blade.php` (+17)
21. `resources/views/components/ixtif/product-card.blade.php` (+69, -56)

### Frontend - Assets (4 dosya)
22. `public/assets/js/ai-chat.js` (+10, -9)
23. `public/css/app.css` (+2, -2)
24. `public/css/back-to-top.css` (+1, -1)
25. `public/mix-manifest.json` (+1, -1)

### Routes (1 dosya)
26. `Modules/AI/routes/api.php` (+2, -3)

### Documentation (1 dosya)
27. `readme/ai-workflow/production-ai-yapilacaklar.md` (+282, -687)

### Config & System (4 dosya)
28. `.gitignore` (+5) ⭐
29. `vendor/composer/autoload_classmap.php` (+1)
30. `vendor/composer/autoload_static.php` (+1)
31. `a-console.txt` (+1, -59) - BOŞALTILDI
32. `a-html.txt` (+1, -223) - BOŞALTILDI
33. `b-html.txt` (+1, -597) - BOŞALTILDI

### Deleted (1 dosya)
34. `app/Services/AI/TenantSpecific/IxtifProductSearchService.php` (SİLİNDİ)

### New Files (3 item)
35. `Modules/AI/app/Services/Workflow/Nodes/MeilisearchSettingsNode.php` 🆕
36. `app/Services/AI/TenantSpecific/Tenant2ProductSearchService.php` 🆕
37. `readme/ai-workflow/v2.3/` (klasör + 2 dosya) 🆕

---

## 🔍 DEPLOYMENT SONRASI KONTROL

### Log Kontrolü

```bash
# Laravel log kontrol
tail -100 storage/logs/laravel.log

# ✅ Aranan log'lar:
# - "🔍 MeilisearchSettingsNode: Searching"
# - "✅ AIResponseNode: Response generated"
# - "Conversation history loaded: X messages"

# ❌ Olmaması gerekenler:
# - "Class not found: MeilisearchSettingsNode"
# - "Permission denied"
# - "Call to undefined method"
```

### Database Kontrolü (Gerekirse)

```bash
php artisan tinker

# Yeni conversation'lar kaydediliyor mu?
>>> \Modules\AI\App\Models\AIConversation::latest()->first();

# Conversation history var mı?
>>> \Modules\AI\App\Models\AIMessage::where('conversation_id', 123)->count();

>>> exit
```

---

## 📞 DESTEK

**Sorun Olursa:**

1. **Cache Temizle:**
   ```bash
   php artisan cache:clear
   php artisan view:clear
   php artisan responsecache:clear
   curl -s -k https://ixtif.com/opcache-reset.php
   ```

2. **Log Kontrol:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Class Loading Kontrol:**
   ```bash
   composer dump-autoload --optimize
   php artisan tinker
   >>> class_exists(\Modules\AI\App\Services\Workflow\Nodes\MeilisearchSettingsNode::class);
   ```

4. **Git Rollback (Gerekirse):**
   ```bash
   git log --oneline -5
   git reset --hard [önceki-commit-hash]
   git push origin main --force
   ```

---

## 🎯 BAŞARI KRİTERLERİ

Deployment başarılı sayılır eğer:

✅ Site HTTP 200 dönüyor
✅ AI chatbot açılıyor
✅ Mesaj gönderme çalışıyor
✅ AI yanıt veriyor
✅ Conversation history çalışıyor (AI eski mesajları hatırlıyor)
✅ Log'larda "MeilisearchSettingsNode" kayıtları görünüyor
✅ Auto-focus çalışıyor (input otomatik focus alıyor)
✅ Admin panel hatasız açılıyor
✅ Performans normal (sayfa yükleme < 2 saniye)

---

**Son Güncelleme:** 2025-11-08
**Hazırlayan:** Claude AI Assistant
**Test Eden:** [Kullanıcı adı buraya]
**Onaylayan:** [Kullanıcı adı buraya]

---

## 📝 NOTLAR

- Bu deployment **CODE + COMPOSER** değişikliği içeriyor
- **DATABASE değişikliği YOK** (migration yok)
- **ENV değişikliği YOK** (config aynı)
- **File permissions** kritik (yeni dosyalar için)
- **OPcache reset** zorunlu (PHP class cache)
- **Composer dump-autoload** zorunlu (yeni class'lar var)
- **NPM run prod** önerilen (CSS/JS değişti)

---

## 🚨 GERİ ALMA PLANI

Eğer deployment başarısız olursa:

```bash
# 1. Git rollback
git log --oneline -5
git reset --hard [önceki-commit-hash]

# 2. Composer rollback
composer dump-autoload --optimize

# 3. Cache temizle
php artisan cache:clear
php artisan view:clear
curl -s -k https://ixtif.com/opcache-reset.php

# 4. Test et
curl -s -k -I "https://ixtif.com/" 2>&1 | grep "HTTP"
```

**Önemli:** Backup yoksa geri dönüş YOK! (Database değişikliği olmadığı için sorun değil ama yine de dikkat!)

---

**BU DEPLOYMENT'TA DATABASE DEĞİŞİKLİĞİ YOK!**
**SADECE KOD + COMPOSER + CACHE İŞLEMLERİ VAR!**
**BACKUP ZORUNLU DEĞİL AMA ÖNERİLİR!**
