<?php

return [
    // Success Messages
    'language_created' => 'Language created successfully',
    'language_updated' => 'Language updated successfully',
    'language_deleted' => 'Language deleted successfully',
    'language_activated' => 'Language activated successfully',
    'language_deactivated' => 'Language deactivated successfully',
    'language_set_as_default' => 'Language set as default',
    'language_changed' => 'Language changed to :language',
    'translations_updated' => 'Translations updated successfully',
    'cache_cleared' => 'Language cache cleared',
    
    // Error Messages
    'language_not_found' => 'Language not found',
    'language_create_failed' => 'Language could not be created',
    'language_update_failed' => 'Language could not be updated',
    'language_delete_failed' => 'Language could not be deleted',
    'language_activate_failed' => 'Language could not be activated',
    'language_deactivate_failed' => 'Language could not be deactivated',
    'cannot_delete_default' => 'Cannot delete default language',
    'cannot_deactivate_default' => 'Cannot deactivate default language',
    'code_already_exists' => 'This language code is already in use',
    'invalid_language_code' => 'Invalid language code',
    'no_languages_available' => 'No languages available',
    
    // Warning Messages
    'confirm_delete' => 'Are you sure you want to delete this language?',
    'confirm_deactivate' => 'Are you sure you want to deactivate this language?',
    'language_has_content' => 'This language has content',
    'last_active_language' => 'Cannot deactivate the last active language',
    
    // Info Messages
    'no_system_languages' => 'No system languages added yet',
    'no_site_languages' => 'No site languages added yet',
    'select_language_to_edit' => 'Select a language to edit',
    'language_already_active' => 'This language is already active',
    'language_already_inactive' => 'This language is already inactive',
    
    // Validation
    'code_required' => 'Language code is required',
    'code_min' => 'Language code must be at least 2 characters',
    'code_max' => 'Language code cannot exceed 5 characters',
    'name_required' => 'Language name is required',
    'native_name_required' => 'Native language name is required',
    'code_format' => 'Language code can only contain lowercase letters',
    
    // Helper Texts
    'system_language_info' => 'Admin panel languages',
    'site_language_info' => 'Frontend content languages',
    'translation_info' => 'You can manage language files from here',
    'flag_icon_info' => 'You can use emoji for flags',
];