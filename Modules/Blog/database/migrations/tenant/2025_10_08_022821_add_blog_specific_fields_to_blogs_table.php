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
        Schema::table('blogs', function (Blueprint $table) {
            // Blog-specific fields
            $table->json('excerpt')->nullable()->after('body')->comment('Çoklu dil özet: {"tr": "Özet", "en": "Excerpt"}');
            $table->timestamp('published_at')->nullable()->after('excerpt')->comment('Yayınlanma tarihi');
            $table->boolean('is_featured')->default(false)->after('published_at')->index()->comment('Öne çıkan yazı');
            $table->enum('status', ['draft', 'published', 'scheduled'])->default('draft')->after('is_featured')->index()->comment('Yazı durumu');

            // İndeksler
            $table->index('published_at');
            $table->index(['status', 'is_active', 'published_at'], 'blogs_status_active_published_idx');
            $table->index(['is_featured', 'status', 'published_at'], 'blogs_featured_status_published_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            // İndeksleri drop et
            $table->dropIndex('blogs_status_active_published_idx');
            $table->dropIndex('blogs_featured_status_published_idx');
            $table->dropIndex(['is_featured']);
            $table->dropIndex(['status']);
            $table->dropIndex(['published_at']);

            // Kolonları drop et
            $table->dropColumn([
                'excerpt',
                'published_at',
                'is_featured',
                'status'
            ]);
        });
    }
};
