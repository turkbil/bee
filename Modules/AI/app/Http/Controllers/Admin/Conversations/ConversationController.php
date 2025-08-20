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
        // Root admin ise tüm konuşmaları görebilir
        $query = Conversation::with(['user', 'tenant']);
        
        if (!auth()->user()->hasRole('root')) {
            $query->where('user_id', Auth::id());
        }

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

        if ($request->filled('tenant_id') && auth()->user()->isRoot()) {
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

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $conversations = $query->orderBy('created_at', 'desc')
            ->paginate(30);

        // Filtre seçenekleri
        $filterOptions = [
            'types' => Conversation::select('type')->distinct()->pluck('type'),
            'features' => Conversation::whereNotNull('feature_name')->select('feature_name')->distinct()->pluck('feature_name'),
            'tenants' => auth()->user()->isRoot() ? 
                \App\Models\Tenant::select('id', 'title')->where('id', '!=', 1)->get() : 
                collect()
        ];

        // İstatistikler
        $stats = [
            'total' => Conversation::active()->count(),
            'feature_tests' => Conversation::active()->featureTests()->count(),
            'demo_tests' => Conversation::active()->demoTests()->count(),
            'real_tests' => Conversation::active()->realTests()->count(),
            'chat_conversations' => Conversation::active()->byType('chat')->count(),
        ];

        // Credit istatistikleri - sadece aktif konuşmaları
        $activeConversationIds = Conversation::active()->pluck('id');
        $creditStats = [
            'total_credits_used' => AICreditUsage::whereIn('conversation_id', $activeConversationIds)->sum('credits_used') ?? 0,
            'avg_credits_per_conversation' => $activeConversationIds->count() > 0 ? 
                (AICreditUsage::whereIn('conversation_id', $activeConversationIds)->sum('credits_used') / $activeConversationIds->count()) : 0,
            'demo_credits_used' => AICreditUsage::whereIn('conversation_id', 
                Conversation::active()->demoTests()->pluck('id'))->sum('credits_used') ?? 0,
            'real_credits_used' => AICreditUsage::whereIn('conversation_id', 
                Conversation::active()->realTests()->pluck('id'))->sum('credits_used') ?? 0,
        ];
            
        return view('ai::admin.conversations.index', compact('conversations', 'filterOptions', 'stats', 'creditStats'));
    }

    public function archived(Request $request)
    {
        // Root admin ise tüm arşivlenmiş konuşmaları görebilir
        $query = Conversation::with(['user', 'tenant'])->where('status', 'archived');
        
        if (!auth()->user()->hasRole('root')) {
            $query->where('user_id', Auth::id());
        }

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

        if ($request->filled('tenant_id') && auth()->user()->isRoot()) {
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
            'tenants' => auth()->user()->isRoot() ? 
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
        
        // Root admin değilse sadece kendi konuşmalarını görebilir
        if (!auth()->user()->hasRole('root')) {
            $query->where('user_id', Auth::id());
        }
        
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
        
        // Root admin değilse sadece kendi konuşmalarını silebilir
        if (!auth()->user()->hasRole('root')) {
            $query->where('user_id', Auth::id());
        }
        
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
        
        if (!auth()->user()->hasRole('root')) {
            $query->where('user_id', Auth::id());
        }
        
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
        
        if (!auth()->user()->hasRole('root')) {
            $query->where('user_id', Auth::id());
        }
        
        $conversation = $query->findOrFail($id);
        $conversation->update(['status' => 'active']);
        
        return redirect()->back()
            ->with('success', 'Konuşma arşivden çıkarıldı.');
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
        
        if (!auth()->user()->hasRole('root')) {
            $query->where('user_id', Auth::id());
        }

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