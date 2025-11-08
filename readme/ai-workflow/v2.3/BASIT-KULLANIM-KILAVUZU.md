# AI Sohbet Robotu - Basit Kullanım Kılavuzu

## 📌 Bu Nedir?

Sağ altta gördüğün mor robot ikonu bir yapay zeka destekli sohbet robotudur. Müşteriler siteye girdiğinde ürünler hakkında soru sorabilir, fiyat öğrenebilir, ürün önerisi alabilir.

---

## 🎯 Nasıl Çalışır? (Basit Anlatım)

### 1️⃣ Robot Butonu Görünür

**Ne oluyor?**
- Sayfa açıldıktan 10 saniye sonra robot butonu otomatik açılıyor (sadece bilgisayarda, telefonda açılmıyor)
- Buton üstünde baloncuklar çıkıyor: "Merhaba! Nasıl yardımcı olabilirim? 👋" gibi yazılar döngü halinde gösteriliyor

**Nerede?**
- Sağ alt köşede sabit duruyor
- Her sayfada görünür

**Kullanıcı ne yapar?**
- Mora tıklar

---

### 2️⃣ Sohbet Penceresi Açılır

**Ne oluyor?**
- Mor buton kaybolur
- Yerine beyaz bir sohbet kutusu açılır
- Üstte "iXtif Yapay Zeka Asistanı" başlığı var
- Altta mesaj yazma kutusu var

**İlk açıldığında ne görünür?**
- Ortada animasyonlu bir ikon var
- "Merhaba! 👋" yazısı
- "Size nasıl yardımcı olabilirim?" sorusu
- Alta dönen öneriler: "Ürün özellikleri", "Stok durumu", "Fiyat bilgisi" vb.

**Kullanıcı ne yapar?**
- Alta "Mesajınızı yazın..." kutusuna tıklar
- Mesajını yazar
- Mavi "gönder" butonuna basar

---

### 3️⃣ Mesaj Gönderilir

**Ekranda ne görünür?**
- Kullanıcının mesajı sağ tarafta mavi baloncukta çıkar
- Alta saat yazılır (örn: 14:30)

**Arka planda ne oluyor?** (Kullanıcı görmez ama sistem şunu yapıyor)

1. **Mesaj sunucuya gönderiliyor**
   - JavaScript kodu mesajı alıyor
   - `/api/ai/v1/shop-assistant/chat` adresine POST isteği yapıyor
   - Oturum ID'si (session_id) ekleniyor ki robot kullanıcıyı hatırlasın

2. **Sunucu mesajı alıyor**
   - Laravel controller devreye giriyor
   - "Bu kişi dakikada 10'dan fazla mesaj atmış mı?" kontrol ediliyor (spam koruması)
   - Mesaj geçerli mi kontrol ediliyor (minimum 1, maksimum 1000 karakter)

3. **Oturum kontrolü**
   - Daha önce bu kullanıcıyla konuşulmuş mu bakılıyor
   - Eğer eski konuşma varsa, o konuşma veritabanından yükleniyor
   - Robot eski mesajları hatırlıyor, o yüzden tekrar "merhaba" demiyor

4. **Workflow (İş Akışı) Başlıyor**
   - Robot mesajı analiz etmek için adım adım işlemler yapıyor
   - Her adıma "Node" (düğüm) deniyor

---

### 4️⃣ Workflow Adımları (Robot Arka Planda Ne Yapıyor?)

#### **Adım 1: Context Builder (Bağlam Oluşturucu)**
**Ne yapıyor?**
- "Bu kullanıcı hangi sayfada?" bakıyor
- Eğer ürün sayfasındaysa, o ürünün bilgilerini alıyor
- Kategori sayfasındaysa, o kategoriyle ilgili ürünleri alıyor

**Örnek:**
- Kullanıcı "Transpalet fiyatı nedir?" diye soruyor
- Transpalet ürün sayfasındaysa → O ürünün fiyatını hazırlıyor
- Anasayfadaysa → Tüm transpalet kategorisindeki ürünlere bakıyor

---

#### **Adım 2: Category Detection (Kategori Algılama)**
**Ne yapıyor?**
- Kullanıcının ne sorduğunu anlamaya çalışıyor
- "Bu fiyat sorusu mu? Stok sorusu mu? Ürün önerisi mi?"

**OpenAI'ye soruyor:**
```
"Kullanıcı şu mesajı yazmış: 'Transpalet fiyatı nedir?'
Bu mesaj hangi kategoriye giriyor?
- Fiyat sorusu ✅
- Stok sorusu ❌
- Ürün önerisi ❌
- Genel soru ❌"
```

**Çıktı:**
- Kategori: `price_question` (Fiyat sorusu)
- Güven: %92

---

#### **Adım 3: Product Search (Ürün Arama)**
**Ne yapıyor?**
- Kullanıcının mesajındaki anahtar kelimeleri buluyor
- "Transpalet" kelimesini görüyor
- Veritabanında "transpalet" araması yapıyor

**Veritabanı sorgusu:**
```sql
SELECT * FROM products
WHERE title LIKE '%transpalet%'
   OR description LIKE '%transpalet%'
ORDER BY relevance DESC
LIMIT 5
```

**Çıktı:**
```
1. Transpalet 2.5 Ton - 15.000 TL - Stokta var
2. Transpalet 3 Ton - 18.000 TL - Stokta var
3. Transpalet Elektrikli - 45.000 TL - Tükendi
```

---

#### **Adım 4: Stock Sorter (Stok Sıralayıcı)**
**Ne yapıyor?**
- Yukarıdaki ürünleri stok durumuna göre sıralıyor
- **Stokta olanlar önce**, tükenmiş olanlar sonda

**Çıktı:**
```
1. Transpalet 2.5 Ton - Stokta ✅
2. Transpalet 3 Ton - Stokta ✅
3. Transpalet Elektrikli - Tükendi ❌
```

---

#### **Adım 5: AI Response (Yapay Zeka Yanıtı)**
**Ne yapıyor?**
- Tüm topladığı bilgileri OpenAI'ye gönderiyor
- OpenAI'dan Türkçe, doğal bir yanıt istiyor

**OpenAI'ye gönderilen prompt:**
```
Sen iXtif firmasının e-ticaret asistanısın.
Kullanıcı mesajı: "Transpalet fiyatı nedir?"
Mevcut ürünler:
- Transpalet 2.5 Ton: 15.000 TL (Stokta)
- Transpalet 3 Ton: 18.000 TL (Stokta)

Lütfen Türkçe, yardımsever bir yanıt oluştur.
Markdown kullan.
Ürün linklerini [LINK:shop:slug] formatında ekle.
```

**OpenAI'den gelen yanıt:**
```markdown
Merhaba! 👋

Transpalet modellerimizin fiyatları şu şekilde:

- **2.5 Ton Transpalet**: 15.000 TL ✅ (Stokta)
- **3 Ton Transpalet**: 18.000 TL ✅ (Stokta)

Detaylı bilgi için [LINK:shop:category:transpalet] sayfamızı ziyaret edebilirsiniz.

Size yardımcı olabilir miyim? 😊
```

---

#### **Adım 6: Message Saver (Mesaj Kaydedici)**
**Ne yapıyor?**
- Konuşmayı veritabanına kaydediyor
- 2 kayıt oluşturuyor:
  1. Kullanıcının mesajı: `role: 'user'`
  2. Robotun cevabı: `role: 'assistant'`

**Veritabanı:**
```
ai_conversations (oturum bilgisi)
├── id: 123
├── session_id: "abc123xyz"
├── tenant_id: 2 (ixtif.com)
└── created_at: "2025-01-06 14:30:00"

ai_messages (mesajlar)
├── conversation_id: 123
├── role: "user"
├── content: "Transpalet fiyatı nedir?"
├── created_at: "2025-01-06 14:30:05"

ai_messages
├── conversation_id: 123
├── role: "assistant"
├── content: "Merhaba! Transpalet modellerimizin..."
├── created_at: "2025-01-06 14:30:08"
```

---

#### **Adım 7: End Node (Son Adım)**
**Ne yapıyor?**
- İş akışını sonlandırıyor
- "Tamamlandı" işareti veriyor

---

### 5️⃣ Yanıt Kullanıcıya Gösteriliyor

**Ne oluyor?**

1. **Sunucu JSON yanıt gönderiyor:**
```json
{
  "success": true,
  "data": {
    "message": "<p>Merhaba! 👋</p><p>Transpalet modellerimizin...</p>",
    "session_id": "abc123xyz"
  }
}
```

2. **JavaScript yanıtı alıyor**
   - JSON parse ediliyor
   - HTML içerik çıkartılıyor

3. **Ekranda görünüyor**
   - Sol tarafta beyaz baloncukta AI yanıtı çıkıyor
   - Markdown formatında (kalın yazı, listeler, linkler)
   - Alta saat yazılıyor: "14:30"

4. **Otomatik kaydırma**
   - Chat penceresi en alta kayıyor
   - Yeni mesaj görünür hale geliyor

---

### 6️⃣ Kullanıcı Yeni Mesaj Yazarsa

**Ne oluyor?**
- Aynı süreç tekrarlanıyor
- Ama bu sefer robot eski konuşmayı hatırlıyor
- Çünkü `session_id` aynı ve veritabanından eski mesajlar yükleniyor

**Örnek:**
```
Kullanıcı: "Transpalet fiyatı nedir?"
Robot: "2.5 ton: 15.000 TL, 3 ton: 18.000 TL"

Kullanıcı: "3 tonluk stokta mı?"
Robot: (Eski konuşmayı hatırlıyor → 3 ton transpalet = 18.000 TL)
       "Evet, 3 ton transpalet stokta mevcut! ✅"
```

---

### 7️⃣ Kullanıcı Pencereyi Kapatırsa

**Ne oluyor?**
- Sağ üstteki X butonuna tıklanıyor
- Sohbet penceresi kaybolur
- Mor robot butonu tekrar görünür

**Arka planda:**
- `localStorage`'a kaydediliyor: `ai_chat_floating_open = false`
- Konuşma silinmiyor, sadece gizleniyor
- Kullanıcı tekrar açarsa eski mesajları görür

---

### 8️⃣ Kullanıcı Başka Sayfaya Giderse

**Ne oluyor?**
- Robot butonu yeni sayfada da görünür
- Kullanıcı tekrar açarsa, eski konuşma devam eder
- Çünkü `session_id` localStorage'da saklanıyor

---

## 🔐 Güvenlik ve Limitler

### Spam Koruması
**Ne var?**
- Dakikada maksimum 10 mesaj
- Fazla gönderirse: "Rate limit exceeded" hatası

**Nasıl çalışıyor?**
- IP adresine göre sayaç tutuluyor
- Redis'te saklanıyor
- 1 saat sonra sıfırlanıyor

---

### Oturum Yönetimi
**Session ID nedir?**
- Her kullanıcıya özel bir kimlik kodu
- Örnek: `session_abc123xyz456`
- Tarayıcıda localStorage'da saklanıyor

**Ne işe yarıyor?**
- Kullanıcıyı tanıyor
- Eski konuşmaları yüklüyor
- Sayfa yenilense de kaybetmiyor

---

## 📊 Veritabanı Kayıtları

### ai_conversations (Oturum Tablosu)
**Ne saklanıyor?**
- Oturum ID'si
- Tenant ID'si (hangi site: ixtif.com, tuufi.com vb.)
- IP adresi
- Tarayıcı bilgisi
- Oluşturma tarihi

### ai_messages (Mesaj Tablosu)
**Ne saklanıyor?**
- Hangi oturuma ait
- Kim yazdı (`user` mı `assistant` mı)
- Mesaj içeriği
- Tarih-saat

---

## 🎨 Görsel Özellikler

### Animasyonlar
1. **Mor buton üstündeki baloncuk**
   - 1.5 saniye gösteriliyor
   - 0.3 saniye kaybolma animasyonu
   - Sonraki mesaj geliyor
   - Toplam 3.3 saniyede 1 döngü

2. **Hoş geldin ekranı**
   - Ortada animasyonlu ikon var
   - Dalgalanma efekti (ping animation)
   - "Ürün özellikleri, Stok durumu..." yazıları dönüyor

3. **Yazıyor göstergesi**
   - AI cevap hazırlarken 3 nokta zıplıyor
   - Gri baloncuk içinde

---

## 🌐 Çoklu Dil Desteği

**Şu anda:**
- Sadece Türkçe

**Gelecekte:**
- İngilizce, Almanca eklenebilir
- `app()->getLocale()` ile tespit edilir
- OpenAI'ye dil bilgisi gönderilir

---

## 📱 Responsive (Mobil Uyum)

### Masaüstü (1024px+)
- Sağ altta sabit buton
- 10 saniye sonra otomatik açılır
- Geniş sohbet penceresi (400px genişlik)

### Tablet/Mobil (< 1024px)
- Sağ altta küçük buton
- Otomatik açılmaz (kullanıcı tıklamalı)
- Dar sohbet penceresi (tam genişlik)

---

## ⚙️ Ayarlar ve Özelleştirme

### Otomatik Açılma Süresi
**Dosya:** `floating-widget.blade.php`
**Satır 52:**
```javascript
setTimeout(() => { ... }, 10000);  // 10000 = 10 saniye
```

**Değiştirmek için:**
- `10000` → `5000` yapılırsa 5 saniyede açılır

---

### Renk Teması
**Dosya:** `floating-widget.blade.php`
**Satır 200:**
```html
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
```

**Değiştirmek için:**
- Hex renk kodlarını değiştir
- Örnek: `#667eea` → `#FF5733` (turuncu)

---

### Maksimum Mesaj Uzunluğu
**Dosya:** `PublicAIController.php`
**Satır 2559:**
```php
'message' => 'required|string|min:1|max:1000',
```

**Değiştirmek için:**
- `max:1000` → `max:2000` yapılırsa 2000 karakter kabul eder

---

## 🛠️ Sorun Giderme

### Robot butonu görünmüyor
**Neden?**
- JavaScript yüklenmemiş
- Asset compile edilmemiş

**Çözüm:**
```bash
npm run prod
php artisan cache:clear
```

---

### Mesaj gönderilmiyor
**Neden?**
- OpenAI API key yanlış
- Rate limit aşılmış
- Workflow ayarları hatalı

**Kontrol:**
```bash
# Log dosyasına bak
tail -f storage/logs/laravel.log

# OpenAI key kontrol
cat .env | grep OPENAI
```

---

### Eski konuşmalar görünmüyor
**Neden?**
- Session ID kaybedilmiş
- Veritabanı bağlantısı kopuk

**Çözüm:**
- Tarayıcı console'da `localStorage.getItem('ai_chat_session_id')` kontrol et
- Redis flush: `redis-cli FLUSHDB`

---

## 📚 Özet

1. **Kullanıcı** siteye girer → Robot butonu görünür
2. **Butona tıklar** → Sohbet penceresi açılır
3. **Mesaj yazar** → Sunucuya gönderilir
4. **Sunucu** mesajı alır → Workflow başlar
5. **Workflow adımları:**
   - Context Builder (sayfa bilgisi)
   - Category Detection (ne soruluyor?)
   - Product Search (ürün araması)
   - Stock Sorter (stokta olanlar önce)
   - AI Response (OpenAI yanıt üretiyor)
   - Message Saver (veritabanına kaydet)
6. **Yanıt** kullanıcıya gösteriliyor → Chat penceresinde görünür
7. **Kullanıcı** yeni mesaj yazarsa → Süreç tekrar başlar (ama robot eski konuşmayı hatırlıyor)

---

**Oluşturulma Tarihi:** 6 Ocak 2025
**Yazan:** iXtif Geliştirme Ekibi
**Hedef Kitle:** Teknik olmayan okuyucular
