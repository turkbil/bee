<?php

namespace Modules\Portfolio\Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Portfolio\App\Models\Portfolio;
use Modules\Portfolio\App\Models\PortfolioCategory;
use Spatie\Permission\Models\Role;

class PortfolioControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $adminUser;
    protected $editorUser;
    protected $category;
    protected $portfolio;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->initializeTenancy();
        
        // Test rolleri oluştur
        $adminRole = Role::create(['name' => 'admin']);
        $editorRole = Role::create(['name' => 'editor']);
        
        // Test kullanıcıları oluştur
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole($adminRole);
        
        $this->editorUser = User::factory()->create();
        $this->editorUser->assignRole($editorRole);
        
        // Test kategorisi oluştur
        $this->category = PortfolioCategory::create([
            'name' => 'Web Tasarım',
            'slug' => 'web-tasarim',
            'description' => 'Web tasarım projeleri',
            'status' => 'active'
        ]);
        
        // Test portfolio'su oluştur
        $this->portfolio = Portfolio::create([
            'title' => 'Test Portfolio',
            'slug' => 'test-portfolio',
            'description' => 'Test portfolio açıklaması',
            'content' => 'Test portfolio içeriği',
            'category_id' => $this->category->id,
            'status' => 'active',
            'featured_image' => 'test-image.jpg',
            'technologies' => ['PHP', 'Laravel', 'Vue.js'],
            'project_url' => 'https://example.com',
            'github_url' => 'https://github.com/example',
            'created_by' => $this->adminUser->id
        ]);
        
        Storage::fake('tenant');
    }

    /** @test */
    public function admin_can_view_portfolio_admin_page()
    {
        $response = $this->actingAs($this->adminUser)
                         ->get('/admin/portfolios');
        
        $response->assertStatus(200);
    }

    /** @test */
    public function guest_can_view_portfolio_frontend()
    {
        $response = $this->get('/portfolios');
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_create_portfolio()
    {
        Storage::fake('tenant');
        
        $image = UploadedFile::fake()->image('portfolio.jpg', 800, 600);
        
        $portfolioData = [
            'title' => 'Yeni Portfolio',
            'description' => 'Yeni portfolio açıklaması',
            'content' => 'Yeni portfolio içeriği',
            'category_id' => $this->category->id,
            'status' => 'active',
            'featured_image' => $image,
            'technologies' => ['React', 'Node.js'],
            'project_url' => 'https://yeni-proje.com',
            'github_url' => 'https://github.com/yeni-proje'
        ];
        
        $response = $this->actingAs($this->adminUser)
                         ->post('/admin/portfolios', $portfolioData);
        
        $response->assertRedirect();
        
        // Veritabanında kaydedildi mi kontrol et
        $this->assertDatabaseHas('portfolios', [
            'title' => 'Yeni Portfolio',
            'category_id' => $this->category->id,
            'status' => 'active'
        ]);
        
        // Dosya yüklendi mi kontrol et
        Storage::disk('tenant')->assertExists('portfolios/' . $image->hashName());
    }

    /** @test */
    public function portfolio_creation_requires_title()
    {
        $response = $this->actingAs($this->adminUser)
                         ->post('/admin/portfolios', [
                             'description' => 'Açıklama var ama başlık yok'
                         ]);
        
        $response->assertSessionHasErrors(['title']);
    }

    /** @test */
    public function portfolio_creation_requires_category()
    {
        $response = $this->actingAs($this->adminUser)
                         ->post('/admin/portfolios', [
                             'title' => 'Başlık var ama kategori yok',
                             'description' => 'Test açıklaması'
                         ]);
        
        $response->assertSessionHasErrors(['category_id']);
    }

    /** @test */
    public function admin_can_update_portfolio()
    {
        $updatedData = [
            'title' => 'Güncellenmiş Portfolio',
            'description' => 'Güncellenmiş açıklama',
            'category_id' => $this->category->id,
            'status' => 'active'
        ];
        
        $response = $this->actingAs($this->adminUser)
                         ->put('/admin/portfolios/' . $this->portfolio->id, $updatedData);
        
        $response->assertRedirect();
        
        $this->assertDatabaseHas('portfolios', [
            'id' => $this->portfolio->id,
            'title' => 'Güncellenmiş Portfolio'
        ]);
    }

    /** @test */
    public function admin_can_delete_portfolio()
    {
        $response = $this->actingAs($this->adminUser)
                         ->delete('/admin/portfolios/' . $this->portfolio->id);
        
        $response->assertRedirect();
        
        // Soft delete kontrolü
        $this->assertSoftDeleted('portfolios', [
            'id' => $this->portfolio->id
        ]);
    }

    /** @test */
    public function portfolio_slug_is_generated_automatically()
    {
        $portfolioData = [
            'title' => 'Otomatik Slug Testi',
            'description' => 'Slug otomatik oluşturulsun',
            'category_id' => $this->category->id,
            'status' => 'active'
        ];
        
        $response = $this->actingAs($this->adminUser)
                         ->post('/admin/portfolios', $portfolioData);
        
        $this->assertDatabaseHas('portfolios', [
            'title' => 'Otomatik Slug Testi',
            'slug' => 'otomatik-slug-testi'
        ]);
    }

    /** @test */
    public function portfolio_slug_must_be_unique()
    {
        // Aynı slug'a sahip ikinci portfolio oluşturmaya çalış
        $portfolioData = [
            'title' => 'Test Portfolio',
            'slug' => 'test-portfolio', // Mevcut portfolio ile aynı slug
            'description' => 'İkinci test portfolio',
            'category_id' => $this->category->id,
            'status' => 'active'
        ];
        
        $response = $this->actingAs($this->adminUser)
                         ->post('/admin/portfolios', $portfolioData);
        
        $response->assertSessionHasErrors(['slug']);
    }

    /** @test */
    public function guest_can_view_portfolio_detail()
    {
        $response = $this->get('/portfolios/' . $this->portfolio->slug);
        $response->assertStatus(200);
        $response->assertSee($this->portfolio->title);
        $response->assertSee($this->portfolio->description);
    }

    /** @test */
    public function inactive_portfolio_not_visible_on_frontend()
    {
        $this->portfolio->update(['status' => 'inactive']);
        
        $response = $this->get('/portfolios/' . $this->portfolio->slug);
        $response->assertStatus(404);
    }

    /** @test */
    public function portfolios_can_be_filtered_by_category()
    {
        $category2 = PortfolioCategory::create([
            'name' => 'Mobil Uygulama',
            'slug' => 'mobil-uygulama',
            'status' => 'active'
        ]);
        
        Portfolio::create([
            'title' => 'Mobil Portfolio',
            'slug' => 'mobil-portfolio',
            'description' => 'Mobil uygulama',
            'category_id' => $category2->id,
            'status' => 'active'
        ]);
        
        $response = $this->get('/portfolios?category=' . $category2->slug);
        $response->assertStatus(200);
        $response->assertSee('Mobil Portfolio');
        $response->assertDontSee($this->portfolio->title);
    }

    /** @test */
    public function portfolios_can_be_searched()
    {
        $response = $this->get('/portfolios?search=Test');
        $response->assertStatus(200);
        $response->assertSee($this->portfolio->title);
        
        $response = $this->get('/portfolios?search=Bulunamaz');
        $response->assertStatus(200);
        $response->assertDontSee($this->portfolio->title);
    }

    /** @test */
    public function portfolio_images_are_resized_correctly()
    {
        Storage::fake('tenant');
        
        $largeImage = UploadedFile::fake()->image('large.jpg', 2000, 2000);
        
        $portfolioData = [
            'title' => 'Görsel Test Portfolio',
            'description' => 'Görsel test açıklaması',
            'category_id' => $this->category->id,
            'status' => 'active',
            'featured_image' => $largeImage
        ];
        
        $response = $this->actingAs($this->adminUser)
                         ->post('/admin/portfolios', $portfolioData);
        
        $response->assertRedirect();
        
        // Ana görsel ve thumbnail'in oluşturulduğunu kontrol et
        $portfolio = Portfolio::where('title', 'Görsel Test Portfolio')->first();
        $this->assertNotNull($portfolio->featured_image);
        
        // Thumbnail dosyasının oluşturulduğunu kontrol et
        $thumbnailPath = 'portfolios/thumbnails/' . basename($portfolio->featured_image);
        Storage::disk('tenant')->assertExists($thumbnailPath);
    }

    /** @test */
    public function admin_can_bulk_delete_portfolios()
    {
        $portfolio2 = Portfolio::create([
            'title' => 'İkinci Portfolio',
            'slug' => 'ikinci-portfolio',
            'description' => 'İkinci test portfolio',
            'category_id' => $this->category->id,
            'status' => 'active'
        ]);
        
        $response = $this->actingAs($this->adminUser)
                         ->delete('/admin/portfolios/bulk', [
                             'ids' => [$this->portfolio->id, $portfolio2->id]
                         ]);
        
        $response->assertRedirect();
        
        // Her iki portfolio'nun da silindiğini kontrol et
        $this->assertSoftDeleted('portfolios', ['id' => $this->portfolio->id]);
        $this->assertSoftDeleted('portfolios', ['id' => $portfolio2->id]);
    }

    /** @test */
    public function admin_can_bulk_change_status()
    {
        $portfolio2 = Portfolio::create([
            'title' => 'İkinci Portfolio',
            'slug' => 'ikinci-portfolio',
            'description' => 'İkinci test portfolio',
            'category_id' => $this->category->id,
            'status' => 'active'
        ]);
        
        $response = $this->actingAs($this->adminUser)
                         ->patch('/admin/portfolios/bulk-status', [
                             'ids' => [$this->portfolio->id, $portfolio2->id],
                             'status' => 'inactive'
                         ]);
        
        $response->assertRedirect();
        
        // Her iki portfolio'nun da durumunun değiştiğini kontrol et
        $this->assertDatabaseHas('portfolios', [
            'id' => $this->portfolio->id,
            'status' => 'inactive'
        ]);
        
        $this->assertDatabaseHas('portfolios', [
            'id' => $portfolio2->id,
            'status' => 'inactive'
        ]);
    }

    /** @test */
    public function portfolio_categories_are_managed_correctly()
    {
        // Kategori oluşturma
        $categoryData = [
            'name' => 'E-ticaret',
            'description' => 'E-ticaret projeleri',
            'status' => 'active'
        ];
        
        $response = $this->actingAs($this->adminUser)
                         ->post('/admin/portfolio-categories', $categoryData);
        
        $response->assertRedirect();
        
        $this->assertDatabaseHas('portfolio_categories', [
            'name' => 'E-ticaret',
            'slug' => 'e-ticaret'
        ]);
    }

    /** @test */
    public function portfolio_with_gallery_images()
    {
        Storage::fake('tenant');
        
        $image1 = UploadedFile::fake()->image('gallery1.jpg');
        $image2 = UploadedFile::fake()->image('gallery2.jpg');
        
        $portfolioData = [
            'title' => 'Galeri Test Portfolio',
            'description' => 'Galeri testi',
            'category_id' => $this->category->id,
            'status' => 'active',
            'gallery_images' => [$image1, $image2]
        ];
        
        $response = $this->actingAs($this->adminUser)
                         ->post('/admin/portfolios', $portfolioData);
        
        $response->assertRedirect();
        
        // Galeri görsellerinin kaydedildiğini kontrol et
        Storage::disk('tenant')->assertExists('portfolios/gallery/' . $image1->hashName());
        Storage::disk('tenant')->assertExists('portfolios/gallery/' . $image2->hashName());
    }

    /** @test */
    public function portfolio_seo_fields_are_saved()
    {
        $portfolioData = [
            'title' => 'SEO Test Portfolio',
            'description' => 'SEO test açıklaması',
            'category_id' => $this->category->id,
            'status' => 'active',
            'meta_title' => 'SEO Başlığı',
            'meta_description' => 'SEO açıklaması',
            'meta_keywords' => 'seo, test, portfolio'
        ];
        
        $response = $this->actingAs($this->adminUser)
                         ->post('/admin/portfolios', $portfolioData);
        
        $response->assertRedirect();
        
        $this->assertDatabaseHas('portfolios', [
            'title' => 'SEO Test Portfolio',
            'meta_title' => 'SEO Başlığı',
            'meta_description' => 'SEO açıklaması',
            'meta_keywords' => 'seo, test, portfolio'
        ]);
    }

    /** @test */
    public function portfolio_view_counter_works()
    {
        $initialViews = $this->portfolio->views ?? 0;
        
        // Portfolio detay sayfasını ziyaret et
        $this->get('/portfolios/' . $this->portfolio->slug);
        
        // View sayısının arttığını kontrol et
        $this->portfolio->refresh();
        $this->assertEquals($initialViews + 1, $this->portfolio->views);
    }

    /** @test */
    public function tenant_isolation_works_for_portfolios()
    {
        $this->switchTenant('another-tenant');
        
        // Farklı tenant'ta portfolio oluştur
        $otherTenantCategory = PortfolioCategory::create([
            'name' => 'Other Tenant Category',
            'slug' => 'other-tenant-category',
            'status' => 'active'
        ]);
        
        $otherTenantPortfolio = Portfolio::create([
            'title' => 'Other Tenant Portfolio',
            'slug' => 'other-tenant-portfolio',
            'description' => 'Other tenant description',
            'category_id' => $otherTenantCategory->id,
            'status' => 'active'
        ]);
        
        // Ana tenant'a geri dön
        $this->switchTenant('main-tenant');
        
        // Ana tenant'taki kullanıcı diğer tenant'ın portfolio'sunu görememeli
        $response = $this->get('/portfolios/' . $otherTenantPortfolio->slug);
        $response->assertStatus(404);
    }

    private function initializeTenancy()
    {
        // Tenant context'i ayarla
    }

    private function switchTenant($tenantId)
    {
        // Tenant switching logic
    }
}