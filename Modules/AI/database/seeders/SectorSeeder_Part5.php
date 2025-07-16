<?php

namespace Modules\AI\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Helpers\TenantHelpers;

class SectorSeeder_Part5 extends Seeder
{
    /**
     * SECTOR SEEDER PART 5 (ID 300)
     * Sadece 1 ekstra sektör - diğer part'lardan farklı
     */
    public function run(): void
    {
        if (!TenantHelpers::isCentral()) {
            return;
        }
        
        echo "🎯 Sektörler Part 5 yükleniyor (sadece 1 ekstra sektör)...\n";

        // Sadece 1 farklı sektör ekle
        $this->addExtraSector();

        echo "✅ Part 5 tamamlandı! (1 ekstra sektör eklendi)\n";
    }
    
    private function addExtraSector(): void
    {
        // Sadece 1 ekstra sektör (ID 300 - diğer part'lardan tamamen farklı)
        $sectors = [
            // QUANTUM COMPUTING & FUTURE TECH (ID 300) - Tamamen yeni ve farklı
            ['id' => 300, 'code' => 'quantum_computing', 'category_id' => 1, 'name' => 'Kuantum Bilişim & Gelecek Teknolojileri', 'emoji' => '⚛️', 'color' => 'indigo', 'description' => 'Kuantum bilgisayar, gelecek teknolojileri araştırması', 'keywords' => 'kuantum, quantum, gelecek teknoloji, araştırma, inovasyon'],
        ];

        $addedCount = 0;
        foreach ($sectors as $sector) {
            try {
                // Sadece mevcut değilse ekle
                $existing = DB::table('ai_profile_sectors')->where('id', $sector['id'])->exists();
                if (!$existing) {
                    DB::table('ai_profile_sectors')->insert(array_merge($sector, [
                        'icon' => null,
                        'is_subcategory' => 1, // Alt kategori olarak işaretle
                        'is_active' => 1,
                        'sort_order' => $sector['id'] * 10,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]));
                    $addedCount++;
                }
            } catch (\Exception $e) {
                echo "⚠️ Sektör atlandı: " . $e->getMessage() . "\n";
                continue;
            }
        }

        echo "📊 Part 5: {$addedCount} ekstra farklı sektör eklendi\n";
    }
}