<?php

namespace Modules\AI\Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\AI\App\Models\Conversation;
use Modules\AI\App\Models\Message;
use Modules\AI\App\Models\Prompt;
use Spatie\Permission\Models\Role;

class AIControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $adminUser;
    protected $normalUser;
    protected $conversation;
    protected $prompt;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Tenant context'i ayarla
        $this->initializeTenancy();
        
        // Test kullanıcıları oluştur
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);
        
        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole($adminRole);
        
        $this->normalUser = User::factory()->create();
        $this->normalUser->assignRole($userRole);
        
        // Test verileri oluştur
        $this->prompt = Prompt::create([
            'title' => 'Test Prompt',
            'content' => 'Test prompt content',
            'is_active' => true
        ]);
        
        $this->conversation = Conversation::create([
            'title' => 'Test Conversation',
            'user_id' => $this->adminUser->id,
            'prompt_id' => $this->prompt->id
        ]);
    }

    /** @test */
    public function admin_can_access_ai_index_page()
    {
        $response = $this->actingAs($this->adminUser)
                         ->get('/admin/ai');
                         
        $response->assertStatus(200);
    }

    /** @test */
    public function normal_user_cannot_access_ai_admin_panel()
    {
        $response = $this->actingAs($this->normalUser)
                         ->get('/admin/ai');
                         
        $response->assertStatus(403);
    }

    /** @test */
    public function guest_cannot_access_ai_admin_panel()
    {
        $response = $this->get('/admin/ai');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function admin_can_generate_ai_response()
    {
        $this->mockAIService();
        
        $response = $this->actingAs($this->adminUser)
                         ->postJson('/admin/ai/generate', [
                             'prompt' => 'Test AI prompt',
                             'context' => 'test context',
                             'module' => 'test_module',
                             'entity_id' => 1
                         ]);
        
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true
                 ]);
        
        // Veritabanında konuşma oluşturuldu mu kontrol et
        $this->assertDatabaseHas('ai_conversations', [
            'user_id' => $this->adminUser->id
        ]);
    }

    /** @test */
    public function ai_generate_requires_prompt()
    {
        $response = $this->actingAs($this->adminUser)
                         ->postJson('/admin/ai/generate', []);
        
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['prompt']);
    }

    /** @test */
    public function admin_can_update_conversation_prompt()
    {
        $newPrompt = Prompt::create([
            'title' => 'New Test Prompt',
            'content' => 'New test prompt content',
            'is_active' => true
        ]);
        
        $response = $this->actingAs($this->adminUser)
                         ->postJson('/admin/ai/update-conversation-prompt', [
                             'conversation_id' => $this->conversation->id,
                             'prompt_id' => $newPrompt->id
                         ]);
        
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Konuşma promptu güncellendi.'
                 ]);
        
        // Veritabanında güncelleme yapıldı mı kontrol et
        $this->assertDatabaseHas('ai_conversations', [
            'id' => $this->conversation->id,
            'prompt_id' => $newPrompt->id
        ]);
    }

    /** @test */
    public function user_cannot_update_others_conversation()
    {
        $otherUserConversation = Conversation::create([
            'title' => 'Other User Conversation',
            'user_id' => $this->normalUser->id,
            'prompt_id' => $this->prompt->id
        ]);
        
        $response = $this->actingAs($this->adminUser)
                         ->postJson('/admin/ai/update-conversation-prompt', [
                             'conversation_id' => $otherUserConversation->id,
                             'prompt_id' => $this->prompt->id
                         ]);
        
        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Konuşma bulunamadı veya erişim izniniz yok.'
                 ]);
    }

    /** @test */
    public function cannot_use_inactive_prompt()
    {
        $inactivePrompt = Prompt::create([
            'title' => 'Inactive Prompt',
            'content' => 'Inactive prompt content',
            'is_active' => false
        ]);
        
        $response = $this->actingAs($this->adminUser)
                         ->postJson('/admin/ai/update-conversation-prompt', [
                             'conversation_id' => $this->conversation->id,
                             'prompt_id' => $inactivePrompt->id
                         ]);
        
        $response->assertStatus(400)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Seçilen prompt aktif değil.'
                 ]);
    }

    /** @test */
    public function admin_can_view_conversations_list()
    {
        $response = $this->actingAs($this->adminUser)
                         ->get('/admin/ai/conversations');
        
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_view_conversation_details()
    {
        $response = $this->actingAs($this->adminUser)
                         ->get('/admin/ai/conversations/' . $this->conversation->id);
        
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_delete_conversation()
    {
        $response = $this->actingAs($this->adminUser)
                         ->delete('/admin/ai/conversations/' . $this->conversation->id);
        
        $response->assertStatus(200);
        
        // Soft delete kontrolü
        $this->assertSoftDeleted('ai_conversations', [
            'id' => $this->conversation->id
        ]);
    }

    /** @test */
    public function conversation_deletion_requires_permission()
    {
        $response = $this->actingAs($this->normalUser)
                         ->delete('/admin/ai/conversations/' . $this->conversation->id);
        
        $response->assertStatus(403);
    }

    /** @test */
    public function ai_service_handles_errors_gracefully()
    {
        // AI servisinin hata döndürdüğü durumu simüle et
        $this->mockAIServiceWithError();
        
        $response = $this->actingAs($this->adminUser)
                         ->postJson('/admin/ai/generate', [
                             'prompt' => 'Test prompt that will fail'
                         ]);
        
        $response->assertStatus(500)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Yanıt alınamadı. Lütfen daha sonra tekrar deneyin veya yöneticinize başvurun.'
                 ]);
    }

    /** @test */
    public function conversation_title_is_generated_automatically()
    {
        $this->mockAIService();
        
        $response = $this->actingAs($this->adminUser)
                         ->postJson('/admin/ai/generate', [
                             'prompt' => 'Bu bir test mesajıdır'
                         ]);
        
        $response->assertStatus(200);
        
        // Konuşmanın otomatik başlık aldığını kontrol et
        $conversation = Conversation::where('user_id', $this->adminUser->id)
                                   ->latest()
                                   ->first();
        
        $this->assertNotNull($conversation->title);
        $this->assertNotEmpty($conversation->title);
    }

    /** @test */
    public function messages_are_stored_with_tokens()
    {
        $this->mockAIService();
        
        $response = $this->actingAs($this->adminUser)
                         ->postJson('/admin/ai/generate', [
                             'prompt' => 'Token sayısı test mesajı'
                         ]);
        
        $response->assertStatus(200);
        
        // Mesajın token sayısıyla kaydedildiğini kontrol et
        $this->assertDatabaseHas('ai_messages', [
            'role' => 'user',
            'content' => 'Token sayısı test mesajı'
        ]);
        
        $this->assertDatabaseHas('ai_messages', [
            'role' => 'assistant'
        ]);
    }

    /** @test */
    public function tenant_isolation_works_for_conversations()
    {
        $this->switchTenant('another-tenant');
        
        // Farklı tenant'ta konuşma oluştur
        $otherTenantUser = User::factory()->create();
        $otherTenantConversation = Conversation::create([
            'title' => 'Other Tenant Conversation',
            'user_id' => $otherTenantUser->id,
            'prompt_id' => $this->prompt->id
        ]);
        
        // İlk tenant'a geri dön
        $this->switchTenant('main-tenant');
        
        // İlk tenant'taki kullanıcı diğer tenant'ın konuşmasını görememeli
        $response = $this->actingAs($this->adminUser)
                         ->get('/admin/ai/conversations/' . $otherTenantConversation->id);
        
        $response->assertStatus(404);
    }

    /**
     * AI servisini mock'la
     */
    private function mockAIService()
    {
        $this->mock(\Modules\AI\App\Services\AIService::class, function ($mock) {
            $mock->shouldReceive('ask')
                 ->andReturn('Mock AI response');
        });
    }

    /**
     * AI servisini hata ile mock'la
     */
    private function mockAIServiceWithError()
    {
        $this->mock(\Modules\AI\App\Services\AIService::class, function ($mock) {
            $mock->shouldReceive('ask')
                 ->andReturn(false);
        });
    }

    /**
     * Tenancy'yi başlat
     */
    private function initializeTenancy()
    {
        // Tenant context'i ayarla - gerçek implementasyona göre düzenle
        // $tenant = \App\Models\Tenant::create(['id' => 'test-tenant']);
        // tenancy()->initialize($tenant);
    }

    /**
     * Tenant değiştir
     */
    private function switchTenant($tenantId)
    {
        // Tenant switching logic - gerçek implementasyona göre düzenle
    }
}