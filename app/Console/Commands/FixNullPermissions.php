<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Tenancy;
use App\Models\Tenant;
use Spatie\Permission\Models\Permission;

class FixNullPermissions extends Command
{
    protected $signature = 'permissions:fix-null-descriptions';
    protected $description = 'Fix NULL permission descriptions across all tenants';

    public function handle()
    {
        $this->info('Starting NULL permission description fix...');
        
        try {
            // Tüm tenant'ları al
            $tenants = Tenant::all();
            
            foreach ($tenants as $tenant) {
                $this->info("Processing tenant: {$tenant->id}");
                
                // Tenant context'ini başlat
                app(Tenancy::class)->initialize($tenant);
                
                try {
                    // NULL description'a sahip permission'ları al
                    $nullPermissions = Permission::whereNull('description')->get();
                    
                    foreach ($nullPermissions as $permission) {
                        $description = $this->generatePermissionDescription($permission->name);
                        
                        $permission->update(['description' => $description]);
                        
                        $this->info("  Fixed: {$permission->name} -> {$description}");
                    }
                    
                    $this->info("  Fixed {$nullPermissions->count()} permissions for tenant {$tenant->id}");
                    
                } finally {
                    // Tenant context'ini sonlandır
                    app(Tenancy::class)->end();
                }
            }
            
            $this->info('NULL permission description fix completed successfully!');
            
        } catch (\Exception $e) {
            $this->error('Error fixing NULL permissions: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
    
    /**
     * Permission description'ını otomatik oluştur
     */
    protected function generatePermissionDescription(string $permissionName): string
    {
        $parts = explode('.', $permissionName);
        $moduleName = $parts[0] ?? '';
        $action = $parts[1] ?? '';
        
        // Modül adını Türkçe'ye çevir
        $moduleDisplayNames = [
            'ai' => 'AI',
            'widgetmanagement' => 'Widgetmanagement',
            'modulemanagement' => 'Modulemanagement',
            'tenantmanagement' => 'Tenantmanagement',
            'usermanagement' => 'Usermanagement',
            'settingmanagement' => 'Settingmanagement',
            'thememanagement' => 'Thememanagement',
            'studio' => 'Studio',
            'announcement' => 'Announcement',
            'page' => 'Page',
            'portfolio' => 'Portfolio',
            'languagemanagement' => 'Languagemanagement'
        ];
        
        // Action'ı Türkçe'ye çevir
        $actionDisplayNames = [
            'view' => 'Görüntüleme',
            'create' => 'Oluşturma',
            'edit' => 'Güncelleme',
            'delete' => 'Silme',
            'manage' => 'Yönetme',
            'update' => 'Güncelleme'
        ];
        
        $moduleDisplay = $moduleDisplayNames[$moduleName] ?? ucfirst($moduleName);
        $actionDisplay = $actionDisplayNames[$action] ?? ucfirst($action);
        
        return "{$moduleDisplay} - {$actionDisplay}";
    }
}