<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ai_settings', function (Blueprint $table) {
            // Token Kampanya Ayarları
            $table->decimal('token_campaign_multiplier', 5, 2)->default(1.00)->after('performance_monitoring');
            $table->string('campaign_name')->nullable()->after('token_campaign_multiplier');
            $table->text('campaign_description')->nullable()->after('campaign_name');
            $table->timestamp('campaign_start_date')->nullable()->after('campaign_description');
            $table->timestamp('campaign_end_date')->nullable()->after('campaign_start_date');
            $table->boolean('campaign_active')->default(false)->after('campaign_end_date');
        });
    }

    public function down()
    {
        Schema::table('ai_settings', function (Blueprint $table) {
            $table->dropColumn([
                'token_campaign_multiplier',
                'campaign_name',
                'campaign_description',
                'campaign_start_date',
                'campaign_end_date',
                'campaign_active'
            ]);
        });
    }
};