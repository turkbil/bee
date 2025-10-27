# 📝 UNIVERSAL MD DOKÜMANTASYON ŞABLONU - PROMPT

## 🎯 KULLANIM TALİMATI

Bu prompt, herhangi bir AI feature veya sistem için profesyonel MD dokümantasyonu oluşturmak üzere tasarlanmıştır. 

**Komut örneği:** "Email Oluşturucu feature'ı için MD dokümantasyonu oluştur"

---

## 📋 MD DOSYASI OLUŞTURMA ŞABLONU

### BAŞLIK FORMATI:
```markdown
# [EMOJI] [FEATURE ADI] - KOMPLE DOKÜMANTASYON
```

### 1. AMATÖR İÇİN BASIT ANLATIM BÖLÜMÜ:

```markdown
## 🎯 AMATÖR İÇİN BASIT ANLATIM

### [Feature] Nasıl Kullanılır?

1. **[İşlem Adı]:**
   - [Adım adım basit anlatım]
   - [Hangi butona tıklanacak]
   - [Ne olacağının açıklaması]

2. **Form Doldurma:**
   - **[Alan Adı]**: [Ne yazılacağının açıklaması] (örnek: "...")
   - **[Alan Adı]**: [Seçeneklerin açıklaması]
   - [Diğer form alanları]

3. **[İşlem] Oluşturma:**
   - "[Buton Adı]" butonuna tıklayın
   - [Ne olacağının açıklaması]
   - Yanıt şu bölümleri içerecek:
     - ✅ [Bölüm 1]
     - ✅ [Bölüm 2]
     - ✅ [Bölüm 3]

4. **Kredi Sistemi:**
   - Her [işlem] kredi harcar
   - Kullanılan kredi miktarı, [parametreye] göre değişir
   - Kredi yetersizse sistem uyarı verir

### Arkada Ne Oluyor?

1. **Form gönderildiğinde:**
   - Sistem önce [kontrol 1]
   - [Kontrol 2] varsa işleme devam eder

2. **AI'ya gönderim:**
   - Sistem [X] farklı prompt'u sırayla birleştirir
   - Önce [prompt tip 1], sonra [prompt tip 2] eklenir
   - En son kullanıcı girdisi eklenir

3. **Yanıt işleme:**
   - AI'dan gelen yanıt [işlem 1]
   - [İşlem 2] ve kayıt edilir
   - Yanıt ekranda gösterilir
```

### 2. TEKNİK DOKÜMANTASYON BÖLÜMÜ:

```markdown
## 🔧 TEKNİK DOKÜMANTASYON

### Sistem Mimarisi

[Feature adı] feature'ı **[teknoloji/pattern]** üzerine inşa edilmiştir ve tamamen **[özellik]** çalışır.

### Dosya Yapısı

\```
📁 [Feature] Sistemi
├── 📄 [dosya yolu 1] ([açıklama])
├── 📄 [dosya yolu 2] ([açıklama])
├── 📄 [klasör]/
│   ├── [dosya] ([açıklama])
│   └── [dosya] ([açıklama])
└── 📄 [dosya yolu 3] ([açıklama])
\```

### Frontend İşleyişi

#### 1. [İşlem Adı]:
\```javascript
// [Açıklama]
[kod örneği]
\```

#### 2. [Class/Component Adı]:
\```javascript
class [ClassName] {
    constructor() {
        // [özellikler]
    }
    
    // [Method açıklaması]
    async [methodName]() {
        // [işlem adımları]
    }
}
\```

### Backend İşleyişi

#### 1. Route Tanımları:
\```php
// [dosya yolu]
Route::[method]('[path]', [Controller::class, 'method']);
\```

#### 2. Controller:
\```php
class [ControllerName] extends Controller
{
    public function [methodName](Request $request): JsonResponse
    {
        // 1. [İşlem 1]
        // 2. [İşlem 2]
        // 3. [İşlem 3]
        // 4. [Sonuç döndürme]
    }
}
\```

#### 3. Service Layer:
\```php
class [ServiceName]
{
    public function [methodName](array $config): array
    {
        // [İşlem detayları]
    }
}
\```

### Kredi Sistemi

#### Kredi Hesaplama:
\```php
// [Kredi hesaplama mantığı]
$tokensUsed = [hesaplama];
$creditsUsed = [formül];

// [Kredi düşürme]
[kod örneği]

// [Kayıt oluşturma]
[kod örneği]
\```

### Veritabanı Tabloları

#### Kullanılan Tablolar:
1. **[tablo_adı]** - [açıklama]
2. **[tablo_adı]** - [açıklama]
3. **[tablo_adı]** - [açıklama]

### [Ek Özellik] (Opsiyonel)

#### [Alt başlık]:
- ✅ **[Özellik 1]** - [açıklama]
- ✅ **[Özellik 2]** - [açıklama]
- ✅ **[Özellik 3]** - [açıklama]

#### [İşlem] Ekleme:
\```php
// [Kod örneği]
\```

### Hata Yönetimi

\```javascript
// Frontend hata yönetimi
[kod örneği]
\```

\```php
// Backend hata yönetimi
[kod örneği]
\```

## 🚀 PERFORMANS VE OPTİMİZASYON

### Cache Sistemi:
- [Cache item 1] [süre] cache'lenir
- [Cache item 2] [süre] cache'lenir

### Batch İşleme:
- [Batch detayları]
- Timeout: [süre]
- Memory limit: [limit]

### Rate Limiting:
- [Limit 1]
- [Limit 2]
- [Limit 3]
```

### 3. SONUÇ BÖLÜMÜ:

```markdown
## ✅ SONUÇ

[Feature Adı] feature'ı:
- **[özellik 1]** [açıklama]
- **[özellik 2]** [açıklama]
- **[özellik 3]** [açıklama]
- **[özellik 4]** - [detay]
- **[özellik 5]** - [detay]
- **[özellik 6]** - [detay]
- **[özellik 7]** - [detay]
- **[özellik 8]** - [detay]
```

---

## 🎨 DOKÜMANTASYON KURALLARI

### İçerikte Bulunması Gerekenler:

1. **AMATÖR ANLATIM:**
   - Adım adım kullanım
   - Somut örnekler
   - Görsel açıklamalar (emoji kullanımı)
   - "Şuna tıklayın, şu olur" tarzı basit anlatım

2. **TEKNİK DETAYLAR:**
   - Tüm dosya yolları
   - Kod örnekleri (gerçek koddan alıntılar)
   - Veritabanı tabloları ve ilişkileri
   - API endpoint'leri
   - Service layer detayları
   - Kredi sistemi entegrasyonu

3. **KRİTİK BİLGİLER:**
   - Hangi tablolar kullanılıyor
   - Hangi seeder'lar çalışıyor
   - Kredi nasıl hesaplanıyor ve düşürülüyor
   - Log ve debug kayıtları nereye yazılıyor
   - Hata durumları nasıl yönetiliyor

4. **ÇALIŞMA AKIŞI:**
   - Frontend'den backend'e tam akış
   - Her adımda hangi dosya/fonksiyon çalışıyor
   - Prompt sistemi nasıl işliyor
   - AI'ya ne gönderiliyor, nasıl yanıt alınıyor

5. **GENİŞLETME TALİMATLARI:**
   - Başka modüle nasıl uygulanır
   - Yeni özellik nasıl eklenir
   - Customization noktaları

### Stil Kuralları:

- ✅ Emoji kullan (başlıklarda ve vurgulamalarda)
- ✅ Kod örnekleri syntax highlighting ile
- ✅ Liste ve tablo formatları düzgün
- ✅ Hierarchical başlık yapısı (H1 > H2 > H3 > H4)
- ✅ Bold ve italic vurgulamalar
- ✅ Gerçek dosya yolları ve class isimleri

### Kontrol Listesi:

- [ ] Amatör anlatım var mı?
- [ ] Teknik detaylar eksiksiz mi?
- [ ] Kod örnekleri gerçek koddan mı?
- [ ] Veritabanı tabloları açıklandı mı?
- [ ] Kredi sistemi detaylandırıldı mı?
- [ ] Hata yönetimi açıklandı mı?
- [ ] Performans optimizasyonları belirtildi mi?
- [ ] Genişletme talimatları eklendi mi?
- [ ] **🚨 SEEDER TEST PROTOKOLÜ UYGULANDI MI?** (ZORUNLU)

---

## 🚨 AI SEEDER TEST PROTOKOLÜ - ZORUNLİ

### Seeder Dokümantasyonu İçin Test Kuralları:

Herhangi bir feature için seeder dokümantasyonu hazırlarken **MUTLAKA** şu test protokolünü uygula:

#### 1. Test Komutu:
```bash
php artisan app:clear-all && php artisan migrate:fresh --seed && php artisan module:clear-cache && php artisan responsecache:clear && php artisan telescope:clear
```

#### 2. Log Kontrolü:
- Komut çalıştıktan sonra `storage/logs/laravel.log` dosyasını kontrol et
- **Hiçbir hata olmaması gerekir**
- Eğer hata varsa, düzelt ve tekrar test et
- Hata vermeyene kadar devam et

#### 3. Tamamlanma Kriterleri:
- ✅ Test komutu başarıyla çalışmalı
- ✅ Laravel.log'da hata olmamalı
- ✅ Tüm seeder'lar çalışmalı
- ✅ Migration'lar başarılı olmalı
- ✅ Cache temizlenmeli

#### 4. Dokümantasyonda Belirtilmesi Gerekenler:
- Feature'ın hangi seeder dosyalarını kullandığı
- Test komutunun başarıyla çalıştığı
- Hangi tabloları etkilediği
- Kaç satır veri eklediği

### ⚠️ UYARI:
Eğer test komutu hata verirse:
1. **ASLA** dokümantasyonu tamamlanmış sayma
2. **MUTLAKA** hatayı düzelt
3. **TEKRAR** test et
4. **BAŞARILI** olunca dokümantasyonu tamamla

---

## 🚀 KULLANIM ÖRNEĞİ

**Komut:** "Email Template Oluşturucu için MD dokümantasyonu yap"

**Yapılacaklar:**
1. Feature'ın kodlarını incele
2. Yukarıdaki şablonu kullan
3. Tüm [bracket] alanları gerçek bilgilerle doldur
4. Amatör ve teknik anlatımı dengele
5. Kod örneklerini gerçek koddan al
6. **🚨 SEEDER TEST PROTOKOLÜNÜ UYGULA:**
   - Test komutunu çalıştır
   - Laravel.log kontrol et
   - Hata varsa düzelt ve tekrarla
7. Eksik bırakma, tam dokümantasyon yap
8. Test başarılıysa "tamamlandı" de

---

## 📌 NOT

Bu şablon kullanıldığında:
- Feature'ın TÜM detayları dokümante edilmeli
- Hem amatör hem profesyonel anlayabilmeli
- Copy-paste ile başka projelerde kullanılabilmeli
- Hiçbir hardcode veya fallback kullanılmamalı
- Tamamen dinamik ve gerçek verilerle çalışmalı
- **🚨 SEEDER TEST PROTOKOLÜ MUTLAKA UYGULANMALI**
- Laravel.log dosyası hata vermeden çalışmalı
- Test başarılı olunca CLAUDE.md'ye kayıt edilmeli