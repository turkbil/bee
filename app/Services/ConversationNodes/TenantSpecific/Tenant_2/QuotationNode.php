<?php

namespace App\Services\ConversationNodes\TenantSpecific\Tenant_2;

use App\Models\AIConversation;
use App\Services\ConversationNodes\AbstractNode;

class QuotationNode extends AbstractNode
{
    public function execute(AIConversation $conversation, string $userMessage): array
    {
        // TODO: Implement quotation generation logic
        return $this->success("Teklif hazırlama mantığı buraya gelecek", [], $this->getConfig('next_node'));
    }

    public function validate(): bool { return true; }
    public static function getType(): string { return 'quotation'; }
    public static function getName(): string { return 'Teklif Hazırla (İxtif)'; }
    public static function getDescription(): string { return 'Müşteri için teklif oluştur'; }
    public static function getConfigSchema(): array { return []; }
    public static function getInputs(): array { return [['id' => 'input_1', 'label' => 'Giriş']]; }
    public static function getOutputs(): array { return [['id' => 'output_1', 'label' => 'Çıkış']]; }
    public static function getCategory(): string { return 'ixtif_ecommerce'; }
    public static function getIcon(): string { return 'ti ti-file-invoice'; }
}
