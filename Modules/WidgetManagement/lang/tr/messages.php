<?php

return [
    // Başarı mesajları
    'widget_created' => 'Yeni bileşen oluşturuldu.',
    'widget_updated' => 'Bileşen güncellendi.',
    'widget_deleted' => ':name silindi.',
    'widget_activated' => 'Bileşen aktifleştirildi.',
    'widget_deactivated' => 'Bileşen devre dışı bırakıldı.',
    'widget_settings_saved' => 'Widget ayarları kaydedildi.',
    'widget_form_structure_saved' => 'Widget form yapısı kaydedildi.',

    'item_created' => 'Yeni içerik eklendi.',
    'item_updated' => 'İçerik güncellendi.',
    'item_deleted' => 'Öğe başarıyla silindi.',
    'item_activated' => 'İçerik durumu aktif olarak güncellendi.',
    'item_deactivated' => 'İçerik durumu pasif olarak güncellendi.',
    'items_reordered' => 'Öğeler başarıyla sıralandı.',

    'category_created' => 'Kategori başarıyla eklendi.',
    'category_updated' => 'Kategori başarıyla güncellendi.',
    'category_deleted' => 'Kategori başarıyla silindi.',
    'category_activated' => 'Kategori aktif edildi.',
    'category_deactivated' => 'Kategori pasif edildi.',
    'category_order_updated' => 'Kategori sıralaması güncellendi.',
    'category_moved' => 'Kategori :parent altına taşındı.',
    'category_moved_to_root' => 'Kategori ana kategori olarak taşındı.',

    // Hata mesajları
    'widget_not_found' => 'Widget bulunamadı veya aktif değil.',
    'widget_file_not_found' => 'Belirtilen dosya bulunamadı: :path',
    'widget_module_file_not_found' => 'Belirtilen modül dosyası bulunamadı: :path',
    'widget_view_not_found' => 'Belirtilen view dosyası bulunamadı: :path',
    'widget_template_load_error' => 'Widget şablonu yüklenirken bir hata oluştu: :error',
    'widget_instance_load_error' => 'Widget instance yüklenirken bir hata oluştu: :error',
    'widget_render_error' => 'Widget render hatası: :error',
    'widget_view_render_error' => 'View render hatası: :error',
    'widget_module_render_error' => 'Modül render hatası: :error',
    'widget_content_empty' => 'Widget içeriği boş',
    'widget_html_empty' => 'Rendered HTML is empty or whitespace.',

    'widget_name_required' => 'Bileşen adı boş olamaz.',
    'widget_data_missing' => 'Eksik parametreler: widgetId veya formData',
    'widget_json_invalid' => 'Geçersiz JSON formatı',
    'widget_form_data_empty' => 'Form verisi boş olamaz.',

    'item_save_error' => 'İçerik kaydedilirken bir hata oluştu: :error',
    'item_delete_error' => 'Öğe silinirken bir hata oluştu: :error',
    'item_reorder_error' => 'Öğeler sıralanırken bir hata oluştu: :error',
    'item_cannot_delete_static' => 'Statik bileşenin tek içerik öğesi silinemez.',

    'category_has_widgets' => 'Bu kategoriye bağlı widget\'lar var. Önce bunları silmelisiniz veya başka kategoriye taşımalısınız.',
    'category_has_children' => 'Bu kategorinin alt kategorileri var. Önce alt kategorileri silmelisiniz.',
    'category_add_error' => 'Kategori eklenirken bir hata oluştu.',
    'category_update_error' => 'Kategori güncellenirken bir hata oluştu.',
    'category_delete_error' => 'Kategori silinirken bir hata oluştu.',
    'category_toggle_error' => 'Kategori durumu değiştirilirken bir hata oluştu.',
    'category_order_error' => 'Kategori sıralaması güncellenirken bir hata oluştu.',

    // Dosya yükleme mesajları
    'file_uploaded' => 'Dosya başarıyla yüklendi.',
    'file_upload_error' => 'Dosya yüklenirken bir hata oluştu.',
    'file_deleted' => 'Dosya başarıyla silindi.',
    'file_not_found' => 'Dosya bulunamadı.',
    'image_uploaded' => 'Resim başarıyla yüklendi.',
    'image_upload_error' => 'Resim yüklenirken bir hata oluştu.',
    'multiple_images_uploaded' => 'Resimler başarıyla yüklendi.',

    // Validasyon mesajları
    'title_required' => 'Başlık zorunludur.',
    'title_min' => 'Başlık en az :min karakter olmalıdır.',
    'title_max' => 'Başlık en fazla :max karakter olmalıdır.',
    'category_title_required' => 'Kategori başlığı zorunludur.',
    'category_title_min' => 'Kategori başlığı en az :min karakter olmalıdır.',
    'category_title_max' => 'Kategori başlığı en fazla :max karakter olmalıdır.',
    'slug_regex' => 'Slug sadece harfler, rakamlar, tire ve alt çizgi içerebilir.',
    'form_fields_check' => 'Lütfen form alanlarını kontrol ediniz.',

    // Uyarı mesajları
    'widget_no_permission' => 'Bu widget türü için içerik şeması düzenlenemez. Sadece ayarlarını düzenleyebilirsiniz.',
    'widget_not_module_type' => 'Bu widget bir module tipi değil.',
    'widget_file_path_missing' => 'Bu dosya widget\'ı için dosya yolu tanımlanmamış.',
    'widget_module_file_path_missing' => 'Bu modül widget\'ı için dosya yolu tanımlanmamış.',
    'widget_module_no_html' => 'Bu modül bileşeni için HTML şablonu tanımlanmamış. Lütfen widget\'ı düzenleyin ve bir HTML şablonu ekleyin.',
    
    // Bilgi mesajları
    'widget_loading' => 'Widget form yükleniyor...',
    'canvas_loading' => 'Widget form yükleniyor...',
    'content_loading' => 'İçerik yüklenirken bir hata oluştu: :error',
    'no_element_selected' => 'Element Seçilmedi',
    'select_element_to_edit' => 'Özelliklerini düzenlemek için bir form elementi seçin.',
    'widget_form_building_start' => 'Widget Form Oluşturmaya Başlayın',
    'drag_elements_here' => 'Sol taraftaki elemanları sürükleyip buraya bırakın.',

    // Empty state mesajları
    'no_components_found' => 'Hiç bileşen bulunamadı',
    'no_components_add_new' => 'Yeni bir bileşen eklemek için "Bileşen Galerisi" sayfasına geçebilirsiniz',
    'no_content_found' => 'Henüz içerik bulunmuyor',
    'no_content_add_new' => '"Yeni İçerik Ekle" butonunu kullanarak bileşen içeriklerinizi oluşturun.',
    'no_categories_found' => 'Kategori bulunamadı',
    'no_categories_search' => 'Arama kriterinize uygun kategori bulunamadı.',
    'no_categories_add_new' => 'Henüz kategori eklenmemiş. Sol taraftaki formu kullanarak yeni bir kategori ekleyebilirsiniz.',
    'clear_search' => 'Aramayı Temizle',

    // Form builder mesajları
    'form_structure_saved' => 'Form yapısı kaydedildi',
    'form_structure_save_error' => 'Form yapısı kaydedilirken bir hata oluştu',
    'widget_settings_structure' => ':name Ayarları',
    'widget_content_structure' => ':name İçerik Yapısı',
    'schema_data_not_found' => 'Schema data not found',

    // Önizleme mesajları
    'preview_info' => 'Önizleme Bilgileri:',
    'widget_type' => 'Tür:',
    'widget_description' => 'Açıklama:',
    'description_not_available' => 'Açıklama bulunmuyor',
    'widget_content_empty_preview' => 'Widget İçeriği Boş',
    'widget_no_processed_html' => 'Bu widget için işlenmiş HTML içeriği bulunamadı.',

    // Navigasyon mesajları
    'back_to_list' => 'Listeye Dön',
    'back_to_components' => 'Geri Dön',
    'go_to_gallery' => 'Bileşen Galerisine Git',
    'go_to_management' => 'Yönetime Git',

    // Menü başlıkları
    'component_management' => 'Bileşen Yönetimi',
    'active_components' => 'Aktif Bileşenler',
    'component_gallery' => 'Bileşen Galerisi',
    'component_menu' => 'Bileşen Menüsü',
    'special_components' => 'Özel Bileşenler',
    'ready_files' => 'Hazır Dosyalar',
    'component_configuration' => 'Bileşen Yapılandırması',
    'category_management' => 'Kategori Yönetimi',
    'add_component' => 'Bileşen Ekle',
    'content_management' => 'İçerik Yönetimi',

    // Sayfa başlıkları ve açıklamalar
    'active_components_desc' => 'Kullanmakta olduğunuz bileşenleri yönetin',
    'widget_content_management_desc' => 'Widget içeriklerini buradan yönetebilirsiniz.',
    'widget_form_editing' => 'Widget Form Düzenleme',

    // Button metinleri
    'add_new_content' => 'Yeni İçerik Ekle',
    'add_content' => 'İçerik Ekle',
    'edit_content' => 'İçerik Düzenle',
    'save_and_continue' => 'Kaydet ve Devam Et',
    'save_and_add_new' => 'Kaydet ve Yeni Ekle',
    'reset_to_default' => 'Varsayılana Döndür',

    // Modal ve dialog mesajları
    'confirm_delete_component' => 'Bu bileşeni silmek istediğinize emin misiniz?',
    'confirm_delete_content' => 'Bu içeriği silmek istediğinize emin misiniz?',
    'confirm_delete_category' => 'Bu kategoriyi silmek istediğinize emin misiniz?',

    // Placeholder metinleri
    'search_components' => 'Bileşen ara...',
    'search_categories' => 'Aramak için yazmaya başlayın...',
    'enter_widget_name' => 'Widget adını giriniz',
    'enter_content_title' => 'İçerik başlığını giriniz',
    'enter_category_title' => 'Kategori başlığı',
    'enter_category_slug' => 'kategori-slug',
    'enter_category_description' => 'Kategori açıklaması',
    'select_parent_category' => 'Üst Kategori Seçin',
    'add_as_main_category' => 'Ana Kategori Olarak Ekle',

    // Help text ve açıklamalar
    'slug_auto_generate' => 'Boş bırakırsanız otomatik oluşturulur',
    'fontawesome_icon_code' => 'FontAwesome ikon kodu (örn: fa-folder)',
    'drag_image_or_click' => 'Resmi sürükleyip bırakın veya tıklayın',
    'drop_file_here' => 'Bırakın!',

    // Durum metinleri
    'widget_empty_content' => 'Widget içeriği boş',
    'widget_not_displayed' => 'Widget görüntülenemiyor',
    'check_widget_configuration' => 'Lütfen widget yapılandırmasını kontrol edin.',

    // Kategori ile ilgili özel mesajlar
    'category_edit' => 'Kategori Düzenle',
    'add_new_category' => 'Yeni Kategori Ekle',
    'main_category' => 'Ana Kategori',
    'sub_categories' => 'Alt Kategoriler',
    'show_all_items' => 'Tümünü Göster',

    // Widget Studio özel mesajları
    'widget_studio_title' => ':name Widget Studio',
    'settings_schema' => 'Ayarlar',
    'content_schema' => 'İçerik Yapısı',

    // Dosya ve medya mesajları
    'current_image' => 'Mevcut Fotoğraf',
    'uploaded_image' => 'Yüklenen Fotoğraf',
    'no_image' => 'Resim yok',
    'multiple_images_info' => '+:count resim',

    // Dropdown ve liste metinleri
    'items_per_page' => ':count Bileşen',
    'default_tab' => 'Varsayılan Sekme',
    'tab_title' => 'Sekme :number',

    // Widget özel tipleri
    'static_widget' => 'Statik Bileşen',
    'dynamic_widget' => 'Dinamik Bileşen',
    'content_widget' => 'İçerik Bileşeni',
    'file_widget' => 'Dosya Bileşeni',
    'module_widget' => 'Modül Bileşeni',

    // Sistem alanları
    'widget_title' => 'Widget Başlığı',
    'item_title' => 'Başlık',
    'item_status' => 'Durum',
    'category_not_assigned' => 'Kategori Atanmamış',
    'no_category' => 'Kategori Yok',

    // JSON ve veri mesajları
    'json_response_success' => 'İşlem başarıyla tamamlandı',
    'json_response_error' => 'İşlem sırasında bir hata oluştu',
];