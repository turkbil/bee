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
            
            // Open Graph - JSON support for multilingual
            $table->json('og_title')->nullable(); // {"tr": "OG Title", "en": "OG Title"}
            $table->json('og_description')->nullable(); // {"tr": "OG Description", "en": "OG Description"}
            $table->string('og_image')->nullable();
            $table->string('og_type')->default('website');
            
            // Twitter Cards
            $table->string('twitter_card')->default('summary');
            $table->string('twitter_title')->nullable();
            $table->text('twitter_description')->nullable();
            $table->string('twitter_image')->nullable();
            
            // Advanced SEO
            $table->string('canonical_url')->nullable();
            $table->json('robots_meta')->nullable(); // {"index": true, "follow": true, "archive": false}
            $table->json('schema_markup')->nullable(); // Structured data
            $table->string('focus_keyword')->nullable();
            $table->json('focus_keywords')->nullable(); // Dil bazında focus keywords {"tr": "anahtar", "en": "keyword"}
            $table->json('additional_keywords')->nullable(); // ["keyword1", "keyword2"]
            
            // SEO Metrics & Analysis
            $table->integer('seo_score')->default(0); // 0-100 AI calculated score
            $table->json('seo_analysis')->nullable(); // AI analysis results
            $table->timestamp('last_analyzed')->nullable();
            
            // Hreflang support
            $table->json('hreflang_urls')->nullable(); // {"tr": "url", "en": "url"}
            
            // Content analysis
            $table->integer('content_length')->default(0);
            $table->integer('keyword_density')->default(0); // Percentage
            $table->json('readability_score')->nullable(); // AI readability analysis
            
            // Performance tracking
            $table->json('page_speed_insights')->nullable(); // Google PageSpeed data
            $table->timestamp('last_crawled')->nullable();
            
            // AI Integration
            $table->json('ai_suggestions')->nullable(); // AI SEO suggestions
            $table->boolean('auto_optimize')->default(false); // AI auto-optimization enabled
            
            // Status & Priority
            $table->enum('status', ['active', 'inactive', 'pending'])->default('active');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            
            // Language management
            $table->json('available_languages')->nullable(); // ["tr", "en", "de"]
            $table->string('default_language')->default('tr');
            $table->json('language_fallbacks')->nullable(); // {"de": "en", "fr": "en"}
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['seoable_id', 'seoable_type']);
            $table->index('status');
            $table->index('seo_score');
            $table->index('focus_keyword');
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