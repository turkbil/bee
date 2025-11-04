# 🎯 AI SOHBET AKIŞI SİSTEMİ - BASİT ANLATIM

## NE YAPACAĞIZ?

Admin panelinde **görsel akış çizme** sistemi yapacağız:
- Kutucuklar sürükle-bırak ile yerleştir
- Her tenant (kiracı) kendi akışını ayarlasın
- Kutucuklar = Hazır işlevler (Ortak + Tenant'a özel)
- AI bu akışa göre çalışsın
- **İxtif.com öncelikli** - E-ticaret satış akışı

---

## NASIL ÇALIŞIR?

### 1. Admin Paneli - Akış Çizimi

```
Admin açar:
┌─────────────────────────────────┐
│  Sohbet Akışı Tasarlayıcı       │
├─────────────────────────────────┤
│  Sol taraf: Kutucuk listesi     │
│  ┌──────────────┐               │
│  │ 📝 AI Yanıt  │               │
│  │ 📦 Ürün Göster│              │
│  │ 💰 Fiyat Ver │               │
│  │ 📞 Numara Al │               │
│  └──────────────┘               │
│                                  │
│  Sağ taraf: Çizim alanı         │
│  ┌─────┐   ┌────────┐           │
│  │BAŞLA│──►│Selamlama│           │
│  └─────┘   └───┬────┘           │
│                │                 │
│           ┌────▼────┐            │
│           │Ürün Göster│          │
│           └────┬────┘            │
│                │                 │
│           ┌────▼────┐            │
│           │Fiyat Ver│            │
│           └────┬────┘            │
│                │                 │
│           ┌────▼────┐            │
│           │ BİTİŞ   │            │
│           └─────────┘            │
│                                  │
│  [Kaydet]                        │
└─────────────────────────────────┘
```

### 2. Veritabanına Kayıt

Admin kaydet deyince:
```
Veritabanına şöyle kaydeder:

Kiracı ID: 2
Akış Adı: "E-ticaret Akışı"
Kutucuklar:
  - Kutucuk 1:
      Tip: AI Yanıt
      İsim: "Selamlama"
      Ayar: "Müşteriyi sıcak karşıla"
      Sonraki: Kutucuk 2

  - Kutucuk 2:
      Tip: Ürün Göster
      İsim: "Ürün Göster"
      Ayar: Anasayfadakiler, 5 tane
      Sonraki: Kutucuk 3
```

### 3. Kullanıcı Mesaj Gönderince

```
Kullanıcı: "Merhaba, transpalet arıyorum"
    ↓
Sistem akışı okur
    ↓
[1. Karşılama] çalışır
    ↓
AI: "Hoş geldiniz! Transpalet için size yardımcı olabilirim"
    ↓
[2. Kategori Tespit] çalışır
    ↓
Kategori: TRANSPALET tespit edildi
    ↓
[3. Ürün Önerme] çalışır
    ↓
Anasayfa + Stok öncelikli sıralama
    ↓
AI: "İşte en çok tercih edilen transpaletlerimiz: F4, F6..."
    ↓
[4. Fiyat/Detay] bekler
```

---

## SİSTEM PARÇALARI

### 1. Kutucuk Türleri (Hazır İşlevler)

**ORTAK KUTUCUKLAR (Tüm Tenant'lar):**
```
- AIResponseNode.php         → AI'a talimat gönder
- ConditionNode.php          → Eğer/o zaman mantığı
- CollectDataNode.php        → Veri topla (telefon, email)
- ShareContactNode.php       → İletişim bilgisi paylaş
```

**İXTİF.COM ÖZEL KUTUCUKLAR:**
```
- CategoryDetectionNode.php  → Kategori tespit (transpalet, forklift)
- ProductRecommendNode.php   → Ürün öner (anasayfa + stok öncelik)
- PriceFilterNode.php        → Ucuz/pahalı filtreleme
- CurrencyConvertNode.php    → USD → TL dönüşüm (exchange_rates)
- StockCheckNode.php         → Stok durumu kontrolü
- ComparisonNode.php         → Ürün karşılaştırma (F4 vs F6)
- QuotationNode.php          → Teklif hazırlama
```

Her kutucuk = 1 PHP dosyası = 1 işlev

### 2. Veritabanı Tablosu

```
Tablo adı: tenant_conversation_flows

Kolonlar:
- id                → Kayıt numarası
- tenant_id         → Hangi kiracı
- flow_name         → Akış adı
- flow_data         → Kutucuklar ve bağlantılar
- is_active         → Aktif mi?
- created_at        → Ne zaman oluştu
```

### 3. Kiracı Ayarları

**Önerilen yöntem:**
```
Tablo adı: ai_tenant_directives

Kolonlar:
- id                → Kayıt numarası
- tenant_id         → Hangi kiracı
- directive_key     → Ayar adı (örn: "selamlama_tipi")
- directive_value   → Ayar değeri (örn: "resmi")
```

**Neden bu yöntem?**
- Arama kolay
- Kontrol kolay
- Güncelleme kolay

---

## ADMİN PANELİ SAYFALARI

### Sayfa 1: Akış Listesi

```
┌────────────────────────────────────────┐
│  Sohbet Akışları                       │
├────────────────────────────────────────┤
│  Akış Adı          Durum      İşlem    │
│  ────────────────────────────────────  │
│  E-ticaret Akışı   ✅ Aktif   [Düzenle]│
│  Hizmet Akışı      ⏸ Pasif    [Düzenle]│
│                                         │
│  [+ Yeni Akış Oluştur]                 │
└────────────────────────────────────────┘
```

### Sayfa 2: Akış Tasarlayıcı (Drawflow sistemi)

```
Ekranda:
  - Sol tarafta hazır kutucuklar (sürükleyebilirsin)
  - Sağ tarafta çizim alanı
  - Kutucukları sürükle-bırak
  - Çizgilerle birbirine bağla
  - Kaydet butonuna bas

Sistem arka planda:
  - Çizdiğin akışı JSON'a çevirir
  - Veritabanına kaydeder
```

### Sayfa 3: Kiracı Ayarları

```
┌────────────────────────────────────────┐
│  AI Davranış Ayarları                  │
├────────────────────────────────────────┤
│  Selamlama Tarzı:                      │
│  ○ Resmi  ● Samimi  ○ Profesyonel     │
│                                         │
│  Fiyat Göster:                         │
│  ☑ Evet   ☐ Hayır                      │
│                                         │
│  Emoji Kullan:                         │
│  ☑ Evet   ☐ Hayır                      │
│                                         │
│  En Fazla Kaç Ürün Göster:             │
│  [5]                                   │
│                                         │
│  [Kaydet]                              │
└────────────────────────────────────────┘
```

---

## İXTİF.COM SATIŞ AKIŞI ÖRNEĞİ

**10 Adımlık E-Ticaret Akışı:**

```
1. KARŞILAMA → Müşteriyi sıcak karşıla
2. KATEGORİ TESPİT → Ne arıyor? (transpalet/forklift)
3. ÜRÜN ÖNERME → Anasayfa + stok öncelikli
4. FİYAT FİLTRE → Ucuz/pahalı tercihi
5. PARA BİRİMİ → USD veya TL göster
6. KUR DÖNÜŞÜM → Güncel kurdan TL hesapla
7. ÜRÜN DETAY → Teknik özellikler
8. TELEFON AL → Lead toplama
9. İLETİŞİM PAYLAŞ → WhatsApp/telefon ver
10. MAİL/ADRES → İletişim bilgileri
```

**Kategori Odaklı Çalışma:**
- Transpalet sorulunca → Sadece transpalet göster
- Başka kategori → Kullanıcı özel isterse
- Kategori içinde kal → Dışına çıkma

## ÇALIŞIRKEN NE OLUR?

```
Kullanıcı: "Transpalet arıyorum"
    ↓
Sistem:
  1. Kategori tespit: TRANSPALET ✓
  2. Transpalet node'u çalıştır
  3. shop_products'tan transpaletleri çek
  4. Anasayfa + stok sıralaması yap
  5. AI'a ürün listesini ver
  6. AI: "İşte transpaletlerimiz..."
  7. Sonraki: Fiyat/detay node'u bekle
  8. Kaydet
  9. Kullanıcıya gönder
```

---

## HANGİ SİSTEMİ KULLANACAĞIZ?

**Drawflow** (Sürükle-bırak sistemi)

**Neden bu?**
- ✅ JavaScript (Laravel'de çalışır)
- ✅ Sürükle-bırak editör
- ✅ Çizimi veritabanına kaydeder
- ✅ Bedava
- ✅ Kolay kurulum

**Link:** https://jerosoler.github.io/Drawflow/ (demosu)

---

## ÖZETLE

1. Admin panelde **Drawflow** ile akış çiz
2. Çizimi **veritabanına kaydet**
3. Kullanıcı mesaj gönderince **akışı oku**
4. Her kutucuk **kendi işlevini yapar**
5. AI **yanıt verir**
6. **Sonraki kutucuğa geç**

**Basit, anlaşılır, her kiracı kendi akışını ayarlar.**
