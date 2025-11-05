<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedShopNodes extends Command
{
    protected $signature = 'ai:seed-shop-nodes';
    protected $description = 'Seed shop module nodes to central database';

    public function handle()
    {
        $this->info('🛒 Seeding shop module nodes...');

        $nodes = [
            [
                'node_key' => 'category_detection',
                'node_class' => 'App\\Services\\ConversationNodes\\Shop\\CategoryDetectionNode',
                'node_name' => json_encode(['tr' => 'Kategori Tespit', 'en' => 'Category Detection']),
                'node_description' => json_encode(['tr' => 'Kategori tespit ve özel sorular', 'en' => 'Detect category and ask questions']),
                'category' => 'shop',
                'icon' => 'ti ti-category',
                'order' => 1,
                'is_global' => false,
                'tenant_whitelist' => json_encode([2, 3]),
                'is_active' => true,
            ],
            [
                'node_key' => 'product_search',
                'node_class' => 'App\\Services\\ConversationNodes\\Shop\\ProductSearchNode',
                'node_name' => json_encode(['tr' => 'Ürün Ara', 'en' => 'Product Search']),
                'node_description' => json_encode(['tr' => 'Meilisearch/DB ile ürün arama (HALÜSİNASYON YASAK)', 'en' => 'Product search with Meilisearch/DB']),
                'category' => 'shop',
                'icon' => 'ti ti-search',
                'order' => 2,
                'is_global' => false,
                'tenant_whitelist' => json_encode([2, 3]), // iXtif tenants
                'is_active' => true,
            ],
            [
                'node_key' => 'price_query',
                'node_class' => 'App\\Services\\ConversationNodes\\Shop\\PriceQueryNode',
                'node_name' => json_encode(['tr' => 'Fiyat Sorgusu', 'en' => 'Price Query']),
                'node_description' => json_encode(['tr' => 'Fiyat bazlı sorgular (en ucuz, en pahalı)', 'en' => 'Price-based queries']),
                'category' => 'shop',
                'icon' => 'ti ti-currency-lira',
                'order' => 3,
                'is_global' => false,
                'tenant_whitelist' => json_encode([2, 3]),
                'is_active' => true,
            ],
            [
                'node_key' => 'category_detection',
                'node_class' => 'App\\Services\\ConversationNodes\\Shop\\CategoryDetectionNode',
                'node_name' => json_encode(['tr' => 'Kategori Tespit', 'en' => 'Category Detection']),
                'node_description' => json_encode(['tr' => 'Kategori tespit ve özel sorular', 'en' => 'Detect category and ask questions']),
                'category' => 'shop',
                'icon' => 'ti ti-category',
                'order' => 3,
                'is_global' => false,
                'tenant_whitelist' => json_encode([2, 3]),
                'is_active' => true,
            ],
            [
                'node_key' => 'currency_converter',
                'node_class' => 'App\\Services\\ConversationNodes\\Shop\\CurrencyConverterNode',
                'node_name' => json_encode(['tr' => 'Döviz Çevirici', 'en' => 'Currency Converter']),
                'node_description' => json_encode(['tr' => 'TL/USD/EUR çevirici (tenant kuru)', 'en' => 'Convert prices to USD/EUR']),
                'category' => 'shop',
                'icon' => 'ti ti-currency-dollar',
                'order' => 4,
                'is_global' => false,
                'tenant_whitelist' => json_encode([2, 3]),
                'is_active' => true,
            ],
            [
                'node_key' => 'product_comparison',
                'node_class' => 'App\\Services\\ConversationNodes\\Shop\\ProductComparisonNode',
                'node_name' => json_encode(['tr' => 'Ürün Karşılaştır', 'en' => 'Product Comparison']),
                'node_description' => json_encode(['tr' => 'İki ürünü karşılaştır ve farkları göster', 'en' => 'Compare two products']),
                'category' => 'shop',
                'icon' => 'ti ti-arrows-left-right',
                'order' => 5,
                'is_global' => false,
                'tenant_whitelist' => json_encode([2, 3]),
                'is_active' => true,
            ],
            [
                'node_key' => 'contact_request',
                'node_class' => 'App\\Services\\ConversationNodes\\Shop\\ContactRequestNode',
                'node_name' => json_encode(['tr' => 'İletişim İsteği', 'en' => 'Contact Request']),
                'node_description' => json_encode(['tr' => 'İletişim bilgilerini settings\'ten göster', 'en' => 'Show contact information']),
                'category' => 'shop',
                'icon' => 'ti ti-phone',
                'order' => 6,
                'is_global' => false,
                'tenant_whitelist' => json_encode([2, 3]),
                'is_active' => true,
            ],
            [
                'node_key' => 'stock_sorter',
                'node_class' => 'App\\Services\\ConversationNodes\\Shop\\StockSorterNode',
                'node_name' => json_encode(['tr' => 'Stok Sırala', 'en' => 'Stock Sorter']),
                'node_description' => json_encode(['tr' => 'Stok sıralaması (Featured → Yüksek Stok → Normal)', 'en' => 'Sort by stock priority']),
                'category' => 'shop',
                'icon' => 'ti ti-sort-ascending-numbers',
                'order' => 7,
                'is_global' => false,
                'tenant_whitelist' => json_encode([2, 3]),
                'is_active' => true,
            ],
        ];

        DB::connection('mysql')->table('ai_workflow_nodes')->insert($nodes);

        $this->info('✅ Seeded ' . count($nodes) . ' shop module nodes');
        $this->info('🎉 Shop nodes seeded successfully!');

        return Command::SUCCESS;
    }
}
