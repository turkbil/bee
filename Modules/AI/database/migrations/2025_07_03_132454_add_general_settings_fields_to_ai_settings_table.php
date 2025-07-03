<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ai_settings', function (Blueprint $table) {
            // Sistem Davranışı
            $table->string('default_language', 5)->default('tr')->after('charge_question_tokens');
            $table->string('response_format', 20)->default('markdown')->after('default_language');
            
            // Performans Ayarları
            $table->integer('cache_duration')->default(60)->after('response_format'); // dakika
            $table->integer('concurrent_requests')->default(5)->after('cache_duration');
            
            // Güvenlik Ayarları
            $table->boolean('content_filtering')->default(true)->after('concurrent_requests');
            $table->boolean('rate_limiting')->default(true)->after('content_filtering');
            
            // Loglama & İzleme
            $table->boolean('detailed_logging')->default(false)->after('rate_limiting');
            $table->boolean('performance_monitoring')->default(true)->after('detailed_logging');
        });
    }

    public function down()
    {
        Schema::table('ai_settings', function (Blueprint $table) {
            $table->dropColumn([
                'default_language',
                'response_format',
                'cache_duration',
                'concurrent_requests',
                'content_filtering',
                'rate_limiting',
                'detailed_logging',
                'performance_monitoring'
            ]);
        });
    }
};