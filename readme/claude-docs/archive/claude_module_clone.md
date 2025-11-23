# 🚀 Modül Klonlama Sistemi

**Dosya:** `scripts/module-clone.sh`
**Amaç:** Mevcut modüllerden hızlıca yeni modül oluşturmak

---

## 📋 KULLANIM

### Temel Komut
```bash
./scripts/module-clone.sh [kaynak_modül] [hedef_modül]
```

### Örnekler
```bash
# Page modülünden Product modülü oluştur
./scripts/module-clone.sh Page Product

# Portfolio modülünden Gallery modülü oluştur
./scripts/module-clone.sh Portfolio Gallery

# Announcement modülünden News modülü oluştur
./scripts/module-clone.sh Announcement News
```

---

## 🔄 YAPILAN İŞLEMLER

### 1️⃣ **Dosya/Klasör Kopyalama**
- Kaynak modülün tüm dosya/klasör yapısı kopyalanır
- `Modules/KaynakModül` → `Modules/HedefModül`

### 2️⃣ **İsim Değiştirme**
- **Klasör isimleri:** `PageController` → `ProductController`
- **Dosya isimleri:** `PageService.php` → `ProductService.php`
- **Derin seviye:** En derin klasörden başlayarak güvenli rename

### 3️⃣ **İçerik Değiştirme**
Script şu string dönüşümlerini yapar:

| Tip | Örnek Dönüşüm |
|-----|----------------|
| **Normal** | `Page` → `Product` |
| **Küçük** | `page` → `product` |
| **Büyük** | `PAGE` → `PRODUCT` |
| **Title** | `Page` → `Product` |
| **Snake** | `page_category` → `product_category` |
| **Kebab** | `page-management` → `product-management` |
| **Çoğul** | `pages` → `products` |
| **Çoğul Title** | `Pages` → `Products` |

### 4️⃣ **Dosya Tipleri**
Bu dosya tiplerinde değişiklik yapılır:
- `.php` (Controllers, Models, Services, etc.)
- `.blade.php` (Views)
- `.json` (Config files)
- `.js` (JavaScript files)
- `.vue` (Vue components)

### 5️⃣ **Composer Autoload**
- `composer dump-autoload` otomatik çalıştırılır
- PSR-4 namespace'ler güncellenir

---

## ⚠️ MANUEL KONTROL GEREKENLER

### 🗂️ **Migration Dosyaları**
```php
// Manuel değiştir:
Schema::create('pages', function...
// Şuna:
Schema::create('products', function...
```

### 🛣️ **Route Dosyaları**
```php
// Kontrol et:
Route::prefix('admin/products')->group(function () {
    // Route tanımları
});
```

### 🌱 **Seeder Dosyaları**
```php
// Manuel güncelle:
class ProductSeeder extends Seeder
{
    // Product verilerini ekle
}
```

### 🌍 **Lang Dosyaları**
```php
// Kontrol et ve güncelle:
'products' => [
    'title' => 'Ürünler',
    'create' => 'Ürün Oluştur',
    // ...
];
```

---

## 🎯 MASTER PATTERN'LER

### **Page Modülü** (En Güvenli)
```bash
./scripts/module-clone.sh Page YeniModul
```
- Homepage sistemi
- CSS/JS editörü
- Güvenlikli sanitizer
- SEO sistemi

### **Announcement Modülü** (Medya Odaklı)
```bash
./scripts/module-clone.sh Announcement YeniModul
```
- Featured image sistemi
- Gallery sistemi
- Medya yönetimi
- Basit yapı

### **Portfolio Modülü** (Kategori Sistemi)
```bash
./scripts/module-clone.sh Portfolio YeniModul
```
- Kategori hiyerarşisi
- İlişkisel yapı
- Medya + kategori

---

## 🔧 GELİŞMİŞ ÖZELLİKLER

### **Güvenlik**
- ✅ Backup dosyaları otomatik temizlenir
- ✅ Hedef modül varsa onay ister
- ✅ Hata durumunda işlem durur

### **Performans**
- ⚡ Paralel dosya işleme
- ⚡ Optimize edilmiş string replace
- ⚡ Minimal memory kullanımı

### **Logging**
- 📋 Renkli konsol çıktısı
- ✅ Başarı/hata mesajları
- 📊 İşlem özeti

---

## 🚀 ÖRNEK KULLANIM

```bash
# Product modülü oluştur
./scripts/module-clone.sh Page Product

# Çıktı:
# ========================================
# 🚀 Laravel Modül Klonlama Script'i
# ========================================
# 📋 Modül klonlanıyor: Page → Product
# 📋 String dönüşümler hazırlandı:
#   📝 Kaynak: Page → Hedef: Product
#   📝 Snake: page → product
#   📝 Kebab: page → product
#   📝 Çoğul: pages → products
# 📋 Dosyalar kopyalanıyor...
# ✅ Modül kopyalandı
# 📋 Dosya ve klasör isimleri değiştiriliyor...
# ✅ Klasör: PageController → ProductController
# ✅ Dosya: PageService.php → ProductService.php
# 📋 Dosya içerikleri güncelleniyor...
# ✅ Dosya içerikleri güncellendi
# 📋 Composer autoload yenileniyor...
# ✅ Composer autoload yenilendi
# ========================================
# ✅ 🎉 Modül başarıyla klonlandı!
#
# 📁 Yeni Modül: Modules/Product
# 📋 Sonraki Adımlar:
#   1️⃣  Migration dosyalarını kontrol edin
#   2️⃣  Route'ları kontrol edin
#   3️⃣  Seeder'ları güncelleyin
#   4️⃣  Lang dosyalarını düzenleyin
#   5️⃣  Test edin: php artisan migrate:fresh --seed
```

---

## 💡 İPUÇLARI

1. **İlk Klonlamada:** Page modülünü kullan (en stabil)
2. **Medya İhtiyacı:** Announcement modülünü kullan
3. **Kategori İhtiyacı:** Portfolio modülünü kullan
4. **Sonrasında:** Migration, Route, Seeder manuel kontrol et
5. **Test Et:** `php artisan migrate:fresh --seed` çalıştır

Bu sistem sayesinde 5 dakikada yeni modül oluşturabilir, sadece business logic'e odaklanabilirsin! 🚀