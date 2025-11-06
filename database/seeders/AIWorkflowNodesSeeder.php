<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AIWorkflowNode;

class AIWorkflowNodesSeeder extends Seeder
{
    public function run(): void
    {
        $nodes = [
            // COMMON NODES - Global (Tüm tenant'lar)
            [
                'node_key' => 'ai_response',
                'node_class' => 'App\\Services\\ConversationNodes\\Common\\AIResponseNode',
                'node_name' => ['en' => 'AI Response', 'tr' => 'AI Yanıtı'],
                'node_description' => ['en' => 'Send instruction to AI', 'tr' => 'AI\'a talimat gönder'],
                'category' => 'common',
                'icon' => 'fa-robot',
                'order' => 1,
                'is_global' => true,
            ],
            [
                'node_key' => 'condition',
                'node_class' => 'App\\Services\\ConversationNodes\\Common\\ConditionNode',
                'node_name' => ['en' => 'Condition', 'tr' => 'Koşul'],
                'node_description' => ['en' => 'If/else logic', 'tr' => 'If/else mantığı'],
                'category' => 'common',
                'icon' => 'fa-code-branch',
                'order' => 2,
                'is_global' => true,
            ],
            [
                'node_key' => 'collect_data',
                'node_class' => 'App\\Services\\ConversationNodes\\Common\\CollectDataNode',
                'node_name' => ['en' => 'Collect Data', 'tr' => 'Veri Topla'],
                'node_description' => ['en' => 'Collect phone/email', 'tr' => 'Telefon/email topla'],
                'category' => 'common',
                'icon' => 'fa-database',
                'order' => 3,
                'is_global' => true,
            ],
            [
                'node_key' => 'share_contact',
                'node_class' => 'App\\Services\\ConversationNodes\\Common\\ShareContactNode',
                'node_name' => ['en' => 'Share Contact', 'tr' => 'İletişim Paylaş'],
                'node_description' => ['en' => 'Share contact info', 'tr' => 'İletişim bilgisi paylaş'],
                'category' => 'communication',
                'icon' => 'fa-address-card',
                'order' => 4,
                'is_global' => true,
            ],
            [
                'node_key' => 'webhook',
                'node_class' => 'App\\Services\\ConversationNodes\\Common\\WebhookNode',
                'node_name' => ['en' => 'Webhook', 'tr' => 'Web Kancası'],
                'node_description' => ['en' => 'Send HTTP request', 'tr' => 'HTTP isteği gönder'],
                'category' => 'communication',
                'icon' => 'fa-plug',
                'order' => 5,
                'is_global' => true,
            ],
            [
                'node_key' => 'end',
                'node_class' => 'App\\Services\\ConversationNodes\\Common\\EndNode',
                'node_name' => ['en' => 'End Conversation', 'tr' => 'Sohbeti Bitir'],
                'node_description' => ['en' => 'End conversation', 'tr' => 'Sohbeti bitir'],
                'category' => 'common',
                'icon' => 'fa-stop-circle',
                'order' => 99,
                'is_global' => true,
            ],

            // SHOP NODES - Global (E-commerce özellikli tüm tenant'lar kullanabilir)
            [
                'node_key' => 'category_detection',
                'node_class' => 'App\\Services\\ConversationNodes\\Shop\\CategoryDetectionNode',
                'node_name' => ['en' => 'Category Detection', 'tr' => 'Kategori Tespiti'],
                'node_description' => ['en' => 'Detect product category', 'tr' => 'Ürün kategorisi tespit et'],
                'category' => 'ecommerce',
                'icon' => 'fa-tags',
                'order' => 10,
                'is_global' => true, // Artık global - tüm shop tenant'ları kullanabilir
            ],
            [
                'node_key' => 'product_search',
                'node_class' => 'App\\Services\\ConversationNodes\\Shop\\ProductSearchNode',
                'node_name' => ['en' => 'Product Search', 'tr' => 'Ürün Arama'],
                'node_description' => ['en' => 'Search products in catalog', 'tr' => 'Katalogda ürün ara'],
                'category' => 'ecommerce',
                'icon' => 'fa-search',
                'order' => 11,
                'is_global' => true,
            ],
            [
                'node_key' => 'price_query',
                'node_class' => 'App\\Services\\ConversationNodes\\Shop\\PriceQueryNode',
                'node_name' => ['en' => 'Price Query', 'tr' => 'Fiyat Sorgula'],
                'node_description' => ['en' => 'Query product prices', 'tr' => 'Ürün fiyatlarını sorgula'],
                'category' => 'ecommerce',
                'icon' => 'fa-dollar-sign',
                'order' => 12,
                'is_global' => true,
            ],
            [
                'node_key' => 'currency_converter',
                'node_class' => 'App\\Services\\ConversationNodes\\Shop\\CurrencyConverterNode',
                'node_name' => ['en' => 'Currency Converter', 'tr' => 'Para Birimi Çevirici'],
                'node_description' => ['en' => 'Convert currencies', 'tr' => 'Para birimlerini çevir'],
                'category' => 'ecommerce',
                'icon' => 'fa-exchange-alt',
                'order' => 13,
                'is_global' => true,
            ],
            [
                'node_key' => 'stock_sorter',
                'node_class' => 'App\\Services\\ConversationNodes\\Shop\\StockSorterNode',
                'node_name' => ['en' => 'Stock Sorter', 'tr' => 'Stok Sıralayıcı'],
                'node_description' => ['en' => 'Sort by stock availability', 'tr' => 'Stok durumuna göre sırala'],
                'category' => 'ecommerce',
                'icon' => 'fa-boxes',
                'order' => 14,
                'is_global' => true,
            ],
            [
                'node_key' => 'product_comparison',
                'node_class' => 'App\\Services\\ConversationNodes\\Shop\\ProductComparisonNode',
                'node_name' => ['en' => 'Product Comparison', 'tr' => 'Ürün Karşılaştırma'],
                'node_description' => ['en' => 'Compare products', 'tr' => 'Ürünleri karşılaştır'],
                'category' => 'ecommerce',
                'icon' => 'fa-balance-scale',
                'order' => 15,
                'is_global' => true,
            ],
            [
                'node_key' => 'contact_request',
                'node_class' => 'App\\Services\\ConversationNodes\\Shop\\ContactRequestNode',
                'node_name' => ['en' => 'Contact Request', 'tr' => 'İletişim Talebi'],
                'node_description' => ['en' => 'Handle contact requests', 'tr' => 'İletişim taleplerini işle'],
                'category' => 'ecommerce',
                'icon' => 'fa-envelope',
                'order' => 16,
                'is_global' => true,
            ],
        ];

        foreach ($nodes as $nodeData) {
            AIWorkflowNode::updateOrCreate(
                ['node_key' => $nodeData['node_key']],
                $nodeData
            );
        }

        $this->command->info('✅ ' . count($nodes) . ' AI Workflow node seeded!');
    }
}
