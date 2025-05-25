<h1 align="center">Turkbil Bee - Laravel 11 Multi-Tenancy Projesi</h1>

Bu proje, Laravel 11 kullanılarak geliştirilmiş, modüler ve çok kiracılı (multi-tenancy) bir web uygulamasıdır.

## Temel Teknolojiler ve Paketler

*   **Framework:** Laravel 11
*   **Multi-Tenancy:** Stancl Tenancy ([stancl/tenancy](https://tenancyforlaravel.com/))
*   **Modüler Yapı:** Nwidart Laravel Modules ([nwidart/laravel-modules](https://nwidart.com/laravel-modules/v11/introduction))
*   **Frontend:** Livewire 3.5 ([laravel-livewire/livewire](https://livewire.laravel.com/)), Livewire Volt ([livewire/volt](https://livewire.laravel.com/docs/volt)), Tabler.io ([tabler/tabler](https://tabler.io/))
*   **Kimlik Doğrulama:** Laravel Breeze ([laravel/breeze](https://laravel.com/docs/11.x/starter-kits#laravel-breeze))
*   **Yetkilendirme:** Spatie Laravel Permission ([spatie/laravel-permission](https://spatie.be/docs/laravel-permission/v6/introduction))
*   **Aktivite Loglama:** Spatie Laravel Activity Log ([spatie/laravel-activitylog](https://spatie.be/docs/laravel-activitylog/v4/introduction))
*   **Önbellekleme:** Spatie Laravel Response Cache ([spatie/laravel-responsecache](https://spatie.be/docs/laravel-responsecache/v7/introduction)), Redis (Tenant bazlı)
*   **Medya Yönetimi:** Spatie Laravel Media Library ([spatie/laravel-medialibrary](https://spatie.be/docs/laravel-medialibrary/v11/introduction))
*   **Slug Yönetimi:** Cviebrock Eloquent Sluggable ([cviebrock/eloquent-sluggable](https://github.com/cviebrock/eloquent-sluggable))
*   **Tarih/Zaman:** Nesbot Carbon ([nesbot/carbon](https://carbon.nesbot.com/docs/))

## Versiyon Geçmişi

*   **v0.6.0 (2025-05-24):**
    *   **WidgetManagement Modülü İyileştirmeleri:**
        *   **Hero Widget:** Yapılandırması güncellenerek `has_items` `false` yapıldı, `item_schema` kaldırıldı ve tüm alanlar `settings_schema`'ya taşındı. `content_html` ve seeder içindeki veri oluşturma mantığı bu değişikliğe uyarlandı.
        *   **Kullanıcı Arayüzü (UI):** Widget listeleme (`widget-component.blade.php`) ve kod editörü (`widget-code-editor.blade.php`) sayfalarında, widget'ların `has_items` özelliğine göre "İçerik" ile ilgili buton/linkler dinamik olarak gösterildi/gizlendi. İçerik eklenemeyen widget'lar için "Ayarlar" linki "Özelleştir" olarak güncellendi.
        *   **Güvenlik:** `WidgetFormBuilderComponent` içinde, `has_items` özelliği `false` olan widget'ların item şeması düzenleme sayfasına doğrudan URL ile erişimi engellendi.
        *   **Kod Kalitesi:** `WidgetFormBuilderComponent`'ta layout tanımı, Livewire 3 `#[Layout]` attribute'u kullanılarak güncellendi ve olası bir linter uyarısı giderildi.

*   **v0.5.0 (2025-05-02):**
    *   `studio-widget-loader.js` içinde widget embed overlay özelliği eklendi; görsel overlay olarak `pointer-events: none` ile tıklamalar modele iletildi.
    *   `registerWidgetEmbedComponent` fonksiyonu ile embed component tipi tanımlandı ve editöre kaydedildi.
    *   `studio-editor-setup.js` içindeki `component:remove` handler geliştirildi: `_loadedWidgets` set güncellemesi, iframe ve model DOM temizleme, `col-md-*` wrapper ve `section.container` öğelerinin kaldırılması ve `html-content` input’unun senkronizasyonu.

*   **v0.4.0 (2025-04-05):**
    *   SettingManagement modülünde dosya yükleme bileşeni (file-upload) sorunu çözüldü.
    *   ValuesComponent sınıfına removeImage metodu eklenerek geçici dosyaların silinmesi sağlandı.
    *   Dosya yükleme ve görüntü yükleme bileşenleri arasında tutarlılık sağlandı.
    *   Geçici dosyalar ve kaydedilmiş dosyalar için doğru silme metodları uygulandı.

*   **v0.3.0 (2025-04-05):**
    *   WidgetManagement ve SettingManagement modüllerinde dosya yükleme işlemleri standartlaştırıldı.
    *   Tüm resim ve dosya yüklemeleri için merkezi TenantStorageHelper sınıfı kullanıldı.
    *   Dosya adı formatları ve klasör yapısı standartlaştırıldı.
    *   Çoklu resim yükleme işlemleri iyileştirildi.
    *   Tenant bazlı dosya yükleme ve görüntüleme sorunları çözüldü.

*   **v0.2.0 (2025-04-05):**
    *   WidgetManagement modülünde resim yükleme ve görüntüleme sorunları çözüldü.
    *   Dosya yükleme işlemleri TenantStorageHelper kullanacak şekilde düzenlendi.
    *   Tenant bazlı resim URL'leri için doğru görüntüleme desteği eklendi.
    *   Çoklu resim yükleme desteği iyileştirildi.
    *   Farklı tenant'lar için doğru dosya yolları ve URL'ler sağlandı.

*   **v0.1.0 (2024-07-27):** 
    *   Widget Yönetimi Modülü: Widget sıralama işlevselliği düzeltildi.
    *   Livewire bileşeni (`WidgetSectionComponent`) ve Blade görünümü (`widget-section-component.blade.php`) güncellenerek sürükle-bırak ile widget sıralamasının doğru şekilde kaydedilmesi sağlandı.
    *   JavaScript tarafında gönderilen veri formatı ile PHP tarafındaki metod imzası uyumlu hale getirildi.
    *   Hata ayıklama için loglama mekanizmaları iyileştirildi.

*   **v0.0.1 (YYYY-AA-GG):**
    *   Proje kurulumu ve temel yapılandırmalar.
    *   Gerekli paketlerin entegrasyonu.

*   **Widget Önizleme İyileştirmeleri v0.2 (Portfolyo Listeleme Düzeltmesi)**

Modül tipi portfolyo listeleme widget'ının (`Modules/WidgetManagement/Resources/views/blocks/modules/portfolio/list/view.blade.php`) önizlemesi önemli ölçüde iyileştirildi:

- **Doğru Model ve Alan Adları:** Widget artık doğru portfolyo modellerini (`Portfolio`, `PortfolioCategory`) ve bu modellere ait doğru alan adlarını kullanıyor.
- **Dinamik Listeleme:** Portfolyo öğeleri, widget ayarlarından (`$settings`) alınan parametrelere (örneğin, gösterilecek öğe sayısı, kategori slug'ı) göre dinamik olarak filtrelenip listeleniyor.
- **Hata Giderimi:** Daha önce karşılaşılan "Class not found" ve ham HTML/Blade kodunun görüntülenmesi gibi sorunlar çözüldü.
- **Gelişmiş Resim ve Kategori Gösterimi:** Resimler ve kategori bilgileri, modeldeki doğrudan alanlar ve Spatie Media Library fallback'leri ile daha esnek bir şekilde gösteriliyor.
- **URL Yapısı:** Portfolyo detay sayfalarına yönlendiren linkler, `slug` kullanılarak doğru bir şekilde oluşturuluyor.

Bu değişiklikler, portfolyo listeleme widget'ının önizlemesinin daha doğru, stabil ve kullanıcı dostu olmasını sağlamıştır.

## Sunucu Taraflı Widget Önizleme Düzeltmeleri v0.3

Widget önizleme sistemi, sunucu tarafında render edilen içeriğin doğru bir şekilde görüntülenmesini engelleyen bir dizi sorunu gidermek üzere önemli ölçüde iyileştirildi:

- **`$context` Değişkeni Hataları Çözüldü:** Hem JavaScript kaynaklı sanılan hem de PHP `@include` direktiflerinde ortaya çıkan "Undefined variable $context" hataları giderildi. İstemci taraflı Handlebars render mantığı tamamen kaldırıldı ve PHP tarafında `null coalescing operatörü (??)` kullanılarak güvenlik artırıldı.
- **Boş Widget İçeriği Sorunu Giderildi:** En kritik sorun olan, işlenmiş widget HTML'inin (`$renderedHtml`) önizlemede görünmemesi problemi çözüldü. `preview.blade.php` dosyasındaki Blade koşulları, `$renderedHtml`'in boş olup olmadığını doğru bir şekilde kontrol edecek şekilde güncellendi. Ayrıca, dolu olan `$renderedHtml` içeriğinin `<div id="widget-container">{!! $renderedHtml !!}</div>` ile doğru bir şekilde ekrana basılması sağlandı.
- **Hata Ayıklama İyileştirmeleri:** `WidgetPreviewController`'a eklenen detaylı loglama sayesinde, veri akışı ve HTML üretimindeki sorunlar daha etkin bir şekilde tespit edilebildi.

Bu değişiklikler, widget önizleme sisteminin daha stabil, hatasız ve beklendiği gibi çalışmasını sağlamıştır. Artık tüm widget türleri için sunucu taraflı render edilen içerikler önizlemede doğru bir şekilde görüntülenmektedir.
