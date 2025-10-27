# TURKBIL BEE MODÜL TEST SENARYOLARI

Bu dokümanda her modül için detaylı test senaryoları yer almaktadır. Testler multi-tenant yapıya uygun olarak hazırlanmıştır.

## TEST ORTAMI HAZIRLIĞI

### Genel Gereksinimler
- Laravel 11 kurulu ve çalışır durumda
- MySQL veritabanı ve Redis çalışır durumda  
- Tenant veritabanları oluşturulmuş
- Test verileri seeded edilmiş
- PHPUnit ve Feature testleri için environment hazır

### Test Verilerinin Hazırlanması
```bash
# Seeder'ları çalıştır
php artisan db:seed
php artisan tenants:seed

# Test veritabanını hazırla
php artisan migrate --database=testing
php artisan tenants:migrate --database=testing
```

---

## 1. AI MODÜLÜ TEST SENARYOLARI

### 1.1 CRUD İşlemleri

#### 1.1.1 Konuşma Oluşturma (Create)
**Test Adımları:**
1. Admin paneline giriş yap (`/admin/ai`)
2. Yeni konuşma başlat
3. Prompt seç veya yazı
4. "Gönder" butonuna tıkla

**Beklenen Sonuçlar:**
- Konuşma `ai_conversations` tablosuna kaydediliyor
- İlk mesaj `ai_messages` tablosuna kaydediliyor  
- AI'dan yanıt alınıyor ve görüntüleniyor
- Token sayısı hesaplanıyor

**Test Kodu:**
```php
public function test_ai_conversation_creation()
{
    $this->actingAs($this->adminUser)
         ->post('/admin/ai/generate', [
             'prompt' => 'Test mesajı',
             'context' => 'test context'
         ])
         ->assertJson(['success' => true]);
         
    $this->assertDatabaseHas('ai_conversations', [
        'user_id' => $this->adminUser->id
    ]);
}
```

#### 1.1.2 Konuşma Listeleme (Read)
**Test Adımları:**
1. `/admin/ai/conversations` sayfasına git
2. Konuşma listesini kontrol et
3. Arama fonksiyonunu test et
4. Filtreleme seçeneklerini test et

**Beklenen Sonuçlar:**
- Tüm konuşmalar listeleniyor
- Sayfalama çalışıyor
- Arama sonuçları doğru
- Kullanıcı sadece kendi konuşmalarını görüyor

#### 1.1.3 Konuşma Güncelleme (Update)
**Test Adımları:**
1. Mevcut konuşmayı aç
2. Prompt değiştir
3. Başlık değiştir
4. Kaydet

**Beklenen Sonuçlar:**
- Prompt ID güncelleniyor
- Başlık güncelleniyor
- Değişiklik kaydediliyor

#### 1.1.4 Konuşma Silme (Delete)
**Test Adımları:**
1. Konuşma listesinde sil butonuna tıkla
2. Onaylama pop-up'ını onayla

**Beklenen Sonuçlar:**
- Konuşma tablodan siliniyor
- İlişkili mesajlar da siliniyor
- Soft delete uygulanıyor

### 1.2 Özel Fonksiyonaliteler

#### 1.2.1 AI Yanıt Alma
**Test Adımları:**
1. Geçerli API anahtarıyla mesaj gönder
2. Geçersiz API anahtarıyla test et
3. Uzun prompt ile test et
4. Limit aşımında test et

**Beklenen Sonuçlar:**
- Geçerli durumda yanıt alınıyor
- Hata durumlarında uygun mesajlar gösteriliyor
- Token limitleri kontrol ediliyor

#### 1.2.2 Prompt Yönetimi
**Test Adımları:**
1. Yeni prompt oluştur
2. Prompt'u aktif/pasif yap
3. Prompt'u konuşmaya ata
4. Prompt'u sil

### 1.3 Yetkilendirme Testleri
**Test Senaryoları:**
- Admin kullanıcısı tüm işlemleri yapabilir
- Normal kullanıcı sadece görüntüleme yapabilir
- Misafir kullanıcı erişim alamaz
- Tenant izolasyonu çalışıyor

### 1.4 API Testleri
**Endpoint Testleri:**
- `POST /admin/ai/generate` - Mesaj gönderme
- `POST /admin/ai/update-conversation-prompt` - Prompt güncelleme
- `GET /admin/ai/conversations` - Konuşma listesi
- `DELETE /admin/ai/conversations/{id}` - Konuşma silme

---

## 2. ANNOUNCEMENT MODÜLÜ TEST SENARYOLARI

### 2.1 CRUD İşlemleri

#### 2.1.1 Duyuru Oluşturma (Create)
**Test Adımları:**
1. Admin paneline giriş yap
2. Duyurular sayfasına git
3. "Yeni Duyuru" butonuna tıkla
4. Form doldur:
   - Başlık (required)
   - İçerik (required) 
   - Durum (active/inactive)
   - Yayın tarihi
   - Bitiş tarihi
5. Kaydet

**Beklenen Sonuçlar:**
- Duyuru `announcement` tablosuna kaydediliyor
- Tüm alanlar doğru kaydediliyor
- Slug otomatik oluşturuluyor
- Tenant izolasyonu çalışıyor

**Test Kodu:**
```php
public function test_announcement_creation()
{
    $announcementData = [
        'title' => 'Test Duyuru',
        'content' => 'Test içerik',
        'status' => 'active',
        'publish_date' => now(),
        'end_date' => now()->addDays(7)
    ];
    
    $this->actingAs($this->adminUser)
         ->post('/admin/announcements', $announcementData)
         ->assertRedirect()
         ->assertSessionHas('success');
         
    $this->assertDatabaseHas('announcement', [
        'title' => 'Test Duyuru',
        'status' => 'active'
    ]);
}
```

#### 2.1.2 Duyuru Listeleme (Read)
**Test Adımları:**
1. Duyurular listesi sayfasına git
2. Tüm duyurular görüntüleniyor mu?
3. Arama fonksiyonu çalışıyor mu?
4. Durum filtresi çalışıyor mu?
5. Sayfalama çalışıyor mu?

**Beklenen Sonuçlar:**
- Aktif duyurular listeleniyor
- Pasif duyurular ayrı gösteriliyor
- Arama sonuçları doğru
- Sayfalama çalışıyor

#### 2.1.3 Duyuru Güncelleme (Update)
**Test Adımları:**
1. Mevcut duyuru düzenle
2. Başlık değiştir
3. İçerik güncelle
4. Durum değiştir
5. Kaydet

**Beklenen Sonuçlar:**
- Tüm değişiklikler kaydediliyor
- Slug güncelleniyor (gerekirse)
- Updated_at alanı güncelleniyor

#### 2.1.4 Duyuru Silme (Delete)
**Test Adımları:**
1. Duyuru listesinde sil butonuna tıkla
2. Onaylama dialogunu onayla

**Beklenen Sonuçlar:**
- Duyuru tablodan siliniyor
- Soft delete uygulanıyor
- İlişkili veriler kontrol ediliyor

### 2.2 Toplu İşlemler (Bulk Operations)

#### 2.2.1 Toplu Silme
**Test Adımları:**
1. Birden fazla duyuru seç
2. "Seçilenleri Sil" butonuna tıkla
3. Onaylama

**Beklenen Sonuçlar:**
- Seçilen duyurular siliniyor
- Başarı mesajı gösteriliyor

#### 2.2.2 Toplu Durum Değiştirme
**Test Adımları:**
1. Birden fazla duyuru seç
2. Durum değiştirme seçeneğini kullan
3. Aktif/Pasif yap

**Beklenen Sonuçlar:**
- Seçilen duyuruların durumu değişiyor
- Toplu güncelleme çalışıyor

### 2.3 Frontend Testleri

#### 2.3.1 Duyuru Listesi Görünümü
**Test Adımları:**
1. Frontend duyuru sayfasına git
2. Aktif duyurular görüntüleniyor mu?
3. Tarih sıralaması doğru mu?
4. Sayfalama çalışıyor mu?

#### 2.3.2 Duyuru Detay Sayfası
**Test Adımları:**
1. Duyuru linkine tıkla
2. Detay sayfası açılıyor mu?
3. Tüm bilgiler görüntüleniyor mu?

### 2.4 Yetkilendirme Testleri
- Admin: Tüm CRUD işlemleri
- Editor: Oluşturma ve düzenleme
- Viewer: Sadece görüntüleme
- Guest: Frontend erişim

### 2.5 Validasyon Testleri
**Test Edilen Alanlar:**
- Başlık (required, max:255)
- İçerik (required)
- Durum (in:active,inactive)
- Tarih formatları

---

## 3. PAGE MODÜLÜ TEST SENARYOLARI

### 3.1 CRUD İşlemleri

#### 3.1.1 Sayfa Oluşturma (Create)
**Test Adımları:**
1. "Yeni Sayfa" butonuna tıkla
2. Form doldur:
   - Başlık (required)
   - İçerik (required)
   - Slug (auto-generated)
   - Meta bilgileri
   - Durum
3. Kaydet

**Beklenen Sonuçlar:**
- Sayfa `pages` tablosuna kaydediliyor
- Slug otomatik oluşturuluyor
- Meta bilgileri kaydediliyor
- SEO alanları doldurulmuş

#### 3.1.2 Sayfa Listeleme (Read)
**Test Adımları:**
1. Sayfa listesini görüntüle
2. Arama yap
3. Durum filtrele
4. Sayfalamayı test et

#### 3.1.3 Sayfa Güncelleme (Update)
**Test Adımları:**
1. Mevcut sayfayı düzenle
2. İçerik güncelle
3. Meta bilgileri değiştir
4. Kaydet

#### 3.1.4 Sayfa Silme (Delete)
**Test Adımları:**
1. Sayfayı sil
2. Onaylama

### 3.2 SEO Testleri
- Meta title düzgün kaydediliyor
- Meta description doğru
- Keywords kaydediliyor
- Open Graph etiketleri

### 3.3 Slug Testleri
- Otomatik slug oluşturma
- Benzersiz slug kontrolü
- Türkçe karakter dönüşümü
- URL dostu format

### 3.4 Frontend Testleri
- Sayfa URL'si çalışıyor
- İçerik doğru görüntüleniyor
- Meta bilgileri head'de
- 404 sayfası çalışıyor

---

## 4. PORTFOLIO MODÜLÜ TEST SENARYOLARI

### 4.1 Kategori Yönetimi

#### 4.1.1 Kategori CRUD
**Test Adımları:**
1. Yeni kategori oluştur
2. Kategori listesini görüntüle
3. Kategori düzenle
4. Kategori sil

**Beklenen Sonuçlar:**
- Kategori `portfolio_categories` tablosuna kaydediliyor
- Hiyerarşik yapı destekleniyor
- Slug otomatik oluşturuluyor

### 4.2 Portfolio CRUD

#### 4.2.1 Portfolio Oluşturma
**Test Adımları:**
1. Yeni portfolio oluştur
2. Form doldur:
   - Başlık
   - Açıklama
   - Kategori seç (required)
   - Görseller yükle
   - Teknolojiler
   - URL'ler
   - Durum
3. Kaydet

**Beklenen Sonuçlar:**
- Portfolio `portfolios` tablosuna kaydediliyor
- Görseller tenant storage'a kaydediliyor
- Kategori ilişkisi kuruluyor

#### 4.2.2 Görsel Yükleme Testleri
**Test Adımları:**
1. Ana görsel yükle
2. Galeri görselleri yükle
3. Geçersiz format test et
4. Boyut limiti test et

**Beklenen Sonuçlar:**
- Geçerli formatlar kabul ediliyor
- Boyut limitleri kontrol ediliyor
- Thumbnail oluşturuluyor
- Dosya adları güvenli

### 4.3 Frontend Testleri

#### 4.3.1 Portfolio Listesi
**Test Adımları:**
1. Portfolio listesi sayfasına git
2. Kategoriye göre filtrele
3. Arama yap
4. Sayfalama test et

#### 4.3.2 Portfolio Detay
**Test Adımları:**
1. Portfolio detayına git
2. Görseller görüntüleniyor mu?
3. Galeri çalışıyor mu?
4. Bilgiler doğru mu?

### 4.4 İlişkisel Veri Testleri
- Portfolio-Kategori ilişkisi
- Portfolio-Görseller ilişkisi
- Kategori silme etkisi
- Cascade delete testleri

---

## 5. USER MANAGEMENT MODÜLÜ TEST SENARYOLARI

### 5.1 Kullanıcı CRUD

#### 5.1.1 Kullanıcı Oluşturma
**Test Adımları:**
1. Yeni kullanıcı formu
2. Gerekli alanları doldur:
   - Ad Soyad (required)
   - Email (required, unique)
   - Şifre (required, min:8)
   - Rol seç
   - Durum
3. Kaydet

**Beklenen Sonuçlar:**
- Kullanıcı `users` tablosuna kaydediliyor
- Şifre hash'leniyor
- Email unique kontrolü
- Rol atanıyor

#### 5.1.2 Kullanıcı Düzenleme
**Test Adımları:**
1. Mevcut kullanıcıyı düzenle
2. Bilgileri güncelle
3. Rol değiştir
4. Durum güncelle

#### 5.1.3 Kullanıcı Silme
**Test Adımları:**
1. Kullanıcı sil
2. İlişkili verileri kontrol et
3. Soft delete test et

### 5.2 Rol ve Yetki Yönetimi

#### 5.2.1 Rol CRUD
**Test Adımları:**
1. Yeni rol oluştur
2. Yetkiler ata
3. Rol düzenle
4. Rol sil

**Beklenen Sonuçlar:**
- Rol `roles` tablosuna kaydediliyor
- Yetki ilişkileri kuruluyor
- Korumalı roller silinemiyor

#### 5.2.2 Yetki Atama
**Test Adımları:**
1. Kullanıcıya rol ata
2. Direkt yetki ver
3. Yetki kontrolü test et

### 5.3 Modül Yetkileri

#### 5.3.1 Modül Yetki Testleri
**Test Adımları:**
1. Her modül için yetki kontrolü
2. Middleware testleri
3. Erişim reddedilme testleri

### 5.4 Aktivite Logları
- Kullanıcı aktivitelerinin loglanması
- Login/logout kayıtları
- CRUD işlem logları

---

## 6. WIDGET MANAGEMENT MODÜLÜ TEST SENARYOLARI

### 6.1 Widget CRUD

#### 6.1.1 Widget Oluşturma
**Test Adımları:**
1. Yeni widget oluştur
2. Widget türü seç
3. Ayarları yapılandır
4. İçerik ekle
5. Kaydet

**Beklenen Sonuçlar:**
- Widget `widgets` tablosuna kaydediliyor
- Ayarlar JSON formatında kaydediliyor
- Kategori ilişkisi kuruluyor

#### 6.1.2 Widget Önizleme
**Test Adımları:**
1. Widget önizlemesini aç
2. Farklı ayarlarla test et
3. Responsive görünüm test et

### 6.2 Widget Kategorileri

#### 6.2.1 Kategori Yönetimi
**Test Adımları:**
1. Kategori oluştur
2. Widget'ları kategoriye ata
3. Kategori filtrele

### 6.3 Widget Render Testleri

#### 6.3.1 Widget Gösterim
**Test Adımları:**
1. Widget'ı sayfaya yerleştir
2. Shortcode render test et
3. Embed kodunu test et

### 6.4 Widget Şablonları
- Handlebars şablon sistemi
- Shortcode parser
- CSS/JS varlık yükleme

---

## 7. SETTING MANAGEMENT MODÜLÜ TEST SENARYOLARI

### 7.1 Ayar Grupları

#### 7.1.1 Grup CRUD
**Test Adımları:**
1. Yeni ayar grubu oluştur
2. Grup düzenle
3. Grup sil

### 7.2 Ayar Yönetimi

#### 7.2.1 Ayar CRUD
**Test Adımları:**
1. Yeni ayar oluştur
2. Farklı veri tipleri test et:
   - Text
   - Number
   - Boolean
   - Image
   - File
   - JSON
3. Ayar güncelle

#### 7.2.2 Form Builder
**Test Adımları:**
1. Dynamic form oluştur
2. Form alanları test et
3. Validasyon kuralları

### 7.3 Tenant Ayarları
- Tenant-specific ayarlar
- Global ayarlar
- Ayar kalıtım sistemi

---

## 8. THEME MANAGEMENT MODÜLÜ TEST SENARYOLARI

### 8.1 Tema CRUD

#### 8.1.1 Tema Yükleme
**Test Adımları:**
1. Yeni tema yükle
2. Tema dosyalarını kontrol et
3. Tema aktif et

#### 8.1.2 Tema Özelleştirme
**Test Adımları:**
1. Tema ayarlarını değiştir
2. Renk paletini güncelle
3. Logo değiştir

### 8.2 Tema Uyumluluk
- Modül uyumluluğu
- Widget uyumluluğu
- Responsive tasarım

---

## 9. STUDIO MODÜLÜ TEST SENARYOLARI

### 9.1 Görsel Editör

#### 9.1.1 Page Builder
**Test Adımları:**
1. Yeni sayfa oluştur
2. Blokları ekle
3. Düzenle
4. Önizle
5. Yayınla

#### 9.1.2 Widget Entegrasyonu
**Test Adımları:**
1. Widget'ları editöre ekle
2. Widget ayarlarını değiştir
3. Real-time önizleme

### 9.2 Blok Sistemi
- Blok ekleme/çıkarma
- Blok sırası değiştirme
- Blok ayarları

---

## 10. TENANT MANAGEMENT MODÜLÜ TEST SENARYOLARI

### 10.1 Tenant CRUD

#### 10.1.1 Tenant Oluşturma
**Test Adımları:**
1. Yeni tenant oluştur
2. Domain ata
3. Veritabanı oluştur
4. Modülleri aktif et

#### 10.1.2 Tenant İzolasyonu
**Test Adımları:**
1. Tenant verilerinin izole olduğunu kontrol et
2. Cross-tenant erişim testi
3. Veritabanı izolasyonu

### 10.2 Domain Yönetimi
- Domain ekleme/çıkarma
- SSL sertifikası
- Subdomain desteği

---

## 11. MODULE MANAGEMENT MODÜLÜ TEST SENARYOLARI

### 11.1 Modül Yönetimi

#### 11.1.1 Modül Aktif/Pasif
**Test Adımları:**
1. Modülü pasif yap
2. Erişimi kontrol et
3. Tekrar aktif et

#### 11.1.2 Modül Ayarları
**Test Adımları:**
1. Modül slug ayarları
2. Yetki ayarları
3. Konfigürasyon değişiklikleri

### 11.2 Modül İzinleri
- Modül bazlı yetkilendirme
- Route middleware testleri
- API endpoint koruması

---

## TEST ÇALIŞTIRMA KOMUTLARI

### Tüm Testleri Çalıştırma
```bash
# Tüm testleri çalıştır
php artisan test

# Belirli bir modülün testlerini çalıştır
php artisan test --testsuite=AI
php artisan test --testsuite=Portfolio

# Coverage raporu ile
php artisan test --coverage
```

### Test Veritabanı Yönetimi
```bash
# Test veritabanını temizle
php artisan migrate:fresh --env=testing

# Test verilerini ekle
php artisan db:seed --env=testing
```

### Browser Testleri (Dusk)
```bash
# Dusk testlerini çalıştır
php artisan dusk

# Belirli bir test grubu
php artisan dusk --group=auth
php artisan dusk --group=crud
```

---

## TEST RAPORLAMA

### Başarı Kriterleri
- Tüm CRUD işlemleri çalışıyor
- Yetkilendirme düzgün çalışıyor
- Tenant izolasyonu sağlanmış
- Frontend görünümler hatasız
- API endpoint'leri çalışıyor
- Validasyon kuralları geçerli

### Performans Testleri
- Sayfa yükleme süreleri
- Veritabanı sorgu optimizasyonu
- Cache performansı
- Dosya yükleme hızı

### Güvenlik Testleri
- SQL injection koruması
- XSS koruması
- CSRF token kontrolü
- File upload güvenliği
- Yetki eskalasyonu testleri

Bu test senaryoları düzenli olarak çalıştırılmalı ve sonuçları dokümante edilmelidir.