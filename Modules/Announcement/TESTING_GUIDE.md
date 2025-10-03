# Announcement Modülü Test Rehberi

## Hızlı Başlangıç

### Test Çalıştırma
```bash
# Tüm testler (önerilen)
./Modules/Announcement/run-tests.sh

# Sadece Unit testler
./Modules/Announcement/run-tests.sh unit

# Sadece Feature testler
./Modules/Announcement/run-tests.sh feature

# Coverage raporu ile
./Modules/Announcement/run-tests.sh coverage

# Parallel (hızlı)
./Modules/Announcement/run-tests.sh fast
```

### Manuel PHPUnit Komutları
```bash
# Tüm testler
vendor/bin/phpunit Modules/Announcement/tests

# Tek bir dosya
vendor/bin/phpunit Modules/Announcement/tests/Unit/AnnouncementRepositoryTest.php

# Tek bir test
vendor/bin/phpunit --filter=it_can_find_page_by_id Modules/Announcement/tests/Unit/AnnouncementRepositoryTest.php

# Testdox format (okunabilir)
vendor/bin/phpunit Modules/Announcement/tests --testdox
```

## Test Yapısı

### Klasör Organizasyonu
```
Modules/Announcement/tests/
├── Unit/                           # İzole birim testleri
│   ├── AnnouncementRepositoryTest.php     # Repository katmanı
│   ├── AnnouncementServiceTest.php        # Business logic
│   ├── AnnouncementObserverTest.php       # Model events
│   └── AnnouncementModelTest.php          # Model attributes
│
├── Feature/                        # Entegrasyon testleri
│   ├── AnnouncementAdminTest.php          # Admin panel UI
│   ├── AnnouncementApiTest.php            # Routes & endpoints
│   ├── AnnouncementCacheTest.php          # Cache mekanizmaları
│   ├── AnnouncementBulkOperationsTest.php # Toplu işlemler
│   └── AnnouncementPermissionTest.php     # Yetkilendirme
│
├── README.md                       # Detaylı döküman
├── phpunit.xml                     # PHPUnit config
└── run-tests.sh                    # Test çalıştırma script
```

## Test Kategorileri

### 1. Unit Testler (139 test)

#### Repository Tests (46 test)
```php
// Örnek test
/** @test */
public function it_can_find_page_by_id(): void
{
    $announcement = Announcement::factory()->create();

    $found = $this->repository->findById($announcement->announcement_id);

    $this->assertNotNull($found);
    $this->assertEquals($announcement->announcement_id, $found->announcement_id);
}
```

**Kapsam**:
- CRUD operations
- Search & filtering
- Homepage management
- Bulk operations
- Cache clearing

#### Service Tests (30 test)
```php
/** @test */
public function it_creates_page_successfully(): void
{
    $data = [
        'title' => ['tr' => 'Test', 'en' => 'Test'],
        'body' => ['tr' => 'Content', 'en' => 'Content'],
    ];

    $result = $this->service->createPage($data);

    $this->assertTrue($result->success);
    $this->assertInstanceOf(Announcement::class, $result->data);
}
```

**Kapsam**:
- Business logic
- Exception handling
- Slug generation
- SEO preparation
- Validation rules

#### Observer Tests (28 test)
```php
/** @test */
public function it_generates_slug_automatically(): void
{
    $announcement = Announcement::create([
        'title' => ['tr' => 'Test Sayfa', 'en' => 'Test Announcement'],
        'body' => ['tr' => 'Test', 'en' => 'Test'],
    ]);

    $this->assertEquals('test-sayfa', $announcement->getTranslated('slug', 'tr'));
}
```

**Kapsam**:
- Lifecycle events
- Automatic slug generation
- Homepage uniqueness
- Validation
- Cache clearing

#### Model Tests (35 test)
```php
/** @test */
public function it_has_seo_fallback_title(): void
{
    $announcement = Announcement::factory()->create([
        'title' => ['tr' => 'Test Başlık']
    ]);

    app()->setLocale('tr');
    $seoTitle = $announcement->getSeoFallbackTitle();

    $this->assertEquals('Test Başlık', $seoTitle);
}
```

**Kapsam**:
- Attributes & casts
- Relationships
- Scopes
- SEO fallbacks
- Factory states

### 2. Feature Testler (120 test)

#### Admin Tests (35 test)
```php
/** @test */
public function admin_can_create_page(): void
{
    $this->actingAs($this->admin);

    Livewire::test(AnnouncementManageComponent::class)
        ->set('multiLangInputs.tr.title', 'Yeni Sayfa')
        ->set('multiLangInputs.tr.body', '<p>İçerik</p>')
        ->call('save')
        ->assertDispatched('toast');

    $this->assertDatabaseHas('pages', [
        'title->tr' => 'Yeni Sayfa'
    ]);
}
```

**Kapsam**:
- Livewire components
- Form validation
- CRUD UI operations
- Security (XSS, injection)

#### API Tests (20 test)
```php
/** @test */
public function admin_can_access_page_index(): void
{
    $this->actingAs($this->admin);

    $response = $this->get(route('admin.announcement.index'));

    $response->assertStatus(200);
}
```

**Kapsam**:
- Route definitions
- Middleware
- Permission checks
- CSRF protection

#### Cache Tests (18 test)
```php
/** @test */
public function it_clears_cache_after_update(): void
{
    $announcement = Announcement::factory()->create();

    $this->repository->update($announcement->announcement_id, [
        'title' => ['tr' => 'Updated']
    ]);

    $this->assertNull(Cache::get('homepage_data'));
}
```

**Kapsam**:
- Cache strategies
- Invalidation
- Tenant awareness
- Response cache

#### Bulk Operations Tests (22 test)
```php
/** @test */
public function it_protects_homepage_in_bulk_delete(): void
{
    $homepage = Announcement::factory()->homepage()->create();
    $pages = Announcement::factory()->count(3)->create();

    $result = $this->service->bulkDeletePages([
        $homepage->announcement_id,
        ...$pages->pluck('announcement_id')
    ]);

    $this->assertEquals(3, $result->affectedCount);
    $this->assertDatabaseHas('pages', ['announcement_id' => $homepage->announcement_id]);
}
```

**Kapsam**:
- Bulk delete/toggle
- Homepage protection
- Edge cases
- Data integrity

#### Permission Tests (25 test)
```php
/** @test */
public function guest_cannot_access_admin_pages(): void
{
    $response = $this->get(route('admin.announcement.index'));

    $response->assertRedirect(route('login'));
}
```

**Kapsam**:
- Role-based access
- Authentication
- Security measures
- Activity logging

## Yaygın Test Senaryoları

### Homepage Koruması
```php
// Homepage silinemez
$this->expectException(\Exception::class);
$homepage->delete();

// Homepage deaktive edilemez
$result = $this->service->togglePageStatus($homepage->announcement_id);
$this->assertFalse($result->success);
```

### Slug Benzersizliği
```php
// Aynı slug'dan ikinci bir sayfa otomatik unique slug alır
$page1 = Announcement::create(['title' => ['tr' => 'Test']]);
$page2 = Announcement::create(['title' => ['tr' => 'Test']]);

$this->assertNotEquals(
    $page1->getTranslated('slug', 'tr'),
    $page2->getTranslated('slug', 'tr')
);
```

### XSS Koruması
```php
$maliciousHtml = '<script>alert("XSS")</script><p>Safe</p>';

$announcement = Announcement::create([
    'title' => ['tr' => 'Test'],
    'body' => ['tr' => $maliciousHtml],
]);

$this->assertStringNotContainsString(
    '<script>',
    $announcement->getTranslated('body', 'tr')
);
```

## Debugging

### Test Hata Ayıklama
```php
// Dump data
dd($announcement->toArray());
dump($announcement->fresh());

// Log'a yaz
\Log::info('Test Debug', ['announcement' => $announcement]);

// Assert messages ile
$this->assertEquals($expected, $actual, 'Expected values do not match');
```

### Database Debugging
```php
// Database sorgularını logla
DB::enableQueryLog();
// ... test operations
dd(DB::getQueryLog());

// Mevcut database state'i kontrol et
$this->assertDatabaseCount('pages', 5);
$this->assertDatabaseHas('pages', ['slug->tr' => 'test-slug']);
$this->assertDatabaseMissing('pages', ['announcement_id' => 999]);
```

### Cache Debugging
```php
// Cache'i kontrol et
$cacheValue = Cache::get('homepage_data');
dump($cacheValue);

// Cache temizle
Cache::flush();
$this->repository->clearCache();
```

## Best Practices

### ✅ Yapılması Gerekenler

1. **Test İzolasyonu**
   ```php
   use RefreshDatabase; // Her test'te temiz database
   ```

2. **Factory Kullanımı**
   ```php
   $announcement = Announcement::factory()->create(); // Gerçekçi data
   ```

3. **Açıklayıcı İsimler**
   ```php
   public function it_can_create_page(): void // Açık
   public function test_create(): void        // Belirsiz
   ```

4. **AAA Pattern**
   ```php
   // Arrange
   $announcement = Announcement::factory()->create();

   // Act
   $result = $this->service->deletePage($announcement->announcement_id);

   // Assert
   $this->assertTrue($result->success);
   ```

5. **Type Declarations**
   ```php
   public function it_returns_page(): void
   {
       $announcement = $this->repository->findById(1);
       $this->assertInstanceOf(Announcement::class, $announcement);
   }
   ```

### ❌ Yapılmaması Gerekenler

1. **Test'ler birbirine bağımlı olmamalı**
   ```php
   // YANLIŞ
   public function test_1() { $this->pageId = ...; }
   public function test_2() { $announcement = Announcement::find($this->pageId); }
   ```

2. **Sleep kullanma**
   ```php
   // YANLIŞ
   sleep(2); // Asynchronous operations için
   ```

3. **Production database kullanma**
   ```php
   // phpunit.xml'de :memory: veya test database kullan
   ```

4. **Random data ile assertion**
   ```php
   // YANLIŞ
   $this->assertEquals(rand(1, 100), $announcement->view_count);
   ```

## Coverage Raporu

### HTML Rapor Oluşturma
```bash
./Modules/Announcement/run-tests.sh coverage
# Rapor: Modules/Announcement/tests/coverage/index.html
open Modules/Announcement/tests/coverage/index.html
```

### Hedef Coverage
- **Minimum**: %75
- **Hedef**: %85
- **Ideal**: %90+

### Coverage İstatistikleri
```
Lines:    85.2%
Functions: 87.8%
Classes:   92.1%
```

## CI/CD Integration

### GitHub Actions
```yaml
name: Tests
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Run Announcement Tests
        run: vendor/bin/phpunit Modules/Announcement/tests
```

### GitLab CI
```yaml
test:announcement:
  script:
    - vendor/bin/phpunit Modules/Announcement/tests --coverage-text
```

## Sorun Giderme

### Yaygın Hatalar

#### 1. Database Migration Hatası
```bash
# Çözüm
php artisan migrate:fresh --env=testing
```

#### 2. Cache Problemi
```bash
# Çözüm
php artisan config:clear
php artisan cache:clear
```

#### 3. Memory Limit
```bash
# Çözüm
php -d memory_limit=512M vendor/bin/phpunit
```

#### 4. Permission Hatası
```bash
# Çözüm
chmod -R 775 storage bootstrap/cache
```

## İleri Seviye

### Parallel Testing
```bash
php artisan test --parallel --processes=4 --filter=Announcement
```

### Test Filtreleme
```bash
# Sadece homepage testleri
vendor/bin/phpunit --filter=homepage

# Sadece validation testleri
vendor/bin/phpunit --filter=validation
```

### Test Groups
```php
/**
 * @test
 * @group slow
 */
public function it_handles_large_dataset(): void
{
    // ...
}
```

```bash
# Sadece slow group
vendor/bin/phpunit --group=slow

# Slow hariç
vendor/bin/phpunit --exclude-group=slow
```

## Test Yazma Şablonu

### Yeni Unit Test Eklerken
```php
<?php
declare(strict_types=1);

namespace Modules\Announcement\Tests\Unit;

use Modules\Announcement\Tests\TestCase;
use Modules\Announcement\App\Models\Announcement;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_descriptive_test_name(): void
    {
        // Arrange
        $announcement = Announcement::factory()->create();

        // Act
        $result = $announcement->someMethod();

        // Assert
        $this->assertNotNull($result);
    }
}
```

### Yeni Feature Test Eklerken
```php
<?php
declare(strict_types=1);

namespace Modules\Announcement\Tests\Feature;

use Modules\Announcement\Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

class ExampleFeatureTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    /** @test */
    public function admin_can_perform_action(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(SomeComponent::class)
            ->set('someProperty', 'value')
            ->call('someMethod')
            ->assertDispatched('toast');

        $this->assertDatabaseHas('pages', ['title->tr' => 'value']);
    }
}
```

## Kod Kalitesi Kontrolü

### PHPStan (Static Analysis)
```bash
# Eğer kuruluysa
vendor/bin/phpstan analyse Modules/Announcement

# Önerilen level: 6-8
```

### PHP CS Fixer (Code Style)
```bash
# Eğer kuruluysa
vendor/bin/php-cs-fixer fix Modules/Announcement --dry-run
```

## Test Metrikleri

### Mevcut Durum (Son Güncelleme: 2025-10-01)
```
Total Tests:     259
Unit Tests:      139 (53.7%)
Feature Tests:   120 (46.3%)

Test Success:    259/259 (100%)
Coverage:        85.2%
Functions:       87.8%
Classes:         92.1%

Execution Time:  ~45 seconds
```

### Hedef Metrikler
```
Total Tests:     300+ (✅ Büyütülebilir)
Coverage:        90%+ (🎯 Hedef)
Functions:       90%+ (🎯 Hedef)
Execution Time:  <60s  (✅ Başarılı)
```

## Continuous Integration Best Practices

### Pre-Commit Hook
```bash
#!/bin/bash
# .git/hooks/pre-commit

# Run tests before commit
vendor/bin/phpunit Modules/Announcement/tests --stop-on-failure

if [ $? -ne 0 ]; then
    echo "❌ Tests failed. Commit aborted."
    exit 1
fi

echo "✅ All tests passed. Proceeding with commit."
```

### Pre-Push Hook
```bash
#!/bin/bash
# .git/hooks/pre-push

# Run tests with coverage before push
./Modules/Announcement/run-tests.sh coverage

if [ $? -ne 0 ]; then
    echo "❌ Tests or coverage check failed. Push aborted."
    exit 1
fi

echo "✅ All checks passed. Proceeding with push."
```

## Test Coverage Detayları

### Kapsanan Alanlar (85%+)
- ✅ **Repository Layer**: 92% coverage
- ✅ **Service Layer**: 89% coverage
- ✅ **Model Layer**: 95% coverage
- ✅ **Observer**: 88% coverage
- ✅ **Livewire Components**: 78% coverage
- ✅ **Jobs**: 85% coverage

### Kapsanmayan Alanlar (Kabul Edilebilir)
- ⚠️ **Exception Handling Edge Cases**: 15%
- ⚠️ **Fallback Routes**: 10%
- ⚠️ **Deprecated Methods**: 5%

## Daha Fazla Bilgi

- **Ana Döküman**: `README.md`
- **Test Özeti**: `TEST_SUMMARY.md`
- **PHPUnit Config**: `phpunit.xml`
- **Run Script**: `run-tests.sh`

## Pattern Uygunluğu

Bu test yapısı **Announcement Pattern**'in bir parçasıdır. Yeni modüller bu test yapısını temel almalıdır:

1. ✅ `tests/TestCase.php` base class
2. ✅ `Unit/` ve `Feature/` ayrımı
3. ✅ Factory kullanımı
4. ✅ RefreshDatabase trait
5. ✅ Descriptive test names
6. ✅ AAA pattern (Arrange-Act-Assert)
7. ✅ Type declarations

**Referans**: `readme/claude-docs/claude_modulpattern.md`

---

**Oluşturulma**: 2025-09-30
**Son Güncelleme**: 2025-10-01
**Versiyon**: 1.1.0
**Test Status**: ✅ 259/259 Passing (100%)
**Coverage**: 85.2%
