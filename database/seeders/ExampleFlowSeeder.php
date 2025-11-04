<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TenantConversationFlow;

class ExampleFlowSeeder extends Seeder
{
    /**
     * Seed example conversation flow for tenant (İxtif.com)
     */
    public function run(): void
    {
        // Check if we're in tenant context
        if (!tenancy()->initialized) {
            $this->command->error('❌ This seeder must run in tenant context!');
            $this->command->info('💡 Use: php artisan tenants:seed --class=Database\\Seeders\\ExampleFlowSeeder');
            return;
        }

        $tenantId = tenant('id');
        $this->command->info("🏢 Creating example flow for tenant: {$tenantId}");

        // Example Flow for İxtif.com (E-commerce flow)
        $exampleFlow = [
            'flow_name' => 'İxtif E-Ticaret Satış Akışı',
            'flow_description' => 'Transpalet ve forklift satışı için optimize edilmiş AI sohbet akışı. Kategori tespiti, ürün önerme, fiyat bilgilendirme ve teklif hazırlama adımlarını içerir.',
            'is_active' => true,
            'priority' => 100,
            'flow_data' => [
                'nodes' => [
                    // Node 1: AI Response - Selamlama
                    [
                        'id' => 'node_1',
                        'type' => 'ai_response',
                        'name' => 'Karşılama',
                        'config' => [
                            'prompt' => 'Müşteriyi sıcak ve profesyonel bir şekilde karşıla. İxtif.com olarak transpalet ve forklift konusunda yardımcı olabileceğini belirt.',
                        ],
                        'position' => ['x' => 150, 'y' => 100],
                    ],

                    // Node 2: Category Detection
                    [
                        'id' => 'node_2',
                        'type' => 'category_detection',
                        'name' => 'Kategori Tespiti',
                        'config' => [
                            'categories' => ['transpalet', 'forklift', 'aksesuarlar'],
                            'confidence_threshold' => 0.7,
                        ],
                        'position' => ['x' => 150, 'y' => 280],
                    ],

                    // Node 3: Product Recommendation
                    [
                        'id' => 'node_3',
                        'type' => 'product_recommendation',
                        'name' => 'Ürün Önerisi',
                        'config' => [
                            'max_products' => 5,
                            'homepage_priority' => true,
                            'stock_priority' => true,
                            'show_images' => true,
                        ],
                        'position' => ['x' => 150, 'y' => 460],
                    ],

                    // Node 4: Condition - Fiyat Sorusu
                    [
                        'id' => 'node_4',
                        'type' => 'condition',
                        'name' => 'Fiyat Soruldu mu?',
                        'config' => [
                            'condition_type' => 'contains_keyword',
                            'keywords' => ['fiyat', 'kaç para', 'ne kadar', 'ücret'],
                            'true_branch' => 'node_5',
                            'false_branch' => 'node_6',
                        ],
                        'position' => ['x' => 150, 'y' => 640],
                    ],

                    // Node 5: Price Filter (RIGHT BRANCH)
                    [
                        'id' => 'node_5',
                        'type' => 'price_filter',
                        'name' => 'Fiyat Bilgisi',
                        'config' => [
                            'show_currency' => true,
                            'show_vat' => true,
                            'price_format' => 'detailed',
                        ],
                        'position' => ['x' => 500, 'y' => 760],
                    ],

                    // Node 6: Collect Data (MAIN BRANCH)
                    [
                        'id' => 'node_6',
                        'type' => 'collect_data',
                        'name' => 'İletişim Bilgisi Al',
                        'config' => [
                            'fields' => ['phone', 'email', 'company_name'],
                            'required' => ['phone'],
                            'message' => 'Size daha detaylı bilgi verebilmem için iletişim bilgilerinizi alabilir miyim?',
                        ],
                        'position' => ['x' => 150, 'y' => 900],
                    ],

                    // Node 7: Quotation
                    [
                        'id' => 'node_7',
                        'type' => 'quotation',
                        'name' => 'Teklif Hazırla',
                        'config' => [
                            'include_products' => true,
                            'include_prices' => true,
                            'include_delivery' => true,
                            'template' => 'professional',
                        ],
                        'position' => ['x' => 150, 'y' => 1080],
                    ],

                    // Node 8: Share Contact
                    [
                        'id' => 'node_8',
                        'type' => 'share_contact',
                        'name' => 'İletişim Bilgileri Paylaş',
                        'config' => [
                            'phone' => '+90 850 000 00 00',
                            'email' => 'info@ixtif.com',
                            'website' => 'https://ixtif.com',
                            'message' => 'Ek sorularınız için bizimle iletişime geçebilirsiniz:',
                        ],
                        'position' => ['x' => 150, 'y' => 1260],
                    ],

                    // Node 9: End
                    [
                        'id' => 'node_9',
                        'type' => 'end',
                        'name' => 'Konuşma Sonu',
                        'config' => [
                            'message' => 'İyi günler dilerim!',
                            'save_conversation' => true,
                        ],
                        'position' => ['x' => 150, 'y' => 1440],
                    ],
                ],

                'edges' => [
                    ['id' => 'edge_1_2', 'source' => 'node_1', 'target' => 'node_2'],
                    ['id' => 'edge_2_3', 'source' => 'node_2', 'target' => 'node_3'],
                    ['id' => 'edge_3_4', 'source' => 'node_3', 'target' => 'node_4'],
                    ['id' => 'edge_4_5', 'source' => 'node_4', 'target' => 'node_5', 'label' => 'true'],
                    ['id' => 'edge_4_6', 'source' => 'node_4', 'target' => 'node_6', 'label' => 'false'],
                    ['id' => 'edge_5_6', 'source' => 'node_5', 'target' => 'node_6'],
                    ['id' => 'edge_6_7', 'source' => 'node_6', 'target' => 'node_7'],
                    ['id' => 'edge_7_8', 'source' => 'node_7', 'target' => 'node_8'],
                    ['id' => 'edge_8_9', 'source' => 'node_8', 'target' => 'node_9'],
                ],
            ],
        ];

        // Create or update the flow
        $flow = TenantConversationFlow::updateOrCreate(
            [
                'tenant_id' => $tenantId,
                'flow_name' => $exampleFlow['flow_name'],
            ],
            [
                'flow_description' => $exampleFlow['flow_description'],
                'flow_data' => $exampleFlow['flow_data'],
                'start_node_id' => 'node_1',
                'is_active' => $exampleFlow['is_active'],
                'priority' => $exampleFlow['priority'],
            ]
        );

        $this->command->info("✅ Example flow created: {$flow->flow_name}");
        $this->command->info("   - Nodes: " . count($exampleFlow['flow_data']['nodes']));
        $this->command->info("   - Edges: " . count($exampleFlow['flow_data']['edges']));
        $this->command->info("   - Flow ID: {$flow->id}");
    }
}
