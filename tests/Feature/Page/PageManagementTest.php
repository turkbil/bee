<?php

namespace Tests\Feature\Page;

use Tests\TestCase;
use Modules\Page\App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Page\App\Http\Livewire\Admin\PageComponent;
use Modules\Page\App\Http\Livewire\Admin\PageManageComponent;

class PageManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_list_pages()
    {
        Page::factory()->count(5)->create();

        Livewire::test(PageComponent::class)
            ->assertViewIs('page::admin.livewire.page-component')
            ->assertSee('pages');
    }

    /** @test */
    public function it_can_search_pages()
    {
        Page::factory()->create(['title' => json_encode(['tr' => 'Laravel CMS'])]);
        Page::factory()->create(['title' => json_encode(['tr' => 'PHP Tutorial'])]);

        Livewire::test(PageComponent::class)
            ->set('search', 'Laravel')
            ->assertSee('Laravel CMS')
            ->assertDontSee('PHP Tutorial');
    }

    /** @test */
    public function it_can_toggle_page_status()
    {
        $page = Page::factory()->create(['is_active' => true]);

        Livewire::test(PageComponent::class)
            ->call('toggleActive', $page->page_id);

        $page->refresh();
        $this->assertFalse($page->is_active);
    }

    /** @test */
    public function it_can_create_new_page()
    {
        Livewire::test(PageManageComponent::class)
            ->set('multiLangInputs.title.tr', 'Test Sayfa')
            ->set('multiLangInputs.title.en', 'Test Page')
            ->set('multiLangInputs.slug.tr', 'test-sayfa')
            ->set('multiLangInputs.slug.en', 'test-page')
            ->set('inputs.is_active', true)
            ->call('save');

        $this->assertDatabaseHas('pages', [
            'title->tr' => 'Test Sayfa',
            'title->en' => 'Test Page',
            'is_active' => true
        ]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        Livewire::test(PageManageComponent::class)
            ->set('multiLangInputs.title.tr', '')
            ->call('save')
            ->assertHasErrors(['multiLangInputs.title.tr']);
    }

    /** @test */
    public function it_can_update_existing_page()
    {
        $page = Page::factory()->create([
            'title' => json_encode(['tr' => 'Eski Başlık', 'en' => 'Old Title'])
        ]);

        Livewire::test(PageManageComponent::class, ['pageId' => $page->page_id])
            ->set('multiLangInputs.title.tr', 'Yeni Başlık')
            ->set('multiLangInputs.title.en', 'New Title')
            ->call('save');

        $page->refresh();
        $this->assertEquals('Yeni Başlık', $page->getTitle('tr'));
        $this->assertEquals('New Title', $page->getTitle('en'));
    }
}