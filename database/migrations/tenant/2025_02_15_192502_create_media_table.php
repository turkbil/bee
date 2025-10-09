<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (!Schema::hasTable('media')) {
            Schema::create('media', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->nullable()->unique();
                $table->string('model_type');
                $table->unsignedBigInteger('model_id');
                $table->index(['model_type', 'model_id']);
                $table->string('collection_name')->index();
                $table->string('name');
                $table->string('file_name');
                $table->string('mime_type')->nullable()->index();
                $table->string('disk')->index();
                $table->string('conversions_disk')->nullable();
                $table->unsignedBigInteger('size');
                $table->json('manipulations');
                $table->json('custom_properties');
                $table->json('generated_conversions');
                $table->json('responsive_images');
                $table->unsignedInteger('order_column')->nullable();
                $table->nullableTimestamps();

                // İlave performans indeksleri
                $table->index(['disk', 'collection_name']);
                $table->index('created_at');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('media');
    }
};