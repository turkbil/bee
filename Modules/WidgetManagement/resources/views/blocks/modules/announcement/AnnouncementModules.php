<?php

namespace Modules\WidgetManagement\Resources\views\blocks\modules\announcement;

use Modules\Announcement\App\Models\Announcement;
use Illuminate\Support\Str;

class AnnouncementModules
{
    public function register()
    {
        return [
            'announcement.list' => [
                'name' => 'Duyuru Listesi',
                'description' => 'Duyuruları listeler',
                'view' => 'widgetmanagement::blocks.modules.announcement.list.view',
                'fields' => [
                    'widget_title' => [
                        'type' => 'text',
                        'label' => 'Widget Başlığı',
                        'default' => 'Duyurular',
                    ],
                    'limit' => [
                        'type' => 'number',
                        'label' => 'Gösterilecek Duyuru Sayısı',
                        'default' => 5,
                    ],
                    'columns' => [
                        'type' => 'select',
                        'label' => 'Sütun Sayısı',
                        'options' => [
                            '1' => '1 Sütun',
                            '2' => '2 Sütun',
                            '3' => '3 Sütun',
                        ],
                        'default' => '2',
                    ],
                    'show_date' => [
                        'type' => 'checkbox',
                        'label' => 'Tarih Göster',
                        'default' => true,
                    ],
                    'show_description' => [
                        'type' => 'checkbox',
                        'label' => 'Açıklama Göster',
                        'default' => true,
                    ],
                ],
            ],
        ];
    }
}
