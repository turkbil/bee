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

*   **v0.1.0 (2024-07-27):** 
    *   Widget Yönetimi Modülü: Widget sıralama işlevselliği düzeltildi.
    *   Livewire bileşeni (`WidgetSectionComponent`) ve Blade görünümü (`widget-section-component.blade.php`) güncellenerek sürükle-bırak ile widget sıralamasının doğru şekilde kaydedilmesi sağlandı.
    *   JavaScript tarafında gönderilen veri formatı ile PHP tarafındaki metod imzası uyumlu hale getirildi.
    *   Hata ayıklama için loglama mekanizmaları iyileştirildi.

*   **v0.0.1 (YYYY-AA-GG):**
    *   Proje kurulumu ve temel yapılandırmalar.
    *   Gerekli paketlerin entegrasyonu.
