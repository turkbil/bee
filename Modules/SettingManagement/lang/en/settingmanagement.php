<?php

return [
    'title' => 'Setting Management',
    'description' => 'Manage system settings',
    'list' => 'Settings List',
    'create' => 'New Setting',
    'edit' => 'Edit Setting',
    
    'group' => [
        'title' => 'Group',
        'list' => 'Group List',
        'create' => 'New Group',
        'edit' => 'Edit Group',
        'name' => 'Group Name',
        'description' => 'Description',
        'status' => 'Status',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'enabled' => 'Enabled',
        'disabled' => 'Disabled',
    ],
    
    'messages' => [
        'success' => 'Success!',
        'error' => 'Error!',
        'group_created' => 'Group created successfully',
        'group_updated' => 'Group updated successfully',
        'group_deleted' => 'Group deleted successfully',
        'group_status_updated' => 'Group status updated',
        'group_activated' => 'activated',
        'group_deactivated' => 'deactivated',
        'group_create_error' => 'An error occurred while creating the group',
        'group_delete_error' => 'Cannot delete a group that has subgroups',
        'form_layout_saved' => 'Form structure saved',
        'values_saved' => 'Changes saved.',
        'file_removed' => 'File removed.',
        'file_deleted' => 'File deleted.',
        'file_upload_error' => 'An error occurred while uploading the file: ',
        'multi_image_upload_error' => 'An error occurred while uploading multiple images: ',
    ],
    
    'actions' => [
        'created' => 'created',
        'updated' => 'updated',
        'deleted' => 'deleted',
        'form_layout_updated' => 'form layout updated', 
        'value_updated' => 'value updated',
        'reset_to_default' => 'reset to default value',
    ],
    
    'fields' => [
        'name' => 'Name',
        'value' => 'Value', 
        'default' => 'Default',
        'description' => 'Description',
        'type' => 'Type',
        'required' => 'Required',
        'options' => 'Options',
    ],
    
    'file_upload' => [
        'drag_drop' => 'Drag and drop image or click here',
        'drop_here' => 'Drop here!',
        'supported_formats' => 'PNG, JPG, WEBP, GIF - Max 2MB - Multiple selection supported',
        'uploaded_photo' => 'Uploaded Photo',
        'alt_text' => 'Image',
    ],
    
    'misc' => [
        'show_all' => 'Show All',
        'loading' => 'Loading...',
        'no_results' => 'No results found',
        'search_placeholder' => 'Search...',
    ],
    
    'operations' => 'Settings Operations',
    'tenant_settings' => 'Tenant Settings',
];