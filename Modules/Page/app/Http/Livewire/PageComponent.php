<?php
namespace Modules\Page\app\Http\Livewire;

use Livewire\Component;
use Modules\Page\app\Models\Page;

class PageComponent extends Component
{
    // Server-side paginasyon için
    public function getData($params)
    {
        $query = Page::where('tenant_id', tenant('id'));

        if (! empty($params['search'])) {
            $query->where('title', 'like', '%' . $params['search'] . '%');
        }

        if (! empty($params['sort']) && ! empty($params['order'])) {
            $query->orderBy($params['sort'], $params['order']);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $total = $query->count();

        $result = $query->offset($params['offset'])
            ->limit($params['limit'])
            ->get();

        return response()->json([
            'total'            => $total,
            'totalNotFiltered' => Page::where('tenant_id', tenant('id'))->count(),
            'rows'             => $result,
        ]);
    }

    public function toggleActive($pageId)
    {
        $page = Page::where('tenant_id', tenant('id'))
            ->where('page_id', $pageId)
            ->first();

        if ($page) {
            $page->is_active = ! $page->is_active;
            $page->save();

            activity()
                ->causedBy(auth()->user())
                ->log($page->title . " başlıklı sayfa " . ($page->is_active ? 'aktif' : 'pasif') . " duruma getirildi");

            // Tabloyu yenile
            $this->dispatch('tableUpdated');

            return true;
        }

        return false;
    }

    public function deletePage($pageId, $title)
    {
        Page::where('tenant_id', tenant('id'))
            ->where('page_id', $pageId)
            ->delete();

        activity()
            ->causedBy(auth()->user())
            ->log("$title başlıklı sayfa silindi");

        $this->dispatch('refreshDatatable');
    }

    public function render()
    {
        return view('page::livewire.page-component');
    }
}
