<?php

namespace Modules\AI\Database\Seeders\SectorQuestions;

class TechnologySectorQuestions
{
    public static function getQuestions(): array
    {
        return [
            // Teknoloji Sektörü Soruları
            [
                'id' => 3020, 'sector_code' => 'technology', 'step' => 3, 'section' => null,
                'question_key' => 'tech_specific_services', 'question_text' => 'Hangi teknoloji hizmetlerini sunuyorsunuz?',
                'help_text' => 'Teknoloji alanındaki uzmanlaştığınız hizmetler', 'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Yazılım geliştirme', 'value' => 'software_development'],
                    ['label' => 'Mobil uygulama', 'value' => 'mobile_app'],
                    ['label' => 'Sistem entegrasyonu', 'value' => 'system_integration'],
                    ['label' => 'Bulut çözümleri', 'value' => 'cloud_solutions'],
                    ['label' => 'Veri analizi', 'value' => 'data_analytics'],
                    ['label' => 'Yapay zeka/ML', 'value' => 'ai_ml'],
                    ['label' => 'Siber güvenlik', 'value' => 'cybersecurity'],
                    ['label' => 'DevOps/Altyapı', 'value' => 'devops'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => json_encode(['required']),
                'is_required' => true, 'sort_order' => 1, 'priority' => 2, 'ai_weight' => 80,
                'category' => 'sector', 'ai_priority' => 2, 'always_include' => false,
                'context_category' => 'services'
            ],
            [
                'id' => 3021, 'sector_code' => 'technology', 'step' => 3, 'section' => null,
                'question_key' => 'tech_platforms', 'question_text' => 'Hangi platformlarda çalışıyorsunuz?',
                'help_text' => 'Kullandığınız teknoloji platformları ve çerçeveler', 'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => '.NET/C#', 'value' => 'dotnet'],
                    ['label' => 'Java/Spring', 'value' => 'java'],
                    ['label' => 'Python/Django', 'value' => 'python'],
                    ['label' => 'Node.js/Express', 'value' => 'nodejs'],
                    ['label' => 'React/Angular', 'value' => 'frontend_frameworks'],
                    ['label' => 'AWS/Azure/GCP', 'value' => 'cloud_platforms'],
                    ['label' => 'Docker/Kubernetes', 'value' => 'containerization'],
                    ['label' => 'Microservices', 'value' => 'microservices'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => null,
                'is_required' => false, 'sort_order' => 2, 'priority' => 3, 'ai_weight' => 70,
                'category' => 'sector', 'ai_priority' => 3, 'always_include' => false,
                'context_category' => 'technical_skills'
            ],
            [
                'id' => 3022, 'sector_code' => 'technology', 'step' => 3, 'section' => null,
                'question_key' => 'tech_industry_focus', 'question_text' => 'Hangi sektörlere hizmet veriyorsunuz?',
                'help_text' => 'Müşteri portföyünüzdeki sektörel odaklanma', 'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Fintech/Bankacılık', 'value' => 'fintech'],
                    ['label' => 'E-ticaret/Perakende', 'value' => 'ecommerce'],
                    ['label' => 'Sağlık/Healthtech', 'value' => 'healthtech'],
                    ['label' => 'Eğitim/Edtech', 'value' => 'edtech'],
                    ['label' => 'Lojistik/Taşımacılık', 'value' => 'logistics'],
                    ['label' => 'Medya/İletişim', 'value' => 'media'],
                    ['label' => 'Üretim/Endüstri 4.0', 'value' => 'manufacturing'],
                    ['label' => 'Kamu/Devlet', 'value' => 'government'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => null,
                'is_required' => false, 'sort_order' => 3, 'priority' => 3, 'ai_weight' => 60,
                'category' => 'sector', 'ai_priority' => 3, 'always_include' => false,
                'context_category' => 'market_focus'
            ]
        ];
    }
}