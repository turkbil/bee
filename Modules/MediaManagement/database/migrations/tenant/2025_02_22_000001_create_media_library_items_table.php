<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('media_library_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->nullable()->index();
            $table->unsignedBigInteger('media_id')->nullable()->unique();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_library_items');
    }
};
