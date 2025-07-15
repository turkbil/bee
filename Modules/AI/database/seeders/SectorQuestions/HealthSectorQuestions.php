<?php

namespace Modules\AI\Database\Seeders\SectorQuestions;

class HealthSectorQuestions
{
    public static function getQuestions(): array
    {
        return [
            // Sağlık Sektörü Soruları
            [
                'id' => 3025, 'sector_code' => 'health', 'step' => 3, 'section' => null,
                'question_key' => 'health_specific_services', 'question_text' => 'Hangi sağlık hizmetlerini sunuyorsunuz?',
                'help_text' => 'Sağlık alanındaki uzmanlaştığınız hizmetler', 'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Genel pratisyen', 'value' => 'general_practice'],
                    ['label' => 'Dahiliye', 'value' => 'internal_medicine'],
                    ['label' => 'Kardiyoloji', 'value' => 'cardiology'],
                    ['label' => 'Nöroloji', 'value' => 'neurology'],
                    ['label' => 'Ortopedi', 'value' => 'orthopedics'],
                    ['label' => 'Dermatologi', 'value' => 'dermatology'],
                    ['label' => 'Göz hastalıkları', 'value' => 'ophthalmology'],
                    ['label' => 'Dış güzellik/Estetik', 'value' => 'aesthetic'],
                    ['label' => 'Diş hekimliği', 'value' => 'dentistry'],
                    ['label' => 'Fizyoterapi', 'value' => 'physiotherapy'],
                    ['label' => 'Psikolog/Psikiyatrist', 'value' => 'psychology'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => json_encode(['required']),
                'is_required' => true, 'sort_order' => 1, 'priority' => 2, 'ai_weight' => 80,
                'category' => 'sector', 'ai_priority' => 2, 'always_include' => false,
                'context_category' => 'services'
            ],
            [
                'id' => 3026, 'sector_code' => 'health', 'step' => 3, 'section' => null,
                'question_key' => 'health_patient_groups', 'question_text' => 'Hangi hasta gruplarına hizmet veriyorsunuz?',
                'help_text' => 'Hasta demografiniz ve hedef kitle', 'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Çocuk hastalar (0-18 yaş)', 'value' => 'pediatric'],
                    ['label' => 'Yetişkin hastalar (18-65 yaş)', 'value' => 'adult'],
                    ['label' => 'Yaşlı hastalar (65+ yaş)', 'value' => 'geriatric'],
                    ['label' => 'Hamile kadınlar', 'value' => 'pregnant_women'],
                    ['label' => 'Sporcular', 'value' => 'athletes'],
                    ['label' => 'Kronik hastalar', 'value' => 'chronic_patients'],
                    ['label' => 'Acil hastalar', 'value' => 'emergency_patients'],
                    ['label' => 'Check-up/Preventif', 'value' => 'preventive'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => null,
                'is_required' => false, 'sort_order' => 2, 'priority' => 3, 'ai_weight' => 60,
                'category' => 'sector', 'ai_priority' => 3, 'always_include' => false,
                'context_category' => 'target_audience'
            ],
            [
                'id' => 3027, 'sector_code' => 'health', 'step' => 3, 'section' => null,
                'question_key' => 'health_service_style', 'question_text' => 'Sağlık hizmetinizi nasıl sunuyorsunuz?',
                'help_text' => 'Hizmet sunım şekli ve yaklaşımınız', 'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Özel muayenehane', 'value' => 'private_clinic'],
                    ['label' => 'Hastane bünyesinde', 'value' => 'hospital_based'],
                    ['label' => 'Evde bakım', 'value' => 'home_care'],
                    ['label' => 'Online konsültasyon', 'value' => 'telemedicine'],
                    ['label' => 'Acil servis', 'value' => 'emergency'],
                    ['label' => 'Yatılı tedavi', 'value' => 'inpatient'],
                    ['label' => 'Günübirlik tedavi', 'value' => 'outpatient'],
                    ['label' => 'Rehabilitasyon merkezi', 'value' => 'rehabilitation'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => null,
                'is_required' => false, 'sort_order' => 3, 'priority' => 3, 'ai_weight' => 50,
                'category' => 'sector', 'ai_priority' => 3, 'always_include' => false,
                'context_category' => 'service_delivery'
            ]
        ];
    }
}