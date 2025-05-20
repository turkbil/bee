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
            $table->unsignedBigInteger('setting_id')->index();
            $table->text('value')->nullable();
            $table->timestamps();
            
            // Foreign key kısıtlamasını kaldırıyoruz çünkü settings tablosu merkezi veritabanında
            // $table->foreign('setting_id')->references('id')->on('settings')->onDelete('cascade');
            
            // Aynı ayarın tekrar edilmemesi için unique index
            $table->unique(['setting_id']);
            
            // İlave indeksler
            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings_values');
    }
};