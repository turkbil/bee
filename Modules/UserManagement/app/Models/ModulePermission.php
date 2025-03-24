<?php

namespace Modules\UserManagement\App\Models;

class ModulePermission
{
    /**
     * Tüm olası izin tiplerini döndürür
     */
    public static function getPermissionTypes(): array
    {
        return [
            'view' => 'Görüntüleme',
            'create' => 'Oluşturma',
            'update' => 'Güncelleme',
            'delete' => 'Silme'
        ];
    }
}