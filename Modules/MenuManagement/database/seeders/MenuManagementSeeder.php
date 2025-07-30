<?php

declare(strict_types=1);

namespace Modules\MenuManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\MenuManagement\App\Models\Menu;
use Modules\MenuManagement\App\Models\MenuItem;
use Modules\LanguageManagement\App\Models\TenantLanguage;
use App\Services\ModuleService;

class MenuManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get active languages
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

        $this->createDefaultMenus($languages);
        $this->createModuleNavigationMenu($languages);
    }

    /**
     * Create default menus for the system
     */
    private function createDefaultMenus($languages): void
    {
        // 1. Ana Menü (Header)
        $headerMenu = $this->createMenu($languages, [
            'tr' => [
                'name' => 'Ana Menü',
                'slug' => 'ana-menu',
                'description' => 'Site ana menüsü'
            ],
            'en' => [
                'name' => 'Main Menu',
                'slug' => 'main-menu',
                'description' => 'Site main menu'
            ]
        ], 'header');

        // Ana menü öğeleri
        $this->createMenuItems($headerMenu, $languages, [
            [
                'tr' => ['title' => 'Ana Sayfa', 'url_value' => '/'],
                'en' => ['title' => 'Home', 'url_value' => '/'],
                'url_type' => 'custom',
                'sort_order' => 1,
            ],
            [
                'tr' => ['title' => 'Hakkımızda', 'url_value' => 'hakkimizda'],
                'en' => ['title' => 'About Us', 'url_value' => 'about-us'],
                'url_type' => 'page',
                'sort_order' => 2,
            ],
            [
                'tr' => ['title' => 'Hizmetler', 'url_value' => 'hizmetler'],
                'en' => ['title' => 'Services', 'url_value' => 'services'],
                'url_type' => 'page',
                'sort_order' => 3,
                'children' => [
                    [
                        'tr' => ['title' => 'Web Tasarım', 'url_value' => 'web-tasarim'],
                        'en' => ['title' => 'Web Design', 'url_value' => 'web-design'],
                        'url_type' => 'page',
                        'sort_order' => 1,
                    ],
                    [
                        'tr' => ['title' => 'SEO', 'url_value' => 'seo'],
                        'en' => ['title' => 'SEO', 'url_value' => 'seo'],
                        'url_type' => 'page',
                        'sort_order' => 2,
                    ]
                ]
            ],
            [
                'tr' => ['title' => 'Portfolyo', 'url_value' => 'portfolyo'],
                'en' => ['title' => 'Portfolio', 'url_value' => 'portfolio'],
                'url_type' => 'module',
                'sort_order' => 4,
            ],
            [
                'tr' => ['title' => 'İletişim', 'url_value' => 'iletisim'],
                'en' => ['title' => 'Contact', 'url_value' => 'contact'],
                'url_type' => 'page',
                'sort_order' => 5,
            ]
        ]);

        // 2. Alt Menü (Footer)
        $footerMenu = $this->createMenu($languages, [
            'tr' => [
                'name' => 'Alt Menü',
                'slug' => 'alt-menu',
                'description' => 'Site alt menüsü'
            ],
            'en' => [
                'name' => 'Footer Menu',
                'slug' => 'footer-menu',
                'description' => 'Site footer menu'
            ]
        ], 'footer');

        // Alt menü öğeleri
        $this->createMenuItems($footerMenu, $languages, [
            [
                'tr' => ['title' => 'Gizlilik Politikası', 'url_value' => 'gizlilik-politikasi'],
                'en' => ['title' => 'Privacy Policy', 'url_value' => 'privacy-policy'],
                'url_type' => 'page',
                'sort_order' => 1,
            ],
            [
                'tr' => ['title' => 'Kullanım Şartları', 'url_value' => 'kullanim-sartlari'],
                'en' => ['title' => 'Terms of Service', 'url_value' => 'terms-of-service'],
                'url_type' => 'page',
                'sort_order' => 2,
            ],
            [
                'tr' => ['title' => 'Çerez Politikası', 'url_value' => 'cerez-politikasi'],
                'en' => ['title' => 'Cookie Policy', 'url_value' => 'cookie-policy'],
                'url_type' => 'page',
                'sort_order' => 3,
            ]
        ]);

        // 3. Mobil Menü
        $mobileMenu = $this->createMenu($languages, [
            'tr' => [
                'name' => 'Mobil Menü',
                'slug' => 'mobil-menu',
                'description' => 'Mobil cihazlar için menü'
            ],
            'en' => [
                'name' => 'Mobile Menu',
                'slug' => 'mobile-menu',
                'description' => 'Menu for mobile devices'
            ]
        ], 'mobile');

        // Mobil menü öğeleri (ana menü ile aynı)
        $this->createMenuItems($mobileMenu, $languages, [
            [
                'tr' => ['title' => 'Ana Sayfa', 'url_value' => '/'],
                'en' => ['title' => 'Home', 'url_value' => '/'],
                'url_type' => 'custom',
                'sort_order' => 1,
            ],
            [
                'tr' => ['title' => 'Hakkımızda', 'url_value' => 'hakkimizda'],
                'en' => ['title' => 'About Us', 'url_value' => 'about-us'],
                'url_type' => 'page',
                'sort_order' => 2,
            ],
            [
                'tr' => ['title' => 'İletişim', 'url_value' => 'iletisim'],
                'en' => ['title' => 'Contact', 'url_value' => 'contact'],
                'url_type' => 'page',
                'sort_order' => 3,
            ]
        ]);

        // Info message for seeder completion
        if ($this->command) {
            $this->command->info('✅ Default menus created successfully!');
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
        
        // Slug is string, not multi-language
        $menuData['slug'] = $data['tr']['slug'] ?? $data['en']['slug'] ?? 'menu-' . time();

        return Menu::create($menuData);
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
                'css_class' => $itemData['css_class'] ?? null,
                'icon' => $itemData['icon'] ?? null,
            ];

            // Build multi-language title and url_value
            foreach (['title', 'url_value'] as $field) {
                $menuItemData[$field] = [];
                foreach ($languages as $language) {
                    $menuItemData[$field][$language->code] = $itemData[$language->code][$field] ?? '';
                }
            }

            $menuItem = MenuItem::create($menuItemData);

            // Create children if they exist
            if (isset($itemData['children']) && is_array($itemData['children'])) {
                $this->createMenuItems($menu, $languages, $itemData['children'], $menuItem);
            }
        }
    }

    /**
     * Create module navigation menu (main menu for admin navigation)
     */
    private function createModuleNavigationMenu($languages): void
    {
        // Get active modules for current tenant
        $moduleService = app(ModuleService::class);
        $activeModules = $moduleService->getActiveModules();

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

        foreach ($activeModules as $module) {
            $moduleName = strtolower($module->name);
            
            // Skip MenuManagement itself to avoid infinite loop
            if ($moduleName === 'menumanagement') {
                continue;
            }

            // Get translations for this module
            $translations = $moduleTranslations[$moduleName] ?? [
                'tr' => ucfirst($module->name),
                'en' => ucfirst($module->name)
            ];

            // Create admin route URL
            $adminRoute = "admin/{$moduleName}";
            
            $menuItems[] = [
                'tr' => [
                    'title' => $translations['tr'],
                    'url_value' => $adminRoute
                ],
                'en' => [
                    'title' => $translations['en'],
                    'url_value' => $adminRoute
                ],
                'url_type' => 'custom',
                'sort_order' => $sortOrder,
                'target' => '_self',
                'icon' => $this->getModuleIcon($moduleName)
            ];

            $sortOrder++;
        }

        // Add MenuManagement itself at the end
        $menuItems[] = [
            'tr' => [
                'title' => 'Menü Yönetimi',
                'url_value' => 'admin/menumanagement'
            ],
            'en' => [
                'title' => 'Menu Management',
                'url_value' => 'admin/menumanagement'
            ],
            'url_type' => 'custom',
            'sort_order' => $sortOrder,
            'target' => '_self',
            'icon' => 'fas fa-bars'
        ];

        // Create menu items
        $this->createMenuItems($navigationMenu, $languages, $menuItems);

        if ($this->command) {
            $this->command->info('✅ Module navigation menu created successfully!');
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
}