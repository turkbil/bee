<?php

namespace App\Services\ConversationNodes\Common;

use App\Models\AIConversation;
use App\Services\ConversationNodes\AbstractNode;

/**
 * Share Contact Node
 *
 * Shares company contact information with user
 * Retrieves from settings_values table
 */
class ShareContactNode extends AbstractNode
{
    public function execute(AIConversation $conversation, string $userMessage): array
    {
        $contactTypes = $this->getConfig('contact_types', ['phone', 'email']);

        // Get contact information from settings
        $contacts = $this->getContactInfo($conversation->tenant_id, $contactTypes);

        if (empty($contacts)) {
            return $this->failure('No contact information configured');
        }

        // Build prompt with contact information
        $prompt = $this->buildContactPrompt($contacts);

        $this->log('info', 'Contact information shared', [
            'conversation_id' => $conversation->id,
            'contact_types' => $contactTypes,
        ]);

        return $this->success(
            $prompt,
            ['contacts' => $contacts],
            $this->getConfig('next_node')
        );
    }

    protected function getContactInfo(int $tenantId, array $types): array
    {
        $contacts = [];

        // Map of contact types to settings keys
        $settingsMap = [
            'phone' => ['header-phone', 'footer-phone', 'whatsapp_number'],
            'email' => ['header-email', 'footer-email', 'contact_email'],
            'whatsapp' => ['whatsapp_number'],
            'address' => ['footer-address', 'company_address'],
        ];

        foreach ($types as $type) {
            if (!isset($settingsMap[$type])) {
                continue;
            }

            foreach ($settingsMap[$type] as $settingKey) {
                $value = $this->getSettingValue($tenantId, $settingKey);

                if ($value) {
                    $contacts[$type] = $value;
                    break; // Use first found value
                }
            }
        }

        return $contacts;
    }

    protected function getSettingValue(int $tenantId, string $key): ?string
    {
        try {
            $setting = \DB::table('settings_values')
                ->where('tenant_id', $tenantId)
                ->where('key', $key)
                ->where('is_active', 1)
                ->first();

            return $setting->value ?? null;
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get setting value', [
                'key' => $key,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    protected function buildContactPrompt(array $contacts): string
    {
        $lines = ["İletişim bilgilerimiz:"];

        if (isset($contacts['phone'])) {
            $lines[] = "📞 Telefon: {$contacts['phone']}";
        }

        if (isset($contacts['whatsapp'])) {
            $lines[] = "💬 WhatsApp: {$contacts['whatsapp']}";
        }

        if (isset($contacts['email'])) {
            $lines[] = "📧 Email: {$contacts['email']}";
        }

        if (isset($contacts['address'])) {
            $lines[] = "📍 Adres: {$contacts['address']}";
        }

        return implode("\n", $lines);
    }

    public function validate(): bool
    {
        return !empty($this->getConfig('contact_types'));
    }

    public static function getType(): string
    {
        return 'share_contact';
    }

    public static function getName(): string
    {
        return 'İletişim Paylaş';
    }

    public static function getDescription(): string
    {
        return 'Şirket iletişim bilgilerini paylaş';
    }

    public static function getConfigSchema(): array
    {
        return [
            'contact_types' => [
                'type' => 'multiselect',
                'label' => 'İletişim Türleri',
                'options' => [
                    'phone' => 'Telefon',
                    'email' => 'Email',
                    'whatsapp' => 'WhatsApp',
                    'address' => 'Adres',
                ],
                'default' => ['phone', 'email'],
                'required' => true,
            ],
            'next_node' => [
                'type' => 'node_select',
                'label' => 'Sonraki Node',
            ],
        ];
    }

    public static function getInputs(): array
    {
        return [
            ['id' => 'input_1', 'label' => 'Tetikleyici'],
        ];
    }

    public static function getOutputs(): array
    {
        return [
            ['id' => 'output_1', 'label' => 'Paylaşıldı'],
        ];
    }

    public static function getCategory(): string
    {
        return 'action';
    }

    public static function getIcon(): string
    {
        return 'ti ti-phone';
    }
}
