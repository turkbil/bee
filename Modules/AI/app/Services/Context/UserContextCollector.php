<?php

declare(strict_types=1);

namespace Modules\AI\App\Services\Context;

use Illuminate\Support\Facades\Log;

/**
 * User Context Collector
 * Kullanıcı bilgilerini toplar ve context oluşturur
 */
class UserContextCollector extends ContextCollector
{
    public function __construct()
    {
        parent::__construct('user');
    }

    /**
     * Kullanıcı context'ini topla
     */
    protected function collectContext(array $options): array
    {
        $user = auth()->user();
        $context = [];

        if (!$user) {
            return [
                'type' => 'guest',
                'has_user' => false,
                'context_text' => '👤 ANONYMOUS MODE: Misafir kullanıcı ile iletişim.',
                'priority' => 5
            ];
        }

        // Temel kullanıcı bilgileri
        $context = [
            'type' => 'authenticated',
            'has_user' => true,
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'is_admin' => $user->hasRole('admin') ?? false,
            'created_at' => $user->created_at,
            'priority' => 2 // High priority for user context
        ];
        
        // Kullanıcı hesap yaşı hesapla
        try {
            if (is_string($user->created_at)) {
                $createdAt = \Carbon\Carbon::parse($user->created_at);
            } else {
                $createdAt = $user->created_at;
            }
            $context['account_age_days'] = $createdAt->diffInDays(now());
            $context['is_new_user'] = $context['account_age_days'] <= 30;
        } catch (\Exception $e) {
            $context['account_age_days'] = 0;
            $context['is_new_user'] = true;
        }

        // Kullanıcı preferences (eğer varsa)
        if (method_exists($user, 'preferences')) {
            $context['preferences'] = $user->preferences ?? [];
        }

        // Son aktivite bilgisi
        if ($user->last_login_at ?? null) {
            $context['last_login'] = $user->last_login_at;
            
            // Carbon instance kontrolü
            try {
                if (is_string($user->last_login_at)) {
                    $lastLogin = \Carbon\Carbon::parse($user->last_login_at);
                } else {
                    $lastLogin = $user->last_login_at;
                }
                $context['is_frequent_user'] = $lastLogin->diffInDays(now()) <= 7;
            } catch (\Exception $e) {
                $context['is_frequent_user'] = false;
            }
        }

        // AI için context metni oluştur
        $context['context_text'] = $this->buildContextText($context);

        Log::debug('User context collected', [
            'user_id' => $user->id,
            'name' => $user->name,
            'context_size' => strlen($context['context_text'])
        ]);

        return $context;
    }

    /**
     * AI için okunabilir context metni oluştur
     */
    private function buildContextText(array $context): string
    {
        if (!$context['has_user']) {
            return '👤 ANONYMOUS MODE: Misafir kullanıcı ile iletişim.';
        }

        $parts = [];
        
        // Temel kullanıcı tanıtımı
        $parts[] = "👤 USER INFO: Konuşan kişi {$context['name']}.";
        
        // Admin durumu
        if ($context['is_admin']) {
            $parts[] = "🔑 ADMIN USER: Yönetici yetkilerine sahip.";
        }

        // Kullanım sıklığı ve hesap durumu
        if (isset($context['is_frequent_user']) && $context['is_frequent_user']) {
            $parts[] = "⭐ FREQUENT USER: Aktif kullanıcı, kişisel yaklaşım uygula.";
        }
        
        if (isset($context['is_new_user']) && $context['is_new_user']) {
            $parts[] = "🆕 NEW USER: Yeni kullanıcı, rehberlik ve açıklama odaklı yaklaş.";
        }

        // Özel talimatlar  
        $parts[] = "💬 CHAT GUIDELINE: Samimi, dostça ton benimse ancak kendi kimliğini koru.";
        $parts[] = "🎯 RESPONSE RULE: Kullanıcıya hitap ederken '{$context['name']}' ismini kullan, ama sen AI modelisin.";

        return implode("\n", $parts);
    }

    /**
     * Context priority hesaplama (override)
     */
    protected function calculatePriority(array $context): int
    {
        if (!$context['has_user']) {
            return 5; // Lowest priority for anonymous users
        }

        $priority = 2; // High priority for authenticated users

        // Admin users get highest priority
        if ($context['is_admin']) {
            $priority = 1;
        }

        // Frequent users get boost
        if (isset($context['is_frequent_user']) && $context['is_frequent_user']) {
            $priority = max(1, $priority - 1);
        }

        return $priority;
    }

    /**
     * Context validation (override)
     */
    protected function validateContext(array $context): bool
    {
        // User context is always valid (guest or authenticated)
        return isset($context['type']) && in_array($context['type'], ['guest', 'authenticated']);
    }

    /**
     * Kullanıcı tercihlerini güncelle
     */
    public function updateUserPreferences(array $preferences): void
    {
        $user = auth()->user();
        if (!$user) {
            return;
        }

        // Cache'i temizle çünkü preferences değişti
        $this->clearCache();

        // Preferences güncellemesi yapılabilir
        if (method_exists($user, 'updatePreferences')) {
            $user->updatePreferences($preferences);
        }

        Log::info('User preferences updated', [
            'user_id' => $user->id,
            'preferences' => $preferences
        ]);
    }

    /**
     * Kullanıcının AI kullanım geçmişi analizi
     */
    public function analyzeUsageHistory(): array
    {
        $user = auth()->user();
        if (!$user) {
            return ['type' => 'no_history'];
        }

        // AI kullanım geçmişi analizi burada yapılabilir
        // Şimdilik basit bir analiz
        return [
            'type' => 'basic_analysis',
            'user_id' => $user->id,
            'analysis_date' => now(),
            'recommendation' => 'standard_approach'
        ];
    }
}