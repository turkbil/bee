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
        Schema::create('search_queries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id', 100)->index();
            $table->string('query', 500)->index();
            $table->string('searchable_type')->nullable()->index()->comment('Model type: ShopProduct, Page, etc.');
            $table->integer('results_count')->default(0);
            $table->json('filters_applied')->nullable()->comment('Applied filters');
            $table->integer('response_time_ms')->nullable()->comment('Search response time in milliseconds');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('locale', 5)->default('tr');
            $table->string('referrer_url', 500)->nullable()->comment('Where user came from');
            $table->timestamps();

            // Composite indexes for analytics
            $table->index('created_at');
            $table->index(['query', 'created_at']);
            $table->index(['searchable_type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_queries');
    }
};
