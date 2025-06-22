<?php

return [
    // Success Messages
    'page_created' => 'Page created successfully',
    'page_updated' => 'Page updated successfully',
    'page_deleted' => 'Page deleted successfully',
    'page_published' => 'Page published successfully',
    'page_unpublished' => 'Page unpublished',
    'page_archived' => 'Page archived',
    'page_restored' => 'Page restored',
    'page_duplicated' => 'Page duplicated',
    'page_activated' => '":title" activated',
    'page_deactivated' => '":title" deactivated',
    'pages_reordered' => 'Pages reordered',
    'settings_updated' => 'Settings updated successfully',
    'cache_cleared' => 'Cache cleared',
    
    // Error Messages
    'page_not_found' => 'Page not found',
    'page_create_failed' => 'Page could not be created',
    'page_update_failed' => 'Page could not be updated',
    'page_delete_failed' => 'Page could not be deleted',
    'page_publish_failed' => 'Page could not be published',
    'page_access_denied' => 'You do not have access to this page',
    'slug_already_exists' => 'This URL slug is already in use',
    'parent_not_found' => 'Parent page not found',
    'cannot_delete_with_children' => 'Cannot delete page with child pages',
    'invalid_template' => 'Invalid template selection',
    'upload_failed' => 'File upload failed',
    
    // Warning Messages
    'confirm_delete' => 'Are you sure you want to delete this page?',
    'confirm_archive' => 'Are you sure you want to archive this page?',
    'confirm_unpublish' => 'Are you sure you want to unpublish this page?',
    'confirm_bulk_delete' => 'Are you sure you want to delete selected pages?',
    'unsaved_changes' => 'You have unsaved changes',
    'leave_without_saving' => 'Are you sure you want to leave without saving your changes?',
    
    // Info Messages
    'page_auto_saved' => 'Page auto-saved',
    'page_scheduled' => 'Page scheduled for publication',
    'no_pages_found' => 'No pages found',
    'no_results' => 'No pages match your search',
    'draft_mode' => 'You are working in draft mode',
    'preview_mode' => 'You are in preview mode',
    
    // Bulk Actions
    'bulk_published' => ':count pages published',
    'bulk_unpublished' => ':count pages unpublished',
    'bulk_archived' => ':count pages archived',
    'bulk_deleted' => ':count pages deleted',
    'bulk_restored' => ':count pages restored',
    'no_items_selected' => 'No items selected',
    'select_action' => 'Select an action',
    
    // Validation
    'title_required' => 'Title is required',
    'title_min' => 'Title must be at least 3 characters',
    'title_max' => 'Title cannot exceed 255 characters',
    'content_required' => 'Content is required',
    'slug_required' => 'URL slug is required',
    'slug_unique' => 'This URL slug is already in use',
    'slug_format' => 'URL slug can only contain letters, numbers and hyphens',
    'parent_invalid' => 'Invalid parent page selection',
    'template_required' => 'Template selection is required',
    'homepage_cannot_be_deactivated' => 'Homepage cannot be deactivated!',
    
    // Helper Texts
    'slug_help' => 'URL-friendly version of the title. Leave blank to auto-generate from title.',
    'excerpt_help' => 'Short description of the page. Used in list views.',
    'meta_description_help' => 'Page description for search engines (160 characters recommended)',
    'parent_help' => 'Select to make this page a child of another page',
    'template_help' => 'Display template to use for this page',
    'visibility_help' => 'Controls who can see this page',
];