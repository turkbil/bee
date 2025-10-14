# Laravel Central Tenant Database Export

Bu klasör **laravel.test** tenant'ının (Central Tenant) veritabanı export'unu içerir.

## 📁 Klasör Yapısı

```
sql/
├── schema/           # Her tablo için ayrı schema dosyaları (.sql)
├── data/             # Her tablo için ayrı data dosyaları (.sql)
├── import_database.sh # Otomatik import script'i
├── reset_auto_increment.sql # Auto increment değerlerini düzeltme
└── README.md         # Bu dosya
```

## 🚀 Nasıl Import Edilir?

### Otomatik Import (Önerilen)
```bash
cd sql/
./import_database.sh [database_name] [username] [password] [host]
```

**Örnek:**
```bash
./import_database.sh laravel_new root "" 127.0.0.1
```

### Manuel Import
1. Veritabanını oluştur
2. Schema dosyalarını import et (önce dependencies)
3. Data dosyalarını import et
4. Auto increment değerlerini düzelt

## 📊 Tablo Listesi

Sistemde toplamda **${TOTAL_TABLES}** tablo bulunmaktadır:

### Core System Tables
- `migrations` - Laravel migration kayıtları
- `tenants` - Tenant bilgileri
- `domains` - Domain yönetimi

### User & Permissions
- `users` - Kullanıcılar
- `roles` - Roller
- `permissions` - İzinler
- `model_has_roles` - Rol atamaları
- `model_has_permissions` - İzin atamaları

### Content Management
- `pages` - Sayfalar
- `announcements` - Duyurular
- `portfolios` - Portfolio öğeleri
- `portfolio_categories` - Portfolio kategorileri
- `menus` - Menüler
- `menu_items` - Menü öğeleri

### AI System (Extensive)
- `ai_providers` - AI sağlayıcıları
- `ai_features` - AI özellikleri
- `ai_prompts` - AI prompt'ları
- `ai_credit_*` - AI kredi sistemi
- Ve daha fazlası...

### Widget System
- `widgets` - Widget'lar
- `widget_categories` - Widget kategorileri
- `widget_items` - Widget öğeleri

### System & Monitoring
- `settings` - Ayarlar
- `activity_log` - Aktivite logları
- `telescope_*` - Laravel Telescope
- `pulse_*` - Laravel Pulse

## ⚠️ Önemli Notlar

1. **Auto Increment Değerleri**: Export sırasında mevcut max ID'ler tespit edilip +1 değeri set edilmiştir
2. **Dependencies**: Import script'i doğru sıralamayı kullanır
3. **Character Set**: utf8mb4_unicode_ci kullanılır
4. **Foreign Keys**: Tüm foreign key constraint'ler korunmuştur

## 🔧 Import Script Özellikleri

- ✅ Dependency order ile import
- ✅ Her tablo için ayrı error handling
- ✅ Auto increment düzeltme
- ✅ Detailed progress reporting
- ✅ Error summary at the end

## 📝 Export Detayları

- **Export Tarihi**: $(date)
- **Source Database**: laravel (Central Tenant)
- **MySQL Version**: $(mysql --version 2>/dev/null | head -1)
- **Total Tables**: ${TOTAL_TABLES}
- **Auto Increment Tables**: $(cat reset_auto_increment.sql 2>/dev/null | wc -l) tablo

## 🎯 Kullanım Senaryoları

1. **Production Deployment**: Sunucuya ilk kurulum
2. **Development Setup**: Yeni geliştirici ortamı
3. **Backup Restore**: Yedek geri yükleme
4. **Testing Environment**: Test ortamı kurulumu

Her tablo ayrı dosyada olduğu için **hangi tabloda hata** olduğu kolayca tespit edilebilir!