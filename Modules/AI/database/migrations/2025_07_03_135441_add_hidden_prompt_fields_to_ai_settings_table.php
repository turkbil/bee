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
        Schema::table('ai_settings', function (Blueprint $table) {
            $table->text('hidden_system_prompt')->nullable()->comment('Gizli sistem promptu - kullanıcıdan gizlenir');
            $table->text('secret_knowledge_base')->nullable()->comment('Gizli bilgi tabanı - AI bilir ama bahsetmez');
            $table->text('conditional_responses')->nullable()->comment('Şartlı yanıtlar - sadece sorulunca anlatır');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_settings', function (Blueprint $table) {
            $table->dropColumn(['hidden_system_prompt', 'secret_knowledge_base', 'conditional_responses']);
        });
    }
};
