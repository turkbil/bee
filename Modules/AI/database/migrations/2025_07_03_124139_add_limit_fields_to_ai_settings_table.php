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
            $table->integer('max_question_length')->default(2000)->after('enabled');
            $table->integer('max_daily_questions')->default(50)->after('max_question_length');
            $table->integer('max_monthly_questions')->default(1000)->after('max_daily_questions');
            $table->integer('question_token_limit')->default(500)->after('max_monthly_questions');
            $table->integer('free_question_tokens_daily')->default(1000)->after('question_token_limit');
            $table->boolean('charge_question_tokens')->default(false)->after('free_question_tokens_daily');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_settings', function (Blueprint $table) {
            $table->dropColumn([
                'max_question_length',
                'max_daily_questions', 
                'max_monthly_questions',
                'question_token_limit',
                'free_question_tokens_daily',
                'charge_question_tokens'
            ]);
        });
    }
};
