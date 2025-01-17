<?php
namespace Modules\Portfolio\App\Http\Livewire;

use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Portfolio\App\Http\Livewire\Traits\InlineEditTitle;
use Modules\Portfolio\App\Http\Livewire\Traits\WithBulkActions;
use Modules\Portfolio\App\Models\Portfolio;

class PortfolioComponent extends Component
{
    use WithPagination, WithBulkActions, InlineEditTitle;

    #[Url]
    public $search = '';

    #[Url]
    public $perPortfolio = 10;

    #[Url]
    public $sortField = 'portfolio_id';

    #[Url]
    public $sortDirection = 'desc';

    protected function getModelClass()
    {
        return Portfolio::class;
    }

    public function updatedPerPortfolio()
    {
        $this->resetPortfolio();
    }

    public function updatedSearch()
    {
        $this->resetPortfolio();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField     = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleActive($id)
    {
        $tenant = tenancy()->tenant;

        if (! $tenant) {
            return;
        }

        $portfolio = Portfolio::where('portfolio_id', $id)
            ->where('tenant_id', $tenant->id)
            ->first();

        if ($portfolio) {
            $portfolio->is_active = ! $portfolio->is_active;
            $portfolio->save();
            $status = $portfolio->is_active ? 'aktif' : 'pasif';

            log_activity(
                'Sayfa',
                "\"{$portfolio->title}\" {$status} yapıldı.",
                $portfolio,
                [],
                $status
            );

            $this->dispatch('toast', [
                'title'   => 'Başarılı!',
                'message' => "\"{$portfolio->title}\" {$status} yapıldı.",
                'type'    => 'success',
            ]);
        }
    }

    public function render()
    {
        $tenant = tenancy()->tenant;
    
        $query = Portfolio::where('tenant_id', $tenant->id)
            ->where(function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('slug', 'like', '%' . $this->search . '%');
            });
    
        $portfolios = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPortfolio);
    
        return view('portfolio::livewire.portfolio-component', [
            'portfolios' => $portfolios,
        ])->layout('admin.layout');
    }
}
