<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->string('api_key')->nullable();
            $table->string('model')->default('deepseek-chat');
            $table->integer('max_tokens')->default(4096);
            $table->float('temperature')->default(0.7);
            $table->boolean('enabled')->default(true);
            $table->timestamps();
            
            $table->index('created_at');
            $table->index('updated_at');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_settings');
    }
};