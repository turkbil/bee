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

    protected $listeners = ['showCategoryDeleteModal'];

    public function showCategoryDeleteModal($module, $id, $title)
    {
        $this->module = $module;
        $this->itemId = $id;
        $this->title = $title;
        $this->selectedCategory = '';

        $categoryTable = $this->module . '_categories';
        $categoryIdColumn = $this->module . '_category_id';
        $contentTable = $this->module . 's';

        $this->contentCount = DB::table($contentTable)
            ->where($categoryIdColumn, $this->itemId)
            ->whereNull('deleted_at')
            ->count();

        $this->categories = DB::table($categoryTable)
            ->where($categoryIdColumn, '!=', $this->itemId)
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->orderBy('title', 'asc')
            ->get();

        $this->showModal = true;
    }

    public function delete()
    {
        try {
            DB::beginTransaction();

            $categoryModelClass = "Modules\\" . ucfirst($this->module) . "\\App\\Models\\" . ucfirst($this->module) . "Category";
            $category = $categoryModelClass::find($this->itemId);

            $contentModelClass = "Modules\\" . ucfirst($this->module) . "\\App\\Models\\" . ucfirst($this->module);
            $contents = $contentModelClass::where($this->module . '_category_id', $this->itemId)
                ->whereNull('deleted_at')
                ->get();

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
            $categoryIdColumn = $this->module . '_category_id';
            $contentTable = $this->module . 's';
            $now = now();

            DB::table($categoryTable)
                ->where($categoryIdColumn, $this->itemId)
                ->update(['deleted_at' => $now]);

            DB::table($contentTable)
                ->where($categoryIdColumn, $this->itemId)
                ->whereNull('deleted_at')
                ->update(['deleted_at' => $now]);

            DB::commit();

            $this->showModal = false;
            $this->dispatch('toast', [
                'title' => 'Başarılı!', 
                'message' => 'Kategori ve içerikleri silindi.',
                'type' => 'success',
            ]);

            $this->dispatch('refreshPage');

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
        try {
            DB::beginTransaction();

            $categoryModelClass = "Modules\\" . ucfirst($this->module) . "\\App\\Models\\" . ucfirst($this->module) . "Category";
            $category = $categoryModelClass::find($this->itemId);

            $contentModelClass = "Modules\\" . ucfirst($this->module) . "\\App\\Models\\" . ucfirst($this->module);
            $contents = $contentModelClass::where($this->module . '_category_id', $this->itemId)
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
            $categoryIdColumn = $this->module . '_category_id';
            $contentTable = $this->module . 's';
            $now = now();

            DB::table($contentTable)
                ->where($categoryIdColumn, $this->itemId)
                ->whereNull('deleted_at')
                ->update([$categoryIdColumn => $this->selectedCategory]);

            DB::table($categoryTable)
                ->where($categoryIdColumn, $this->itemId)
                ->update(['deleted_at' => $now]);

            DB::commit();

            $this->showModal = false;
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'İçerikler taşındı ve kategori silindi.',
                'type' => 'success',
            ]);

            $this->dispatch('refreshPage');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'İşlem sırasında bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.modals.category-delete-modal');
    }
}