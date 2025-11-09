# 🗄️ Arşivlenmiş Eski Payment Sistemi Migration'ları

**Tarih:** 2025-11-09
**Sebep:** Global Payment Modülüne Geçiş

---

## ⚠️ BU DOSYALAR ARŞİVLENMİŞTİR

Bu klasördeki migration'lar **artık kullanılmıyor**.

### SEBEBİ:

Shop modülü kendi özel payment tablolarını kullanıyordu:
- `shop_payment_methods`
- `shop_payments`

**Yeni Sistem:**
Global Payment modülü ile polymorphic ilişki kullanıyoruz:
- `payment_methods` (global, tüm modüller için)
- `payments` (global, polymorphic - ShopOrder, Subscription, vb.)

---

## 📋 ARŞİVLENEN DOSYALAR:

1. **007_create_shop_payment_methods_table.php**
   - Shop özel ödeme yöntemleri tablosu
   - Artık: `payment_methods` (global modül)

2. **023_create_shop_payments_table.php**
   - Shop özel ödemeler tablosu
   - Artık: `payments` (global, polymorphic)

---

## 🔄 CLEANUP MİGRATION:

Bu tablolar **029_cleanup_old_payment_tables.php** migration'u ile kaldırıldı:

```bash
php artisan migrate
# veya
php artisan tenants:migrate
```

### Yapılanlar:
1. ✅ `shop_payment_methods` tablosu DROP
2. ✅ `shop_payments` tablosu DROP
3. ✅ `shop_orders.payment_method_id` kolonu kaldırıldı
4. ✅ `shop_orders.paid_amount` kolonu kaldırıldı
5. ✅ `shop_orders.remaining_amount` kolonu kaldırıldı

---

## 🚫 BU DOSYALARI ÇALIŞTIRMA!

Bu migration'lar **sadece referans için** arşivlendi.

Eğer yanlışlıkla çalıştırırsan:
- Global Payment modülü ile çakışır
- Eski sistem geri gelir (istemiyoruz!)

---

## 📚 YENİ SİSTEM DOKÜMANTASYONu:

- **Mimari:** `readme/paytr-setup/GLOBAL-PAYMENT-ARCHITECTURE.md`
- **Görsel Rehber:** `https://ixtif.com/paytr-setup/`
- **Kod Örnekleri:** `readme/paytr-setup/PAYTR-CODE-TEMPLATES.md`

---

**Not:** Eğer rollback gerekirse (çok nadir), bu dosyaları tekrar migration klasörüne taşıyabilirsin. Ama bunu yapmadan önce global Payment modülünü kaldırman gerekir.
