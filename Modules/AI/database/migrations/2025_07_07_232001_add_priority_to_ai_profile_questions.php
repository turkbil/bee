<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ai_profile_questions', function (Blueprint $table) {
            $table->integer('ai_priority')->default(3)->after('sort_order')
                  ->comment('AI context priority: 1=critical, 2=important, 3=normal, 4=optional, 5=rarely_used');
            $table->boolean('always_include')->default(false)->after('ai_priority')
                  ->comment('Bu alan her AI context\'inde yer alsın mı?');
            $table->string('context_category')->nullable()->after('always_include')
                  ->comment('Context kategori: brand_identity, business_info, behavior_rules');
        });
    }

    public function down()
    {
        Schema::table('ai_profile_questions', function (Blueprint $table) {
            $table->dropColumn(['ai_priority', 'always_include', 'context_category']);
        });
    }
};