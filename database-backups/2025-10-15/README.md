# 📦 Database Backup - 2025-10-15

Bu klasör, **tuufi.com** multi-tenant sisteminin tam database yedeklerini içerir.

## 📋 İçerik

### 1. Central Database (Merkez Veritabanı)
- **Database Adı**: `tuufi_4ekim`
- **Dosyalar**:
  - `01-central-structure.sql` - Sadece yapı (structure only)
  - `02-central-data.sql` - Tam veri (2.8 MB)

**İçerik**:
- Tenant tanımları (tenants, domains)
- Kullanıcılar (users)
- Roller ve izinler (roles, permissions)
- Modül ayarları
- System settings

**Hariç Tutulan Tablolar** (performans için):
- telescope_* (Laravel Telescope logları)
- pulse_* (Laravel Pulse metrikleri)
- cache, cache_locks (Redis cache)
- sessions (Oturum verileri)
- jobs, failed_jobs (Queue verileri)

---

### 2. Tenant Databases (Kiracı Veritabanları)

#### A. tenant_ixtif (ixtif.com)
- **Dosya**: `03-tenant-ixtif-full.sql` (2.5 MB)
- **Domain**: ixtif.com
- **Tenant ID**: 2

**İçerik**:
- Shop ürünleri (700+ ürün)
- Kategoriler, markalar
- Pages, blogs, portfolios
- Media library
- Shop orders, customers
- AI conversation history
- Knowledge base

#### B. tenant_ixtif_tr (ixtif.com.tr)
- **Dosya**: `04-tenant-ixtif-tr-full.sql` (181 KB)
- **Domain**: ixtif.com.tr
- **Tenant ID**: 3

**İçerik**:
- Aynı yapı, daha az veri
- Turkish version of ixtif

---

### 3. Boş Tablolar (Empty Tables)
- **Dosya**: `05-empty-tables-structure.sql` (11 KB)

Bu tablolar sadece yapılarıyla oluşturulur (veri olmadan):
- `telescope_entries`, `telescope_entries_tags`, `telescope_monitoring`
- `pulse_aggregates`, `pulse_entries`, `pulse_values`
- `cache`, `cache_locks`
- `sessions`
- `jobs`, `failed_jobs`, `job_batches`

**Neden?** Bu tablolar runtime'da dolacak, backup'ta gereksiz yer kaplarlar.

---

## 🔧 Lokal Ortama Yükleme (Localhost)

### Ön Gereksinimler
- MySQL/MariaDB 8.0+
- PHP 8.2+
- Laravel 11.x
- Composer

---

### Adım 1: Database Oluşturma

```bash
# MySQL'e bağlan
mysql -u root -p

# Central database oluştur
CREATE DATABASE tuufi_local CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Tenant database'leri oluştur
CREATE DATABASE tenant_ixtif CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE tenant_ixtif_tr CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Kullanıcı oluştur (opsiyonel)
CREATE USER 'tuufi_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON tuufi_local.* TO 'tuufi_user'@'localhost';
GRANT ALL PRIVILEGES ON tenant_ixtif.* TO 'tuufi_user'@'localhost';
GRANT ALL PRIVILEGES ON tenant_ixtif_tr.* TO 'tuufi_user'@'localhost';
FLUSH PRIVILEGES;

EXIT;
```

---

### Adım 2: SQL Dosyalarını Import Etme

```bash
# Backup klasörüne git
cd /path/to/database-backups/2025-10-15/

# Central database'i import et
mysql -u root -p tuufi_local < 02-central-data.sql

# Boş tabloları ekle
mysql -u root -p tuufi_local < 05-empty-tables-structure.sql

# Tenant database'leri import et
mysql -u root -p tenant_ixtif < 03-tenant-ixtif-full.sql
mysql -u root -p tenant_ixtif_tr < 04-tenant-ixtif-tr-full.sql

# Boş tabloları tenant'lara da ekle
mysql -u root -p tenant_ixtif < 05-empty-tables-structure.sql
mysql -u root -p tenant_ixtif_tr < 05-empty-tables-structure.sql
```

---

### Adım 3: Laravel .env Ayarları

Projenizin `.env` dosyasını düzenleyin:

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost
APP_DOMAIN=localhost

# Database - Central
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tuufi_local
DB_USERNAME=tuufi_user
DB_PASSWORD=your_password

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Queue
QUEUE_CONNECTION=database

# Cache
CACHE_DRIVER=redis
SESSION_DRIVER=file
```

---

### Adım 4: Laravel Hazırlık

```bash
# Composer bağımlılıklarını yükle
composer install

# Storage linklerini oluştur
php artisan storage:link

# Cache'leri temizle
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize et
php artisan optimize
```

---

### Adım 5: Hosts Dosyasını Güncelle (Opsiyonel)

Local domain'leri test etmek için:

**Windows**: `C:\Windows\System32\drivers\etc\hosts`
**Mac/Linux**: `/etc/hosts`

```
127.0.0.1   ixtif.local
127.0.0.1   ixtif.com.tr.local
127.0.0.1   tuufi.local
```

---

### Adım 6: Laravel Valet/Herd (Mac) veya Laragon (Windows)

#### Laravel Valet (Mac)
```bash
cd /path/to/project
valet link tuufi
valet secure tuufi  # HTTPS için
```

**Erişim**: `https://tuufi.test`

#### Laragon (Windows)
1. Projeyi `C:\laragon\www\` altına taşıyın
2. Laragon'u başlatın
3. **Erişim**: `http://tuufi.test`

#### PHP Built-in Server (Basit Test)
```bash
php artisan serve
```

**Erişim**: `http://localhost:8000`

---

### Adım 7: Tenant Domain Ayarları

Central database'de domain kayıtlarını güncelleyin:

```sql
USE tuufi_local;

-- ixtif.com domain'ini local'e çevir
UPDATE domains SET domain = 'ixtif.local' WHERE domain = 'ixtif.com';
UPDATE domains SET domain = 'ixtif.com.tr.local' WHERE domain = 'ixtif.com.tr';

-- Tenant'ları kontrol et
SELECT * FROM tenants;
SELECT * FROM domains;
```

---

### Adım 8: Storage Dosyalarını Kopyalama (Opsiyonel)

Eğer media dosyalarınız varsa:

```bash
# Sunucudan storage'ı indir
rsync -avz user@server:/var/www/vhosts/tuufi.com/httpdocs/storage/ ./storage/

# Veya manuel olarak kopyala
# storage/tenant2/ -> local storage/
```

---

### Adım 9: Test

```bash
# Artisan komutlarını test et
php artisan tinker

# Tenant'ları listele
>>> \App\Models\Tenant::with('domains')->get();

# Domain'leri listele
>>> \Stancl\Tenancy\Database\Models\Domain::with('tenant')->get();
```

**Browser'da test**:
- Central: `http://tuufi.local` veya `http://localhost:8000`
- Tenant 1: `http://ixtif.local`
- Tenant 2: `http://ixtif.com.tr.local`

---

## 🔍 Sorun Giderme

### Problem: "Access denied for user"
**Çözüm**: Database kullanıcı izinlerini kontrol edin:
```sql
SHOW GRANTS FOR 'tuufi_user'@'localhost';
```

### Problem: "Table doesn't exist"
**Çözüm**: Import sırasını kontrol edin. Önce central, sonra tenant.

### Problem: "Tenant not found"
**Çözüm**: Domain kayıtlarını kontrol edin:
```sql
SELECT * FROM domains WHERE domain LIKE '%local%';
```

### Problem: Media/Images görünmüyor
**Çözüm**: Storage link'ini kontrol edin:
```bash
php artisan storage:link
ls -la public/storage  # Symlink olmalı
```

---

## 📊 Database İstatistikleri

| Database | Boyut | Tablo Sayısı | Önemli Tablolar |
|----------|-------|-------------|-----------------|
| tuufi_4ekim (Central) | 2.8 MB | ~50 | tenants, domains, users, roles |
| tenant_ixtif | 2.5 MB | ~120 | shop_products, shop_categories, pages |
| tenant_ixtif_tr | 181 KB | ~120 | Aynı yapı, az veri |

---

## ⚠️ Önemli Notlar

1. **Production Data**: Bu backup production'dan alındı. Hassas veriler içerebilir.
2. **Passwords**: Tüm user password'ları hash'li (bcrypt).
3. **API Keys**: Production API key'leri içerir. Local'de .env'de değiştirin.
4. **Telescope/Pulse**: Boş tablolar olarak eklendi, runtime'da dolacak.
5. **Redis**: Production Redis prefix'leri kullanabilir, local'de sıfırlanmalı.
6. **Queue**: Jobs tablosu boş, yeni job'lar oluşturulacak.

---

## 🔐 Güvenlik

- Bu dosyaları **asla public repository'e PUSH ETMEYİN**
- `.gitignore` listesine ekleyin:
  ```
  database-backups/
  *.sql
  ```
- Paylaşıyorsanız şifreli arşiv kullanın:
  ```bash
  tar -czf backup.tar.gz 2025-10-15/
  openssl enc -aes-256-cbc -in backup.tar.gz -out backup.tar.gz.enc
  ```

---

## 📞 Destek

Sorun yaşarsanız:
1. Laravel log'larını kontrol edin: `storage/logs/laravel.log`
2. Database connection'ı test edin: `php artisan tinker`
3. Migration durumunu kontrol edin: `php artisan migrate:status`

---

**Oluşturulma Tarihi**: 2025-10-15
**Backup Kaynağı**: Production Server (tuufi.com)
**Laravel Version**: 11.x
**PHP Version**: 8.2+

---

🚀 **İyi çalışmalar!**
