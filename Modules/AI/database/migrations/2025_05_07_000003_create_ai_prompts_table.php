<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_prompts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('content');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_system')->default(false); // Sistem promptları değiştirilemez
            $table->boolean('is_common')->default(false); // Ortak özellikler promptu
            $table->boolean('is_active')->default(true);  // Aktiflik durumu eklendi
            $table->timestamps();
            
            $table->index('name');
            $table->index('is_default');
            $table->index('is_system');
            $table->index('is_common');
            $table->index('is_active');  // is_active için index eklendi
            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_prompts');
    }
};