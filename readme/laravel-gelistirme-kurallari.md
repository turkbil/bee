# 🟢 Laravel + Tabler.io + Bootstrap + Livewire Geliştirme Kuralları

Sen, **Tabler.io Framework**, **Bootstrap**, **Livewire** ve **Laravel** teknolojilerinde uzman seviyede bir geliştirici olmak zorundasın. Laravel ekosisteminde ileri seviye bilgiye sahip olacak ve bu bilgiyle en doğru, en sürdürülebilir çözümleri üreteceksin. Sadece kod yazmayacak, aynı zamanda global standartlara uygun, yaratıcı ve uzun vadeli çözümler geliştireceksin.

Her tenantın kendi dilleri olduğu için sakın ola dillerle ilgili hardcode kullanma.

CSS ve JS gibi kısımlarda **Tabler.io** ve **Bootstrap** kurallarına göre düzgün şekilde CSS'i ve JS'i implement et. Livewire component'lerin JavaScript etkileşimi sadece Livewire'ın kendi API'si ile yapılacak.

**Ana Kural**: Ne iş yaparsan yap, kafandan çözüm üretme. Önceliğin her zaman dünya standartları, global yapı ve mevcut kullandığımız teknolojilerin yapısına uygun olmalı. Mevcut **Laravel**, **Tabler.io**, **Bootstrap** ve **Livewire** teknolojileri neyi gerektiriyorsa onları uygulayacağız. Tüm geliştirmelerimiz bu teknolojilerin standartlarına uygun olacak. Bu standartların olduğu yerde kendi geliştirmelerini yapma, global çözümleri kullan.

## 🌍 Genel Kurallar

1. **Standartlara Uyum**: Global standartlara, framework dökümantasyonuna ve proje yapısına uygun geliştir.
2. **Hardcode Yasak**: Hiçbir domain, URL veya site adresi hardcode edilmeyecek. Tüm veriler veritabanı veya config dosyalarından dinamik olarak alınacak.
3. **Geçici Çözüm Yok**: "Şimdilik böyle çalışsın" yaklaşımı yok. Her çözüm kalıcı, sürdürülebilir ve ölçeklenebilir olmalı.
4. **Admin Panel Önceliği**: Laravel admin panel (/admin) üzerinde geliştirme yapılır.
5. **Çift Dil Desteği**: Geliştirme sırasında `admin_languages` (admin için) ve `site_languages` (site için) tablolarına uyum sağlanır. Her geliştirme çok dilliliğe uygun olur.

## 🗄️ Veri ve Migration Kuralları

* SQL'e manuel işlem girmiyoruz. Veriler sadece seeder dosyaları üzerinden eklenir.
* Migration dosyaları sadece create migration dosyaları düzenlenerek hazırlanır.
* Yeni eklemeler veya değişiklikler mevcut create dosyalarını geliştirerek yapılır.
* Her migrate ve seeder dosyası kendi modules klasörü içinde yer alır.
* Commit öncesi manuel SQL, hardcode domain veya geçici çözüm bulunmadığı kontrol edilir.

## 🏗️ Sistem Yapısı

`http://laravel.test/admin/xxx` → Laravel admin panelimiz. Localhost:port yerine `laravel.test` (portsuz) kullanıyoruz. Hiçbir yere `laravel.test` hardcode edilmeyecek. Sistem dinamik, tenant yapısı ile çalışıyor ve domainler veritabanındaki `domains` tablosundan çekiliyor.

**Site Adresi Hiçbir Yerde Hardcode Olmayacak.**

## 🔗 API ve Modüler Yapı

* Tüm API'ler kendi modülleri içinde, planlı ve birbirleriyle aynı yapıda olacak.
* Models, Component ve Controller'lardan gelen tüm veriler API Controller ve API route'lara eklenecek.
* Admin paneldeki tüm linkler `/admin/xx` formatında olacak.
* Yapılan her işlem ilgili modülün kendi nwidart modules klasöründe düzenlenip geliştirilecek.
* Örnek: Page ile ilgili her API `modules/Page` klasöründen çekilecek.

## 📌 Sistem Yapısı (Özet)

1. **…site.com** → Ana site (Tailwind CSS)
2. **…site.com/admin** → Laravel Admin Panel (Tabler.io + Bootstrap + Livewire)

## 🔧 Framework Özel Kuralları

### 📊 Tabler.io + Bootstrap Standartları
* **UI Components**: Tabler.io'nun hazır componentlerini kullan
* **Grid System**: Bootstrap 5 grid yapısını takip et
* **Icons**: Tabler Icons setini kullan
* **Color Palette**: Tabler.io'nun varsayılan renk paletini kullan
* **Form Elements**: Tabler.io form componentlerini tercih et
* **Card Layout**: Admin panelinde Tabler.io card yapısını kullan

### ⚡ Livewire Entegrasyonu
* **Component Structure**: Her Livewire component kendi modülü içinde
* **Real-time Updates**: Livewire wire:poll ve wire:model kullan
* **JavaScript Interaction**: Sadece Livewire'ın kendi JavaScript API'sini kullan (**Alpine.js YOK**)
* **Event System**: Livewire events ile component arası iletişim
* **Validation**: Livewire'ın built-in validasyonunu kullan
* **Loading States**: Livewire loading state'lerini her yerde göster

## 🔧 Ek Kurallar

### ✅ Kod Kalitesi ve Düzen
* **Kod Tekrarsız (DRY)**: Aynı kodu iki kere yazma, reusable Livewire component oluştur.
* **Naming Standartları**: Değişken, fonksiyon ve dosya isimlerinde net, anlaşılır ve tutarlı isimlendirme kullan.
* **Kod Okunabilirliği**: Her geliştirici okuduğunda kolayca anlayabilmeli (gereksiz kısaltma, karışık yapı yok).

### ✅ Güvenlik ve Dayanıklılık
* **Validation Zorunlu**: API'lere ve Livewire'a gelen tüm veriler doğrulanmalı.
* **Yetkilendirme Kontrolü**: Her endpoint ve Livewire component için yetki kontrolü yapılmalı.
* **Hata Yönetimi**: Hatalar yakalanmalı ve kullanıcıya Tabler.io alert componentleri ile dönmeli.

### ✅ Dokümantasyon ve Süreç
* **Değişiklik Dökümantasyonu**: Her yeni geliştirme için kısa bir açıklama ve örnek eklenmeli.
* **Commit Mesajı Standardı**: Açıklayıcı ve tek tip commit mesajı (örnek: `feat(page): create Livewire component for pages listing`).
* **Kod İncelemesi Şart**: PR merge olmadan önce en az bir gözden geçirme yapılmalı.

### ✅ Test ve Kontrol
* **Livewire Test Zorunluluğu**: Önemli Livewire componentler için test yazılmalı.
* **Çoklu Dil Testi**: Dil ekleme/güncelleme işlemleri en az iki dilde test edilmeli.
* **Manual Check List**: Deploy öncesi hardcode, manuel SQL veya geçici çözüm taraması yapılmalı.

### ✅ Performans ve Ölçeklenebilirlik
* **Verimli Sorgular**: Gereksiz sorgu veya N+1 problemi olmamalı.
* **Cache Kullanımı**: Sık kullanılan veriler için uygun Laravel cache stratejisi uygulanmalı.
* **Livewire Lazy Loading**: Büyük datalar için Livewire lazy loading kullanılmalı.
* **Tabler.io Optimization**: Sadece kullanılan Tabler.io componentlerini yükle.

## 🎯 Admin Panel Özel Kuralları

### 🏗️ Sayfa Yapısı
* Her admin sayfası **Tabler.io layout** kullanacak
* **Breadcrumb** navigation her sayfada bulunacak
* **Page Header** Tabler.io standardına uygun olacak
* **Content Card** ile ana içerik sarmalanacak

### 📋 Tablo ve Listeler
* **DataTables** entegrasyonu Livewire ile yapılacak
* **Search**, **Filter**, **Pagination** Livewire ile
* **Action Buttons** Tabler.io button stillerinde
* **Status Badges** Tabler.io badge componentleri ile

### 📝 Form Yapısı
* **Form Layout** Tabler.io form yapısını takip edecek
* **Validation Messages** Tabler.io alert sistemini kullanacak
* **File Upload** Livewire ile entegre olacak
* **WYSIWYG Editor** admin paneline uygun olacak

## 🎨 Modül ve Component Standartları

### 📦 Module Pattern
* Her modül kendi **Livewire componentlerini** içerecek
* **Page Pattern Master**: Yeni modüller Page modül yapısını takip edecek
* **JSON Multilingual**: Çoklu dil desteği JSON formatında
* **SEO Ready**: Her modülde SEO meta tag desteği

### 🧩 Livewire Component Yapısı
* **Component Structure**: Her Livewire component kendi modülü içinde olacak
* **Data Methods**: Private getData() metoduyla veri çekimi yapılacak
* **Event Listeners**: Component arası iletişim için listener tanımlanacak
* **Search & Pagination**: Search ve pagination Livewire ile implemente edilecek

### 🎯 Blade Template Yapısı
* **Page Header**: Tabler.io page-header yapısını kullan
* **Card Layout**: Tüm içerikler card içinde gösterilecek
* **Table Structure**: Tabler.io table class'ları kullanılacak
* **Action Buttons**: wire:click ile Livewire metodlarına bağlanacak
* **Loading States**: wire:loading ile yükleme durumları gösterilecek

## 🛠️ Development Workflow

### 🚀 Geliştirme Adımları
1. **Modül Oluştur**: `php artisan module:make ModuleName`
2. **Livewire Component**: Modül içinde Livewire component oluştur
3. **Migration & Model**: Gerekirse veritabanı yapısını hazırla
4. **Seeder**: Gerekirse test verileri için seeder oluştur
5. **Routes**: Admin route'ları tanımla (`/admin/module-name`)
6. **Blade Views**: Tabler.io uyumlu view'ları hazırla
7. **Test**: Agent ile tam test yap

### 🔄 Test Döngüsü
* **Cache Temizliği**: Gerektiğinde tam cache temizliği yapılacak
* **Database Refresh**: Gerektiğinde fresh migration ile seeder çalıştırılacak
* **Module Cache**: Gerektiğinde module cache temizlenecek
* **Response Cache**: Gerektiğinde response cache temizlenecek
* **Telescope**: Gerektiğinde telescope verileri temizlenecek

### 📱 Responsive Design
* **Mobile First**: Önce mobile tasarım, sonra desktop
* **Bootstrap Breakpoints**: Bootstrap 5 breakpoint'lerini kullan
* **Tabler.io Mobile**: Tabler.io'nun mobile optimizasyonlarından yararlan

## 🎯 JavaScript Etkileşimi (Admin Panel)

### ✅ Kullanılacak JavaScript Teknolojileri
* **Livewire wire: direktifleri** (wire:click, wire:model, wire:loading, wire:poll)
* **Tabler.io'nun vanilla JavaScript'i** (modal, dropdown, toast, vb.)
* **Bootstrap 5 JavaScript componentleri** (tooltip, popover, collapse, vb.)

### ❌ Kullanılmayacak Teknolojiler
* **Alpine.js** - Admin panelde kullanılmayacak
* **Vue.js** - Admin panelde kullanılmayacak  
* **React.js** - Admin panelde kullanılmayacak

### 🔄 Livewire Event Sistemi
* **Event Gönderme**: $this->dispatch() metoduyla component arası iletişim
* **Event Dinleme**: protected $listeners array'i ile event dinleme
* **Browser Events**: Browser eventleri için dispatch kullanımı
* **Refresh Patterns**: $refresh ile component yenileme
* **Alert System**: Kullanıcı bilgilendirme için event sistemi

## 🎯 Özet Teknoloji Stacki

**Backend**: Laravel 12 + Livewire 3 + Nwidart Modules  
**Frontend Admin**: Tabler.io + Bootstrap 5 (**Alpine.js YOK**)  
**Frontend Site**: Tailwind CSS  
**Database**: MySQL + Redis (Cache)  
**Queue**: Laravel Horizon  
**Multi-tenancy**: Stancl Tenancy  

---

Bu doküman, Laravel + Tabler.io + Bootstrap + Livewire teknoloji stack'i ile geliştirme yapacak tüm geliştiriciler için rehber niteliğindedir. Tüm kuralların eksiksiz takip edilmesi, proje kalitesi ve sürdürülebilirliği için kritiktir.