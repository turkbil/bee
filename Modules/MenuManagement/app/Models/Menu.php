<?php

namespace Modules\MenuManagement\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasTranslations;

class Menu extends Model
{
    use HasTranslations, SoftDeletes;

    protected $primaryKey = 'menu_id';

    protected $fillable = [
        'name',
        'slug',
        'location',
        'is_default',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'name' => 'array',
        'settings' => 'array',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Çevrilebilir alanlar
     */
    protected $translatable = ['name'];

    /**
     * Sluggable configuration
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
                'method' => function ($string, $separator) {
                    // Multi-language name için sadece varsayılan dili kullan
                    $name = is_array($string) ? ($string[config('app.locale')] ?? reset($string)) : $string;
                    return \Str::slug($name, $separator);
                }
            ]
        ];
    }

    /**
     * Menu items relationship
     */
    public function items()
    {
        return $this->hasMany(MenuItem::class, 'menu_id', 'menu_id')
            ->orderBy('sort_order');
    }

    /**
     * Root level menu items
     */
    public function rootItems()
    {
        return $this->hasMany(MenuItem::class, 'menu_id', 'menu_id')
            ->whereNull('parent_id')
            ->orderBy('sort_order');
    }

    /**
     * Active menu items
     */
    public function activeItems()
    {
        return $this->hasMany(MenuItem::class, 'menu_id', 'menu_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    /**
     * Aktif menüleri getir
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Default menüyü getir
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Location'a göre menü getir
     */
    public function scopeByLocation($query, $location)
    {
        return $query->where('location', $location);
    }

    /**
     * Get menu tree structure
     */
    public function getTreeStructure()
    {
        return $this->buildTree($this->activeItems()->get()->toArray());
    }

    /**
     * Build hierarchical tree from flat array
     */
    private function buildTree(array $items, $parentId = null): array
    {
        $branch = [];

        foreach ($items as $item) {
            if ($item['parent_id'] == $parentId) {
                $children = $this->buildTree($items, $item['item_id']);
                if ($children) {
                    $item['children'] = $children;
                }
                $branch[] = $item;
            }
        }

        return $branch;
    }

}