<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_limits', function (Blueprint $table) {
            $table->id();
            $table->integer('daily_limit')->default(100);
            $table->integer('monthly_limit')->default(3000);
            $table->integer('used_today')->default(0);
            $table->integer('used_month')->default(0);
            $table->timestamp('reset_at')->nullable();
            $table->timestamps();
            
            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_limits');
    }
};