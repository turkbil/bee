# Turkbil Bee - Laravel 11 Multi-Tenancy Projesi

Bu proje, Laravel 11 ile geliştirilmiş, modüler ve çok kiracılı (multi-tenancy) bir web uygulamasıdır.

## Temel Teknolojiler ve Kullanılan Paketler

- **Framework:** Laravel 11
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

### v0.7.0 (2025-06-05) - Widget Rendering Düzeltmesi ve Log Temizliği
- **Widget Rendering Düzeltmesi:** Ana sayfadaki widget'larda ve diğer widget içeren sayfalarda oluşan fazladan kapanış `</div>` etiketi sorunu giderildi. Bu sorun, `ShortcodeParser` içerisindeki `HTML_MODULE_WIDGET_PATTERN` adlı regex deseninin widget yer tutucularını eksik eşleştirmesinden kaynaklanıyordu. Desen, widget'ın tüm dış `div` yapısını kapsayacak şekilde güncellenerek sorun çözüldü.
- **Log Temizliği:** Hata ayıklama sürecinde `ShortcodeParser.php` ve `WidgetServiceProvider.php` dosyalarına eklenen tüm geçici `Log::debug`, `Log::error` ve `Log::warning` çağrıları kaldırıldı. Bu sayede kod tabanı daha temiz ve stabil hale getirildi.

### v0.3 (2025-05-25)
- Portfolio ve Page modülü widget'larında limit değeri sıfır veya geçersiz geldiğinde varsayılan olarak 5 atanacak şekilde kodlar güncellendi.
- Artık tüm widget'larda "öğe bulunamadı" hatası alınmaz, örnek veri varsa otomatik listelenir.
- Kod okunabilirliği ve güvenliği artırıldı.
- Debug logları ile widget veri akışı kolayca izlenebilir hale getirildi.


### v0.6.0 (2025-05-24)
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

### v0.0.1 (YYYY-AA-GG)
- Proje kurulumu ve temel yapılandırmalar.
- Gerekli paketlerin entegrasyonu.
