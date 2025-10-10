# 📋 PHASE 1: PRIORITY ENGINE TEST PLANI

## Test Edilecek Dosyalar

### 1. AIPriorityEngine.php
**Lokasyon**: `Modules/AI/app/Services/AIPriorityEngine.php`
**Test Özellikleri**:
- Feature-based priority mapping
- Dynamic context filtering  
- Brand context devre dışı bırakma
- Weight-based scoring sistemi

### 2. AIFeaturePriorityMap.php (Yeni)
**Lokasyon**: `Modules/AI/app/Config/AIFeaturePriorityMap.php`
**Test Özellikleri**:
- SEO features için düşük brand priority
- Blog features için yüksek brand priority
- Code features için minimal context

### 3. AIPromptController.php
**Lokasyon**: `Modules/AI/app/Http/Controllers/Admin/AIPromptController.php`
**Test Özellikleri**:
- Priority değerlerinin doğru uygulanması
- Context type filtreleme

## Test Senaryoları

### Test 1: SEO Analiz Feature
**Test Sayfası**: `/admin/ai/test-feature/seo-analiz`
**Kontrol Edilecek**:
- Marka bilgisi kullanılmamalı
- System prompts yüksek öncelikte
- Expert knowledge yüksek öncelikte
- Response format doğru uygulanmalı

### Test 2: Blog Yazma Feature  
**Test Sayfası**: `/admin/ai/test-feature/blog-yaz`
**Kontrol Edilecek**:
- Marka bilgisi kullanılmalı
- Tenant identity önemli
- Brand context yüksek öncelikte

### Test 3: Kod Üretme Feature
**Test Sayfası**: `/admin/ai/test-feature/kod-uret`
**Kontrol Edilecek**:
- Minimal context kullanımı
- Expert knowledge çok yüksek öncelikte
- Brand/tenant bilgisi gereksiz

## Test Komutları

```bash
# Unit Test
php artisan test --filter=AIPriorityEngineTest

# Feature Test  
php artisan test --filter=AIFeaturePriorityTest

# Debug Mode
php artisan ai:test-priority --feature=seo-analiz --debug
```