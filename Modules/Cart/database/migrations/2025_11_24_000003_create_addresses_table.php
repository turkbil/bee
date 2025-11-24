<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cart_addresses')) {
            return;
        }

        Schema::create('cart_addresses', function (Blueprint $table) {
            $table->id('address_id');

            // User relation
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Address Type
            $table->enum('address_type', ['billing', 'shipping', 'both'])
                  ->default('both');

            // Title
            $table->string('title')->nullable()->comment('Ev, İş, vb.');

            // Personal Info
            $table->string('first_name');
            $table->string('last_name');
            $table->string('company_name')->nullable();
            $table->string('tax_office')->nullable();
            $table->string('tax_number')->nullable();

            // Contact
            $table->string('phone');
            $table->string('email')->nullable();

            // Address
            $table->text('address_line_1');
            $table->text('address_line_2')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('district');
            $table->string('city');
            $table->string('postal_code', 10)->nullable();
            $table->string('country_code', 2)->default('TR');

            // Defaults
            $table->boolean('is_default_billing')->default(false);
            $table->boolean('is_default_shipping')->default(false);

            // Notes
            $table->text('delivery_notes')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('user_id');
            $table->index('address_type');
            $table->index('city');
            $table->index(['user_id', 'is_default_billing']);
            $table->index(['user_id', 'is_default_shipping']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_addresses');
    }
};
