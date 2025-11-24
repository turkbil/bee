<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Helpers\TenantHelpers;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('ai_feature_categories')) {
            return;
        }

        Schema::create('ai_feature_categories', function (Blueprint $table) {
                $table->id('ai_feature_category_id');
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->integer('order')->default(0);
                $table->string('icon')->default('fas fa-folder');
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->boolean('has_subcategories')->default(false);
                $table->timestamps();

                $table->foreign('parent_id')->references('ai_feature_category_id')->on('ai_feature_categories')->onDelete('cascade');
                $table->index(['is_active', 'order']);
                $table->index(['parent_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_feature_categories');
    }
};
