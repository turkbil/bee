<?php

return [
    // General titles
    'title' => 'Studio',
    'editor' => 'Studio Editor',
    'widget_manager' => 'Widget Manager',
    'visual_editor' => 'Visual Editor',
    'studio_home' => 'Studio Home',
    'page_editor' => 'Page Editor',
    'portfolio_editor' => 'Portfolio Editor',
    
    // Actions
    'actions' => [
        'save' => 'Save',
        'preview' => 'Preview',
        'export' => 'Export',
        'cancel' => 'Cancel',
        'delete' => 'Delete',
        'edit' => 'Edit',
        'add' => 'Add',
        'close' => 'Close',
        'back' => 'Back',
        'clear_content' => 'Clear content',
        'undo' => 'Undo',
        'redo' => 'Redo',
        'show_hide_borders' => 'Show/hide component borders',
        'edit_html' => 'Edit HTML',
        'edit_css' => 'Edit CSS',
        'view_all' => 'View All',
        'create_first' => 'Create First Page',
    ],
    
    // Messages
    'messages' => [
        'save_success' => 'Content saved successfully.',
        'save_error' => 'An error occurred while saving content.',
        'save_general_error' => 'Error occurred while saving',
        'delete_confirm' => 'Are you sure you want to delete this item?',
        'clear_confirm' => 'Are you sure you want to clear the content? This action cannot be undone.',
        'loading_error' => 'Error loading content',
        'widget_loading_error' => 'Error loading widget',
        'file_upload_error' => 'File upload error',
        'no_valid_file' => 'Files could not be uploaded. No valid file found.',
        'select_file' => 'Please select a file.',
        'content_could_not_saved' => 'Content could not be saved.',
        'blocks_could_not_loaded' => 'Block data could not be retrieved',
        'resources_copied' => 'Resources copied successfully',
        'resources_copy_error' => 'Error occurred while copying resources',
        'view_not_found' => 'View not found',
        'widget_content_loading' => 'Loading widget content...',
        'no_pages_yet' => 'No pages yet',
        'first_page_hint' => 'Click the button below to create your first page',
    ],
    
    // Blocks and Components
    'blocks' => [
        'layout' => 'Layout',
        'content' => 'Content',
        'form' => 'Form',
        'media' => 'Media',
        'widget' => 'Widgets',
        'components' => 'Components',
        'active_components' => 'Active Components',
        'search_component' => 'Search component...',
        'search_layer' => 'Search layer...',
    ],
    
    // Tabs
    'tabs' => [
        'blocks' => 'Components',
        'styles' => 'Styles',
        'layers' => 'Layers',
        'configure' => 'Configure',
        'design' => 'Design',
    ],
    
    // Devices
    'devices' => [
        'desktop' => 'Desktop',
        'tablet' => 'Tablet',
        'mobile' => 'Mobile',
    ],
    
    // Statistics
    'stats' => [
        'total_pages' => 'Total Pages',
        'active_component' => 'Active Components',
        'unlimited_editing' => 'Unlimited Editing',
        'responsive' => 'Responsive',
    ],
    
    // Page Operations
    'page' => [
        'operations' => 'Page Operations',
        'all_pages' => 'All Pages',
        'new_page' => 'New Page',
        'add_new_page' => 'Add New Page',
        'recent_edited' => 'Recently Edited Pages',
        'edit_with_studio' => 'Edit with Studio',
    ],
    
    // Widget Operations
    'widget' => [
        'management' => 'Widget Management',
        'placeholder' => 'Widget',
        'loading' => 'Loading widget...',
        'load_error' => 'Widget loading error',
    ],
    
    // Quick Start
    'quick_start' => [
        'title' => 'Quick Start',
        'new_page' => 'Create New Page',
        'all_pages' => 'All Pages',
        'widget_management' => 'Widget Management',
    ],
    
    // How to Use
    'how_to_use' => [
        'title' => 'How to Use',
        'step1_title' => 'Select Page',
        'step1_desc' => 'Select the page you want to edit',
        'step2_title' => 'Open Studio',
        'step2_desc' => 'Click the "Edit with Studio" button',
        'step3_title' => 'Design',
        'step3_desc' => 'Drag and drop components',
        'step4_title' => 'Save',
        'step4_desc' => 'Save your changes',
    ],
    
    // Log messages
    'logs' => [
        'content_saved' => 'edited with studio',
        'save_request' => 'Studio Save - Request Details',
        'prepared_values' => 'Studio Save - Prepared Values',
        'save_error' => 'Error saving Studio content',
        'page_load_error' => 'Error loading page',
        'portfolio_load_error' => 'Error loading portfolio',
        'file_upload_error' => 'File upload error',
        'blocks_loading' => 'BlockService::getAllBlocks - Loading blocks',
        'widget_module_not_found' => 'WidgetManagement module not found',
        'total_blocks_loaded' => 'BlockService - Total :count blocks loaded',
        'block_load_error' => 'BlockService - Block loading error',
        'tenant_widget_load_error' => 'Tenant widget loading error',
        'block_data_error' => 'Error retrieving block data',
        'resource_copy_error' => 'Resource copy error',
        'widget_loading_started' => 'Loading started for widget :id...',
        'container_not_found' => 'Container not found, trying alternative search...',
        'widget_container_not_found' => 'Widget container not found',
        'global_loader_used' => 'Using global loader',
        'direct_fetch_loading' => 'Loading with direct fetch',
        'widget_loaded_successfully' => 'Widget :id loaded successfully',
        'widget_retry_loading' => 'Retrying load for widget :id...',
    ],
    
    // Error messages
    'errors' => [
        'general' => 'Error',
        'load' => 'Loading error',
        'save' => 'Save error',
        'upload' => 'Upload error',
    ],
    
    // Widget categories
    'categories' => [
        'modules' => 'Modules',
        'page' => 'Page',
        'content' => 'Content',
    ],
];