<?php

namespace App\Services\ConversationNodes\Common;

use App\Models\AIConversation;
use App\Services\ConversationNodes\AbstractNode;

/**
 * Sentiment Detection Node
 *
 * Detects user intent/sentiment from their message
 * Helps route conversation flow based on user needs
 */
class SentimentDetectionNode extends AbstractNode
{
    public function execute(AIConversation $conversation, string $userMessage): array
    {
        $sentiment = $this->detectSentiment($userMessage);

        // Store sentiment in conversation context
        $conversation->addToContext('user_sentiment', $sentiment);

        $this->log('info', 'Sentiment detected', [
            'conversation_id' => $conversation->id,
            'sentiment' => $sentiment,
            'message_length' => strlen($userMessage),
        ]);

        // Get conditional next nodes based on sentiment
        $nextNode = $this->getNextNodeBySentiment($sentiment);

        return $this->success(
            null,
            [
                'sentiment' => $sentiment,
                'confidence' => 0.8, // TODO: Implement confidence scoring
            ],
            $nextNode
        );
    }

    protected function detectSentiment(string $message): string
    {
        $message = mb_strtolower($message);

        // Purchase Intent
        $purchaseKeywords = ['almak', 'istiyorum', 'satın', 'sipariş', 'fiyat', 'kaç para', 'ne kadar', 'lazım', 'gerek', 'arıyorum', 'bul', 'var mı'];
        foreach ($purchaseKeywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return 'purchase_intent';
            }
        }

        // Comparison
        $comparisonKeywords = ['karşılaştır', 'fark', 'hangisi', 'ile', 'arasında', 'veya'];
        foreach ($comparisonKeywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return 'comparison';
            }
        }

        // Question
        $questionKeywords = ['nasıl', 'nedir', 'neden', 'ne zaman', 'nereden', 'kim'];
        foreach ($questionKeywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return 'question';
            }
        }

        // Support Request
        $supportKeywords = ['yardım', 'sorun', 'çalışmıyor', 'bozuk', 'arıza'];
        foreach ($supportKeywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return 'support_request';
            }
        }

        // Default: browsing
        return 'browsing';
    }

    protected function getNextNodeBySentiment(string $sentiment): ?string
    {
        $sentimentRoutes = $this->getConfig('sentiment_routes', []);

        return $sentimentRoutes[$sentiment] ?? $this->getConfig('default_next_node');
    }

    public function validate(): bool
    {
        return true;
    }

    public static function getType(): string
    {
        return 'sentiment_detection';
    }

    public static function getName(): string
    {
        return 'Niyet Analizi';
    }

    public static function getDescription(): string
    {
        return 'Kullanıcının niyetini tespit eder (satın alma, karşılaştırma, soru, vb.)';
    }

    public static function getConfigSchema(): array
    {
        return [
            'sentiment_routes' => [
                'type' => 'object',
                'label' => 'Niyet Bazlı Yönlendirme',
                'help' => 'Her niyet türü için farklı node\'a yönlendir',
                'properties' => [
                    'purchase_intent' => ['type' => 'node_select', 'label' => 'Satın Alma Niyeti'],
                    'comparison' => ['type' => 'node_select', 'label' => 'Karşılaştırma'],
                    'question' => ['type' => 'node_select', 'label' => 'Soru'],
                    'support_request' => ['type' => 'node_select', 'label' => 'Destek Talebi'],
                    'browsing' => ['type' => 'node_select', 'label' => 'Gezinme'],
                ],
            ],
            'default_next_node' => [
                'type' => 'node_select',
                'label' => 'Varsayılan Sonraki Node',
                'help' => 'Niyet tespit edilemezse',
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
            ['id' => 'purchase_intent', 'label' => 'Satın Alma'],
            ['id' => 'comparison', 'label' => 'Karşılaştırma'],
            ['id' => 'question', 'label' => 'Soru'],
            ['id' => 'support', 'label' => 'Destek'],
            ['id' => 'browsing', 'label' => 'Gezinme'],
        ];
    }

    public static function getCategory(): string
    {
        return 'analysis';
    }

    public static function getIcon(): string
    {
        return 'ti ti-brain';
    }
}
