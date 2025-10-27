# Dinamik AI Placeholder Konuşmaları - Plan

**Tarih**: 2025-01-13
**Konu**: Ürün sayfalarında AI chatbot için ürüne özel, dinamik placeholder konuşmaları

## Sorun
Şu anda AI chatbot widget'ında sabit forklift placeholder konuşmaları var. Her ürün için aynı örnek konuşma gösteriliyor, bu da kullanıcıları yanlış yönlendirebiliyor.

## Çözüm
Her ürün için AI ile gerçekçi placeholder konuşmaları üretip cache'lemek. İlk açılışta otomatik üretim, sonrasında cache'den gösterme.

## Mimari

### 1. Database (Cache)
```sql
product_chat_placeholders
- id
- product_id (unique)
- conversation_json (JSON - [{role, text}])
- generated_at
- created_at, updated_at
```

### 2. Backend Service
`App\Services\AI\ProductPlaceholderService.php`
- Ürün bilgilerini topla (title, description, specs)
- AI'ya gerçekçi 3 soru-cevap ürettir
- Cache'e kaydet
- Gerekirse yeniden üret

### 3. API Endpoint
`GET /api/ai/product-placeholder/{productId}`
- Cache'de varsa döndür
- Yoksa üret, cache'le ve döndür
- JSON response: conversation array

### 4. Frontend (Alpine.js)
- placeholderV4() fonksiyonunu güncelle
- API'den placeholder çek
- Yüklenene kadar generic placeholder göster

## Dosya Yapısı

```
Modules/Shop/
├── database/migrations/
│   └── xxxx_create_product_chat_placeholders_table.php
│
app/Services/AI/
└── ProductPlaceholderService.php

Modules/AI/
├── app/Http/Controllers/Api/
│   └── ProductPlaceholderController.php
└── routes/api.php

resources/views/components/ai/
├── chat-store.blade.php (placeholderV4 güncelleme)
└── inline-widget.blade.php (productId geçme)
```

## İmplementasyon Adımları

### ✅ TODO

- [ ] Migration oluştur (product_chat_placeholders tablosu)
- [ ] ProductPlaceholderService oluştur
- [ ] API endpoint ekle
- [ ] Frontend'i dinamik sisteme güncelle
- [ ] Test ve doğrula

## Örnek AI Prompt
```
Sen bir ürün satış uzmanısın. Aşağıdaki ürün için müşterinin sorabileceği 3 gerçekçi soru ve sen bunlara verebileceğin profesyonel cevapları yaz:

Ürün: {title}
Açıklama: {description}
Özellikler: {specs}

Formatı JSON olarak ver:
[
  {"role": "user", "text": "soru 1"},
  {"role": "assistant", "text": "cevap 1"},
  ...
]

Türkçe yaz. Gerçekçi, doğal ve satış odaklı ol.
```

## Avantajlar
✅ Her ürün için özel konuşma
✅ Gerçekçi ve ilgili sorular
✅ Cache ile hızlı yükleme
✅ SEO ve kullanıcı deneyimi artışı

## Risk ve Çözümler
- **Risk**: AI yanıt süresi uzun → **Çözüm**: Cache + background job
- **Risk**: AI maliyeti → **Çözüm**: Sadece ilk kez üret, sonra cache
- **Risk**: Yanlış bilgi → **Çözüm**: Template + ürün data validation

---

## İlerleme

### ✅ TAMAMLANDI - SISTEM HAZIR!

#### 1. Database Layer
- ✅ Migration: `product_chat_placeholders` tablosu oluşturuldu
- ✅ Model: `ProductChatPlaceholder` model oluşturuldu
- ✅ Cache mekanizması hazır

#### 2. Backend Services
- ✅ `ProductPlaceholderService` oluşturuldu
  - AI ile gerçekçi, dikkat çekici placeholder üretimi
  - Ürüne özel, detaylı soru-cevaplar
  - Cache sistemi (ilk kez üret, sonra cache'den oku)
  - Fallback konuşmaları

#### 3. API Endpoints
- ✅ Route: `GET /api/ai/v1/product-placeholder/{productId}`
- ✅ Controller method: `PublicAIController::getProductPlaceholder()`
- ✅ JSON response: conversation array

#### 4. Frontend (Alpine.js)
- ✅ `placeholderV4()` fonksiyonu dinamik hale getirildi
- ✅ API'den placeholder çekme
- ✅ Loading state
- ✅ Error handling + fallback

#### 5. AI Prompt İyileştirmesi
- ✅ Genel sorular YASAK
- ✅ Ürüne özel, detaylı sorular ZORUNLU
- ✅ Teknik özellikler ve faydalar vurgulanacak
- ✅ Dikkat çekici ve satış odaklı

## Kullanım

### Otomatik Çalışma
1. Ürün sayfası açılır
2. AI chatbot widget'ı yüklenir
3. ProductId widget'a geçilir
4. API otomatik çağrılır:
   - Cache varsa → Anında gösterir (hızlı + ücretsiz)
   - Cache yoksa → AI üretir + cache'ler (1 kez maliyet) → Gösterir

### Manuel Test
```bash
# Bir ürün için placeholder üret/getir
curl http://www.laravel.test/api/ai/v1/product-placeholder/1
```

## Avantajlar

✅ **Maliyet Tasarrufu**: Her ürün için sadece 1 kez AI kullanımı
✅ **Hız**: Cache'den anlık yükleme
✅ **Ürüne Özel**: Her ürün kendi özelliklerine göre konuşma
✅ **Dikkat Çekici**: Sıradan değil, satış odaklı konuşmalar
✅ **SEO Friendly**: Ürün sayfaları daha etkileşimli
✅ **Otomatik**: Manuel işlem yok, sistem otomatik üretir

## Test

Gerçek site URL'inde test edilmeli:
1. Bir ürün sayfasına git
2. AI chatbot widget'ını aç
3. Placeholder konuşmaları otomatik yüklenecek
4. İlk yüklemede AI üretir (biraz bekle)
5. Sonraki yüklemelerde cache'den anında gösterir

---

**DURUM**: SİSTEM HAZIR VE KULLANIMA HAZIR!
