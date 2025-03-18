<?php
// Modules/Portfolio/App/Http/Livewire/PortfolioComponent.php
namespace Modules\Portfolio\App\Http\Livewire;

use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Portfolio\App\Http\Livewire\Traits\InlineEditTitle;
use Modules\Portfolio\App\Http\Livewire\Traits\WithBulkActions;
use Modules\Portfolio\App\Models\Portfolio;
use Modules\Portfolio\App\Models\PortfolioCategory;

#[Layout('admin.layout')]
class PortfolioComponent extends Component
{
    use WithPagination, WithBulkActions, InlineEditTitle;

    #[Url]
    public $search = '';

    #[Url]
    public $perPage = 10;

    #[Url]
    public $sortField = 'portfolio_id';

    #[Url]
    public $sortDirection = 'desc';

    #[Url]
    public $selectedCategory = '';

    protected $queryString = [
        'sortField' => ['except' => 'portfolio_id'],
        'sortDirection' => ['except' => 'desc'],
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'selectedCategory' => ['except' => ''],
    ];

    protected function getModelClass()
    {
        return Portfolio::class;
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedCategory()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleActive($id)
    {
        $portfolio = Portfolio::where('portfolio_id', $id)->first();
    
        if ($portfolio) {
            $portfolio->update(['is_active' => !$portfolio->is_active]);
            
            log_activity(
                $portfolio,
                $portfolio->is_active ? 'aktif edildi' : 'pasif edildi',
                ['is_active' => $portfolio->is_active]
            );
    
            $this->dispatch('toast', [
                'title' => 'Başarılı!',
                'message' => "\"{$portfolio->title}\" " . ($portfolio->is_active ? 'aktif' : 'pasif') . " edildi.",
                'type' => 'success',
            ]);
        }
    }

    public function render()
    {
        $baseQuery = Portfolio::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('portfolios.title', 'like', '%' . $this->search . '%')
                        ->orWhere('portfolios.slug', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedCategory, function ($query) {
                $query->where('portfolios.portfolio_category_id', $this->selectedCategory);
            });

        if ($this->sortField === 'portfolio_category_id') {
            $query = $baseQuery->select('portfolios.*')
                ->leftJoin('portfolio_categories', function($join) {
                    $join->on('portfolios.portfolio_category_id', '=', 'portfolio_categories.portfolio_category_id');
                })
                ->orderBy('portfolio_categories.title', $this->sortDirection);
        } else {
            $query = $baseQuery->orderBy('portfolios.' . $this->sortField, $this->sortDirection);
        }
    
        $portfolios = $query->paginate($this->perPage);
    
        $categories = PortfolioCategory::where('is_active', true)
            ->orderBy('title')
            ->get();
    
        return view('portfolio::livewire.portfolio-component', [
            'portfolios' => $portfolios,
            'categories' => $categories,
        ]);
    }
}