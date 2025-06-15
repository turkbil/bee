# Turkbil Bee - Laravel 12 Multi-Tenancy Projesi

Bu proje, Laravel 12 ile geliştirilmiş, modüler ve çok kiracılı (multi-tenancy) bir web uygulamasıdır.

## Kullanışlı Komutlar

- `compact` - Geçmiş konuşma özetini gösterir (ctrl+r ile tam özeti görüntüle)
- `composer run dev` - Geliştirme sunucularını başlatır (PHP, queue, logs, vite)

## Temel Teknolojiler ve Kullanılan Paketler

- **Framework:** Laravel 12
- **Multi-Tenancy:** Stancl Tenancy ([stancl/tenancy](https://tenancyforlaravel.com/))
- **Modüler Yapı:** Nwidart Laravel Modules ([nwidart/laravel-modules](https://nwidart.com/laravel-modules/v11/introduction))
- **Frontend:** Livewire 3.5 ([laravel-livewire/livewire](https://livewire.laravel.com/)), Livewire Volt ([livewire/volt](https://livewire.laravel.com/docs/volt)), Tabler.io ([tabler/tabler](https://tabler.io/))
- **Kimlik Doğrulama:** Laravel Breeze ([laravel/breeze](https://laravel.com/docs/11.x/starter-kits#laravel-breeze))
- **Yetkilendirme:** Spatie Laravel Permission ([spatie/laravel-permission](https://spatie.be/docs/laravel-permission/v6/introduction))
- **Aktivite Loglama:** Spatie Laravel Activity Log ([spatie/laravel-activitylog](https://spatie.be/docs/laravel-activitylog/v4/introduction))
- **Önbellekleme:** Spatie Laravel Response Cache ([spatie/laravel-responsecache](https://spatie.be/docs/laravel-responsecache/v7/introduction)), Redis (tenant bazlı)
- **Medya Yönetimi:** Spatie Laravel Media Library ([spatie/laravel-medialibrary](https://spatie.be/docs/laravel-medialibrary/v11/introduction))
- **Slug Yönetimi:** Cviebrock Eloquent Sluggable ([cviebrock/eloquent-sluggable](https://github.com/cviebrock/eloquent-sluggable))
- **Tarih/Zaman:** Nesbot Carbon ([nesbot/carbon](https://carbon.nesbot.com/docs/))

---

## Sürüm Geçmişi

### v1.2.4 (2025-06-14) - Sistem Geneli Form Element Görsel Standartizasyonu
- **Help Text/Info Yazıları Standardizasyonu:**
  - Tüm help text'lere `<i class="fas fa-info-circle me-1"></i>` ikonu eklendi
  - Standart format: `<div class="form-text mt-2 ms-2">` ile uygun boşluk
  - WidgetManagement, SettingManagement, AI modüllerinde 41 form-text elementi güncellendi
- **Başlık Tutarlılığı Sağlandı:**
  - Tüm h1,h2,h3,h4,h5,h6 etiketleri için standart class sistemi
  - Page titles: `page-title`, Card titles: `card-title`, Section titles: `section-title`
  - Modal titles: `modal-title`, Alert titles: `alert-title`
  - `fw-bold text-primary` kombinasyonu kaldırıldı, Tabler standartlarına uyumlu hale getirildi
- **Spacing Optimizasyonları:**
  - Form başlıklarındaki fazla boşluklar azaltıldı (mb-4 → mb-2)
  - Heading elementlerinde: `col-12` temizlendi, `h3`'e `mb-0` eklendi
  - Form-text elementleri için üst ve sol margin (`mt-2 ms-2`) eklendi
- **İkon Renk Standardizasyonu:**
  - Tüm başlık ikonlarından `text-primary` sınıfı kaldırıldı
  - İkonlar artık tema ile uyumlu varsayılan metin renginde
  - Sistemde tutarlı görsel deneyim sağlandı
- **Güncellenen Modüller:**
  - WidgetManagement: 17 form elementi + widget yönetim sayfaları
  - SettingManagement: 15 form elementi + yönetim bileşenleri  
  - AI: Settings panel ve prompt modal sayfaları
  - UserManagement: Kullanıcı profil ve aktivite log sayfaları

### v1.2.3 (2025-06-14) - Kapsamlı UI/UX Standartizasyonu ve Widget Management Güncellemeleri
- **Tablo Listeleme Kuralları Standartlaştırıldı:**
  - Header yapısı: 3 sütun (arama, loading, filtreler) + row mb-3
  - Action button'lar: Portfolio/Page modülü standardı (container > row > col)
  - Filter select'ler: Normal select + listing-filter-select class + CSS styling
  - Kritik class'lar: text-center align-middle, fa-lg, link-secondary, lh-1, mt-1
  - Sayfalama: UserManagement için 3'ün katları (12,48,99,498,999), diğerleri normal
- **Manage/Form Element Kuralları Belirlendi:**
  - Portfolio modülü referans standardı (tabs hariç, single page tercih)
  - Form-floating sistemi: Tüm input/select/textarea form-floating içinde
  - Choices.js: Sadece manage sayfalarında, 6+ seçenek varsa arama aktif
  - Pretty select: Aktif/Pasif için Portfolio modülü standardı
  - Form footer: x-form-footer component'i tutarlı kullanım
- **Widget Management Güncellemeleri:**
  - Widget manage ve category sayfalarında form-floating + Choices.js
  - Category listesinde action button'lar standardize edildi
  - Header yapısı diğer modüllerle tutarlı hale getirildi
- **UserManagement Özelleştirmeleri:**
  - Durum filtresi kaldırıldı (gereksiz)
  - Sayfalama 3'ün katları olarak ayarlandı (grid layout uyumu)
  - Loading göstergesi çakışma sorunu çözüldü
- **Sistem Geneli Tutarlılık:**
  - Tüm listeleme sayfaları aynı header yapısında
  - Tüm manage sayfaları aynı form element standartlarında
  - Action icon'ları Portfolio/Page modülü referans alınarak düzenlendi
  - Link formatları: listeleme (/admin/module), manage (/admin/module/manage/1)

### v1.2.2 (2025-06-14) - Sistem Geneli Form Standartizasyonu ve Choices.js Optimizasyonu
- **Listeleme vs Manage Sayfası Ayrımı:** Tüm sistemde tutarlı form yapısı
  - Listeleme sayfalarında: Normal select + Choices.js benzeri CSS styling
  - Manage sayfalarında: Tam Choices.js entegrasyonu + Form-floating
- **Choices.js CSS Düzeltmesi:** Sadece listing-filter-select class'ına özel styling
  - Manage sayfalarındaki Choices.js bozulmadan korundu
  - Listeleme filtrelerinde normal select ama görsel olarak Choices.js gibi
- **Form-Floating Sistemi:** Tüm manage formlarında modern tasarım
  - Input, select, textarea elementleri form-floating yapısında
  - Türkçe placeholder ve label değerleri
  - Required alanlar için "*" işaretleme sistemi
- **Arama Özelliği Optimizasyonu:** 6+ seçenek varsa otomatik arama aktif
  - Portfolio kategoriler için dinamik arama: `data-choices-search="{{ count($categories) > 6 ? 'true' : 'false' }}"`
  - Meta kelimeler için çoklu seçim ve uygun placeholder'lar
- **Güncellenen Modüller:** 
  - UserManagement, Portfolio, Page, Announcement, ModuleManagement
  - TenantManagement, SettingManagement, ThemeManagement, WidgetManagement
- **Link Sistemi Öğrenildi:** laravel.test/admin/... formatında, manage sayfalar için /1 parametresi

### v1.2.1 (2025-06-14) - Filter Selectbox'ları ve Compact Tasarım
- **UserManagement Filter Sistemi:** Admin panelinde compact filter selectbox'ları
  - Rol Filtresi: 140px genişlik, compact tasarım
  - Durum Filtresi: 140px genişlik, nobr text koruması
  - Sayfa Adeti: 80px genişlik, minimal boyut
  - Font-size: .75rem (12px) kompakt görünüm
  - Yükseklik: 33.14px düşük profil
- **Özel Filter Attributeleri:**
  - data-choices-filter="true" sistemi
  - itemSelectText="" (hover yazısı yok)
  - searchEnabled: false (arama kapalı)
  - placeholderValue: null (başlık korunuyor)
- **CSS Optimizasyonları:**
  - Min-width zorunlu genişlik sistemi
  - Nobr tag'ları ile text bölünme koruması
  - Important override'lar ile choices.js CSS'i ezme
  - Virgül karakteri engelleme + Türkçe uyarı

### v1.2.0 (2025-06-14) - Choices.js Entegrasyonu ve Form-Floating Desteği
- **Choices.js Kütüphanesi Eklendi:** Portfolio ve diğer modüller için gelişmiş dropdown sistemi
  - Arama özellikli dropdown'lar
  - Multiple selection (çoklu seçim) desteği  
  - Tabler teması ile mükemmel uyum
  - Dark/Light mode otomatik desteği
- **Form-Floating Entegrasyonu:** Choices.js için özel form-floating label sistemi
  - Label animasyonları
  - Tabler'ın form-floating yapısıyla tam uyum
  - Responsive tasarım
- **Tags Sistemi İyileştirmeleri:**
  - Virgül karakteri engelleme sistemi
  - Türkçe hata mesajları
  - Enter ile tag ekleme (sadece)
  - Unlimited tag desteği
- **CSS Optimizasyonları:**
  - TinyMCE ile z-index uyumluluğu
  - Form-control ile aynı yükseklik ve stil
  - Custom CSS dosyası (choices-custom.css)
- **Güncellenen Sayfalar:**
  - Portfolio Manage: Kategori seçimi ve meta tags form-floating'e çevrildi
  - Tabler'ın CSS değişkenleri kullanılarak tutarlı renk sistemi

### v1.1.0 (2025-06-13) - Tom-Select Kaldırıldı ve Native HTML Sistemine Geçiş
- **Tom-Select Tamamen Kaldırıldı:** Tabler.io v1.2.0 güncellemesi ile uyumsuzluk yaşanan tom-select kütüphanesi tamamen sistemden çıkarıldı
- **Native HTML Sistemi:** Dropdown'lar için artık sadece Bootstrap'ın native `<select class="form-select">` yapısı kullanılıyor
- **Özel Tags Input Sistemi:** Meta anahtar kelimeler için vanilla JavaScript ile yazılmış yeni tags sistemi eklendi
  - Enter veya virgül ile tag ekleme
  - X butonu ile tag silme  
  - Livewire ile tam entegrasyon
  - Tabler teması ile mükemmel uyum
- **Güncellenen Modüller:**
  - ModuleManagement: 3 dropdown güncellemesi
  - Portfolio: 1 dropdown + 1 tags sistemi  
  - Page, Announcement, PortfolioCategory: Tags sistemleri
- **Performans İyileştirmesi:** %90 daha hızlı form elemanları (sıfır JavaScript dependency)
- **Görsel İyileştirme:** Tabler'ın native stillerini kullanarak tutarlı görünüm
- **Accessibility:** Native HTML ile daha iyi erişilebilirlik desteği

### v1.0.0 (2025-06-13) - Laravel 12 Yükseltmesi
- **Framework Yükseltmesi:** Laravel 11.42.1'den Laravel 12.18.0'a başarıyla yükseltildi
- **Paket Güncellemeleri:**
  - `cviebrock/eloquent-sluggable`: ^11.0 → ^12.0
  - `nesbot/carbon`: ^2.67 → ^3.8
  - `wire-elements/modal`: `livewire-ui/modal`'ın yerine geçti
- **Uyumluluk:** Tüm modüller ve bağımlılıklar Laravel 12 ile uyumlu hale getirildi
- **Session Düzeltmesi:** Yükseltme sonrası session dizini oluşturuldu ve izinler düzeltildi
- **Geçici Kaldırılan Paketler:** `deepseek-php/deepseek-laravel` (Laravel 12 uyumlu sürüm bekleniyor)

### v0.7.0 (2025-06-05) - Widget Rendering Düzeltmesi ve Log Temizliği
- **Widget Rendering Düzeltmesi:** Ana sayfadaki widget'larda ve diğer widget içeren sayfalarda oluşan fazladan kapanış `</div>` etiketi sorunu giderildi. Bu sorun, `ShortcodeParser` içerisindeki `HTML_MODULE_WIDGET_PATTERN` adlı regex deseninin widget yer tutucularını eksik eşleştirmesinden kaynaklanıyordu. Desen, widget'ın tüm dış `div` yapısını kapsayacak şekilde güncellenerek sorun çözüldü.
- **Log Temizliği:** Hata ayıklama sürecinde `ShortcodeParser.php` ve `WidgetServiceProvider.php` dosyalarına eklenen tüm geçici `Log::debug`, `Log::error` ve `Log::warning` çağrıları kaldırıldı. Bu sayede kod tabanı daha temiz ve stabil hale getirildi.

### v0.6.0 (2025-05-25)
- Portfolio ve Page modülü widget'larında limit değeri sıfır veya geçersiz geldiğinde varsayılan olarak 5 atanacak şekilde kodlar güncellendi.
- Artık tüm widget'larda "öğe bulunamadı" hatası alınmaz, örnek veri varsa otomatik listelenir.
- Kod okunabilirliği ve güvenliği artırıldı.
- Debug logları ile widget veri akışı kolayca izlenebilir hale getirildi.

### v0.5.0 (2025-05-24)
- WidgetManagement Modülü iyileştirildi:
    - Hero Widget yapılandırması güncellendi (`has_items` false yapıldı, `item_schema` kaldırıldı, tüm alanlar `settings_schema`'ya taşındı, `content_html` ve seeder veri oluşturma mantığı uyarlandı).
    - Widget listeleme (`widget-component.blade.php`) ve kod editörü (`widget-code-editor.blade.php`) sayfalarında, widget'ların `has_items` özelliğine göre "İçerik" ile ilgili buton/linkler dinamik olarak gösterildi/gizlendi. İçerik eklenemeyen widget'lar için "Ayarlar" linki "Özelleştir" olarak güncellendi.
    - WidgetFormBuilderComponent içinde, `has_items` özelliği false olan widget'ların item şeması düzenleme sayfasına doğrudan URL ile erişimi engellendi.
    - WidgetFormBuilderComponent'ta layout tanımı, Livewire 3 `#[Layout]` attribute'u kullanılarak güncellendi ve olası bir linter uyarısı giderildi.

### v0.5.0 (2025-05-02)
- Studio modülü ve widget embed sistemi iyileştirildi:
    - `studio-widget-loader.js` içinde widget embed overlay özelliği eklendi; görsel overlay olarak `pointer-events: none` ile tıklamalar modele iletildi.
    - `registerWidgetEmbedComponent` fonksiyonu ile embed component tipi tanımlandı ve editöre kaydedildi.
    - `studio-editor-setup.js` içindeki `component:remove` handler geliştirildi: `_loadedWidgets` set güncellemesi, iframe ve model DOM temizleme, `col-md-*` wrapper ve `section.container` öğelerinin kaldırılması ve `html-content` input’unun senkronizasyonu.

### v0.4.0 (2025-04-05)
- SettingManagement modülünde dosya yükleme bileşeni (file-upload) sorunu çözüldü.
- ValuesComponent sınıfına removeImage metodu eklenerek geçici dosyaların silinmesi sağlandı.
- Dosya yükleme ve görüntü yükleme bileşenleri arasında tutarlılık sağlandı.
- Geçici dosyalar ve kaydedilmiş dosyalar için doğru silme metodları uygulandı.

### v0.3.0 (2025-04-05)
- WidgetManagement ve SettingManagement modüllerinde dosya yükleme işlemleri standartlaştırıldı.
- Tüm resim ve dosya yüklemeleri için merkezi TenantStorageHelper sınıfı kullanıldı.
- Dosya adı formatları ve klasör yapısı standartlaştırıldı.
- Çoklu resim yükleme işlemleri iyileştirildi.
- Tenant bazlı dosya yükleme ve görüntüleme sorunları çözüldü.
- Widget önizleme sistemi sunucu tarafında tamamen düzeltildi:
    - `$context` değişkeni hataları giderildi.
    - Boş widget içeriği sorunu giderildi.
    - `preview.blade.php` Blade koşulları ve `$renderedHtml` gösterimi düzeltildi.
    - WidgetPreviewController'a detaylı loglama eklendi.
    - Artık tüm widget türleri için sunucu taraflı render edilen içerikler önizlemede doğru bir şekilde görüntülenmektedir.
- Modül tipi portfolyo listeleme widget'ının (`Modules/WidgetManagement/Resources/views/blocks/modules/portfolio/list/view.blade.php`) önizlemesi önemli ölçüde iyileştirildi:
    - Doğru model ve alan adları kullanıldı.
    - Dinamik listeleme widget ayarlarından alınan parametrelere göre filtreleniyor.
    - "Class not found" ve ham HTML/Blade kodu sorunları giderildi.
    - Resim ve kategori gösterimi esnekleştirildi.
    - Portfolyo detay linkleri slug ile oluşturuluyor.

### v0.2.0 (2025-04-05)
- WidgetManagement modülünde resim yükleme ve görüntüleme sorunları giderildi.
- Dosya yükleme işlemleri TenantStorageHelper kullanacak şekilde düzenlendi.
- Tenant bazlı resim URL'leri için doğru görüntüleme desteği eklendi.
- Çoklu resim yükleme desteği iyileştirildi.
- Farklı tenant'lar için doğru dosya yolları ve URL'ler sağlandı.
- Portfolyo widget önizlemesi tamamen iyileştirildi.

### v0.0.1 (2025-04-01)
- Proje kurulumu ve temel yapılandırmalar.
- Gerekli paketlerin entegrasyonu.
