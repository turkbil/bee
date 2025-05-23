<?php

namespace Modules\SettingManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SettingsGroupsTableSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            ['id' => 1, 'name' => 'Sistem', 'parent_id' => null, 'icon' => 'fas fa-cogs'],
            ['id' => 2, 'name' => 'Tenant', 'parent_id' => null, 'icon' => 'fas fa-building'],
            ['id' => 3, 'name' => 'Kullanıcı', 'parent_id' => null, 'icon' => 'fas fa-users'],
            ['id' => 4, 'name' => 'Modül', 'parent_id' => null, 'icon' => 'fas fa-puzzle-piece'],
            ['id' => 5, 'name' => 'Site', 'parent_id' => null, 'icon' => 'fas fa-globe'],
            ['id' => 6, 'name' => 'Site Ayarları', 'parent_id' => 1, 'icon' => 'fas fa-sliders-h', 'prefix' => 'site', 'layout' => $this->getSiteAyarlariLayout()],
            ['id' => 7, 'name' => 'Tema', 'parent_id' => 5, 'icon' => 'fas fa-palette', 'prefix' => 'theme'],
            // NOT: Daha önce 'Tema Ayarları' olarak eklenmiş grup kaldırıldı, bu tüm tema ayarları 'Tema' grubu altında olacak
        ];
        
        foreach ($groups as $group) {
            $layout = $group['layout'] ?? null;
            unset($group['layout']);
            
            $groupId = DB::table('settings_groups')->insertGetId([
                'id' => $group['id'],
                'name' => $group['name'],
                'slug' => Str::slug($group['name']),
                'parent_id' => $group['parent_id'],
                'icon' => $group['icon'],
                'prefix' => $group['prefix'] ?? null,
                'is_active' => true,
                'layout' => $layout,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
    
    private function getSiteAyarlariLayout(): string
    {
        $layout = [
            'title' => 'Site Ayarları Formu',
            'elements' => [
                [
                    'type' => 'row',
                    'properties' => [
                        'columns' => [
                            ['index' => 1, 'width' => 6],
                            ['index' => 2, 'width' => 6]
                        ]
                    ],
                    'columns' => [
                        [
                            'width' => 6,
                            'elements' => [
                                [
                                    'type' => 'text',
                                    'properties' => [
                                        'label' => 'Site Başlığı',
                                        'name' => 'site_title',
                                        'placeholder' => 'Site başlığını giriniz',
                                        'help_text' => 'Sitenizin başlığı tüm sayfalarda görünecektir',
                                        'width' => 12,
                                        'required' => true,
                                        'default_value' => '',
                                        'setting_id' => 1
                                    ]
                                ],
                                [
                                    'type' => 'email',
                                    'properties' => [
                                        'label' => 'Ana E-posta Adresi',
                                        'name' => 'site_email',
                                        'placeholder' => 'E-posta adresini giriniz',
                                        'help_text' => 'Bu e-posta adresi iletişim formlarında kullanılacaktır',
                                        'width' => 12,
                                        'required' => true,
                                        'default_value' => '',
                                        'setting_id' => 4
                                    ]
                                ]
                            ]
                        ],
                        [
                            'width' => 6,
                            'elements' => [
                                [
                                    'type' => 'file',
                                    'properties' => [
                                        'label' => 'Site Logo',
                                        'name' => 'site_logo',
                                        'help_text' => 'Önerilen boyut: 200x60 piksel',
                                        'width' => 12,
                                        'required' => false,
                                        'default_value' => '',
                                        'setting_id' => 2
                                    ]
                                ],
                                [
                                    'type' => 'file',
                                    'properties' => [
                                        'label' => 'Favicon',
                                        'name' => 'site_favicon',
                                        'help_text' => 'Önerilen boyut: 32x32 piksel',
                                        'width' => 12,
                                        'required' => false,
                                        'default_value' => '',
                                        'setting_id' => 3
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'type' => 'heading',
                    'properties' => [
                        'content' => 'Analitik Ayarları',
                        'size' => 'h3',
                        'width' => 12,
                        'align' => 'left'
                    ]
                ],
                [
                    'type' => 'text',
                    'properties' => [
                        'label' => 'Google Analytics Kodu',
                        'name' => 'site_google_analytics_code',
                        'placeholder' => 'G-XXXXXXXXXX',
                        'help_text' => 'Google Analytics izleme kodu (Örnek: G-XXXXXXXXXX)',
                        'width' => 12,
                        'required' => false,
                        'default_value' => '',
                        'setting_id' => 5
                    ]
                ]
            ]
        ];
        
        return json_encode($layout);
    }
}