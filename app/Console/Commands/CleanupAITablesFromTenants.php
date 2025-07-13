<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Tenancy;
use App\Models\Tenant;

class CleanupAITablesFromTenants extends Command
{
    protected $signature = 'ai:cleanup-tenant-tables';
    protected $description = 'Remove AI tables from tenant databases (AI should be central only)';

    public function handle()
    {
        $this->info('Removing AI tables from tenant databases...');
        $this->warn('AI tables should only exist in central database with tenant_id separation');
        
        try {
            $tenants = Tenant::all();
            
            foreach ($tenants as $tenant) {
                $this->info("Processing tenant: {$tenant->id}");
                
                // Tenant context'ini başlat
                app(Tenancy::class)->initialize($tenant);
                
                try {
                    // Foreign key check'leri geçici olarak kapat
                    DB::statement('SET FOREIGN_KEY_CHECKS=0');
                    
                    // AI tablolarını sırayla sil
                    $aiTables = [
                        'ai_token_usage',
                        'ai_messages', 
                        'ai_conversations',
                        'ai_settings',
                        'ai_token_purchases',
                        'ai_tenant_profiles'
                    ];
                    
                    foreach ($aiTables as $table) {
                        if (DB::getSchemaBuilder()->hasTable($table)) {
                            DB::statement("DROP TABLE `{$table}`");
                            $this->info("  ✓ {$table} table removed");
                        }
                    }
                    
                    // Foreign key check'leri tekrar aç
                    DB::statement('SET FOREIGN_KEY_CHECKS=1');
                    
                } finally {
                    // Tenant context'ini sonlandır
                    app(Tenancy::class)->end();
                }
            }
            
            $this->info('');
            $this->info('AI table cleanup completed successfully!');
            $this->info('All AI data will now be stored in central database with tenant_id separation');
            
        } catch (\Exception $e) {
            $this->error('Error cleaning up AI tables: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}