<?php

namespace App\Services\ConversationNodes\TenantSpecific\Tenant_2;

use App\Models\AIConversation;
use App\Services\ConversationNodes\AbstractNode;

class CurrencyConvertNode extends AbstractNode
{
    public function execute(AIConversation $conversation, string $userMessage): array
    {
        // TODO: Implement currency conversion from exchange_rates table
        return $this->success("Kur dönüşüm mantığı buraya gelecek (exchange_rates)", [], $this->getConfig('next_node'));
    }

    public function validate(): bool { return true; }
    public static function getType(): string { return 'currency_convert'; }
    public static function getName(): string { return 'Kur Dönüşümü (İxtif)'; }
    public static function getDescription(): string { return 'USD → TL dönüşümü (exchange_rates)'; }
    public static function getConfigSchema(): array { return []; }
    public static function getInputs(): array { return [['id' => 'input_1', 'label' => 'Giriş']]; }
    public static function getOutputs(): array { return [['id' => 'output_1', 'label' => 'Çıkış']]; }
    public static function getCategory(): string { return 'ixtif_ecommerce'; }
    public static function getIcon(): string { return 'ti ti-currency-lira'; }
}
