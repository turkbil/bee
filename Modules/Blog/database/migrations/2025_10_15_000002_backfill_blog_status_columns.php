<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Aktif yazıları yayımlanmış kabul et, yayın tarihi yoksa oluşturulma tarihini kullan
        DB::table('blogs')
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', '=', 'draft')
                    ->orWhere('status', '=','');
            })
            ->where(function ($query) {
                $query->where('is_active', true)
                    ->orWhere('is_active', 1);
            })
            ->update([
                'status' => 'published',
                'published_at' => DB::raw('COALESCE(published_at, created_at)')
            ]);

        // Aktif olmayan ve statüsü boş olan kayıtları taslak olarak işaretle
        DB::table('blogs')
            ->where(function ($query) {
                $query->whereNull('status')
                    ->orWhere('status', '=','');
            })
            ->where(function ($query) {
                $query->where('is_active', false)
                    ->orWhere('is_active', 0)
                    ->orWhereNull('is_active');
            })
            ->update([
                'status' => 'draft'
            ]);
    }

    public function down(): void
    {
        // Veri geri alma gerekmiyor
    }
};
