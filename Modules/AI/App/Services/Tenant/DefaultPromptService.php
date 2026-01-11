<?php

namespace Modules\AI\App\Services\Tenant;

use Modules\AI\App\Contracts\TenantPromptServiceInterface;

/**
 * Default Prompt Service
 *
 * Tenant-specific PromptService olmayan tenant'lar için kullanılır.
 * Genel e-ticaret/chatbot kuralları içerir.
 *
 * Yeni tenant eklendiğinde:
 * 1. Bu default service otomatik kullanılır
 * 2. Özelleştirme gerekirse TenantXPromptService.php oluşturulur
 */
class DefaultPromptService implements TenantPromptServiceInterface
{
    /**
     * @inheritDoc
     */
    public function buildPrompt(): array
    {
        $prompts = [];

        // Genel pozitif iletişim kuralları
        $prompts[] = "# GENEL CHATBOT KURALLARI";
        $prompts[] = "";
        $prompts[] = "## 1. POZİTİF İLETİŞİM";
        $prompts[] = "- Her zaman yardımcı ve pozitif ol";
        $prompts[] = "- Müşteriyi asla olumsuz mesajla karşılama";
        $prompts[] = "- Ürün bulunamazsa alternatif öner veya temsilciye yönlendir";
        $prompts[] = "";

        $prompts[] = "## 2. OLUMSUZ KELİME YASAĞI";
        $prompts[] = "❌ ASLA KULLANMA:";
        $prompts[] = "- 'bulunmamaktadır', 'bulunmuyor'";
        $prompts[] = "- 'mevcut değil', 'yok'";
        $prompts[] = "- 'maalesef', 'üzgünüm'";
        $prompts[] = "";
        $prompts[] = "✅ BUNUN YERİNE:";
        $prompts[] = "'Bu konuda size yardımcı olabilirim! 😊 Müşteri temsilcimiz sizinle iletişime geçecek.'";
        $prompts[] = "";

        $prompts[] = "## 3. BİLGİ DOĞRULUĞU";
        $prompts[] = "- Sadece verilen bilgileri kullan";
        $prompts[] = "- FİYAT UYDURMA!";
        $prompts[] = "- ÜRÜN UYDURMA!";
        $prompts[] = "- Bilmediğin konularda temsilciye yönlendir";
        $prompts[] = "";

        $prompts[] = "## 4. MÜŞTERİ TEMSİLCİSİ YÖNLENDİRME";
        $prompts[] = "Şu durumlarda temsilciye yönlendir:";
        $prompts[] = "- Fiyat pazarlığı";
        $prompts[] = "- Özel talepler";
        $prompts[] = "- Teknik detaylar";
        $prompts[] = "- Şikayet/sorun";
        $prompts[] = "";

        return $prompts;
    }

    /**
     * @inheritDoc
     */
    public function getSpecialRules(): string
    {
        return <<<'RULES'
## GENEL KURALLAR:

### POZİTİF İLETİŞİM
- Her yanıt pozitif ve yardımsever olmalı
- Olumsuz kelimeler YASAK (bulunmamaktadır, yok, maalesef)
- Ürün yoksa: "Bu konuda size yardımcı olabilirim! 😊 Temsilcimiz sizinle iletişime geçecek."

### BİLGİ DOĞRULUĞU
- Sadece verilen ürün listesindeki bilgileri kullan
- Fiyat ve ürün UYDURMA
- Emin olmadığın konularda temsilciye yönlendir
RULES;
    }

    /**
     * @inheritDoc
     */
    public function getNoProductMessage(): string
    {
        return "Bu konuda size yardımcı olabilirim! 😊\n\n" .
               "Müşteri temsilcimiz sizinle iletişime geçerek size özel seçenekleri sunacak.\n\n" .
               "Telefon numaranızı paylaşır mısınız? 📱";
    }

    /**
     * @inheritDoc
     */
    public function getContactInfo(): array
    {
        // Default olarak boş - her tenant kendi iletişim bilgisini tanımlar
        // Ya da Settings modülünden çekilebilir
        return [
            'phone' => setting('site_phone') ?? '',
            'email' => setting('site_email') ?? '',
        ];
    }

    /**
     * @inheritDoc
     */
    public function getSector(): string
    {
        return 'general';
    }
}
