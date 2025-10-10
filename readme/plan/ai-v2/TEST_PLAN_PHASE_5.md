# 📋 PHASE 5: USER-BASED KREDİ SİSTEMİ TEST PLANI

## Test Edilecek Dosyalar

### 1. User Credit Tables
**Migration**: `create_ai_user_credits_table.php`
**Test Özellikleri**:
- User credit balance
- Monthly limits
- Purchase history
- Usage tracking

### 2. Hybrid Credit Service
**Lokasyon**: `Modules/AI/app/Services/AIHybridCreditService.php`
**Test Özellikleri**:
- Tenant-first logic
- User-first logic
- Mixed mode
- Unlimited mode

### 3. User Credit Models
**Lokasyon**: `Modules/AI/app/Models/`
- `AIUserCredit.php`
- `AIUserCreditPurchase.php`
- `AIUserCreditUsage.php`

### 4. Credit Mode Settings
**Lokasyon**: `Modules/AI/app/Http/Livewire/Admin/AICreditModeComponent.php`
**Test Özellikleri**:
- Mode switching
- Reseller settings
- Unlimited toggle

## Test Senaryoları

### Test 1: Tenant Mode
**Test Sayfası**: `/admin/ai/credit-mode`
**Ayarlar**:
```php
is_ai_reseller = false
credit_priority = 'tenant_first'
unlimited_user_credits = false
```
**Kontrol Edilecek**:
- Sadece tenant kredisi kullanılmalı
- User kredisi gösterilmemeli
- Tenant kredisi bitince hata

### Test 2: User Mode
**Test Sayfası**: `/admin/ai/credit-mode`
**Ayarlar**:
```php
is_ai_reseller = true
credit_priority = 'user_first'
unlimited_user_credits = false
```
**Kontrol Edilecek**:
- Önce user kredisi kullanılmalı
- User bitince tenant kredisine geçmeli
- Her ikisi de gösterilmeli

### Test 3: Mixed Mode
**Test Sayfası**: `/admin/ai/test-mixed-credits`
**Kontrol Edilecek**:
- User: 50 kredi, Tenant: 100 kredi
- 30 kredi kullanım → User: 20, Tenant: 100
- 30 kredi daha → User: 0, Tenant: 90
- Doğru hesaplama

### Test 4: Unlimited Mode
**Test Sayfası**: `/admin/ai/test-unlimited`
**Ayarlar**:
```php
is_ai_reseller = false
unlimited_user_credits = true
```
**Kontrol Edilecek**:
- Kredi kontrolü yapılmamalı
- Sınırsız kullanım
- Usage log tutulmalı

## Test Database Queries

```sql
-- User credit check
SELECT u.name, uc.balance, uc.monthly_limit, uc.monthly_used
FROM users u
JOIN ai_user_credits uc ON u.id = uc.user_id;

-- Credit mode check
SELECT id, domain, is_ai_reseller, credit_priority, unlimited_user_credits
FROM tenants;

-- Usage tracking
SELECT user_id, SUM(credits_used) as total_used
FROM ai_user_credit_usage
GROUP BY user_id;
```

## Test Flows

### Flow 1: User Credit Purchase
1. Login as user
2. Go to `/ai/credits/purchase`
3. Select "Mini Package" (100 credits)
4. Complete purchase
5. Check balance updated

### Flow 2: Credit Consumption
1. User has 100 credits
2. Use AI feature (costs 10 credits)
3. Check User: 90 credits
4. Check tenant credits also reduced
5. Check usage log created

### Flow 3: Monthly Limit
1. Set monthly limit: 500 credits
2. Use 500 credits
3. Try to use more → Error
4. Wait for reset_at date
5. Credits available again

## Test Komutları

```bash
# Create test user with credits
php artisan ai:create-test-user --credits=1000

# Test credit modes
php artisan ai:test-credit-mode --mode=tenant_first
php artisan ai:test-credit-mode --mode=user_first
php artisan ai:test-credit-mode --mode=mixed

# Test consumption
php artisan ai:test-consumption --user=1 --amount=50

# Monthly limit test
php artisan ai:test-monthly-limit --user=1
```