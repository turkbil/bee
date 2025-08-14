<?php

declare(strict_types=1);

namespace Modules\AI\Database\Seeders\Universal;

use App\Helpers\TenantHelpers;
use Illuminate\Database\Seeder;
use Modules\AI\app\Models\Prompt;

/**
 * Universal Writing Tone Prompts Seeder
 * 
 * Tüm AI feature'larda kullanılabilir yazım tonu prompt'larını oluşturur.
 * Bu prompt'lar input'lardan seçilebilir ve her feature'da kullanılabilir.
 */
class UniversalWritingTonePromptsSeeder extends Seeder
{
    /**
     * Yazım tonu prompt ID aralığı: 11001-11020
     */
    private const WRITING_TONE_ID_START = 11001;
    
    public function run(): void
    {
        $this->command->info('🎨 Universal Writing Tone Prompts seeding başlıyor...');
        
        TenantHelpers::central(function() {
            $this->clearExistingPrompts();
            $this->createWritingTonePrompts();
        });
        
        $this->command->info('✅ Universal Writing Tone Prompts başarıyla oluşturuldu!');
    }
    
    private function clearExistingPrompts(): void
    {
        Prompt::whereBetween('prompt_id', [self::WRITING_TONE_ID_START, self::WRITING_TONE_ID_START + 19])->delete();
        $this->command->info('🧹 Mevcut writing tone prompts temizlendi');
    }
    
    private function createWritingTonePrompts(): void
    {
        $writingTones = $this->getWritingTonePrompts();
        
        foreach ($writingTones as $index => $tone) {
            $promptId = self::WRITING_TONE_ID_START + $index;
            
            Prompt::create([
                'id' => $promptId,
                'prompt_id' => $promptId,
                'name' => $tone['name'],
                'content' => $tone['content'],
                'prompt_type' => 'writing_tone',
                'prompt_category' => 'system_common',
                'priority' => 2, // Yüksek öncelik
                'ai_weight' => 75,
                'is_system' => true,
                'is_active' => true,
            ]);
            
            $this->command->info("📝 {$tone['name']} yazım tonu oluşturuldu (ID: {$promptId})");
        }
    }
    
    private function getWritingTonePrompts(): array
    {
        return [
            [
                'name' => 'Professional Yazım Tonu',
                'content' => 'Profesyonel ve resmi bir yazım tonu kullan. Kurumsal dil tercih et, saygılı ve ciddi bir yaklaşım sergile. Teknik terimler uygun şekilde kullanılsın. Nezaket ve samimiyet dengesi kur. İş dünyasına uygun, güvenilir ve yetkin bir dil kullan.',
            ],
            [
                'name' => 'Casual (Günlük) Yazım Tonu',
                'content' => 'Günlük ve rahat bir yazım tonu kullan. Doğal, samimi ve yakın bir dil tercih et. Karmaşık kelimeler yerine anlaşılır ifadeler kullan. Okuyucuyla arkadaşça bir bağ kur. Sıcak ve dostane bir yaklaşım sergile.',
            ],
            [
                'name' => 'Friendly (Arkadaşça) Yazım Tonu',
                'content' => 'Arkadaşça ve destekleyici bir yazım tonu kullan. Pozitif ve cesaretlendirici bir dil tercih et. Empatik yaklaşım sergile, okuyucunun yanında olduğunu hissettir. Sıcak, anlayışlı ve motive edici ifadeler kullan.',
            ],
            [
                'name' => 'Technical (Teknik) Yazım Tonu',
                'content' => 'Teknik ve ayrıntılı bir yazım tonu kullan. Uzmanlık gerektiren konularda net ve kesin ifadeler tercih et. Teknik terimleri doğru kullan. Sistematik ve analitik yaklaşım sergile. Bilimsel doğruluk ve hassasiyet göster.',
            ],
            [
                'name' => 'Creative (Yaratıcı) Yazım Tonu',
                'content' => 'Yaratıcı ve ilham verici bir yazım tonu kullan. Özgün ifadeler ve metaforlar kullan. Hayal gücünü harekete geçiren, renkli ve canlı bir dil tercih et. Orijinal bakış açıları sun. Sanatsal ve estetik bir yaklaşım sergile.',
            ],
            [
                'name' => 'Authoritative (Otoriter) Yazım Tonu',
                'content' => 'Otoriter ve güvenilir bir yazım tonu kullan. Uzmanlık ve deneyim yansıtan ifadeler tercih et. Kesin ve net çıkarımlar yap. Liderlik gösteren, ikna edici ve etkileyici bir dil kullan. Güven veren ve inanılır ol.',
            ],
            [
                'name' => 'Empathetic (Empatik) Yazım Tonu',
                'content' => 'Empatik ve anlayışlı bir yazım tonu kullan. Okuyucunun duygularını ve durumunu anlayan ifadeler tercih et. Destekleyici ve teselli edici bir yaklaşım sergile. İnsani değerleri öne çıkar. Şefkatli ve içten bir dil kullan.',
            ],
            [
                'name' => 'Humorous (Esprili) Yazım Tonu',
                'content' => 'Esprili ve eğlenceli bir yazım tonu kullan. Uygun mizah öğeleri ekle, ama saygısız olmadan. Hafif ve keyifli bir okuma deneyimi sun. Pozitif enerji yarat. Zeka ve nükte içeren ifadeler tercih et.',
            ],
        ];
    }
}