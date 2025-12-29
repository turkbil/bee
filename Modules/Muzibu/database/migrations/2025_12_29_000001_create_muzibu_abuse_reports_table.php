<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('muzibu_abuse_reports')) {
            return;
        }

        Schema::create('muzibu_abuse_reports', function (Blueprint $table) {
            $table->id();

            // Taranan kullanıcı
            $table->unsignedBigInteger('user_id');

            // Tarama bilgileri
            $table->date('scan_date')->comment('Tarama yapılan tarih');
            $table->dateTime('period_start')->comment('İncelenen dönem başlangıcı');
            $table->dateTime('period_end')->comment('İncelenen dönem sonu');

            // İstatistikler
            $table->unsignedInteger('total_plays')->default(0)->comment('Toplam dinleme sayısı');
            $table->unsignedInteger('overlap_count')->default(0)->comment('Çakışma sayısı');
            $table->unsignedInteger('abuse_score')->default(0)->comment('Toplam çakışma saniyesi');

            // Durum
            $table->enum('status', ['clean', 'suspicious', 'abuse'])->default('clean');

            // Detaylı veri (JSON)
            $table->json('overlaps_json')->nullable()->comment('Detaylı çakışma verileri');
            $table->json('daily_stats')->nullable()->comment('Günlük istatistikler');

            // Admin review
            $table->unsignedBigInteger('reviewed_by')->nullable()->comment('İnceleyen admin');
            $table->dateTime('reviewed_at')->nullable();
            $table->enum('action_taken', ['none', 'warned', 'suspended'])->nullable();
            $table->text('notes')->nullable()->comment('Admin notları');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['user_id', 'scan_date'], 'abuse_user_date_idx');
            $table->index(['status', 'abuse_score'], 'abuse_status_score_idx');
            $table->index('scan_date', 'abuse_scan_date_idx');
            $table->index('status', 'abuse_status_idx');

            // Unique constraint: bir kullanıcı için aynı tarihte tek rapor
            $table->unique(['user_id', 'scan_date'], 'abuse_user_scan_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('muzibu_abuse_reports');
    }
};
