# Teknik Sunum Kuralları - MAP Dokümantasyon Sistemi

## 🎯 TEMEL YAKLAŞIM
- **Amaç**: Yatırımcılar, mühendisler, TÜBİTAK, KOSGeB için teknik sunum
- **Hedef**: İşi biliyor izlenimi yaratmak, ince detayları göstermek
- **Ton**: Profesyonel, yücelten, teknik yetkinlik vurgusu
- **Format**: Sunum odaklı, web sitesi değil
- **AI** AI - ai gibi yazımlar yasak. Yapay Zeka kelimesi kullanıyoruz.
- **Sayı Yok** Olabildiğince sayı vermekten uzaklaşalım. Sanki her şey sonsuz olabiliyor gibi hareket edelim. Yapay zekalar için sayı belirtme. Modul sayısı verme.
- **Önce Öğren**: Tüm yapımızı yazmadan ve yazılarını üretmeden önce araştır ve ögren. Sayfalarda KOD asla verme. Kullanım metodu verme. Fakat düşünme ve calısma mekanizması hakkında yazı ile bilgiler vereceksin. 

## 🔧 TEKNİK YAZIM KURALLARI

### DİL VE AÇIKLAMA
- **Türkçe öncelik**: Her yanıt Türkçe olacak
- **Teknik terim açıklaması**: Her teknik terimin yanında Türkçe karşılığı VE ne işe yaradığı
  - **Format**: `Technical Term` <span class="text-muted">(türkçe çeviri)</span><br><span class="text-sm text-secondary">→ Ne işe yaradığı açıklaması</span>
  - **Örnek**: `Cross-tenant data leak` <span class="text-muted">(müşteri verilerinin birbirine karışması)</span><br><span class="text-sm text-secondary">→ Bir müşterinin verilerini diğer müşterinin görmesi</span>
- **PARANTEZ İÇİ ZORUNLU AÇIKLAMA**: Parantez içinde olan her terim mutlaka açıklanmalı
- **AI kısaltması yasak**: Her zaman "yapay zeka" yazılacak
- **Native kelimesi**: Gereksiz "native" kullanımından kaçınacağız
- **Hardcode asla**: Sistem tamamen dinamik, hardcode kullanmayacağız

### YÜCELTEN DİL KULLANIMI
- **Çeşitli kelimeler**: "mükemmel" tekrarı yapmayacağız
- **Alternatif yücelten kelimeler**: 
  - Üstün, kusursuz, gelişmiş, profesyonel, titizlikle
  - Optimal, en iyi, başarılı, ileri teknoloji, dünya standartlarında
- **Kendi geliştirdiğimiz vurgusu**: Her fırsatta "tamamen kendi geliştirdiğimiz"
- **Sektörde benzersiz**: Rakiplerden farklılığımızı vurgulayacağız
- **Dünya standartlarının üzerinde**: Sadece standart değil, üstünde olduğumuz belirtilecek

### TASARIM KURALLARI

#### HERO SECTION
- **Modern ve şık**: Sade ama efektli tasarım
- **Full width**: Hero section sayfa genişliğinde olacak
- **Animasyonlu efektler**: Gradient animasyonları, floating particles
- **Pembe yasak**: Pembe renkler kullanılmayacak, mavi tonlar tercih edilecek
- **Yapay zeka + mobil vurgusu**: En önemli iki unsur öne çıkacak
- **Kısa ve öz**: Gereksiz kutular ve butonlar kaldırılacak, sadece ana mesaj
- **Minimal yaklaşım**: Stat kutuları ve action button'lar kullanılmayacak

#### GRID SİSTEMİ
- **Col-6 kuralı**: Tüm card'lar col-6 (2 sütunlu) olacak
- **Responsive**: md:grid-cols-2 kullanacağız
- **Feature grid**: `minmax(420px, 1fr)` ile geniş kutular

#### LİSTE FORMATI
- **Liste önceliği**: Karışık metin yerine liste kullanacağız
- **Spacing**: `space-y-2` ile rahat aralıklar
- **Format**: `<ul class="list-none space-y-2">` standardı

#### TEMA SİSTEMİ
- **CSS Variables**: Perfect dark/light mode
- **Smooth transitions**: Tema geçişlerinde animasyon
- **Consistent colors**: Değişken renk sistemleri

## 📊 VERİ YAKLAŞIMI

### ARAŞTIRMA ZORUNLULUĞU
- **Analiz önce**: Sistemi analiz etmeden veri yazmayacağız
- **Gerçek veriler**: Mevcut sistem özelliklerini araştıracağız
- **Doğru bilgi**: Varsayım yapmak yerine kod incelemesi yapacağız

### DOĞRU VERİ KAYNAKLARI
- **Codebase analizi**: Task tool ile sistem incelemesi
- **Database tabloları**: Gerçek veri yapıları
- **Config dosyaları**: Sistem ayarları ve parametreler
- **Service classes**: Gerçek implementasyon detayları

## 🚫 YAPILMAYACAKLAR

### TASARIM YASAK LİSTESİ
- **Telefon numarası**: İletişim bilgileri yazmayacağız
- **Bootstrap grid**: Tailwind CSS kullanacağız
- **FontAwesome**: Lucide icons kullanacağız
- **Col-4 kutular**: Sadece col-6 kullanacağız

### İÇERİK YASAK LİSTESİ
- **Hardcode veriler**: Dinamik olmayan bilgiler
- **Yanlış servis isimleri**: OpenAI gibi yanlış varsayımlar
- **Abartılı sayılar**: Gerçek olmayan metrikler
- **Çelişkili bilgiler**: Sistem dışı özellikler

### TEKRAR YASAK LİSTESİ
- **Mükemmel kelimesi**: Sürekli tekrar etmeyeceğiz
- **Aynı açıklamalar**: Çeşitli ifadeler kullanacağız
- **Benzer yapılar**: Her sayfa farklı approach

## 🎨 TASARIM PATTERN'LERİ

### KART YAPISI
```html
<div class="feature-card">
    <div class="feature-icon">
        <i data-lucide="icon-name"></i>
    </div>
    <h3>Yücelten Başlık <span class="text-sm text-muted">(açıklama)</span></h3>
    <p>Kısa açıklama metni</p>
    <div class="code-block">
        <ul class="list-none space-y-2">
            <li>• <span class="tech-highlight">Teknik Terim</span> <span class="text-muted">(türkçe)</span></li>
        </ul>
    </div>
</div>
```

### GRID YAPISI
```html
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- 6 adet kart buraya gelecek -->
</div>
```

### TEKNİK HIGHLIGHT - YENİ FORMAT
```html
<span class="tech-highlight">Technical Term</span> 
<span class="text-muted">(türkçe çeviri)</span><br>
<span class="text-sm text-secondary">→ Ne işe yaradığı açıklaması</span>
```

**Açıklama Şablonu Kuralları:**
- **Türkçe çeviri**: Parantez içinde, text-muted class ile
- **İşlev açıklaması**: Ok (→) ile başlayan, text-sm text-secondary class ile
- **Ayrım**: Çeviri ve açıklama farklı satırlarda olacak
- **Boyut**: Açıklama metni daha büyük ve okunabilir olacak (text-sm)
- **Renk**: Açıklama secondary color ile vurgulanacak

**ZORUNLU KURAL: PARANTEZ İÇİ HER TERİM AÇIKLANACAK**
- Sitede parantez içinde olan HER ŞEY açıklanmalı
- Parantez içindeki terimler teknik kelime ya da yabancı kelimedir
- Her terimin ne işe yaradığı mutlaka yazılmalı
- Sadece Türkçe çeviri yetmez, işlevi de belirtilmeli
- Format: `(türkçe çeviri)` + `→ Ne işe yaradığı açıklaması`

### DOSYA ORGANIZASYONU
- **CSS ve JS ayrı dosyalar**: HTML'den bağımsız styles.css ve script.js
- **Modüler yapı**: Her dosya kendi sorumluluğunda
- **Temiz kod**: HTML sadece yapı, CSS sadece stil, JS sadece fonksiyon

## 🔄 SÜREÇ KURALLARI

### SAYFA HAZIRLIK AŞAMALARI
1. **Analiz**: Sistem ve mevcut özellikleri araştır
2. **Veri toplama**: Gerçek bilgileri derle
3. **Taslak hazırla**: Yapı ve içerik planı
4. **Uygula**: Kodlama ve tasarım
5. **Test et**: Dark/light mode ve responsive kontrol

### KALITE KONTROL
- **Responsive test**: Tüm cihazlarda test
- **Theme test**: Dark/light mode geçişleri
- **Content review**: Türkçe açıklamalar kontrolü
- **Link check**: Navigation ve scroll işlevleri
- **Performance**: Sayfa yükleme hızı

## 🎯 BAŞARI KRİTERLERİ

### TEKNİK YETKINLIK GÖSTERGELERİ
- **Kod kalitesi**: Temiz, okunabilir kod
- **Sistem mimarisi**: Karmaşık yapıları basit anlatma
- **Optimizasyon**: Performans vurgusu
- **Güvenlik**: Güvenlik yaklaşımları

### SUNUM ETKİLİLİĞİ
- **Görsel çekicilik**: Modern, şık tasarım
- **Bilgi yoğunluğu**: Detaylı teknik içerik
- **Anlaşılabilirlik**: Karmaşık konuları basit anlatma
- **Profesyonellik**: Ciddi, güvenilir hava

## 📝 YAZIM STANDARTLARI

### BAŞLIKLAR
- **Ana başlık**: 2.5rem, font-weight: 700
- **Alt başlık**: 1.5rem, font-weight: 600
- **Açıklama**: 1.1rem, color: secondary
- **Türkçe açıklama**: text-sm, text-secondary

### RENKLENDİRME
- **Primary**: #3b82f6 (mavi)
- **Secondary**: #64748b (gri)
- **Accent**: #7c3aed (mor)
- **Success**: #10b981 (yeşil)
- **Muted**: #94a3b8 (açık gri)

### SPACING
- **Kartlar arası**: gap-6
- **Liste elemanları**: space-y-2
- **Bölümler arası**: mb-8
- **Padding**: p-6 (kartlar için)

## 🔧 TABLO TASARIM KURALLARI

### KARŞILAŞTIRMA TABLOLARI
- **3 Platform Karşılaştırma**: WordPress, Wix & Canva, Bizim Sistemimiz
- **Yıldız Puanlama**: 5 yıldız üzerinden değerlendirme (⭐⭐⭐⭐⭐)
- **Adil Puanlama**: Acımasız olmayan, gerçekçi değerlendirme
- **Sticky Header Yasak**: Tablo header'ı sabit olmayacak
- **Mobil Responsive**: data-label attribute'leri ile mobil uyumlu
- **Kategori Sınırı**: Maksimum 5-6 karşılaştırma kategorisi
- **Odaklanmış İçerik**: Mobil uygulama, analiz-raporlama, maliyet gibi uzun kategoriler kaldırılacak

### TABLO İÇERİK KURALLARI
- **WordPress İyileştirme**: Dil sistemi gibi güçlü olduğu alanlar vurgulanacak
- **Wix & Canva Adaleti**: Özellikle maliyet konusunda uygun fiyat vurgulanacak
- **Ücret Bilgisi**: Mobil uygulama gibi konularda somut fiyat aralıkları
- **Kısa Açıklamalar**: Her özellik için (parantez içi) + → açıklama formatı

## 🔧 TEKNOLOJİ STACK KURALLARI

### SADELEŞME PRENSİPLERİ
- **Kısa Açıklamalar**: Sadece teknoloji adı + (ne olduğu)
- **Aşırı Detay Yasak**: Uzun açıklamalar ve → ok işaretli detaylar kaldırılacak
- **Temiz Görünüm**: Teknoloji adı + kısa açıklama + özellik etiketi
- **6 Kategori**: Backend, Admin Panel, Frontend, Mobile, Geliştirici Araçları, Yapay Zeka

### AÇIKLAMA FORMATI
```html
<span class="font-semibold">Teknoloji Adı <span class="text-xs text-muted">(kısa açıklama)</span></span>
<span class="tech-highlight">Özellik Etiketi</span>
```

## 🎯 GÜVENLIK BÖLÜMÜ KURALLARI

### LİSTE FORMATINA DÖNÜŞTÜRME
- **Güvenlik özellikleri**: Diğer feature card'lar gibi liste formatında
- **6 Güvenlik Katmanı**: SQL Injection, CSRF, XSS, Input Validation, Authentication, Data Encryption
- **Açıklama Formatı**: Her güvenlik özelliği için (türkçe) + → işlevi
- **Profesyonel Sunum**: Çok katmanlı güvenlik sistemi vurgusu

## 📄 İÇ SAYFA KURALLARI

### AI SYSTEM SAYFASI
- **Teknik Detaylar**: 27 AI feature, 9 katmanlı prompt hiyerarşisi
- **DeepSeek API**: Entegrasyon detayları
- **Token Management**: Gerçek token yönetimi
- **Weighted Scoring**: Puanlama sistemi açıklaması

### TENANT SYSTEM SAYFASI
- **Multi-tenant Mimari**: Database isolation detayları
- **Domain-based Tenancy**: Çalışma prensibi
- **Cross-tenant Data Leak Prevention**: Güvenlik katmanları
- **Teknik Implementasyon**: Gerçek kod örnekleri

### MOBILE APP SAYFASI
- **Flutter Cross-platform**: Geliştirme detayları
- **Native Performance**: Performans metrikleri
- **REST API Integration**: Entegrasyon açıklaması
- **Offline Support**: Çevrimdışı çalışma özellikleri

### İÇ SAYFA TASARIM KURALLARI
- **Tutarlı Yapı**: Ana sayfa ile aynı tasarım dili
- **Derin Analiz**: Teknik detaylar ve gerçek veri örnekleri
- **Profesyonel Sunum**: Yatırımcı odaklı içerik
- **Responsive Design**: Tüm cihazlarda mükemmel görünüm

## 🔄 GÜNCEL DÜZENLEMELER (17.07.2025)

### TAMAMLANAN İYİLEŞTİRMELER
- ✅ Hero section kısaltıldı, stat kutuları kaldırıldı
- ✅ Tablo sticky header kaldırıldı
- ✅ Karşılaştırma tablosu 3 platform ile sınırlandı
- ✅ WordPress dil sistemi puanı iyileştirildi (2/5 → 3/5)
- ✅ Wix & Canva maliyet puanı iyileştirildi (3/5 → 4/5)
- ✅ Mobil uygulama kısmına ücret bilgileri eklendi
- ✅ Teknoloji stack sadeleştirildi, açıklamalar kısaltıldı
- ✅ Güvenlik bölümü liste formatına çevrildi
- ✅ Tüm teknik terimlere parantez içi açıklamalar eklendi

### KALDIRILAN BÖLÜMLER
- ❌ Analiz & Raporlama kategorisi
- ❌ Teknik Destek kategorisi
- ❌ Maliyet kategorisi
- ❌ Hero section stat kutuları
- ❌ Action button'lar
- ❌ Sticky table header

Bu kuralları her sayfada uygulayacağız. Tutarlılık ve kalite odaklı yaklaşım ile profesyonel sunum hazırlayacağız.
