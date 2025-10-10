# 📋 PHASE 3: KREDİ SİSTEMİ DÖNÜŞÜMÜ TEST PLANI

## Test Edilecek Dosyalar

### 1. Migration Files
**Lokasyon**: `Modules/AI/database/migrations/`
**Test Özellikleri**:
- Token → Credit rename migrations
- New credit package structure
- Provider multiplier tables

### 2. AICreditService.php (Yeni)
**Lokasyon**: `Modules/AI/app/Services/AICreditService.php`
**Test Özellikleri**:
- Credit calculation
- Provider multipliers
- Tenant discounts/markups
- Credit consumption

### 3. AI Models Update
**Lokasyon**: `Modules/AI/app/Models/`
- `AICredit.php` (eski AIToken)
- `AICreditPackage.php` (eski AITokenPackage)
- `AICreditPurchase.php` (eski AITokenPurchase)

### 4. Livewire Components
**Lokasyon**: `Modules/AI/app/Http/Livewire/Admin/`
- `AICreditManageComponent.php`
- `AICreditPackageComponent.php`
- `AICreditPurchaseComponent.php`

## Test Senaryoları

### Test 1: Credit Terminology
**Test Sayfaları**:
- `/admin/ai/credits` (eski /admin/ai/tokens)
- `/admin/ai/credit-packages`
- `/admin/ai/credit-purchases`

**Kontrol Edilecek**:
- UI'da "token" kelimesi olmamalı
- Tüm yerler "kredi" olmalı
- Lang dosyaları güncel

### Test 2: Provider Multipliers
**Test Sayfası**: `/admin/ai/test-credit-calculation`
**Kontrol Edilecek**:
```
OpenAI GPT-3.5: 100 kredi = 100 kredi (1x)
OpenAI GPT-4: 100 kredi = 1000 kredi (10x)
Claude Haiku: 100 kredi = 120 kredi (1.2x)
Claude Opus: 100 kredi = 1500 kredi (15x)
DeepSeek Chat: 100 kredi = 50 kredi (0.5x)
```

### Test 3: Tenant Pricing
**Test Sayfası**: `/admin/ai/tenant-pricing`
**Kontrol Edilecek**:
- %20 indirim: 100 kredi → 80 kredi
- %50 zam: 100 kredi → 150 kredi
- Kombinasyon: Provider + Tenant pricing

### Test 4: Package System
**Test Sayfası**: `/admin/ai/packages`
**Kontrol Edilecek**:
- Tenant paketleri (büyük)
- User paketleri (küçük)
- Bonus krediler
- Featured packages

## Database Test Queries

```sql
-- Token to Credit migration check
SELECT COUNT(*) FROM ai_credits;
SELECT COUNT(*) FROM ai_credit_packages;
SELECT COUNT(*) FROM ai_credit_purchases;

-- Provider multipliers
SELECT * FROM ai_provider_settings;

-- Package types
SELECT type, COUNT(*) 
FROM ai_credit_packages 
GROUP BY type;
```

## Test Komutları

```bash
# Migration test
php artisan migrate:fresh --path=Modules/AI/database/migrations

# Credit calculation test
php artisan ai:test-credits --provider=openai --model=gpt-4 --amount=100

# Package seeder
php artisan module:seed AI --class=AICreditPackageSeeder
```