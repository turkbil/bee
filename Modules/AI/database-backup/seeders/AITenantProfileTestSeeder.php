<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\App\Models\AITenantProfile;
use Carbon\Carbon;

class AITenantProfileTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🎯 Test AI Tenant Profile oluşturuluyor...');

        // Mevcut profil varsa güncelle, yoksa oluştur
        $profile = AITenantProfile::updateOrCreate(
            ['tenant_id' => 1],
            [
                'company_info' => [
                    'city' => 'İstanbul',
                    'brand_name' => 'Türk Bilişim',
                    'main_service' => 'Web Tasarım, Sosyal Medya, İnternet Reklamcılığı ve Grafik Tasarım',
                    'founder_experience' => [
                        'tech' => true,
                        'design' => true,
                        'business' => true,
                        'marketing' => true,
                        'consulting' => true,
                        'management' => true
                    ],
                    'founder_permission' => 'yes_full'
                ],
                'sector_details' => [
                    'sector' => 'technology',
                    'branches' => 'hybrid',
                    'brand_age' => 'custom',
                    'company_size' => 'small',
                    'project_types' => [
                        'web-apps' => true,
                        'mobile-apps' => true
                    ],
                    'tech_services' => [
                        'e-ticaret' => true,
                        'web-tasarim' => true,
                        'mobil-uygulama' => true,
                        'yazilim-gelistirme' => true
                    ],
                    'market_position' => 'premium',
                    'target_audience' => [
                        'b2b-large' => true,
                        'b2b-small' => true,
                        'b2b-medium' => true
                    ],
                    'brand_age_custom' => '1998 den beri',
                    'brand_personality' => [
                        'friendly' => true
                    ]
                ],
                'success_stories' => [
                    'brand_voice' => 'advisor',
                    'avoid_topics' => [
                        'controversy' => true
                    ],
                    'writing_tone' => [
                        'formal' => true
                    ],
                    'content_focus' => 'result',
                    'emphasis_points' => [
                        'quality' => true
                    ]
                ],
                'ai_behavior_rules' => [
                    'writing_tone' => [
                        'professional' => true,
                        'friendly' => true
                    ],
                    'communication_style' => [
                        'consultative' => true,
                        'solution-focused' => true
                    ],
                    'brand_voice' => 'trustworthy',
                    'content_approach' => [
                        'storytelling' => true,
                        'benefit-focused' => true
                    ],
                    'emphasis_points' => [
                        'quality' => true,
                        'experience' => true,
                        'trust' => true
                    ],
                    'avoid_topics' => [
                        'controversy' => true,
                        'politics' => true
                    ]
                ],
                'founder_info' => [
                    'founder_name' => 'Nurullah Okatan',
                    'founder_role' => 'director',
                    'founder_experience' => [
                        'tech' => true,
                        'design' => true,
                        'business' => true,
                        'consulting' => true,
                        'management' => true
                    ],
                    'founder_personality' => [
                        'visionary' => true,
                        'analytical' => true
                    ]
                ],
                'additional_info' => [],
                'brand_story' => 'İstanbul\'un hareketli teknoloji ekosisteminde bir fikir filizlendi. Nurullah Okatan, dijital dünyanın gücüne inanan bir vizyoner olarak, markaların çevrimiçi varlıklarını güçlendirmek için yola çıktı. Türk Bilişim\'in temelleri, müşterilerin gerçek ihtiyaçlarını anlama ve onlara özel çözümler sunma tutkusuyla atıldı. İlk günlerde küçük bir ekip ve büyük hayallerle başlayan bu yolculuk, zamanla güvenilir bir danışmanlık markasına dönüştü.

Zamanla fark ettik ki başarılı bir dijital strateji, sadece teknik becerilerle değil, insan odaklı yaklaşımla mümkün. Her projeye bir hikaye gözüyle bakıyor, markaların hedef kitleleriyle gerçek bağlar kurmasına yardımcı oluyoruz. Web tasarımından sosyal medya yönetimine kadar her adımda, müşterilerimizin işlerini büyütmeleri için gereken araçları sunarken, onların uzun vadeli başarısını düşünüyoruz.

Bugün Türk Bilişim olarak, yüzlerce markanın dijital dönüşümüne tanıklık etmenin gururunu yaşıyoruz. Ancak asıl gurur kaynağımız, müşterilerimizin başarı hikayelerinde küçük de olsa bir payımızın olması. Nurullah\'ın vizyonuyla başlayan bu yolculuk, artık bir ekip ruhuyla devam ediyor. Teknolojiyi insanileştirmek ve her projeye samimiyet katmak, bizim için sadece bir iş değil, bir tutku.',
                'brand_story_created_at' => Carbon::parse('2025-07-08 22:42:25'),
                'is_active' => true,
                'is_completed' => true,
                'created_at' => Carbon::parse('2025-07-08 18:39:26'),
                'updated_at' => Carbon::parse('2025-07-08 22:42:25')
            ]
        );

        $this->command->info('✅ Test AI Tenant Profile oluşturuldu/güncellendi!');
        $this->command->info("📋 Profil ID: {$profile->id}");
        $this->command->info("🏢 Marka: {$profile->company_info['brand_name']}");
        $this->command->info("👨‍💼 Kurucu: {$profile->founder_info['founder_name']}");
        $this->command->info("📅 Kuruluş: {$profile->sector_details['brand_age_custom']}");
        $this->command->info("🎯 Pozisyon: {$profile->sector_details['market_position']}");
    }
}