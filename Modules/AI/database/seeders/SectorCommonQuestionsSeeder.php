<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Helpers\TenantHelpers;

class SectorCommonQuestionsSeeder extends Seeder
{
    /**
     * SECTOR COMMON QUESTIONS SEEDER
     * Tüm sektörlere sorulacak ortak hizmet soruları
     * Genel işletme bilgileri ve hizmet kapsamı
     */
    public function run(): void
    {
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        echo "🎯 Sektörel ortak sorular yükleniyor...\n";

        // Gereksiz ortak soruları temizle (hizmet alanları, çalışma saatleri vb.)
        DB::table('ai_profile_questions')->whereIn('question_key', [
            'service_areas', 'working_hours', 'payment_options', 'special_services', 
            'customer_profile', 'expertise_areas', 'business_capacity', 'communication_channels'
        ])->delete();
        
        // Ana iş bilgileri sorularını güncelle - zaten mevcut olanları güncelle
        $updateQuestions = [
            'target_customers' => [
                'question_text' => 'Ana müşteri kitleniz kimler?',
                'help_text' => 'Genellikle hangi tür müşterilerle çalışıyorsunuz',
                'input_type' => 'checkbox',
                'options' => '["Bireysel müşteriler", "Küçük ölçekli işletmeler", "Büyük ölçekli şirketler", {"label": "Diğer (belirtiniz)", "value": "custom", "has_custom_input": true, "custom_placeholder": "Müşteri kitlenizi belirtiniz"}]',
                'is_required' => 1,
                'section' => 'ana_is_bilgileri',
                'priority' => 1,
                'ai_weight' => 90,
                'ai_priority' => 1,
                'always_include' => 1,
                'context_category' => 'target_market'
            ],
            'main_business_activities' => [
                'question_text' => 'Yaptığınız ana iş kolları nelerdir?',
                'help_text' => 'Ana faaliyet alanlarınızı ve sunduğunuz temel hizmetleri belirtin',
                'input_type' => 'textarea',
                'options' => null,
                'is_required' => 1,
                'section' => 'ana_is_bilgileri',
                'priority' => 1,
                'ai_weight' => 95,
                'ai_priority' => 1,
                'always_include' => 1,
                'context_category' => 'business_scope'
            ]
        ];

        // Mevcut soruları güncelle
        foreach ($updateQuestions as $questionKey => $updateData) {
            DB::table('ai_profile_questions')
                ->where('question_key', $questionKey)
                ->update(array_merge($updateData, [
                    'updated_at' => now()
                ]));
        }

        echo "✅ " . count($updateQuestions) . " sabit ana soru güncellendi!\n";
        echo "📋 Ana iş bilgileri: Ana müşteri kitlesi ve ana iş kolları\n";
        echo "🚫 Gereksiz detay sorular kaldırıldı - sektöre özel sorular ayrı yüklenecek\n";
    }
}