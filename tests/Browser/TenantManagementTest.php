<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TenantManagementTest extends DuskTestCase
{
    public function testTenantManagementPagesLoad()
    {
        $this->browse(function (Browser $browser) {
            // Giriş yap
            $browser->visit('/login')
                   ->type('email', 'nurullah@nurullah.net')
                   ->type('password', 'test')
                   ->press('Giriş Yap')
                   ->waitForLocation('/admin', 10);

            // Tenant Management ana sayfası
            $browser->visit('/admin/tenantmanagement')
                   ->assertSee('Kiracı')
                   ->screenshot('tenantmanagement-index');

            // Monitoring sayfası - kritik test
            $browser->visit('/admin/tenantmanagement/monitoring')
                   ->waitFor('.card', 10)
                   ->assertSee('Sistem Kaynak Kullanımı')
                   ->screenshot('tenantmonitoring-page');

            // Cache sayfası
            $browser->visit('/admin/tenantmanagement/cache')
                   ->waitFor('.card', 10)
                   ->screenshot('tenantcache-page');

            // Limits sayfası
            $browser->visit('/admin/tenantmanagement/limits')
                   ->waitFor('.card', 10)
                   ->screenshot('tenantlimits-page');

            // Rate Limits sayfası
            $browser->visit('/admin/tenantmanagement/rate-limits')
                   ->waitFor('.card', 10)
                   ->screenshot('tenantratelimits-page');
        });
    }

    public function testTenantManagementButtons()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                   ->type('email', 'nurullah@nurullah.net')
                   ->type('password', 'test')
                   ->press('Giriş Yap')
                   ->waitForLocation('/admin', 10);

            // Monitoring sayfası buton testleri
            $browser->visit('/admin/tenantmanagement/monitoring')
                   ->waitFor('.card', 10);

            // Helper butonlarını test et
            if ($browser->element('.btn')) {
                $browser->screenshot('before-button-click')
                       ->click('.btn:first-of-type')
                       ->pause(1000)
                       ->screenshot('after-button-click');
            }

            // Cache sayfası buton testleri
            $browser->visit('/admin/tenantmanagement/cache')
                   ->waitFor('.card', 10);

            // Cache butonları varsa test et
            if ($browser->element('.btn-primary')) {
                $browser->click('.btn-primary')
                       ->pause(2000)
                       ->screenshot('cache-button-clicked');
            }

            // Limits sayfası dropdown testleri
            $browser->visit('/admin/tenantmanagement/limits')
                   ->waitFor('.card', 10);

            // Tenant dropdown varsa test et
            if ($browser->element('select')) {
                $browser->select('selectedTenantId', '1')
                       ->pause(2000)
                       ->screenshot('tenantselected');
            }
        });
    }
}