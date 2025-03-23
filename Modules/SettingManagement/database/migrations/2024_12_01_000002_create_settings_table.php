<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id')->index();
            $table->string('label')->index();
            $table->string('key')->unique();
            $table->string('type')->index();
            $table->json('options')->nullable();
            $table->text('default_value')->nullable();
            $table->integer('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_system')->default(false)->index();
            $table->timestamps();

            $table->foreign('group_id')->references('id')->on('settings_groups')->onDelete('cascade');
            
            // İlave indeksler
            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};