<?php

declare(strict_types=1);

namespace Modules\AI\App\Services\Templates;

/**
 * 📄 Config-based Template Repository Implementation
 * 
 * Template'leri config dosyasından ve hardcode'dan yükler
 * Daha sonra veritabanı implementasyonu eklenebilir
 */
readonly class ConfigBasedTemplateRepository implements TemplateRepository
{
    /**
     * Template'i isimle bul
     */
    public function findByName(string $name): ?array
    {
        // Built-in templates
        $templates = $this->getBuiltInTemplates();
        
        return $templates[$name] ?? null;
    }

    /**
     * Feature için template'leri al
     */
    public function findByFeature(string $featureName): array
    {
        $templates = $this->getBuiltInTemplates();
        $result = [];

        foreach ($templates as $templateName => $template) {
            if (isset($template['features']) && in_array($featureName, $template['features'])) {
                $result[] = $template;
            }
        }

        return $result;
    }

    /**
     * Template kaydet (config-based için read-only)
     */
    public function save(array $templateData): bool
    {
        // Config-based repository read-only
        return false;
    }

    /**
     * Template sil (config-based için read-only)
     */
    public function delete(string $name): bool
    {
        // Config-based repository read-only
        return false;
    }

    /**
     * Tüm template'leri listele
     */
    public function all(): array
    {
        return $this->getBuiltInTemplates();
    }

    /**
     * 🏗️ Built-in template'leri al
     */
    private function getBuiltInTemplates(): array
    {
        return [
            'default' => [
                'name' => 'default',
                'parent_template' => null,
                'format' => 'markdown',
                'sections' => [
                    'introduction' => 'Bu konuda size yardımcı olmak için buradayım.',
                    'content' => '{{content}}',
                    'conclusion' => 'Başka bir sorunuz varsa çekinmeden sorun!'
                ],
                'structure' => ['introduction', 'content', 'conclusion'],
                'variables' => ['content'],
                'conditions' => [],
                'features' => ['general']
            ],

            'blog_post' => [
                'name' => 'blog_post',
                'parent_template' => 'content_base',
                'format' => 'markdown',
                'sections' => [
                    'title' => '{{title}}',
                    'introduction' => '{{introduction}}',
                    'main_content' => '{{main_content}}',
                    'key_points' => '## Önemli Noktalar\n\n{{#foreach key_points:point}}\n- {{point}}\n{{/foreach}}',
                    'conclusion' => '## Sonuç\n\n{{conclusion}}',
                    'call_to_action' => '{{?call_to_action:{{call_to_action}}}}'
                ],
                'structure' => ['title', 'introduction', 'main_content', 'key_points', 'conclusion', 'call_to_action'],
                'variables' => ['title', 'introduction', 'main_content', 'key_points', 'conclusion', 'call_to_action'],
                'conditions' => [
                    'call_to_action' => ['type' => 'simple', 'variable' => 'call_to_action']
                ],
                'features' => ['content_generator', 'blog_writer', 'article_writer']
            ],

            'content_base' => [
                'name' => 'content_base',
                'parent_template' => null,
                'format' => 'markdown',
                'sections' => [
                    'header' => '# {{title}}\n\n*{{_mode}} modunda, {{_timestamp}} tarihinde oluşturuldu*',
                    'meta_info' => '**Hedef Kelime Sayısı:** {{_word_count_target}} kelime  \n**Ton:** {{_response_tone}}  \n**İçerik Tipi:** {{_content_type}}',
                    'footer' => '---\n\n*Bu içerik {{company_name}} için AI tarafından oluşturulmuştur.*'
                ],
                'structure' => ['header', 'meta_info', 'footer'],
                'variables' => ['title', 'company_name'],
                'conditions' => [],
                'features' => []
            ],

            'seo_content' => [
                'name' => 'seo_content',
                'parent_template' => 'content_base',
                'format' => 'markdown',
                'sections' => [
                    'seo_title' => '# {{seo_title}}',
                    'meta_description' => '*{{meta_description}}*',
                    'keywords_section' => '**Anahtar Kelimeler:** {{keywords}}',
                    'optimized_content' => '{{optimized_content}}',
                    'internal_links' => '## İlgili Makaleler\n\n{{#foreach internal_links:link}}\n- [{{link.title}}]({{link.url}})\n{{/foreach}}',
                    'seo_footer' => '**SEO Skoru:** {{seo_score}}/100'
                ],
                'structure' => ['seo_title', 'meta_description', 'keywords_section', 'optimized_content', 'internal_links', 'seo_footer'],
                'variables' => ['seo_title', 'meta_description', 'keywords', 'optimized_content', 'internal_links', 'seo_score'],
                'conditions' => [
                    'internal_links' => ['type' => 'simple', 'variable' => 'internal_links'],
                    'seo_footer' => ['type' => 'simple', 'variable' => 'seo_score']
                ],
                'features' => ['seo_analyzer', 'content_optimizer', 'keyword_researcher']
            ],

            'social_media' => [
                'name' => 'social_media',
                'parent_template' => null,
                'format' => 'markdown',
                'sections' => [
                    'main_post' => '{{main_post}}',
                    'hashtags' => '{{#foreach hashtags:tag}}#{{tag}} {{/foreach}}',
                    'call_to_action' => '{{call_to_action}}',
                    'engagement_hooks' => '{{?engagement_hooks:{{engagement_hooks}}}}'
                ],
                'structure' => ['main_post', 'hashtags', 'call_to_action', 'engagement_hooks'],
                'variables' => ['main_post', 'hashtags', 'call_to_action', 'engagement_hooks'],
                'conditions' => [
                    'hashtags' => ['type' => 'simple', 'variable' => 'hashtags'],
                    'engagement_hooks' => ['type' => 'simple', 'variable' => 'engagement_hooks']
                ],
                'features' => ['social_media_generator', 'twitter_generator', 'instagram_generator']
            ],

            'email_marketing' => [
                'name' => 'email_marketing',
                'parent_template' => null,
                'format' => 'markdown',
                'sections' => [
                    'subject_line' => '**Konu:** {{subject_line}}',
                    'preheader' => '**Ön Başlık:** {{preheader}}',
                    'greeting' => 'Merhaba {{recipient_name}},',
                    'opening' => '{{opening}}',
                    'main_message' => '{{main_message}}',
                    'value_proposition' => '## Neden Bu Önemli?\n\n{{value_proposition}}',
                    'call_to_action_button' => '**[{{cta_text}}]({{cta_link}})**',
                    'closing' => '{{closing}}\n\n{{sender_name}}  \n{{sender_title}}  \n{{company_name}}',
                    'unsubscribe' => '*Bu e-postayı almak istemiyorsanız [buradan]({{unsubscribe_link}}) çıkabilirsiniz.*'
                ],
                'structure' => ['subject_line', 'preheader', 'greeting', 'opening', 'main_message', 'value_proposition', 'call_to_action_button', 'closing', 'unsubscribe'],
                'variables' => ['subject_line', 'preheader', 'recipient_name', 'opening', 'main_message', 'value_proposition', 'cta_text', 'cta_link', 'closing', 'sender_name', 'sender_title', 'company_name', 'unsubscribe_link'],
                'conditions' => [
                    'preheader' => ['type' => 'simple', 'variable' => 'preheader'],
                    'value_proposition' => ['type' => 'simple', 'variable' => 'value_proposition'],
                    'unsubscribe' => ['type' => 'simple', 'variable' => 'unsubscribe_link']
                ],
                'features' => ['email_generator', 'newsletter_generator', 'marketing_email']
            ],

            'product_description' => [
                'name' => 'product_description',
                'parent_template' => null,
                'format' => 'markdown',
                'sections' => [
                    'product_name' => '# {{product_name}}',
                    'short_description' => '{{short_description}}',
                    'key_features' => '## Özellikler\n\n{{#foreach key_features:feature}}\n- **{{feature.name}}:** {{feature.description}}\n{{/foreach}}',
                    'benefits' => '## Faydaları\n\n{{#foreach benefits:benefit}}\n- {{benefit}}\n{{/foreach}}',
                    'specifications' => '## Teknik Özellikler\n\n{{#foreach specifications:spec}}\n- **{{spec.name}}:** {{spec.value}}\n{{/foreach}}',
                    'price_info' => '## Fiyat\n\n{{price_info}}',
                    'availability' => '**Stok Durumu:** {{availability}}',
                    'purchase_cta' => '**[{{cta_text}}]({{purchase_link}})**'
                ],
                'structure' => ['product_name', 'short_description', 'key_features', 'benefits', 'specifications', 'price_info', 'availability', 'purchase_cta'],
                'variables' => ['product_name', 'short_description', 'key_features', 'benefits', 'specifications', 'price_info', 'availability', 'cta_text', 'purchase_link'],
                'conditions' => [
                    'specifications' => ['type' => 'simple', 'variable' => 'specifications'],
                    'price_info' => ['type' => 'simple', 'variable' => 'price_info'],
                    'availability' => ['type' => 'simple', 'variable' => 'availability']
                ],
                'features' => ['product_description_generator', 'ecommerce_content']
            ],

            'faq_generator' => [
                'name' => 'faq_generator',
                'parent_template' => null,
                'format' => 'markdown',
                'sections' => [
                    'title' => '# Sıkça Sorulan Sorular',
                    'introduction' => '{{introduction}}',
                    'faq_items' => '{{#foreach faq_items:faq}}\n## {{faq.question}}\n\n{{faq.answer}}\n{{/foreach}}',
                    'contact_info' => '## Başka Sorularınız mı Var?\n\n{{contact_info}}'
                ],
                'structure' => ['title', 'introduction', 'faq_items', 'contact_info'],
                'variables' => ['introduction', 'faq_items', 'contact_info'],
                'conditions' => [
                    'introduction' => ['type' => 'simple', 'variable' => 'introduction'],
                    'contact_info' => ['type' => 'simple', 'variable' => 'contact_info']
                ],
                'features' => ['faq_generator', 'customer_support']
            ],

            'comparison_table' => [
                'name' => 'comparison_table',
                'parent_template' => 'content_base',
                'format' => 'markdown',
                'sections' => [
                    'comparison_title' => '# {{comparison_title}}',
                    'introduction' => '{{introduction}}',
                    'comparison_table' => '| Özellik | {{#foreach products:product}}{{product.name}} | {{/foreach}}\n|---------|{{#foreach products:product}}----------|{{/foreach}}\n{{#foreach features:feature}}| {{feature.name}} | {{#foreach products:product}}{{feature.values[product.id]}} | {{/foreach}}\n{{/foreach}}',
                    'summary' => '## Sonuç\n\n{{summary}}',
                    'recommendation' => '## Tavsiyemiz\n\n{{recommendation}}'
                ],
                'structure' => ['comparison_title', 'introduction', 'comparison_table', 'summary', 'recommendation'],
                'variables' => ['comparison_title', 'introduction', 'products', 'features', 'summary', 'recommendation'],
                'conditions' => [
                    'introduction' => ['type' => 'simple', 'variable' => 'introduction'],
                    'summary' => ['type' => 'simple', 'variable' => 'summary'],
                    'recommendation' => ['type' => 'simple', 'variable' => 'recommendation']
                ],
                'features' => ['comparison_generator', 'product_comparison']
            ],

            'landing_page' => [
                'name' => 'landing_page',
                'parent_template' => null,
                'format' => 'html',
                'sections' => [
                    'hero_section' => '<div class="hero">\n<h1>{{hero_title}}</h1>\n<p>{{hero_subtitle}}</p>\n<a href="{{cta_link}}" class="btn-primary">{{cta_text}}</a>\n</div>',
                    'features_section' => '<div class="features">\n<h2>Özellikler</h2>\n{{#foreach features:feature}}\n<div class="feature">\n<h3>{{feature.title}}</h3>\n<p>{{feature.description}}</p>\n</div>\n{{/foreach}}\n</div>',
                    'testimonials' => '<div class="testimonials">\n<h2>Müşteri Yorumları</h2>\n{{#foreach testimonials:testimonial}}\n<blockquote>\n<p>"{{testimonial.text}}"</p>\n<cite>- {{testimonial.author}}</cite>\n</blockquote>\n{{/foreach}}\n</div>',
                    'final_cta' => '<div class="final-cta">\n<h2>{{final_cta_title}}</h2>\n<p>{{final_cta_text}}</p>\n<a href="{{cta_link}}" class="btn-primary btn-large">{{cta_text}}</a>\n</div>'
                ],
                'structure' => ['hero_section', 'features_section', 'testimonials', 'final_cta'],
                'variables' => ['hero_title', 'hero_subtitle', 'cta_link', 'cta_text', 'features', 'testimonials', 'final_cta_title', 'final_cta_text'],
                'conditions' => [
                    'testimonials' => ['type' => 'simple', 'variable' => 'testimonials'],
                    'final_cta' => ['type' => 'simple', 'variable' => 'final_cta_title']
                ],
                'features' => ['landing_page_generator', 'sales_page']
            ]
        ];
    }
}