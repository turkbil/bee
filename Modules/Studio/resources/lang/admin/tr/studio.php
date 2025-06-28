<?php

return [
    // Genel başlıklar
    'title' => 'Studio',
    'editor' => 'Studio Editör',
    'widget_manager' => 'Widget Yöneticisi',
    'visual_editor' => 'Görsel Editör',
    'studio_home' => 'Studio Ana Sayfa',
    'page_editor' => 'Sayfa Düzenleyici',
    'portfolio_editor' => 'Portfolio Düzenleyici',
    
    // Aksiyonlar
    'actions' => [
        'save' => 'Kaydet',
        'preview' => 'Önizleme',
        'export' => 'Dışa Aktar',
        'cancel' => 'İptal',
        'delete' => 'Sil',
        'edit' => 'Düzenle',
        'add' => 'Ekle',
        'close' => 'Kapat',
        'back' => 'Geri',
        'clear_content' => 'İçeriği temizle',
        'undo' => 'Geri al',
        'redo' => 'Yinele',
        'show_hide_borders' => 'Bileşen sınırlarını göster/gizle',
        'edit_html' => 'HTML Düzenle',
        'edit_css' => 'CSS Düzenle',
        'view_all' => 'Tümünü Görüntüle',
        'create_first' => 'İlk Sayfayı Oluştur',
    ],
    
    // Mesajlar
    'messages' => [
        'save_success' => 'İçerik başarıyla kaydedildi.',
        'save_error' => 'İçerik kaydedilirken bir hata oluştu.',
        'save_general_error' => 'Kaydetme sırasında hata oluştu',
        'delete_confirm' => 'Bu öğeyi silmek istediğinize emin misiniz?',
        'clear_confirm' => 'İçeriği temizlemek istediğinize emin misiniz? Bu işlem geri alınamaz.',
        'loading_error' => 'İçerik yüklenirken hata oluştu',
        'widget_loading_error' => 'Widget yüklenirken hata',
        'file_upload_error' => 'Dosya yükleme hatası',
        'no_valid_file' => 'Dosyalar yüklenemedi. Geçerli dosya bulunamadı.',
        'select_file' => 'Lütfen bir dosya seçin.',
        'content_could_not_saved' => 'İçerik kaydedilemedi.',
        'blocks_could_not_loaded' => 'Blok verileri alınamadı',
        'resources_copied' => 'Kaynaklar başarıyla kopyalandı',
        'resources_copy_error' => 'Kaynaklar kopyalanırken hata oluştu',
        'view_not_found' => 'Görünüm bulunamadı',
        'widget_content_loading' => 'Widget içeriği yükleniyor...',
        'no_pages_yet' => 'Henüz sayfa yok',
        'first_page_hint' => 'İlk sayfayı oluşturmak için aşağıdaki butona tıklayın',
    ],
    
    // Bloklar ve Bileşenler
    'blocks' => [
        'layout' => 'Düzen',
        'content' => 'İçerik',
        'form' => 'Form',
        'media' => 'Medya',
        'widget' => 'Widgetlar',
        'components' => 'Bileşenler',
        'active_components' => 'Aktif Bileşenler',
        'search_component' => 'Bileşen ara...',
        'search_layer' => 'Katman ara...',
    ],
    
    // Sekmeler
    'tabs' => [
        'blocks' => 'Bileşenler',
        'styles' => 'Stiller',
        'layers' => 'Katmanlar',
        'configure' => 'Yapılandır',
        'design' => 'Tasarla',
    ],
    
    // Cihazlar
    'devices' => [
        'desktop' => 'Masaüstü',
        'tablet' => 'Tablet',
        'mobile' => 'Mobil',
    ],
    
    // İstatistikler
    'stats' => [
        'total_pages' => 'Toplam Sayfa',
        'active_component' => 'Aktif Bileşen',
        'unlimited_editing' => 'Sınırsız Düzenleme',
        'responsive' => 'Responsive',
    ],
    
    // Sayfa İşlemleri
    'page' => [
        'operations' => 'Sayfa İşlemleri',
        'all_pages' => 'Tüm Sayfalar',
        'new_page' => 'Yeni Sayfa',
        'add_new_page' => 'Yeni Sayfa Ekle',
        'recent_edited' => 'Son Düzenlenen Sayfalar',
        'edit_with_studio' => 'Studio ile Düzenle',
    ],
    
    // Widget İşlemleri
    'widget' => [
        'management' => 'Widget Yönetimi',
        'placeholder' => 'Widget',
        'loading' => 'Widget yükleniyor...',
        'load_error' => 'Widget yükleme hatası',
    ],
    
    // Hızlı Başlangıç
    'quick_start' => [
        'title' => 'Hızlı Başlangıç',
        'new_page' => 'Yeni Sayfa Oluştur',
        'all_pages' => 'Tüm Sayfalar',
        'widget_management' => 'Widget Yönetimi',
    ],
    
    // Nasıl Kullanılır
    'how_to_use' => [
        'title' => 'Nasıl Kullanılır',
        'step1_title' => 'Sayfa Seç',
        'step1_desc' => 'Düzenlemek istediğiniz sayfayı seçin',
        'step2_title' => 'Studio Aç',
        'step2_desc' => '"Studio ile Düzenle" butonuna tıklayın',
        'step3_title' => 'Tasarımla',
        'step3_desc' => 'Bileşenleri sürükleyip bırakın',
        'step4_title' => 'Kaydet',
        'step4_desc' => 'Değişikliklerinizi kaydedin',
    ],
    
    // Log mesajları
    'logs' => [
        'content_saved' => 'studio ile düzenlendi',
        'save_request' => 'Studio Save - Request Details',
        'prepared_values' => 'Studio Save - Prepared Values',
        'save_error' => 'Studio içerik kaydederken hata',
        'page_load_error' => 'Sayfa yüklenirken hata',
        'portfolio_load_error' => 'Portfolio yüklenirken hata',
        'file_upload_error' => 'Dosya yükleme hatası',
        'blocks_loading' => 'BlockService::getAllBlocks - Bloklar yükleniyor',
        'widget_module_not_found' => 'WidgetManagement modülü bulunamadı',
        'total_blocks_loaded' => 'BlockService - Toplam :count adet blok yüklendi',
        'block_load_error' => 'BlockService - Blok yükleme hatası',
        'tenant_widget_load_error' => 'Tenant widget yükleme hatası',
        'block_data_error' => 'Blok verileri alınırken hata',
        'resource_copy_error' => 'Kaynakları kopyalama hatası',
        'widget_loading_started' => 'Widget :id için yükleme başlatılıyor...',
        'container_not_found' => 'Container bulunamadı, alternatif arama yapılıyor...',
        'widget_container_not_found' => 'Widget container bulunamadı',
        'global_loader_used' => 'Global yükleyici kullanılıyor',
        'direct_fetch_loading' => 'Doğrudan fetch ile yükleme yapılıyor',
        'widget_loaded_successfully' => 'Widget :id başarıyla yüklendi',
        'widget_retry_loading' => 'Widget :id için yükleme tekrar deneniyor...',
    ],
    
    // Hata mesajları
    'errors' => [
        'general' => 'Hata',
        'load' => 'Yükleme hatası',
        'save' => 'Kaydetme hatası',
        'upload' => 'Yükleme hatası',
    ],
    
    // Widget kategorileri
    'categories' => [
        'modules' => 'Modüller',
        'page' => 'Sayfa',
        'content' => 'İçerik',
    ],
];