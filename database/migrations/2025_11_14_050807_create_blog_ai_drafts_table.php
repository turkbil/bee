<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * NOTE: blog_ai_drafts table is in TENANT database only.
     * This is a placeholder for central migrations.
     */
    public function up(): void
    {
        // Tenant-only table - no central migration needed
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tenant-only table - no central migration needed
    }
};
