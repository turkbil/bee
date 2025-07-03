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
        Schema::table('ai_prompts', function (Blueprint $table) {
            $table->enum('prompt_type', [
                'standard',        // Normal prompt
                'common',          // Ortak özellikler (eski is_common)
                'hidden_system',   // Gizli sistem promptu
                'secret_knowledge', // Gizli bilgi tabanı
                'conditional'      // Şartlı yanıtlar
            ])->default('standard')->after('content');
        });
        
        // Mevcut is_common=true olanları common tipine çevir
        DB::table('ai_prompts')
            ->where('is_common', true)
            ->update(['prompt_type' => 'common']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_prompts', function (Blueprint $table) {
            $table->dropColumn('prompt_type');
        });
    }
};
