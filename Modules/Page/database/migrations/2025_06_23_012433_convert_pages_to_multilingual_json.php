<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Bu migration mevcut string verilerini JSON formatına çevirir
     * Sadece pages tablosu zaten var ve veriler string formatındaysa çalışır
     */
    public function up(): void
    {
        // Önce pages tablosu var mı kontrol et
        if (!Schema::hasTable('pages')) {
            return;
        }

        // Tablodaki verileri al
        $pages = DB::table('pages')->get();
        
        foreach ($pages as $page) {
            $updates = [];
            
            // title string ise JSON'a çevir
            if (is_string($page->title ?? null)) {
                $updates['title'] = json_encode(['tr' => $page->title]);
            }
            
            // slug string ise JSON'a çevir
            if (is_string($page->slug ?? null)) {
                $updates['slug'] = json_encode(['tr' => $page->slug]);
            }
            
            // body string ise JSON'a çevir
            if (is_string($page->body ?? null)) {
                $updates['body'] = json_encode(['tr' => $page->body]);
            }
            
            // metakey string ise JSON'a çevir
            if (is_string($page->metakey ?? null)) {
                $updates['metakey'] = json_encode(['tr' => $page->metakey]);
            }
            
            // metadesc string ise JSON'a çevir
            if (is_string($page->metadesc ?? null)) {
                $updates['metadesc'] = json_encode(['tr' => $page->metadesc]);
            }
            
            // Güncellemeler varsa uygula
            if (!empty($updates)) {
                DB::table('pages')
                    ->where('page_id', $page->page_id)
                    ->update($updates);
            }
        }
    }

    /**
     * Reverse migration - JSON'dan string'e geri çevir
     */
    public function down(): void
    {
        // Önce pages tablosu var mı kontrol et
        if (!Schema::hasTable('pages')) {
            return;
        }

        $pages = DB::table('pages')->get();
        
        foreach ($pages as $page) {
            $updates = [];
            
            // JSON title'ı string'e çevir (tr dili tercih)
            if ($page->title && $this->isJson($page->title)) {
                $titleData = json_decode($page->title, true);
                $updates['title'] = $titleData['tr'] ?? $titleData[array_key_first($titleData)] ?? '';
            }
            
            // JSON slug'ı string'e çevir (tr dili tercih)
            if ($page->slug && $this->isJson($page->slug)) {
                $slugData = json_decode($page->slug, true);
                $updates['slug'] = $slugData['tr'] ?? $slugData[array_key_first($slugData)] ?? '';
            }
            
            // JSON body'yi string'e çevir (tr dili tercih)
            if ($page->body && $this->isJson($page->body)) {
                $bodyData = json_decode($page->body, true);
                $updates['body'] = $bodyData['tr'] ?? $bodyData[array_key_first($bodyData)] ?? '';
            }
            
            // JSON metakey'i string'e çevir (tr dili tercih)
            if ($page->metakey && $this->isJson($page->metakey)) {
                $metakeyData = json_decode($page->metakey, true);
                $updates['metakey'] = $metakeyData['tr'] ?? $metakeyData[array_key_first($metakeyData)] ?? '';
            }
            
            // JSON metadesc'i string'e çevir (tr dili tercih)
            if ($page->metadesc && $this->isJson($page->metadesc)) {
                $metadescData = json_decode($page->metadesc, true);
                $updates['metadesc'] = $metadescData['tr'] ?? $metadescData[array_key_first($metadescData)] ?? '';
            }
            
            // Güncellemeler varsa uygula
            if (!empty($updates)) {
                DB::table('pages')
                    ->where('page_id', $page->page_id)
                    ->update($updates);
            }
        }
    }
    
    /**
     * String'in JSON olup olmadığını kontrol et
     */
    private function isJson($string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
};
