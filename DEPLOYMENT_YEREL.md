# 🏠 YEREL ORTAMDA YAPILACAKLAR (Şu An Burdasın)

**Tarih:** 2025-10-04
**Kişi:** Nurullah
**Konum:** MacBook - Yerel Geliştirme Ortamı

---

## ✅ TODO LİSTESİ

### ☐ 1. Son Git Durumu Kontrolü
```bash
cd /Users/nurullah/Desktop/cms/laravel
git status
```

**Beklenen Sonuç:** `nothing to commit, working tree clean`

**Eğer değişiklik varsa:**
```bash
git add .
git commit -m "Son değişiklikler"
git push origin main
```

---

### ☐ 2. GitHub Repository URL'sini Al

**Mevcut Repo:**
```
https://github.com/turkbil/bee.git
```

**Personal Access Token (PAT):**
```
[GitHub PAT - Yerel .env'den kopyala]
```

**Clone Komutu (Sunucu için):**
```bash
git clone https://turkbil:[GITHUB_TOKEN]@github.com/turkbil/bee.git .
```

> **NOT:** [GITHUB_TOKEN] yerine gerçek token'ı yaz (yerel ortamda mevcut)

> **NOT:** Bu komutu sunucuda kullanacaksın, kopyala!

---

### ☐ 3. .env Değerlerini Hazırla

**Sunucuda kullanılacak değerler:**

```ini
# APP AYARLARI
APP_NAME="Laravel CMS"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain.com  # Gerçek domain'ini yaz

# DATABASE (Sunucuda oluşturacaksın)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=laravel_prod
DB_USERNAME=laravel_user
DB_PASSWORD=[Sunucuda oluşturacağın şifre]

# CACHE & SESSION
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# REDIS
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# API KEYS (Yerel .env'den kopyala - Gerçek değerleri buraya YAZMA!)
OPENAI_API_KEY=[Yerel .env'den kopyala]
ANTHROPIC_API_KEY=[Yerel .env'den kopyala]
DEEPSEEK_API_KEY=[Yerel .env'den kopyala]

# MODÜL SİSTEM AYARLARI
SYSTEM_LANGUAGES=tr,en
DEFAULT_LANGUAGE=tr
ADMIN_PER_PAGE=10
FRONT_PER_PAGE=12
MODULE_CACHE_ENABLED=true
CACHE_TTL_LIST=3600
CACHE_TTL_DETAIL=7200
CACHE_TTL_API=1800
RESPONSE_CACHE_ENABLED=true
RESPONSE_CACHE_DRIVER=redis
RESPONSE_CACHE_LIFETIME=3600
MODULE_QUEUE_CONNECTION=redis
MODULE_QUEUE_NAME=tenant_isolated
QUEUE_TIMEOUT=300
QUEUE_TRIES=3
QUEUE_RETRY_AFTER=90
MEDIA_MAX_FILE_SIZE=10240
```

> **NOT:** Bunları bir notepad'e kopyala, sunucuda .env dosyasına yapıştıracaksın!

---

### ☐ 4. DEPLOYMENT_SUNUCU.md Dosyasını Oku

Bu dosya sunucuda ne yapılacağını anlatıyor. Önce onu oku, hazır ol.

---

### ☐ 5. Sunucuya Bağlan

**SSH Bağlantısı:**
```bash
ssh kullanici_adi@sunucu_ip
# veya
ssh kullanici_adi@domain.com
```

**Plesk Panel:**
```
https://sunucu-ip:8443
Kullanıcı: admin
```

---

### ☐ 6. Sunucuda DEPLOYMENT_SUNUCU.md'yi Aç

Sunucuya bağlandıktan sonra:

```bash
cd /var/www/vhosts/domain.com/httpdocs/
```

Proje dosyalarını git clone yaptıktan sonra:

```bash
cat DEPLOYMENT_SUNUCU.md
```

Bu dosyadaki adımları Claude'a ver, yapsın.

---

## 📋 ÖZET KONTROL

**Hazır mı?**
- ✅ Git commit yapıldı ve push edildi
- ✅ GitHub repo URL'si alındı
- ✅ API key'ler kopyalandı
- ✅ .env template hazır
- ✅ DEPLOYMENT_SUNUCU.md okundu
- ✅ SSH bilgileri hazır

**SONRAKI ADIM:** Sunucuya bağlan ve `DEPLOYMENT_SUNUCU.md` dosyasındaki adımları uygula!

---

## 🆘 SORUN ÇIKARSA

1. **Git push hatası:** Token'ın süresi dolmuş olabilir, yeni token al
2. **SSH bağlantı sorunu:** Plesk'ten SSH'ın aktif olduğundan emin ol
3. **Dosya eksik:** `git status` ile kontrol et, commit edilmemiş dosya var mı?

---

**HAZIRLANDIĞI TARİH:** 2025-10-04
**HAZIRLAYAN:** Claude AI (Yerel Ortam)
**SONRAKI ADIM:** DEPLOYMENT_SUNUCU.md
