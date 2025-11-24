<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tenant_rate_limits')) {
            return;
        }

        Schema::create('tenant_rate_limits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('endpoint_pattern')->default('*'); // Route pattern (*, /api/*, /api/users/*)
            $table->string('method')->default('*'); // HTTP method (GET, POST, *, etc)
            $table->integer('requests_per_minute')->default(60);
            $table->integer('requests_per_hour')->default(1000);
            $table->integer('requests_per_day')->default(10000);
            $table->integer('burst_limit')->default(10); // Ani yoğunluk limiti
            $table->integer('concurrent_requests')->default(5); // Eşzamanlı istek limiti
            $table->json('ip_whitelist')->nullable(); // IP beyaz listesi
            $table->json('ip_blacklist')->nullable(); // IP kara listesi
            $table->enum('throttle_strategy', ['fixed_window', 'sliding_window', 'token_bucket'])->default('sliding_window');
            $table->integer('penalty_duration')->default(60); // Ceza süresi (saniye)
            $table->enum('penalty_action', ['block', 'delay', 'queue', 'warn'])->default('delay');
            $table->boolean('is_active')->default(true);
            $table->boolean('log_violations')->default(true); // İhlalleri kaydet
            $table->integer('priority')->default(0); // Öncelik sırası (yüksek sayı = yüksek öncelik)
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index(['tenant_id', 'is_active']);
            $table->index(['endpoint_pattern', 'method']);
            $table->index(['priority', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_rate_limits');
    }
};