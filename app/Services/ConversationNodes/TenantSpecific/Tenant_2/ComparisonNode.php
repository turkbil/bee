<?php

namespace App\Services\ConversationNodes\TenantSpecific\Tenant_2;

use App\Models\AIConversation;
use App\Services\ConversationNodes\AbstractNode;

class ComparisonNode extends AbstractNode
{
    public function execute(AIConversation $conversation, string $userMessage): array
    {
        // TODO: Implement product comparison logic (F4 vs F6)
        return $this->success("Ürün karşılaştırma mantığı buraya gelecek", [], $this->getConfig('next_node'));
    }

    public function validate(): bool { return true; }
    public static function getType(): string { return 'comparison'; }
    public static function getName(): string { return 'Ürün Karşılaştırma (İxtif)'; }
    public static function getDescription(): string { return 'İki ürünü karşılaştır (örn: F4 vs F6)'; }
    public static function getConfigSchema(): array { return []; }
    public static function getInputs(): array { return [['id' => 'input_1', 'label' => 'Giriş']]; }
    public static function getOutputs(): array { return [['id' => 'output_1', 'label' => 'Çıkış']]; }
    public static function getCategory(): string { return 'ixtif_ecommerce'; }
    public static function getIcon(): string { return 'ti ti-arrows-diff'; }
}
