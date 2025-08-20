<?php

namespace Modules\Page\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Page\App\Models\Page;
use App\Models\SeoSetting;
use App\Helpers\TenantHelpers;
use Modules\Page\database\seeders\PageSeederCentral;
use Modules\Page\database\seeders\PageSeederTenant2;
use Modules\Page\database\seeders\PageSeederTenant3;
use Modules\Page\database\seeders\PageSeederTenant4;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        // Context'e göre ilgili seeder'ı çağır
        if (TenantHelpers::isCentral()) {
            $this->command->info('🎯 Central context - çalıştırılacak seeder: PageSeederCentral');
            $this->call(PageSeederCentral::class);
            return;
        }
        
        // Tenant context - hangi tenant olduğunu belirle
        $tenantId = TenantHelpers::getCurrentTenantId();
        
        switch ($tenantId) {
            case 2:
                $this->command->info('🎯 Tenant2 context - çalıştırılacak seeder: PageSeederTenant2');
                $this->call(PageSeederTenant2::class);
                break;
            case 3:
                $this->command->info('🎯 Tenant3 context - çalıştırılacak seeder: PageSeederTenant3');
                $this->call(PageSeederTenant3::class);
                break;
            case 4:
                $this->command->info('🎯 Tenant4 context - çalıştırılacak seeder: PageSeederTenant4');
                $this->call(PageSeederTenant4::class);
                break;
            default:
                $this->command->info("❌ Bilinmeyen tenant ID: {$tenantId} - seeder atlanıyor");
                break;
        }
        return;
    }
}