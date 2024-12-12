<?php
namespace Modules\Page\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Page\App\Models\Page;

class PageController extends Controller
{
    public function index()
    {
        $tenant = tenancy()->tenant;

        if (! $tenant) {
            // Tenant bulunamadığında hata
            return redirect()->back()->withErrors(['tenant' => 'Tenant bilgisi bulunamadı. Lütfen sistem yöneticinize başvurun.']);
        }

        $tenantId = $tenant->id;

        // En son eklenen sayfalar
        $pages = Page::where('tenant_id', $tenantId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('page::index', compact('pages'));
    }

    public function manage(Request $request, $page_id = null)
    {
        $tenant = tenancy()->tenant;

        if (! $tenant) {
            // Tenant bulunamadığında hata
            return redirect()->back()->withErrors(['tenant' => 'Tenant bilgisi bulunamadı. Lütfen sistem yöneticinize başvurun.']);
        }

        $tenantId = session('tenant_id', $tenant->id); // Tenant ID'yi session'dan al

        if ($request->isMethod('post')) {
            // Gelen veriyi doğrula
            $data = $request->validate([
                'title'     => 'required|string|max:255',
                'slug'      => 'nullable|string|max:255',
                'body'      => 'nullable|string',
                'css'       => 'nullable|string|max:255',
                'js'        => 'nullable|string|max:255',
                'metakey'   => 'nullable|string|max:255',
                'metadesc'  => 'nullable|string|max:255',
                'is_active' => 'nullable|boolean',
            ]);

            $data['tenant_id'] = $tenantId;

            // Güncelleme veya oluşturma işlemi
            $page = Page::updateOrCreate(
                ['page_id' => $page_id, 'tenant_id' => $tenantId], // Burada page_id kullanıyoruz
                $data
            );

            // İşlem türüne göre log
            $action = $page->wasRecentlyCreated ? 'eklendi' : 'güncellendi';
            log_activity('Sayfa', $action, $page);

            return redirect()->route('admin.page.index')->with('success', 'Sayfa başarıyla kaydedildi.');
        }

        // Burada da id yerine page_id kullanmalısınız
        $page = $page_id ? Page::where('page_id', $page_id)->where('tenant_id', $tenantId)->firstOrFail() : null;

        return view('page::manage', compact('page'));
    }

    public function destroy($page_id)
    {
        $tenant = tenancy()->tenant;

        if (! $tenant) {
            // Tenant bulunamadığında hata
            return response()->json([
                'success' => false,
                'message' => 'Tenant bilgisi bulunamadı.',
            ], 400);
        }

        $tenantId = $tenant->id;

        // Burada da id yerine page_id kullanıyoruz
        $page = Page::where('page_id', $page_id)->where('tenant_id', $tenantId)->first();

        if (! $page) {
            return response()->json([
                'success' => false,
                'message' => 'Silinmek istenen sayfa bulunamadı.',
            ], 404);
        }

        // Manuel olarak modül adını "Page" olarak veriyoruz
        log_activity('Sayfa', 'silindi', $page);

        $page->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sayfa başarıyla silindi.',
        ]);
    }

    public function list(Request $request)
    {
        $limit  = $request->input('limit', 10);
        $offset = $request->input('offset', 0);
        $sort   = $request->input('sort', 'created_at');
        $order  = $request->input('order', 'desc');
        $search = $request->input('search', '');

        $query = Page::where('tenant_id', tenant('id'));

        if ($search) {
            $query->where('title', 'like', '%' . $search . '%');
        }

        $total = $query->count();

        $rows = $query->orderBy($sort, $order)
            ->offset($offset)
            ->limit($limit)
            ->get();

        return response()->json([
            'total'            => $total,
            'totalNotFiltered' => Page::where('tenant_id', tenant('id'))->count(),
            'rows'             => $rows,
        ]);
    }
}
