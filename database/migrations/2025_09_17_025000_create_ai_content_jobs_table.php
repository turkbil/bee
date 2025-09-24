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
        Schema::create('ai_content_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique()->index();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('component')->default('ContentBuilder');

            // Job parametreleri
            $table->json('parameters');
            $table->string('content_type')->nullable();
            $table->string('page_title')->nullable();

            // Job durumu
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->integer('progress_percentage')->default(0);
            $table->string('progress_message')->nullable();

            // Sonuç
            $table->longText('generated_content')->nullable();
            $table->integer('credits_used')->default(0);
            $table->json('meta_data')->nullable();

            // Hata bilgileri
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);

            // Zaman damgaları
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamps();

            // İndeksler
            $table->index(['tenant_id', 'status']);
            $table->index(['user_id', 'created_at']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_content_jobs');
    }
};