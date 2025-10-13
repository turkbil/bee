<?php

namespace Modules\Shop\Database\Seeders\LitefSpareParts;

use Illuminate\Database\Seeder;
use Modules\Shop\App\Models\ShopCategory;

class LitefSparePartsCategoriesSeeder extends Seeder
{
    private $categoryMapping = [];

    public function run(): void
    {

        // Kategori: YEDEK PARÇA (ID: 50)
        $existing = ShopCategory::where('slug->tr', 'yedek-parca')->first();
        if (!$existing) {
            $cat50 = ShopCategory::create([
                'parent_id' => null,
                'title' => json_encode(['tr' => 'YEDEK PARÇA']),
                'slug' => json_encode(['tr' => 'yedek-parca']),
                'description' => json_encode(['tr' => '']),
                'image_url' => 'tcm-dizel-forkliftler-panel.jpg',
                'level' => 1,
                'sort_order' => 27,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[50] = $cat50->category_id;
        } else {
            $this->categoryMapping[50] = $existing->category_id;
        }

        // Kategori: Lastik Jant Teker (ID: 51)
        $existing = ShopCategory::where('slug->tr', 'lastik-jant-teker')->first();
        if (!$existing) {
            $cat51 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[50] ?? null,
                'title' => json_encode(['tr' => 'Lastik Jant Teker']),
                'slug' => json_encode(['tr' => 'lastik-jant-teker']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 28,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[51] = $cat51->category_id;
        } else {
            $this->categoryMapping[51] = $existing->category_id;
        }

        // Kategori: Motor Grubu (ID: 63)
        $existing = ShopCategory::where('slug->tr', 'motor-grubu')->first();
        if (!$existing) {
            $cat63 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[50] ?? null,
                'title' => json_encode(['tr' => 'Motor Grubu']),
                'slug' => json_encode(['tr' => 'motor-grubu']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 40,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[63] = $cat63->category_id;
        } else {
            $this->categoryMapping[63] = $existing->category_id;
        }

        // Kategori: Çatal / Ataşman (ID: 64)
        $existing = ShopCategory::where('slug->tr', 'catal-atasman')->first();
        if (!$existing) {
            $cat64 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[50] ?? null,
                'title' => json_encode(['tr' => 'Çatal / Ataşman']),
                'slug' => json_encode(['tr' => 'catal-atasman']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 51,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[64] = $cat64->category_id;
        } else {
            $this->categoryMapping[64] = $existing->category_id;
        }

        // Kategori: Şanzıman Parçaları (ID: 65)
        $existing = ShopCategory::where('slug->tr', 'sanziman-parcalari')->first();
        if (!$existing) {
            $cat65 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[50] ?? null,
                'title' => json_encode(['tr' => 'Şanzıman Parçaları']),
                'slug' => json_encode(['tr' => 'sanziman-parcalari']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 54,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[65] = $cat65->category_id;
        } else {
            $this->categoryMapping[65] = $existing->category_id;
        }

        // Kategori: Keçeler (ID: 66)
        $existing = ShopCategory::where('slug->tr', 'keceler')->first();
        if (!$existing) {
            $cat66 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[50] ?? null,
                'title' => json_encode(['tr' => 'Keçeler']),
                'slug' => json_encode(['tr' => 'keceler']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 62,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[66] = $cat66->category_id;
        } else {
            $this->categoryMapping[66] = $existing->category_id;
        }

        // Kategori: Direksiyon Grubu (ID: 67)
        $existing = ShopCategory::where('slug->tr', 'direksiyon-grubu')->first();
        if (!$existing) {
            $cat67 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[50] ?? null,
                'title' => json_encode(['tr' => 'Direksiyon Grubu']),
                'slug' => json_encode(['tr' => 'direksiyon-grubu']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 63,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[67] = $cat67->category_id;
        } else {
            $this->categoryMapping[67] = $existing->category_id;
        }

        // Kategori: Asansör Grubu (ID: 68)
        $existing = ShopCategory::where('slug->tr', 'asansor-grubu')->first();
        if (!$existing) {
            $cat68 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[50] ?? null,
                'title' => json_encode(['tr' => 'Asansör Grubu']),
                'slug' => json_encode(['tr' => 'asansor-grubu']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 68,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[68] = $cat68->category_id;
        } else {
            $this->categoryMapping[68] = $existing->category_id;
        }

        // Kategori: Rulman Grubu (ID: 69)
        $existing = ShopCategory::where('slug->tr', 'rulman-grubu')->first();
        if (!$existing) {
            $cat69 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[50] ?? null,
                'title' => json_encode(['tr' => 'Rulman Grubu']),
                'slug' => json_encode(['tr' => 'rulman-grubu']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 78,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[69] = $cat69->category_id;
        } else {
            $this->categoryMapping[69] = $existing->category_id;
        }

        // Kategori: Elektrik / Elektronik / Akü (ID: 70)
        $existing = ShopCategory::where('slug->tr', 'elektrik-elektronik-aku')->first();
        if (!$existing) {
            $cat70 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[50] ?? null,
                'title' => json_encode(['tr' => 'Elektrik / Elektronik / Akü']),
                'slug' => json_encode(['tr' => 'elektrik-elektronik-aku']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 87,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[70] = $cat70->category_id;
        } else {
            $this->categoryMapping[70] = $existing->category_id;
        }

        // Kategori: Forklift Dingil Parçaları (ID: 71)
        $existing = ShopCategory::where('slug->tr', 'forklift-dingil-parcalari')->first();
        if (!$existing) {
            $cat71 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[50] ?? null,
                'title' => json_encode(['tr' => 'Forklift Dingil Parçaları']),
                'slug' => json_encode(['tr' => 'forklift-dingil-parcalari']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 97,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[71] = $cat71->category_id;
        } else {
            $this->categoryMapping[71] = $existing->category_id;
        }

        // Kategori: Forklift Aksesuarları (ID: 72)
        $existing = ShopCategory::where('slug->tr', 'forklift-aksesuarlari')->first();
        if (!$existing) {
            $cat72 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[50] ?? null,
                'title' => json_encode(['tr' => 'Forklift Aksesuarları']),
                'slug' => json_encode(['tr' => 'forklift-aksesuarlari']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 107,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[72] = $cat72->category_id;
        } else {
            $this->categoryMapping[72] = $existing->category_id;
        }

        // Kategori: Pompalar (ID: 73)
        $existing = ShopCategory::where('slug->tr', 'pompalar')->first();
        if (!$existing) {
            $cat73 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[50] ?? null,
                'title' => json_encode(['tr' => 'Pompalar']),
                'slug' => json_encode(['tr' => 'pompalar']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 112,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[73] = $cat73->category_id;
        } else {
            $this->categoryMapping[73] = $existing->category_id;
        }

        // Kategori: Fren Grubu (ID: 74)
        $existing = ShopCategory::where('slug->tr', 'fren-grubu')->first();
        if (!$existing) {
            $cat74 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[50] ?? null,
                'title' => json_encode(['tr' => 'Fren Grubu']),
                'slug' => json_encode(['tr' => 'fren-grubu']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 117,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[74] = $cat74->category_id;
        } else {
            $this->categoryMapping[74] = $existing->category_id;
        }

        // Kategori: Filtreler (ID: 75)
        $existing = ShopCategory::where('slug->tr', 'filtreler')->first();
        if (!$existing) {
            $cat75 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[50] ?? null,
                'title' => json_encode(['tr' => 'Filtreler']),
                'slug' => json_encode(['tr' => 'filtreler']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 123,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[75] = $cat75->category_id;
        } else {
            $this->categoryMapping[75] = $existing->category_id;
        }

        // Kategori: Siyah Dolgu Lastik (ID: 52)
        $existing = ShopCategory::where('slug->tr', 'siyah-dolgu-lastik')->first();
        if (!$existing) {
            $cat52 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[51] ?? null,
                'title' => json_encode(['tr' => 'Siyah Dolgu Lastik']),
                'slug' => json_encode(['tr' => 'siyah-dolgu-lastik']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 29,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[52] = $cat52->category_id;
        } else {
            $this->categoryMapping[52] = $existing->category_id;
        }

        // Kategori: Beyaz Dolgu Lastik (ID: 53)
        $existing = ShopCategory::where('slug->tr', 'beyaz-dolgu-lastik')->first();
        if (!$existing) {
            $cat53 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[51] ?? null,
                'title' => json_encode(['tr' => 'Beyaz Dolgu Lastik']),
                'slug' => json_encode(['tr' => 'beyaz-dolgu-lastik']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 30,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[53] = $cat53->category_id;
        } else {
            $this->categoryMapping[53] = $existing->category_id;
        }

        // Kategori: Havalı Lastik (ID: 54)
        $existing = ShopCategory::where('slug->tr', 'havali-lastik')->first();
        if (!$existing) {
            $cat54 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[51] ?? null,
                'title' => json_encode(['tr' => 'Havalı Lastik']),
                'slug' => json_encode(['tr' => 'havali-lastik']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 31,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[54] = $cat54->category_id;
        } else {
            $this->categoryMapping[54] = $existing->category_id;
        }

        // Kategori: İç Lastik Şambrel (ID: 55)
        $existing = ShopCategory::where('slug->tr', 'ic-lastik-sambrel')->first();
        if (!$existing) {
            $cat55 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[51] ?? null,
                'title' => json_encode(['tr' => 'İç Lastik Şambrel']),
                'slug' => json_encode(['tr' => 'ic-lastik-sambrel']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 32,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[55] = $cat55->category_id;
        } else {
            $this->categoryMapping[55] = $existing->category_id;
        }

        // Kategori:  Jant - Segman (ID: 56)
        $existing = ShopCategory::where('slug->tr', 'jant-segman')->first();
        if (!$existing) {
            $cat56 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[51] ?? null,
                'title' => json_encode(['tr' => ' Jant - Segman']),
                'slug' => json_encode(['tr' => 'jant-segman']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 33,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[56] = $cat56->category_id;
        } else {
            $this->categoryMapping[56] = $existing->category_id;
        }

        // Kategori: Denge Tekeri (ID: 57)
        $existing = ShopCategory::where('slug->tr', 'denge-tekeri')->first();
        if (!$existing) {
            $cat57 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[51] ?? null,
                'title' => json_encode(['tr' => 'Denge Tekeri']),
                'slug' => json_encode(['tr' => 'denge-tekeri']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 34,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[57] = $cat57->category_id;
        } else {
            $this->categoryMapping[57] = $existing->category_id;
        }

        // Kategori: Çatal Tekeri (ID: 58)
        $existing = ShopCategory::where('slug->tr', 'catal-tekeri')->first();
        if (!$existing) {
            $cat58 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[51] ?? null,
                'title' => json_encode(['tr' => 'Çatal Tekeri']),
                'slug' => json_encode(['tr' => 'catal-tekeri']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 35,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[58] = $cat58->category_id;
        } else {
            $this->categoryMapping[58] = $existing->category_id;
        }

        // Kategori: Yürüyüş Tekeri (ID: 59)
        $existing = ShopCategory::where('slug->tr', 'yuruyus-tekeri')->first();
        if (!$existing) {
            $cat59 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[51] ?? null,
                'title' => json_encode(['tr' => 'Yürüyüş Tekeri']),
                'slug' => json_encode(['tr' => 'yuruyus-tekeri']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 36,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[59] = $cat59->category_id;
        } else {
            $this->categoryMapping[59] = $existing->category_id;
        }

        // Kategori: Platform Tekeri (ID: 60)
        $existing = ShopCategory::where('slug->tr', 'platform-tekeri')->first();
        if (!$existing) {
            $cat60 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[51] ?? null,
                'title' => json_encode(['tr' => 'Platform Tekeri']),
                'slug' => json_encode(['tr' => 'platform-tekeri']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 37,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[60] = $cat60->category_id;
        } else {
            $this->categoryMapping[60] = $existing->category_id;
        }

        // Kategori: Transpalet Tekeri (ID: 61)
        $existing = ShopCategory::where('slug->tr', 'transpalet-tekeri')->first();
        if (!$existing) {
            $cat61 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[51] ?? null,
                'title' => json_encode(['tr' => 'Transpalet Tekeri']),
                'slug' => json_encode(['tr' => 'transpalet-tekeri']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 38,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[61] = $cat61->category_id;
        } else {
            $this->categoryMapping[61] = $existing->category_id;
        }

        // Kategori: Bijon (ID: 62)
        $existing = ShopCategory::where('slug->tr', 'bijon')->first();
        if (!$existing) {
            $cat62 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[51] ?? null,
                'title' => json_encode(['tr' => 'Bijon']),
                'slug' => json_encode(['tr' => 'bijon']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 39,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[62] = $cat62->category_id;
        } else {
            $this->categoryMapping[62] = $existing->category_id;
        }

        // Kategori: Devirdaim (ID: 76)
        $existing = ShopCategory::where('slug->tr', 'devirdaim')->first();
        if (!$existing) {
            $cat76 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[63] ?? null,
                'title' => json_encode(['tr' => 'Devirdaim']),
                'slug' => json_encode(['tr' => 'devirdaim']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 41,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[76] = $cat76->category_id;
        } else {
            $this->categoryMapping[76] = $existing->category_id;
        }

        // Kategori: Yakıt Otomatiği (ID: 77)
        $existing = ShopCategory::where('slug->tr', 'yakit-otomatigi')->first();
        if (!$existing) {
            $cat77 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[63] ?? null,
                'title' => json_encode(['tr' => 'Yakıt Otomatiği']),
                'slug' => json_encode(['tr' => 'yakit-otomatigi']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 42,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[77] = $cat77->category_id;
        } else {
            $this->categoryMapping[77] = $existing->category_id;
        }

        // Kategori: Pervane (ID: 78)
        $existing = ShopCategory::where('slug->tr', 'pervane')->first();
        if (!$existing) {
            $cat78 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[63] ?? null,
                'title' => json_encode(['tr' => 'Pervane']),
                'slug' => json_encode(['tr' => 'pervane']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 43,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[78] = $cat78->category_id;
        } else {
            $this->categoryMapping[78] = $existing->category_id;
        }

        // Kategori: Şamandıra (ID: 79)
        $existing = ShopCategory::where('slug->tr', 'samandira')->first();
        if (!$existing) {
            $cat79 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[63] ?? null,
                'title' => json_encode(['tr' => 'Şamandıra']),
                'slug' => json_encode(['tr' => 'samandira']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 44,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[79] = $cat79->category_id;
        } else {
            $this->categoryMapping[79] = $existing->category_id;
        }

        // Kategori: Silindir Kapak Contası (ID: 80)
        $existing = ShopCategory::where('slug->tr', 'silindir-kapak-contasi')->first();
        if (!$existing) {
            $cat80 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[63] ?? null,
                'title' => json_encode(['tr' => 'Silindir Kapak Contası']),
                'slug' => json_encode(['tr' => 'silindir-kapak-contasi']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 45,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[80] = $cat80->category_id;
        } else {
            $this->categoryMapping[80] = $existing->category_id;
        }

        // Kategori: Piston (ID: 81)
        $existing = ShopCategory::where('slug->tr', 'piston')->first();
        if (!$existing) {
            $cat81 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[63] ?? null,
                'title' => json_encode(['tr' => 'Piston']),
                'slug' => json_encode(['tr' => 'piston']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 46,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[81] = $cat81->category_id;
        } else {
            $this->categoryMapping[81] = $existing->category_id;
        }

        // Kategori: Piston Kolu (ID: 82)
        $existing = ShopCategory::where('slug->tr', 'piston-kolu')->first();
        if (!$existing) {
            $cat82 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[63] ?? null,
                'title' => json_encode(['tr' => 'Piston Kolu']),
                'slug' => json_encode(['tr' => 'piston-kolu']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 47,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[82] = $cat82->category_id;
        } else {
            $this->categoryMapping[82] = $existing->category_id;
        }

        // Kategori: Kayış (ID: 83)
        $existing = ShopCategory::where('slug->tr', 'kayis')->first();
        if (!$existing) {
            $cat83 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[63] ?? null,
                'title' => json_encode(['tr' => 'Kayış']),
                'slug' => json_encode(['tr' => 'kayis']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 48,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[83] = $cat83->category_id;
        } else {
            $this->categoryMapping[83] = $existing->category_id;
        }

        // Kategori: Volant Dişlisi (ID: 84)
        $existing = ShopCategory::where('slug->tr', 'volant-dislisi')->first();
        if (!$existing) {
            $cat84 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[63] ?? null,
                'title' => json_encode(['tr' => 'Volant Dişlisi']),
                'slug' => json_encode(['tr' => 'volant-dislisi']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 49,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[84] = $cat84->category_id;
        } else {
            $this->categoryMapping[84] = $existing->category_id;
        }

        // Kategori: Motor Yatağı (ID: 85)
        $existing = ShopCategory::where('slug->tr', 'motor-yatagi')->first();
        if (!$existing) {
            $cat85 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[63] ?? null,
                'title' => json_encode(['tr' => 'Motor Yatağı']),
                'slug' => json_encode(['tr' => 'motor-yatagi']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 50,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[85] = $cat85->category_id;
        } else {
            $this->categoryMapping[85] = $existing->category_id;
        }

        // Kategori: Çatal (ID: 86)
        $existing = ShopCategory::where('slug->tr', 'catal')->first();
        if (!$existing) {
            $cat86 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[64] ?? null,
                'title' => json_encode(['tr' => 'Çatal']),
                'slug' => json_encode(['tr' => 'catal']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 52,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[86] = $cat86->category_id;
        } else {
            $this->categoryMapping[86] = $existing->category_id;
        }

        // Kategori: Çatal Kılıf (ID: 87)
        $existing = ShopCategory::where('slug->tr', 'catal-kilif')->first();
        if (!$existing) {
            $cat87 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[64] ?? null,
                'title' => json_encode(['tr' => 'Çatal Kılıf']),
                'slug' => json_encode(['tr' => 'catal-kilif']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 53,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[87] = $cat87->category_id;
        } else {
            $this->categoryMapping[87] = $existing->category_id;
        }

        // Kategori: Segman Takımı (ID: 88)
        $existing = ShopCategory::where('slug->tr', 'segman-takimi')->first();
        if (!$existing) {
            $cat88 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[65] ?? null,
                'title' => json_encode(['tr' => 'Segman Takımı']),
                'slug' => json_encode(['tr' => 'segman-takimi']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 55,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[88] = $cat88->category_id;
        } else {
            $this->categoryMapping[88] = $existing->category_id;
        }

        // Kategori: Çelik Plate (ID: 89)
        $existing = ShopCategory::where('slug->tr', 'celik-plate')->first();
        if (!$existing) {
            $cat89 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[65] ?? null,
                'title' => json_encode(['tr' => 'Çelik Plate']),
                'slug' => json_encode(['tr' => 'celik-plate']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 56,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[89] = $cat89->category_id;
        } else {
            $this->categoryMapping[89] = $existing->category_id;
        }

        // Kategori: Balata (ID: 90)
        $existing = ShopCategory::where('slug->tr', 'balata')->first();
        if (!$existing) {
            $cat90 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[65] ?? null,
                'title' => json_encode(['tr' => 'Balata']),
                'slug' => json_encode(['tr' => 'balata']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 57,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[90] = $cat90->category_id;
        } else {
            $this->categoryMapping[90] = $existing->category_id;
        }

        // Kategori: Tork Converter (ID: 91)
        $existing = ShopCategory::where('slug->tr', 'tork-converter')->first();
        if (!$existing) {
            $cat91 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[65] ?? null,
                'title' => json_encode(['tr' => 'Tork Converter']),
                'slug' => json_encode(['tr' => 'tork-converter']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 58,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[91] = $cat91->category_id;
        } else {
            $this->categoryMapping[91] = $existing->category_id;
        }

        // Kategori: Tork Sacı (ID: 92)
        $existing = ShopCategory::where('slug->tr', 'tork-saci')->first();
        if (!$existing) {
            $cat92 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[65] ?? null,
                'title' => json_encode(['tr' => 'Tork Sacı']),
                'slug' => json_encode(['tr' => 'tork-saci']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 59,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[92] = $cat92->category_id;
        } else {
            $this->categoryMapping[92] = $existing->category_id;
        }

        // Kategori: Şanzıman Şaftı (ID: 93)
        $existing = ShopCategory::where('slug->tr', 'sanziman-safti')->first();
        if (!$existing) {
            $cat93 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[65] ?? null,
                'title' => json_encode(['tr' => 'Şanzıman Şaftı']),
                'slug' => json_encode(['tr' => 'sanziman-safti']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 60,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[93] = $cat93->category_id;
        } else {
            $this->categoryMapping[93] = $existing->category_id;
        }

        // Kategori: Şanzıman Şaft Dişlisi (ID: 94)
        $existing = ShopCategory::where('slug->tr', 'sanziman-saft-dislisi')->first();
        if (!$existing) {
            $cat94 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[65] ?? null,
                'title' => json_encode(['tr' => 'Şanzıman Şaft Dişlisi']),
                'slug' => json_encode(['tr' => 'sanziman-saft-dislisi']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 61,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[94] = $cat94->category_id;
        } else {
            $this->categoryMapping[94] = $existing->category_id;
        }

        // Kategori: Direksiyon Simidi (ID: 95)
        $existing = ShopCategory::where('slug->tr', 'direksiyon-simidi')->first();
        if (!$existing) {
            $cat95 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[67] ?? null,
                'title' => json_encode(['tr' => 'Direksiyon Simidi']),
                'slug' => json_encode(['tr' => 'direksiyon-simidi']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 64,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[95] = $cat95->category_id;
        } else {
            $this->categoryMapping[95] = $existing->category_id;
        }

        // Kategori: Direksiyon Mafsalı (ID: 96)
        $existing = ShopCategory::where('slug->tr', 'direksiyon-mafsali')->first();
        if (!$existing) {
            $cat96 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[67] ?? null,
                'title' => json_encode(['tr' => 'Direksiyon Mafsalı']),
                'slug' => json_encode(['tr' => 'direksiyon-mafsali']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 65,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[96] = $cat96->category_id;
        } else {
            $this->categoryMapping[96] = $existing->category_id;
        }

        // Kategori: Direksiyon Mili (ID: 97)
        $existing = ShopCategory::where('slug->tr', 'direksiyon-mili')->first();
        if (!$existing) {
            $cat97 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[67] ?? null,
                'title' => json_encode(['tr' => 'Direksiyon Mili']),
                'slug' => json_encode(['tr' => 'direksiyon-mili']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 66,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[97] = $cat97->category_id;
        } else {
            $this->categoryMapping[97] = $existing->category_id;
        }

        // Kategori: Direksiyon Danfozu (ID: 98)
        $existing = ShopCategory::where('slug->tr', 'direksiyon-danfozu')->first();
        if (!$existing) {
            $cat98 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[67] ?? null,
                'title' => json_encode(['tr' => 'Direksiyon Danfozu']),
                'slug' => json_encode(['tr' => 'direksiyon-danfozu']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 67,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[98] = $cat98->category_id;
        } else {
            $this->categoryMapping[98] = $existing->category_id;
        }

        // Kategori: Kızak Takımı (ID: 99)
        $existing = ShopCategory::where('slug->tr', 'kizak-takimi')->first();
        if (!$existing) {
            $cat99 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[68] ?? null,
                'title' => json_encode(['tr' => 'Kızak Takımı']),
                'slug' => json_encode(['tr' => 'kizak-takimi']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 69,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[99] = $cat99->category_id;
        } else {
            $this->categoryMapping[99] = $existing->category_id;
        }

        // Kategori: Side Shift Kızak (ID: 100)
        $existing = ShopCategory::where('slug->tr', 'side-shift-kizak')->first();
        if (!$existing) {
            $cat100 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[68] ?? null,
                'title' => json_encode(['tr' => 'Side Shift Kızak']),
                'slug' => json_encode(['tr' => 'side-shift-kizak']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 70,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[100] = $cat100->category_id;
        } else {
            $this->categoryMapping[100] = $existing->category_id;
        }

        // Kategori: Asansör Ayar Teflonu (ID: 101)
        $existing = ShopCategory::where('slug->tr', 'asansor-ayar-teflonu')->first();
        if (!$existing) {
            $cat101 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[68] ?? null,
                'title' => json_encode(['tr' => 'Asansör Ayar Teflonu']),
                'slug' => json_encode(['tr' => 'asansor-ayar-teflonu']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 71,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[101] = $cat101->category_id;
        } else {
            $this->categoryMapping[101] = $existing->category_id;
        }

        // Kategori: Asansör Kep Burcu (ID: 102)
        $existing = ShopCategory::where('slug->tr', 'asansor-kep-burcu')->first();
        if (!$existing) {
            $cat102 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[68] ?? null,
                'title' => json_encode(['tr' => 'Asansör Kep Burcu']),
                'slug' => json_encode(['tr' => 'asansor-kep-burcu']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 72,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[102] = $cat102->category_id;
        } else {
            $this->categoryMapping[102] = $existing->category_id;
        }

        // Kategori: Makara (ID: 103)
        $existing = ShopCategory::where('slug->tr', 'makara')->first();
        if (!$existing) {
            $cat103 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[68] ?? null,
                'title' => json_encode(['tr' => 'Makara']),
                'slug' => json_encode(['tr' => 'makara']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 73,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[103] = $cat103->category_id;
        } else {
            $this->categoryMapping[103] = $existing->category_id;
        }

        // Kategori: Tilt Silindir Mili (ID: 104)
        $existing = ShopCategory::where('slug->tr', 'tilt-silindir-mili')->first();
        if (!$existing) {
            $cat104 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[68] ?? null,
                'title' => json_encode(['tr' => 'Tilt Silindir Mili']),
                'slug' => json_encode(['tr' => 'tilt-silindir-mili']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 74,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[104] = $cat104->category_id;
        } else {
            $this->categoryMapping[104] = $existing->category_id;
        }

        // Kategori: Tilt Pistonu (ID: 105)
        $existing = ShopCategory::where('slug->tr', 'tilt-pistonu')->first();
        if (!$existing) {
            $cat105 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[68] ?? null,
                'title' => json_encode(['tr' => 'Tilt Pistonu']),
                'slug' => json_encode(['tr' => 'tilt-pistonu']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 75,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[105] = $cat105->category_id;
        } else {
            $this->categoryMapping[105] = $existing->category_id;
        }

        // Kategori: Side Shift Pistonu (ID: 106)
        $existing = ShopCategory::where('slug->tr', 'side-shift-pistonu')->first();
        if (!$existing) {
            $cat106 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[68] ?? null,
                'title' => json_encode(['tr' => 'Side Shift Pistonu']),
                'slug' => json_encode(['tr' => 'side-shift-pistonu']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 76,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[106] = $cat106->category_id;
        } else {
            $this->categoryMapping[106] = $existing->category_id;
        }

        // Kategori: Zincir (ID: 107)
        $existing = ShopCategory::where('slug->tr', 'zincir')->first();
        if (!$existing) {
            $cat107 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[68] ?? null,
                'title' => json_encode(['tr' => 'Zincir']),
                'slug' => json_encode(['tr' => 'zincir']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 77,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[107] = $cat107->category_id;
        } else {
            $this->categoryMapping[107] = $existing->category_id;
        }

        // Kategori: Rulman Ayna (ID: 108)
        $existing = ShopCategory::where('slug->tr', 'rulman-ayna')->first();
        if (!$existing) {
            $cat108 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[69] ?? null,
                'title' => json_encode(['tr' => 'Rulman Ayna']),
                'slug' => json_encode(['tr' => 'rulman-ayna']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 79,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[108] = $cat108->category_id;
        } else {
            $this->categoryMapping[108] = $existing->category_id;
        }

        // Kategori: Rulman Asansör (ID: 109)
        $existing = ShopCategory::where('slug->tr', 'rulman-asansor')->first();
        if (!$existing) {
            $cat109 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[69] ?? null,
                'title' => json_encode(['tr' => 'Rulman Asansör']),
                'slug' => json_encode(['tr' => 'rulman-asansor']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 80,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[109] = $cat109->category_id;
        } else {
            $this->categoryMapping[109] = $existing->category_id;
        }

        // Kategori: Porya Rulman (ID: 110)
        $existing = ShopCategory::where('slug->tr', 'porya-rulman')->first();
        if (!$existing) {
            $cat110 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[69] ?? null,
                'title' => json_encode(['tr' => 'Porya Rulman']),
                'slug' => json_encode(['tr' => 'porya-rulman']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 81,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[110] = $cat110->category_id;
        } else {
            $this->categoryMapping[110] = $existing->category_id;
        }

        // Kategori: Kilit Bilya (ID: 111)
        $existing = ShopCategory::where('slug->tr', 'kilit-bilya')->first();
        if (!$existing) {
            $cat111 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[69] ?? null,
                'title' => json_encode(['tr' => 'Kilit Bilya']),
                'slug' => json_encode(['tr' => 'kilit-bilya']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 82,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[111] = $cat111->category_id;
        } else {
            $this->categoryMapping[111] = $existing->category_id;
        }

        // Kategori: Rulman Zarfı (ID: 112)
        $existing = ShopCategory::where('slug->tr', 'rulman-zarfi')->first();
        if (!$existing) {
            $cat112 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[69] ?? null,
                'title' => json_encode(['tr' => 'Rulman Zarfı']),
                'slug' => json_encode(['tr' => 'rulman-zarfi']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 83,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[112] = $cat112->category_id;
        } else {
            $this->categoryMapping[112] = $existing->category_id;
        }

        // Kategori: Rulman Taşıyıcı (ID: 113)
        $existing = ShopCategory::where('slug->tr', 'rulman-tasiyici')->first();
        if (!$existing) {
            $cat113 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[69] ?? null,
                'title' => json_encode(['tr' => 'Rulman Taşıyıcı']),
                'slug' => json_encode(['tr' => 'rulman-tasiyici']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 84,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[113] = $cat113->category_id;
        } else {
            $this->categoryMapping[113] = $existing->category_id;
        }

        // Kategori: Denge Rulmanı (ID: 115)
        $existing = ShopCategory::where('slug->tr', 'denge-rulmani')->first();
        if (!$existing) {
            $cat115 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[69] ?? null,
                'title' => json_encode(['tr' => 'Denge Rulmanı']),
                'slug' => json_encode(['tr' => 'denge-rulmani']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 85,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[115] = $cat115->category_id;
        } else {
            $this->categoryMapping[115] = $existing->category_id;
        }

        // Kategori: İğneli Rulman (ID: 116)
        $existing = ShopCategory::where('slug->tr', 'igneli-rulman')->first();
        if (!$existing) {
            $cat116 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[69] ?? null,
                'title' => json_encode(['tr' => 'İğneli Rulman']),
                'slug' => json_encode(['tr' => 'igneli-rulman']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 86,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[116] = $cat116->category_id;
        } else {
            $this->categoryMapping[116] = $existing->category_id;
        }

        // Kategori: Akü Soketi (ID: 117)
        $existing = ShopCategory::where('slug->tr', 'aku-soketi')->first();
        if (!$existing) {
            $cat117 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[70] ?? null,
                'title' => json_encode(['tr' => 'Akü Soketi']),
                'slug' => json_encode(['tr' => 'aku-soketi']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 88,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[117] = $cat117->category_id;
        } else {
            $this->categoryMapping[117] = $existing->category_id;
        }

        // Kategori: Ampul (ID: 118)
        $existing = ShopCategory::where('slug->tr', 'ampul')->first();
        if (!$existing) {
            $cat118 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[70] ?? null,
                'title' => json_encode(['tr' => 'Ampul']),
                'slug' => json_encode(['tr' => 'ampul']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 89,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[118] = $cat118->category_id;
        } else {
            $this->categoryMapping[118] = $existing->category_id;
        }

        // Kategori: Kontaktör (ID: 119)
        $existing = ShopCategory::where('slug->tr', 'kontaktor')->first();
        if (!$existing) {
            $cat119 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[70] ?? null,
                'title' => json_encode(['tr' => 'Kontaktör']),
                'slug' => json_encode(['tr' => 'kontaktor']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 90,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[119] = $cat119->category_id;
        } else {
            $this->categoryMapping[119] = $existing->category_id;
        }

        // Kategori: Konvektör (ID: 120)
        $existing = ShopCategory::where('slug->tr', 'konvektor')->first();
        if (!$existing) {
            $cat120 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[70] ?? null,
                'title' => json_encode(['tr' => 'Konvektör']),
                'slug' => json_encode(['tr' => 'konvektor']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 91,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[120] = $cat120->category_id;
        } else {
            $this->categoryMapping[120] = $existing->category_id;
        }

        // Kategori: Far (ID: 121)
        $existing = ShopCategory::where('slug->tr', 'far')->first();
        if (!$existing) {
            $cat121 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[70] ?? null,
                'title' => json_encode(['tr' => 'Far']),
                'slug' => json_encode(['tr' => 'far']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 92,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[121] = $cat121->category_id;
        } else {
            $this->categoryMapping[121] = $existing->category_id;
        }

        // Kategori: Termostat (ID: 122)
        $existing = ShopCategory::where('slug->tr', 'termostat')->first();
        if (!$existing) {
            $cat122 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[70] ?? null,
                'title' => json_encode(['tr' => 'Termostat']),
                'slug' => json_encode(['tr' => 'termostat']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 93,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[122] = $cat122->category_id;
        } else {
            $this->categoryMapping[122] = $existing->category_id;
        }

        // Kategori: Korna (ID: 123)
        $existing = ShopCategory::where('slug->tr', 'korna')->first();
        if (!$existing) {
            $cat123 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[70] ?? null,
                'title' => json_encode(['tr' => 'Korna']),
                'slug' => json_encode(['tr' => 'korna']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 94,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[123] = $cat123->category_id;
        } else {
            $this->categoryMapping[123] = $existing->category_id;
        }

        // Kategori: Fan (ID: 124)
        $existing = ShopCategory::where('slug->tr', 'fan')->first();
        if (!$existing) {
            $cat124 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[70] ?? null,
                'title' => json_encode(['tr' => 'Fan']),
                'slug' => json_encode(['tr' => 'fan']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 95,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[124] = $cat124->category_id;
        } else {
            $this->categoryMapping[124] = $existing->category_id;
        }

        // Kategori: Vites - Sinyal Kolu (ID: 125)
        $existing = ShopCategory::where('slug->tr', 'vites-sinyal-kolu')->first();
        if (!$existing) {
            $cat125 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[70] ?? null,
                'title' => json_encode(['tr' => 'Vites - Sinyal Kolu']),
                'slug' => json_encode(['tr' => 'vites-sinyal-kolu']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 96,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[125] = $cat125->category_id;
        } else {
            $this->categoryMapping[125] = $existing->category_id;
        }

        // Kategori: Link Pimi (ID: 126)
        $existing = ShopCategory::where('slug->tr', 'link-pimi')->first();
        if (!$existing) {
            $cat126 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[71] ?? null,
                'title' => json_encode(['tr' => 'Link Pimi']),
                'slug' => json_encode(['tr' => 'link-pimi']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 98,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[126] = $cat126->category_id;
        } else {
            $this->categoryMapping[126] = $existing->category_id;
        }

        // Kategori: Link (ID: 127)
        $existing = ShopCategory::where('slug->tr', 'link')->first();
        if (!$existing) {
            $cat127 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[71] ?? null,
                'title' => json_encode(['tr' => 'Link']),
                'slug' => json_encode(['tr' => 'link']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 99,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[127] = $cat127->category_id;
        } else {
            $this->categoryMapping[127] = $existing->category_id;
        }

        // Kategori: Akson (ID: 128)
        $existing = ShopCategory::where('slug->tr', 'akson')->first();
        if (!$existing) {
            $cat128 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[71] ?? null,
                'title' => json_encode(['tr' => 'Akson']),
                'slug' => json_encode(['tr' => 'akson']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 100,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[128] = $cat128->category_id;
        } else {
            $this->categoryMapping[128] = $existing->category_id;
        }

        // Kategori: Akson Kapağı (ID: 129)
        $existing = ShopCategory::where('slug->tr', 'akson-kapagi')->first();
        if (!$existing) {
            $cat129 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[71] ?? null,
                'title' => json_encode(['tr' => 'Akson Kapağı']),
                'slug' => json_encode(['tr' => 'akson-kapagi']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 101,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[129] = $cat129->category_id;
        } else {
            $this->categoryMapping[129] = $existing->category_id;
        }

        // Kategori: Yönlü Plaka (ID: 130)
        $existing = ShopCategory::where('slug->tr', 'yonlu-plaka')->first();
        if (!$existing) {
            $cat130 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[71] ?? null,
                'title' => json_encode(['tr' => 'Yönlü Plaka']),
                'slug' => json_encode(['tr' => 'yonlu-plaka']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 102,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[130] = $cat130->category_id;
        } else {
            $this->categoryMapping[130] = $existing->category_id;
        }

        // Kategori: Dingil Askı Burcu (ID: 131)
        $existing = ShopCategory::where('slug->tr', 'dingil-aski-burcu')->first();
        if (!$existing) {
            $cat131 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[71] ?? null,
                'title' => json_encode(['tr' => 'Dingil Askı Burcu']),
                'slug' => json_encode(['tr' => 'dingil-aski-burcu']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 103,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[131] = $cat131->category_id;
        } else {
            $this->categoryMapping[131] = $existing->category_id;
        }

        // Kategori: Perno Mili (ID: 132)
        $existing = ShopCategory::where('slug->tr', 'perno-mili')->first();
        if (!$existing) {
            $cat132 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[71] ?? null,
                'title' => json_encode(['tr' => 'Perno Mili']),
                'slug' => json_encode(['tr' => 'perno-mili']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 104,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[132] = $cat132->category_id;
        } else {
            $this->categoryMapping[132] = $existing->category_id;
        }

        // Kategori: Porya (ID: 133)
        $existing = ShopCategory::where('slug->tr', 'porya')->first();
        if (!$existing) {
            $cat133 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[71] ?? null,
                'title' => json_encode(['tr' => 'Porya']),
                'slug' => json_encode(['tr' => 'porya']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 105,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[133] = $cat133->category_id;
        } else {
            $this->categoryMapping[133] = $existing->category_id;
        }

        // Kategori: Rot Başı (ID: 134)
        $existing = ShopCategory::where('slug->tr', 'rot-basi')->first();
        if (!$existing) {
            $cat134 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[71] ?? null,
                'title' => json_encode(['tr' => 'Rot Başı']),
                'slug' => json_encode(['tr' => 'rot-basi']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 106,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[134] = $cat134->category_id;
        } else {
            $this->categoryMapping[134] = $existing->category_id;
        }

        // Kategori: Bakalit (ID: 135)
        $existing = ShopCategory::where('slug->tr', 'bakalit')->first();
        if (!$existing) {
            $cat135 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[72] ?? null,
                'title' => json_encode(['tr' => 'Bakalit']),
                'slug' => json_encode(['tr' => 'bakalit']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 108,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[135] = $cat135->category_id;
        } else {
            $this->categoryMapping[135] = $existing->category_id;
        }

        // Kategori: Kabin (ID: 136)
        $existing = ShopCategory::where('slug->tr', 'kabin')->first();
        if (!$existing) {
            $cat136 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[72] ?? null,
                'title' => json_encode(['tr' => 'Kabin']),
                'slug' => json_encode(['tr' => 'kabin']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 109,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[136] = $cat136->category_id;
        } else {
            $this->categoryMapping[136] = $existing->category_id;
        }

        // Kategori: Amortisör (ID: 137)
        $existing = ShopCategory::where('slug->tr', 'amortisor')->first();
        if (!$existing) {
            $cat137 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[72] ?? null,
                'title' => json_encode(['tr' => 'Amortisör']),
                'slug' => json_encode(['tr' => 'amortisor']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 110,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[137] = $cat137->category_id;
        } else {
            $this->categoryMapping[137] = $existing->category_id;
        }

        // Kategori: Gösterge Paneli (ID: 138)
        $existing = ShopCategory::where('slug->tr', 'gosterge-paneli')->first();
        if (!$existing) {
            $cat138 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[72] ?? null,
                'title' => json_encode(['tr' => 'Gösterge Paneli']),
                'slug' => json_encode(['tr' => 'gosterge-paneli']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 111,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[138] = $cat138->category_id;
        } else {
            $this->categoryMapping[138] = $existing->category_id;
        }

        // Kategori: Yağ Pompası (ID: 139)
        $existing = ShopCategory::where('slug->tr', 'yag-pompasi')->first();
        if (!$existing) {
            $cat139 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[73] ?? null,
                'title' => json_encode(['tr' => 'Yağ Pompası']),
                'slug' => json_encode(['tr' => 'yag-pompasi']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 113,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[139] = $cat139->category_id;
        } else {
            $this->categoryMapping[139] = $existing->category_id;
        }

        // Kategori: Hidrolik Pompa (ID: 140)
        $existing = ShopCategory::where('slug->tr', 'hidrolik-pompa')->first();
        if (!$existing) {
            $cat140 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[73] ?? null,
                'title' => json_encode(['tr' => 'Hidrolik Pompa']),
                'slug' => json_encode(['tr' => 'hidrolik-pompa']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 114,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[140] = $cat140->category_id;
        } else {
            $this->categoryMapping[140] = $existing->category_id;
        }

        // Kategori: Şanzıman Pompası (ID: 141)
        $existing = ShopCategory::where('slug->tr', 'sanziman-pompasi')->first();
        if (!$existing) {
            $cat141 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[73] ?? null,
                'title' => json_encode(['tr' => 'Şanzıman Pompası']),
                'slug' => json_encode(['tr' => 'sanziman-pompasi']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 115,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[141] = $cat141->category_id;
        } else {
            $this->categoryMapping[141] = $existing->category_id;
        }

        // Kategori: Yakıt Pompası (ID: 142)
        $existing = ShopCategory::where('slug->tr', 'yakit-pompasi')->first();
        if (!$existing) {
            $cat142 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[73] ?? null,
                'title' => json_encode(['tr' => 'Yakıt Pompası']),
                'slug' => json_encode(['tr' => 'yakit-pompasi']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 116,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[142] = $cat142->category_id;
        } else {
            $this->categoryMapping[142] = $existing->category_id;
        }

        // Kategori: Fren Ana Merkezi (ID: 143)
        $existing = ShopCategory::where('slug->tr', 'fren-ana-merkezi')->first();
        if (!$existing) {
            $cat143 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[74] ?? null,
                'title' => json_encode(['tr' => 'Fren Ana Merkezi']),
                'slug' => json_encode(['tr' => 'fren-ana-merkezi']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 118,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[143] = $cat143->category_id;
        } else {
            $this->categoryMapping[143] = $existing->category_id;
        }

        // Kategori: Fren Balatası (ID: 144)
        $existing = ShopCategory::where('slug->tr', 'fren-balatasi')->first();
        if (!$existing) {
            $cat144 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[74] ?? null,
                'title' => json_encode(['tr' => 'Fren Balatası']),
                'slug' => json_encode(['tr' => 'fren-balatasi']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 119,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[144] = $cat144->category_id;
        } else {
            $this->categoryMapping[144] = $existing->category_id;
        }

        // Kategori: Fren Tekerlek Merkezi (ID: 145)
        $existing = ShopCategory::where('slug->tr', 'fren-tekerlek-merkezi')->first();
        if (!$existing) {
            $cat145 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[74] ?? null,
                'title' => json_encode(['tr' => 'Fren Tekerlek Merkezi']),
                'slug' => json_encode(['tr' => 'fren-tekerlek-merkezi']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 120,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[145] = $cat145->category_id;
        } else {
            $this->categoryMapping[145] = $existing->category_id;
        }

        // Kategori: Sabitleme Levhası (ID: 146)
        $existing = ShopCategory::where('slug->tr', 'sabitleme-levhasi')->first();
        if (!$existing) {
            $cat146 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[74] ?? null,
                'title' => json_encode(['tr' => 'Sabitleme Levhası']),
                'slug' => json_encode(['tr' => 'sabitleme-levhasi']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 121,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[146] = $cat146->category_id;
        } else {
            $this->categoryMapping[146] = $existing->category_id;
        }

        // Kategori: Fren Cırcırı (ID: 147)
        $existing = ShopCategory::where('slug->tr', 'fren-circiri')->first();
        if (!$existing) {
            $cat147 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[74] ?? null,
                'title' => json_encode(['tr' => 'Fren Cırcırı']),
                'slug' => json_encode(['tr' => 'fren-circiri']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 122,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[147] = $cat147->category_id;
        } else {
            $this->categoryMapping[147] = $existing->category_id;
        }

        // Kategori: Hava Fitresi (ID: 148)
        $existing = ShopCategory::where('slug->tr', 'hava-fitresi')->first();
        if (!$existing) {
            $cat148 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[75] ?? null,
                'title' => json_encode(['tr' => 'Hava Fitresi']),
                'slug' => json_encode(['tr' => 'hava-fitresi']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 124,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[148] = $cat148->category_id;
        } else {
            $this->categoryMapping[148] = $existing->category_id;
        }

        // Kategori: Hidrolik Filtresi (ID: 149)
        $existing = ShopCategory::where('slug->tr', 'hidrolik-filtresi')->first();
        if (!$existing) {
            $cat149 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[75] ?? null,
                'title' => json_encode(['tr' => 'Hidrolik Filtresi']),
                'slug' => json_encode(['tr' => 'hidrolik-filtresi']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 125,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[149] = $cat149->category_id;
        } else {
            $this->categoryMapping[149] = $existing->category_id;
        }

        // Kategori: Şanzıman Filtresi (ID: 150)
        $existing = ShopCategory::where('slug->tr', 'sanziman-filtresi')->first();
        if (!$existing) {
            $cat150 = ShopCategory::create([
                'parent_id' => $this->categoryMapping[75] ?? null,
                'title' => json_encode(['tr' => 'Şanzıman Filtresi']),
                'slug' => json_encode(['tr' => 'sanziman-filtresi']),
                'description' => json_encode(['tr' => '']),
                'image_url' => '',
                'level' => 2,
                'sort_order' => 126,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
            $this->categoryMapping[150] = $cat150->category_id;
        } else {
            $this->categoryMapping[150] = $existing->category_id;
        }
    }
}
