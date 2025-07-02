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
        Schema::create('ai_token_usage', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedInteger('tokens_used'); // Kullanılan token miktarı
            $table->string('usage_type')->default('chat'); // "chat", "image", "text" vs.
            $table->text('description')->nullable(); // Kullanım açıklaması
            $table->string('reference_id')->nullable(); // İlgili conversation/message ID
            $table->json('metadata')->nullable(); // Ek bilgiler
            $table->timestamp('used_at');
            $table->timestamps();
            
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            
            $table->index(['tenant_id', 'used_at']);
            $table->index(['usage_type']);
            $table->index(['used_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_token_usage');
    }
};