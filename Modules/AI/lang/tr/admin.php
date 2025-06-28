<?php

return [
    // Admin Panel Temel
    'module_management' => 'AI Yönetimi',
    'title' => 'Yapay Zeka',
    'ai_operations' => 'AI İşlemleri',
    'ai_settings' => 'AI Ayarları',
    
    // Admin Konuşma Yönetimi
    'all_conversations' => 'Tüm Konuşmalar',
    'my_conversations' => 'Konuşmalarım',
    'conversation_management' => 'Konuşma Yönetimi',
    'search_placeholder' => 'Ara...',
    'loading' => 'Güncelleniyor...',
    'actions' => 'İşlemler',
    'edit' => 'Düzenle',
    'delete' => 'Sil',
    'view' => 'Görüntüle',
    'last_message' => 'Son Mesaj',
    'creation' => 'Oluşturma',
    'date_format' => 'd.m.Y H:i',
    'token' => 'token',
    
    // Admin Ayarlar
    'basic_settings' => 'Temel Ayarlar',
    'common_features' => 'Ortak Özellikler',
    'usage_limits' => 'Kullanım Limitleri',
    'prompt_templates' => 'Prompt Şablonları',
    'api_key' => 'API Anahtarı',
    'enter_api_key' => 'API anahtarını girin',
    'api_key_info' => 'OpenAI API anahtarınızı girin',
    'test_connection' => 'Bağlantı Testi',
    'model' => 'Model',
    'max_tokens' => 'Maksimum Token',
    'max_tokens_info' => 'Token sınırını belirtin',
    'temperature' => 'Sıcaklık',
    'temperature_info' => 'Yaratıcılık seviyesi (0-1)',
    'active' => 'Aktif',
    'inactive' => 'Pasif',
    'passive' => 'Pasif',
    'inactive_info' => 'Modül durumu',
    'save_settings' => 'Ayarları Kaydet',
    'save_limits' => 'Limitleri Kaydet',
    'save_common_features' => 'Ortak Özellikleri Kaydet',
    
    // Admin Prompt Yönetimi
    'prompt' => 'Prompt',
    'new_prompt' => 'Yeni Prompt',
    'prompt_name' => 'Prompt Adı',
    'prompt_content' => 'Prompt İçeriği',
    'system_prompt_content' => 'Sistem prompt içeriğini girin',
    'default' => 'Varsayılan',
    'system' => 'Sistem',
    'default_prompt' => 'Varsayılan Prompt',
    'default_prompt_info' => 'Bu prompt varsayılan olarak kullanılacak',
    'common_features_prompt' => 'Ortak Özellikler Prompt\'u',
    'common_prompt_info' => 'Ortak özellikler için kullanılacak',
    'enter_common_prompt' => 'Ortak prompt\'u girin',
    'common_features_usage_info' => 'Ortak özellikler kullanım bilgisi',
    'system_protected_info' => 'Sistem korumalı bilgi',
    'cancel' => 'İptal',
    'update' => 'Güncelle',
    'save' => 'Kaydet',
    
    // Admin Limit Ayarları
    'daily_limit' => 'Günlük Limit',
    'daily_limit_info' => 'Günlük kullanım sınırı',
    'monthly_limit' => 'Aylık Limit',
    'monthly_limit_info' => 'Aylık kullanım sınırı',
    
    // Admin Mesajlar
    'success' => [
        'settings_updated' => 'Ayarlar başarıyla güncellendi',
        'prompt_created' => 'Prompt başarıyla oluşturuldu',
        'prompt_updated' => 'Prompt başarıyla güncellendi',
        'prompt_deleted' => 'Prompt başarıyla silindi',
        'conversation_deleted' => 'Konuşma başarıyla silindi',
    ],
    
    'error' => [
        'save_failed' => 'Kayıt başarısız',
        'prompt_not_found' => 'Prompt bulunamadı',
        'conversation_not_found' => 'Konuşma bulunamadı',
        'access_denied' => 'Erişim reddedildi',
    ],
    
    // Admin Onay Mesajları
    'confirm' => [
        'delete_prompt' => 'Prompt\'u Sil',
        'delete_prompt_description' => ':name prompt\'unu silmek istediğinizden emin misiniz?',
        'delete_conversation' => 'Konuşmayı silmek istediğinizden emin misiniz?',
        'reset_settings' => 'Ayarları sıfırlamak istediğinizden emin misiniz?',
    ],
    
    // Admin Uyarılar
    'warning' => [
        'prompt_system_no_edit' => 'Sistem prompt\'u düzenlenemez',
        'prompt_cannot_delete' => 'Bu prompt silinemez',
        'api_key_required' => 'API anahtarı gerekli',
        'connection_failed' => 'Bağlantı başarısız',
    ],
    
    // Admin Bilgi Mesajları
    'info' => [
        'no_prompts' => 'Prompt bulunamadı',
        'no_prompts_description' => 'Henüz prompt eklenmemiş',
        'no_conversations' => 'Konuşma bulunamadı',
        'no_conversations_description' => 'Henüz konuşma başlatılmamış',
        'common_prompt_description' => 'Ortak özellikler açıklaması',
        'what_is_this_prompt' => 'Bu prompt nedir?',
        'common_prompt_features' => 'Ortak prompt özellikleri',
        'common_prompt_features_list' => 'Özellik listesi',
    ],
];