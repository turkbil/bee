# 🎯 AI V2 SİSTEMİ - BASİT TEST REHBERİ

Bu dokümanda AI sistemimizin v2 güncellemelerini nasıl test edeceğimizi basit adımlarla anlatıyoruz. **Hiç teknik bilginiz olmasa bile** bu testleri rahatlıkla yapabilirsiniz.

---

## 📋 ÖNCESİ HAZIRLIK

### □ Adım 1: Sayfaya Giriş Yapın
- Admin panele giriş yapın: `http://laravel.test/admin`
- Kullanıcı adınız ve şifrenizle giriş yapın

### □ Adım 2: AI Bölümüne Gidin
- Sol menüden **"AI"** yazan bölüme tıklayın
- AI ana sayfası açılmalı

---

## 🔄 TEST BÖLÜMLERİ

## 🎯 BÖLÜM 1: AKILLI YANIT SİSTEMİ TESTİ

Bu testte AI'nın daha akıllı ve çeşitli yanıtlar verip vermediğini kontrol edeceğiz.

### □ Test 1.1: Blog Yazısı İsteyin
1. **Nereye gideceğiz**: AI chat sayfası (`/admin/ai`)
2. **Ne yazacağız**: "Laravel hakkında 300 kelimelik blog yazısı yaz"
3. **Neyi kontrol edeceğiz**:
   - ❌ **KÖTÜ**: Yanıt 1-2-3 şeklinde maddeler halinde gelirse
   - ✅ **İYİ**: Yanıt düz paragraflar ve başlıklar şeklinde gelirse

### □ Test 1.2: SEO Analizi İsteyin
1. **Ne yazacağız**: "www.example.com adresinin SEO analizini yap"
2. **Neyi kontrol edeceğiz**:
   - ✅ **İYİ**: Yanıtda tablo formatında sonuçlar varsa
   - ✅ **İYİ**: "Anahtar Kelime Analizi" gibi başlıklar varsa
   - ❌ **KÖTÜ**: Sadece sıralı liste (1,2,3) şeklinde gelirse

### □ Test 1.3: Çeviri İsteyin
1. **Ne yazacağız**: "Merhaba, nasılsın? Bu metni İngilizceye çevir"
2. **Neyi kontrol edeceğiz**:
   - ✅ **İYİ**: Sadece "Hello, how are you?" şeklinde çeviri gelirse
   - ❌ **KÖTÜ**: "Bu çeviridir: Hello, how are you?" şeklinde ek açıklama gelirse

---

## 💰 BÖLÜM 2: KREDİ SİSTEMİ TESTİ

Bu testte "Token" kelimesinin "Kredi" olarak değiştirildiğini kontrol edeceğiz.

### □ Test 2.1: Sayfa İçeriği Kontrolü
1. **Nereye gideceğiz**: AI ayarlar sayfası (`/admin/ai/settings`)
2. **Ne arayacağız**: Sayfada hiçbir yerde "Token" kelimesi **olmamalı**
3. **Neyi kontrol edeceğiz**:
   - ✅ **İYİ**: "Kredi Bakiyesi" yazıyorsa
   - ✅ **İYİ**: "Kredi Satın Al" yazıyorsa
   - ❌ **KÖTÜ**: "Token" kelimesi herhangi bir yerde görünüyorsa

### □ Test 2.2: Kredi Kullanımı
1. **Ne yapacağız**: AI'dan herhangi bir şey isteyin
2. **Neyi kontrol edeceğiz**:
   - ✅ **İYİ**: Kredi bakiyesi azalıyorsa
   - ✅ **İYİ**: Ekranda "X kredi kullanıldı" mesajı çıkıyorsa
   - ❌ **KÖTÜ**: Herhangi bir yerde "token" kelimesi görünüyorsa

---

## 🎨 BÖLÜM 3: MARKA BİLGİSİ AKILLI KULLANIMI

Bu testte AI'nın marka bilginizi ne zaman kullanıp ne zaman kullanmadığını kontrol edeceğiz.

### □ Test 3.1: SEO Analizi (Marka Bilgisi KULLANMAMALI)
1. **Ne yazacağız**: "SEO analizi yap"
2. **Neyi kontrol edeceğiz**:
   - ✅ **İYİ**: AI yanıtında firma adınız **geçmiyorsa**
   - ✅ **İYİ**: Genel SEO tavsiyeleri veriyorsa
   - ❌ **KÖTÜ**: "Sizin firmanız için" gibi kişisel ifadeler varsa

### □ Test 3.2: Blog Yazısı (Marka Bilgisi KULLANMALI)
1. **Ne yazacağız**: "Firmamız hakkında tanıtım yazısı yaz"
2. **Neyi kontrol edeceğiz**:
   - ✅ **İYİ**: AI yanıtında firma adınız **geçiyorsa**
   - ✅ **İYİ**: Sektörünüzle ilgili özel bilgiler varsa
   - ❌ **KÖTÜ**: Genel, şablonumsu bir metin geliyorsa

---

## 🌐 BÖLÜM 4: HERKESE AÇIK AI TESTİ

Bu testte AI'yı admin paneli dışından da kullanabildiğimizi kontrol edeceğiz.

### □ Test 4.1: Widget Testi
1. **Nereye gideceğiz**: Site ana sayfası (`http://laravel.test`)
2. **Ne arayacağız**: Sayfanın sağ alt köşesinde chat balonu
3. **Neyi kontrol edeceğiz**:
   - ✅ **İYİ**: Chat balonu görünüyorsa
   - ✅ **İYİ**: Balona tıklayınca chat penceresi açılıyorsa
   - ❌ **KÖTÜ**: Hiçbir şey görünmüyorsa

### □ Test 4.2: Misafir Kullanımı
1. **Ne yapacağız**: Çıkış yapın (logout)
2. **Nereye gideceğiz**: Ana sayfa (`http://laravel.test`)
3. **Neyi kontrol edeceğiz**:
   - ✅ **İYİ**: Giriş yapmadan da chat kullanabiliyorsanız
   - ✅ **İYİ**: "X kredi kaldı" mesajı görüyorsanız
   - ❌ **KÖTÜ**: "Giriş yapın" hatası alıyorsanız

---

## 🎛️ BÖLÜM 5: YÖNETİCİ PANELİ TESTLERİ

Bu testler sadece yönetici yetkisi olan kişiler içindir.

### □ Test 5.1: AI Sağlayıcı Ayarları
1. **Nereye gideceğiz**: AI Provider Ayarları (`/admin/ai/providers`)
2. **Ne göreceğiz**: OpenAI, Claude, DeepSeek gibi seçenekler
3. **Neyi kontrol edeceğiz**:
   - ✅ **İYİ**: Her sağlayıcı için "Aktif/Pasif" düğmesi varsa
   - ✅ **İYİ**: "Öncelik Sırası" ayarı varsa
   - ✅ **İYİ**: "Test Et" butonu çalışıyorsa

### □ Test 5.2: Kredi Paket Yönetimi
1. **Nereye gideceğiz**: Kredi Paketleri (`/admin/ai/credits/packages`)
2. **Ne göreceğiz**: Farklı kredi paketleri listesi
3. **Neyi kontrol edeceğiz**:
   - ✅ **İYİ**: "Tenant Paketleri" (büyük paketler) varsa
   - ✅ **İYİ**: "User Paketleri" (küçük paketler) varsa
   - ✅ **İYİ**: Fiyat bilgileri doğru görünüyorsa

---

## 🧪 ÖZEL TESTLER

### □ Performans Testi
1. **Ne yapacağız**: AI'ya uzun bir metin yazdırın
2. **Neyi kontrol edeceğiz**:
   - ✅ **İYİ**: Yanıt 5 saniyeden kısa sürede geliyorsa
   - ❌ **KÖTÜ**: 10 saniyeden uzun sürüyorsa

### □ Hata Durumu Testi
1. **Ne yapacağız**: İnternet bağlantınızı kesin
2. **AI'ya bir şey yazın**:
   - ✅ **İYİ**: "Bağlantı hatası" mesajı geliyorsa
   - ✅ **İYİ**: Sayfa donmuyorsa
   - ❌ **KÖTÜ**: Sayfa hata veriyor ve kapanıyorsa

---

## 📊 TEST SONUÇLARI KAYDI

Her test için sonuçları işaretleyin:

### ✅ BAŞARILI TESTLER
- [ ] Blog yazısı düzgün formatda geldi
- [ ] SEO analizi tablo formatında geldi
- [ ] Çeviri sadece çeviri olarak geldi
- [ ] Hiçbir yerde "token" kelimesi yok
- [ ] Kredi bakiyesi doğru görünüyor
- [ ] SEO analizinde marka bilgisi yok
- [ ] Blog yazısında marka bilgisi var
- [ ] Chat widget ana sayfada görünüyor
- [ ] Misafir olarak da chat kullanılabiliyor
- [ ] Provider ayarları çalışıyor
- [ ] Kredi paketleri görünüyor
- [ ] Performans kabul edilebilir
- [ ] Hata durumları iyi yönetiliyor

### ❌ BAŞARISIZ TESTLER
- [ ] [Buraya başarısız testleri yazın]

### 💭 NOTLARINIZ
```
[Buraya test sırasında fark ettiğiniz şeyleri yazın]
```

---

## 🆘 SORUN ÇÖZME

### Sık Karşılaşılan Sorunlar:

**Soru**: Chat açılmıyor
**Çözüm**: Sayfayı yenileyin (F5), hala açılmıyorsa tarayıcıyı değiştirip tekrar deneyin

**Soru**: "Kredi yetersiz" hatası alıyorum
**Çözüm**: AI Ayarlar → Kredi Yönetimi bölümünden kredi ekleyin

**Soru**: AI çok yavaş yanıt veriyor
**Çözüm**: Beklenen durum, 3-5 saniye normaldir. 10+ saniye sürmesi sorun.

**Soru**: Marka bilgileri yanlış
**Çözüm**: AI → Profil Ayarları bölümünden firma bilgilerinizi güncelleyin

---

## 📞 DESTEK

Test sırasında sorunla karşılaşırsanız:

1. **Ekran görüntüsü alın**
2. **Hata mesajını not edin**
3. **Hangi adımda hata olduğunu belirtin**
4. **Tarayıcı ve işletim sistemi bilginizi paylaşın**

---

**🔄 En son güncelleme**: 6 Ağustos 2025
**📋 Test sürümü**: AI V2.0
**⏱️ Tahmini test süresi**: 45 dakika