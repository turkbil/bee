<?php

namespace Modules\MenuManagement\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasTranslations;

class MenuItem extends Model
{
    use HasTranslations, SoftDeletes;

    protected $primaryKey = 'item_id';

    protected $fillable = [
        'menu_id',
        'parent_id',
        'title',
        'url_type',
        'url_value',
        'target',
        'css_class',
        'is_active',
        'sort_order',
        'depth_level',
        'visibility',
        'icon',
    ];

    protected $casts = [
        'title' => 'array',
        'url_value' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'depth_level' => 'integer',
    ];

    /**
     * Çevrilebilir alanlar
     */
    protected $translatable = ['title', 'url_value'];

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
     * By depth level
     */
    public function scopeByDepth($query, $depth)
    {
        return $query->where('depth_level', $depth);
    }

    /**
     * Get resolved URL based on type and data
     */
    public function getResolvedUrl()
    {
        $locale = app()->getLocale();
        
        switch ($this->url_type) {
            case 'page':
                if (isset($this->url_data['page_id'])) {
                    $page = \Modules\Page\App\Models\Page::find($this->url_data['page_id']);
                    if ($page) {
                        $slug = $page->getTranslated('slug', $locale);
                        return url('/' . ltrim($slug, '/'));
                    }
                }
                break;
                
            case 'module':
                if (isset($this->url_data['module'])) {
                    $module = $this->url_data['module'];
                    $action = $this->url_data['action'] ?? 'index';
                    return url("/{$module}/{$action}");
                }
                break;
                
            case 'external':
                return $this->url_data['url'] ?? '#';
                
            case 'custom':
                return url('/' . ltrim($this->url_data['url'] ?? '', '/'));
        }

        return '#';
    }

    /**
     * Check if current URL matches this menu item
     */
    public function isActive()
    {
        $currentUrl = request()->url();
        $itemUrl = $this->getResolvedUrl();
        
        return $currentUrl === $itemUrl;
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
     * Calculate and update depth level
     */
    public function updateDepthLevel()
    {
        $depth = 0;
        $current = $this->parent;

        while ($current) {
            $depth++;
            $current = $current->parent;
        }

        $this->update(['depth_level' => $depth]);
        
        // Update children recursively
        foreach ($this->children as $child) {
            $child->updateDepthLevel();
        }
    }
}