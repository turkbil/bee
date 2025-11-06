# Backend Implementation Guide

**Tarih:** 2025-11-05
**Durum:** 📝 IMPLEMENTATION GUIDE

---

## 📍 DOSYA KONUMLARI

### Context Builder Node
**Dosya:** `/app/Services/ConversationNodes/Common/ContextBuilderNode.php`
**Class:** `App\Services\ConversationNodes\Common\ContextBuilderNode`
**Node Type:** `context_builder`

### Product Search Node
**Dosya:** `/app/Services/ConversationNodes/Shop/ProductSearchNode.php`
**Class:** `App\Services\ConversationNodes\Shop\ProductSearchNode`
**Node Type:** `product_search`

### Price Query Node
**Dosya:** Likely `/app/Services/ConversationNodes/Shop/PriceQueryNode.php`
**Node Type:** `price_query`

---

## 🔧 YAPILACAK DEĞİŞİKLİKLER

### 1. ContextBuilderNode - Settings & Contact Entegrasyonu

**Dosya:** `/app/Services/ConversationNodes/Common/ContextBuilderNode.php`

```php
<?php

namespace App\Services\ConversationNodes\Common;

use App\Models\AIConversation;
use App\Services\ConversationNodes\AbstractNode;
use Modules\Settings\App\Services\SettingService; // YENİ

class ContextBuilderNode extends AbstractNode
{
    public function execute(AIConversation $conversation, string $userMessage): array
    {
        $includeDirectives = $this->getConfig('include_tenant_directives', true);
        $includeHistory = $this->getConfig('include_conversation_history', true);
        $includeContext = $this->getConfig('include_conversation_context', true);

        $contextData = [];

        // 1. Tenant Directives
        if ($includeDirectives) {
            $directives = \App\Models\AITenantDirective::getAllForTenant($conversation->tenant_id);
            $contextData['tenant_directives'] = $directives;
        }

        // 2. Conversation History
        if ($includeHistory) {
            $historyLimit = $this->getConfig('history_limit', 10);
            $history = $this->getConversationHistory($conversation, $historyLimit);
            $contextData['conversation_history'] = $history;
        }

        // 3. Conversation Context Data
        if ($includeContext && !empty($conversation->context_data)) {
            $contextData['conversation_context'] = $conversation->context_data;
        }

        // 4. Brand/Tenant Info
        $contextData['tenant_info'] = [
            'tenant_id' => $conversation->tenant_id,
            'locale' => app()->getLocale(),
        ];

        // ✅ YENİ: 5. Contact Information (Settings'ten)
        $contextData['contact'] = $this->getContactInformation();

        // ✅ YENİ: 6. AI Settings (Settings'ten)
        $contextData['ai_settings'] = $this->getAISettings();

        // ✅ YENİ: 7. Currency formatla (eğer context'te ürün varsa)
        if (!empty($contextData['conversation_context']['products'])) {
            $contextData['conversation_context']['products'] = $this->formatProductPrices(
                $contextData['conversation_context']['products']
            );
        }

        // Get next node
        $nextNode = $this->getConfig('next_node');

        $this->log('info', 'Context builder node executed', [
            'conversation_id' => $conversation->id,
            'context_keys' => array_keys($contextData),
            'history_count' => count($contextData['conversation_history'] ?? []),
            'has_contact' => !empty($contextData['contact']),
            'has_ai_settings' => !empty($contextData['ai_settings']),
        ]);

        return $this->success(
            null,
            $contextData,
            $nextNode
        );
    }

    // ✅ YENİ METOD: Contact bilgileri
    protected function getContactInformation(): array
    {
        try {
            $settingService = app(SettingService::class);

            $whatsapp = $settingService->get('contact_whatsapp_1');
            $phone = $settingService->get('contact_phone_1');
            $email = $settingService->get('contact_email_1');

            return [
                'whatsapp' => $whatsapp,
                'whatsapp_link' => $whatsapp ? $this->generateWhatsAppLink($whatsapp) : null,
                'phone' => $phone,
                'email' => $email,
            ];
        } catch (\Exception $e) {
            $this->log('warning', 'Failed to load contact information', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    // ✅ YENİ METOD: AI Settings
    protected function getAISettings(): array
    {
        try {
            $settingService = app(SettingService::class);

            return [
                'assistant_name' => $settingService->get('ai_assistant_name', 'AI Asistan'),
                'response_tone' => $settingService->get('ai_response_tone', 'friendly'),
                'use_emojis' => $settingService->get('ai_use_emojis', 'moderate'),
                'response_length' => $settingService->get('ai_response_length', 'medium'),
                'sales_approach' => $settingService->get('ai_sales_approach', 'consultative'),
            ];
        } catch (\Exception $e) {
            $this->log('warning', 'Failed to load AI settings', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    // ✅ YENİ METOD: WhatsApp link oluştur
    protected function generateWhatsAppLink(string $phoneNumber): string
    {
        // Format: 0534 515 26 26 → 905345152626
        $clean = preg_replace('/[^0-9]/', '', $phoneNumber);

        // Başında 0 varsa 90 ile değiştir
        if (substr($clean, 0, 1) === '0') {
            $clean = '90' . substr($clean, 1);
        }

        return "https://wa.me/{$clean}";
    }

    // ✅ YENİ METOD: Ürün fiyatlarını formatla
    protected function formatProductPrices(array $products): array
    {
        return array_map(function($product) {
            // Eğer zaten formatted_price varsa, değiştirme
            if (isset($product['formatted_price'])) {
                return $product;
            }

            // Currency bilgisi varsa formatla
            if (isset($product['base_price']) && isset($product['currency'])) {
                try {
                    $currency = \Modules\Shop\App\Models\ShopCurrency::where('code', $product['currency'])->first();

                    if ($currency) {
                        $product['currency_symbol'] = $currency->symbol;
                        $product['currency_format'] = $currency->format;
                        $product['decimal_places'] = $currency->decimal_places;
                        $product['formatted_price'] = $this->formatPrice($product['base_price'], $currency);
                    }
                } catch (\Exception $e) {
                    $this->log('warning', 'Failed to format product price', [
                        'product_id' => $product['id'] ?? null,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            return $product;
        }, $products);
    }

    // ✅ YENİ METOD: Fiyat formatlama
    protected function formatPrice(float $price, $currency): string
    {
        $formatted = number_format(
            $price,
            $currency->decimal_places ?? 0,
            ',',
            '.'
        );

        if ($currency->format === 'symbol_before') {
            return $currency->symbol . $formatted;
        }

        return $formatted . ' ' . $currency->symbol;
    }

    // Mevcut metod...
    protected function getConversationHistory(AIConversation $conversation, int $limit): array
    {
        try {
            return $conversation->messages()
                ->orderBy('created_at', 'asc')
                ->limit($limit)
                ->get()
                ->map(fn($msg) => [
                    'role' => $msg->role,
                    'content' => $msg->content,
                    'created_at' => $msg->created_at->toIso8601String(),
                ])
                ->toArray();
        } catch (\Exception $e) {
            $this->log('warning', 'Failed to load conversation history', [
                'conversation_id' => $conversation->id,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    // Diğer metodlar aynı kalacak...
}
```

---

### 2. ProductSearchNode - Currency Bilgisi Ekleme

**Dosya:** `/app/Services/ConversationNodes/Shop/ProductSearchNode.php`

```php
// execute() metodunda, ürün sonuçlarını hazırlarken:

protected function formatProductResults(Collection $products): array
{
    return $products->map(function ($product) {
        // Currency bilgisini ekle
        $currency = \Modules\Shop\App\Models\ShopCurrency::where('code', $product->currency)->first();

        return [
            'id' => $product->id,
            'title' => $product->getTranslated('title', app()->getLocale()),
            'slug' => $product->getTranslated('slug', app()->getLocale()),
            'base_price' => $product->base_price,
            'currency' => $product->currency,
            'currency_symbol' => $currency->symbol ?? '₺',
            'currency_format' => $currency->format ?? 'symbol_after',
            'decimal_places' => $currency->decimal_places ?? 0,
            // formatted_price ContextBuilder'da eklenecek
            'stock_quantity' => $product->stock_quantity,
            'category_id' => $product->category_id,
            'category_title' => $product->category?->getTranslated('title', app()->getLocale()),
        ];
    })->toArray();
}
```

---

### 3. PriceQueryNode - Currency Bilgisi Ekleme

**Dosya:** `/app/Services/ConversationNodes/Shop/PriceQueryNode.php`

Aynı currency bilgisi ekleme mantığını uygula:

```php
protected function formatPriceResults(Collection $products): array
{
    return $products->map(function ($product) {
        $currency = \Modules\Shop\App\Models\ShopCurrency::where('code', $product->currency)->first();

        return [
            'id' => $product->id,
            'title' => $product->getTranslated('title', app()->getLocale()),
            'slug' => $product->getTranslated('slug', app()->getLocale()),
            'base_price' => $product->base_price,
            'currency' => $product->currency,
            'currency_symbol' => $currency->symbol ?? '₺',
            'currency_format' => $currency->format ?? 'symbol_after',
            'decimal_places' => $currency->decimal_places ?? 0,
            // formatted_price ContextBuilder'da eklenecek
        ];
    })->toArray();
}
```

---

## 📊 CONTEXT DATA YAPISI (Güncellenmiş)

```php
$contextData = [
    // Mevcut...
    'tenant_directives' => [...],
    'conversation_history' => [...],
    'conversation_context' => [
        'products' => [
            [
                'id' => 123,
                'title' => 'İXTİF EPT20 - 2 Ton Elektrikli Transpalet',
                'slug' => 'ixtif-ept20-2-ton',
                'base_price' => 15000,
                'currency' => 'TRY',
                'currency_symbol' => '₺',
                'currency_format' => 'symbol_after',
                'decimal_places' => 0,
                'formatted_price' => '15.000 ₺', // ✅ ContextBuilder ekler
            ],
        ],
    ],
    'tenant_info' => [...],

    // ✅ YENİ: Contact bilgileri
    'contact' => [
        'whatsapp' => '0501 005 67 58',
        'whatsapp_link' => 'https://wa.me/905010056758',
        'phone' => '0216 755 3 555',
        'email' => 'info@ixtif.com',
    ],

    // ✅ YENİ: AI Settings
    'ai_settings' => [
        'assistant_name' => 'İxtif Asistan',
        'response_tone' => 'enthusiastic',
        'use_emojis' => 'high',
        'response_length' => 'short',
        'sales_approach' => 'aggressive',
    ],
];
```

---

## 🧪 TEST PLANI

### 1. Unit Test: formatPrice()

```php
// Test TRY currency (symbol_after)
$currency = ShopCurrency::where('code', 'TRY')->first();
$formatted = $node->formatPrice(15000, $currency);
// Expected: "15.000 ₺"

// Test USD currency (symbol_before)
$currency = ShopCurrency::where('code', 'USD')->first();
$formatted = $node->formatPrice(1350, $currency);
// Expected: "$1,350"
```

### 2. Integration Test: Settings

```bash
# Test settings çekme
php artisan tinker
>>> app(SettingService::class)->get('contact_whatsapp_1');
// Expected: "0501 005 67 58" (İxtif için)

>>> app(SettingService::class)->get('ai_assistant_name');
// Expected: "İxtif Asistan" veya default
```

### 3. Flow Test: End-to-End

```bash
# Frontend'den mesaj gönder
"transpalet arıyorum"

# Log kontrol et
tail -f storage/logs/laravel.log | grep "Context builder"

# Beklenilen:
# - contact bilgileri yüklendi
# - ai_settings yüklendi
# - ürün fiyatları formatted_price ile geldi
```

---

## ⚠️ ÖNEMLİ NOTLAR

### 1. Settings Service Dependency

```php
use Modules\Settings\App\Services\SettingService;
```

Eğer `SettingService` farklı namespace'te ise düzelt.

### 2. ShopCurrency Model

```php
use Modules\Shop\App\Models\ShopCurrency;
```

Eğer farklı namespace'te ise düzelt.

### 3. Error Handling

Tüm yeni metodlar `try-catch` ile korumalı:
- Settings yüklenememesi
- Currency bulunamama
- Format hatası

Log'la ama sistemi durdurma!

### 4. Performance

- Currency bilgisi her ürün için ayrı sorgu yapmamalı
- Önce tüm unique currency code'ları topla
- Tek sorguda tüm currency'leri çek
- Cache'le

**Optimize edilmiş:**

```php
protected function formatProductPrices(array $products): array
{
    // Unique currency code'ları topla
    $currencyCodes = array_unique(array_column($products, 'currency'));

    // Tek sorguda tüm currency'leri çek
    $currencies = \Modules\Shop\App\Models\ShopCurrency::whereIn('code', $currencyCodes)
        ->get()
        ->keyBy('code');

    // Her ürünü formatla
    return array_map(function($product) use ($currencies) {
        if (isset($product['currency']) && isset($currencies[$product['currency']])) {
            $currency = $currencies[$product['currency']];
            $product['formatted_price'] = $this->formatPrice($product['base_price'], $currency);
            $product['currency_symbol'] = $currency->symbol;
            $product['currency_format'] = $currency->format;
            $product['decimal_places'] = $currency->decimal_places;
        }
        return $product;
    }, $products);
}
```

---

## ✅ CHECKLIST

**ContextBuilderNode:**
- [ ] `getContactInformation()` metodu ekle
- [ ] `getAISettings()` metodu ekle
- [ ] `generateWhatsAppLink()` metodu ekle
- [ ] `formatProductPrices()` metodu ekle
- [ ] `formatPrice()` metodu ekle
- [ ] `execute()` metodunu güncelle
- [ ] Import'ları ekle (SettingService, ShopCurrency)

**ProductSearchNode:**
- [ ] `formatProductResults()` metodunu güncelle
- [ ] Currency bilgisi ekle
- [ ] Import'ları ekle (ShopCurrency)

**PriceQueryNode:**
- [ ] `formatPriceResults()` metodunu güncelle
- [ ] Currency bilgisi ekle
- [ ] Import'ları ekle (ShopCurrency)

**Test:**
- [ ] formatPrice() unit test
- [ ] Settings entegrasyonu test
- [ ] Currency formatı test (TRY, USD, EUR)
- [ ] Frontend end-to-end test
- [ ] Log kontrol

**Performance:**
- [ ] Currency sorguları optimize et (N+1 problemi önle)
- [ ] Settings cache'lenmiş mi kontrol et

---

## 🎯 SONUÇ

Bu değişikliklerden sonra:

✅ Fiyatlar dinamik formatlanacak (`shop_currencies`)
✅ Contact bilgileri settings'ten gelecek (`contact_whatsapp_1`, vb.)
✅ AI kişiliği settings'ten gelecek (`ai_assistant_name`, vb.)
✅ Hallüsinasyon riski düşecek (placeholder kullanımı)
✅ Yeni tenant ekleme kolaylaşacak (settings değiştir, flow kopyala)

**Tüm detaylar bu dokümanda!** 🚀
