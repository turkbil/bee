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
            
            // JSON language support - bulletproof multi-language
            $table->json('titles')->nullable(); // {"tr": "Başlık", "en": "Title"}
            $table->json('descriptions')->nullable(); // {"tr": "Açıklama", "en": "Description"}  
            $table->json('keywords')->nullable(); // {"tr": ["anahtar"], "en": ["keyword"]}
            $table->string('canonical_url')->nullable(); // Canonical URL
            
            // Author Info (only for blog posts)
            $table->string('author')->nullable(); // Content author name
            
            // Open Graph - JSON support for multilingual
            $table->json('og_titles')->nullable(); // {"tr": "OG Title", "en": "OG Title"}
            $table->json('og_descriptions')->nullable(); // {"tr": "OG Description", "en": "OG Description"}
            $table->string('og_image')->nullable(); // Featured image for social media
            $table->string('og_type')->default('website'); // website, article, product, etc.
            
            // Twitter Cards
            $table->string('twitter_card')->default('summary');
            $table->string('twitter_title')->nullable();
            $table->text('twitter_description')->nullable();
            $table->string('twitter_image')->nullable();
            
            // Advanced SEO
            $table->json('robots_meta')->nullable(); // {"index": true, "follow": true, "archive": false}
            $table->json('focus_keywords')->nullable(); // Dil bazında focus keywords {"tr": "anahtar", "en": "keyword"}
            $table->json('additional_keywords')->nullable(); // ["keyword1", "keyword2"]
            
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
            
            // Status & Priority
            $table->enum('status', ['active', 'inactive', 'pending'])->default('active');
            $table->integer('priority_score')->default(5); // 1-10 dynamic priority score
            
            // Note: Language management handled by HasTranslations trait
            // Note: Timestamps disabled in model
            
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