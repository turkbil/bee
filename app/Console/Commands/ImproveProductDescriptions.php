<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Shop\App\Models\ShopProduct;

/**
 * Improve product descriptions with template-based content
 */
class ImproveProductDescriptions extends Command
{
    protected $signature = 'shop:improve-descriptions
                            {--tenant= : Specific tenant ID}
                            {--force : Overwrite all descriptions}
                            {--min-length=100 : Minimum acceptable length}';

    protected $description = 'Improve short/empty product descriptions with template-based content';

    protected int $improved = 0;
    protected int $skipped = 0;

    public function handle()
    {
        $this->info('📝 Product Descriptions Improvement');
        $this->newLine();

        if ($tenantId = $this->option('tenant')) {
            $this->improveTenant($tenantId);
        } else {
            $this->improveAllTenants();
        }

        $this->displaySummary();
        return 0;
    }

    protected function improveAllTenants()
    {
        $tenants = \App\Models\Tenant::all();
        foreach ($tenants as $tenant) {
            tenancy()->initialize($tenant);
            $this->improveTenantDescriptions($tenant->id);
            tenancy()->end();
        }
    }

    protected function improveTenant($tenantId)
    {
        $tenant = \App\Models\Tenant::find($tenantId);
        if (!$tenant) {
            $this->error("Tenant {$tenantId} not found!");
            return;
        }
        tenancy()->initialize($tenant);
        $this->improveTenantDescriptions($tenantId);
        tenancy()->end();
    }

    protected function improveTenantDescriptions($tenantId)
    {
        $minLength = (int)$this->option('min-length');

        $products = ShopProduct::all()->filter(function($product) use ($minLength) {
            if ($this->option('force')) return true;

            $body = is_array($product->body)
                ? ($product->body['tr'] ?? $product->body['en'] ?? '')
                : ($product->body ?? '');

            return mb_strlen(strip_tags($body)) < $minLength;
        });

        $this->info("Found {$products->count()} products to improve");

        $bar = $this->output->createProgressBar($products->count());
        $bar->setFormat('%current%/%max% [%bar%] %percent:3s%% %message%');

        foreach ($products as $product) {
            $description = $this->generateDescription($product);

            $product->body = [
                'tr' => $description,
                'en' => $description,
            ];

            $product->save();

            $this->improved++;
            $bar->setMessage("Improved: {$product->sku}");
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
    }

    protected function generateDescription(ShopProduct $product): string
    {
        $title = is_array($product->title)
            ? ($product->title['tr'] ?? $product->title['en'] ?? '')
            : ($product->title ?? 'Ürün');

        $sku = $product->sku;
        $tags = $product->tags ?? [];
        $specs = $product->technical_specs ?? [];

        // Determine product type from title/tags
        $productType = $this->determineProductType($title, $tags);

        // Build description
        $html = $this->buildHtmlDescription($title, $sku, $productType, $specs, $tags);

        return $html;
    }

    protected function determineProductType(string $title, array $tags): string
    {
        $titleLower = mb_strtolower($title);

        // Check patterns
        if (str_contains($titleLower, 'tekerlek') || str_contains($titleLower, 'teker')) return 'tekerlek';
        if (str_contains($titleLower, 'hidrolik')) return 'hidrolik';
        if (str_contains($titleLower, 'batarya') || str_contains($titleLower, 'akü')) return 'batarya';
        if (str_contains($titleLower, 'motor')) return 'motor';
        if (str_contains($titleLower, 'çatal')) return 'çatal';
        if (str_contains($titleLower, 'fren')) return 'fren';
        if (str_contains($titleLower, 'pompa')) return 'pompa';
        if (str_contains($titleLower, 'silindir')) return 'silindir';
        if (str_contains($titleLower, 'filtre')) return 'filtre';
        if (str_contains($titleLower, 'zincir')) return 'zincir';
        if (str_contains($titleLower, 'rulman')) return 'rulman';
        if (str_contains($titleLower, 'conta')) return 'conta';

        return 'yedek parça';
    }

    protected function buildHtmlDescription(string $title, string $sku, string $type, array $specs, array $tags): string
    {
        $templates = [
            'tekerlek' => [
                'intro' => 'yüksek dayanıklı ve uzun ömürlü bir tekerlek çözümüdür. Forklift, transpalet ve istif makineleri gibi endüstriyel ekipmanlarda kullanılmak üzere tasarlanmıştır.',
                'features' => [
                    'Yüksek kaliteli malzeme yapısı',
                    'Dayanıklı ve uzun ömürlü kullanım',
                    'Sessiz ve titreşimsiz çalışma',
                    'Kolay montaj ve bakım',
                ],
                'usage' => 'Depo, lojistik, üretim tesisleri ve her türlü endüstriyel ortamda kullanılabilir.',
            ],
            'hidrolik' => [
                'intro' => 'endüstriyel ekipmanlarınızın hidrolik sistemleri için özel olarak tasarlanmış bir yedek parçadır. Yüksek performans ve güvenilirlik sunar.',
                'features' => [
                    'Yüksek basınç dayanımı',
                    'Uzun ömürlü kullanım',
                    'Sızdırmazlık garantisi',
                    'Kolay montaj',
                ],
                'usage' => 'Forklift, transpalet ve diğer hidrolik sistemli ekipmanlarda kullanılır.',
            ],
            'batarya' => [
                'intro' => 'elektrikli endüstriyel ekipmanlarınız için güçlü ve dayanıklı bir enerji çözümüdür. Uzun çalışma süresi ve hızlı şarj özellikleri sunar.',
                'features' => [
                    'Yüksek kapasite ve performans',
                    'Uzun ömürlü kullanım',
                    'Hızlı şarj teknolojisi',
                    'Güvenli ve dayanıklı yapı',
                ],
                'usage' => 'Elektrikli forklift, transpalet ve istif makinelerinde kullanılır.',
            ],
            'motor' => [
                'intro' => 'endüstriyel ekipmanlarınızın tahrik sistemleri için güçlü ve verimli bir motor çözümüdür.',
                'features' => [
                    'Yüksek verim ve performans',
                    'Uzun ömürlü kullanım',
                    'Düşük enerji tüketimi',
                    'Sessiz çalışma',
                ],
                'usage' => 'Elektrikli forklift, transpalet ve diğer malzeme taşıma ekipmanlarında kullanılır.',
            ],
            'yedek parça' => [
                'intro' => 'endüstriyel ekipmanlarınız için yüksek kaliteli bir yedek parçadır. Orijinal parça kalitesinde üretilmiştir.',
                'features' => [
                    'Yüksek kalite ve dayanıklılık',
                    'Uzun ömürlü kullanım',
                    'Kolay montaj ve bakım',
                    'Uygun fiyat',
                ],
                'usage' => 'Forklift, transpalet, istif makinesi ve diğer endüstriyel ekipmanlarda kullanılır.',
            ],
        ];

        $template = $templates[$type] ?? $templates['yedek parça'];

        // Build HTML
        $html = '<div class="product-description space-y-6">';

        // Main heading
        $html .= '<h2 class="text-2xl font-bold text-gray-800 mb-4">' . htmlspecialchars($title) . '</h2>';

        // Introduction
        $html .= '<p class="text-gray-700 leading-relaxed">';
        $html .= '<strong class="text-blue-600">İXTİF ' . htmlspecialchars($title) . '</strong>, ';
        $html .= $template['intro'];
        $html .= '</p>';

        // Technical Specs (if available)
        if (!empty($specs)) {
            $html .= '<div class="bg-gray-50 border-l-4 border-blue-500 p-4 my-4">';
            $html .= '<h3 class="text-lg font-semibold text-gray-800 mb-3">Teknik Özellikler</h3>';
            $html .= '<ul class="list-disc list-inside space-y-1 text-gray-700">';

            if (!empty($specs['capacity'])) {
                $html .= '<li>Kapasite: ' . htmlspecialchars($specs['capacity']) . '</li>';
            }
            if (!empty($specs['voltage'])) {
                $html .= '<li>Voltaj: ' . htmlspecialchars($specs['voltage']) . '</li>';
            }
            if (!empty($specs['battery_type'])) {
                $html .= '<li>Batarya Tipi: ' . htmlspecialchars($specs['battery_type']) . '</li>';
            }
            if (!empty($specs['battery_capacity'])) {
                $html .= '<li>Batarya Kapasitesi: ' . htmlspecialchars($specs['battery_capacity']) . '</li>';
            }
            if (!empty($specs['lift_height'])) {
                $html .= '<li>Kaldırma Yüksekliği: ' . htmlspecialchars($specs['lift_height']) . '</li>';
            }
            if (!empty($specs['dimensions'])) {
                $html .= '<li>Boyutlar: ' . htmlspecialchars($specs['dimensions']) . '</li>';
            }
            if (!empty($specs['weight'])) {
                $html .= '<li>Ağırlık: ' . htmlspecialchars($specs['weight']) . '</li>';
            }

            $html .= '<li>Ürün Kodu: ' . htmlspecialchars($sku) . '</li>';
            $html .= '</ul>';
            $html .= '</div>';
        }

        // Features
        $html .= '<div class="my-4">';
        $html .= '<h3 class="text-lg font-semibold text-gray-800 mb-3">Özellikler ve Avantajlar</h3>';
        $html .= '<ul class="list-disc list-inside space-y-2 text-gray-700">';
        foreach ($template['features'] as $feature) {
            $html .= '<li>' . $feature . '</li>';
        }
        $html .= '</ul>';
        $html .= '</div>';

        // Usage Areas
        $html .= '<div class="bg-blue-50 border-l-4 border-blue-400 p-4 my-4">';
        $html .= '<h3 class="text-lg font-semibold text-blue-800 mb-2">Kullanım Alanları</h3>';
        $html .= '<p class="text-blue-700">' . $template['usage'] . '</p>';
        $html .= '</div>';

        // Quality & Brand Info
        $html .= '<div class="mt-6">';
        $html .= '<h3 class="text-lg font-semibold text-gray-800 mb-3">İXTİF Kalite Garantisi</h3>';
        $html .= '<p class="text-gray-700 leading-relaxed">';
        $html .= 'İXTİF olarak, tüm ürünlerimizde yüksek kalite standartlarını uyguluyoruz. ';
        $html .= 'Bu ürün, endüstriyel kullanım için tasarlanmış ve test edilmiştir. ';
        $html .= 'Uzun ömürlü kullanım ve güvenilir performans için İXTİF\'i tercih edin.';
        $html .= '</p>';
        $html .= '</div>';

        // Call to Action
        $html .= '<div class="bg-green-50 border border-green-200 rounded-lg p-4 mt-6">';
        $html .= '<p class="text-green-800 font-medium">';
        $html .= '📞 Fiyat bilgisi ve detaylı teknik destek için bizimle iletişime geçin.';
        $html .= '</p>';
        $html .= '</div>';

        $html .= '</div>';

        return $html;
    }

    protected function displaySummary()
    {
        $this->newLine();
        $this->info('========================================');
        $this->info('📊 DESCRIPTION IMPROVEMENT SUMMARY');
        $this->info('========================================');
        $this->newLine();

        $this->table(
            ['Metric', 'Count'],
            [
                ['✅ Improved', $this->improved],
                ['⏭️  Skipped', $this->skipped],
            ]
        );
    }
}
