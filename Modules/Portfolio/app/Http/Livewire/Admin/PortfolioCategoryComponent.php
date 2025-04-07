<?php
namespace Modules\Portfolio\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Illuminate\Support\Str;
use Modules\Portfolio\App\Models\PortfolioCategory;

#[Layout('admin.layout')]
class PortfolioCategoryComponent extends Component
{
    use WithPagination;

    public $title = '';
    public $newCategoryTitle = '';
    public $categories = [];
    public $maxPortfoliosCount = 0; 

    protected $rules = [
        'title' => 'required|min:3|max:255',
    ];

    protected $messages = [
        'title.required' => 'Kategori başlığı zorunludur.',
        'title.min' => 'Kategori başlığı en az 3 karakter olmalıdır.',
        'title.max' => 'Kategori başlığı en fazla 255 karakter olmalıdır.',
    ];

    public function mount()
    {
        $this->loadCategories();
    }

    #[On('refreshPage')] 
    public function loadCategories()
    {
        $this->categories = PortfolioCategory::orderBy('order')
            ->withCount('portfolios')
            ->get();

        $this->maxPortfoliosCount = $this->categories->max('portfolios_count') ?? 1; 
    }

    public function toggleActive($id)
    {
        $category = PortfolioCategory::findOrFail($id);
        $category->is_active = !$category->is_active;
        $category->save();

        log_activity(
            $category,
            $category->is_active ? 'aktif edildi' : 'pasif edildi'
        );

        $this->dispatch('toast', [
            'title' => 'Başarılı!', 
            'message' => "Kategori " . ($category->is_active ? 'aktif' : 'pasif') . " edildi.",
            'type' => 'success'
        ]);

        $this->loadCategories();
    }

    public function delete($id)
    {
        $category = PortfolioCategory::findOrFail($id);
        
        if ($category->portfolios()->count() > 0) {
            $this->dispatch('toast', [
                'title' => 'Uyarı!',
                'message' => 'Bu kategoriye bağlı portfolyolar var. Önce bunları silmelisiniz veya başka kategoriye taşımalısınız.',
                'type' => 'warning'
            ]);
            return;
        }
        
        $category->delete();

        log_activity(
            $category,
            'silindi'
        );

        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Kategori başarıyla silindi.',
            'type' => 'success'
        ]);

        $this->loadCategories();
    }

    public function quickAdd()
    {
        try {
            $this->validate();
            
            $maxOrder = PortfolioCategory::max('order') ?? 0;
            
            $category = PortfolioCategory::create([
                'title' => $this->title,
                'slug' => Str::slug($this->title),
                'order' => $maxOrder + 1,
                'is_active' => true,
            ]);
            
            if (!$category) {
                throw new \Exception('Kategori eklenirken bir hata oluştu.');
            }

            log_activity($category, 'oluşturuldu');

            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => 'Kategori başarıyla eklendi.',
                'type' => 'success',
            ]);
            
            $this->reset('title');
            $this->loadCategories();

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Lütfen form alanlarını kontrol ediniz.',
                'type' => 'error',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata!',
                'message' => 'Kategori eklenirken bir hata oluştu.',
                'type' => 'error',
            ]);
        }
    }

    #[On('updateOrder')] 
    public function updateOrder($list)
    {
        if (!is_array($list)) {
            return;
        }

        foreach ($list as $item) {
            if (!isset($item['value'], $item['order'])) {
                continue;
            }

            PortfolioCategory::where('portfolio_category_id', $item['value'])
                ->update(['order' => $item['order']]);
        }

        $this->loadCategories();
        
        $this->dispatch('toast', [
            'title' => 'Başarılı!',
            'message' => 'Kategori sıralaması güncellendi.',
            'type' => 'success',
        ]);
    }

    public function render()
    {
        return view('portfolio::admin.livewire.portfolio-category-component');
    }
}