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
            $table->string('canonical_url')->nullable(); // Canonical URL
            
            // Author & Publisher Info (2025 SEO Standards)
            $table->string('author')->nullable(); // Content author name
            $table->string('publisher')->nullable(); // Publisher name
            $table->string('copyright')->nullable(); // Copyright information
            
            // Open Graph - JSON support for multilingual
            $table->json('og_titles')->nullable(); // {"tr": "OG Title", "en": "OG Title"}
            $table->json('og_descriptions')->nullable(); // {"tr": "OG Description", "en": "OG Description"}
            $table->string('og_image')->nullable(); // Featured image for social media
            $table->string('og_type')->default('website'); // website, article, product, etc.
            $table->string('og_locale')->nullable(); // tr_TR, en_US, etc.
            $table->string('og_site_name')->nullable(); // Site name override
            
            // Twitter Cards
            $table->string('twitter_card')->default('summary'); // summary, summary_large_image
            $table->string('twitter_title')->nullable();
            $table->text('twitter_description')->nullable();
            $table->string('twitter_image')->nullable(); // Twitter specific image
            $table->string('twitter_site')->nullable(); // @username
            $table->string('twitter_creator')->nullable(); // @author_username
            
            // Advanced SEO
            $table->string('robots')->default('index, follow'); // Simple robots directive
            $table->json('schema_markup')->nullable(); // Structured data
            
            // AI Crawler permissions (2025 modern SEO)
            $table->boolean('allow_gptbot')->default(true); // ChatGPT crawler
            $table->boolean('allow_claudebot')->default(true); // Claude crawler  
            $table->boolean('allow_google_extended')->default(true); // Bard/Gemini crawler
            $table->boolean('allow_bingbot_ai')->default(true); // Bing AI crawler
            
            // Priority score system (modern approach)
            $table->integer('priority_score')->default(5); // 1-10 dynamic priority score
            
            // SEO Metrics & Analysis
            $table->integer('seo_score')->default(0); // 0-100 AI calculated score
            $table->json('seo_analysis')->nullable(); // AI analysis results
            $table->timestamp('last_analyzed')->nullable();
            
            // Content analysis
            $table->integer('content_length')->default(0);
            $table->integer('keyword_density')->default(0); // Percentage
            $table->json('readability_score')->nullable(); // AI readability analysis
            
            // AI Integration
            $table->json('ai_suggestions')->nullable(); // AI SEO suggestions
            $table->boolean('auto_optimize')->default(false); // AI auto-optimization enabled
            
            // Status & Priority  
            $table->enum('status', ['active', 'inactive', 'pending'])->default('active');
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['seoable_id', 'seoable_type']);
            $table->index('status');
            $table->index('seo_score');
            $table->index('last_analyzed');
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