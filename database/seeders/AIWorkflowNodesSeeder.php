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

            // İXTİF.COM ÖZEL NODES - Tenant_2
            [
                'node_key' => 'category_detection',
                'node_class' => 'App\\Services\\ConversationNodes\\TenantSpecific\\Tenant_2\\CategoryDetectionNode',
                'node_name' => ['en' => 'Category Detection', 'tr' => 'Kategori Tespiti'],
                'node_description' => ['en' => 'Detect product category (transpalet/forklift)', 'tr' => 'Ürün kategorisi tespit et (transpalet/forklift)'],
                'category' => 'ecommerce',
                'icon' => 'fa-tags',
                'order' => 10,
                'is_global' => false,
                'tenant_whitelist' => [2], // İxtif.com
            ],
            [
                'node_key' => 'product_recommendation',
                'node_class' => 'App\\Services\\ConversationNodes\\TenantSpecific\\Tenant_2\\ProductRecommendationNode',
                'node_name' => ['en' => 'Product Recommendation', 'tr' => 'Ürün Önerme'],
                'node_description' => ['en' => 'Recommend products (homepage+stock priority)', 'tr' => 'Ürün öner (anasayfa+stok öncelikli)'],
                'category' => 'ecommerce',
                'icon' => 'fa-shopping-cart',
                'order' => 11,
                'is_global' => false,
                'tenant_whitelist' => [2],
            ],
            [
                'node_key' => 'price_filter',
                'node_class' => 'App\\Services\\ConversationNodes\\TenantSpecific\\Tenant_2\\PriceFilterNode',
                'node_name' => ['en' => 'Price Filter', 'tr' => 'Fiyat Filtresi'],
                'node_description' => ['en' => 'Filter by price (cheap/expensive)', 'tr' => 'Fiyata göre filtrele (ucuz/pahalı)'],
                'category' => 'ecommerce',
                'icon' => 'fa-filter',
                'order' => 12,
                'is_global' => false,
                'tenant_whitelist' => [2],
            ],
            [
                'node_key' => 'currency_convert',
                'node_class' => 'App\\Services\\ConversationNodes\\TenantSpecific\\Tenant_2\\CurrencyConvertNode',
                'node_name' => ['en' => 'Currency Convert', 'tr' => 'Para Birimi Çevir'],
                'node_description' => ['en' => 'Convert USD to TL', 'tr' => 'USD\'yi TL\'ye çevir'],
                'category' => 'ecommerce',
                'icon' => 'fa-dollar-sign',
                'order' => 13,
                'is_global' => false,
                'tenant_whitelist' => [2],
            ],
            [
                'node_key' => 'stock_check',
                'node_class' => 'App\\Services\\ConversationNodes\\TenantSpecific\\Tenant_2\\StockCheckNode',
                'node_name' => ['en' => 'Stock Check', 'tr' => 'Stok Kontrolü'],
                'node_description' => ['en' => 'Check stock availability', 'tr' => 'Stok durumunu kontrol et'],
                'category' => 'ecommerce',
                'icon' => 'fa-boxes',
                'order' => 14,
                'is_global' => false,
                'tenant_whitelist' => [2],
            ],
            [
                'node_key' => 'comparison',
                'node_class' => 'App\\Services\\ConversationNodes\\TenantSpecific\\Tenant_2\\ComparisonNode',
                'node_name' => ['en' => 'Product Comparison', 'tr' => 'Ürün Karşılaştırma'],
                'node_description' => ['en' => 'Compare products', 'tr' => 'Ürünleri karşılaştır'],
                'category' => 'ecommerce',
                'icon' => 'fa-balance-scale',
                'order' => 15,
                'is_global' => false,
                'tenant_whitelist' => [2],
            ],
            [
                'node_key' => 'quotation',
                'node_class' => 'App\\Services\\ConversationNodes\\TenantSpecific\\Tenant_2\\QuotationNode',
                'node_name' => ['en' => 'Quotation', 'tr' => 'Teklif Hazırla'],
                'node_description' => ['en' => 'Prepare quotation', 'tr' => 'Teklif hazırla'],
                'category' => 'ecommerce',
                'icon' => 'fa-file-invoice-dollar',
                'order' => 16,
                'is_global' => false,
                'tenant_whitelist' => [2],
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
