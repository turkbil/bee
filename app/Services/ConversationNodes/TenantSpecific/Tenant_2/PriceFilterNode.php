<?php

namespace App\Services\ConversationNodes\TenantSpecific\Tenant_2;

use App\Models\AIConversation;
use App\Services\ConversationNodes\AbstractNode;

class PriceFilterNode extends AbstractNode
{
    public function execute(AIConversation $conversation, string $userMessage): array
    {
        // TODO: Implement price filtering logic
        return $this->success("Fiyat filtre mantığı buraya gelecek", [], $this->getConfig('next_node'));
    }

    public function validate(): bool { return true; }
    public static function getType(): string { return 'price_filter'; }
    public static function getName(): string { return 'Fiyat Filtre (İxtif)'; }
    public static function getDescription(): string { return 'Ucuz/pahalı filtreleme'; }
    public static function getConfigSchema(): array { return []; }
    public static function getInputs(): array { return [['id' => 'input_1', 'label' => 'Giriş']]; }
    public static function getOutputs(): array { return [['id' => 'output_1', 'label' => 'Çıkış']]; }
    public static function getCategory(): string { return 'ixtif_ecommerce'; }
    public static function getIcon(): string { return 'ti ti-currency-dollar'; }
}
