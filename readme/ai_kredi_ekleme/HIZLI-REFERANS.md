# ⚡ Hızlı Referans: AI Kredi Ekleme

**5 Dakikada Manuel Kredi Ekleme**

---

## 🚀 Tek Komut (Laravel Tinker - ÖNERİLEN)

```bash
php artisan tinker
```

```php
$tenantId = 2;              // Tenant ID
$creditAmount = 100000;     // Eklenecek kredi

// Tenant bul
$tenant = App\Models\Tenant::find($tenantId);

// Purchase kaydı
$purchase = Modules\AI\App\Models\AICreditPurchase::create([
    'tenant_id' => $tenant->id,
    'user_id' => null,
    'package_id' => 1,
    'credit_amount' => $creditAmount,
    'price_paid' => 0.00,
    'amount' => 0.00,
    'currency' => 'TRY',
    'status' => 'completed',
    'payment_method' => 'admin_grant',
    'notes' => 'Manuel ekleme - ' . now(),
    'purchased_at' => now(),
]);

// Tenant güncelle
$tenant->increment('ai_credits_balance', $creditAmount);
$tenant->ai_credits_balance = $tenant->fresh()->ai_credits_balance;
$tenant->save();

// Cache temizle
Cache::forget("tenant_credits_{$tenant->id}");

// Sonuç
echo "✅ " . format_credit($creditAmount) . " eklendi!\n";
echo "Yeni bakiye: " . format_credit($tenant->fresh()->ai_credits_balance);
```

---

## 💾 SQL ile Manuel (3 Adım)

### 1. Purchase Kaydı

```sql
INSERT INTO ai_credit_purchases (
    tenant_id, user_id, package_id, credit_amount,
    price_paid, amount, currency, status,
    payment_method, notes, purchased_at, created_at, updated_at
) VALUES (
    2, NULL, 1, 100000,
    0.00, 0.00, 'TRY', 'completed',
    'admin_grant', 'Manuel ekleme', NOW(), NOW(), NOW()
);
```

### 2. Tenant Kolonu

```sql
UPDATE tenants
SET ai_credits_balance = ai_credits_balance + 100000
WHERE id = 2;
```

### 3. Data JSON (KRİTİK!)

```sql
UPDATE tenants
SET data = JSON_SET(data, '$.ai_credits_balance', ai_credits_balance)
WHERE id = 2;
```

### 4. Cache Temizle

```bash
php artisan cache:clear
php artisan tinker --execute="Cache::forget('tenant_credits_2');"
```

---

## ✅ Doğrulama

```sql
SELECT
    'Purchases' as kaynak, SUM(credit_amount) as toplam
FROM ai_credit_purchases
WHERE tenant_id=2 AND status='completed'
UNION ALL
SELECT 'Tenant Kolon', ai_credits_balance FROM tenants WHERE id=2
UNION ALL
SELECT 'Data JSON', JSON_EXTRACT(data, '$.ai_credits_balance') FROM tenants WHERE id=2;
```

**Başarılı çıktı:** 3 satır da aynı değeri göstermeli!

---

## 🔧 Sorun Giderme (1 Dakika)

### Kredi görünmüyor?

```bash
# 1. Cache temizle
php artisan cache:clear

# 2. JSON kontrolü
mariadb -u USER -p'PASS' DB -e "
SELECT ai_credits_balance as kolon,
JSON_EXTRACT(data, '\$.ai_credits_balance') as json_data
FROM tenants WHERE id = 2;"

# 3. Farklıysa düzelt
mariadb -u USER -p'PASS' DB -e "
UPDATE tenants
SET data = JSON_SET(data, '\$.ai_credits_balance', ai_credits_balance)
WHERE id = 2;"
```

---

## 📋 Checklist

**İşlem öncesi:**
- [ ] Tenant ID doğru mu?
- [ ] Mevcut bakiye kaydedildi mi?

**İşlem:**
- [ ] Purchase kaydı oluşturuldu
- [ ] Tenant kolonu güncellendi
- [ ] Data JSON güncellendi ⚠️ **ASLA ATLAMA!**
- [ ] Cache temizlendi

**İşlem sonrası:**
- [ ] Admin panelden bakiye kontrol et
- [ ] 3 kaynak da aynı değeri gösteriyor mu?

---

## ⚠️ KRİTİK HATIRLATMA

**Data JSON güncellemesi ZORUNLU!**

Sisteminizde **Stancl Tenancy Virtual Column** var.
`data` JSON güncellemezseniz kredi **GÖRÜNMEyecektir!**

---

## 📚 Detaylı Bilgi

- **Tam Kılavuz:** `MANUEL-KREDI-EKLEME-KILAVUZU.md`
- **SQL Script:** `manuel-kredi-ekleme.sql`

---

**Son Güncelleme:** 27 Ekim 2025
