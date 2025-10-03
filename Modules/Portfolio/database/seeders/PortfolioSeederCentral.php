<?php

namespace Modules\Portfolio\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Portfolio\App\Models\Portfolio;
use Modules\Portfolio\App\Models\PortfolioCategory;

/**
 * Portfolio Central Database Seeder
 *
 * Seeds portfolios and categories for the Central database.
 * Creates demo portfolios with TR/EN translations.
 *
 * @package Modules\Portfolio\Database\Seeders
 */
class PortfolioSeederCentral extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌐 CENTRAL DATABASE - Portfolio Seeding');
        $this->command->info('ℹ️  Portfolios sadece tenant database\'lerde olur, central\'da seed yok');
        $this->command->newLine();

        // Portfolio ve kategorileri SADECE tenant database'lerde olmalı
        // Central database'de portfolio seed edilmez
        return;
    }
}
