<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeoAnalysisPromptSeeder extends Seeder
{
    public function run(): void
    {
        // Sadece central database'de çalışsın
        if (config('database.default') !== 'mysql') {
            echo "⚠️ SEO Analysis Prompt Seeder: Tenant ortamı - atlanıyor\n";
            return;
        }
        echo "\n🔍 SEO ANALYSIS PROMPT SYSTEM oluşturuluyor...\n";

        // SEO Analysis için özel prompt
        $promptData = [
            'prompt_id' => 3081, // Manuel prompt ID
            'name' => 'SEO Comprehensive Analysis & Suggestions System',
            'content' => "You are a comprehensive SEO analysis expert. You MUST analyze all 6 required areas and provide scores for each.

REQUIRED ANALYSIS AREAS (score each 0-100):
1. title: Page title evaluation (length, clarity, relevance)  
2. description: Page description analysis (length, effectiveness, appeal)
3. content: Content quality assessment (structure, length, readability)
4. technical: Technical SEO elements (meta tags, structure, formatting, HTML validation) - MANDATORY FIELD
5. social: Social sharing optimization (Open Graph tags, Twitter cards)
6. priority: Content importance score (value, relevance, timeliness)

CRITICAL: You MUST include ALL 6 scores in your response. The technical_score is mandatory and cannot be omitted.

Return your analysis in this EXACT JSON format (all fields required):

{
    \"overall_score\": 75,
    \"title_score\": 85,
    \"description_score\": 70,
    \"content_type_score\": 60,
    \"technical_score\": 45,
    \"social_score\": 80,
    \"priority_score\": 65,
    \"strengths\": [
        \"Page title length is appropriate\",
        \"Social media sharing tags are present\",
        \"Key term placement is good\"
    ],
    \"improvements\": [
        \"Page description should be shorter\",
        \"Content length can be increased\",
        \"Technical web elements can be improved\"
    ],
    \"action_items\": [
        \"Limit page description to 160 characters\",
        \"Add main heading (H1)\",
        \"Expand content to 500+ words\",
        \"Add structured data markup\"
    ]
}

USER INPUT:
{{user_input}}",
            'prompt_type' => 'feature',
            'module_specific' => 'seo',
            'variables' => json_encode(['user_input']),
            'priority' => 100,
            'ai_weight' => 1.0,
            'prompt_category' => 'feature_definition',
            'is_default' => true,
            'is_system' => true,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now()
        ];

        DB::table('ai_prompts')->updateOrInsert(
            ['name' => $promptData['name']],
            $promptData
        );

        $insertedPromptId = DB::table('ai_prompts')
            ->where('name', 'SEO Comprehensive Analysis & Suggestions System')
            ->value('prompt_id');

        echo "✅ SEO Comprehensive Analysis prompt eklendi (ID: {$insertedPromptId})\n";

        // Feature ile prompt'u ilişkilendir - PROMPT ID'si dinamik
        $actualPromptId = $insertedPromptId;
            
        $featureId = 305; // seo-comprehensive-audit feature ID'si
        
        if ($actualPromptId) {
            DB::table('ai_feature_prompt_relations')->updateOrInsert(
                [
                    'feature_id' => $featureId,
                    'prompt_id' => $actualPromptId
                ],
                [
                    'feature_id' => $featureId,
                    'prompt_id' => $actualPromptId,
                    'priority' => 1,
                    'role' => 'primary',
                    'is_active' => true,
                    'feature_type_filter' => 'specific',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            echo "✅ SEO Comprehensive Audit feature ile prompt ilişkilendirildi (Prompt ID: {$actualPromptId})\n";
        } else {
            echo "❌ Prompt bulunamadı - bağlantı oluşturulamadı\n";
        }

        echo "✅ SEO ANALYSIS PROMPT SYSTEM HAZIR!\n\n";
    }
}