<?php
namespace Modules\TenantManagement\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\TenantManagement\App\Models\Tenant;

class TenantManagementController extends Controller
{
    public function index()
    {
        // Tüm tenant'ları çek
        $tenants = Tenant::orderBy('created_at', 'desc')->get();

        return view('tenant::index', compact('tenants'));
    }
    public function manage(Request $request)
    {
        $data = $request->validate([
            'id'            => 'nullable|exists:tenants,id',
            'data.name'     => 'nullable|string|max:255',
            'data.fullname' => 'nullable|string|max:255',
            'data.email'    => 'nullable|email|max:255',
            'data.phone'    => 'nullable|string|max:20',
            'is_active'     => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        // Tenant kaydetme/güncelleme işlemi
        $tenant = Tenant::updateOrCreate(
            ['id' => $data['id']], // Sadece 'id' kullanıyoruz
            ['data' => $data['data'], 'is_active' => $data['is_active']]
        );

        // Log kaydı
        $action = $tenant->wasRecentlyCreated ? 'eklendi' : 'güncellendi';
        log_activity('Tenant', $action, $tenant);

        return response()->json([
            'success' => true,
            'tenant'  => $tenant,
        ]);
    }

    public function destroy($id)
    {
        $tenant = Tenant::find($id);

        if (! $tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Silinmek istenen tenant bulunamadı.',
            ], 404);
        }

        // Log kaydı
        log_activity('Tenant', 'silindi', $tenant);

        $tenant->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tenant başarıyla silindi.',
        ]);
    }

}
