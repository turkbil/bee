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
        Schema::table('ai_tenant_profiles', function (Blueprint $table) {
            $table->text('brand_story')->nullable()->after('additional_info');
            $table->timestamp('brand_story_created_at')->nullable()->after('brand_story');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_tenant_profiles', function (Blueprint $table) {
            $table->dropColumn(['brand_story', 'brand_story_created_at']);
        });
    }
};
