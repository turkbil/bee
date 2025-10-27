# 💰 AI Kredi Ekleme Dokümantasyonu

Tuufi Multi-Tenant sistemine manuel AI kredi ekleme rehberi.

---

## 📁 Dosyalar

| Dosya | Açıklama | Kullanım |
|-------|----------|----------|
| **HIZLI-REFERANS.md** | 5 dakikada hızlı kredi ekleme | ⭐ Acil durumlar için başlayın buradan |
| **MANUEL-KREDI-EKLEME-KILAVUZU.md** | Detaylı sistem analizi ve kılavuz | Tam döküman (sistem mimarisi, sorun giderme) |
| **manuel-kredi-ekleme.sql** | SQL script koleksiyonu | Copy-paste SQL komutları |

---

## 🎯 Hangi Dosyayı Kullanmalıyım?

### Acil Durum (5 dakika)
→ **HIZLI-REFERANS.md** - Tek komutla kredi ekle

### İlk Kez Ekleme Yapıyorsanız
→ **MANUEL-KREDI-EKLEME-KILAVUZU.md** - Önce sistemi anlayın

### SQL Tercih Ediyorsanız
→ **manuel-kredi-ekleme.sql** - Hazır SQL komutları

---

## ⚡ Hızlı Başlangıç

### Tek Komutla (Laravel Tinker):

```bash
php artisan tinker
```

```php
$tenant = App\Models\Tenant::find(2);
$amount = 100000;

$purchase = Modules\AI\App\Models\AICreditPurchase::create([
    'tenant_id' => $tenant->id,
    'user_id' => null,
    'package_id' => 1,
    'credit_amount' => $amount,
    'price_paid' => 0.00,
    'amount' => 0.00,
    'currency' => 'TRY',
    'status' => 'completed',
    'payment_method' => 'admin_grant',
    'notes' => 'Manuel ekleme',
    'purchased_at' => now(),
]);

$tenant->increment('ai_credits_balance', $amount);
$tenant->ai_credits_balance = $tenant->fresh()->ai_credits_balance;
$tenant->save();

Cache::forget("tenant_credits_{$tenant->id}");
```

---

## 🏗️ Sistem Mimarisi (Özet)

Kredi **3 yerde** saklanır:

```
1. ai_credit_purchases → Satın alma geçmişi
2. tenants.ai_credits_balance → Gerçek bakiye (kolon)
3. tenants.data JSON → Virtual Column (Stancl Tenancy)
```

**KRİTİK:** 3'ü de güncelleyin, yoksa kredi görünmez!

---

## ⚠️ En Sık Yapılan Hatalar

### ❌ Sadece purchases ekleme
→ Tenant kolonunu güncellemeyi unutmak

### ❌ JSON'u güncellememek
→ **En kritik hata!** Eloquent Model JSON'u öncelikli kullanır

### ❌ Cache temizlememek
→ Eski değerler görünmeye devam eder

---

## 📊 Doğrulama

Her işlemden sonra bu query ile kontrol edin:

```sql
SELECT
    'Purchases' as kaynak, SUM(credit_amount) as toplam
FROM ai_credit_purchases WHERE tenant_id=2 AND status='completed'
UNION ALL
SELECT 'Tenant Kolon', ai_credits_balance FROM tenants WHERE id=2
UNION ALL
SELECT 'Data JSON', JSON_EXTRACT(data, '$.ai_credits_balance') FROM tenants WHERE id=2;
```

**Başarılı:** 3 satır da aynı değer ✅

---

## 🆘 Sorun Giderme

### Kredi görünmüyor?

```bash
# 1. Cache temizle
php artisan cache:clear

# 2. JSON'u kontrol et ve düzelt
mariadb -u USER -p DB -e "
UPDATE tenants
SET data = JSON_SET(data, '\$.ai_credits_balance', ai_credits_balance)
WHERE id = 2;"
```

---

## 🚀 Gelecek Geliştirmeler

Detaylı kılavuzda şunlar anlatılıyor:

- ✅ Otomatik sync Observer sistemi
- ✅ Admin Panel UI entegrasyonu
- ✅ Artisan Command oluşturma
- ✅ Webhook/API endpoint

---

## 📞 Destek

**Sorun yaşarsanız:**

1. `MANUEL-KREDI-EKLEME-KILAVUZU.md` → Sorun Giderme bölümü
2. Log dosyaları: `storage/logs/laravel.log`
3. Debug mode: `.env` → `APP_DEBUG=true`

**Log kontrol:**

```bash
tail -f storage/logs/laravel.log | grep -i credit
```

---

## 📚 İlgili Dosyalar

**Core Files:**
- `/app/Helpers/CreditHelpers.php` - Helper fonksiyonları
- `/Modules/AI/app/Services/AICreditService.php` - Kredi servisi
- `/app/Models/Tenant.php` - Tenant modeli (Virtual Column)

**Routes:**
- `/Modules/AI/routes/admin.php` - Admin kredi route'ları

---

## ✅ Son Kontrol Listesi

**Her kredi eklemeden sonra:**

- [ ] Purchase kaydı var mı?
- [ ] Tenant kolonu güncel mi?
- [ ] Data JSON güncel mi? ⚠️
- [ ] Cache temizlendi mi?
- [ ] Admin panelden bakiye doğru görünüyor mu?

---

**Tarih:** 27 Ekim 2025
**Versiyon:** 1.0
**Yazar:** Claude AI + Nurullah

---

💡 **İpucu:** İlk kez kullanıyorsanız `HIZLI-REFERANS.md` ile başlayın!
