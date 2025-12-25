<?php
namespace Modules\AI\App\Http\Controllers\Admin\Conversations;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\AI\App\Models\Conversation;
use Modules\AI\App\Models\Message;
use Modules\AI\App\Models\AICreditUsage;
use Modules\AI\App\Services\AIService;
use Modules\AI\App\Services\MarkdownService;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    protected $aiService;
    protected $markdownService;

    public function __construct(AIService $aiService, MarkdownService $markdownService)
    {
        $this->aiService = $aiService;
        $this->markdownService = $markdownService;
    }

    public function index(Request $request)
    {
        // Central tenant (is_central = 1) tüm konuşmaları görebilir, diğer tenant'lar sadece kendi verilerini
        $query = Conversation::with(['user', 'tenant'])->withCount('messages');

        $currentTenantId = tenant('id');
        $isCentral = tenant() ? tenant()->is_central : false;

        // Central tenant değilse sadece kendi tenant'ının konuşmalarını göster
        if (!$isCentral && $currentTenantId) {
            $query->where('tenant_id', $currentTenantId);
        }
        // Central tenant (is_central = 1) ise tüm konuşmaları görebilir, filtre uygulanmaz

        // Filtreler
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('feature_name')) {
            $query->where('feature_name', $request->feature_name);
        }

        if ($request->filled('is_demo')) {
            $query->where('is_demo', $request->boolean('is_demo'));
        }

        // Central tenant (is_central = 1) ise tenant filtrelemesine izin ver
        if ($request->filled('tenant_id') && $isCentral) {
            $query->where('tenant_id', $request->tenant_id);
        }

        // Durum filtresi (active, archived, all)
        $status = $request->get('status', 'active');
        if ($status === 'active') {
            $query->where('status', 'active');
        } elseif ($status === 'archived') {
            $query->where('status', 'archived');
        }
        // 'all' seçilirse hiç filtre uygulanmaz

        // Test konuşmalarını gizle (varsayılan: gizli)
        if (!$request->boolean('show_tests')) {
            $query->where('type', '!=', 'feature_test');
        }

        // Kısa konuşmaları gizle (<=2 mesaj, varsayılan: gizli)
        if (!$request->boolean('show_short')) {
            $query->has('messages', '>', 2);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $perPage = $request->get('per_page', 15);
        $conversations = $query->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Filtre seçenekleri
        $filterOptions = [
            'types' => Conversation::select('type')->distinct()->pluck('type'),
            'features' => Conversation::whereNotNull('feature_name')->select('feature_name')->distinct()->pluck('feature_name'),
            // Central tenant (is_central = 1) ise tüm tenant'ları göster, diğer tenant'lar göremez
            'tenants' => $isCentral ?
                \App\Models\Tenant::select('id', 'title')->where('id', '!=', 1)->get() :
                collect()
        ];

        // İstatistikler - tenant'a göre filtrelenmiş
        $statsQuery = Conversation::active();

        // Central tenant değilse sadece kendi tenant'ının istatistiklerini göster
        if (!$isCentral && $currentTenantId) {
            $statsQuery->where('tenant_id', $currentTenantId);
        }
        // Central tenant (is_central = 1) ise tüm istatistikleri görebilir

        // Base query'yi klonla
        $stats = [
            'total' => (clone $statsQuery)->count(),
            'feature_tests' => (clone $statsQuery)->featureTests()->count(),
            'demo_tests' => (clone $statsQuery)->demoTests()->count(),
            'real_tests' => (clone $statsQuery)->realTests()->count(),
            'chat_conversations' => (clone $statsQuery)->byType('chat')->count(),
        ];

        // Credit istatistikleri - sadece aktif konuşmaları (tenant filtrelenmiş)
        $activeConversationIds = (clone $statsQuery)->pluck('id');
        $creditStats = [
            'total_credits_used' => AICreditUsage::whereIn('conversation_id', $activeConversationIds)->sum('credits_used') ?? 0,
            'avg_credits_per_conversation' => $activeConversationIds->count() > 0 ?
                (AICreditUsage::whereIn('conversation_id', $activeConversationIds)->sum('credits_used') / $activeConversationIds->count()) : 0,
            'demo_credits_used' => AICreditUsage::whereIn('conversation_id',
                (clone $statsQuery)->demoTests()->pluck('id'))->sum('credits_used') ?? 0,
            'real_credits_used' => AICreditUsage::whereIn('conversation_id',
                (clone $statsQuery)->realTests()->pluck('id'))->sum('credits_used') ?? 0,
        ];
            
        return view('ai::admin.conversations.index', compact('conversations', 'filterOptions', 'stats', 'creditStats'));
    }

    public function archived(Request $request)
    {
        // Central tenant (is_central = 1) tüm arşivlenmiş konuşmaları görebilir, diğer tenant'lar sadece kendi verilerini
        $query = Conversation::with(['user', 'tenant'])->where('status', 'archived');

        $currentTenantId = tenant('id');
        $isCentral = tenant() ? tenant()->is_central : false;

        // Central tenant değilse sadece kendi tenant'ının arşivlenmiş konuşmalarını göster
        if (!$isCentral && $currentTenantId) {
            $query->where('tenant_id', $currentTenantId);
        }
        // Central tenant (is_central = 1) ise tüm arşivlenmiş konuşmaları görebilir

        // Filtreler (durum hariç)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('feature_name')) {
            $query->where('feature_name', $request->feature_name);
        }

        if ($request->filled('is_demo')) {
            $query->where('is_demo', $request->boolean('is_demo'));
        }

        // Central tenant (is_central = 1) ise tenant filtrelemesine izin ver
        if ($request->filled('tenant_id') && $isCentral) {
            $query->where('tenant_id', $request->tenant_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $conversations = $query->orderBy('updated_at', 'desc')
            ->paginate(30);

        // Filtre seçenekleri (sadece arşivlenmişlerden)
        $filterOptions = [
            'types' => Conversation::select('type')->where('status', 'archived')->distinct()->pluck('type'),
            'features' => Conversation::whereNotNull('feature_name')->where('status', 'archived')->select('feature_name')->distinct()->pluck('feature_name'),
            // Central tenant (is_central = 1) ise tüm tenant'ları göster, diğer tenant'lar göremez
            'tenants' => $isCentral ?
                \App\Models\Tenant::select('id', 'title')->where('id', '!=', 1)->get() :
                collect()
        ];

        // Arşiv istatistikleri
        $stats = [
            'total' => Conversation::where('status', 'archived')->count(),
            'feature_tests' => Conversation::where('status', 'archived')->featureTests()->count(),
            'demo_tests' => Conversation::where('status', 'archived')->demoTests()->count(),
            'real_tests' => Conversation::where('status', 'archived')->realTests()->count(),
            'chat_conversations' => Conversation::where('status', 'archived')->byType('chat')->count(),
        ];
            
        return view('ai::admin.conversations.archived', compact('conversations', 'filterOptions', 'stats'));
    }

    public function show($id)
    {
        $query = Conversation::with(['user', 'tenant', 'messages']);

        $currentTenantId = tenant('id');
        $isCentral = tenant() ? tenant()->is_central : false;

        // Central tenant değilse sadece kendi tenant'ının konuşmalarını görebilir
        if (!$isCentral && $currentTenantId) {
            $query->where('tenant_id', $currentTenantId);
        }
        // Central tenant (is_central = 1) ise tüm konuşmaları görebilir

        $conversation = $query->findOrFail($id);
            
        $messages = $conversation->messages()
            ->orderBy('created_at')
            ->get();

        // AI mesajlarında markdown işleme ve meta bilgileri
        $messages->each(function($message) {
            if ($message->role === 'assistant' && $this->markdownService->hasMarkdown($message->content)) {
                $message->html_content = $this->markdownService->convertToHtml($message->content);
                $message->has_markdown = true;
            } else {
                $message->has_markdown = false;
                $message->html_content = null;
            }
            
            // Meta bilgileri hazırla
            $metaParts = [];
            
            // Tam tarih saat
            $metaParts[] = $message->created_at->format('d.m.Y H:i:s');
            
            // Token bilgisi
            if ($message->tokens > 0) {
                $metaParts[] = ai_format_token_count($message->tokens) . ' token';
            }
            
            // İşlem süresi
            if ($message->processing_time_ms > 0) {
                $seconds = $message->processing_time_ms / 1000;
                $metaParts[] = number_format($seconds, 1) . ' sn';
            }
            
            // Model bilgisi kaldırıldı - gerek yok
            
            $message->meta_text = implode(' • ', $metaParts);
        });

        // İstatistikler
        $messageStats = [
            'total_messages' => $messages->count(),
            'user_messages' => $messages->where('role', 'user')->count(),
            'ai_messages' => $messages->where('role', 'assistant')->count(),
            'total_tokens' => $messages->sum('tokens'),
            'prompt_tokens' => $messages->sum('prompt_tokens'),
            'completion_tokens' => $messages->sum('completion_tokens'),
            'avg_processing_time' => $messages->where('processing_time_ms', '>', 0)->avg('processing_time_ms'),
        ];

        // Bu konuşmaya ait credit kullanımları
        $conversationCredits = AICreditUsage::where('conversation_id', $conversation->id)
            ->selectRaw('
                SUM(credits_used) as total_credits,
                COUNT(*) as total_usage_records,
                AVG(credits_used) as avg_credits_per_request,
                provider_name,
                model
            ')
            ->groupBy('provider_name', 'model')
            ->get();

        $creditSummary = [
            'total_credits_used' => $conversationCredits->sum('total_credits'),
            'total_usage_records' => $conversationCredits->sum('total_usage_records'),
            'avg_credits_per_request' => $conversationCredits->avg('avg_credits_per_request'),
            'providers_used' => $conversationCredits->groupBy('provider_name'),
        ];
        
        return view('ai::admin.conversations.show', compact('conversation', 'messages', 'messageStats', 'creditSummary'));
    }

    public function delete($id)
    {
        $query = Conversation::query();

        $currentTenantId = tenant('id');
        $isCentral = tenant() ? tenant()->is_central : false;

        // Central tenant değilse sadece kendi tenant'ının konuşmalarını silebilir
        if (!$isCentral && $currentTenantId) {
            $query->where('tenant_id', $currentTenantId);
        }
        // Central tenant (is_central = 1) ise tüm konuşmaları silebilir

        $conversation = $query->findOrFail($id);
            
        // İlişkili mesajları sil
        Message::where('conversation_id', $conversation->id)->delete();
        
        // Konuşmayı sil
        $conversation->delete();
        
        return redirect()->route('admin.ai.conversations.index')
            ->with('success', 'Konuşma başarıyla silindi.');
    }

    /**
     * Konuşmayı arşivle (silmek yerine)
     */
    public function archive($id)
    {
        $query = Conversation::query();

        $currentTenantId = tenant('id');
        $isCentral = tenant() ? tenant()->is_central : false;

        // Central tenant değilse sadece kendi tenant'ının konuşmalarını arşivleyebilir
        if (!$isCentral && $currentTenantId) {
            $query->where('tenant_id', $currentTenantId);
        }
        // Central tenant (is_central = 1) ise tüm konuşmaları arşivleyebilir

        $conversation = $query->findOrFail($id);
        $conversation->update(['status' => 'archived']);
        
        return redirect()->back()
            ->with('success', 'Konuşma arşivlendi.');
    }

    /**
     * Konuşmayı arşivden çıkar
     */
    public function unarchive($id)
    {
        $query = Conversation::query();

        $currentTenantId = tenant('id');
        $isCentral = tenant() ? tenant()->is_central : false;

        // Central tenant değilse sadece kendi tenant'ının konuşmalarını arşivden çıkarabilir
        if (!$isCentral && $currentTenantId) {
            $query->where('tenant_id', $currentTenantId);
        }
        // Central tenant (is_central = 1) ise tüm konuşmaları arşivden çıkarabilir

        $conversation = $query->findOrFail($id);
        $conversation->update(['status' => 'active']);
        
        return redirect()->back()
            ->with('success', 'Konuşma arşivden çıkarıldı.');
    }

    /**
     * Önizleme - AJAX ile mesajları getir
     */
    public function preview($id)
    {
        $query = Conversation::with(['messages' => function($q) {
            $q->orderBy('created_at', 'asc');
        }]);

        $currentTenantId = tenant('id');
        $isCentral = tenant() ? tenant()->is_central : false;

        // Central tenant değilse sadece kendi tenant'ının konuşmalarını görebilir
        if (!$isCentral && $currentTenantId) {
            $query->where('tenant_id', $currentTenantId);
        }
        // Central tenant (is_central = 1) ise tüm konuşmaları görebilir

        $conversation = $query->findOrFail($id);

        return response()->json([
            'success' => true,
            'conversation' => [
                'id' => $conversation->id,
                'title' => $conversation->title,
                'type' => $conversation->type,
                'feature_name' => $conversation->feature_name,
                'created_at' => $conversation->created_at,
                'message_count' => $conversation->messages->count()
            ],
            'messages' => $conversation->messages->map(function($message) {
                return [
                    'id' => $message->id,
                    'role' => $message->role,
                    'content' => $message->content,
                    'token_count' => $message->tokens,
                    'created_at' => $message->created_at
                ];
            })
        ]);
    }

    /**
     * Bulk actions (toplu işlemler)
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,archive,unarchive',
            'conversations' => 'required|array|min:1',
            'conversations.*' => 'exists:ai_conversations,id'
        ]);

        $query = Conversation::whereIn('id', $request->conversations);

        $currentTenantId = tenant('id');
        $isCentral = tenant() ? tenant()->is_central : false;

        // Central tenant değilse sadece kendi tenant'ının konuşmalarına toplu işlem yapabilir
        if (!$isCentral && $currentTenantId) {
            $query->where('tenant_id', $currentTenantId);
        }
        // Central tenant (is_central = 1) ise tüm konuşmalara toplu işlem yapabilir

        $conversations = $query->get();
        
        if ($request->action === 'delete') {
            foreach ($conversations as $conversation) {
                Message::where('conversation_id', $conversation->id)->delete();
                $conversation->delete();
            }
            $message = count($conversations) . ' konuşma silindi.';
        } elseif ($request->action === 'archive') {
            $conversations->each(function($conversation) {
                $conversation->update(['status' => 'archived']);
            });
            $message = count($conversations) . ' konuşma arşivlendi.';
        } else { // unarchive
            $conversations->each(function($conversation) {
                $conversation->update(['status' => 'active']);
            });
            $message = count($conversations) . ' konuşma arşivden çıkarıldı.';
        }
        
        return redirect()->back()->with('success', $message);
    }
}