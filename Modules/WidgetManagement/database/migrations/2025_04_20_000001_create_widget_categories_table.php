<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('widget_categories', function (Blueprint $table) {
            $table->id('widget_category_id');
            $table->string('title', 255)->index();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('order')->default(0)->index();
            $table->string('metakey', 255)->nullable();
            $table->string('metadesc', 255)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
            
            // İlave indeksler
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('widget_categories');
    }
};