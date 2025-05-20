<?php

namespace Modules\UserManagement\App\Console\Commands;

use Illuminate\Console\Command;
use Modules\UserManagement\App\Models\Role;

class ProtectBaseRoles extends Command
{
    protected $signature = 'roles:protect-base';
    protected $description = 'Temel rolleri koruma altına alır';

    public function handle()
    {
        $this->info('Temel roller koruma altına alınıyor...');

        Role::createBaseRoles();

        $this->info('Temel roller başarıyla koruma altına alındı!');
        $this->table(
            ['Rol Adı', 'Guard', 'Korumalı'],
            Role::whereIn('name', Role::BASE_ROLES)
                ->get()
                ->map(fn($role) => [
                    $role->name,
                    $role->guard_name,
                    $role->is_protected ? '✓' : '✗'
                ])
        );
    }
}