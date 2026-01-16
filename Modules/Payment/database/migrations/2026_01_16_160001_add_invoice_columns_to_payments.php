<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('invoice_number')->nullable()->after('payment_number')->unique();
            $table->timestamp('invoice_uploaded_at')->nullable();
            $table->unsignedBigInteger('invoice_uploaded_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['invoice_number', 'invoice_uploaded_at', 'invoice_uploaded_by']);
        });
    }
};
