<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{TenantConversationFlow, AITenantDirective};
use Illuminate\Support\Facades\DB;

class AIWorkflowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Seeding AI Workflow data...');

        // Seed İxtif.com (Tenant ID: 2)
        $this->seedIxtifFlow();
        $this->seedIxtifDirectives();

        $this->command->info('✅ AI Workflow seeded successfully!');
    }

    /**
     * Seed İxtif.com default conversation flow
     */
    protected function seedIxtifFlow(): void
    {
        $this->command->info('📋 Creating İxtif.com conversation flow...');

        // Simple 3-node flow for testing
        $flowData = [
            'nodes' => [
                [
                    'id' => 'node_greeting',
                    'type' => 'ai_response',
                    'name' => 'Karşılama',
                    'class' => 'App\\Services\\ConversationNodes\\Common\\AIResponseNode',
                    'config' => [
                        'system_prompt' => 'Müşteriyi sıcak karşıla. İxtif.com endüstriyel ekipman satış asistanısın. Transpalet, forklift gibi ürünler hakkında yardımcı olabilirsin.',
                        'next_node' => 'node_category',
                    ],
                    'position' => ['x' => 100, 'y' => 100],
                ],
                [
                    'id' => 'node_category',
                    'type' => 'category_detection',
                    'name' => 'Kategori Tespit',
                    // Class field removed - NodeExecutor resolves from type
                    'config' => [
                        'category_found_node' => 'node_products',
                        'category_not_found_node' => 'node_greeting',
                    ],
                    'position' => ['x' => 100, 'y' => 300],
                ],
                [
                    'id' => 'node_products',
                    'type' => 'product_search',
                    'name' => 'Ürün Arama',
                    // Class field removed - NodeExecutor resolves from type
                    'config' => [
                        'limit' => 5,
                        'include_price' => true,
                        'next_node' => null,
                    ],
                    'position' => ['x' => 100, 'y' => 500],
                ],
            ],
            'edges' => [
                [
                    'id' => 'edge_1',
                    'source' => 'node_greeting',
                    'target' => 'node_category',
                ],
                [
                    'id' => 'edge_2',
                    'source' => 'node_category',
                    'target' => 'node_products',
                ],
            ],
        ];

        TenantConversationFlow::updateOrCreate(
            [
                'tenant_id' => 2,
                'flow_name' => 'İxtif.com E-Ticaret Akışı',
            ],
            [
                'flow_description' => 'İxtif.com için basit e-ticaret satış akışı (Karşılama → Kategori Tespit → Ürün Önerme)',
                'flow_data' => $flowData,
                'start_node_id' => 'node_greeting',
                'is_active' => true,
                'priority' => 1,
            ]
        );

        $this->command->info('✅ İxtif.com flow created');
    }

    /**
     * Seed İxtif.com tenant directives
     */
    protected function seedIxtifDirectives(): void
    {
        $this->command->info('⚙️ Creating İxtif.com directives...');

        $directives = [
            // Kategori Ayarları
            [
                'directive_key' => 'category_boundary_strict',
                'directive_value' => 'true',
                'directive_type' => 'boolean',
                'category' => 'behavior',
                'description' => 'Kategori sınırlaması sıkı olsun mu?',
            ],
            [
                'directive_key' => 'allow_cross_category',
                'directive_value' => 'false',
                'directive_type' => 'boolean',
                'category' => 'behavior',
                'description' => 'Kategori dışına çıkılabilir mi?',
            ],
            [
                'directive_key' => 'auto_detect_category',
                'directive_value' => 'true',
                'directive_type' => 'boolean',
                'category' => 'behavior',
                'description' => 'Otomatik kategori tespiti aktif mi?',
            ],

            // Ürün Gösterim
            [
                'directive_key' => 'priority_homepage_products',
                'directive_value' => 'true',
                'directive_type' => 'boolean',
                'category' => 'display',
                'description' => 'Anasayfa ürünleri öncelikli mi?',
            ],
            [
                'directive_key' => 'sort_by_stock',
                'directive_value' => 'true',
                'directive_type' => 'boolean',
                'category' => 'display',
                'description' => 'Stok miktarına göre sırala',
            ],
            [
                'directive_key' => 'max_products_per_response',
                'directive_value' => '5',
                'directive_type' => 'integer',
                'category' => 'display',
                'description' => 'Tek yanıtta maksimum kaç ürün gösterilsin',
            ],

            // Fiyat Politikası
            [
                'directive_key' => 'show_price_without_asking',
                'directive_value' => 'true',
                'directive_type' => 'boolean',
                'category' => 'pricing',
                'description' => 'Fiyatları sormadan göster',
            ],
            [
                'directive_key' => 'currency_conversion_enabled',
                'directive_value' => 'true',
                'directive_type' => 'boolean',
                'category' => 'pricing',
                'description' => 'Kur dönüşümü aktif mi?',
            ],
            [
                'directive_key' => 'default_currency',
                'directive_value' => 'USD',
                'directive_type' => 'string',
                'category' => 'pricing',
                'description' => 'Varsayılan para birimi',
            ],

            // Lead Toplama
            [
                'directive_key' => 'collect_phone_required',
                'directive_value' => 'true',
                'directive_type' => 'boolean',
                'category' => 'lead',
                'description' => 'Telefon numarası toplamak zorunlu mu?',
            ],
            [
                'directive_key' => 'auto_save_leads',
                'directive_value' => 'true',
                'directive_type' => 'boolean',
                'category' => 'lead',
                'description' => 'Lead\'leri otomatik kaydet',
            ],

            // Genel Davranış
            [
                'directive_key' => 'greeting_style',
                'directive_value' => 'friendly',
                'directive_type' => 'string',
                'category' => 'general',
                'description' => 'Selamlama tarzı (formal/friendly/professional)',
            ],
            [
                'directive_key' => 'emoji_usage',
                'directive_value' => 'moderate',
                'directive_type' => 'string',
                'category' => 'general',
                'description' => 'Emoji kullanımı (none/moderate/heavy)',
            ],
        ];

        foreach ($directives as $directive) {
            AITenantDirective::updateOrCreate(
                [
                    'tenant_id' => 2, // İxtif.com
                    'directive_key' => $directive['directive_key'],
                ],
                array_merge($directive, ['is_active' => true])
            );
        }

        $this->command->info('✅ İxtif.com directives created (' . count($directives) . ' directives)');
    }
}
