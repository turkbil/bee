# Yeni Tenant Oluşturma TODO Checklist

## Otomatik Yapılanlar (Sistem)

- [x] Central DB - tenants tablosuna kayıt (id, title, tenancy_db_name, theme_id, is_active...)
- [x] MySQL veritabanı oluştur (tenant_[slug]_[6hex] formatında unique isim)
- [x] Tenant migration'ları çalıştır (php artisan tenants:migrate - 100+ tablo)
- [x] Seeder çalıştır (diller, roller, kullanıcılar, menü, anasayfa)
- [x] Storage klasörleri oluştur (storage/tenant{ID}/)
- [x] Public symlink oluştur (public/storage/tenant{ID})
- [x] Plesk veritabanı kaydı (INSERT INTO data_bases)

## Domain Eklendiğinde (Otomatik)

- [x] Central DB - domains tablosuna kayıt (domain, tenant_id)
- [x] Otomatik www. subdomain ekle (yenisite.com → www.yenisite.com)
- [x] Plesk domain alias oluştur (plesk bin domalias --create)
- [x] SSL sertifikası yenile (10sn delay, queue'da çalışır)

## Manuel Kontroller (Opsiyonel)

- [ ] DNS kayıtları kontrol et (domain tuufi.com sunucusuna yönlendirilmiş mi?)
- [ ] SSL sertifikası kontrolü (https://yenisite.com açılıyor mu?)
- [ ] Tenant CSS oluştur (npm run prod veya npm run css:tenant{ID})
- [ ] Admin panele giriş test et (nurullah@nurullah.net / g0nulcelen)

## Hızlı Kontrol Komutları

```bash
# Plesk alias kontrol
sudo /usr/sbin/plesk bin domalias --info yenisite.com

# SSL sertifika kontrol
curl -vI https://yenisite.com 2>&1 | grep -i "SSL\|expire"

# Tenant DB kontrol
mysql -e "SHOW DATABASES LIKE 'tenant_%'"

# Storage symlink kontrol
ls -la public/storage/ | grep tenant
```

## Oluşturma Akışı

```
1. Tenant::create()
   ↓
2. CreateDatabase (MySQL DB oluştur)
   ↓
3. MigrateDatabase (100+ tablo)
   ↓
4. SeedDatabase (başlangıç verileri)
   ↓
5. Storage + Plesk DB kaydı
   ↓
6. Domain Ekle (domains tablosu)
   ↓
7. Plesk Alias oluştur
   ↓
8. SSL Yenile (queue)
   ↓
✓ HAZIR
```

## Kritik Dosyalar

| Dosya | Rol |
|-------|-----|
| `app/Models/Tenant.php` | Tenant modeli |
| `app/Providers/TenancyServiceProvider.php` | Event-listener mapping |
| `app/Listeners/RegisterTenantDatabaseToPlesk.php` | Storage + Plesk DB |
| `app/Listeners/RegisterDomainAliasInPlesk.php` | Domain alias |
| `app/Listeners/CreateTenantDomains.php` | Otomatik www. ekleme |
| `app/Jobs/ReissueLetsEncryptCertificate.php` | SSL yenileme |
| `database/seeders/TenantDatabaseSeeder.php` | Başlangıç verileri |

---
18 Ocak 2026
