# 🎯 AI V2 TEST PLANI - ÖZET VE YÜRÜTME REHBERİ

## 📊 TEST FAZLARı ÖZET TABLOSU

| Faz | Konu | Süre | Öncelik | Bağımlılık |
|-----|------|------|---------|------------|
| **Phase 1** | Priority Engine | 1-2 gün | Yüksek | - |
| **Phase 2** | Response Template | 2-3 gün | Yüksek | Phase 1 |
| **Phase 3** | Kredi Sistemi | 1-2 gün | Orta | - |
| **Phase 4** | Frontend Entegrasyon | 3-4 gün | Orta | Phase 3 |
| **Phase 5** | User Kredileri | 2-3 gün | Düşük | Phase 3 |

## 🔄 TEST YÜRÜTME SIRASI

### 1️⃣ BAŞLANGIÇ TESTLERİ
```bash
# Mevcut sistemin çalıştığını doğrula
php artisan ai:health-check
php artisan test --filter=AIModuleTest
```

### 2️⃣ PHASE 1 - PRIORITY ENGINE
**Test Sayfaları:**
- `/admin/ai/features` - Feature listesi
- `/admin/ai/prompts` - Prompt priority görüntüleme
- `/admin/ai/test-priority` - Priority test aracı

**Test Adımları:**
1. SEO feature'da brand context OFF olmalı
2. Blog feature'da brand context ON olmalı
3. Code feature'da minimal context

**Başarı Kriterleri:**
- ✅ Feature-based priority mapping çalışıyor
- ✅ Gereksiz prompt'lar filtreleniyor
- ✅ Performance %20'den fazla düşmemiş

### 3️⃣ PHASE 2 - RESPONSE TEMPLATES
**Test Sayfaları:**
- `/admin/ai/features/blog-yaz` - Blog template test
- `/admin/ai/features/seo-analiz` - SEO template test
- `/admin/ai/template-preview` - Template önizleme

**Test Adımları:**
1. Blog yazısında 1-2-3 formatı OLMAMALI
2. SEO analizinde tablo formatı olmalı
3. Template validation çalışmalı

**Başarı Kriterleri:**
- ✅ Monoton format kırılmış
- ✅ Dynamic template rendering
- ✅ Section-based yapı çalışıyor

### 4️⃣ PHASE 3 - KREDİ SİSTEMİ
**Test Sayfaları:**
- `/admin/ai/credits` - Kredi yönetimi
- `/admin/ai/packages` - Paket yönetimi
- `/admin/ai/provider-settings` - Provider çarpanları

**Test Adımları:**
1. "Token" kelimesi hiçbir yerde olmamalı
2. Provider multiplier'lar doğru hesaplanmalı
3. Tenant discount/markup çalışmalı

**Başarı Kriterleri:**
- ✅ Tüm UI "kredi" olarak güncellendi
- ✅ GPT-4 10x, Claude Opus 15x çarpan
- ✅ Package sistem çalışıyor

### 5️⃣ PHASE 4 - FRONTEND
**Test Sayfaları:**
- `/api/ai/v1/chat` - Public API
- `/test-ai-widget` - Widget test
- `/ai-chat-demo` - Alpine.js demo

**Test Adımları:**
1. Rate limiting test (5/dakika)
2. Widget yükleme testi
3. Alpine.js interaction test

**Başarı Kriterleri:**
- ✅ API endpoints çalışıyor
- ✅ Rate limiting aktif
- ✅ Widget render ediliyor
- ✅ Real-time chat çalışıyor

### 6️⃣ PHASE 5 - USER KREDİLERİ
**Test Sayfaları:**
- `/admin/ai/credit-mode` - Mode ayarları
- `/ai/credits/purchase` - User satın alma
- `/admin/ai/user-credits` - User kredi yönetimi

**Test Adımları:**
1. Tenant-only mode test
2. User-only mode test
3. Mixed mode test
4. Unlimited mode test

**Başarı Kriterleri:**
- ✅ Her mod doğru çalışıyor
- ✅ Credit consumption doğru
- ✅ Monthly limits çalışıyor

## 🔍 ENTEGRASYON TESTLERİ

### End-to-End Test Senaryosu
1. **Admin Panel**
   - Feature oluştur (custom priority)
   - Template tanımla (no numbering)
   - Credit package oluştur

2. **Frontend**
   - Widget'ı sayfaya ekle
   - Guest olarak chat başlat
   - Rate limit'e takıl
   - Login ol ve devam et

3. **Credit Flow**
   - User credit satın al
   - AI feature kullan
   - Credit azalmasını gözle
   - Monthly limit'e ulaş

## 📝 TEST DÖKÜMANTASYONU

### Her Faz İçin Gerekli
- [ ] Test case listesi
- [ ] Expected vs Actual results
- [ ] Bug/issue kaydı
- [ ] Performance metrikleri
- [ ] User feedback

### Test Araçları
```bash
# Unit Tests
php artisan test --parallel

# Feature Tests
php artisan test --filter=AI

# Browser Tests (Dusk)
php artisan dusk --group=ai

# API Tests
php artisan ai:test-api --comprehensive

# Performance
php artisan ai:benchmark --iterations=100
```

## 🚨 KRİTİK TEST NOKTALARI

1. **Geriye Uyumluluk**
   - Eski API'ler çalışmalı
   - Token → Credit migration sorunsuz

2. **Performance**
   - Response time max +%20
   - Database query count kontrol

3. **Security**
   - Rate limiting aktif
   - Credit validation sıkı
   - SQL injection koruması

4. **UX**
   - Loading states
   - Error messages Türkçe
   - Credit gösterimi anlık

## ✅ ONAY KRİTERLERİ

### Phase 1 ✓
- [ ] Feature priority çalışıyor
- [ ] Brand context filtreleme OK
- [ ] Performance acceptable

### Phase 2 ✓
- [ ] Template engine çalışıyor
- [ ] Monoton format yok
- [ ] Dynamic rendering OK

### Phase 3 ✓
- [ ] Credit terminology güncel
- [ ] Provider multipliers OK
- [ ] Package system çalışıyor

### Phase 4 ✓
- [ ] API endpoints aktif
- [ ] Widget çalışıyor
- [ ] Frontend interactive

### Phase 5 ✓
- [ ] User credits çalışıyor
- [ ] Mode switching OK
- [ ] Purchase flow complete

## 🎯 BAŞARI METRİKLERİ

- Response format çeşitliliği: %80+ improvement
- API response time: <2 saniye
- Credit calculation accuracy: %100
- Widget load time: <500ms
- User satisfaction: 4.5+ / 5

---

**NOT**: Her faz tamamlandığında `say "Phase X tamamlandı"` komutu çalıştırılacak.