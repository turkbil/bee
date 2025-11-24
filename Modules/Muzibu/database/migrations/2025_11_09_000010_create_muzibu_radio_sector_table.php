<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('muzibu_radio_sector')) {
            return;
        }

        Schema::create('muzibu_radio_sector', function (Blueprint $table) {
            $table->foreignId('radio_id')->constrained('muzibu_radios', 'radio_id')->cascadeOnDelete();
            $table->foreignId('sector_id')->constrained('muzibu_sectors', 'sector_id')->cascadeOnDelete();

            // Primary key
            $table->primary(['radio_id', 'sector_id']);

            // İlave indeksler
            $table->index('sector_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('muzibu_radio_sector');
    }
};
