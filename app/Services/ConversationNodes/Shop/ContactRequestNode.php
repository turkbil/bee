<?php

namespace App\Services\ConversationNodes\Shop;

use App\Models\AIConversation;
use App\Services\ConversationNodes\AbstractNode;

/**
 * Contact Request Node
 *
 * Handles contact requests and shows contact information from settings
 */
class ContactRequestNode extends AbstractNode
{
    public function execute(AIConversation $conversation, string $userMessage): array
    {
        try {
            // Get contact info from settings
            $contactInfo = $this->getContactInfo();

            // Store in context
            $conversation->addToContext('contact_info', $contactInfo);

            $this->log('info', 'Contact info provided', [
                'conversation_id' => $conversation->id,
            ]);

            $nextNode = $this->getConfig('next_node');

            return $this->success(
                null,
                [
                    'contact_info' => $contactInfo,
                    'callback_form_url' => $this->getConfig('callback_form_url', '/contact/callback'),
                ],
                $nextNode
            );

        } catch (\Exception $e) {
            $this->log('error', 'Contact request failed', [
                'conversation_id' => $conversation->id,
                'error' => $e->getMessage(),
            ]);

            return $this->failure('Contact request failed: ' . $e->getMessage());
        }
    }

    protected function getContactInfo(): array
    {
        try {
            return [
                'phone' => settings()->get('contact_info.phone', ''),
                'whatsapp' => settings()->get('contact_info.whatsapp', ''),
                'email' => settings()->get('contact_info.email', ''),
                'address' => settings()->get('contact_info.address', ''),
                'working_hours' => settings()->get('contact_info.working_hours', 'Pazartesi-Cuma 09:00-18:00'),
            ];
        } catch (\Exception $e) {
            return [
                'phone' => '',
                'whatsapp' => '',
                'email' => '',
                'address' => '',
                'working_hours' => 'Pazartesi-Cuma 09:00-18:00',
            ];
        }
    }

    public function validate(): bool
    {
        return true;
    }

    public static function getType(): string
    {
        return 'contact_request';
    }

    public static function getName(): string
    {
        return 'İletişim İsteği';
    }

    public static function getDescription(): string
    {
        return 'İletişim bilgilerini settings\'ten çeker ve gösterir';
    }

    public static function getConfigSchema(): array
    {
        return [
            'callback_form_url' => [
                'type' => 'text',
                'label' => 'Geri Arama Formu URL',
                'default' => '/contact/callback',
                'help' => '"Sizi arayalım" formu linki',
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
            ['id' => 'output_1', 'label' => 'İletişim Bilgisi Verildi'],
        ];
    }

    public static function getCategory(): string
    {
        return 'shop';
    }

    public static function getIcon(): string
    {
        return 'ti ti-phone';
    }
}
