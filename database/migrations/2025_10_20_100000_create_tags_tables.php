<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tags')) {
            Schema::create('tags', function (Blueprint $table) {
                $table->id('tag_id');
                $table->string('name');
                $table->string('slug')->index();
                $table->string('type')->nullable()->index();
                $table->string('color', 32)->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->unique('slug', 'tags_slug_unique');
            });
        }

        if (!Schema::hasTable('taggables')) {
            Schema::create('taggables', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tag_id');
                $table->string('taggable_type');
                $table->unsignedBigInteger('taggable_id');
                $table->timestamps();

                $table->unique(['tag_id', 'taggable_type', 'taggable_id'], 'taggables_unique');
                $table->index(['taggable_type', 'taggable_id'], 'taggables_taggable_index');

                $table->foreign('tag_id')
                    ->references('tag_id')
                    ->on('tags')
                    ->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('taggables');
        Schema::dropIfExists('tags');
    }
};
