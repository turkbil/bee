<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tenant_usage_logs')) {
            return;
        }

        Schema::create('tenant_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('resource_type'); // 'api', 'database', 'cache', 'storage', 'ai', 'cpu', 'memory'
            $table->integer('usage_count')->default(0);
            $table->decimal('cpu_usage_percent', 5, 2)->nullable(); // CPU kullanım yüzdesi
            $table->bigInteger('memory_usage_mb')->nullable(); // MB cinsinden RAM kullanımı
            $table->bigInteger('storage_usage_mb')->nullable(); // MB cinsinden disk kullanımı
            $table->integer('db_queries')->nullable(); // Veritabanı sorgu sayısı
            $table->integer('api_requests')->nullable(); // API istek sayısı
            $table->bigInteger('cache_size_mb')->nullable(); // Cache boyutu MB
            $table->integer('active_connections')->nullable(); // Aktif bağlantı sayısı
            $table->decimal('response_time_ms', 8, 2)->nullable(); // Ortalama yanıt süresi ms
            $table->json('additional_metrics')->nullable(); // Ek metrikler için JSON
            $table->enum('status', ['normal', 'warning', 'critical', 'blocked'])->default('normal');
            $table->text('notes')->nullable();
            $table->timestamp('recorded_at')->useCurrent();
            $table->timestamps();
            
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index(['tenant_id', 'recorded_at']);
            $table->index(['tenant_id', 'resource_type', 'recorded_at']);
            $table->index(['resource_type', 'status']);
            $table->index(['recorded_at']); // Temizlik için
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_usage_logs');
    }
};