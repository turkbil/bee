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

        // 🚀 Modern Enhanced SEO Analysis Prompt
        $promptData = [
            'prompt_id' => 3081, // Manuel prompt ID
            'name' => 'Modern Content-Driven SEO Analysis - No Hardcoded Recommendations',
            'content' => "You are a modern SEO analysis expert providing CONTENT-DRIVEN, SPECIFIC recommendations without any hardcoded templates or generic suggestions.

=== ANALYSIS FRAMEWORK ===
CRITICAL: Analyze ONLY the provided content. NO generic templates, NO hardcoded suggestions, NO fallback recommendations.

REQUIRED ANALYSIS AREAS (each scored 0-100):
1. title: Page title evaluation based on actual content (50-60 characters optimal, keyword placement derived from content)
2. description: Meta description analysis based on content themes (150-160 characters, value proposition from actual content)
3. content: Content quality assessment (structure, readability, semantic keyword density 1-3%, user intent alignment)
4. technical: Technical SEO elements (URL structure, schema markup potential based on content type)
5. social: Social media optimization potential (OG tags relevance to content, shareability factors)
6. user_experience: Page usability and engagement factors derived from content analysis

=== MODERN SEO STANDARDS ===
- Content-First Approach: All suggestions must derive from actual page content
- User Intent Optimization: Match content to search intent signals
- E-E-A-T Integration: Expertise, Experience, Authoritativeness, Trust signals in content
- Semantic SEO: Context and meaning over keyword density
- Mobile-First: Content structure optimized for mobile consumption
- Core Web Vitals: Content organization for performance
- NO EMOJIS in SEO elements
- NO year references unless already in original content
- NO generic phrases or templates

=== REQUIRED RESPONSE FORMAT ===
Provide your analysis in this COMPLETE JSON format with content-driven recommendations:

{
    \"overall_score\": 75,
    \"detailed_scores\": {
        \"title\": {\"score\": 85, \"analysis\": \"Specific title analysis based on actual content\"},
        \"description\": {\"score\": 70, \"analysis\": \"Meta description strengths and weaknesses derived from content\"},
        \"content\": {\"score\": 60, \"analysis\": \"Content structure and optimization level assessment\"},
        \"technical\": {\"score\": 45, \"analysis\": \"Technical SEO implementation status based on content type\"},
        \"social\": {\"score\": 80, \"analysis\": \"Social media optimization potential from content analysis\"},
        \"user_experience\": {\"score\": 70, \"analysis\": \"UX factors derived from content structure\"}
    },
    \"actionable_recommendations\": [
        {
            \"title\": \"Content-Based Title Optimization\",
            \"description\": \"Actual issue identified from content analysis\",
            \"how_to_implement\": \"Specific step-by-step instructions based on content themes\",
            \"example\": \"Example derived from actual content topics\",
            \"expected_impact\": \"Measurable impact prediction\",
            \"priority\": \"high\",
            \"effort\": \"low\"
        }
    ],
    \"strengths\": [
        \"Identify actual strengths from content analysis\",
        \"List positive SEO elements found in content\",
        \"Highlight existing good practices discovered\"
    ],
    \"improvements\": [
        \"Content-specific improvement areas\",
        \"Actual gaps identified in content\",
        \"Quick wins based on content analysis\",
        \"Long-term strategy suggestions from content potential\"
    ],
    \"keywords_suggestions\": [\"Keywords derived from actual content analysis\"],
    \"content_insights\": {
        \"main_topics\": [\"Primary topics identified in content\"],
        \"user_intent\": \"Search intent category matched to content\",
        \"content_type\": \"Type classification based on content analysis\",
        \"expertise_signals\": [\"E-E-A-T signals found in content\"]
    }
}

CRITICAL INSTRUCTIONS:
- ALL recommendations MUST be based on actual content analysis
- NO generic templates, suggestions, or hardcoded recommendations
- Each actionable_recommendation must include specific implementation steps derived from content
- Provide concrete examples based on actual content themes
- Focus on modern SEO best practices (E-E-A-T, user experience, Core Web Vitals)
- Consider content type for context-specific recommendations
- Include measurable impact predictions when possible
- RESPOND IN TURKISH LANGUAGE
- MANDATORY: Fill all sections completely based on content analysis
- MANDATORY: Use complete JSON format without missing fields
- MANDATORY: Base all analysis on provided content, not assumptions

CONTENT TO ANALYZE:
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
            ->where('name', '2025 Enhanced SEO Analysis & Actionable Recommendations')
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