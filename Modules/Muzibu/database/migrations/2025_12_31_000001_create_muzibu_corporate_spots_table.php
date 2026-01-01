<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('muzibu_corporate_spots')) {
            return;
        }

        Schema::create('muzibu_corporate_spots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('corporate_account_id')->constrained('muzibu_corporate_accounts')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->unsignedInteger('duration')->default(0)->comment('Süre (saniye)');
            $table->timestamp('starts_at')->nullable()->comment('Başlangıç tarihi ve saati');
            $table->timestamp('ends_at')->nullable()->comment('Bitiş tarihi ve saati');
            $table->unsignedInteger('position')->default(0)->comment('Sıralama');
            $table->boolean('is_enabled')->default(true)->comment('Aktif mi?');
            $table->boolean('is_archived')->default(false)->comment('Arşivlendi mi?');
            $table->timestamps();

            // Indexes
            $table->index(['corporate_account_id', 'is_enabled', 'is_archived'], 'spots_corp_enabled_archived_idx');
            $table->index(['corporate_account_id', 'position'], 'spots_corp_position_idx');
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('muzibu_corporate_spots');
    }
};
