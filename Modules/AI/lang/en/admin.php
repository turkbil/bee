<?php

return [
    // Admin Panel Basic
    'module_management' => 'AI Management',
    'title' => 'Artificial Intelligence',
    'ai_operations' => 'AI Operations',
    'ai_settings' => 'AI Settings',
    
    // Admin Conversation Management
    'all_conversations' => 'All Conversations',
    'my_conversations' => 'My Conversations',
    'conversation_management' => 'Conversation Management',
    'search_placeholder' => 'Search...',
    'loading' => 'Updating...',
    'actions' => 'Actions',
    'edit' => 'Edit',
    'delete' => 'Delete',
    'view' => 'View',
    'last_message' => 'Last Message',
    'creation' => 'Creation',
    'date_format' => 'd.m.Y H:i',
    'token' => 'token',
    
    // Admin Settings
    'basic_settings' => 'Basic Settings',
    'common_features' => 'Common Features',
    'usage_limits' => 'Usage Limits',
    'prompt_templates' => 'Prompt Templates',
    'api_key' => 'API Key',
    'enter_api_key' => 'Enter API key',
    'api_key_info' => 'Enter your OpenAI API key',
    'test_connection' => 'Test Connection',
    'model' => 'Model',
    'max_tokens' => 'Maximum Tokens',
    'max_tokens_info' => 'Specify token limit',
    'temperature' => 'Temperature',
    'temperature_info' => 'Creativity level (0-1)',
    'active' => 'Active',
    'inactive' => 'Inactive',
    'passive' => 'Passive',
    'inactive_info' => 'Module status',
    'save_settings' => 'Save Settings',
    'save_limits' => 'Save Limits',
    'save_common_features' => 'Save Common Features',
    
    // Admin Prompt Management
    'prompt' => 'Prompt',
    'new_prompt' => 'New Prompt',
    'prompt_name' => 'Prompt Name',
    'prompt_content' => 'Prompt Content',
    'system_prompt_content' => 'Enter system prompt content',
    'default' => 'Default',
    'system' => 'System',
    'default_prompt' => 'Default Prompt',
    'default_prompt_info' => 'This prompt will be used as default',
    'common_features_prompt' => 'Common Features Prompt',
    'common_prompt_info' => 'Will be used for common features',
    'enter_common_prompt' => 'Enter common prompt',
    'common_features_usage_info' => 'Common features usage information',
    'system_protected_info' => 'System protected information',
    'cancel' => 'Cancel',
    'update' => 'Update',
    'save' => 'Save',
    
    // Admin Limit Settings
    'daily_limit' => 'Daily Limit',
    'daily_limit_info' => 'Daily usage limit',
    'monthly_limit' => 'Monthly Limit',
    'monthly_limit_info' => 'Monthly usage limit',
    
    // Admin Messages
    'success' => [
        'settings_updated' => 'Settings updated successfully',
        'prompt_created' => 'Prompt created successfully',
        'prompt_updated' => 'Prompt updated successfully',
        'prompt_deleted' => 'Prompt deleted successfully',
        'conversation_deleted' => 'Conversation deleted successfully',
    ],
    
    'error' => [
        'save_failed' => 'Save failed',
        'prompt_not_found' => 'Prompt not found',
        'conversation_not_found' => 'Conversation not found',
        'access_denied' => 'Access denied',
    ],
    
    // Admin Confirmation Messages
    'confirm' => [
        'delete_prompt' => 'Delete Prompt',
        'delete_prompt_description' => 'Are you sure you want to delete :name prompt?',
        'delete_conversation' => 'Are you sure you want to delete this conversation?',
        'reset_settings' => 'Are you sure you want to reset settings?',
    ],
    
    // Admin Warnings
    'warning' => [
        'prompt_system_no_edit' => 'System prompt cannot be edited',
        'prompt_cannot_delete' => 'This prompt cannot be deleted',
        'api_key_required' => 'API key is required',
        'connection_failed' => 'Connection failed',
    ],
    
    // Admin Info Messages
    'info' => [
        'no_prompts' => 'No prompts found',
        'no_prompts_description' => 'No prompts have been added yet',
        'no_conversations' => 'No conversations found',
        'no_conversations_description' => 'No conversations have been started yet',
        'common_prompt_description' => 'Common features description',
        'what_is_this_prompt' => 'What is this prompt?',
        'common_prompt_features' => 'Common prompt features',
        'common_prompt_features_list' => 'Features list',
    ],
];