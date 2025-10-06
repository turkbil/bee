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
        Schema::connection('central')->create('ai_bulk_operations', function (Blueprint $table) {
            $table->id();
            $table->uuid('operation_uuid')->unique();
            $table->string('operation_type', 50)->comment('bulk_translate, bulk_seo, bulk_optimize');
            $table->string('module_name', 50);
            $table->json('record_ids')->comment('İşlenecek kayıt ID listesi');
            $table->json('options')->nullable()->comment('İşlem seçenekleri');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'partial'])->default('pending');
            $table->integer('progress')->default(0)->comment('Yüzde olarak ilerleme');
            $table->integer('total_items');
            $table->integer('processed_items')->default(0);
            $table->integer('success_items')->default(0);
            $table->integer('failed_items')->default(0);
            $table->json('results')->nullable()->comment('İşlem sonuçları');
            $table->json('error_log')->nullable()->comment('Hata kayıtları');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            // Index'ler
            $table->index(['status', 'module_name'], 'idx_status_module');
            $table->index(['created_by'], 'idx_created_by');
            $table->index(['operation_type'], 'idx_operation_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('central')->dropIfExists('ai_bulk_operations');
    }
};