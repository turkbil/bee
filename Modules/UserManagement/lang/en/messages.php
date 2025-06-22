<?php

return [
    // Success Messages
    'user_created' => 'User created successfully.',
    'user_updated' => 'User updated successfully.',
    'user_deleted' => 'User deleted successfully.',
    'user_activated' => '"{name}" has been activated.',
    'user_deactivated' => '"{name}" has been deactivated.',
    'selected_users_status_updated' => 'Selected records status has been updated.',
    
    'role_created' => 'Role created successfully.',
    'role_updated' => 'Role updated successfully.',
    'role_deleted' => 'Role deleted successfully.',
    'role_cannot_be_edited' => 'This role cannot be edited.',
    
    'permission_created' => 'Permission created successfully.',
    'permission_updated' => 'Permission updated successfully.',
    'permission_deleted' => 'Permission deleted successfully.',
    'permissions_saved' => 'Permissions saved successfully.',
    
    'avatar_updated' => 'Avatar updated successfully.',
    'avatar_removed' => 'Avatar removed successfully.',
    'avatar_upload_loading' => 'Avatar management system loading...',
    'user_info' => 'User: {name}',
    
    // Error Messages
    'operation_error' => 'An error occurred during the operation: {error}',
    'role_protected' => 'This role name is protected by the system.',
    'user_not_found' => 'User not found.',
    'role_not_found' => 'Role not found.',
    'permission_not_found' => 'Permission not found.',
    'no_permission_for_module' => 'No available permissions found for this module. Please define module permissions first.',
    
    // Confirmation Messages
    'confirm_delete_user' => 'Are you sure you want to delete this user?',
    'confirm_delete_role' => 'Are you sure you want to delete this role?',
    'confirm_delete_permission' => 'Are you sure you want to delete this permission?',
    'confirm_clear_all_logs' => 'Are you sure you want to clear all records?',
    'confirm_clear_user_logs' => 'Are you sure you want to clear all records for this user?',
    
    // Role Descriptions
    'user_role_description' => 'Users with normal member role can only perform basic user operations. They do not have access to the admin panel and modules.',
    'editor_role_description' => 'Editors can access the modules selected below and perform operations related to these modules. Separate CRUD permissions can be defined for each module.',
    'admin_role_description' => 'Admin user has full access to all modules and functions within their tenant. No special permission assignment is required for this role.',
    'root_role_warning' => 'Root user has full access to all modules and functions in the system. This role is designed only for system administrators.',
    
    // Module Authorization
    'module_authorization' => 'Module Authorization',
    'detailed_authorization' => 'Detailed Authorization',
    'simple_view' => 'Simple View',
    'standard_permissions_view' => 'Standard Permissions View',
    'detailed_crud_permissions' => 'Detailed CRUD Permissions View',
    
    // Log Descriptions
    'user_roles_cleared' => '"{name}" user\'s roles have been cleared',
    'user_direct_permissions_cleared' => '"{name}" user\'s direct permissions have been cleared',
    'user_module_permissions_cleared' => '"{name}" user\'s module permissions have been cleared',
    'user_module_permissions_updated' => '"{name}" user\'s module permissions have been updated',
    'user_permissions_updated' => '{count} permissions updated',
    
    // Validation Messages
    'name_required' => 'Name is required.',
    'name_min' => 'Name must be at least 3 characters.',
    'email_required' => 'Email is required.',
    'email_valid' => 'Please enter a valid email address.',
    'email_unique' => 'This email address is already in use.',
    'password_min' => 'Password must be at least 6 characters.',
    'role_name_required' => 'Role name is required.',
    'role_name_min' => 'Role name must be at least 3 characters.',
    'role_name_max' => 'Role name can be at most 255 characters.',
    'role_name_unique' => 'This role name is already in use.',
    'guard_name_required' => 'Guard name is required.',
    'permission_name_required' => 'Permission name is required.',
    'permission_name_min' => 'Permission name must be at least 3 characters.',
    'permission_name_max' => 'Permission name can be at most 255 characters.',
    'permission_name_unique' => 'This permission name is already in use.',
    'module_name_required' => 'Module name is required.',
    'module_name_min' => 'Module name must be at least 3 characters.',
    'module_name_max' => 'Module name can be at most 255 characters.',
    'permission_types_required' => 'You must select at least one permission type.',
    'permission_types_min' => 'You must select at least one permission type.',
    'manual_permission_min' => 'Manual permission name must be at least 3 characters.',
    'manual_permission_max' => 'Manual permission name can be at most 255 characters.',
    
    // Avatar Upload Messages
    'avatar_upload_error' => 'An error occurred while uploading avatar.',
    'avatar_format_error' => 'Unsupported file format. Please select a file in PNG, JPG or WebP format.',
    'avatar_size_error' => 'File size is too large. Maximum 2MB allowed.',
    
    // Activity Log Messages
    'created' => 'created',
    'updated' => 'updated',
    'deleted' => 'deleted',
    'activated' => 'activated',
    'deactivated' => 'deactivated',
    'login' => 'logged in',
    'logout' => 'logged out',
    'password_changed' => 'password changed',
    'profile_updated' => 'profile updated',
    'role_assigned' => 'role assigned',
    'role_removed' => 'role removed',
    'permission_granted' => 'permission granted',
    'permission_revoked' => 'permission revoked',
];