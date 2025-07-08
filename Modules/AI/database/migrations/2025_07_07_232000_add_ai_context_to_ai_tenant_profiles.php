<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ai_tenant_profiles', function (Blueprint $table) {
            $table->text('ai_context')->nullable()->after('brand_story_created_at')
                  ->comment('AI için optimize edilmiş context - öncelikli bilgiler');
            $table->json('context_priority')->nullable()->after('ai_context')
                  ->comment('Context bilgilerinin priority sıralaması');
        });
    }

    public function down()
    {
        Schema::table('ai_tenant_profiles', function (Blueprint $table) {
            $table->dropColumn(['ai_context', 'context_priority']);
        });
    }
};