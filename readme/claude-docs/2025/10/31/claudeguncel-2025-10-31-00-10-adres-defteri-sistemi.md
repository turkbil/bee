# 📋 ADRES DEFTERİ SİSTEMİ PLANI
**Tarih:** 2025-10-31
**Konu:** Checkout için Adres Defteri Sistemi

---

## 🎯 NE YAPIYORUZ?

Şu anda checkout sayfasında kullanıcı her seferinde adres bilgilerini manuel yazıyor.

**SORUN:**
- Her siparişte aynı bilgileri tekrar tekrar yazmak zorunda
- Birden fazla adresi olan kullanıcı (fabrika, depo, ofis) her seferinde yazıyor
- Hızlı sipariş veremiyor

**ÇÖZÜM:**
Kullanıcılar adreslerini bir kere kaydedecek, sonraki siparişlerde listeden seçecek.

Tıpkı Amazon/Trendyol'da olduğu gibi:
- "Ev adresim"
- "İş yerim"
- "Fabrika"
- "Depo"

**HEDEF:**
1 Sepet = 1 İletişim + 1 Fatura + 1 Teslimat Adresi (kayıtlı listeden seçilir)

---

## 🗄️ VERİTABANINA NE EKLENİYOR?

### YENİ TABLO: Adres Defteri

Kullanıcının kayıtlı adreslerini tutacak tablo: **customer_addresses**

**İçinde ne var?**
- Kime ait? → `user_id` (hangi kullanıcının adresi)
- Ne tür adres? → `type` (iletişim mi, fatura mı, teslimat mı?)
- Adres başlığı → `title` ("Evim", "İş yerim", "Fabrika", "Depo")
- Varsayılan mı? → `is_default` (bir sonraki siparişte otomatik seçilsin mi?)

**Adres bilgileri:**
- İsim, email, telefon
- Şirket, vergi dairesi, vergi no (fatura için)
- Açık adres, şehir, ilçe, posta kodu

**Örnek kayıtlar:**
```
ID  user_id  type       title        is_default  name      city
1   5        contact    "Ali Veli"   ✅          Ali Veli  İstanbul
2   5        billing    "ABC Ltd"    ✅          ABC Ltd   İstanbul
3   5        shipping   "Fabrika"    ✅          -         Gebze
4   5        shipping   "Depo"       ❌          -         Ankara
```

Ali kullanıcısının:
- 1 iletişim bilgisi (varsayılan)
- 1 fatura adresi (varsayılan - şirketi)
- 2 teslimat adresi (Fabrika varsayılan, Depo ekstra)

---

### MEVCUT TABLO GÜNCELLENİYOR: Siparişler

**shop_orders** tablosuna yeni kolonlar ekliyoruz.

**NEDEN?**
Kullanıcı adresini silerse/değiştirirse eski siparişler bozulmasın diye.

**Adres ID'leri (Hangi adres kullanıldı?):**
- `contact_address_id` → Hangi iletişim bilgisi kullanıldı? (ID)
- `billing_address_id` → Hangi fatura adresi kullanıldı? (ID)
- `shipping_address_id` → Hangi teslimat adresi kullanıldı? (ID)

**Fatura Bilgileri (Snapshot - Kopyası):**
Sipariş anında fatura bilgilerini kopyalayıp saklıyoruz:
- `billing_company` → Şirket adı
- `billing_tax_office` → Vergi dairesi
- `billing_tax_number` → Vergi no
- `billing_address` → Fatura adresi
- `billing_city` → Fatura şehir
- `billing_district` → Fatura ilçe
- `billing_postal_code` → Fatura posta kodu

**MEVCUT kolonlar zaten var:**
- İletişim: `customer_name`, `customer_email`, `customer_phone` ✅
- Teslimat: `shipping_address`, `shipping_city`, `shipping_district` ✅

**YANİ:**
Sipariş oluşturulurken adres defterinden seçilen adresler **kopyalanıp** siparişe yapıştırılacak.
Kullanıcı yarın adresini değiştirse bile, eski siparişteki adres değişmeyecek.

---

## 🎨 KULLANICI NASIL KULLANACAK?

### CHECKOUT SAYFASINDA 3 BÖLÜM OLACAK:

**1. İletişim Bilgileri Bölümü:**
```
┌──────────────────────────────────────────────┐
│ 📧 İletişim Bilgileri                        │
│                                              │
│ ┌──────────────────────┐                    │
│ │ [Kayıtlı seç ▼]      │  [+ Yeni Ekle]     │
│ └──────────────────────┘                    │
│                                              │
│ Seçili:                                      │
│ ✅ Ali Veli - 0532 123 45 67                │
│                                              │
│ ☑ Bir sonraki siparişte otomatik kullan     │
│   (varsayılan yap)                           │
└──────────────────────────────────────────────┘
```

**NE OLUYOR?**
- Dropdown'a tıkla → Kayıtlı iletişim bilgilerini göster
- Birini seç → Otomatik dolsun
- Yoksa "Yeni Ekle" → Form aç → Kaydet

---

**2. Fatura Adresi Bölümü:**
```
┌──────────────────────────────────────────────┐
│ 📄 Fatura Adresi                             │
│                                              │
│ ☑ İletişim bilgisiyle aynı                  │
│   (işaretle, fatura bölümü gizlensin)       │
│                                              │
│ ┌──────────────────────┐                    │
│ │ [Kayıtlı seç ▼]      │  [+ Yeni Ekle]     │
│ └──────────────────────┘                    │
│                                              │
│ Seçili:                                      │
│ ✅ ABC Ltd. Şti. - Maslak/İstanbul          │
│    Vergi No: 1234567890                     │
│                                              │
│ ☑ Bir sonraki siparişte otomatik kullan     │
└──────────────────────────────────────────────┘
```

**NE OLUYOR?**
- Checkbox işaretle → İletişim bilgisiyle aynı olsun (kolay yol)
- Farklı fatura adresi → Dropdown'dan seç
- Şirket bilgisi varsa → Vergi dairesi, vergi no da gelsin

---

**3. Teslimat Adresi Bölümü:**
```
┌──────────────────────────────────────────────┐
│ 📦 Teslimat Adresi                           │
│                                              │
│ ☑ Fatura adresiyle aynı                     │
│   (işaretle, teslimat bölümü gizlensin)     │
│                                              │
│ ┌──────────────────────┐                    │
│ │ [Kayıtlı seç ▼]      │  [+ Yeni Ekle]     │
│ └──────────────────────┘                    │
│                                              │
│ Seçili:                                      │
│ ✅ Fabrika - Gebze Organize Sanayi          │
│    Gebze/Kocaeli                            │
│                                              │
│ ☑ Bir sonraki siparişte otomatik kullan     │
└──────────────────────────────────────────────┘
```

**NE OLUYOR?**
- Checkbox işaretle → Fatura adresiyle aynı olsun
- Farklı teslimat adresi → Dropdown'dan seç (fabrika, depo, ofis)
- Birden fazla teslimat adresi olabilir

---

### POPUP PENCERE 1: Adres Listesinden Seç

Kullanıcı [Kayıtlı seç ▼] tıklarsa popup açılacak:

```
╔══════════════════════════════════════════╗
║ Teslimat Adresi Seçin                    ║
╠══════════════════════════════════════════╣
║                                          ║
║ ┌────────────────────────────────────┐  ║
║ │ ⭐ Fabrika (Varsayılan)            │  ║
║ │ Gebze Organize Sanayi              │  ║
║ │ Gebze/Kocaeli, 41400               │  ║
║ │                                    │  ║
║ │            [Seç]  [Düzenle] [Sil] │  ║
║ └────────────────────────────────────┘  ║
║                                          ║
║ ┌────────────────────────────────────┐  ║
║ │ Depo - Ankara                      │  ║
║ │ Ostim Sanayi Sitesi                │  ║
║ │ Yenimahalle/Ankara, 06370          │  ║
║ │                                    │  ║
║ │            [Seç]  [Düzenle] [Sil] │  ║
║ └────────────────────────────────────┘  ║
║                                          ║
║ ┌────────────────────────────────────┐  ║
║ │ İş Yeri                            │  ║
║ │ Maslak Mahallesi                   │  ║
║ │ Şişli/İstanbul, 34398              │  ║
║ │                                    │  ║
║ │            [Seç]  [Düzenle] [Sil] │  ║
║ └────────────────────────────────────┘  ║
║                                          ║
║ [+ Yeni Teslimat Adresi Ekle]           ║
║                                          ║
║                     [Kapat]              ║
╚══════════════════════════════════════════╝
```

**NE OLUYOR?**
- Tüm kayıtlı teslimat adresleri listeleniyor
- Varsayılan olan yıldızlı ⭐
- [Seç] → Bu adresi kullan
- [Düzenle] → Adresi değiştir
- [Sil] → Adresi sil
- [+ Yeni] → Yeni adres ekle formu aç

---

### POPUP PENCERE 2: Yeni Adres Ekle/Düzenle

Kullanıcı [+ Yeni Ekle] veya [Düzenle] tıklarsa:

```
╔══════════════════════════════════════════╗
║ Yeni Teslimat Adresi Ekle                ║
╠══════════════════════════════════════════╣
║                                          ║
║ Adres Başlığı * (örn: Fabrika, Depo)    ║
║ ┌────────────────────────────────────┐  ║
║ │ Fabrika                            │  ║
║ └────────────────────────────────────┘  ║
║                                          ║
║ Alıcı Adı Soyadı *                      ║
║ ┌────────────────────────────────────┐  ║
║ │ Mehmet Yılmaz                      │  ║
║ └────────────────────────────────────┘  ║
║                                          ║
║ Telefon *                               ║
║ ┌────────────────────────────────────┐  ║
║ │ 0532 123 45 67                     │  ║
║ └────────────────────────────────────┘  ║
║                                          ║
║ Açık Adres *                            ║
║ ┌────────────────────────────────────┐  ║
║ │ Gebze Organize Sanayi Bölgesi      │  ║
║ │ 4. Cadde No: 12                    │  ║
║ └────────────────────────────────────┘  ║
║                                          ║
║ Şehir *           İlçe                  ║
║ ┌─────────────┐   ┌──────────────────┐ ║
║ │ Kocaeli   ▼ │   │ Gebze          ▼ │ ║
║ └─────────────┘   └──────────────────┘ ║
║                                          ║
║ Posta Kodu                              ║
║ ┌────────────────────────────────────┐  ║
║ │ 41400                              │  ║
║ └────────────────────────────────────┘  ║
║                                          ║
║ ☑ Varsayılan teslimat adresim yap       ║
║   (bir sonraki siparişte otomatik      ║
║    bu adres gelsin)                     ║
║                                          ║
║              [İptal]        [Kaydet]    ║
╚══════════════════════════════════════════╝
```

**NE OLUYOR?**
- Form doldur
- [Kaydet] → Adres defterine eklenir
- ☑ Varsayılan → Bir sonraki siparişte otomatik gelsin
- [İptal] → Formu kapat

---

## 🔄 KULLANICI AKIŞLARI

### SENARYO 1: İlk Kez Sipariş Veren Müşteri

**Durum:** Ali ilk kez siteden forklift sipariş veriyor.

```
ADIM 1: Checkout sayfasına gelir
        → "Kayıtlı adresiniz yok" mesajı

ADIM 2: İletişim bilgileri [+ Yeni Ekle]
        → İsim, telefon, email yazar
        → ☑ "Varsayılan yap"
        → [Kaydet]

ADIM 3: Fatura adresi [+ Yeni Ekle]
        → Şirket, vergi no, adres yazar
        → ☑ "Varsayılan yap"
        → [Kaydet]

ADIM 4: Teslimat adresi [+ Yeni Ekle]
        → "Fabrika" başlığı, adres yazar
        → ☑ "Varsayılan yap"
        → [Kaydet]

ADIM 5: Siparişi tamamla
        → Adresler kaydedildi!
```

**SONUÇ:** Ali bir sonraki siparişte hiçbir şey yazmayacak!

---

### SENARYO 2: Daha Önce Sipariş Vermiş Müşteri

**Durum:** Mehmet daha önce sipariş vermiş, kayıtlı adresleri var.

```
ADIM 1: Checkout sayfasına gelir
        → Otomatik dolmuş:
          ✅ İletişim: Mehmet - 0532 xxx
          ✅ Fatura: XYZ Ltd - İstanbul
          ✅ Teslimat: Fabrika - Gebze

ADIM 2: Kontrol eder → Doğru ✅

ADIM 3: Siparişi tamamla → BİTTİ!
```

**SONUÇ:** Mehmet 10 saniyede sipariş verdi!

---

### SENARYO 3: Farklı Teslimat Adresi İstiyor

**Durum:** Ahmet bu sefer depo adresine istiyor.

```
ADIM 1: Teslimat bölümünde [Kayıtlı seç ▼]
        → Popup:
          ⭐ Fabrika - Gebze (varsayılan)
          📦 Depo - Ankara
          📦 İş Yeri - İstanbul

ADIM 2: "Depo - Ankara" seçer [Seç]

ADIM 3: Siparişi tamamla
        → Bu sefer depo adresine gidecek
```

---

### SENARYO 4: Yeni Adres Ekliyor

**Durum:** Fatma yeni şantiye adresi ekliyor.

```
ADIM 1: [Kayıtlı seç ▼] → [+ Yeni Adres Ekle]

ADIM 2: Form:
        - Başlık: "Şantiye - İzmir"
        - Adres bilgileri
        - ☐ Varsayılan yapma (tek seferlik)
        → [Kaydet]

ADIM 3: Adres listesine eklendi!
        → Sonra yine kullanabilir
```

---

### SENARYO 5: Hızlı Sipariş (Hepsi Aynı)

**Durum:** Bireysel müşteri Ayşe, ev adresine sipariş veriyor.

```
ADIM 1: İletişim bilgisi gelmiş ✅

ADIM 2: ☑ "Fatura iletişimle aynı"
        → Fatura bölümü kayboldu

ADIM 3: ☑ "Teslimat faturayla aynı"
        → Teslimat bölümü kayboldu

ADIM 4: Siparişi tamamla
        → 2 checkbox ile halletti!
```

---

## ⚙️ NE YAPILACAK? (AŞAMA AŞAMA)

### AŞAMA 1: Veritabanı Hazırlığı

**1.1 - Yeni Tablo Oluştur: customer_addresses**
- user_id (hangi kullanıcı)
- type (contact/billing/shipping)
- title (başlık: "Evim", "Fabrika")
- is_default (varsayılan mı?)
- İsim, email, telefon
- Şirket, vergi dairesi, vergi no
- Adres, şehir, ilçe, posta kodu

**1.2 - Mevcut Tabloyu Güncelle: shop_orders**
- contact_address_id ekle
- billing_address_id ekle
- shipping_address_id ekle
- Fatura snapshot kolonları ekle:
  - billing_company
  - billing_tax_office
  - billing_tax_number
  - billing_address
  - billing_city
  - billing_district
  - billing_postal_code

---

### AŞAMA 2: Adres Defteri Sistemi

**2.1 - Model Oluştur: CustomerAddress**
- Kullanıcıyla ilişki tanımla
- Adres kaydetme/okuma metodları
- Varsayılan adres yapma metodu
- Adres silme (soft delete)

**2.2 - Livewire Component: AddressManager**
Adres yönetimi için:
- Kullanıcının adreslerini listele
- Yeni adres ekle
- Adres düzenle
- Adres sil
- Varsayılan yap

---

### AŞAMA 3: Checkout Sayfası Güncellemeleri

**3.1 - CheckoutPage Component'i Güncelle**
- 3 adres ID'si tutacak değişkenler ekle
- Varsayılan adresleri otomatik yükle
- Adres seçildiğinde ID'yi kaydet
- Sipariş oluştururken adresleri kopyala (snapshot)

**3.2 - Checkout View'i Güncelle**
- 3 bölüm oluştur (İletişim, Fatura, Teslimat)
- Her bölümde:
  - [Kayıtlı seç ▼] dropdown
  - [+ Yeni Ekle] buton
  - Seçili adres gösterimi
  - ☑ "Aynı adres" checkbox

---

### AŞAMA 4: Popup Pencereler (Modal)

**4.1 - Adres Seçim Modal'ı**
- Kullanıcının adreslerini listele
- Varsayılanı yıldızla göster
- [Seç] [Düzenle] [Sil] butonları
- [+ Yeni Ekle] butonu

**4.2 - Adres Ekle/Düzenle Modal'ı**
- Form alanları
- Validasyon kontrolleri
- [Kaydet] [İptal] butonları
- ☑ "Varsayılan yap" checkbox

---

### AŞAMA 5: Test ve Kontrol

**5.1 - Yeni Kullanıcı Testi**
- İlk sipariş → Adres ekle → Kaydet → Seç
- İkinci sipariş → Otomatik gelsin mi?

**5.2 - Mevcut Kullanıcı Testi**
- Varsayılan adresler otomatik yüklensin mi?
- Farklı adres seçebiliyor mu?
- Yeni adres ekleyebiliyor mu?

**5.3 - Sipariş Snapshot Testi**
- Sipariş oluştuktan sonra adres değiştir
- Eski siparişe bak → Değişmemiş mi?

---

## 🎯 ÖZETİN ÖZETİ

**NE YAPIYORUZ?**
Amazon/Trendyol gibi adres defteri sistemi ekliyoruz.

**NASIL ÇALIŞACAK?**
1. Kullanıcı adreslerini bir kez kaydeder
2. Sonraki siparişlerde listeden seçer
3. Hızlı sipariş için checkbox'lar kullanır

**NEREDE SAKLANACAK?**
- Adresler: `customer_addresses` tablosunda
- Siparişler: `shop_orders` tablosunda (snapshot olarak)

**KAÇ ADRES OLABİLİR?**
- Sınırsız! İstediği kadar ekleyebilir
- Her tipte 1 varsayılan seçebilir

**SİPARİŞ BOZULUR MU?**
- Hayır! Snapshot sistemi var
- Adres silinse/değişse bile sipariş korunur

---

**Plan hazır! Onayın gelsin, kodlamaya başlayalım! 🚀**
