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
        Schema::create('studio_settings', function (Blueprint $table) {
            $table->id();
            $table->string('module')->index();
            $table->unsignedBigInteger('module_id')->index();
            $table->string('theme')->nullable();
            $table->string('header_template')->nullable();
            $table->string('footer_template')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
            
            $table->unique(['module', 'module_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('studio_settings');
    }
};