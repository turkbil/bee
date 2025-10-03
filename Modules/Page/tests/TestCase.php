<?php

namespace Modules\Page\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Page modülü için gerekli setup'lar
        $this->artisan('module:migrate', ['module' => 'Page']);
    }
}
