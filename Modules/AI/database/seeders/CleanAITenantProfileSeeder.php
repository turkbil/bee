<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AI\App\Models\AITenantProfile;
use App\Models\Tenant;
use Carbon\Carbon;
use App\Helpers\TenantHelpers;

class CleanAITenantProfileSeeder extends Seeder
{
    /**
     * TEMİZ AI TENANT PROFILE SEEDER
     * SQL export'undan oluşturulmuş temiz ve optimize seeder
     */
    public function run(): void
    {
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        echo "🎯 Temiz AI Tenant Profile oluşturuluyor...\n";

        // Mevcut profilleri temizle
        AITenantProfile::truncate();
        
        $centralTenant = Tenant::where('central', true)->first();

        if (!$centralTenant) {
            echo "⚠️  Central tenant bulunamadı, AI tenant profili oluşturulamadı.\n";
            return;
        }

        // SQL export'undan temiz profil verisi
        $profileData = [
            'id' => 1,
            'tenant_id' => $centralTenant->id,
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
                'founder_permission' => 'yes_full',
                'share_founder_info' => 'evet',
                'business_start_year' => '2020',
                'share_founder_info_label' => 'Evet, bilgilerimi paylaşmak istiyorum',
                'share_founder_info_question' => 'Kurucu hakkında bilgi paylaşmak ister misiniz?',
                'business_start_year_question' => 'Hangi yıldan beri bu işi yapıyorsunuz?'
            ],
            'sector_details' => [
                'sector' => 'web_design',
                'branches' => 'hybrid',
                'brand_age' => 'custom',
                'sector_name' => 'Web Tasarım',
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
                'sector_selection' => 'web_design',
                'target_customers' => [
                    'buyuk_sirketler' => true
                ],
                'brand_personality' => [
                    'friendly' => true
                ],
                'sector_description' => 'Website tasarım, UI/UX',
                'main_business_activities' => 'WEB TASARIM',
                'main_business_activities_question' => 'Yaptığınız ana iş kolları nelerdir?'
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
                'writing_style' => [
                    'kisa_net' => true,
                    'sade_anlasilir' => true
                ],
                'brand_character' => [
                    'geleneksel_koklu' => true
                ],
                'emphasis_points' => [
                    'quality' => true
                ]
            ],
            'ai_behavior_rules' => [
                'brand_voice' => 'trustworthy',
                'avoid_topics' => [
                    'politics' => true,
                    'controversy' => true
                ],
                'writing_tone' => [
                    'friendly' => true,
                    'professional' => true
                ],
                'emphasis_points' => [
                    'trust' => true,
                    'quality' => true,
                    'experience' => true
                ],
                'content_approach' => [
                    'storytelling' => true,
                    'benefit-focused' => true
                ],
                'communication_style' => [
                    'consultative' => true,
                    'solution-focused' => true
                ]
            ],
            'founder_info' => [
                'founder_name' => 'Nurullah Okatan',
                'founder_role' => 'founder',
                'founder_qualities' => [
                    'liderlik' => true
                ],
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
            'smart_field_scores' => null,
            'field_calculation_metadata' => null,
            'profile_completeness_score' => 0.00,
            'profile_quality_grade' => 'F',
            'last_calculation_context' => 'normal',
            'scores_calculated_at' => null,
            'context_performance' => null,
            'ai_recommendations' => null,
            'missing_critical_fields' => 0,
            'field_quality_analysis' => null,
            'usage_analytics' => null,
            'ai_interactions_count' => 0,
            'last_ai_interaction_at' => null,
            'avg_ai_response_quality' => 0.00,
            'profile_version' => 1,
            'version_history' => null,
            'auto_optimization_enabled' => true,
            'brand_story' => '1998 yılında, internetin Türkiye\'de henüz emekleme döneminde olduğu günlerde, Nurullah Okatan bir vizyonla yola çıktı. O dönemde web tasarım dendiğinde akla sadece basit sayfalar geliyordu, ancak kurucumuz dijital dünyanın potansiyelini erken fark edenlerdendi. Küçük bir ekip ve büyük bir tutkuyla başlayan bu yolculuk, zamanla Türk Bilişim\'in bugünkü saygın konumuna ulaşmasını sağladı. İlk günlerden itibaren odak noktası, müşterilerine sadece estetik değil aynı zamanda işlevsel çözümler sunmaktı.

Yıllar içinde teknoloji hızla değişirken, ekip olarak kendilerini sürekli yenilemenin önemini kavradılar. Web tasarımın yanı sıra sosyal medya yönetimi ve internet reklamcılığı alanlarında uzmanlaşarak, müşterilerine bütüncül dijital çözümler sunmaya başladılar. Her projeye yaklaşımlarındaki titizlik ve detaylara verilen önem, kısa sürede güvenilir bir marka olarak tanınmalarını sağladı. Müşteri memnuniyetini her şeyin üzerinde tutan bu anlayış, onları sektörde öne çıkaran en önemli değer oldu.

Bugün Türk Bilişim, 25 yılı aşkın deneyimiyle yüzlerce başarılı projeye imza atmış bir marka. Ancak hikayenin en güzel yanı, hala o ilk günkü heyecanı koruyor olmaları. Her yeni projeyi bir öncekinden daha iyi nasıl yapabileceklerini düşünerek, sürekli kendilerini geliştirmeye devam ediyorlar. Müşterileriyle kurdukları samimi ilişkiler ve uzun soluklu iş birlikleri, kaliteli hizmet anlayışlarının en büyük kanıtı.

Geleceğe bakarken, dijital dünyanın sınırlarını zorlamaya ve yenilikçi çözümler üretmeye devam edecekler. Çünkü onlar için bu sadece bir iş değil, aynı zamanda bir tutku. Türk Bilişim ekibi, her yeni güne "bugün daha iyi nasıl yapabiliriz" sorusuyla başlıyor ve bu sorunun peşinden koşmayı asla bırakmıyor.',
            'brand_story_created_at' => Carbon::parse('2025-07-13 16:22:59'),
            'ai_context' => null,
            'context_priority' => null,
            'is_active' => true,
            'is_completed' => true,
            'created_at' => Carbon::parse('2025-07-08 15:39:26'),
            'updated_at' => Carbon::parse('2025-07-13 18:23:55')
        ];

        // Profili oluştur
        AITenantProfile::create($profileData);

        echo "✅ Temiz AI Tenant Profile oluşturuldu!\n";
        echo "📋 Profil ID: 1\n";
        echo "🏢 Marka: Türk Bilişim\n";
        echo "👨‍💼 Kurucu: Nurullah Okatan\n";
        echo "💼 Sektör: Web Tasarım\n";
    }
}
