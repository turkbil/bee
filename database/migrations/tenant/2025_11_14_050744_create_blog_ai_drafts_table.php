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
        Schema::create('blog_ai_drafts', function (Blueprint $table) {
            $table->id();
            $table->string('topic_keyword');
            $table->json('category_suggestions');
            $table->json('seo_keywords');
            $table->json('outline');
            $table->text('meta_description')->nullable();
            $table->boolean('is_selected')->default(false);
            $table->boolean('is_generated')->default(false);
            $table->foreignId('generated_blog_id')->nullable()->constrained('blogs', 'blog_id')->onDelete('set null');
            $table->timestamps();

            // Indexes
            $table->index(['is_selected', 'is_generated']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_ai_drafts');
    }
};
