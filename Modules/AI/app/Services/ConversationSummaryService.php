<?php

declare(strict_types=1);

namespace Modules\AI\App\Services;

use Modules\AI\App\Models\AIConversation;
use Illuminate\Support\Str;

/**
 * Conversation Summary Service
 *
 * AI konuşmalarının özetini oluşturur
 */
class ConversationSummaryService
{
    /**
     * Konuşmanın özetini oluştur
     */
    public function generateSummary(AIConversation $conversation): string
    {
        $messages = $conversation->messages()
            ->orderBy('created_at', 'asc')
            ->get();

        if ($messages->isEmpty()) {
            return 'Boş konuşma';
        }

        $userMessages = $messages->where('role', 'user');
        $assistantMessages = $messages->where('role', 'assistant');

        $summary = [];

        // Başlık
        $summary[] = "📝 KONUŞMA ÖZETİ";
        $summary[] = str_repeat('-', 50);

        // Genel bilgiler
        $summary[] = "🆔 Konuşma ID: {$conversation->id}";
        $summary[] = "📅 Tarih: {$conversation->created_at->format('d.m.Y H:i')}";
        $summary[] = "💬 Mesaj Sayısı: {$conversation->message_count}";
        $summary[] = "🎯 Özellik: " . ($conversation->feature_name ?: 'Shop Assistant');

        // İlk müşteri mesajı
        $firstUserMessage = $userMessages->first();
        if ($firstUserMessage) {
            $summary[] = "";
            $summary[] = "👤 İlk Müşteri Mesajı:";
            $summary[] = Str::limit($firstUserMessage->content, 150);
        }

        // Son müşteri mesajı (farklıysa)
        $lastUserMessage = $userMessages->last();
        if ($lastUserMessage && $lastUserMessage->id !== $firstUserMessage?->id) {
            $summary[] = "";
            $summary[] = "👤 Son Müşteri Mesajı:";
            $summary[] = Str::limit($lastUserMessage->content, 150);
        }

        // Son AI yanıtı
        $lastAssistantMessage = $assistantMessages->last();
        if ($lastAssistantMessage) {
            $summary[] = "";
            $summary[] = "🤖 Son AI Yanıtı:";
            $summary[] = Str::limit($lastAssistantMessage->content, 150);
        }

        // Telefon numarası tespiti
        $phoneService = new PhoneNumberDetectionService();
        $allPhones = [];

        foreach ($messages as $message) {
            $phones = $phoneService->extractPhoneNumbers($message->content);
            $allPhones = array_merge($allPhones, $phones);
        }

        $allPhones = array_unique($allPhones);

        if (!empty($allPhones)) {
            $summary[] = "";
            $summary[] = "📞 Tespit Edilen Telefon Numaraları:";
            foreach ($allPhones as $phone) {
                $summary[] = "   • " . $phoneService->formatPhoneNumber($phone);
            }
        }

        // Token kullanımı
        $summary[] = "";
        $summary[] = "💰 Token Kullanımı: {$conversation->total_tokens_used} tokens";

        $summary[] = str_repeat('-', 50);

        return implode("\n", $summary);
    }

    /**
     * Admin panel link'i oluştur
     */
    public function generateAdminLink(AIConversation $conversation): string
    {
        $tenantDomain = $conversation->tenant?->domains()->first()?->domain;

        if (!$tenantDomain) {
            $tenantDomain = config('app.url');
        }

        // Protocol ekle (eğer yoksa)
        if (!str_starts_with($tenantDomain, 'http')) {
            $tenantDomain = 'https://' . $tenantDomain;
        }

        return "{$tenantDomain}/admin/ai/conversations/{$conversation->id}";
    }

    /**
     * Telescope için compact özet (tek satır)
     */
    public function generateCompactSummary(AIConversation $conversation): string
    {
        $phoneService = new PhoneNumberDetectionService();
        $messages = $conversation->messages;
        $firstUserMessage = $messages->where('role', 'user')->first();

        $allPhones = [];
        foreach ($messages as $message) {
            $phones = $phoneService->extractPhoneNumbers($message->content);
            $allPhones = array_merge($allPhones, $phones);
        }

        $allPhones = array_unique($allPhones);
        $phonesList = implode(', ', array_map(
            fn($p) => $phoneService->formatPhoneNumber($p),
            $allPhones
        ));

        $firstMessagePreview = $firstUserMessage
            ? Str::limit($firstUserMessage->content, 80)
            : 'N/A';

        return sprintf(
            "Konuşma #%d | %d mesaj | Telefon: %s | İlk mesaj: %s",
            $conversation->id,
            $conversation->message_count,
            $phonesList ?: 'Yok',
            $firstMessagePreview
        );
    }
}
