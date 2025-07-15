<?php

namespace Modules\AI\Database\Seeders\SectorQuestions;

class WebSectorQuestions
{
    public static function getQuestions(): array
    {
        return [
            // Web Tasarım ve Geliştirme Sektörü Soruları
            [
                'id' => 3015, 'sector_code' => 'web', 'step' => 3, 'section' => null,
                'question_key' => 'web_specific_services', 'question_text' => 'Hangi web hizmetlerini sunuyorsunuz?',
                'help_text' => 'Web tasarım ve geliştirme alanındaki uzmanlaştığınız hizmetler', 'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Kurumsal web sitesi', 'value' => 'kurumsal_web'],
                    ['label' => 'E-ticaret sitesi', 'value' => 'eticaret'],
                    ['label' => 'Blog/Portfolio', 'value' => 'blog_portfolio'],
                    ['label' => 'Landing page', 'value' => 'landing_page'],
                    ['label' => 'Laravel uygulamaları', 'value' => 'laravel'],
                    ['label' => 'React/Vue.js uygulamaları', 'value' => 'react_vue'],
                    ['label' => 'SEO optimizasyonu', 'value' => 'seo'],
                    ['label' => 'Hosting ve bakım', 'value' => 'hosting'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => json_encode(['required']),
                'is_required' => true, 'sort_order' => 1, 'priority' => 2, 'ai_weight' => 80,
                'category' => 'sector', 'ai_priority' => 2, 'always_include' => false,
                'context_category' => 'services'
            ],
            [
                'id' => 3016, 'sector_code' => 'web', 'step' => 3, 'section' => null,
                'question_key' => 'web_project_types', 'question_text' => 'Genellikle hangi tür projeler üzerinde çalışıyorsunuz?',
                'help_text' => 'Proje büyüklüğü ve türleri açısından uzmanlaştığınız alanlar', 'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Küçük işletme siteleri', 'value' => 'small_business'],
                    ['label' => 'Kurumsal projeler', 'value' => 'enterprise'],
                    ['label' => 'Startup projeleri', 'value' => 'startup'],
                    ['label' => 'E-ticaret platformları', 'value' => 'ecommerce'],
                    ['label' => 'Web uygulamaları', 'value' => 'web_apps'],
                    ['label' => 'Mobil uyumlu siteler', 'value' => 'responsive'],
                    ['label' => 'SaaS uygulamaları', 'value' => 'saas'],
                    ['label' => 'Portal/Dashboard', 'value' => 'dashboard'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => null,
                'is_required' => false, 'sort_order' => 2, 'priority' => 3, 'ai_weight' => 60,
                'category' => 'sector', 'ai_priority' => 3, 'always_include' => false,
                'context_category' => 'project_types'
            ],
            [
                'id' => 3017, 'sector_code' => 'web', 'step' => 3, 'section' => null,
                'question_key' => 'web_technologies', 'question_text' => 'Hangi teknolojilerde uzmansiniz?',
                'help_text' => 'Kullandığınız programlama dilleri ve teknolojiler', 'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'PHP/Laravel', 'value' => 'php_laravel'],
                    ['label' => 'JavaScript/Node.js', 'value' => 'javascript'],
                    ['label' => 'React/Next.js', 'value' => 'react'],
                    ['label' => 'Vue.js/Nuxt.js', 'value' => 'vue'],
                    ['label' => 'WordPress', 'value' => 'wordpress'],
                    ['label' => 'HTML/CSS/Bootstrap', 'value' => 'frontend'],
                    ['label' => 'MySQL/PostgreSQL', 'value' => 'database'],
                    ['label' => 'Docker/DevOps', 'value' => 'devops'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => null,
                'is_required' => false, 'sort_order' => 3, 'priority' => 3, 'ai_weight' => 70,
                'category' => 'sector', 'ai_priority' => 3, 'always_include' => false,
                'context_category' => 'technical_skills'
            ],
            [
                'id' => 3018, 'sector_code' => 'web', 'step' => 3, 'section' => null,
                'question_key' => 'web_client_focus', 'question_text' => 'Müşteri odaklı hangi konularda öne çıkıyorsunuz?',
                'help_text' => 'Müşteri deneyimi ve hizmet kalitesi açısından güçlü yanlarınız', 'input_type' => 'checkbox',
                'options' => json_encode([
                    ['label' => 'Hızlı teslimat', 'value' => 'fast_delivery'],
                    ['label' => 'Uygun fiyat', 'value' => 'affordable'],
                    ['label' => 'Kaliteli kod', 'value' => 'quality_code'],
                    ['label' => 'SEO optimizasyonu', 'value' => 'seo_friendly'],
                    ['label' => 'Mobil uyumlu tasarım', 'value' => 'mobile_responsive'],
                    ['label' => 'Sürekli destek', 'value' => 'ongoing_support'],
                    ['label' => 'Modern tasarım', 'value' => 'modern_design'],
                    ['label' => 'Güvenlik odaklı', 'value' => 'security_focused'],
                    ['label' => 'Diğer (belirtiniz)', 'value' => 'diger', 'has_custom_input' => true]
                ]),
                'validation_rules' => null,
                'is_required' => false, 'sort_order' => 4, 'priority' => 3, 'ai_weight' => 60,
                'category' => 'sector', 'ai_priority' => 3, 'always_include' => false,
                'context_category' => 'value_proposition'
            ]
        ];
    }
}