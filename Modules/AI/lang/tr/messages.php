<?php

return [
    // Başarı mesajları
    'success' => [
        'settings_updated' => 'AI ayarları güncellendi',
        'common_features_updated' => 'Ortak özellikler güncellendi',
        'limits_updated' => 'Kullanım limitleri güncellendi',
        'conversation_deleted' => 'Konuşma başarıyla silindi.',
        'conversation_reset' => 'Konuşma sıfırlandı.',
        'conversation_copied' => 'Tüm konuşma panoya kopyalandı.',
        'message_copied' => 'Mesaj panoya kopyalandı.',
        'prompt_created' => 'Yeni prompt eklendi.',
        'prompt_updated' => 'Prompt başarıyla güncellendi.',
        'prompt_deleted' => 'Prompt silindi',
        'new_conversation_started' => 'Yeni konuşma başlatıldı',
        'operation_completed' => 'İşlem başarıyla tamamlandı.',
        'api_connection_successful' => 'API bağlantısı başarılı!',
        'prompt_status_updated' => 'Prompt durumu :status olarak güncellendi',
        'prompt_set_as_default' => '":name" varsayılan prompt olarak ayarlandı',
    ],
    
    // Hata mesajları
    'error' => [
        'settings_save_failed' => 'Ayarlar güncellenirken bir hata oluştu.',
        'common_features_save_failed' => 'Ortak özellikler kaydedilirken bir sorun oluştu: :error',
        'limits_save_failed' => 'Limitler kaydedilirken bir sorun oluştu: :error',
        'prompt_not_found' => 'Düzenlenecek prompt bulunamadı.',
        'prompt_save_failed' => 'İşlem sırasında bir hata oluştu: :error',
        'prompt_delete_failed' => 'Silme işlemi sırasında bir hata oluştu',
        'prompt_edit_failed' => 'Prompt bilgileri yüklenirken bir sorun oluştu',
        'response_failed' => 'Yanıt alınamadı. Lütfen daha sonra tekrar deneyin veya yöneticinize başvurun.',
        'message_send_failed' => 'Mesaj gönderilirken bir hata oluştu: :error',
        'conversation_not_found' => 'Konuşma bulunamadı veya erişim izniniz yok.',
        'prompt_not_found_simple' => 'Seçilen prompt bulunamadı.',
        'prompt_not_active' => 'Seçilen prompt aktif değil.',
        'conversation_prompt_update_failed' => 'Konuşma promptu güncellenirken bir hata oluştu.',
        'api_connection_failed' => 'API bağlantısı başarısız. Lütfen API anahtarını kontrol edin.',
        'connection_test_failed' => 'Bağlantı testi sırasında hata oluştu: :error',
        'empty_message' => 'Lütfen bir mesaj yazın.',
        'api_key_empty' => 'API anahtarı boş olamaz!',
        'connection_error' => 'Bağlantı hatası oluştu.',
        'server_error' => 'Sunucu yanıtı başarısız: :status',
        'ai_response_failed' => 'AI yanıtı alınamadı. Lütfen tekrar deneyin.',
        'ai_response_error' => 'Yanıt alınırken bir hata oluştu: :error',
        'conversation_access_denied' => 'Konuşma bulunamadı veya erişim izniniz yok.',
        'general_error' => 'Bir hata oluştu.',
    ],
    
    // Uyarı mesajları
    'warning' => [
        'prompt_system_no_edit' => 'Sistem promptları düzenlenemez',
        'prompt_system_no_delete' => 'Sistem promptları silinemez',
        'prompt_default_no_delete' => 'Varsayılan prompt silinemez',
        'prompt_common_no_delete' => 'Ortak özellikler promptu silinemez',
        'prompt_system_no_status_change' => 'Sistem promptlarının durumu değiştirilemez',
        'prompt_cannot_delete' => 'Bu prompt silinemez',
    ],
    
    // Bilgi mesajları
    'info' => [
        'greeting' => 'Merhaba! Size nasıl yardımcı olabilirim?',
        'no_conversations' => 'Henüz konuşma yok',
        'no_conversations_description' => 'AI asistanı kullanarak yeni bir konuşma başlatabilirsiniz.',
        'no_prompts' => 'Henüz prompt şablonu yok',
        'no_prompts_description' => 'Yeni prompt şablonları eklemek için "Yeni Prompt" butonunu kullanabilirsiniz.',
        'what_is_this_prompt' => 'Bu prompt nedir?',
        'common_prompt_description' => 'Bu prompt, AI asistanın kimliğini, kişiliğini ve davranışlarını tanımlar. Her konuşmada, konuşmaya özel prompttan önce eklenerek AI\'ın tutarlı bir kişiliğe sahip olmasını sağlar.',
        'common_prompt_features' => 'Bu bölümde şunları tanımlayabilirsiniz:',
        'common_prompt_features_list' => [
            'AI asistanın adı',
            'Şirket veya kuruluş bilgileri',
            'Yanıt verme tarzı ve tonu',
            'Uzmanlık alanları',
            'Diğer kişilik özellikleri'
        ],
    ],
    
    // Onay mesajları
    'confirm' => [
        'delete_conversation' => 'Bu konuşmayı silmek istediğinizden emin misiniz?',
        'delete_prompt' => 'Promptu silmek istediğinize emin misiniz?',
        'delete_prompt_description' => '":name" adlı prompt silinecek ve bu işlem geri alınamaz.',
        'reset_conversation' => 'Konuşma geçmişi sıfırlanacak. Emin misiniz?',
        'conversation_id_and_prompt_required' => 'Konuşma ID ve Prompt ID zorunludur.',
    ],
    
    // Durum mesajları
    'status' => [
        'successful' => 'Başarılı',
        'failed' => 'Başarısız',
        'copied' => 'Kopyalandı',
        'completed' => 'Tamamlandı',
        'active' => 'aktif',
        'passive' => 'pasif',
    ],
    
    // Diğer genel mesajlar
    'general' => [
        'no_data' => '-',
        'loading' => 'Yükleniyor...',
        'processing' => 'İşleniyor...',
        'saving' => 'Kaydediliyor...',
        'deleting' => 'Siliniyor...',
        'updating' => 'Güncelleniyor...',
        'you' => 'Siz',
        'ai' => 'AI',
        'conversation_updated' => 'Konuşma promptu güncellendi.',
    ],
];