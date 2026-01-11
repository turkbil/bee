<?php

namespace Modules\AI\App\Services\Workflow\Nodes;

use Illuminate\Support\Facades\Log;

/**
 * Node Factory
 *
 * Generic node oluşturucu
 * Plugin sistemi ile genişletilebilir
 */
class NodeFactory
{
    protected $registry = [];

    public function __construct()
    {
        $this->registerCoreNodes();
        $this->registerTenantPlugins();
    }

    /**
     * Core node'ları kaydet
     */
    protected function registerCoreNodes(): void
    {
        $this->registry = [
            'welcome' => WelcomeNode::class,
            'category_detection' => CategoryDetectionNode::class,
            'product_search' => ProductSearchNode::class,
            'meilisearch_settings' => MeilisearchSettingsNode::class,
            'stock_sorter' => StockSorterNode::class,
            'context_builder' => ContextBuilderNode::class,
            'ai_response' => AIResponseNode::class,
            'message_saver' => MessageSaverNode::class,
            'end' => EndNode::class
        ];
    }

    /**
     * Tenant-specific plugin'leri kaydet
     */
    protected function registerTenantPlugins(): void
    {
        $tenantId = tenant('id');

        // Tenant-specific plugins (auto-discovery)
        $pluginPath = base_path("Modules/AI/app/Services/Workflow/Nodes/Plugins/Tenant{$tenantId}");

        if (is_dir($pluginPath)) {
            $files = glob("{$pluginPath}/*Node.php");

            foreach ($files as $file) {
                $className = basename($file, '.php');
                $nodeType = \Str::snake(str_replace('Node', '', $className));
                $fullClass = "Modules\\AI\\App\\Services\\Workflow\\Nodes\\Plugins\\Tenant{$tenantId}\\{$className}";

                if (class_exists($fullClass)) {
                    $this->registry[$nodeType] = $fullClass;

                    Log::info("🔌 Plugin loaded: {$nodeType}", [
                        'tenant' => $tenantId,
                        'class' => $fullClass
                    ]);
                }
            }
        }
    }

    /**
     * Node instance oluştur
     */
    public function make(string $nodeType, array $config = []): BaseNode
    {
        $class = $this->registry[$nodeType] ?? null;

        if (!$class) {
            throw new \Exception("Unknown node type: {$nodeType}");
        }

        return new $class($config);
    }

    /**
     * Kayıtlı node tiplerini listele
     */
    public function getRegisteredTypes(): array
    {
        return array_keys($this->registry);
    }
}
