<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('seo_settings', function (Blueprint $table) {
            $table->id();
            
            // Polymorphic relationship - herhangi bir modele bağlanabilir
            $table->morphs('seoable'); // seoable_id, seoable_type
            
            // Temel SEO alanları
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            
            // JSON language support - bulletproof multi-language
            $table->json('titles')->nullable(); // {"tr": "Başlık", "en": "Title"}
            $table->json('descriptions')->nullable(); // {"tr": "Açıklama", "en": "Description"}  
            $table->json('keywords')->nullable(); // {"tr": ["anahtar"], "en": ["keyword"]}
            $table->string('canonical_url')->nullable(); // Canonical URL
            
            // Author Info (2025 SEO Standards)
            $table->string('author')->nullable(); // Content author name
            $table->string('author_url')->nullable(); // Author website/profile URL
            $table->string('copyright')->nullable(); // Copyright information
            
            // Open Graph - JSON support for multilingual
            $table->json('og_titles')->nullable(); // {"tr": "OG Title", "en": "OG Title"}
            $table->json('og_descriptions')->nullable(); // {"tr": "OG Description", "en": "OG Description"}
            $table->json('og_images')->nullable(); // {"tr": "url", "en": "url"} - Multi-language OG images
            $table->string('og_image')->nullable(); // Featured image for social media (legacy)
            $table->string('og_type')->default('website'); // website, article, product, etc.
            $table->string('og_locale')->nullable(); // tr_TR, en_US, etc.
            $table->string('og_site_name')->nullable(); // Site name override
            
            // Twitter Cards
            $table->string('twitter_card')->default('summary');
            $table->string('twitter_title')->nullable();
            $table->text('twitter_description')->nullable();
            $table->string('twitter_image')->nullable();
            
            // Advanced SEO
            $table->json('robots_meta')->nullable(); // {"index": true, "follow": true, "archive": false}
            $table->json('schema_markup')->nullable(); // Structured data
            $table->json('schema_type')->nullable()->comment('Schema.org page types per language - {"tr": "Article", "en": "BlogPosting"}'); // 2025 SEO Standard
            $table->json('focus_keywords')->nullable(); // Dil bazında focus keywords {"tr": "anahtar", "en": "keyword"}
            $table->json('additional_keywords')->nullable(); // ["keyword1", "keyword2"]
            
            // Hreflang support
            $table->json('hreflang_urls')->nullable(); // {"tr": "url", "en": "url"}
            
            // AI Crawler permissions (2025 modern SEO)
            $table->boolean('allow_gptbot')->default(true); // ChatGPT crawler
            $table->boolean('allow_claudebot')->default(true); // Claude crawler  
            $table->boolean('allow_google_extended')->default(true); // Bard/Gemini crawler
            $table->boolean('allow_bingbot_ai')->default(true); // Bing AI crawler
            
            // SEO Metrics & Analysis
            $table->integer('seo_score')->default(0); // 0-100 AI calculated score
            $table->json('seo_analysis')->nullable(); // AI analysis results
            $table->timestamp('last_analyzed')->nullable();
            
            // Content analysis
            $table->integer('content_length')->default(0);
            $table->integer('keyword_density')->default(0); // Percentage
            $table->json('readability_score')->nullable(); // AI readability analysis
            
            // Performance tracking
            $table->json('page_speed_insights')->nullable(); // Google PageSpeed data
            $table->timestamp('last_crawled')->nullable();
            
            // AI SEO Analysis Results (2025 AI-powered SEO)
            $table->json('analysis_results')->nullable(); // Complete AI analysis results
            $table->timestamp('analysis_date')->nullable(); // When analysis was performed
            $table->integer('overall_score')->nullable(); // Main SEO score (0-100)
            $table->json('detailed_scores')->nullable(); // All category scores
            $table->json('strengths')->nullable(); // AI-generated strengths list
            $table->json('improvements')->nullable(); // AI-generated improvements list
            $table->json('action_items')->nullable(); // AI-generated action items
            
            // AI Integration
            $table->json('ai_suggestions')->nullable(); // AI SEO suggestions
            $table->boolean('auto_optimize')->default(false); // AI auto-optimization enabled
            
            // Status & Priority
            $table->enum('status', ['active', 'inactive', 'pending'])->default('active');
            $table->integer('priority_score')->default(5); // 1-10 dynamic priority score
            
            // Language management
            $table->json('available_languages')->nullable(); // ["tr", "en", "de"]
            $table->string('default_language')->nullable();
            $table->json('language_fallbacks')->nullable(); // {"de": "en", "fr": "en"}
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['seoable_id', 'seoable_type']);
            $table->index('status');
            $table->index('seo_score');
            $table->index('last_analyzed');
            $table->index('overall_score');
            $table->index('analysis_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_settings');
    }
};