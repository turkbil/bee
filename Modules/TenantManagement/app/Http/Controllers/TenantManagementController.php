<?php

namespace Modules\TenantManagement\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;

class TenantManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $tenants = Tenant::with('domains')
                ->select(['id', 'title', 'is_active', 'theme_id', 'created_at', 'updated_at'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $tenants,
                'message' => 'Tenant listesi başarıyla getirildi.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant listesi getirilirken bir hata oluştu.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'fullname' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'is_active' => 'boolean',
                'theme_id' => 'required|integer|exists:themes,theme_id',
            ]);

            // Benzersiz veritabanı adı oluştur
            $baseDbName = 'tenant_' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $validated['title']));
            $randomSuffix = '_' . substr(md5(mt_rand()), 0, 6);
            $dbName = $baseDbName . $randomSuffix;

            // Data alanı için veriyi hazırla
            $data = [];
            if (!empty($validated['fullname'])) $data['fullname'] = $validated['fullname'];
            if (!empty($validated['email'])) $data['email'] = $validated['email'];
            if (!empty($validated['phone'])) $data['phone'] = $validated['phone'];
            $data['created_at'] = now()->toDateTimeString();

            $tenant = Tenant::create([
                'title' => $validated['title'],
                'tenancy_db_name' => $dbName,
                'is_active' => $validated['is_active'] ?? true,
                'theme_id' => $validated['theme_id'],
                'data' => empty($data) ? null : $data,
            ]);

            return response()->json([
                'success' => true,
                'data' => $tenant->load('domains'),
                'message' => 'Tenant başarıyla oluşturuldu.'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasyon hatası.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant oluşturulurken bir hata oluştu.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        try {
            $tenant = Tenant::with('domains')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $tenant,
                'message' => 'Tenant detayları başarıyla getirildi.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant bulunamadı.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant detayları getirilirken bir hata oluştu.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $tenant = Tenant::findOrFail($id);

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'fullname' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'is_active' => 'boolean',
                'theme_id' => 'required|integer|exists:themes,theme_id',
            ]);

            // Data alanı için veriyi hazırla
            $data = [];
            if (!empty($validated['fullname'])) $data['fullname'] = $validated['fullname'];
            if (!empty($validated['email'])) $data['email'] = $validated['email'];
            if (!empty($validated['phone'])) $data['phone'] = $validated['phone'];
            $data['updated_at'] = now()->toDateTimeString();

            $tenant->update([
                'title' => $validated['title'],
                'is_active' => $validated['is_active'] ?? $tenant->is_active,
                'theme_id' => $validated['theme_id'],
                'data' => empty($data) ? null : $data,
            ]);

            return response()->json([
                'success' => true,
                'data' => $tenant->load('domains'),
                'message' => 'Tenant başarıyla güncellendi.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant bulunamadı.'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasyon hatası.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant güncellenirken bir hata oluştu.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $tenant = Tenant::findOrFail($id);
            
            // Log için eski verileri sakla
            $tenantData = [
                'id' => $tenant->id,
                'title' => $tenant->title
            ];
            
            $tenant->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tenant başarıyla silindi.',
                'data' => $tenantData
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant bulunamadı.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant silinirken bir hata oluştu.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle tenant active status
     */
    public function toggleStatus($id): JsonResponse
    {
        try {
            $tenant = Tenant::findOrFail($id);
            
            $oldStatus = $tenant->is_active;
            $newStatus = !$oldStatus;
            
            $tenant->update(['is_active' => $newStatus]);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $tenant->id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus
                ],
                'message' => 'Tenant durumu ' . ($newStatus ? 'aktif' : 'pasif') . ' olarak değiştirildi.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant bulunamadı.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant durumu değiştirilirken bir hata oluştu.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}