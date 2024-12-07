<?php // TenantManagementController.php
namespace Modules\TenantManagement\App\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\TenantManagement\App\Models\Tenant;

class TenantManagementController extends Controller
{
    public function index()
    {
        $tenants = Tenant::orderBy('created_at', 'desc')->get();

        return view('tenant::index', compact('tenants'));
    }
/*
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

        $tenant = Tenant::updateOrCreate(
            ['id' => $data['id']],
            ['data' => $data['data'], 'is_active' => $data['is_active']]
        );

        return response()->json(['success' => true, 'tenant' => $tenant]);
    }

    public function destroy($id)
    {
        $tenant = Tenant::find($id);

        if (! $tenant) {
            return response()->json(['success' => false, 'message' => 'Silinmek istenen tenant bulunamadı.'], 404);
        }

        $tenant->delete();

        return response()->json(['success' => true]);
    }

    public function addDomain(Request $request)
    {
        $request->validate([
            'domain'    => 'required|string|unique:domains,domain|max:255',
            'tenant_id' => 'required|exists:tenants,id',
        ]);

        $domain = Domain::create([
            'domain'    => $request->domain,
            'tenant_id' => $request->tenant_id,
        ]);

        if (! $domain) {
            \Log::error('Kayıt işlemi başarısız:', $request->all());
            return response()->json(['success' => false, 'message' => 'Domain kaydedilemedi.']);
        }

        return response()->json([
            'success' => true,
            'domain'  => $domain,
        ]);

        try {
            $domain = Domain::create([
                'domain'    => $request->domain,
                'tenant_id' => $request->tenant_id,
            ]);

            if ($domain) {
                return response()->json(['success' => true, 'domain' => $domain]);
            }
        } catch (\Exception $e) {
            \Log::error('Hata:', ['message' => $e->getMessage(), 'data' => $request->all()]);
            return response()->json(['success' => false, 'message' => 'Kayıt sırasında bir hata oluştu.']);
        }

    }

    public function deleteDomain($id)
    {
        $domain = Domain::find($id);

        if (! $domain) {
            return response()->json(['success' => false, 'message' => 'Domain bulunamadı.'], 404);
        }

        $domain->delete();

        return response()->json(['success' => true]);
    }

    public function updateDomain(Request $request, $id)
    {
        $request->validate([
            'domain' => 'required|string|unique:domains,domain|max:255',
        ]);

        $domain = Domain::find($id);

        if (! $domain) {
            return response()->json(['success' => false, 'message' => 'Domain bulunamadı.'], 404);
        }

        $domain->update(['domain' => $request->domain]);

        return response()->json(['success' => true]);
    }

    public function getDomains($tenantId)
    {
        $tenant = Tenant::find($tenantId);

        if (! $tenant) {
            return response()->json(['success' => false, 'message' => 'Tenant bulunamadı.'], 404);
        }

        $domains = $tenant->domains()->get();

        return response()->json(['success' => true, 'domains' => $domains]);
    }
*/
}
