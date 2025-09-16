# 🚀 VSCode Dev Container Kurulum Rehberi

Bu proje VSCode Dev Container desteği ile geliştirilmiştir. Container ortamında Laravel geliştirme yapmak için aşağıdaki adımları takip edin.

## 📋 Gereksinimler

- [Docker Desktop](https://www.docker.com/products/docker-desktop)
- [Visual Studio Code](https://code.visualstudio.com/)
- [Dev Containers Extension](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers)

## 🔧 Kurulum

### Otomatik Başlatma (Önerilen)

1. **Terminal'de proje klasörüne gidin:**
   ```bash
   cd /path/to/laravel-project
   ```

2. **Development environment'ı başlatın:**
   ```bash
   ./start-dev.sh
   ```

3. **VSCode'u açın ve container'ı başlatın:**
   - VSCode'da proje klasörünü açın
   - Sol alt köşede "Reopen in Container" butonuna tıklayın
   - Veya `Ctrl+Shift+P` > "Dev Containers: Reopen in Container"

### Manuel Başlatma

1. **Docker Desktop'ı başlatın**

2. **Development container'ları başlatın:**
   ```bash
   docker-compose -f docker-compose.dev.yml up -d --build
   ```

3. **VSCode Dev Container'ı açın**

## 🌐 Erişim Adresleri

Container başlatıldıktan sonra aşağıdaki adreslere erişebilirsiniz:

- **Laravel App:** http://localhost
- **PhpMyAdmin:** http://localhost:8080
- **Redis Commander:** http://localhost:8081

## 🛠️ VSCode Görevleri

`Ctrl+Shift+P` > "Tasks: Run Task" ile aşağıdaki görevleri çalıştırabilirsiniz:

- **Start Development Environment** - Development ortamını başlatır
- **Stop Development Environment** - Container'ları durdurur
- **View Development Logs** - Container log'larını gösterir
- **Rebuild Development Containers** - Container'ları yeniden oluşturur

## 📦 Container Yapısı

### Development Services:
- **laravel-dev** - Laravel uygulaması (PHP 8.2 + Nginx)
- **mysql-dev** - MySQL 8.0 veritabanı
- **redis-dev** - Redis cache server
- **phpmyadmin-dev** - Veritabanı yönetimi
- **redis-commander-dev** - Redis yönetimi

### Container Özellikleri:
- **Xdebug** - PHP debugging desteği
- **Hot reload** - Kod değişikliklerinde otomatik yeniden yükleme
- **Volume mounting** - Local dosyalar container ile senkronize
- **Port forwarding** - Servisler localhost'ta erişilebilir

## 🔍 Debug Konfigürasyonu

VSCode'da PHP debugging için:

1. **Breakpoint** koyun
2. `F5` tuşuna basın veya "Start Debugging" seçin
3. Browser'da sayfayı yenileyin

Xdebug ayarları:
- Host: `host.docker.internal`
- Port: `9003`
- IDE Key: `VSCODE`

## 📝 Development Workflow

1. **Container'ı başlatın** - `./start-dev.sh`
2. **VSCode'da container'ı açın** - "Reopen in Container"
3. **Kod yazın ve test edin**
4. **Değişiklikler otomatik olarak yansır**
5. **Debug ihtiyacınız olduğunda F5 tuşuna basın**

## 🔧 Composer ve NPM

Container içinde tüm PHP ve Node.js araçları hazır:

```bash
# Container terminal'inde
composer install
npm install
php artisan migrate
php artisan serve
```

## 🚫 Sorun Giderme

### Docker Desktop çalışmıyor
```bash
# Mac'te
open /Applications/Docker.app

# Docker durumunu kontrol et
docker info
```

### Container başlatılamıyor
```bash
# Container'ları temizle
docker-compose -f docker-compose.dev.yml down --volumes --remove-orphans

# Yeniden başlat
docker-compose -f docker-compose.dev.yml up -d --build
```

### VSCode container'ı açmıyor
1. Dev Containers extension'ının yüklü olduğundan emin olun
2. VSCode'u yeniden başlatın
3. `Ctrl+Shift+P` > "Dev Containers: Rebuild Container"

### Port çakışması
```bash
# Kullanılan portları kontrol et
lsof -i :80
lsof -i :3306

# Container'ları durdur
docker-compose -f docker-compose.dev.yml down
```

## 📄 Konfigürasyon Dosyaları

- `.devcontainer/devcontainer.json` - VSCode container ayarları
- `docker-compose.dev.yml` - Development servis tanımları
- `Dockerfile` - Development container imajı
- `docker/` - Container konfigürasyon dosyaları
- `.vscode/` - VSCode workspace ayarları

## 🎯 Production vs Development

Bu kurulum **sadece development** içindir. Production deployment için:
- `docker-compose.yml` kullanın (production servisleri)
- Environment değişkenlerini production için ayarlayın
- SSL sertifikalarını konfigüre edin

## 💡 İpuçları

- Container içinde terminal açmak için: `Ctrl+Shift+` `
- Dosya izinleri için: Container içinde `www-data` kullanıcısı aktif
- Database: root şifresi `password`, veritabanı `laravel`
- Redis: Şifresiz, port 6379
- Hot reload sayesinde kod değişiklikleri anında yansır