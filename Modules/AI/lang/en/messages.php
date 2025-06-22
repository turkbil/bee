<?php

return [
    // Success messages
    'success' => [
        'settings_updated' => 'AI settings updated',
        'common_features_updated' => 'Common features updated',
        'limits_updated' => 'Usage limits updated',
        'conversation_deleted' => 'Conversation successfully deleted.',
        'conversation_reset' => 'Conversation reset.',
        'conversation_copied' => 'Entire conversation copied to clipboard.',
        'message_copied' => 'Message copied to clipboard.',
        'prompt_created' => 'New prompt added.',
        'prompt_updated' => 'Prompt successfully updated.',
        'prompt_deleted' => 'Prompt deleted',
        'new_conversation_started' => 'New conversation started',
        'operation_completed' => 'Operation completed successfully.',
        'api_connection_successful' => 'API connection successful!',
        'prompt_status_updated' => 'Prompt status updated to :status',
        'prompt_set_as_default' => '":name" set as default prompt',
    ],
    
    // Error messages
    'error' => [
        'settings_save_failed' => 'An error occurred while updating settings.',
        'common_features_save_failed' => 'A problem occurred while saving common features: :error',
        'limits_save_failed' => 'A problem occurred while saving limits: :error',
        'prompt_not_found' => 'Prompt to edit not found.',
        'prompt_save_failed' => 'An error occurred during the operation: :error',
        'prompt_delete_failed' => 'An error occurred during the deletion process',
        'prompt_edit_failed' => 'A problem occurred while loading prompt information',
        'response_failed' => 'Could not get response. Please try again later or contact your administrator.',
        'message_send_failed' => 'An error occurred while sending message: :error',
        'conversation_not_found' => 'Conversation not found or you do not have access permission.',
        'prompt_not_found_simple' => 'Selected prompt not found.',
        'prompt_not_active' => 'Selected prompt is not active.',
        'conversation_prompt_update_failed' => 'An error occurred while updating conversation prompt.',
        'api_connection_failed' => 'API connection failed. Please check your API key.',
        'connection_test_failed' => 'Error occurred during connection test: :error',
        'empty_message' => 'Please write a message.',
        'api_key_empty' => 'API key cannot be empty!',
        'connection_error' => 'Connection error occurred.',
        'server_error' => 'Server response failed: :status',
        'ai_response_failed' => 'Could not get AI response. Please try again.',
        'ai_response_error' => 'An error occurred while getting response: :error',
        'conversation_access_denied' => 'Conversation not found or you do not have access permission.',
        'general_error' => 'An error occurred.',
    ],
    
    // Warning messages
    'warning' => [
        'prompt_system_no_edit' => 'System prompts cannot be edited',
        'prompt_system_no_delete' => 'System prompts cannot be deleted',
        'prompt_default_no_delete' => 'Default prompt cannot be deleted',
        'prompt_common_no_delete' => 'Common features prompt cannot be deleted',
        'prompt_system_no_status_change' => 'System prompt status cannot be changed',
        'prompt_cannot_delete' => 'This prompt cannot be deleted',
    ],
    
    // Info messages
    'info' => [
        'greeting' => 'Hello! How can I help you?',
        'no_conversations' => 'No conversations yet',
        'no_conversations_description' => 'You can start a new conversation using the AI assistant.',
        'no_prompts' => 'No prompt templates yet',
        'no_prompts_description' => 'You can use the "New Prompt" button to add new prompt templates.',
        'what_is_this_prompt' => 'What is this prompt?',
        'common_prompt_description' => 'This prompt defines the AI assistant\'s identity, personality, and behaviors. It is added before the conversation-specific prompt in every conversation to ensure the AI has a consistent personality.',
        'common_prompt_features' => 'In this section you can define:',
        'common_prompt_features_list' => [
            'AI assistant\'s name',
            'Company or organization information',
            'Response style and tone',
            'Areas of expertise',
            'Other personality traits'
        ],
    ],
    
    // Confirmation messages
    'confirm' => [
        'delete_conversation' => 'Are you sure you want to delete this conversation?',
        'delete_prompt' => 'Are you sure you want to delete the prompt?',
        'delete_prompt_description' => 'The prompt named ":name" will be deleted and this action cannot be undone.',
        'reset_conversation' => 'Conversation history will be reset. Are you sure?',
        'conversation_id_and_prompt_required' => 'Conversation ID and Prompt ID are required.',
    ],
    
    // Status messages
    'status' => [
        'successful' => 'Successful',
        'failed' => 'Failed',
        'copied' => 'Copied',
        'completed' => 'Completed',
        'active' => 'active',
        'passive' => 'passive',
    ],
    
    // Other general messages
    'general' => [
        'no_data' => '-',
        'loading' => 'Loading...',
        'processing' => 'Processing...',
        'saving' => 'Saving...',
        'deleting' => 'Deleting...',
        'updating' => 'Updating...',
        'you' => 'You',
        'ai' => 'AI',
        'conversation_updated' => 'Conversation prompt updated.',
    ],
];