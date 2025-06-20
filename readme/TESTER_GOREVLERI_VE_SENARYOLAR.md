# 🧪 TESTER GÖREVLERİ VE TEST SENARYOLARI
## Turkbil Bee - Kapsamlı Test Rehberi

---

## 📋 **GENEL TEST STRATEJİSİ**

### 🎯 **Test Öncelikleri:**
1. **Kritik İşlevsellik** - CRUD işlemleri, yetkilendirme
2. **Multi-tenant İzolasyon** - Tenant veri güvenliği
3. **Performance** - Sayfa yükleme, cache sistemi
4. **Security** - XSS, CSRF, SQL injection koruması
5. **User Experience** - Frontend akışları, responsive tasarım

---

## 🏗️ **MODÜL BAZLI TEST GÖREVLERİ**

---

## 📄 **PAGE MODÜLÜ TEST SENARYOLARI**

### 🔧 **CRUD İşlemleri**

#### **CREATE (Sayfa Oluşturma)**
```
✅ Test Adımları:
1. Admin panelde Sayfalar → Yeni Sayfa
2. Başlık, slug, içerik, meta bilgileri doldur
3. SEO alanlarını kontrol et
4. Status (aktif/pasif) seç
5. Kaydet butonuna tıkla

✅ Kontrol Edilecekler:
- Başarı mesajı görünüyor mu?
- Sayfa listesinde görünüyor mu?
- Slug otomatik oluşuyor mu?
- Frontend'de görüntüleniyor mu?
- Meta bilgileri kaydoluyor mu?

❌ Negatif Testler:
- Boş başlık ile kaydet
- Duplicate slug ile kaydet
- Çok uzun başlık (255+ karakter)
- XSS script ekleyerek kaydet
```

#### **READ (Sayfa Listeleme/Görüntüleme)**
```
✅ Listeleme Testleri:
- Sayfalar doğru sıralı görünüyor mu?
- Arama çalışıyor mu?
- Filtreleme (aktif/pasif) çalışıyor mu?
- Pagination düzgün çalışıyor mu?
- Per page seçenekleri çalışıyor mu? (10, 25, 50, 100)

✅ Detay Görüntüleme:
- Sayfa detayı açılıyor mu?
- Tüm alanlar doğru görünüyor mu?
- Edit/Delete butonları görünüyor mu?
- Frontend link çalışıyor mu?
```

#### **UPDATE (Sayfa Güncelleme)**
```
✅ Güncelleme Testleri:
1. Var olan sayfa seç → Edit
2. Başlık, içerik değiştir
3. Status değiştir
4. Meta bilgileri güncelle
5. Kaydet

✅ Kontroller:
- Değişiklikler kaydoluyor mu?
- Slug güncellenebiliyor mu?
- Frontend'de güncel hali görünüyor mu?
- Activity log kaydı oluşuyor mu?
```

#### **DELETE (Sayfa Silme)**
```
✅ Silme Testleri:
- Tek sayfa silme çalışıyor mu?
- Silme onay popup'ı görünüyor mu?
- Silinen sayfa listeden kalkıyor mu?
- Frontend'de 404 döndürüyor mu?
- İlişkili veriler de siliniyor mu?

✅ Toplu Silme:
- Multiple select çalışıyor mu?
- Bulk delete çalışıyor mu?
- Başarı mesajı doğru görünüyor mu?
```

### 🔄 **Özel Fonksiyonlar**

#### **Aktif/Pasif Durumu**
```
✅ Status Toggle:
- Aktif sayfa pasif olabiliyor mu?
- Pasif sayfa aktif olabiliyor mu?
- Frontend'de pasif sayfa görünmüyor mu?
- Toplu aktif/pasif çalışıyor mu?
```

#### **Arama ve Filtreleme**
```
✅ Arama Testleri:
- Başlık ile arama çalışıyor mu?
- İçerik içinde arama çalışıyor mu?
- Türkçe karakter arama çalışıyor mu?
- Boşluk ile arama çalışıyor mu?

✅ Filtreleme:
- Status filtreleme (Tümü, Aktif, Pasif)
- Tarih aralığı filtreleme
- Yazar filtreleme (varsa)
```

#### **SEO ve Meta Veriler**
```
✅ SEO Testleri:
- Meta title kaydoluyor mu?
- Meta description kaydoluyor mu?
- Meta keywords çalışıyor mu?
- OpenGraph tags oluşuyor mu?
- Sitemap.xml'e ekleniyor mu?
```

### 🔒 **Güvenlik Testleri**

#### **Yetkilendirme**
```
✅ Authorization Testleri:
- Yetkisiz kullanıcı sayfaya erişemiyor mu?
- CRUD işlemleri için yetki kontrolü var mı?
- Tenant izolasyonu çalışıyor mu?
- Cross-tenant veri erişimi engellenmiş mi?
```

#### **Validasyon**
```
✅ Input Validation:
- Required field validasyonu
- Max length validasyonu
- Email format validasyonu (varsa)
- XSS koruması aktif mi?
- CSRF token kontrolü var mı?
```

---

## 🎨 **PORTFOLIO MODÜLÜ TEST SENARYOLARI**

### 🔧 **CRUD İşlemleri**

#### **Kategori Yönetimi**
```
✅ Kategori CRUD:
1. Yeni kategori oluştur
2. Kategori listele
3. Kategori güncelle  
4. Kategori sil

✅ Kontroller:
- Parent-child kategori ilişkisi çalışıyor mu?
- Kategori silindiğinde portfolio'lar ne oluyor?
- Kategori sayıları doğru hesaplanıyor mu?
```

#### **Portfolio CRUD**
```
✅ Portfolio İşlemleri:
1. Yeni portfolio oluştur
2. Kategori ata
3. Görsel yükle (multiple)
4. Proje detayları doldur
5. Client bilgileri ekle
6. Aktif/pasif durumu ayarla

✅ Görsel Yönetimi:
- Multiple image upload çalışıyor mu?
- Image resize otomatik mi?
- Image optimization çalışıyor mu?
- Görsel silme çalışıyor mu?
- Ana görsel belirleme çalışıyor mu?
```

### 🖼️ **Frontend Testleri**

#### **Portfolio Listeleme**
```
✅ Listeleme Sayfası:
- Portfolio'lar doğru listeleniyor mu?
- Kategori filtreleme çalışıyor mu?
- Arama çalışıyor mu?
- Pagination çalışıyor mu?
- Grid/List view switch çalışıyor mu?

✅ Detay Sayfası:
- Portfolio detayı açılıyor mu?
- Görsel galeri çalışıyor mu?
- Next/Previous navigation var mı?
- İlgili portfolio'lar gösteriliyor mu?
```

### 🎯 **Widget Entegrasyonu**
```
✅ Portfolio Widget:
- Widget render ediliyor mu?
- Kategori bazlı filtreleme çalışıyor mu?
- Limit parametresi çalışıyor mu?
- Cache sistemi aktif mi?
```

---

## 🤖 **AI MODÜLÜ TEST SENARYOLARI**

### 💬 **Konuşma Yönetimi**

#### **Yeni Konuşma**
```
✅ Konuşma CRUD:
1. Yeni konuşma başlat
2. Başlık belirle
3. İlk mesajı gönder
4. AI yanıtı al
5. Konuşmaya devam et

✅ Kontroller:
- AI yanıt alınıyor mu?
- Token sayısı hesaplanıyor mu?
- Konuşma geçmişi saklanıyor mu?
- Streaming response çalışıyor mu?
```

#### **Token Yönetimi**
```
✅ Token Testleri:
- Token limiti kontrol ediliyor mu?
- Kullanılan token sayılıyor mu?
- Limit aşıldığında uyarı veriyor mu?
- Token resetleme çalışıyor mu?
```

### 🎯 **Prompt Yönetimi**
```
✅ Custom Prompt:
- Özel prompt oluşturulabiliyor mu?
- Prompt kategorileri çalışıyor mu?
- Prompt şablonları kullanılabiliyor mu?
- Prompt önizleme çalışıyor mu?
```

### 🔒 **Güvenlik ve İzolasyon**
```
✅ AI Güvenlik:
- Zararlı prompt filtreleniyor mu?
- Tenant izolasyonu sağlanmış mı?
- Konuşma geçmişi güvenli mi?
- API key korunuyor mu?
```

---

## 🎨 **STUDIO MODÜLÜ TEST SENARYOLARI**

### 🖱️ **Drag & Drop Editor**

#### **Temel İşlevsellik**
```
✅ Editor Testleri:
1. Studio sayfasını aç
2. Element library'yi kontrol et
3. Element sürükle-bırak
4. Element properties düzenle
5. Sayfa kaydet
6. Önizleme yap

✅ Kontroller:
- GrapesJS yükleniyor mu?
- Elementler sürüklenebiliyor mu?
- Properties panel çalışıyor mu?
- Responsive preview çalışıyor mu?
```

#### **Element Yönetimi**
```
✅ Element İşlemleri:
- Text element ekleme/düzenleme
- Image element ekleme/düzenleme
- Button element ekleme/düzenleme
- Container/Row/Column ekleme
- Custom HTML ekleme

✅ Styling:
- CSS class ekleme
- Inline style düzenleme
- Responsive ayarlar
- Animation effects
```

#### **Sayfa Yönetimi**
```
✅ Sayfa İşlemleri:
- Yeni sayfa oluştur
- Sayfa kaydet/yükle
- Sayfa klonla
- Sayfa sil
- Sayfa export/import

✅ Template Sistemi:
- Hazır template'ler yükleniyor mu?
- Custom template kaydetme
- Template kategorileri
```

---

## 🧩 **WIDGET MANAGEMENT TEST SENARYOLARI**

### 🔧 **Widget CRUD**

#### **Widget Oluşturma**
```
✅ Widget İşlemleri:
1. Yeni widget oluştur
2. Widget tipini seç
3. Settings şemasını tanımla
4. Items şemasını tanımla (varsa)
5. Template'i düzenle
6. Önizleme yap
7. Kaydet

✅ Kontroller:
- Widget render ediliyor mu?
- Settings form çalışıyor mu?
- Items management çalışıyor mu?
- Preview sistem çalışıyor mu?
```

#### **Widget Kategorileri**
```
✅ Kategori Testleri:
- Kategori oluştur/düzenle/sil
- Widget-kategori ilişkisi
- Kategori bazlı filtreleme
- Kategori sıralaması
```

### 🎯 **Widget Embed Sistemi**
```
✅ Embed Testleri:
- Shortcode sistemi çalışıyor mu?
- Widget ID ile çağırma
- Parameter geçirme
- Cache sistemi aktif mi?
```

### 🎨 **Template Engine**
```
✅ Handlebars Testleri:
- Template syntax çalışıyor mu?
- Data binding doğru mu?
- Helper fonksiyonlar çalışıyor mu?
- Conditional rendering çalışıyor mu?
```

---

## 👥 **USER MANAGEMENT TEST SENARYOLARI**

### 🔧 **Kullanıcı CRUD**

#### **Kullanıcı İşlemleri**
```
✅ User CRUD:
1. Yeni kullanıcı oluştur
2. Profil bilgileri doldur
3. Rol ata
4. İzinleri ayarla
5. Aktif/pasif durumu
6. Email doğrulama

✅ Kontroller:
- Email unique kontrolü
- Şifre hash'lenme
- Profil resmi upload
- Email verification
```

#### **Rol ve İzin Yönetimi**
```
✅ Role Management:
- Yeni rol oluştur
- İzinleri ata
- Modül bazlı izinler
- Rol hierarchy
- Rol atama/kaldırma

✅ Permission Tests:
- Page permissions
- Module permissions
- CRUD permissions
- Custom permissions
```

### 🔐 **Kimlik Doğrulama**
```
✅ Authentication:
- Login/logout işlemleri
- Password reset
- Email verification
- Remember me functionality
- Session management

✅ Authorization:
- Route protection
- Method level protection
- Resource based authorization
- Tenant isolation
```

---

## 🎭 **THEME MANAGEMENT TEST SENARYOLARI**

### 🎨 **Tema CRUD**

#### **Tema İşlemleri**
```
✅ Theme CRUD:
1. Yeni tema oluştur/yükle
2. Tema aktif et
3. Tema ayarlarını düzenle
4. Tema önizleme
5. Tema export/import
6. Tema sil

✅ Kontroller:
- Tema dosyaları doğru yükleniyor mu?
- CSS/JS dosyaları include ediliyor mu?
- Tema cache'i çalışıyor mu?
- Fallback tema sistemi çalışıyor mu?
```

#### **Tema Özelleştirme**
```
✅ Customization:
- Renk şeması değiştirme
- Font seçenekleri
- Logo upload
- Favicon upload
- Custom CSS ekleme

✅ Responsive Test:
- Mobile görünüm
- Tablet görünüm
- Desktop görünüm
- Cross-browser test
```

---

## 📊 **SETTING MANAGEMENT TEST SENARYOLARI**

### ⚙️ **Ayar Grupları**

#### **Genel Ayarlar**
```
✅ Setting Groups:
- Site bilgileri
- Email ayarları
- Cache ayarları
- Security ayarları
- API ayarları

✅ Setting Types:
- Text input
- Textarea
- Select dropdown
- Checkbox
- File upload
- Color picker
```

#### **Tenant Özel Ayarlar**
```
✅ Tenant Settings:
- Site title/description
- Contact information
- Social media links
- Analytics codes
- Custom scripts
```

---

## 🏢 **TENANT MANAGEMENT TEST SENARYOLARI**

### 🌐 **Multi-Tenant İşlemleri**

#### **Tenant CRUD**
```
✅ Tenant Management:
1. Yeni tenant oluştur
2. Domain atama
3. Database oluşturma
4. Module activation
5. Theme assignment
6. User assignment

✅ İzolasyon Testleri:
- Veri izolasyonu
- File izolasyonu
- Cache izolasyonu
- Session izolasyonu
```

#### **Domain Yönetimi**
```
✅ Domain Tests:
- Domain binding
- Subdomain support
- Custom domain
- SSL certificate
- Domain redirects
```

---

## 📢 **ANNOUNCEMENT TEST SENARYOLARI**

### 📝 **Duyuru CRUD**
```
✅ Announcement CRUD:
1. Yeni duyuru oluştur
2. Başlık, içerik, özet
3. Publish date ayarla
4. Kategori ata (varsa)
5. Featured image
6. SEO bilgileri

✅ Özel Özellikler:
- Scheduled publishing
- Announcement categories
- Featured announcements
- Archive system
```

---

## 🔧 **MODULE MANAGEMENT TEST SENARYOLARI**

### 📦 **Modül İşlemleri**
```
✅ Module Management:
- Modül aktifleştirme/deaktif
- Modül ayarları
- Dependency check
- Module installation
- Module update

✅ Permission Integration:
- Module permissions
- User-module binding
- Role-module permissions
```

---

## 🌐 **FRONTEND TEST SENARYOLARI**

### 📱 **Responsive Design**
```
✅ Device Tests:
- Mobile (320px-768px)
- Tablet (768px-1024px)
- Desktop (1024px+)
- Large screens (1200px+)

✅ Browser Tests:
- Chrome (latest)
- Firefox (latest)  
- Safari (latest)
- Edge (latest)
- Internet Explorer 11
```

### ⚡ **Performance Tests**
```
✅ Speed Tests:
- Page load time (<3 sec)
- Image optimization
- CSS/JS minification
- Cache headers
- CDN integration

✅ SEO Tests:
- Meta tags
- Structured data
- Sitemap.xml
- Robots.txt
- OpenGraph tags
```

---

## 🔒 **GÜVENLİK TEST SENARYOLARI**

### 🛡️ **Security Tests**

#### **Input Validation**
```
✅ XSS Prevention:
- Script tag filtering
- HTML entity encoding
- Attribute sanitization
- URL validation

✅ SQL Injection:
- Prepared statements
- Input sanitization
- Query parameter validation
- ORM protection
```

#### **CSRF Protection**
```
✅ CSRF Tests:
- Token validation
- Form protection
- AJAX requests
- Token refresh
```

#### **Authentication Security**
```
✅ Auth Security:
- Password hashing
- Session hijacking protection
- Brute force protection
- Account lockout
```

---

## ⚡ **PERFORMANCE TEST SENARYOLARI**

### 🚀 **Cache Tests**
```
✅ Redis Cache:
- Cache hit/miss ratios
- Cache invalidation
- Tag-based caching
- TTL management

✅ Application Cache:
- Route caching
- Config caching
- View caching
- Query caching
```

### 📊 **Database Performance**
```
✅ Query Optimization:
- N+1 query detection
- Slow query monitoring
- Index usage
- Join optimization
```

---

## 🧪 **TEST OTOMASYON ARAÇLARI**

### 🔧 **Test Commands**
```bash
# Tüm testleri çalıştır
php artisan test

# Modül bazlı testler
php artisan test --group=pages
php artisan test --group=portfolio
php artisan test --group=ai
php artisan test --group=users

# Feature testleri
php artisan test tests/Feature/

# Unit testleri  
php artisan test tests/Unit/

# Coverage raporu
php artisan test --coverage

# Parallel testing
php artisan test --parallel
```

### 📊 **Test Reporting**
```bash
# HTML coverage raporu
php artisan test --coverage-html coverage

# JUnit XML raporu
php artisan test --log-junit results.xml

# Clover XML raporu
php artisan test --coverage-clover coverage.xml
```

---

## 📋 **TEST CHECKLIST**

### ✅ **Her Sprint Öncesi**
- [ ] Test ortamı hazır mı?
- [ ] Test veritabanı temizlendi mi?
- [ ] Seed data yüklendi mi?
- [ ] Test kullanıcıları oluşturuldu mu?

### ✅ **Her Feature Testi Sonrası**
- [ ] Tüm CRUD işlemleri çalışıyor mu?
- [ ] Validation kuralları doğru mu?
- [ ] Authorization kontrolleri var mı?
- [ ] Frontend görünümler hatasız mı?
- [ ] Mobile uyumluluk var mı?

### ✅ **Release Öncesi**
- [ ] Tüm automated testler geçiyor mu?
- [ ] Performance testleri yapıldı mı?
- [ ] Security testleri tamamlandı mı?
- [ ] Cross-browser testler yapıldı mı?
- [ ] Multi-tenant izolasyon test edildi mi?

---

## 🎯 **BAŞARI KRİTERLERİ**

### 📊 **Test Metrikleri**
- ✅ Test coverage: %80+
- ✅ All critical tests: 100% pass
- ✅ Performance tests: <3 sec page load
- ✅ Security tests: 0 vulnerabilities
- ✅ Mobile compatibility: 100%

### 🏆 **Kalite Standartları**
- ✅ Zero critical bugs
- ✅ Zero security vulnerabilities  
- ✅ 100% feature functionality
- ✅ Cross-browser compatibility
- ✅ Multi-tenant data isolation

---

*Bu doküman tester'ın sistematik olarak tüm modülleri test etmesi için hazırlanmıştır. Her test senaryosu için beklenen sonuçlar ve başarı kriterleri tanımlanmıştır.*

**Versiyon**: 1.0  
**Tarih**: 19 Haziran 2025  
**Hazırlayan**: Claude AI - Test Senaryoları Analisti