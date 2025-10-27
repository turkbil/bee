<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * UNIVERSAL SEARCH LOG SYSTEM
     * Search analytics for all modules (Shop, Blog, Portfolio, etc.)
     */
    public function up(): void
    {
        if (Schema::hasTable('search_logs')) {
            return;
        }

        Schema::create('search_logs', function (Blueprint $table) {
            $table->id('search_log_id');

            // User (nullable for guest searches)
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('session_id')->nullable()->index()->comment('Misafir kullanıcı için session');

            // Search info
            $table->string('query')->index()->comment('Arama kelimesi');
            $table->string('module')->nullable()->index()->comment('shop, blog, portfolio, all, vs.');
            $table->json('filters')->nullable()->comment('Uygulanan filtreler: {"category":"electronics", "price_range":[100,500]}');

            // Results
            $table->integer('results_count')->default(0)->comment('Kaç sonuç bulundu');
            $table->boolean('has_results')->default(true)->index()->comment('Sonuç bulundu mu?');

            // Click tracking
            $table->string('clicked_result_type')->nullable()->comment('Product, Post, Portfolio, vs.');
            $table->unsignedBigInteger('clicked_result_id')->nullable()->comment('Tıklanan sonucun ID\'si');
            $table->integer('clicked_position')->nullable()->comment('Kaçıncı sıradaki sonuca tıklandı (1, 2, 3, ...)');

            // Meta
            $table->string('ip_address', 45)->nullable()->comment('IPv4 or IPv6');
            $table->string('user_agent', 500)->nullable();
            $table->string('referrer')->nullable()->comment('Nereden geldi');
            $table->string('device_type', 20)->nullable()->comment('mobile, tablet, desktop');

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index('created_at');
            $table->index(['query', 'module'], 'search_logs_query_module_idx');
            $table->index(['has_results', 'created_at'], 'search_logs_results_date_idx');
            $table->index(['module', 'created_at'], 'search_logs_module_date_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_logs');
    }
};
