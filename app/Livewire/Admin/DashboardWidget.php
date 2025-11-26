<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class DashboardWidget extends Component
{
    public $activeModules = [];

    // Editör için izinli modüller (view & create ayrı)
    public $allowedModulesView = [];
    public $allowedModulesCreate = [];
    public $isEditor = false;
    public $isAdminOrRoot = false;

    // AI Module Data
    public $aiCredit = 0;
    public $aiCreditFormatted = '0';

    // Modül verileri - dinamik
    public $moduleStats = [];
    public $recentItems = [];

    // Widget sistemi
    public $visibleWidgets = [];
    public $gridLayout = 'full'; // full, half, third, quarter

    // AI Chat Message
    public $aiChatMessage = '';

    public function mount()
    {
        $this->checkUserRole();
        $this->loadActiveModules();
        $this->loadModuleStats();
        $this->loadRecentItems();
        $this->calculateLayout();
    }

    private function checkUserRole()
    {
        $user = Auth::user();

        if (!$user) return;

        // Root veya Admin ise tüm yetkilere sahip
        if ($user->isRoot() || $user->isAdmin()) {
            $this->isAdminOrRoot = true;
            $this->isEditor = false;
            return;
        }

        // Editor ise izinlerini kontrol et
        if ($user->isEditor()) {
            $this->isEditor = true;
            $this->isAdminOrRoot = false;
            $this->loadEditorPermissions($user);
        }
    }

    private function loadEditorPermissions($user)
    {
        try {
            $permissions = \Modules\UserManagement\App\Models\UserModulePermission::where('user_id', $user->id)
                ->where('is_active', true)
                ->get();

            foreach ($permissions as $perm) {
                if ($perm->permission_type === 'view') {
                    $this->allowedModulesView[] = strtolower($perm->module_name);
                }
                if ($perm->permission_type === 'create') {
                    $this->allowedModulesCreate[] = strtolower($perm->module_name);
                }
            }
        } catch (\Exception $e) {
            $this->allowedModulesView = [];
            $this->allowedModulesCreate = [];
        }
    }

    public function canViewModule($moduleName)
    {
        $moduleName = strtolower($moduleName);
        if ($this->isAdminOrRoot) return true;
        if (!$this->isEditor) return false;
        return in_array($moduleName, $this->allowedModulesView);
    }

    public function canCreateModule($moduleName)
    {
        $moduleName = strtolower($moduleName);
        if ($this->isAdminOrRoot) return true;
        if (!$this->isEditor) return false;
        return in_array($moduleName, $this->allowedModulesCreate);
    }

    private function loadActiveModules()
    {
        $tenantModules = [];

        try {
            if (class_exists('\Modules\ModuleManagement\app\Models\Module')) {
                $modules = \Modules\ModuleManagement\app\Models\Module::all();

                foreach ($modules as $module) {
                    $tenant = $module->tenants()->where('tenant_id', tenant('id'))->first();
                    if ($tenant && $tenant->pivot->is_active) {
                        $tenantModules[] = strtolower($module->name);
                    }
                }
            }
        } catch (\Exception $e) {
            $tenantModules = [];
        }

        // Editör ise sadece izinli modülleri göster
        if ($this->isEditor) {
            $this->activeModules = array_values(array_intersect($tenantModules, $this->allowedModulesView));
        } else {
            $this->activeModules = $tenantModules;
        }
    }

    private function loadModuleStats()
    {
        $this->moduleStats = [];

        // AI Kredisi
        if (in_array('ai', $this->activeModules) && function_exists('ai_get_credit_balance')) {
            try {
                $this->aiCredit = ai_get_credit_balance();
                $this->aiCreditFormatted = function_exists('format_credit') ? format_credit($this->aiCredit) : number_format($this->aiCredit, 1);

                $this->moduleStats['ai'] = [
                    'name' => 'AI Kredisi',
                    'value' => $this->aiCreditFormatted,
                    'icon' => 'fas fa-coins',
                    'color' => 'warning',
                    'gradient' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
                    'route' => 'admin.ai.index'
                ];
            } catch (\Exception $e) {}
        }

        // Page
        if (in_array('page', $this->activeModules)) {
            try {
                if (class_exists('\Modules\Page\app\Models\Page')) {
                    $count = \Modules\Page\app\Models\Page::count();
                    $this->moduleStats['page'] = [
                        'name' => __('admin.pages'),
                        'value' => $count,
                        'icon' => 'fas fa-file-alt',
                        'color' => 'danger',
                        'gradient' => 'linear-gradient(135deg, #dc2626 0%, #ef4444 100%)',
                        'route' => 'admin.page.index'
                    ];
                }
            } catch (\Exception $e) {}
        }

        // Portfolio
        if (in_array('portfolio', $this->activeModules)) {
            try {
                if (class_exists('\Modules\Portfolio\app\Models\Portfolio')) {
                    $count = \Modules\Portfolio\app\Models\Portfolio::count();
                    $this->moduleStats['portfolio'] = [
                        'name' => __('admin.portfolio'),
                        'value' => $count,
                        'icon' => 'fas fa-briefcase',
                        'color' => 'orange',
                        'gradient' => 'linear-gradient(135deg, #ea580c 0%, #f97316 100%)',
                        'route' => 'admin.portfolio.index'
                    ];
                }
            } catch (\Exception $e) {}
        }

        // Blog
        if (in_array('blog', $this->activeModules)) {
            try {
                if (class_exists('\Modules\Blog\app\Models\Blog')) {
                    $count = \Modules\Blog\app\Models\Blog::count();
                    $this->moduleStats['blog'] = [
                        'name' => 'Blog',
                        'value' => $count,
                        'icon' => 'fas fa-pen-fancy',
                        'color' => 'success',
                        'gradient' => 'linear-gradient(135deg, #15803d 0%, #16a34a 100%)',
                        'route' => 'admin.blog.index'
                    ];
                }
            } catch (\Exception $e) {}
        }

        // Announcement
        if (in_array('announcement', $this->activeModules)) {
            try {
                if (class_exists('\Modules\Announcement\app\Models\Announcement')) {
                    $count = \Modules\Announcement\app\Models\Announcement::count();
                    $this->moduleStats['announcement'] = [
                        'name' => __('admin.announcements'),
                        'value' => $count,
                        'icon' => 'fas fa-bullhorn',
                        'color' => 'cyan',
                        'gradient' => 'linear-gradient(135deg, #0891b2 0%, #06b6d4 100%)',
                        'route' => 'admin.announcement.index'
                    ];
                }
            } catch (\Exception $e) {}
        }

        // Muzibu modülleri
        if (in_array('muzibu', $this->activeModules)) {
            try {
                // Songs
                if (class_exists('\Modules\Muzibu\app\Models\Song')) {
                    $count = \Modules\Muzibu\app\Models\Song::count();
                    $this->moduleStats['songs'] = [
                        'name' => 'Şarkılar',
                        'value' => $count,
                        'icon' => 'fas fa-music',
                        'color' => 'purple',
                        'gradient' => 'linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%)',
                        'route' => 'admin.muzibu.song.index'
                    ];
                }
                // Albums
                if (class_exists('\Modules\Muzibu\app\Models\Album')) {
                    $count = \Modules\Muzibu\app\Models\Album::count();
                    $this->moduleStats['albums'] = [
                        'name' => 'Albümler',
                        'value' => $count,
                        'icon' => 'fas fa-compact-disc',
                        'color' => 'indigo',
                        'gradient' => 'linear-gradient(135deg, #4f46e5 0%, #6366f1 100%)',
                        'route' => 'admin.muzibu.album.index'
                    ];
                }
            } catch (\Exception $e) {}
        }

        // User stats - sadece admin
        if ($this->isAdminOrRoot && in_array('usermanagement', $this->activeModules)) {
            try {
                $count = \App\Models\User::count();
                $this->moduleStats['users'] = [
                    'name' => __('admin.users'),
                    'value' => $count,
                    'icon' => 'fas fa-users',
                    'color' => 'blue',
                    'gradient' => 'linear-gradient(135deg, #1e40af 0%, #3b82f6 100%)',
                    'route' => 'admin.usermanagement.index'
                ];
            } catch (\Exception $e) {}
        }
    }

    private function loadRecentItems()
    {
        $this->recentItems = [];

        // Son sayfalar
        if (in_array('page', $this->activeModules)) {
            try {
                if (class_exists('\Modules\Page\app\Models\Page')) {
                    $pages = \Modules\Page\app\Models\Page::orderBy('created_at', 'desc')->take(5)->get();
                    if ($pages->count() > 0) {
                        $this->recentItems['page'] = [
                            'title' => __('admin.pages'),
                            'icon' => 'fas fa-file-alt',
                            'color' => 'danger',
                            'route' => 'admin.page.index',
                            'manageRoute' => 'admin.page.manage',
                            'items' => $pages->map(function($item) {
                                return [
                                    'title' => is_array($item->title) ? ($item->title[app()->getLocale()] ?? array_values($item->title)[0] ?? '-') : $item->title,
                                    'date' => $item->created_at->diffForHumans(),
                                    'status' => $item->is_active,
                                    'id' => $item->id
                                ];
                            })->toArray()
                        ];
                    }
                }
            } catch (\Exception $e) {}
        }

        // Son portfolyolar
        if (in_array('portfolio', $this->activeModules)) {
            try {
                if (class_exists('\Modules\Portfolio\app\Models\Portfolio')) {
                    $items = \Modules\Portfolio\app\Models\Portfolio::orderBy('created_at', 'desc')->take(5)->get();
                    if ($items->count() > 0) {
                        $this->recentItems['portfolio'] = [
                            'title' => __('admin.portfolio'),
                            'icon' => 'fas fa-briefcase',
                            'color' => 'orange',
                            'route' => 'admin.portfolio.index',
                            'manageRoute' => 'admin.portfolio.manage',
                            'items' => $items->map(function($item) {
                                return [
                                    'title' => is_array($item->title) ? ($item->title[app()->getLocale()] ?? array_values($item->title)[0] ?? '-') : $item->title,
                                    'date' => $item->created_at->diffForHumans(),
                                    'status' => $item->is_active ?? true,
                                    'id' => $item->id
                                ];
                            })->toArray()
                        ];
                    }
                }
            } catch (\Exception $e) {}
        }

        // Son bloglar
        if (in_array('blog', $this->activeModules)) {
            try {
                if (class_exists('\Modules\Blog\app\Models\Blog')) {
                    $items = \Modules\Blog\app\Models\Blog::orderBy('created_at', 'desc')->take(5)->get();
                    if ($items->count() > 0) {
                        $this->recentItems['blog'] = [
                            'title' => 'Blog',
                            'icon' => 'fas fa-pen-fancy',
                            'color' => 'success',
                            'route' => 'admin.blog.index',
                            'manageRoute' => 'admin.blog.manage',
                            'items' => $items->map(function($item) {
                                return [
                                    'title' => is_array($item->title) ? ($item->title[app()->getLocale()] ?? array_values($item->title)[0] ?? '-') : $item->title,
                                    'date' => $item->created_at->diffForHumans(),
                                    'status' => $item->is_active ?? true,
                                    'id' => $item->id
                                ];
                            })->toArray()
                        ];
                    }
                }
            } catch (\Exception $e) {}
        }

        // Son şarkılar (Muzibu)
        if (in_array('muzibu', $this->activeModules)) {
            try {
                if (class_exists('\Modules\Muzibu\app\Models\Song')) {
                    $items = \Modules\Muzibu\app\Models\Song::orderBy('created_at', 'desc')->take(5)->get();
                    if ($items->count() > 0) {
                        $this->recentItems['songs'] = [
                            'title' => 'Şarkılar',
                            'icon' => 'fas fa-music',
                            'color' => 'purple',
                            'route' => 'admin.muzibu.song.index',
                            'manageRoute' => 'admin.muzibu.song.manage',
                            'items' => $items->map(function($item) {
                                $title = $item->title;
                                if (is_array($title)) {
                                    $title = $title[app()->getLocale()] ?? $title['tr'] ?? array_values($title)[0] ?? '-';
                                }
                                return [
                                    'title' => $title ?? '-',
                                    'date' => $item->created_at->diffForHumans(),
                                    'status' => $item->is_active ?? true,
                                    'id' => $item->id
                                ];
                            })->toArray()
                        ];
                    }
                }

                // Son albümler
                if (class_exists('\Modules\Muzibu\app\Models\Album')) {
                    $items = \Modules\Muzibu\app\Models\Album::orderBy('created_at', 'desc')->take(5)->get();
                    if ($items->count() > 0) {
                        $this->recentItems['albums'] = [
                            'title' => 'Albümler',
                            'icon' => 'fas fa-compact-disc',
                            'color' => 'indigo',
                            'route' => 'admin.muzibu.album.index',
                            'manageRoute' => 'admin.muzibu.album.manage',
                            'items' => $items->map(function($item) {
                                $title = $item->title;
                                if (is_array($title)) {
                                    $title = $title[app()->getLocale()] ?? $title['tr'] ?? array_values($title)[0] ?? '-';
                                }
                                return [
                                    'title' => $title ?? '-',
                                    'date' => $item->created_at->diffForHumans(),
                                    'status' => $item->is_active ?? true,
                                    'id' => $item->id
                                ];
                            })->toArray()
                        ];
                    }
                }

                // Son playlistler
                if (class_exists('\Modules\Muzibu\app\Models\Playlist')) {
                    $items = \Modules\Muzibu\app\Models\Playlist::orderBy('created_at', 'desc')->take(5)->get();
                    if ($items->count() > 0) {
                        $this->recentItems['playlists'] = [
                            'title' => 'Playlistler',
                            'icon' => 'fas fa-list-music',
                            'color' => 'cyan',
                            'route' => 'admin.muzibu.playlist.index',
                            'manageRoute' => 'admin.muzibu.playlist.manage',
                            'items' => $items->map(function($item) {
                                $title = $item->title;
                                if (is_array($title)) {
                                    $title = $title[app()->getLocale()] ?? $title['tr'] ?? array_values($title)[0] ?? '-';
                                }
                                return [
                                    'title' => $title ?? '-',
                                    'date' => $item->created_at->diffForHumans(),
                                    'status' => $item->is_active ?? true,
                                    'id' => $item->id
                                ];
                            })->toArray()
                        ];
                    }
                }

                // Son genreler
                if (class_exists('\Modules\Muzibu\app\Models\Genre')) {
                    $items = \Modules\Muzibu\app\Models\Genre::orderBy('created_at', 'desc')->take(5)->get();
                    if ($items->count() > 0) {
                        $this->recentItems['genres'] = [
                            'title' => 'Türler',
                            'icon' => 'fas fa-guitar',
                            'color' => 'warning',
                            'route' => 'admin.muzibu.genre.index',
                            'manageRoute' => 'admin.muzibu.genre.manage',
                            'items' => $items->map(function($item) {
                                // Genre'da title alanı kullanılıyor, name değil
                                $title = $item->title ?? $item->name;
                                if (is_array($title)) {
                                    $title = $title[app()->getLocale()] ?? $title['tr'] ?? array_values($title)[0] ?? '-';
                                }
                                return [
                                    'title' => $title ?? '-',
                                    'date' => $item->created_at->diffForHumans(),
                                    'status' => $item->is_active ?? true,
                                    'id' => $item->id
                                ];
                            })->toArray()
                        ];
                    }
                }
            } catch (\Exception $e) {}
        }

        // Son duyurular
        if (in_array('announcement', $this->activeModules)) {
            try {
                if (class_exists('\Modules\Announcement\app\Models\Announcement')) {
                    $items = \Modules\Announcement\app\Models\Announcement::orderBy('created_at', 'desc')->take(5)->get();
                    if ($items->count() > 0) {
                        $this->recentItems['announcement'] = [
                            'title' => 'Duyurular',
                            'icon' => 'fas fa-bullhorn',
                            'color' => 'cyan',
                            'route' => 'admin.announcement.index',
                            'manageRoute' => 'admin.announcement.manage',
                            'items' => $items->map(function($item) {
                                return [
                                    'title' => is_array($item->title) ? ($item->title[app()->getLocale()] ?? array_values($item->title)[0] ?? '-') : $item->title,
                                    'date' => $item->created_at->diffForHumans(),
                                    'status' => $item->is_active ?? true,
                                    'id' => $item->id
                                ];
                            })->toArray()
                        ];
                    }
                }
            } catch (\Exception $e) {}
        }
    }

    private function calculateLayout()
    {
        $statCount = count($this->moduleStats);

        // Grid layout belirleme
        if ($statCount >= 4) {
            $this->gridLayout = 'quarter'; // 4 kolon
        } elseif ($statCount >= 2) {
            $this->gridLayout = 'half'; // 2 kolon
        } else {
            $this->gridLayout = 'full'; // tek kolon, geniş
        }

        // Visible widgets
        $this->visibleWidgets = [
            'stats' => count($this->moduleStats) > 0,
            'quickActions' => $this->hasAnyCreatePermission(),
            'recentItems' => count($this->recentItems) > 0,
            'aiChat' => in_array('ai', $this->activeModules) && ($this->isAdminOrRoot || in_array('ai', $this->allowedModulesView)),
            'systemStatus' => $this->isAdminOrRoot,
            'profile' => true
        ];
    }

    private function hasAnyCreatePermission()
    {
        if ($this->isAdminOrRoot) {
            return count($this->activeModules) > 0;
        }
        return count($this->allowedModulesCreate) > 0;
    }

    public function getQuickActions()
    {
        $actions = [];

        $moduleActions = [
            'page' => ['name' => 'Yeni Sayfa', 'icon' => 'fas fa-file-alt', 'color' => 'danger', 'route' => 'admin.page.manage'],
            'portfolio' => ['name' => 'Yeni Portfolio', 'icon' => 'fas fa-briefcase', 'color' => 'orange', 'route' => 'admin.portfolio.manage'],
            'blog' => ['name' => 'Yeni Blog', 'icon' => 'fas fa-pen-fancy', 'color' => 'success', 'route' => 'admin.blog.manage'],
            'announcement' => ['name' => 'Yeni Duyuru', 'icon' => 'fas fa-bullhorn', 'color' => 'cyan', 'route' => 'admin.announcement.manage'],
        ];

        foreach ($moduleActions as $module => $action) {
            if (in_array($module, $this->activeModules) && $this->canCreateModule($module)) {
                $actions[$module] = $action;
            }
        }

        // Muzibu modülleri için quick actions
        if (in_array('muzibu', $this->activeModules)) {
            $actions['songs'] = ['name' => 'Yeni Şarkı', 'icon' => 'fas fa-music', 'color' => 'purple', 'route' => 'admin.muzibu.song.manage'];
            $actions['albums'] = ['name' => 'Yeni Albüm', 'icon' => 'fas fa-compact-disc', 'color' => 'indigo', 'route' => 'admin.muzibu.album.manage'];
        }

        // AI her zaman view ile erişilebilir
        if (in_array('ai', $this->activeModules)) {
            $actions['ai'] = ['name' => 'AI Asistan', 'icon' => 'fas fa-robot', 'color' => 'purple', 'route' => 'admin.ai.index'];
        }

        // Settings sadece admin
        if ($this->isAdminOrRoot && in_array('settingmanagement', $this->activeModules)) {
            $actions['settings'] = ['name' => 'Ayarlar', 'icon' => 'fas fa-cog', 'color' => 'secondary-custom', 'route' => 'admin.settingmanagement.index'];
        }

        return $actions;
    }

    public function getStatGridClass()
    {
        $count = count($this->moduleStats);

        if ($count >= 4) {
            return 'col-6 col-lg-3'; // 4 kolon
        } elseif ($count == 3) {
            return 'col-6 col-lg-4'; // 3 kolon
        } elseif ($count == 2) {
            return 'col-6'; // 2 kolon
        } else {
            return 'col-12'; // tek geniş
        }
    }

    public function getMainColumnClass()
    {
        // Ana içerik kolonu - sidebar varsa 8, yoksa 12
        $hasSidebar = $this->visibleWidgets['systemStatus'] || $this->visibleWidgets['profile'];
        return $hasSidebar ? 'col-lg-8' : 'col-12';
    }

    public function getSidebarColumnClass()
    {
        return 'col-lg-4';
    }

    public function sendAiMessage()
    {
        if (empty(trim($this->aiChatMessage))) {
            return;
        }

        $message = trim($this->aiChatMessage);
        $this->aiChatMessage = '';

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
            return "Dashboard'unuz aktif ve çalışır durumda. PHP " . PHP_VERSION . " ve Laravel " . app()->version() . " kullanılıyor. Veritabanı bağlantısı sağlıklı.";
        }

        if (str_contains($message, 'seo')) {
            return "SEO optimizasyonu için içeriklerinize meta title, description ve anahtar kelimeler eklemenizi öneririm.";
        }

        if (str_contains($message, 'performans') || str_contains($message, 'hız')) {
            return "Performans iyileştirme için cache sistemlerini aktif tutun, görselleri optimize edin.";
        }

        if (str_contains($message, 'yardım') || str_contains($message, 'help')) {
            return "Size şu konularda yardımcı olabilirim: sistem durumu, SEO analizi, performans optimizasyonu, içerik yönetimi.";
        }

        return "Bu konuda size yardımcı olmaya çalışıyorum. Daha spesifik bir soru sorabilirsiniz.";
    }

    public function getWelcomeMessage()
    {
        $user = Auth::user();
        $name = $user->name ?? 'Kullanıcı';

        if ($this->isAdminOrRoot) {
            $moduleCount = count($this->activeModules);
            return [
                'title' => "Hoş geldin, {$name}!",
                'subtitle' => "Sistemde {$moduleCount} aktif modül bulunuyor."
            ];
        }

        if ($this->isEditor) {
            $viewCount = count($this->allowedModulesView);
            $createCount = count($this->allowedModulesCreate);

            $subtitle = "{$viewCount} modüle erişim yetkin var.";
            if ($createCount > 0) {
                $subtitle .= " {$createCount} modülde içerik oluşturabilirsin.";
            }

            return [
                'title' => "Hoş geldin, {$name}!",
                'subtitle' => $subtitle
            ];
        }

        return [
            'title' => "Hoş geldin, {$name}!",
            'subtitle' => "Kontrol panelinize hoş geldiniz."
        ];
    }

    public function render()
    {
        return view('livewire.admin.dashboard-widget', [
            'quickActions' => $this->getQuickActions(),
            'statGridClass' => $this->getStatGridClass(),
            'mainColumnClass' => $this->getMainColumnClass(),
            'sidebarColumnClass' => $this->getSidebarColumnClass(),
            'welcomeMessage' => $this->getWelcomeMessage()
        ]);
    }
}
