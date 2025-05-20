<?php

return [

    'models' => [

        /*
         * Kullanılacak Role modeli
         */
        'role' => Spatie\Permission\Models\Role::class,

        /*
         * Kullanılacak Permission modeli
         */
        'permission' => Spatie\Permission\Models\Permission::class,
    ],

    'table_names' => [

        /*
         * Roller için kullanılacak tablo adı
         */
        'roles' => 'roles',

        /*
         * İzinler için kullanılacak tablo adı
         */
        'permissions' => 'permissions',

        /*
         * Roller ve izinlerin bağlandığı tablo
         */
        'model_has_permissions' => 'model_has_permissions',

        /*
         * Roller ve modeller arasındaki ilişkiyi tutan tablo
         */
        'model_has_roles' => 'model_has_roles',

        /*
         * Roller ve izinler arasındaki ilişkiyi tutan tablo
         */
        'role_has_permissions' => 'role_has_permissions',
    ],

    'column_names' => [

        /*
         * Kullanıcı veya modelin ID sütunu
         */
        'model_morph_key' => 'model_id',
    ],

    /*
     * Kullanıcı birden fazla guard kullanabilir mi?
     * Örneğin: web, api gibi farklı yetkilendirme guardları
     */
    'multiple_guards' => false,

    'cache' => [

        /*
         * Roller ve izinler için önbellekleme süresi (saniye cinsinden)
         */
        'expiration_time' => \DateInterval::createFromDateString('24 hours'),

        /*
         * Cache anahtar ismi
         */
        'key' => 'spatie.permission.cache',

        /*
         * Cache driver (default `config/cache.php` ayarlarını kullanır)
         */
        'store' => 'default',
    ],
];
