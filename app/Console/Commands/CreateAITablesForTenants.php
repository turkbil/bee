<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Tenancy;
use App\Models\Tenant;

class CreateAITablesForTenants extends Command
{
    protected $signature = 'ai:create-tables-for-tenants';
    protected $description = 'Create AI tables for tenants that are missing them';

    public function handle()
    {
        $this->info('Creating AI tables for tenants...');
        
        try {
            $tenants = Tenant::all();
            
            foreach ($tenants as $tenant) {
                $this->info("Processing tenant: {$tenant->id}");
                
                // Tenant context'ini başlat
                app(Tenancy::class)->initialize($tenant);
                
                try {
                    // ai_conversations tablosu kontrolü
                    if (!DB::getSchemaBuilder()->hasTable('ai_conversations')) {
                        DB::statement('
                            CREATE TABLE `ai_conversations` (
                                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                                `title` varchar(255) NOT NULL,
                                `type` varchar(255) NOT NULL DEFAULT "chat",
                                `feature_name` varchar(255) DEFAULT NULL,
                                `is_demo` tinyint(1) NOT NULL DEFAULT "0",
                                `user_id` bigint unsigned NOT NULL,
                                `tenant_id` bigint unsigned DEFAULT NULL,
                                `prompt_id` bigint unsigned DEFAULT NULL,
                                `total_tokens_used` int NOT NULL DEFAULT "0",
                                `metadata` json DEFAULT NULL,
                                `status` varchar(255) NOT NULL DEFAULT "active",
                                `created_at` timestamp NULL DEFAULT NULL,
                                `updated_at` timestamp NULL DEFAULT NULL,
                                PRIMARY KEY (`id`),
                                KEY `ai_conversations_user_id_index` (`user_id`),
                                KEY `ai_conversations_prompt_id_index` (`prompt_id`),
                                KEY `ai_conversations_type_index` (`type`),
                                KEY `ai_conversations_feature_name_index` (`feature_name`),
                                KEY `ai_conversations_tenant_id_index` (`tenant_id`),
                                KEY `ai_conversations_status_index` (`status`),
                                KEY `ai_conversations_created_at_index` (`created_at`),
                                KEY `ai_conversations_updated_at_index` (`updated_at`),
                                KEY `ai_conversations_user_created_idx` (`user_id`,`created_at`),
                                KEY `ai_conversations_prompt_created_idx` (`prompt_id`,`created_at`),
                                KEY `ai_conversations_type_created_idx` (`type`,`created_at`),
                                KEY `ai_conversations_tenant_created_idx` (`tenant_id`,`created_at`),
                                CONSTRAINT `ai_conversations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                        ');
                        $this->info("  ✓ ai_conversations table created");
                    } else {
                        $this->info("  - ai_conversations table already exists");
                    }
                    
                    // ai_messages tablosu kontrolü
                    if (!DB::getSchemaBuilder()->hasTable('ai_messages')) {
                        DB::statement('
                            CREATE TABLE `ai_messages` (
                                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                                `conversation_id` bigint unsigned NOT NULL,
                                `role` enum("user","assistant","system") NOT NULL,
                                `message` longtext NOT NULL,
                                `tokens_used` int NOT NULL DEFAULT "0",
                                `created_at` timestamp NULL DEFAULT NULL,
                                `updated_at` timestamp NULL DEFAULT NULL,
                                PRIMARY KEY (`id`),
                                KEY `ai_messages_conversation_id_index` (`conversation_id`),
                                KEY `ai_messages_role_index` (`role`),
                                KEY `ai_messages_created_at_index` (`created_at`),
                                CONSTRAINT `ai_messages_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `ai_conversations` (`id`) ON DELETE CASCADE
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                        ');
                        $this->info("  ✓ ai_messages table created");
                    } else {
                        $this->info("  - ai_messages table already exists");
                    }
                    
                } finally {
                    // Tenant context'ini sonlandır
                    app(Tenancy::class)->end();
                }
            }
            
            $this->info('AI tables creation completed successfully!');
            
        } catch (\Exception $e) {
            $this->error('Error creating AI tables: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}