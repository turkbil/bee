<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('setting_id');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->foreign('setting_id')->references('id')->on('settings')->onDelete('cascade');
            
            // Aynı ayarın tekrar edilmemesi için unique index
            $table->unique(['setting_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings_values');
    }
};