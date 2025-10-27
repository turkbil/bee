<?php
namespace App\Livewire\Modals;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class CategoryDeleteModal extends Component
{
    public $showModal = false;
    public $module;
    public $itemId;
    public $title;
    public $selectedCategory = '';
    public $categories = [];
    public $contentCount = 0;
    public $childCategoryCount = 0;

    protected $listeners = ['showCategoryDeleteModal'];

    public function showCategoryDeleteModal($module, $id, $title)
    {
        $this->module = $module;
        $this->itemId = $id;
        $this->title = $title;
        $this->selectedCategory = '';

        $categoryTable = $this->module . '_categories';
        $contentForeignKey = $this->module . '_category_id'; // İçerik tablosundaki foreign key
        $contentTable = $this->module . 's';

        $this->contentCount = DB::table($contentTable)
            ->where($contentForeignKey, $this->itemId)
            ->whereNull('deleted_at')
            ->count();

        // Alt kategori sayısını hesapla
        $this->childCategoryCount = DB::table($categoryTable)
            ->where('parent_id', $this->itemId)
            ->whereNull('deleted_at')
            ->count();

        // Alt kategorilerin ID'lerini bul (recursive olarak)
        $childCategoryIds = $this->getChildCategoryIds($categoryTable, $this->itemId);
        $childCategoryIds[] = $this->itemId; // Kendisini de ekle

        $allCategories = DB::table($categoryTable)
            ->whereNotIn('category_id', $childCategoryIds)
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->get()
            ->map(function($category) {
                // JSON title'ı decode et
                if (isset($category->title)) {
                    $titleData = json_decode($category->title, true);
                    if (is_array($titleData)) {
                        $category->display_title = $titleData['tr'] ?? $titleData[collect($titleData)->keys()->first()] ?? 'Başlıksız';
                    } else {
                        $category->display_title = $category->title;
                    }
                }
                // Dropdown için ID
                $category->id = $category->category_id;

                return $category;
            });

        // Depth hesaplama için hızlı lookup map oluştur
        $categoryMap = $allCategories->keyBy('category_id');
        $depthCache = [];

        // Depth hesaplama fonksiyonu (memoization ile)
        $calculateDepth = function($categoryId) use ($categoryMap, &$depthCache, &$calculateDepth) {
            if (isset($depthCache[$categoryId])) {
                return $depthCache[$categoryId];
            }

            $category = $categoryMap->get($categoryId);
            if (!$category || !$category->parent_id) {
                $depthCache[$categoryId] = 0;
                return 0;
            }

            $depth = $calculateDepth($category->parent_id) + 1;
            $depthCache[$categoryId] = $depth;
            return $depth;
        };

        // Depth hesapla ve alfabetik sırala
        $this->categories = $allCategories->map(function($category) use ($calculateDepth) {
            $category->depth_level = $calculateDepth($category->category_id);
            return $category;
        })->sortBy('display_title')->values();

        $this->showModal = true;
    }

    public function deleteCategory()
    {
        // Silme yetkisi kontrolü
        if (!auth()->user()->hasModulePermission($this->module, 'delete')) {
            $this->dispatch('toast', [
                'title' => 'Yetkisiz İşlem!',
                'message' => 'Bu işlem için gerekli yetkiniz bulunmamaktadır.',
                'type' => 'error',
            ]);
            $this->showModal = false;
            return;
        }

        try {
            DB::beginTransaction();

            $contentForeignKey = $this->module . '_category_id'; // portfolios tablosundaki foreign key

            $categoryModelClass = "Modules\\" . ucfirst($this->module) . "\\App\\Models\\" . ucfirst($this->module) . "Category";
            $category = $categoryModelClass::find($this->itemId);

            $contentModelClass = "Modules\\" . ucfirst($this->module) . "\\App\\Models\\" . ucfirst($this->module);
            $contents = $contentModelClass::where($contentForeignKey, $this->itemId)
                ->whereNull('deleted_at')
                ->get();

            // Alt kategorileri bul
            $childCategories = $categoryModelClass::where('parent_id', $this->itemId)
                ->whereNull('deleted_at')
                ->get();

            // Alt kategorilerin içeriklerini de bul
            foreach ($childCategories as $childCategory) {
                $childContents = $contentModelClass::where($contentForeignKey, $childCategory->category_id)
                    ->whereNull('deleted_at')
                    ->get();

                foreach ($childContents as $childContent) {
                    if (method_exists($childContent, 'getMedia')) {
                        $collections = ['featured_image', 'gallery'];
                        foreach ($collections as $collection) {
                            if ($childContent->hasMedia($collection)) {
                                $childContent->clearMediaCollection($collection);
                            }
                        }
                    }
                    log_activity($childContent, 'üst kategori ile birlikte silindi');
                }
            }

            // Alt kategorileri sil
            foreach ($childCategories as $childCategory) {
                if (method_exists($childCategory, 'getMedia')) {
                    $collections = ['featured_image'];
                    foreach ($collections as $collection) {
                        if ($childCategory->hasMedia($collection)) {
                            $childCategory->clearMediaCollection($collection);
                        }
                    }
                }
                log_activity($childCategory, 'üst kategori ile birlikte silindi');
            }

            foreach ($contents as $content) {
                if (method_exists($content, 'getMedia')) {
                    $collections = ['image'];
                    for ($i = 1; $i <= 10; $i++) {
                        $collections[] = 'image_' . $i;
                    }

                    foreach ($collections as $collection) {
                        if ($content->hasMedia($collection)) {
                            $content->clearMediaCollection($collection);
                        }
                    }
                }
                
                log_activity(
                    $content,
                    'kategori ile birlikte silindi'
                );
            }

            if ($category) {
                if (method_exists($category, 'getMedia')) {
                    $collections = ['image'];
                    for ($i = 1; $i <= 10; $i++) {
                        $collections[] = 'image_' . $i;
                    }

                    foreach ($collections as $collection) {
                        if ($category->hasMedia($collection)) {
                            $category->clearMediaCollection($collection);
                        }
                    }
                }

                log_activity(
                    $category,
                    'silindi'
                );
            }

            $categoryTable = $this->module . '_categories';
            $contentTable = $this->module . 's';
            $now = now();

            // Kategoriyi soft delete
            DB::table($categoryTable)
                ->where('category_id', $this->itemId)
                ->update(['deleted_at' => $now]);

            // Bu kategorideki içerikleri soft delete
            DB::table($contentTable)
                ->where($contentForeignKey, $this->itemId)
                ->whereNull('deleted_at')
                ->update(['deleted_at' => $now]);

            // Alt kategorilerin ID'lerini al
            $childCategoryIds = DB::table($categoryTable)
                ->where('parent_id', $this->itemId)
                ->whereNull('deleted_at')
                ->pluck('category_id');

            if ($childCategoryIds->isNotEmpty()) {
                // Alt kategorilerin içeriklerini soft delete
                DB::table($contentTable)
                    ->whereIn($contentForeignKey, $childCategoryIds)
                    ->whereNull('deleted_at')
                    ->update(['deleted_at' => $now]);

                // Alt kategorileri soft delete
                DB::table($categoryTable)
                    ->whereIn('category_id', $childCategoryIds)
                    ->update(['deleted_at' => $now]);
            }

            DB::commit();

            $this->showModal = false;
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Kategori ve içerikleri silindi.',
                'type' => 'success',
            ]);

            $this->dispatch('categoryDeleted');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'İşlem sırasında bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error',
            ]);
        }
    }

    public function move()
    {
        // Silme ve taşıma yetkisi kontrolü
        if (!auth()->user()->hasModulePermission($this->module, 'delete') || 
            !auth()->user()->hasModulePermission($this->module, 'update')) {
            $this->dispatch('toast', [
                'title' => 'Yetkisiz İşlem!',
                'message' => 'Bu işlem için gerekli yetkiniz bulunmamaktadır.',
                'type' => 'error',
            ]);
            $this->showModal = false;
            return;
        }
        
        try {
            DB::beginTransaction();

            $contentForeignKey = $this->module . '_category_id'; // portfolios tablosundaki foreign key

            $categoryModelClass = "Modules\\" . ucfirst($this->module) . "\\App\\Models\\" . ucfirst($this->module) . "Category";
            $category = $categoryModelClass::find($this->itemId);

            $contentModelClass = "Modules\\" . ucfirst($this->module) . "\\App\\Models\\" . ucfirst($this->module);
            $contents = $contentModelClass::where($contentForeignKey, $this->itemId)
                ->whereNull('deleted_at')
                ->get();

            $targetCategory = $categoryModelClass::find($this->selectedCategory);

            foreach ($contents as $content) {
                log_activity(
                    $content,
                    'kategori değiştirildi',
                    [
                        'eski_kategori' => $category->title,
                        'yeni_kategori' => $targetCategory->title
                    ]
                );
            }

            if ($category) {
                log_activity(
                    $category,
                    'içerikleri taşındı ve silindi',
                    [
                        'hedef_kategori' => $targetCategory->title,
                        'taşınan_içerik_sayısı' => count($contents)
                    ]
                );

                if (method_exists($category, 'getMedia')) {
                    $collections = ['image'];
                    for ($i = 1; $i <= 10; $i++) {
                        $collections[] = 'image_' . $i;
                    }

                    foreach ($collections as $collection) {
                        if ($category->hasMedia($collection)) {
                            $category->clearMediaCollection($collection);
                        }
                    }
                }
            }

            $categoryTable = $this->module . '_categories';
            $contentTable = $this->module . 's';
            $now = now();

            // Bu kategorideki içerikleri hedef kategoriye taşı
            DB::table($contentTable)
                ->where($contentForeignKey, $this->itemId)
                ->whereNull('deleted_at')
                ->update([$contentForeignKey => $this->selectedCategory]);

            // Alt kategorileri hedef kategoriye taşı (parent_id değiştir)
            DB::table($categoryTable)
                ->where('parent_id', $this->itemId)
                ->whereNull('deleted_at')
                ->update(['parent_id' => $this->selectedCategory]);

            // Alt kategorilerin içeriklerini de hedef kategoriye taşı (isteğe bağlı)
            $childCategoryIds = DB::table($categoryTable)
                ->where('parent_id', $this->selectedCategory)
                ->whereNull('deleted_at')
                ->pluck('category_id');

            if ($childCategoryIds->isNotEmpty()) {
                DB::table($contentTable)
                    ->whereIn($contentForeignKey, $childCategoryIds)
                    ->whereNull('deleted_at')
                    ->update([$contentForeignKey => $this->selectedCategory]);
            }

            // Kategoriyi soft delete
            DB::table($categoryTable)
                ->where('category_id', $this->itemId)
                ->update(['deleted_at' => $now]);

            DB::commit();

            $this->showModal = false;
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'İçerikler taşındı ve kategori silindi.',
                'type' => 'success',
            ]);

            $this->dispatch('categoryDeleted');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'İşlem sırasında bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error',
            ]);
        }
    }

    /**
     * Alt kategorilerin ID'lerini recursive olarak bul
     */
    private function getChildCategoryIds($categoryTable, $parentId)
    {
        $childIds = [];

        $children = DB::table($categoryTable)
            ->where('parent_id', $parentId)
            ->whereNull('deleted_at')
            ->pluck('category_id');

        foreach ($children as $childId) {
            $childIds[] = $childId;
            // Recursive olarak alt kategorilerin de alt kategorilerini bul
            $childIds = array_merge($childIds, $this->getChildCategoryIds($categoryTable, $childId));
        }

        return $childIds;
    }

    /**
     * Kategorinin depth level'ını hesapla
     */
    private function calculateDepth($categoryId, $parentId, $visited = [])
    {
        // Circular reference kontrolü
        if (in_array($categoryId, $visited)) {
            return 0;
        }

        if (!$parentId) {
            return 0;
        }

        $visited[] = $categoryId;
        $categoryTable = $this->module . '_categories';

        $parent = DB::table($categoryTable)
            ->where('category_id', $parentId)
            ->whereNull('deleted_at')
            ->first();

        if (!$parent) {
            return 0;
        }

        return $this->calculateDepth($parent->category_id, $parent->parent_id ?? null, $visited) + 1;
    }

    /**
     * Hiyerarşik kategori listesi oluştur
     */
    private function buildHierarchicalList($categories, $parentId = null, $depth = 0)
    {
        $result = [];

        // Tüm category ID'lerini al
        $categoryIds = $categories->pluck('category_id')->toArray();

        // Parent'ı bu listede olmayan kategoriler root olarak kabul edilir
        $children = $categories->filter(function($cat) use ($parentId, $categoryIds) {
            if ($parentId === null) {
                // Root seviye: parent_id null VEYA parent_id listede yok
                return $cat->parent_id === null || !in_array($cat->parent_id, $categoryIds);
            }
            return $cat->parent_id === $parentId;
        })->sortBy('display_title');

        foreach ($children as $category) {
            $category->depth_level = $depth;
            $result[] = $category;

            // Alt kategorileri ekle
            $subCategories = $this->buildHierarchicalList($categories, $category->category_id, $depth + 1);
            foreach ($subCategories as $sub) {
                $result[] = $sub;
            }
        }

        return collect($result);
    }

    public function render()
    {
        return view('livewire.modals.category-delete-modal');
    }
}