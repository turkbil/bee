# 🐳 500-Tenant Laravel Docker Deployment

## 📋 Sistem Özellikleri

- **PHP 8.2** + **Nginx** + **MySQL 8.0** + **Redis 7.0**
- **500 Tenant** desteği ile tam yalıtımlı sistem
- **Connection Pooling** + **Redis Clustering** 
- **Auto-scaling** + **Resource Monitoring**
- **Queue Workers** + **Scheduler** + **Supervisor**

## 🚀 Hızlı Başlangıç

### 1. Docker Kurulumu
```bash
./docker-start.sh
```

### 2. Manuel Kurulum
```bash
# Container'ları başlat
docker-compose up -d

# Laravel kurulumu
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate:fresh --seed

# Tenant kurulumu
docker-compose exec app php artisan tenants:install
```

## 📊 Servis Erişim Adresleri

| Servis | URL | Port |
|--------|-----|------|
| **Laravel App** | http://localhost | 80 |
| **PHPMyAdmin** | http://localhost:8080 | 8080 |
| **Redis Commander** | http://localhost:8081 | 8081 |

## 🛠️ Docker Komutları

### Container Yönetimi
```bash
# Tüm servisleri başlat
docker-compose up -d

# Servisleri durdur
docker-compose down

# Servis durumları
docker-compose ps

# Container'a shell erişimi
docker-compose exec app bash
```

### Log Takibi
```bash
# Log viewer script
./docker-logs.sh

# Manuel log takibi
docker-compose logs -f
docker-compose logs -f app
```

### Laravel Komutları
```bash
# Migration
docker-compose exec app php artisan migrate

# Cache temizleme
docker-compose exec app php artisan cache:clear

# Tenant işlemleri
docker-compose exec app php artisan tenants:list
docker-compose exec app php artisan tenants:run "php artisan migrate"
```

## 🔧 Konfigürasyon Dosyaları

### Docker Compose Servisleri
- **app**: Laravel uygulaması (PHP 8.2 + Nginx)
- **mysql**: MySQL 8.0 (500-tenant optimizasyonu)
- **redis**: Redis 7.0 (4-cluster yapılandırması)
- **worker**: Laravel queue worker
- **scheduler**: Cron scheduler
- **phpmyadmin**: Database yönetimi
- **redis-commander**: Redis yönetimi

### Optimizasyon Ayarları
```yaml
MySQL:
  max_connections: 1000
  innodb_buffer_pool_size: 1G
  
PHP:
  memory_limit: 512M
  max_execution_time: 300
  
Redis:
  maxmemory: 256mb
  maxmemory-policy: allkeys-lru
```

## 🏢 Tenant Yönetimi

### Yeni Tenant Oluşturma
```bash
docker-compose exec app php artisan tenants:create tenant1.localhost
```

### Tenant Migration
```bash
# Tüm tenantlarda migration çalıştır
docker-compose exec app php artisan tenants:run "php artisan migrate"

# Belirli tenant'ta migration
docker-compose exec app php artisan tenants:run --tenants=1 "php artisan migrate"
```

## 📈 Monitoring & Performance

### Resource Monitoring
```bash
# Container resource kullanımı
docker stats

# MySQL performansı
docker-compose exec mysql mysql -u root -p -e "SHOW PROCESSLIST;"

# Redis cluster durumu
docker-compose exec redis redis-cli cluster info
```

### Log Dosyaları
- **Laravel Logs**: `storage/logs/laravel.log`
- **MySQL Slow Query**: `/var/log/mysql/mysql-slow.log`
- **Nginx Access**: `/var/log/nginx/access.log`

## 🔒 Güvenlik Ayarları

### Production Hazırlığı
```bash
# .env dosyasında
APP_ENV=production
APP_DEBUG=false
```

### SSL Sertifikası
```yaml
# docker-compose.yml'de nginx portları
ports:
  - "80:80"
  - "443:443"
```

## 🚨 Sorun Giderme

### Container Başlatma Sorunları
```bash
# Container'ları temizle
docker-compose down --volumes --remove-orphans
docker system prune -a

# Yeniden başlat
./docker-start.sh
```

### Database Bağlantı Sorunları
```bash
# MySQL durumunu kontrol et
docker-compose exec mysql mysqladmin ping -h localhost -u root -p

# Bağlantı testleri
docker-compose exec app php artisan tinker
```

### Performance Sorunları
```bash
# Container resources
docker stats

# MySQL slow queries
docker-compose exec mysql mysql -u root -p -e "SELECT * FROM information_schema.processlist WHERE command != 'Sleep';"
```

## 📞 Destek

**Sistem Gereksinimleri:**
- Docker 20.0+
- Docker Compose 2.0+
- 8GB RAM (minimum)
- 50GB Disk alanı

**Önemli Notlar:**
- Production kullanımında SSL sertifikası ekleyin
- Regular database backup alın
- Log rotasyonu yapılandırın
- Resource monitoring kurun