<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable()->index();
            $table->string('prefix')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->string('name')->index();
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->json('layout')->nullable()->comment('Form yapısını JSON formatında saklar');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('parent_id')->references('id')->on('settings_groups')->onDelete('cascade');

            // Slug sadece unique olmalı
            $table->unique(['slug']);
            
            // İlave indeksler
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings_groups');
    }
};