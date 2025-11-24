<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            // Polymorphic relation - Herhangi bir model'e review yazılabilir
            $table->morphs('reviewable'); // reviewable_id + reviewable_type

            // User relation
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Parent review (nested reviews için)
            $table->foreignId('parent_id')->nullable()->constrained('reviews')->cascadeOnDelete();

            // Review içeriği
            $table->string('author_name')->comment('Yazar adı (user name veya manuel)');
            $table->text('review_body')->comment('İnceleme metni');

            // Rating (1-5 yıldız) - Google Schema.org Review standardı
            $table->unsignedTinyInteger('rating_value')->nullable()->comment('1-5 yıldız puanı');

            // Admin onay sistemi
            $table->boolean('is_approved')->default(false)->index()->comment('Admin onayı');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

            // Helpful/Unhelpful counter
            $table->unsignedInteger('helpful_count')->default(0)->comment('Faydalı bulma sayısı');
            $table->unsignedInteger('unhelpful_count')->default(0)->comment('Faydasız bulma sayısı');

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            // NOT: morphs() zaten reviewable_type + reviewable_id için index oluşturur
            // NOT: is_approved zaten satır 31'de ->index() ile tanımlı
            $table->index('parent_id');
            $table->index('created_at');
            $table->index(['is_approved', 'created_at']);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
