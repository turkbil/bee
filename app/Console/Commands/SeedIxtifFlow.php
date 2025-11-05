<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TenantConversationFlow;

class SeedIxtifFlow extends Command
{
    protected $signature = 'ai:seed-ixtif-flow';
    protected $description = 'Seed default shop assistant flow for iXtif tenant';

    public function handle()
    {
        $this->info('🔄 Creating default flow for iXtif.com...');

        // Switch to tenant context
        $tenantId = 2; // iXtif

        try {
            // Check if tenant exists
            $tenant = \App\Models\Tenant::find($tenantId);
            if (!$tenant) {
                $this->error('❌ Tenant not found!');
                return Command::FAILURE;
            }

            // Initialize tenancy
            tenancy()->initialize($tenant);
            $this->info('✅ Tenant context initialized: ' . $tenant->id);

            // Create flow
            $flow = TenantConversationFlow::create([
                'tenant_id' => $tenantId,
                'flow_name' => 'Shop Assistant Flow',
                'flow_description' => 'E-ticaret AI asistanı - Ürün arama, fiyat sorguları, kategori tespiti',
                'flow_data' => $this->getFlowData(),
                'start_node_id' => 'node_1',
                'is_active' => true,
                'priority' => 1,
            ]);

            $this->info('✅ Flow created: ID ' . $flow->id);
            $this->info('🎉 Default flow seeded successfully!');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Failed to create flow: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    protected function getFlowData(): array
    {
        return [
            'nodes' => [
                // 1. Welcome Node
                [
                    'id' => 'node_1',
                    'type' => 'welcome',
                    'name' => 'Karşılama',
                    'config' => [
                        'welcome_message' => 'Merhaba! İxtif.com yapay zeka asistanınızım. Size nasıl yardımcı olabilirim?',
                        'show_suggestions' => true,
                        'suggestions' => ['Ürün ara', 'Fiyat bilgisi', 'İletişim'],
                        'next_node' => 'node_2',
                    ],
                    'position' => ['x' => 100, 'y' => 100],
                ],

                // 2. History Loader
                [
                    'id' => 'node_2',
                    'type' => 'history_loader',
                    'name' => 'Geçmiş Yükle',
                    'config' => [
                        'limit' => 10,
                        'order' => 'asc',
                        'include_system_messages' => false,
                        'next_node' => 'node_3',
                    ],
                    'position' => ['x' => 100, 'y' => 200],
                ],

                // 3. Sentiment Detection
                [
                    'id' => 'node_3',
                    'type' => 'sentiment_detection',
                    'name' => 'Niyet Analizi',
                    'config' => [
                        'sentiment_routes' => [
                            'purchase_intent' => 'node_4',
                            'comparison' => 'node_4',
                            'question' => 'node_9',
                            'support_request' => 'node_11',
                            'browsing' => 'node_9',
                        ],
                        'default_next_node' => 'node_9',
                    ],
                    'position' => ['x' => 100, 'y' => 300],
                ],

                // 4. Category Detection
                [
                    'id' => 'node_4',
                    'type' => 'category_detection',
                    'name' => 'Kategori Tespit',
                    'config' => [
                        'category_questions' => [
                            'transpalet' => [
                                ['key' => 'capacity', 'question' => 'Hangi kapasite transpalet arıyorsunuz?', 'options' => ['1.5 ton', '2 ton', '2.5 ton', '3 ton']],
                                ['key' => 'type', 'question' => 'Manuel mi elektrikli mi?', 'options' => ['Manuel', 'Elektrikli']],
                            ],
                            'forklift' => [
                                ['key' => 'capacity', 'question' => 'Hangi kapasite forklift arıyorsunuz?', 'options' => ['2 ton', '3 ton', '5 ton']],
                                ['key' => 'fuel', 'question' => 'Yakıt tipi?', 'options' => ['Dizel', 'Elektrikli', 'LPG']],
                            ],
                        ],
                        'next_node' => 'node_5',
                        'no_category_next_node' => 'node_6',
                    ],
                    'position' => ['x' => 300, 'y' => 400],
                ],

                // 5. Price Query Check
                [
                    'id' => 'node_5',
                    'type' => 'condition',
                    'name' => 'Fiyat Sorgusu mu?',
                    'config' => [
                        'condition' => 'contains_keywords',
                        'keywords' => ['fiyat', 'kaç para', 'ne kadar', 'en ucuz', 'en pahalı'],
                        'true_node' => 'node_6',
                        'false_node' => 'node_7',
                    ],
                    'position' => ['x' => 300, 'y' => 500],
                ],

                // 6. Price Query Node
                [
                    'id' => 'node_6',
                    'type' => 'price_query',
                    'name' => 'Fiyat Sorgusu',
                    'config' => [
                        'exclude_categories' => [44], // Yedek parça
                        'limit' => 5,
                        'show_vat' => false,
                        'vat_rate' => 20,
                        'next_node' => 'node_8',
                        'no_products_next_node' => 'node_11',
                    ],
                    'position' => ['x' => 500, 'y' => 500],
                ],

                // 7. Product Search (Meilisearch)
                [
                    'id' => 'node_7',
                    'type' => 'product_search',
                    'name' => 'Ürün Ara',
                    'config' => [
                        'search_limit' => 3,
                        'use_meilisearch' => true,
                        'sort_by_stock' => true,
                        'next_node' => 'node_8',
                        'no_products_next_node' => 'node_11',
                    ],
                    'position' => ['x' => 500, 'y' => 600],
                ],

                // 8. Stock Sorter
                [
                    'id' => 'node_8',
                    'type' => 'stock_sorter',
                    'name' => 'Stok Sırala',
                    'config' => [
                        'high_stock_threshold' => 10,
                        'exclude_out_of_stock' => false,
                        'next_node' => 'node_9',
                    ],
                    'position' => ['x' => 700, 'y' => 550],
                ],

                // 9. Context Builder
                [
                    'id' => 'node_9',
                    'type' => 'context_builder',
                    'name' => 'Context Hazırla',
                    'config' => [
                        'include_tenant_directives' => true,
                        'include_conversation_history' => true,
                        'history_limit' => 10,
                        'include_conversation_context' => true,
                        'next_node' => 'node_10',
                    ],
                    'position' => ['x' => 900, 'y' => 400],
                ],

                // 10. AI Response
                [
                    'id' => 'node_10',
                    'type' => 'ai_response',
                    'name' => 'AI Cevap Üret',
                    'config' => [
                        'system_prompt' => 'Sen İxtif.com\'un yapay zeka asistanısın. Transpalet ve forklift konusunda uzman satış danışmanısın.\n\nKRİTİK KURALLAR:\n- SADECE veritabanında bulunan ürünlerden bahset\n- ASLA dünyadan örnek verme (Toyota, Nissan vb. YASAK!)\n- ASLA hayali ürün önerme\n- Ürün yoksa müşteri temsilcisine yönlendir\n- Fiyat verirken KDV hariç fiyat ver ve "KDV sonradan eklenir" notu ekle\n- Link verirken [LINK:shop:product:slug] formatını kullan\n- Her zaman profesyonel ve yardımsever ol\n- Kısa ve öz cevap ver (2-3 cümle)',
                        'temperature' => 0.7,
                        'max_tokens' => 500,
                        'next_node' => 'node_12',
                    ],
                    'position' => ['x' => 900, 'y' => 500],
                ],

                // 11. Contact Request (Ürün Yoksa)
                [
                    'id' => 'node_11',
                    'type' => 'contact_request',
                    'name' => 'İletişim Bilgisi Ver',
                    'config' => [
                        'callback_form_url' => '/contact/callback',
                        'next_node' => 'node_10', // AI'ya yönlendir, iletişim bilgilerini context'ten alsın
                    ],
                    'position' => ['x' => 500, 'y' => 700],
                ],

                // 12. Link Generator
                [
                    'id' => 'node_12',
                    'type' => 'link_generator',
                    'name' => 'Linkleri Render Et',
                    'config' => [
                        'base_url' => 'https://ixtif.com',
                        'next_node' => 'node_13',
                    ],
                    'position' => ['x' => 1100, 'y' => 500],
                ],

                // 13. Message Saver
                [
                    'id' => 'node_13',
                    'type' => 'message_saver',
                    'name' => 'Mesajları Kaydet',
                    'config' => [
                        'save_user_message' => true,
                        'save_assistant_message' => true,
                        'save_metadata' => true,
                        'next_node' => 'node_14',
                    ],
                    'position' => ['x' => 1100, 'y' => 600],
                ],

                // 14. End
                [
                    'id' => 'node_14',
                    'type' => 'end',
                    'name' => 'Bitir',
                    'config' => [],
                    'position' => ['x' => 1100, 'y' => 700],
                ],
            ],

            'edges' => [
                ['id' => 'edge_1', 'source' => 'node_1', 'target' => 'node_2'],
                ['id' => 'edge_2', 'source' => 'node_2', 'target' => 'node_3'],
                ['id' => 'edge_3_purchase', 'source' => 'node_3', 'target' => 'node_4', 'sourceOutput' => 'purchase_intent'],
                ['id' => 'edge_3_comparison', 'source' => 'node_3', 'target' => 'node_4', 'sourceOutput' => 'comparison'],
                ['id' => 'edge_3_question', 'source' => 'node_3', 'target' => 'node_9', 'sourceOutput' => 'question'],
                ['id' => 'edge_3_support', 'source' => 'node_3', 'target' => 'node_11', 'sourceOutput' => 'support'],
                ['id' => 'edge_3_browsing', 'source' => 'node_3', 'target' => 'node_9', 'sourceOutput' => 'browsing'],
                ['id' => 'edge_4', 'source' => 'node_4', 'target' => 'node_5'],
                ['id' => 'edge_5_true', 'source' => 'node_5', 'target' => 'node_6'],
                ['id' => 'edge_5_false', 'source' => 'node_5', 'target' => 'node_7'],
                ['id' => 'edge_6', 'source' => 'node_6', 'target' => 'node_8'],
                ['id' => 'edge_7', 'source' => 'node_7', 'target' => 'node_8'],
                ['id' => 'edge_8', 'source' => 'node_8', 'target' => 'node_9'],
                ['id' => 'edge_9', 'source' => 'node_9', 'target' => 'node_10'],
                ['id' => 'edge_10', 'source' => 'node_10', 'target' => 'node_12'],
                ['id' => 'edge_11', 'source' => 'node_11', 'target' => 'node_10'],
                ['id' => 'edge_12', 'source' => 'node_12', 'target' => 'node_13'],
                ['id' => 'edge_13', 'source' => 'node_13', 'target' => 'node_14'],
            ],
        ];
    }
}
