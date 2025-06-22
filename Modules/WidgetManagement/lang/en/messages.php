<?php

return [
    // Success messages
    'widget_created' => 'New component created.',
    'widget_updated' => 'Component updated.',
    'widget_deleted' => ':name deleted.',
    'widget_activated' => 'Component activated.',
    'widget_deactivated' => 'Component deactivated.',
    'widget_settings_saved' => 'Widget settings saved.',
    'widget_form_structure_saved' => 'Widget form structure saved.',

    'item_created' => 'New content added.',
    'item_updated' => 'Content updated.',
    'item_deleted' => 'Item successfully deleted.',
    'item_activated' => 'Content status updated to active.',
    'item_deactivated' => 'Content status updated to inactive.',
    'items_reordered' => 'Items successfully reordered.',

    'category_created' => 'Category successfully added.',
    'category_updated' => 'Category successfully updated.',
    'category_deleted' => 'Category successfully deleted.',
    'category_activated' => 'Category activated.',
    'category_deactivated' => 'Category deactivated.',
    'category_order_updated' => 'Category order updated.',
    'category_moved' => 'Category moved under :parent.',
    'category_moved_to_root' => 'Category moved as main category.',

    // Error messages
    'widget_not_found' => 'Widget not found or inactive.',
    'widget_file_not_found' => 'Specified file not found: :path',
    'widget_module_file_not_found' => 'Specified module file not found: :path',
    'widget_view_not_found' => 'Specified view file not found: :path',
    'widget_template_load_error' => 'Error loading widget template: :error',
    'widget_instance_load_error' => 'Error loading widget instance: :error',
    'widget_render_error' => 'Widget render error: :error',
    'widget_view_render_error' => 'View render error: :error',
    'widget_module_render_error' => 'Module render error: :error',
    'widget_content_empty' => 'Widget content is empty',
    'widget_html_empty' => 'Rendered HTML is empty or whitespace.',

    'widget_name_required' => 'Component name cannot be empty.',
    'widget_data_missing' => 'Missing parameters: widgetId or formData',
    'widget_json_invalid' => 'Invalid JSON format',
    'widget_form_data_empty' => 'Form data cannot be empty.',

    'item_save_error' => 'Error saving content: :error',
    'item_delete_error' => 'Error deleting item: :error',
    'item_reorder_error' => 'Error reordering items: :error',
    'item_cannot_delete_static' => 'Cannot delete the only content item of a static component.',

    'category_has_widgets' => 'This category has associated widgets. You must delete them first or move them to another category.',
    'category_has_children' => 'This category has subcategories. You must delete the subcategories first.',
    'category_add_error' => 'Error adding category.',
    'category_update_error' => 'Error updating category.',
    'category_delete_error' => 'Error deleting category.',
    'category_toggle_error' => 'Error changing category status.',
    'category_order_error' => 'Error updating category order.',

    // File upload messages
    'file_uploaded' => 'File successfully uploaded.',
    'file_upload_error' => 'Error uploading file.',
    'file_deleted' => 'File successfully deleted.',
    'file_not_found' => 'File not found.',
    'image_uploaded' => 'Image successfully uploaded.',
    'image_upload_error' => 'Error uploading image.',
    'multiple_images_uploaded' => 'Images successfully uploaded.',

    // Validation messages
    'title_required' => 'Title is required.',
    'title_min' => 'Title must be at least :min characters.',
    'title_max' => 'Title must not exceed :max characters.',
    'category_title_required' => 'Category title is required.',
    'category_title_min' => 'Category title must be at least :min characters.',
    'category_title_max' => 'Category title must not exceed :max characters.',
    'slug_regex' => 'Slug can only contain letters, numbers, hyphens and underscores.',
    'form_fields_check' => 'Please check the form fields.',

    // Warning messages
    'widget_no_permission' => 'Content schema cannot be edited for this widget type. You can only edit its settings.',
    'widget_not_module_type' => 'This widget is not a module type.',
    'widget_file_path_missing' => 'File path is not defined for this file widget.',
    'widget_module_file_path_missing' => 'File path is not defined for this module widget.',
    'widget_module_no_html' => 'No HTML template is defined for this module component. Please edit the widget and add an HTML template.',
    
    // Info messages
    'widget_loading' => 'Loading widget form...',
    'canvas_loading' => 'Loading widget form...',
    'content_loading' => 'Error loading content: :error',
    'no_element_selected' => 'No Element Selected',
    'select_element_to_edit' => 'Select a form element to edit its properties.',
    'widget_form_building_start' => 'Start Building Widget Form',
    'drag_elements_here' => 'Drag elements from the left and drop them here.',

    // Empty state messages
    'no_components_found' => 'No components found',
    'no_components_add_new' => 'You can go to the "Component Gallery" page to add a new component',
    'no_content_found' => 'No content found yet',
    'no_content_add_new' => 'Use the "Add New Content" button to create your component content.',
    'no_categories_found' => 'No categories found',
    'no_categories_search' => 'No categories found matching your search criteria.',
    'no_categories_add_new' => 'No categories added yet. You can use the form on the left to add a new category.',
    'clear_search' => 'Clear Search',

    // Form builder messages
    'form_structure_saved' => 'Form structure saved',
    'form_structure_save_error' => 'Error saving form structure',
    'widget_settings_structure' => ':name Settings',
    'widget_content_structure' => ':name Content Structure',
    'schema_data_not_found' => 'Schema data not found',

    // Preview messages
    'preview_info' => 'Preview Information:',
    'widget_type' => 'Type:',
    'widget_description' => 'Description:',
    'description_not_available' => 'Description not available',
    'widget_content_empty_preview' => 'Widget Content Empty',
    'widget_no_processed_html' => 'No processed HTML content found for this widget.',

    // Navigation messages
    'back_to_list' => 'Back to List',
    'back_to_components' => 'Back',
    'go_to_gallery' => 'Go to Component Gallery',
    'go_to_management' => 'Go to Management',

    // Menu titles
    'component_management' => 'Component Management',
    'active_components' => 'Active Components',
    'component_gallery' => 'Component Gallery',
    'component_menu' => 'Component Menu',
    'special_components' => 'Special Components',
    'ready_files' => 'Ready Files',
    'component_configuration' => 'Component Configuration',
    'category_management' => 'Category Management',
    'add_component' => 'Add Component',
    'content_management' => 'Content Management',

    // Page titles and descriptions
    'active_components_desc' => 'Manage your active components',
    'widget_content_management_desc' => 'You can manage widget content from here.',
    'widget_form_editing' => 'Widget Form Editing',

    // Button texts
    'add_new_content' => 'Add New Content',
    'add_content' => 'Add Content',
    'edit_content' => 'Edit Content',
    'save_and_continue' => 'Save and Continue',
    'save_and_add_new' => 'Save and Add New',
    'reset_to_default' => 'Reset to Default',

    // Modal and dialog messages
    'confirm_delete_component' => 'Are you sure you want to delete this component?',
    'confirm_delete_content' => 'Are you sure you want to delete this content?',
    'confirm_delete_category' => 'Are you sure you want to delete this category?',

    // Placeholder texts
    'search_components' => 'Search components...',
    'search_categories' => 'Start typing to search...',
    'enter_widget_name' => 'Enter widget name',
    'enter_content_title' => 'Enter content title',
    'enter_category_title' => 'Category title',
    'enter_category_slug' => 'category-slug',
    'enter_category_description' => 'Category description',
    'select_parent_category' => 'Select Parent Category',
    'add_as_main_category' => 'Add as Main Category',

    // Help text and descriptions
    'slug_auto_generate' => 'Will be auto-generated if left empty',
    'fontawesome_icon_code' => 'FontAwesome icon code (e.g. fa-folder)',
    'drag_image_or_click' => 'Drag and drop image or click',
    'drop_file_here' => 'Drop it!',

    // Status texts
    'widget_empty_content' => 'Widget content is empty',
    'widget_not_displayed' => 'Widget cannot be displayed',
    'check_widget_configuration' => 'Please check the widget configuration.',

    // Category specific messages
    'category_edit' => 'Edit Category',
    'add_new_category' => 'Add New Category',
    'main_category' => 'Main Category',
    'sub_categories' => 'Subcategories',
    'show_all_items' => 'Show All',

    // Widget Studio specific messages
    'widget_studio_title' => ':name Widget Studio',
    'settings_schema' => 'Settings',
    'content_schema' => 'Content Structure',

    // File and media messages
    'current_image' => 'Current Photo',
    'uploaded_image' => 'Uploaded Photo',
    'no_image' => 'No image',
    'multiple_images_info' => '+:count images',

    // Dropdown and list texts
    'items_per_page' => ':count Components',
    'default_tab' => 'Default Tab',
    'tab_title' => 'Tab :number',

    // Widget special types
    'static_widget' => 'Static Component',
    'dynamic_widget' => 'Dynamic Component',
    'content_widget' => 'Content Component',
    'file_widget' => 'File Component',
    'module_widget' => 'Module Component',

    // System fields
    'widget_title' => 'Widget Title',
    'item_title' => 'Title',
    'item_status' => 'Status',
    'category_not_assigned' => 'Category Not Assigned',
    'no_category' => 'No Category',

    // JSON and data messages
    'json_response_success' => 'Operation completed successfully',
    'json_response_error' => 'An error occurred during the operation',
];