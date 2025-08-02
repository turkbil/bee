<?php

declare(strict_types=1);

namespace Modules\MenuManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\MenuManagement\App\Models\Menu;
use Modules\MenuManagement\App\Models\MenuItem;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use Modules\Portfolio\App\Models\PortfolioCategory;
use App\Services\ModuleService;

class MenuManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get active languages from tenant
        $languages = TenantLanguage::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        if ($languages->isEmpty()) {
            // Fallback to default languages if no tenant languages exist
            $languages = collect([
                (object)[ 'code' => 'tr', 'name' => 'Türkçe' ],
                (object)[ 'code' => 'en', 'name' => 'English' ]
            ]);
        }

        // Her dil için çeviri tablosu
        $this->languageMap = [];
        foreach ($languages as $lang) {
            $this->languageMap[$lang->code] = $lang->code;
        }

        $this->createDefaultMenus($languages);
    }

    /**
     * Create default menus for the system
     * Tenant-specific menu creation based on available content modules
     */
    private function createDefaultMenus($languages): void
    {
        // Get current tenant ID and active content modules
        $currentTenantId = $this->getCurrentTenantId();
        $activeContentModules = $this->getActiveContentModulesForTenant($currentTenantId);
        
        // Ana Menü çevirileri - tüm diller için
        $menuTranslations = $this->getMenuTranslations($languages);
        
        // 1. Ana Menü (Header)
        $headerMenu = $this->createMenu($languages, $menuTranslations['header'], 'header', true);

        // Build menu items based on available content modules
        $menuItems = [];
        $sortOrder = 1;

        // Add pages if page module is available
        if (in_array('page', $activeContentModules)) {
            $pageTranslations = [];
            foreach ($languages as $lang) {
                $pageTranslations[$lang->code] = ['title' => $this->getModuleTitle('page', $lang->code)];
            }
            $menuItems[] = array_merge($pageTranslations, [
                'url_type' => 'module',
                'url_data' => ['module' => 'Page', 'type' => 'list', '_locale' => 'tr'],
                'sort_order' => $sortOrder++,
            ]);
        }

        // Add portfolio if portfolio module is available  
        if (in_array('portfolio', $activeContentModules)) {
            $portfolioTranslations = [];
            foreach ($languages as $lang) {
                $portfolioTranslations[$lang->code] = ['title' => $this->getModuleTitle('portfolio', $lang->code)];
            }
            
            // Portfolio kategorilerini dinamik olarak al
            $portfolioChildren = [];
            try {
                $categories = PortfolioCategory::where('is_active', true)
                    ->orderBy('order')
                    ->get();
                
                if ($this->command) {
                    $this->command->info("🎯 Portfolio kategorileri bulundu: " . $categories->count());
                }
                
                $childOrder = 1;
                foreach ($categories as $category) {
                    $categoryItem = [];
                    foreach ($languages as $lang) {
                        $categoryTitle = $category->getTranslated('title', $lang->code);
                        $categoryItem[$lang->code] = [
                            'title' => $categoryTitle
                        ];
                    }
                    $categoryItem['url_type'] = 'module';
                    $categoryItem['url_data'] = [
                        'module' => 'Portfolio', 
                        'type' => 'category', 
                        'id' => $category->portfolio_category_id,
                        'slug' => $category->getTranslated('slug', 'tr'),
                        '_locale' => 'tr'
                    ];
                    $categoryItem['sort_order'] = $childOrder++;
                    
                    // Tenant'a göre portfolio örnekleri ekle
                    $tenantId = $this->getCurrentTenantId();
                    if ($tenantId % 2 == 0) { // Çift ID'li tenant'lar için
                        $categoryItem['children'] = [];
                        
                        $portfolios = \Modules\Portfolio\App\Models\Portfolio::where('portfolio_category_id', $category->portfolio_category_id)
                            ->where('is_active', true)
                            ->limit(2) // Her kategoriden 2 örnek
                            ->get();
                            
                        $portfolioOrder = 1;
                        foreach ($portfolios as $portfolio) {
                            $portfolioItem = [];
                            foreach ($languages as $lang) {
                                $portfolioItem[$lang->code] = [
                                    'title' => $portfolio->getTranslated('title', $lang->code)
                                ];
                            }
                            $portfolioItem['url_type'] = 'module';
                            $portfolioItem['url_data'] = [
                                'module' => 'Portfolio',
                                'type' => 'detail',
                                'id' => $portfolio->portfolio_id,
                                'slug' => $portfolio->getTranslated('slug', 'tr'),
                                '_locale' => 'tr'
                            ];
                            $portfolioItem['sort_order'] = $portfolioOrder++;
                            
                            $categoryItem['children'][] = $portfolioItem;
                        }
                    }
                    
                    $portfolioChildren[] = $categoryItem;
                }
                
                if ($this->command) {
                    $this->command->info("📋 Toplam portfolio alt kategorisi: " . count($portfolioChildren));
                }
            } catch (\Exception $e) {
                // Eğer kategori bulunamazsa boş bırak
                if ($this->command) {
                    $this->command->error("❌ Portfolio kategorileri alınamadı: " . $e->getMessage());
                }
            }
            
            $menuItems[] = array_merge($portfolioTranslations, [
                'url_type' => 'module',
                'url_data' => ['module' => 'Portfolio', 'type' => 'list', '_locale' => 'tr'],
                'sort_order' => $sortOrder++,
                'children' => $portfolioChildren
            ]);
        }

        // Add announcements if announcement module is available
        if (in_array('announcement', $activeContentModules)) {
            $announcementTranslations = [];
            foreach ($languages as $lang) {
                $announcementTranslations[$lang->code] = ['title' => $this->getModuleTitle('announcement', $lang->code)];
            }
            $menuItems[] = array_merge($announcementTranslations, [
                'url_type' => 'module',
                'url_data' => ['module' => 'Announcement', 'type' => 'list', '_locale' => 'tr'],
                'sort_order' => $sortOrder++,
            ]);
        }

        // Create menu items
        $this->createMenuItems($headerMenu, $languages, $menuItems);

        // 2. Alt Menü (Footer)
        $footerMenu = $this->createMenu($languages, $menuTranslations['footer'], 'footer');

        // Alt menü öğeleri - dinamik dil desteği
        $footerItems = [];
        
        // Gizlilik Politikası
        $privacyItem = [];
        foreach ($languages as $lang) {
            $privacyItem[$lang->code] = ['title' => $this->getPageTitle('privacy-policy', $lang->code)];
        }
        $privacyItem['url_type'] = 'internal';
        $privacyItem['url_data'] = ['url' => '/gizlilik-politikasi'];
        $privacyItem['sort_order'] = 1;
        $footerItems[] = $privacyItem;
        
        // Kullanım Şartları
        $termsItem = [];
        foreach ($languages as $lang) {
            $termsItem[$lang->code] = ['title' => $this->getPageTitle('terms-of-service', $lang->code)];
        }
        $termsItem['url_type'] = 'internal';
        $termsItem['url_data'] = ['url' => '/kullanim-sartlari'];
        $termsItem['sort_order'] = 2;
        $footerItems[] = $termsItem;
        
        // Çerez Politikası
        $cookieItem = [];
        foreach ($languages as $lang) {
            $cookieItem[$lang->code] = ['title' => $this->getPageTitle('cookie-policy', $lang->code)];
        }
        $cookieItem['url_type'] = 'internal';
        $cookieItem['url_data'] = ['url' => '/cerez-politikasi'];
        $cookieItem['sort_order'] = 3;
        $footerItems[] = $cookieItem;
        
        $this->createMenuItems($footerMenu, $languages, $footerItems);

        // 3. Mobil Menü
        $mobileMenu = $this->createMenu($languages, $menuTranslations['mobile'], 'mobile');

        // Mobil menü öğeleri - header menüden farklı, basit yapı
        $mobileItems = [];
        $mobileSortOrder = 1;
        
        // Ana Sayfa
        $homeItem = [];
        foreach ($languages as $lang) {
            $homeItem[$lang->code] = ['title' => ($lang->code === 'tr' ? 'Ana Sayfa' : 'Home')];
        }
        $homeItem['url_type'] = 'internal';
        $homeItem['url_data'] = ['url' => '/'];
        $homeItem['sort_order'] = $mobileSortOrder++;
        $mobileItems[] = $homeItem;
        
        // İletişim
        $contactItem = [];
        foreach ($languages as $lang) {
            $contactItem[$lang->code] = ['title' => ($lang->code === 'tr' ? 'İletişim' : 'Contact')];
        }
        $contactItem['url_type'] = 'internal';
        $contactItem['url_data'] = ['url' => '/iletisim'];
        $contactItem['sort_order'] = $mobileSortOrder++;
        $mobileItems[] = $contactItem;

        $this->createMenuItems($mobileMenu, $languages, $mobileItems);

        // Info message for seeder completion
        if ($this->command) {
            // $this->command->info("Created successfully") for tenant {$currentTenantId}!");
            $this->command->info("📋 Active content modules: " . implode(', ', $activeContentModules));
        }
    }

    /**
     * Create a menu with multi-language support
     */
    private function createMenu($languages, array $data, string $location, bool $isDefault = false): Menu
    {
        $menuData = [
            'location' => $location,
            'is_active' => true,
            'is_default' => $isDefault,
        ];

        // Build multi-language name
        $menuData['name'] = [];
        foreach ($languages as $language) {
            $menuData['name'][$language->code] = $data[$language->code]['name'] ?? '';
        }
        
        // Slug is string, not multi-language - make it tenant-specific to avoid conflicts
        $tenantId = $this->getCurrentTenantId();
        $baseSlug = $data['tr']['slug'] ?? $data['en']['slug'] ?? 'menu-' . time();
        $menuData['slug'] = $baseSlug . '-t' . $tenantId;

        return Menu::updateOrCreate(
            [
                'slug' => $menuData['slug'],
                'location' => $menuData['location']
            ], // Find by slug AND location
            $menuData // Update/create with this data
        );
    }

    /**
     * Create menu items recursively
     */
    private function createMenuItems(Menu $menu, $languages, array $items, ?MenuItem $parent = null): void
    {
        
        foreach ($items as $itemData) {
            $menuItemData = [
                'menu_id' => $menu->menu_id,
                'parent_id' => $parent?->item_id,
                'url_type' => $itemData['url_type'],
                'target' => $itemData['target'] ?? '_self',
                'sort_order' => $itemData['sort_order'],
                'depth_level' => $parent ? ($parent->depth_level + 1) : 0,
                'is_active' => $itemData['is_active'] ?? true,
                'visibility' => $itemData['visibility'] ?? 'public',
                'icon' => $itemData['icon'] ?? null,
            ];

            // Build multi-language title
            $menuItemData['title'] = [];
            foreach ($languages as $language) {
                $menuItemData['title'][$language->code] = $itemData[$language->code]['title'] ?? '';
            }
            
            // Handle url_data based on url_type
            $menuItemData['url_data'] = $itemData['url_data'] ?? [];

            // Unique identifier için menu_id, parent_id, sort_order kombinasyonu kullan
            $uniqueFields = [
                'menu_id' => $menu->menu_id,
                'parent_id' => $parent?->item_id,
                'sort_order' => $itemData['sort_order']
            ];

            $menuItem = MenuItem::updateOrCreate($uniqueFields, $menuItemData);

            // Create children if they exist
            if (isset($itemData['children']) && is_array($itemData['children'])) {
                if ($this->command) {
                    $parentTitle = is_array($menuItem->title) ? ($menuItem->title['tr'] ?? $menuItem->title['en'] ?? 'Unknown') : $menuItem->title;
                    $this->command->info("🔧 Alt öğeler oluşturuluyor için: {$parentTitle} (id: {$menuItem->item_id})");
                    $this->command->info("   Alt öğe sayısı: " . count($itemData['children']));
                }
                $this->createMenuItems($menu, $languages, $itemData['children'], $menuItem);
            }
        }
    }

    /**
     * Create module navigation menu (main menu for admin navigation)
     * Tenant-specific module menu creation based on available modules
     */
    private function createModuleNavigationMenu($languages): void
    {
        // Get current tenant ID
        $currentTenantId = $this->getCurrentTenantId();
        
        // Get active modules for current tenant from module_tenants table
        $activeModules = $this->getActiveModulesForTenant($currentTenantId);

        // Create Module Navigation Menu (default menu that appears on header)
        $navigationMenu = $this->createMenu($languages, [
            'tr' => [
                'name' => 'Modül Navigasyonu',
                'slug' => 'modul-navigasyonu',
                'description' => 'Ana navigasyon menüsü - modül linkleri'
            ],
            'en' => [
                'name' => 'Module Navigation',
                'slug' => 'module-navigation',
                'description' => 'Main navigation menu - module links'
            ]
        ], 'header', true); // This is the default menu that will show in header

        // Build menu items from active modules
        $menuItems = [];
        $sortOrder = 1;

        // Module name translations
        $moduleTranslations = [
            'page' => [
                'tr' => 'Sayfalar',
                'en' => 'Pages'
            ],
            'portfolio' => [
                'tr' => 'Portfolyo',
                'en' => 'Portfolio'
            ],
            'announcement' => [
                'tr' => 'Duyurular',
                'en' => 'Announcements'
            ],
            'usermanagement' => [
                'tr' => 'Kullanıcılar',
                'en' => 'Users'
            ],
            'modulemanagement' => [
                'tr' => 'Modüller',
                'en' => 'Modules'
            ],
            'settingmanagement' => [
                'tr' => 'Ayarlar',
                'en' => 'Settings'
            ],
            'tenantmanagement' => [
                'tr' => 'Tenant Yönetimi',
                'en' => 'Tenant Management'
            ],
            'widgetmanagement' => [
                'tr' => 'Widget Yönetimi',
                'en' => 'Widget Management'
            ],
            'thememanagement' => [
                'tr' => 'Tema Yönetimi',
                'en' => 'Theme Management'
            ],
            'studio' => [
                'tr' => 'Studio',
                'en' => 'Studio'
            ],
            'ai' => [
                'tr' => 'AI Asistan',
                'en' => 'AI Assistant'
            ],
            'languagemanagement' => [
                'tr' => 'Dil Yönetimi',
                'en' => 'Language Management'
            ],
            'seomanagement' => [
                'tr' => 'SEO Yönetimi',
                'en' => 'SEO Management'
            ]
        ];

        foreach ($activeModules as $moduleName) {
            $moduleNameLower = strtolower($moduleName);
            
            // Skip MenuManagement itself to avoid infinite loop
            if ($moduleNameLower === 'menumanagement') {
                continue;
            }

            // Get translations for this module
            $translations = $moduleTranslations[$moduleNameLower] ?? [
                'tr' => ucfirst($moduleName),
                'en' => ucfirst($moduleName)
            ];

            // Create admin route URL
            $adminRoute = "admin/{$moduleNameLower}";
            
            $menuItems[] = [
                'tr' => ['title' => $translations['tr']],
                'en' => ['title' => $translations['en']],
                'url_type' => 'internal',
                'url_data' => ['url' => '/' . $adminRoute],
                'sort_order' => $sortOrder,
                'target' => '_self',
                'icon' => $this->getModuleIcon($moduleNameLower)
            ];

            $sortOrder++;
        }

        // Add MenuManagement itself at the end
        $menuItems[] = [
            'tr' => ['title' => 'Menü Yönetimi'],
            'en' => ['title' => 'Menu Management'],
            'url_type' => 'internal',
            'url_data' => ['url' => '/admin/menumanagement'],
            'sort_order' => $sortOrder,
            'target' => '_self',
            'icon' => 'fas fa-bars'
        ];

        // Create menu items
        $this->createMenuItems($navigationMenu, $languages, $menuItems);

        if ($this->command) {
            // $this->command->info("Created successfully") for tenant {$currentTenantId}!");
            $this->command->info("📋 Active modules: " . implode(', ', $activeModules));
        }
    }

    /**
     * Get current tenant ID from central or tenant context
     */
    private function getCurrentTenantId(): int
    {
        try {
            // Method 1: Check tenant() function directly
            if (function_exists('tenant') && tenant()) {
                $tenantModel = tenant();
                if ($tenantModel && isset($tenantModel->id)) {
                    if ($this->command) {
                        $this->command->info("🏢 Found tenant ID via tenant(): {$tenantModel->id}");
                    }
                    return (int) $tenantModel->id;
                }
            }
            
            // Method 2: Check if we're running from tenant seeder command
            if (app()->runningInConsole()) {
                // Get current database name
                $dbName = \DB::getDatabaseName();
                if ($this->command) {
                    $this->command->info("📋 Current database: {$dbName}");
                }
                
                // Extract tenant ID from database name (e.g., tenant1 -> 1)
                if (preg_match('/tenant(\d+)/', $dbName, $matches)) {
                    $tenantId = (int) $matches[1];
                    if ($this->command) {
                        $this->command->info("🔍 Extracted tenant ID from DB name: {$tenantId}");
                    }
                    return $tenantId;
                }
            }
            
            // Method 3: Check app('tenant') binding
            if (app()->bound('tenant')) {
                $tenant = app('tenant');
                if ($tenant && isset($tenant->id)) {
                    if ($this->command) {
                        $this->command->info("🏢 Found tenant ID via app binding: {$tenant->id}");
                    }
                    return (int) $tenant->id;
                }
            }
            
            // Fallback: If we're in central context (laravel database), return 1 (Laravel tenant)
            if ($this->command) {
                $this->command->warn("⚠️ No tenant context found, using default tenant ID: 1");
            }
            return 1;
        } catch (\Exception $e) {
            if ($this->command) {
                $this->command->error("❌ Error getting tenant ID: " . $e->getMessage());
            }
            // Fallback to tenant 1
            return 1;
        }
    }

    /**
     * Get active modules for specific tenant from module_tenants table
     */
    private function getActiveModulesForTenant(int $tenantId): array
    {
        try {
            // Central veritabanına connection kullanarak module_tenants tablosuna eriş
            $activeModules = \Illuminate\Support\Facades\DB::connection('mysql')
                ->table('module_tenants')
                ->join('modules', 'module_tenants.module_id', '=', 'modules.module_id')
                ->where('module_tenants.tenant_id', $tenantId)
                ->where('module_tenants.is_active', true)
                ->where('modules.is_active', true)
                ->pluck('modules.name')
                ->toArray();

            if ($this->command) {
                $this->command->info("🔍 Found " . count($activeModules) . " active modules for tenant {$tenantId}");
            }

            return $activeModules;
        } catch (\Exception $e) {
            if ($this->command) {
                $this->command->error("❌ Error getting active modules for tenant {$tenantId}: " . $e->getMessage());
            }
            
            // Fallback to default modules
            return ['modulemanagement', 'usermanagement', 'settingmanagement', 'page', 'announcement', 'portfolio'];
        }
    }

    /**
     * Get active content modules for specific tenant from module_tenants table
     */
    private function getActiveContentModulesForTenant(int $tenantId): array
    {
        try {
            // Central veritabanına connection kullanarak module_tenants tablosuna eriş
            $activeContentModules = \Illuminate\Support\Facades\DB::connection('mysql')
                ->table('module_tenants')
                ->join('modules', 'module_tenants.module_id', '=', 'modules.module_id')
                ->where('module_tenants.tenant_id', $tenantId)
                ->where('module_tenants.is_active', true)
                ->where('modules.is_active', true)
                ->where('modules.type', 'content')
                ->pluck('modules.name')
                ->toArray();

            if ($this->command) {
                $this->command->info("🔍 Found " . count($activeContentModules) . " active content modules for tenant {$tenantId}");
            }

            return $activeContentModules;
        } catch (\Exception $e) {
            if ($this->command) {
                $this->command->error("❌ Error getting active content modules for tenant {$tenantId}: " . $e->getMessage());
            }
            
            // Fallback to default content modules
            return ['page', 'announcement', 'portfolio'];
        }
    }

    /**
     * Get icon for module
     */
    private function getModuleIcon(string $moduleName): ?string
    {
        $icons = [
            'page' => 'fas fa-file-alt',
            'portfolio' => 'fas fa-briefcase',
            'announcement' => 'fas fa-bullhorn',
            'usermanagement' => 'fas fa-users',
            'modulemanagement' => 'fas fa-puzzle-piece',
            'settingmanagement' => 'fas fa-cogs',
            'tenantmanagement' => 'fas fa-building',
            'widgetmanagement' => 'fas fa-th-large',
            'thememanagement' => 'fas fa-paint-brush',
            'studio' => 'fas fa-magic',
            'ai' => 'fas fa-robot',
            'languagemanagement' => 'fas fa-language',
            'seomanagement' => 'fas fa-search'
        ];

        return $icons[$moduleName] ?? 'fas fa-circle';
    }

    /**
     * Get menu translations for all languages
     */
    private function getMenuTranslations($languages): array
    {
        $translations = [
            'header' => [],
            'footer' => [],
            'mobile' => []
        ];

        // Çeviri tablosu
        $menuNames = [
            'header' => [
                'tr' => ['name' => 'Ana Menü', 'slug' => 'ana-menu', 'description' => 'Site ana menüsü'],
                'en' => ['name' => 'Main Menu', 'slug' => 'main-menu', 'description' => 'Site main menu'],
                'ar' => ['name' => 'القائمة الرئيسية', 'slug' => 'main-menu-ar', 'description' => 'قائمة الموقع الرئيسية'],
                'de' => ['name' => 'Hauptmenü', 'slug' => 'hauptmenu', 'description' => 'Website-Hauptmenü'],
                'fr' => ['name' => 'Menu principal', 'slug' => 'menu-principal', 'description' => 'Menu principal du site'],
                'es' => ['name' => 'Menú principal', 'slug' => 'menu-principal', 'description' => 'Menú principal del sitio'],
                'ru' => ['name' => 'Главное меню', 'slug' => 'glavnoe-menu', 'description' => 'Главное меню сайта'],
            ],
            'footer' => [
                'tr' => ['name' => 'Alt Menü', 'slug' => 'alt-menu', 'description' => 'Site alt menüsü'],
                'en' => ['name' => 'Footer Menu', 'slug' => 'footer-menu', 'description' => 'Site footer menu'],
                'ar' => ['name' => 'قائمة التذييل', 'slug' => 'footer-menu-ar', 'description' => 'قائمة تذييل الموقع'],
                'de' => ['name' => 'Fußmenü', 'slug' => 'fussmenu', 'description' => 'Website-Fußmenü'],
                'fr' => ['name' => 'Menu de pied de page', 'slug' => 'menu-pied-page', 'description' => 'Menu de pied de page du site'],
                'es' => ['name' => 'Menú de pie de página', 'slug' => 'menu-pie-pagina', 'description' => 'Menú de pie de página del sitio'],
                'ru' => ['name' => 'Нижнее меню', 'slug' => 'nizhnee-menu', 'description' => 'Нижнее меню сайта'],
            ],
            'mobile' => [
                'tr' => ['name' => 'Mobil Menü', 'slug' => 'mobil-menu', 'description' => 'Mobil cihazlar için menü'],
                'en' => ['name' => 'Mobile Menu', 'slug' => 'mobile-menu', 'description' => 'Menu for mobile devices'],
                'ar' => ['name' => 'قائمة الجوال', 'slug' => 'mobile-menu-ar', 'description' => 'قائمة للأجهزة المحمولة'],
                'de' => ['name' => 'Mobiles Menü', 'slug' => 'mobiles-menu', 'description' => 'Menü für mobile Geräte'],
                'fr' => ['name' => 'Menu mobile', 'slug' => 'menu-mobile', 'description' => 'Menu pour appareils mobiles'],
                'es' => ['name' => 'Menú móvil', 'slug' => 'menu-movil', 'description' => 'Menú para dispositivos móviles'],
                'ru' => ['name' => 'Мобильное меню', 'slug' => 'mobilnoe-menu', 'description' => 'Меню для мобильных устройств'],
            ]
        ];

        // Her menü tipi için dil çevirilerini hazırla
        foreach (['header', 'footer', 'mobile'] as $menuType) {
            foreach ($languages as $lang) {
                $translations[$menuType][$lang->code] = $menuNames[$menuType][$lang->code] ?? $menuNames[$menuType]['en'];
            }
        }

        return $translations;
    }

    /**
     * Get module title for specific language
     */
    private function getModuleTitle(string $module, string $langCode): string
    {
        $titles = [
            'page' => [
                'tr' => 'Sayfalar',
                'en' => 'Pages',
                'ar' => 'الصفحات',
                'de' => 'Seiten',
                'fr' => 'Pages',
                'es' => 'Páginas',
                'ru' => 'Страницы',
            ],
            'portfolio' => [
                'tr' => 'Portfolyo',
                'en' => 'Portfolio',
                'ar' => 'المحفظة',
                'de' => 'Portfolio',
                'fr' => 'Portfolio',
                'es' => 'Portafolio',
                'ru' => 'Портфолио',
            ],
            'announcement' => [
                'tr' => 'Duyurular',
                'en' => 'Announcements',
                'ar' => 'الإعلانات',
                'de' => 'Ankündigungen',
                'fr' => 'Annonces',
                'es' => 'Anuncios',
                'ru' => 'Объявления',
            ]
        ];

        return $titles[$module][$langCode] ?? $titles[$module]['en'] ?? ucfirst($module);
    }

    /**
     * Get page title for specific language (footer pages)
     */
    private function getPageTitle(string $page, string $langCode): string
    {
        $titles = [
            'privacy-policy' => [
                'tr' => 'Gizlilik Politikası',
                'en' => 'Privacy Policy',
                'ar' => 'سياسة الخصوصية',
                'de' => 'Datenschutzrichtlinie',
                'fr' => 'Politique de confidentialité',
                'es' => 'Política de privacidad',
                'ru' => 'Политика конфиденциальности',
            ],
            'terms-of-service' => [
                'tr' => 'Kullanım Şartları',
                'en' => 'Terms of Service',
                'ar' => 'شروط الخدمة',
                'de' => 'Nutzungsbedingungen',
                'fr' => 'Conditions d\'utilisation',
                'es' => 'Términos de servicio',
                'ru' => 'Условия использования',
            ],
            'cookie-policy' => [
                'tr' => 'Çerez Politikası',
                'en' => 'Cookie Policy',
                'ar' => 'سياسة ملفات تعريف الارتباط',
                'de' => 'Cookie-Richtlinie',
                'fr' => 'Politique de cookies',
                'es' => 'Política de cookies',
                'ru' => 'Политика использования файлов cookie',
            ]
        ];

        return $titles[$page][$langCode] ?? $titles[$page]['en'] ?? ucfirst(str_replace('-', ' ', $page));
    }
}