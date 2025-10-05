<?php

namespace Modules\MenuManagement\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Prunable;
use App\Traits\HasTranslations;
use Modules\MenuManagement\App\Services\MenuUrlBuilderService;
use Illuminate\Support\Facades\Cache;

class MenuItem extends Model
{
    use HasTranslations, SoftDeletes, Prunable;

    protected $primaryKey = 'item_id';

    protected $fillable = [
        'menu_id',
        'parent_id',
        'title',
        'url_type',
        'url_data',
        'target',
        'is_active',
        'sort_order',
        'visibility',
        'icon',
    ];

    protected $casts = [
        'title' => 'array',
        'url_data' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Çevrilebilir alanlar
     */
    protected $translatable = ['title'];

    /**
     * Menu relationship
     */
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'menu_id');
    }

    /**
     * Parent menu item
     */
    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id', 'item_id');
    }

    /**
     * Child menu items
     */
    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id', 'item_id')
            ->orderBy('sort_order');
    }

    /**
     * Active children
     */
    public function activeChildren()
    {
        return $this->hasMany(MenuItem::class, 'parent_id', 'item_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    /**
     * All descendants (recursive)
     */
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    /**
     * Aktif menü öğelerini getir
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Root level items (no parent)
     */
    public function scopeRootLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Calculate depth level based on parent (recursive)
     * Portfolio pattern - accessor ile dinamik hesaplama
     * Circular reference korumalı
     */
    public function getDepthLevelAttribute(): int
    {
        return $this->calculateDepth();
    }

    private function calculateDepth(array $visited = []): int
    {
        // Circular reference kontrolü
        if (in_array($this->item_id, $visited)) {
            \Log::warning("Circular reference detected in menu item hierarchy", [
                'item_id' => $this->item_id,
                'visited' => $visited
            ]);
            return 0;
        }

        if (!$this->parent_id) {
            return 0;
        }

        $visited[] = $this->item_id;
        $parent = $this->parent()->first();

        if (!$parent) {
            return 0;
        }

        return $parent->calculateDepth($visited) + 1;
    }

    /**
     * Get indent pixels for display
     */
    public function getIndentPxAttribute(): int
    {
        return $this->depth_level * 30; // 30px per level
    }

    /**
     * Get resolved URL based on type and data
     * LOCALE AWARE - Her zaman doğru dil için URL üretir
     */
    public function getResolvedUrl(string $locale = null)
    {
        // Locale yoksa mevcut app locale'ini kullan
        $locale = $locale ?? app()->getLocale();
        
        // Cache key - locale ve menu item specific
        $cacheKey = "menu_item_url_{$this->item_id}_{$locale}";
        
        // Cache devre dışı - direkt URL oluştur
        // return Cache::remember($cacheKey, 300, function() use ($locale) {
        // URL data'yı direkt kullan, locale ekleme
        $urlData = $this->url_data ?? [];
        // $urlData['_locale'] = $locale; // REMOVED - locale is passed as parameter
        
        $urlBuilder = app(MenuUrlBuilderService::class);
        return $urlBuilder->buildUrl($this->url_type, $urlData, $locale);
        // });
    }

    /**
     * Check if current URL matches this menu item
     * OPTIMIZED VERSION
     */
    public function isActive(): bool
    {
        $currentPath = request()->path();
        $itemUrl = $this->getResolvedUrl();
        
        // Quick exact match
        if (request()->url() === $itemUrl) {
            return true;
        }
        
        // Normalize paths by removing locale prefix
        $itemPath = $this->normalizeLocalePath(parse_url($itemUrl, PHP_URL_PATH) ?? '');
        $currentPath = $this->normalizeLocalePath($currentPath);
        
        // Direct path match
        if ($itemPath === $currentPath) {
            return true;
        }
        
        // Check if current path starts with item path (for nested routes)
        if ($itemPath && str_starts_with($currentPath, rtrim($itemPath, '/') . '/')) {
            return true;
        }
        
        // Module-specific active state handling
        if ($this->url_type === 'module' && isset($this->url_data['module'])) {
            return $this->checkModuleActiveState($currentPath);
        }
        
        return false;
    }
    
    /**
     * Normalize path by removing locale prefix
     */
    private function normalizeLocalePath(string $path): string
    {
        $path = trim($path, '/');
        
        // Cache active locales for better performance
        static $locales = null;
        if ($locales === null) {
            $locales = \Cache::remember('active_tenant_locales', 3600, function() {
                return \Modules\LanguageManagement\App\Models\TenantLanguage::where('is_active', true)
                    ->pluck('code')
                    ->toArray();
            });
        }
        
        // Remove locale prefix if exists
        foreach ($locales as $locale) {
            if (str_starts_with($path, $locale . '/')) {
                return substr($path, strlen($locale) + 1);
            }
        }
        
        return $path;
    }
    
    /**
     * Check module-specific active states
     */
    private function checkModuleActiveState(string $currentPath): bool
    {
        $module = $this->url_data['module'] ?? '';
        $type = $this->url_data['type'] ?? '';
        
        // Module index pages
        if ($type === 'index' && str_starts_with($currentPath, $module)) {
            return true;
        }
        
        // Category pages
        if ($type === 'category' && isset($this->url_data['id'])) {
            $pattern = $module . '/[^/]+/' . $this->url_data['id'];
            return (bool) preg_match("~^{$pattern}~", $currentPath);
        }
        
        return false;
    }

    /**
     * Check if this menu item or any of its children is active
     */
    public function hasActiveChild()
    {
        if ($this->isActive()) {
            return true;
        }

        foreach ($this->activeChildren as $child) {
            if ($child->hasActiveChild()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get breadcrumb trail to this item
     */
    public function getBreadcrumb()
    {
        $breadcrumb = [];
        $current = $this;

        while ($current) {
            array_unshift($breadcrumb, $current);
            $current = $current->parent;
        }

        return $breadcrumb;
    }

    /**
     * Prunable: Otomatik soft delete temizleme
     * Global Standart: 30 gün (GDPR uyumlu + müşteri şikayetleri için yeterli)
     * SaaS Best Practice: Stripe, Google Workspace, Shopify standartları
     */
    public function prunable()
    {
        return static::where('deleted_at', '<=', now()->subDays(30));
    }

    /**
     * Pruning sırasında çalışacak cleanup hook
     */
    protected function pruning()
    {
        \Log::info('MenuItem pruned (kalıcı silindi)', [
            'item_id' => $this->item_id,
            'title' => $this->title,
            'deleted_at' => $this->deleted_at,
            'days_ago' => $this->deleted_at?->diffInDays(now())
        ]);
    }
}