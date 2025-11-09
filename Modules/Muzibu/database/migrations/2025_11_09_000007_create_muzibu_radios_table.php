<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('muzibu_radios', function (Blueprint $table) {
            $table->id('radio_id');
            $table->json('title')->comment('Çoklu dil radyo adı: {"tr": "Radyo", "en": "Radio"}');
            $table->json('slug')->comment('Çoklu dil slug: {"tr": "radyo", "en": "radio"}');
            $table->unsignedBigInteger('media_id')->nullable()->comment('Thumbmaker media ID (radio logo)');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('media_id')->references('id')->on('media')->nullOnDelete();

            // İlave indeksler
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');

            // Composite index'ler
            $table->index(['is_active', 'deleted_at', 'created_at'], 'muzibu_radios_active_deleted_created_idx');
            $table->index(['is_active', 'deleted_at'], 'muzibu_radios_active_deleted_idx');
        });

        // JSON slug indexes (MySQL 8.0+ / MariaDB 10.5+) - Disabled for compatibility
        // Note: JSON functional indexes disabled for broader database compatibility
    }

    public function down(): void
    {
        Schema::dropIfExists('muzibu_radios');
    }
};
