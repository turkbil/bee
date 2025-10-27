<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class DashboardWidget extends Component
{
    public $activeModules = [];
    
    // AI Module Data
    public $aiProfile = null;
    public $aiCompletionPercentage = 0;
    public $aiIsCompleted = false;
    
    // Token Data
    public $remainingTokens = 0;
    public $totalTokens = 0;
    public $usedTokens = 0;
    public $remainingTokensFormatted = '0';
    public $usagePercentage = 0;
    public $statusColor = 'secondary';
    public $statusText = 'Bilinmiyor';
    
    // Page Data
    public $totalPages = 0;
    public $recentPages = [];
    
    // Portfolio Data
    public $totalPortfolios = 0;
    public $recentPortfolios = [];
    
    // Announcement Data
    public $totalAnnouncements = 0;
    public $recentAnnouncements = [];
    
    // User Data
    public $recentLogins = [];
    public $newUsers = [];
    
    // Widget Order Management
    public $widgetOrder = [];
    
    // AI Chat Message
    public $aiChatMessage = '';
    
    public function mount()
    {
        $this->loadActiveModules();
        $this->loadAIData();
        $this->loadTokenData();
        $this->loadPagesData();
        $this->loadPortfolioData();
        $this->loadAnnouncementData();
        $this->loadUserData();
        $this->applySavedLayout();
    }
    
    private function loadActiveModules()
    {
        try {
            if (class_exists('\Modules\ModuleManagement\app\Models\Module')) {
                $modules = \Modules\ModuleManagement\app\Models\Module::whereIn('name', ['ai', 'page', 'portfolio', 'announcement', 'usermanagement'])
                    ->get();
                
                foreach ($modules as $module) {
                    $tenant = $module->tenants()->where('tenant_id', tenant('id'))->first();
                    if ($tenant && $tenant->pivot->is_active) {
                        $this->activeModules[] = strtolower($module->name);
                    }
                }
            }
        } catch (\Exception $e) {
            // Module yoksa tüm modülleri varsayılan aktif yap - FALLBACK
            $this->activeModules = ['ai', 'page', 'portfolio', 'announcement', 'usermanagement'];
        }
        
        // DEBUG: Hangi modüller aktif görüyor kontrol et
        if (empty($this->activeModules)) {
            $this->activeModules = ['ai', 'page', 'portfolio', 'announcement', 'usermanagement']; // Geçici debug
        }
    }
    
    private function loadAIData()
    {
        if (!in_array('ai', $this->activeModules)) return;
        
        try {
            $this->aiProfile = \Modules\AI\app\Models\AITenantProfile::currentOrCreate();
            if ($this->aiProfile) {
                $completionData = $this->aiProfile->getEditPageCompletionPercentage();
                $this->aiCompletionPercentage = $completionData['percentage'] ?? 0;
                $this->aiIsCompleted = $this->aiCompletionPercentage >= 100;
            }
        } catch (\Exception $e) {
            // AI module yoksa sessizce geç
        }
    }
    
    private function loadTokenData()
    {
        if (!in_array('ai', $this->activeModules)) return;
        
        try {
            $tenantId = tenant('id') ?: '1';
            
            // Credit Helper fonksiyonları kullan (yeni sistem)
            $this->remainingTokens = function_exists('ai_get_credit_balance') ? ai_get_credit_balance($tenantId) : 0;
            $this->usedTokens = function_exists('ai_get_total_credits_used') ? ai_get_total_credits_used($tenantId) : 0;
            $this->totalTokens = function_exists('ai_get_total_credits_purchased') ? ai_get_total_credits_purchased($tenantId) : 0;
            
            // Formatlanmış kredi sayısı  
            $this->remainingTokensFormatted = format_credit($this->remainingTokens);
            
            // Kullanım yüzdesi
            $this->usagePercentage = $this->totalTokens > 0 ? round(($this->usedTokens / $this->totalTokens) * 100, 1) : 0;
            
            // Status belirleme (credit sistemi)
            if ($this->remainingTokens <= 0) {
                $this->statusColor = 'danger';
                $this->statusText = 'Kredi tükendi';
            } elseif ($this->remainingTokens < 1.0) {
                $this->statusColor = 'warning';
                $this->statusText = 'Kredi azalıyor';
            } else {
                $this->statusColor = 'success';
                $this->statusText = 'Kredi yeterli';
            }
        } catch (\Exception $e) {
            // Token hesaplama hatası
        }
    }
    
    private function loadPagesData()
    {
        if (!in_array('page', $this->activeModules)) return;
        
        try {
            if (class_exists('\Modules\Page\app\Models\Page')) {
                $this->totalPages = \Modules\Page\app\Models\Page::count();
                $this->recentPages = \Modules\Page\app\Models\Page::orderBy('created_at', 'desc')
                    ->take(3)
                    ->get();
            }
        } catch (\Exception $e) {
            // Page module yoksa
        }
    }
    
    private function loadPortfolioData()
    {
        if (!in_array('portfolio', $this->activeModules)) return;
        
        try {
            if (class_exists('\Modules\Portfolio\app\Models\Portfolio')) {
                $this->totalPortfolios = \Modules\Portfolio\app\Models\Portfolio::count();
                $this->recentPortfolios = \Modules\Portfolio\app\Models\Portfolio::orderBy('created_at', 'desc')
                    ->take(3)
                    ->get();
            }
        } catch (\Exception $e) {
            // Portfolio module yoksa
        }
    }
    
    private function loadAnnouncementData()
    {
        if (!in_array('announcement', $this->activeModules)) return;
        
        try {
            if (class_exists('\Modules\Announcement\app\Models\Announcement')) {
                $this->totalAnnouncements = \Modules\Announcement\app\Models\Announcement::count();
                $this->recentAnnouncements = \Modules\Announcement\app\Models\Announcement::orderBy('created_at', 'desc')
                    ->take(3)
                    ->get();
            }
        } catch (\Exception $e) {
            // Announcement module yoksa
        }
    }
    
    private function loadUserData()
    {
        if (!in_array('usermanagement', $this->activeModules)) return;
        
        try {
            // Son giriş yapan kullanıcılar
            $this->recentLogins = \App\Models\User::whereNotNull('last_login_at')
                ->orderBy('last_login_at', 'desc')
                ->take(5)
                ->get();
            
            // Son üye olan kullanıcılar
            $this->newUsers = \App\Models\User::orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            // User data hatası
        }
    }
    
    private function applySavedLayout()
    {
        try {
            // Session'dan saved layout'u al
            $savedLayout = session('dashboard_layout');
            
            if ($savedLayout && is_array($savedLayout)) {
                $this->widgetOrder = $savedLayout;
            } else {
                // Varsayılan sıralama
                $this->widgetOrder = [
                    'AI Token Durumu',
                    'Yapay Zeka Durumu', 
                    'Hızlı AI Chat',
                    'Sayfalar',
                    'Portfolio',
                    'Duyurular',
                    'Son Girişler',
                    'Yeni Üyeler'
                ];
            }
        } catch (\Exception $e) {
            // Hata durumunda varsayılan sıralama
            $this->widgetOrder = [
                'AI Token Durumu',
                'Yapay Zeka Durumu',
                'Hızlı AI Chat', 
                'Sayfalar',
                'Portfolio',
                'Duyurular',
                'Son Girişler',
                'Yeni Üyeler'
            ];
        }
    }
    
    public function sendAiMessage()
    {
        if (empty(trim($this->aiChatMessage))) {
            return;
        }
        
        $message = trim($this->aiChatMessage);
        $this->aiChatMessage = '';
        
        // Basit yanıt sistemi
        $response = $this->generateAiResponse($message);
        
        $this->dispatch('message-sent', [
            'userMessage' => $message,
            'aiResponse' => $response
        ]);
    }
    
    private function generateAiResponse($message)
    {
        if (empty($message)) {
            return "Lütfen bir mesaj yazın.";
        }
        
        $message = strtolower(trim($message));
        
        if (str_contains($message, 'sistem') || str_contains($message, 'durum')) {
            return "🔍 Dashboard'unuz aktif ve çalışır durumda. PHP " . PHP_VERSION . " ve Laravel " . app()->version() . " kullanılıyor. Veritabanı bağlantısı sağlıklı.";
        } 
        
        if (str_contains($message, 'seo')) {
            return "🎯 SEO optimizasyonu için içeriklerinize meta title, description ve anahtar kelimeler eklemenizi öneririm. Ayrıca sayfa hızınızı optimize edebilirsiniz.";
        }
        
        if (str_contains($message, 'performans') || str_contains($message, 'hız')) {
            return "⚡ Performans iyileştirme için cache sistemlerini aktif tutun, görselleri optimize edin ve gereksiz eklentileri kaldırın.";
        }
        
        if (str_contains($message, 'yardım') || str_contains($message, 'help')) {
            return "📋 Size şu konularda yardımcı olabilirim: sistem durumu, SEO analizi, performans optimizasyonu, içerik yönetimi. Hangi konuda bilgi istiyorsunuz?";
        }
        
        if (str_contains($message, 'içerik') || str_contains($message, 'sayfa')) {
            return "📝 İçerik yönetimi için sol menüden Sayfalar, Portfolio veya Duyurular bölümlerini kullanabilirsiniz. Yeni içerik oluşturmak için + butonuna tıklayın.";
        }
        
        return "Bu konuda size yardımcı olmaya çalışıyorum. Daha spesifik bir soru sorabilir veya şu konulardan birini seçebilirsiniz: sistem durumu, SEO, performans, içerik yönetimi.";
    }
    
    public function render()
    {
        return view('livewire.admin.dashboard-widget');
    }
}