<?php

namespace Modules\Portfolio\Database\Seeders;

use Illuminate\Database\Seeder;
use Stancl\Tenancy\Database\TenantCollection;
use App\Helpers\TenantHelpers;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🎨 Portfolio Module Seeder starting...');
        
        if (TenantHelpers::isCentral()) {
            $this->command->info('📍 Running CENTRAL Portfolio seeders...');
            
            // Önce kategorileri oluştur
            $this->call(\Modules\Portfolio\Database\Seeders\PortfolioCategorySeederCentral::class);
            
            // Sonra portfolioları oluştur
            $this->call(\Modules\Portfolio\Database\Seeders\PortfolioSeederCentral::class);
            
            $this->command->info('✅ CENTRAL Portfolio seeders completed!');
        } else {
            $this->command->info('📍 Running TENANT Portfolio seeders...');
            
            $tenantId = tenant('id');
            $this->command->info("Tenant ID: {$tenantId}");
            
            // Tenant'a göre uygun seeder'ları çalıştır
            switch ($tenantId) {
                case '2':
                    $this->call(\Modules\Portfolio\Database\Seeders\PortfolioCategorySeederTenant2::class);
                    $this->call(\Modules\Portfolio\Database\Seeders\PortfolioSeederTenant2::class);
                    break;
                case '3':
                    $this->call(\Modules\Portfolio\Database\Seeders\PortfolioCategorySeederTenant3::class);
                    $this->call(\Modules\Portfolio\Database\Seeders\PortfolioSeederTenant3::class);
                    break;
                case '4':
                    $this->call(\Modules\Portfolio\Database\Seeders\PortfolioCategorySeederTenant4::class);
                    // PortfolioSeederTenant4 yok, sadece kategori
                    break;
                default:
                    $this->command->info("No specific portfolio seeder for tenant {$tenantId}");
                    break;
            }
            
            $this->command->info('✅ TENANT Portfolio seeders completed!');
        }
    }
}