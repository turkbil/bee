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
        Schema::table('media_library_items', function (Blueprint $table) {
            $table->string('generation_source')->nullable()->after('meta')
                ->comment('Source of media: ai_generated, user_upload, etc.');
            $table->text('generation_prompt')->nullable()->after('generation_source')
                ->comment('AI prompt used for generation');
            $table->json('generation_params')->nullable()->after('generation_prompt')
                ->comment('AI generation parameters: size, quality, model, etc.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media_library_items', function (Blueprint $table) {
            $table->dropColumn(['generation_source', 'generation_prompt', 'generation_params']);
        });
    }
};
