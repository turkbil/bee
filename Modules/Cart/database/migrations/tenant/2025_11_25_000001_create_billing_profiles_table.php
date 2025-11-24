<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('billing_profiles')) {
            return;
        }

        Schema::create('billing_profiles', function (Blueprint $table) {
            $table->id('billing_profile_id');

            // User
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Profile Info
            $table->string('title')->comment('Profil adı: Kişisel, ABC Şirketi vb.');
            $table->enum('type', ['individual', 'corporate'])->default('individual');

            // Individual (TC Kimlik)
            $table->string('identity_number', 11)->nullable()->comment('TC Kimlik No (Bireysel)');

            // Corporate
            $table->string('company_name')->nullable()->comment('Şirket ünvanı');
            $table->string('tax_number', 10)->nullable()->comment('Vergi Kimlik No');
            $table->string('tax_office')->nullable()->comment('Vergi Dairesi');

            // Contact (opsiyonel - şirket için farklı olabilir)
            $table->string('contact_name')->nullable()->comment('Yetkili kişi adı');
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();

            // Default
            $table->boolean('is_default')->default(false);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('user_id');
            $table->index('type');
            $table->index('is_default');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_profiles');
    }
};
