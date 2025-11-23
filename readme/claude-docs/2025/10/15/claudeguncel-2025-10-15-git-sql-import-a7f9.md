# 🔄 GIT PULL + SQL İMPORT İŞLEMİ
**Tarih**: 2025-10-15
**ID**: a7f9

---

## 📋 GÖREV PLANI

### 1️⃣ Git Remote'tan Güncel Sistemi Çek
- Repository: https://github.com/turkbil/bee.git
- Branch: main
- Komut: `git pull origin main`

### 2️⃣ SQL Klasörünü Bul ve İncele
- SQL dosyalarının konumunu tespit et
- İçeriği analiz et (central DB + tenant DBs)

### 3️⃣ SQL Tenant Yapısını Uyarla
**Mevcut Durumdan → Yeni Duruma:**
- `laravel.test` → tuufi tenant
- `a.test` → ixtif.com tenant

**Düzenlenecek tablolar:**
- `tenants` tablosu (id, domain bilgileri)
- `domains` tablosu (domain mapping)
- Tenant-specific database isimleri

### 4️⃣ SQL Import
- Central database import
- Tenant databases import
- Domain ve tenant ayarlarını güncelle

### 5️⃣ Doğrulama
- Tenant listesini kontrol et
- Domain mapping'i doğrula
- Her iki sitenin erişilebilirliğini test et

---

## ⚠️ ÖNEMLİ NOTLAR

- **MİGRATE FRESH YAPMA!** (Mevcut veri var)
- Sadece SQL import kullan
- Tenant ID'leri ve domain mapping'i dikkatlice uyarla
- Backup almaya gerek yok (zaten git'te mevcut)

---

## ✅ YAPILACAKLAR

- [x] Git pull (bee-temp klasörüne clone edildi)
- [x] SQL klasörünü bul (database-backups/2025-10-15/)
- [x] SQL dosyalarını incele ve kopyala
- [x] Database'leri oluştur (tenant_tuufi, tenant_ixtif)
- [x] Central database import (02-central-data.sql)
- [x] Tenant database import (03-tenant-ixtif-full.sql)
- [x] Tenant/domain uyarlaması yap
- [x] Cache temizle
- [x] Test ve doğrulama

---

## 📊 SONUÇ

### ✅ Başarıyla Tamamlandı

**Tenant Yapısı:**
- Tenant 1: `laravel.test` (tuufi) → `tenant_tuufi` DB
- Tenant 2: `a.test` (ixtif) → `tenant_ixtif` DB

**Domain Mapping:**
```
1. laravel.test → Tenant 1 (tuufi)
2. www.laravel.test → Tenant 1 (tuufi)
3. a.test → Tenant 2 (ixtif)
4. www.a.test → Tenant 2 (ixtif)
```

**İçe Aktarılan Veriler:**
- Central DB: Tenant tanımları, kullanıcılar, roller, ayarlar
- tenant_ixtif: 700+ ürün, kategoriler, markalar, pages, blogs

**Değişiklikler:**
- Domain'ler production'dan local'e dönüştürüldü
- Tenant database mapping güncellendi
- Kullanılmayan tenant (3) silindi
- Cache ve config temizlendi

---

## 🎯 AKSİYONLAR

**Sistem Hazır:**
- ✅ Database'ler kuruldu
- ✅ Tenant mapping tamamlandı
- ✅ Domain routing aktif
- ✅ Cache temizlendi

**Test için:**
```bash
# Tenant listele
php artisan tinker --execute="App\Models\Tenant::with('domains')->get()"

# Site erişim
http://laravel.test (tuufi)
http://a.test (ixtif)
```

