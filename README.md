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
