<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();

            // Polymorphic relation - Herhangi bir model'e puan verilebilir
            $table->morphs('ratable'); // ratable_id + ratable_type

            // User relation
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Rating değeri (1-5 yıldız) - Google Schema.org standardı
            $table->unsignedTinyInteger('rating_value')->comment('1-5 yıldız puanı');

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            // NOT: morphs() zaten ratable_type + ratable_id için index oluşturur
            $table->index('rating_value');
            $table->index('created_at');

            // Unique constraint - Bir kullanıcı aynı içeriğe birden fazla puan veremez
            $table->unique(['user_id', 'ratable_id', 'ratable_type'], 'ratings_unique_user_item');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
