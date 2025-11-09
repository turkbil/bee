# 🔐 PayTR API Teknik Referans

**PayTR iFrame API - Detaylı Teknik Döküman**

---

## 📡 API ENDPOİNT'LERİ

### 1. Token Alma (Payment İframe Oluşturma)

**Endpoint:** `POST https://www.paytr.com/odeme/api/get-token`

**Content-Type:** `application/x-www-form-urlencoded`

---

## 🔑 ZORUNLU PARAMETRELER

### Temel Bilgiler

| Parametre | Tip | Açıklama | Örnek |
|-----------|-----|----------|-------|
| `merchant_id` | string | PayTR üye işyeri numarası | `123456` |
| `merchant_key` | string | PayTR güvenlik anahtarı | `xxxxxxxxxxxxx` |
| `merchant_salt` | string | PayTR güvenlik salt | `xxxxxxxxxxxxx` |
| `email` | string | Müşteri e-posta | `musteri@example.com` |
| `payment_amount` | integer | **KURUŞ cinsinden** toplam tutar | `10000` (= 100.00 TRY) |
| `merchant_oid` | string | Benzersiz sipariş numarası | `ORD-2024-001` |
| `user_name` | string | Müşteri adı soyadı | `Ahmet Yılmaz` |
| `user_address` | string | Müşteri adresi | `İstanbul, Türkiye` |
| `user_phone` | string | Müşteri telefonu | `05551234567` |
| `merchant_ok_url` | string | Başarılı ödeme redirect URL | `https://site.com/payment/success` |
| `merchant_fail_url` | string | Başarısız ödeme redirect URL | `https://site.com/payment/failed` |
| `user_basket` | string | **Base64 encoded** sepet bilgisi (JSON) | `W1siw5Zyw7xuxIEiLCIxMDAuMDAiLDFdXQ==` |
| `paytr_token` | string | **HMAC-SHA256** hash | `xxxxxxxxxxxxx` |
| `debug_on` | integer | Debug modu (test: 1, canlı: 0) | `1` |
| `test_mode` | integer | Test modu (test: 1, canlı: 0) | `1` |
| `no_installment` | integer | Taksit kapalı mı? (0: açık, 1: kapalı) | `0` |
| `max_installment` | integer | Maksimum taksit sayısı | `12` |
| `user_ip` | string | Müşteri IP adresi | `123.456.789.10` |
| `timeout_limit` | integer | Ödeme timeout süresi (dakika) | `30` |
| `currency` | string | Para birimi (TRY, USD, EUR) | `TRY` |

---

## 🛠️ OPSİYONEL PARAMETRELER

| Parametre | Tip | Açıklama | Default |
|-----------|-----|----------|---------|
| `lang` | string | Dil (tr, en, de, fr, it, ru, ar) | `tr` |
| `non_3d` | integer | 3D Secure kapalı mı? (0: açık, 1: kapalı) | `0` |
| `client_lang` | string | Müşteri tarayıcı dili | `tr` |
| `installment_count` | integer | Zorunlu taksit sayısı (belirtilirse diğerleri gizlenir) | - |

---

## 🔐 HASH HESAPLAMA (Token İsteği)

### Hash String Formatı:

```
merchant_id + user_ip + merchant_oid + email + payment_amount + user_basket + no_installment + max_installment + currency + test_mode + merchant_salt
```

### PHP Örneği:

```php
$hash_str = $merchant_id .
            $user_ip .
            $merchant_oid .
            $email .
            $payment_amount .
            $user_basket .
            $no_installment .
            $max_installment .
            $currency .
            $test_mode .
            $merchant_salt;

$paytr_token = base64_encode(hash_hmac('sha256', $hash_str, $merchant_key, true));
```

### ⚠️ ÖNEMLİ NOTLAR:
- Hash hesaplamasında **merchant_salt** en sonda eklenir
- Hash hesaplamasında **merchant_key** HMAC key olarak kullanılır
- Sonuç **base64_encode** ile encode edilir
- **raw_output = true** parametresi kritik!

---

## 📦 USER_BASKET FORMATI

### Sepet Bilgisi (JSON Array)

```json
[
  ["Ürün Adı 1", "100.00", 2],
  ["Ürün Adı 2", "50.50", 1]
]
```

**Format:** `[["Ürün adı", "Birim fiyat (ondalık)", Adet], ...]`

### PHP Örneği:

```php
$basket = [
    ["Transpalet Forklift", "1500.00", 1],
    ["Kargo Ücreti", "50.00", 1]
];

$user_basket = base64_encode(json_encode($basket));
```

### ⚠️ KURALLAR:
- Birim fiyat **string** olmalı (ondalık formatında)
- Adet **integer** olmalı
- **JSON encode** → **base64 encode** sırası önemli!

---

## 📤 API REQUEST ÖRNEĞİ

### cURL PHP:

```php
$post_data = [
    'merchant_id' => $merchant_id,
    'user_ip' => $user_ip,
    'merchant_oid' => $merchant_oid,
    'email' => $email,
    'payment_amount' => $payment_amount, // Kuruş cinsinden!
    'paytr_token' => $paytr_token,
    'user_basket' => $user_basket,
    'debug_on' => 1,
    'no_installment' => 0,
    'max_installment' => 12,
    'user_name' => $user_name,
    'user_address' => $user_address,
    'user_phone' => $user_phone,
    'merchant_ok_url' => $merchant_ok_url,
    'merchant_fail_url' => $merchant_fail_url,
    'timeout_limit' => 30,
    'currency' => 'TRY',
    'test_mode' => 1,
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://www.paytr.com/odeme/api/get-token');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
```

---

## 📥 API RESPONSE FORMATI

### Başarılı Response:

```json
{
  "status": "success",
  "token": "xxxxxxxxxxxxxxxxxxxxxxx",
  "reason": null
}
```

### Başarısız Response:

```json
{
  "status": "failed",
  "reason": "Hata açıklaması",
  "token": null
}
```

---

## 🖼️ İFRAME KULLANIMI

### Token Alındıktan Sonra:

```html
<iframe
    src="https://www.paytr.com/odeme/guvenli/{{ token }}"
    id="paytriframe"
    frameborder="0"
    scrolling="no"
    style="width: 100%;">
</iframe>

<script src="https://www.paytr.com/js/iframeResizer.min.js"></script>
<script>
    iFrameResize({
        log: false,
        checkOrigin: false
    }, '#paytriframe');
</script>
```

### ⚠️ NOTLAR:
- **iframeResizer.min.js** mutlaka eklenmelidir (responsive için)
- iframe **minimum 600px** height olmalı
- Token **tek kullanımlıktır** (yeniden ödeme için yeni token)

---

## 🔔 CALLBACK (IPN) MEKANİZMASI

### Callback URL'e Gelen POST Parametreleri:

| Parametre | Tip | Açıklama |
|-----------|-----|----------|
| `merchant_oid` | string | Sipariş numarası |
| `status` | string | `success` veya `failed` |
| `total_amount` | integer | Toplam tutar (kuruş) |
| `hash` | string | Güvenlik hash'i |
| `failed_reason_code` | integer | Hata kodu (başarısız ise) |
| `failed_reason_msg` | string | Hata mesajı (başarısız ise) |
| `test_mode` | integer | Test modu mu? |
| `payment_type` | string | `card`, `eft` vb. |
| `currency` | string | Para birimi |
| `payment_amount` | integer | Ödenen tutar (kuruş) |
| `installment_count` | integer | Taksit sayısı |

---

## 🔐 CALLBACK HASH DOĞRULAMA

### Hash String Formatı:

```
merchant_oid + merchant_salt + status + total_amount
```

### PHP Örneği:

```php
$hash_str = $_POST['merchant_oid'] .
            $merchant_salt .
            $_POST['status'] .
            $_POST['total_amount'];

$calculated_hash = base64_encode(hash_hmac('sha256', $hash_str, $merchant_key, true));

if ($calculated_hash !== $_POST['hash']) {
    // HATA: Hash eşleşmiyor!
    echo "FAILED: Invalid hash";
    exit;
}

// Hash doğru, işleme devam et
echo "OK";
```

### ⚠️ KRİTİK KURALLAR:

1. **Hash mutlaka doğrulanmalı** - Güvenlik için kritik!
2. **Response "OK" olmalı** - Aksi halde PayTR tekrar callback gönderir
3. **Duplicate callback handle edilmeli** - Aynı `merchant_oid` birden fazla gelebilir
4. **200 OK HTTP status dönülmeli**
5. **Timeout 30 saniye** - Callback'te uzun işlem yapma!

---

## 🔄 DUPLICATE CALLBACK HANDLE

```php
// merchant_oid ile kontrol et
$existingPayment = Payment::where('gateway_transaction_id', $_POST['merchant_oid'])
    ->where('status', 'completed')
    ->first();

if ($existingPayment) {
    // Daha önce işlenmiş, sadece OK döndür
    echo "OK";
    exit;
}

// İlk kez geliyor, işle
// ...
echo "OK";
```

---

## 💳 TEST KARTLARI

### Başarılı Ödeme:

```
Kart No: 4355 0843 5508 4358
Son Kullanma: 12/26
CVV: 000
```

### Başarısız Ödeme (Yetersiz Bakiye):

```
Kart No: 5406 6754 0667 5403
Son Kullanma: 12/26
CVV: 000
```

### Başarısız Ödeme (Geçersiz Kart):

```
Kart No: 4508 0345 0803 4509
Son Kullanma: 12/26
CVV: 000
```

---

## 🚨 HATA KODLARI

| Kod | Açıklama | Çözüm |
|-----|----------|-------|
| `1` | Geçersiz merchant_id | Merchant ID kontrol et |
| `2` | Geçersiz token | Hash yanlış hesaplanmış |
| `3` | Yetkisiz erişim | IP kısıtlaması olabilir |
| `10` | Geçersiz tutar | payment_amount kuruş cinsinden olmalı |
| `11` | Minimum tutar altı | Min. 10 TL (1000 kuruş) |
| `20` | Kart reddedildi | Müşteri bankasıyla iletişime geçmeli |
| `21` | Yetersiz bakiye | Başka kart denenmeli |
| `22` | 3D Secure başarısız | Müşteri SMS kodunu yanlış girmiş |
| `30` | Timeout | İşlem süresi aşımı (30 dk) |
| `99` | Bilinmeyen hata | Tekrar denenebilir |

---

## ⏱️ TIMEOUT & RATE LİMİTİNG

### API Timeout:
- **Token isteği**: Max 30 saniye
- **Callback response**: Max 30 saniye
- **İframe payment**: Max 30 dakika (user timeout)

### Rate Limiting:
- **Token API**: Max 100 req/min per merchant
- **Callback retry**: 5 dakika arayla max 10 kez dener

---

## 🔒 GÜVENLİK BEST PRACTİCES

### ✅ YAPILMASI GEREKENLER:

1. **Hash doğrulama mutlaka yapılmalı** (callback'te)
2. **merchant_salt ve merchant_key gizli tutulmalı** (.env'de)
3. **HTTPS zorunlu** (callback URL https:// olmalı)
4. **IP whitelist** (opsiyonel, PayTR IP'leri)
5. **Amount validation** (callback'teki tutar ile DB tutarı eşleşmeli)
6. **Duplicate prevention** (merchant_oid kontrolü)
7. **SQL injection koruması** (prepared statements)
8. **XSS koruması** (user input sanitize)
9. **CSRF token exempt** (callback route'u middleware'den muaf)
10. **Logging** (tüm callback'leri logla)

### ❌ YAPILMAMASI GEREKENLER:

1. **merchant_key/salt frontend'e gönderme** (ASLA!)
2. **Hash doğrulamadan payment status güncelleme**
3. **GET ile callback kabul etme** (sadece POST)
4. **Callback'te uzun işlem yapma** (max 30 saniye)
5. **OK yanıtı vermeden DB transaction commit etme**
6. **Test credentials production'da kullanma**

---

## 🧪 TEST ORTAMI AYARLARI

### Test Merchant Credentials (Örnek):

```bash
PAYTR_MERCHANT_ID=123456
PAYTR_MERCHANT_KEY=xxxxxxxxxxxxxx
PAYTR_MERCHANT_SALT=xxxxxxxxxxxxxx
PAYTR_MODE=test
```

### Test Modunda:

- `test_mode=1` parametre gönder
- `debug_on=1` parametre gönder
- Test kartlarını kullan
- **Gerçek para kesintisi yapılmaz**
- **Callback gerçek gelir** (test ortamında da çalışır)

---

## 💰 TAKSİT SEÇENEKLERİ

### Taksit Açık (Varsayılan):

```php
'no_installment' => 0,  // Taksit açık
'max_installment' => 12, // Max 12 taksit
```

### Taksit Kapalı:

```php
'no_installment' => 1,  // Sadece tek çekim
'max_installment' => 0,
```

### Belirli Taksit Zorla:

```php
'no_installment' => 0,
'installment_count' => 3, // Sadece 3 taksit seçeneği göster
```

---

## ↩️ REFUND (İADE) API

**Not:** PayTR iFrame API'de otomatik refund API yoktur.

İade işlemleri **PayTR panel** üzerinden manuel yapılır:
1. PayTR hesabına giriş yap
2. İşlemler → İşlem Ara
3. merchant_oid ile işlemi bul
4. İade butonuna tıkla
5. İade tutarını gir

**Alternatif:** PayTR Direct API kullanılırsa refund API mevcuttur (gelecekte eklenebilir).

---

## 📊 STATUS QUERY API (Durum Sorgulama)

**Endpoint:** `POST https://www.paytr.com/odeme/durum-sorgu`

**Parametreler:**
- `merchant_id`
- `merchant_oid`
- `merchant_salt`
- `paytr_token` (hash)

**Response:**
```json
{
  "status": "success",
  "payment_status": "completed",
  "payment_amount": "10000",
  "currency": "TRY"
}
```

---

## 🌍 ÇOK DİLLİ DESTEK

PayTR aşağıdaki dilleri destekler:

| Kod | Dil |
|-----|-----|
| `tr` | Türkçe (varsayılan) |
| `en` | English |
| `de` | Deutsch |
| `fr` | Français |
| `it` | Italiano |
| `ru` | Русский |
| `ar` | العربية |

**Kullanım:**
```php
'lang' => 'en', // Ödeme sayfası İngilizce olur
```

---

## 📞 DESTEK & KAYNAKLAR

- **Resmi Döküman:** https://dev.paytr.com/
- **PayTR Panel:** https://www.paytr.com/
- **Destek Email:** info@paytr.com
- **Destek Tel:** 0850 305 0 305
- **GitHub Örnekler:** https://github.com/mewebstudio/paytr

---

## ✅ CHECKLIST (Canlıya Almadan Önce)

- [ ] Test kartı ile başarılı ödeme denendi
- [ ] Test kartı ile başarısız ödeme denendi
- [ ] Callback hash doğrulaması çalışıyor
- [ ] Duplicate callback handle ediliyor
- [ ] Amount validation yapılıyor
- [ ] Timeout senaryosu test edildi
- [ ] HTTPS aktif (callback URL)
- [ ] merchant_key/salt .env'de gizli
- [ ] Logging aktif (tüm callback'ler)
- [ ] CSRF exempt (callback route)
- [ ] Test mode kapatıldı (test_mode=0, debug_on=0)
- [ ] Canlı credentials girildi
- [ ] Production test (küçük miktarla gerçek kart)

---

**Hazırlayan:** Claude Code
**Kaynak:** PayTR Developer Documentation
**Versiyon:** 1.0
**Son Güncelleme:** 2025-11-09
