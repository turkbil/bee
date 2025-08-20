<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Mevcut string body verilerini JSON'a çevir
        $pages = DB::table('pages')->whereNotNull('body')->get();
        
        foreach ($pages as $page) {
            if (!empty($page->body) && !is_array(json_decode($page->body, true))) {
                // String body'yi TR dilinde JSON array'e çevir
                $jsonBody = [
                    'tr' => $page->body,
                    'en' => '', // Boş placeholder
                    'fr' => '', // Boş placeholder  
                    'de' => '', // Boş placeholder
                    'ja' => ''  // Boş placeholder
                ];
                
                DB::table('pages')
                    ->where('page_id', $page->page_id)
                    ->update(['body' => json_encode($jsonBody)]);
            }
        }
        
        // 2. CSS ve JS alanlarını da JSON'a çevir (eğer gerekirse)
        $pagesWithCss = DB::table('pages')->whereNotNull('css')->get();
        
        foreach ($pagesWithCss as $page) {
            if (!empty($page->css) && !is_array(json_decode($page->css, true))) {
                $jsonCss = [
                    'tr' => $page->css,
                    'en' => '',
                    'fr' => '',
                    'de' => '',
                    'ja' => ''
                ];
                
                DB::table('pages')
                    ->where('page_id', $page->page_id)
                    ->update(['css' => json_encode($jsonCss)]);
            }
        }
        
        $pagesWithJs = DB::table('pages')->whereNotNull('js')->get();
        
        foreach ($pagesWithJs as $page) {
            if (!empty($page->js) && !is_array(json_decode($page->js, true))) {
                $jsonJs = [
                    'tr' => $page->js,
                    'en' => '',
                    'fr' => '',
                    'de' => '',
                    'ja' => ''
                ];
                
                DB::table('pages')
                    ->where('page_id', $page->page_id)
                    ->update(['js' => json_encode($jsonJs)]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Geri alma: JSON'u string'e çevir (sadece TR dilini al)
        $pages = DB::table('pages')->whereNotNull('body')->get();
        
        foreach ($pages as $page) {
            $bodyArray = json_decode($page->body, true);
            if (is_array($bodyArray) && isset($bodyArray['tr'])) {
                DB::table('pages')
                    ->where('page_id', $page->page_id)
                    ->update(['body' => $bodyArray['tr']]);
            }
        }
        
        // CSS ve JS için de aynı işlem
        $pagesWithCss = DB::table('pages')->whereNotNull('css')->get();
        foreach ($pagesWithCss as $page) {
            $cssArray = json_decode($page->css, true);
            if (is_array($cssArray) && isset($cssArray['tr'])) {
                DB::table('pages')
                    ->where('page_id', $page->page_id)
                    ->update(['css' => $cssArray['tr']]);
            }
        }
        
        $pagesWithJs = DB::table('pages')->whereNotNull('js')->get();
        foreach ($pagesWithJs as $page) {
            $jsArray = json_decode($page->js, true);
            if (is_array($jsArray) && isset($jsArray['tr'])) {
                DB::table('pages')
                    ->where('page_id', $page->page_id)
                    ->update(['js' => $jsArray['tr']]);
            }
        }
    }
};
