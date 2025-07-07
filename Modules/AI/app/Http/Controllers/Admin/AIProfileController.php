<?php

namespace Modules\AI\app\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\AI\app\Models\AITenantProfile;
use Modules\AI\app\Models\AIProfileSector;
use Illuminate\Http\Request;

class AIProfileController extends Controller
{
    /**
     * Profil görüntüleme sayfası
     */
    public function show()
    {
        try {
            $profile = AITenantProfile::currentOrCreate();
            $sector = null;
            
            if ($profile && isset($profile->sector_details['sector'])) {
                $sector = AIProfileSector::where('code', $profile->sector_details['sector'])->first();
            }
            
            // Profil tamamlandıysa ve hikaye yoksa oluştur
            $brandStoryGenerating = false;
            if ($profile->is_completed && !$profile->hasBrandStory()) {
                try {
                    $profile->generateBrandStory();
                    session()->flash('brand_story_generated', 'Marka hikayeniz başarıyla oluşturuldu!');
                } catch (\Exception $e) {
                    \Log::error('Brand story generation failed in show', ['error' => $e->getMessage()]);
                    $brandStoryGenerating = true; // Loading state göster
                }
            }
            
            return view('ai::admin.profile.show', compact('profile', 'sector', 'brandStoryGenerating'));
        } catch (\Exception $e) {
            return redirect()->route('admin.dashboard')
                           ->with('error', 'Profil yüklenirken hata: ' . $e->getMessage());
        }
    }
    
    /**
     * Profil düzenleme sayfası (Livewire component)
     */
    public function edit($step = 1)
    {
        // Step validation
        $step = max(1, min(6, (int) $step));
        
        return view('ai::admin.profile.edit', [
            'initialStep' => $step
        ]);
    }
    
    /**
     * Marka hikayesi oluştur (AJAX)
     */
    public function generateStory(Request $request)
    {
        try {
            $profile = AITenantProfile::currentOrCreate();
            
            if (!$profile->is_completed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profil henüz tamamlanmamış. Önce profili tamamlamanız gerekiyor.'
                ]);
            }
            
            // Mevcut hikayeyi sil (yeniden oluşturma durumunda)
            $profile->brand_story = null;
            $profile->brand_story_created_at = null;
            
            // Yeni hikaye oluştur
            $brandStory = $profile->generateBrandStory();
            
            return response()->json([
                'success' => true,
                'message' => 'Marka hikayeniz başarıyla oluşturuldu!',
                'story' => $brandStory,
                'created_at' => $profile->brand_story_created_at->format('d.m.Y H:i')
            ]);
            
        } catch (\Exception $e) {
            \Log::error('AIProfileController - Generate story error', [
                'error' => $e->getMessage(),
                'tenant_id' => tenant('id')
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Profili sıfırla
     */
    public function reset(Request $request)
    {
        try {
            $profile = AITenantProfile::currentTenant();
            
            if ($profile && $profile->exists) {
                // Cache'i temizle
                \Illuminate\Support\Facades\Cache::forget('ai_tenant_profile_' . tenant('id'));
                
                // Profili tamamen sil (ID dahil)
                $profile->forceDelete();
                
                \Log::info('AIProfileController - Profile force deleted', [
                    'tenant_id' => tenant('id'),
                    'profile_id' => $profile->id
                ]);
            }
            
            return redirect()->route('admin.ai.profile.show')
                             ->with('success', 'Yapay zeka profili tamamen sıfırlandı! Yeni profil oluşturabilirsiniz.');
        } catch (\Exception $e) {
            \Log::error('AIProfileController - Reset error', [
                'error' => $e->getMessage(),
                'tenant_id' => tenant('id')
            ]);
            
            return redirect()->route('admin.ai.profile.show')
                           ->with('error', 'Profil sıfırlanırken hata: ' . $e->getMessage());
        }
    }

    /**
     * jQuery Auto-Save için field kaydetme
     */
    public function saveField(Request $request)
    {
        \Log::info('🔧 AIProfileController - saveField called', [
            'field' => $request->input('field'),
            'value' => $request->input('value'),
            'step' => $request->input('step')
        ]);
        
        try {
            $field = $request->input('field');
            $value = $request->input('value');
            $step = $request->input('step', 1);

            // Tenant ID resolve_tenant_id helper ile al
            $tenantId = resolve_tenant_id();
            \Log::info('🔧 saveField - Tenant ID resolved', [
                'tenant_id' => $tenantId,
                'tenant_function' => tenant('id'),
                'session_tenant' => session('admin_selected_tenant_id')
            ]);
            
            if (!$tenantId) {
                \Log::error('❌ saveField - Tenant bulunamadı');
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant bulunamadı'
                ], 400);
            }

            // Profili al veya oluştur
            $profile = AITenantProfile::firstOrCreate(
                ['tenant_id' => $tenantId],
                [
                    'company_info' => [],
                    'sector_details' => [],
                    'success_stories' => [],
                    'ai_behavior_rules' => [],
                    'founder_info' => [],
                    'additional_info' => [],
                    'is_active' => true,
                    'is_completed' => false
                ]
            );

            // Field'ı parse et (section.key format'ında)
            $fieldParts = explode('.', $field, 2);
            $section = $fieldParts[0];
            $key = $fieldParts[1] ?? null;

            // Mevcut section data'sını al
            $sectionData = $profile->$section ?? [];

            // Checkbox array handling - Livewire format'a çevir
            if (is_array($value) && !empty($value)) {
                // Array gelirse (checkbox), nested object'e çevir
                $checkboxData = [];
                foreach ($value as $checkboxValue) {
                    $checkboxData[$checkboxValue] = true;
                }
                $sectionData[$key] = $checkboxData;
                
                \Log::info('✅ jQuery Auto-Save - Checkbox array converted', [
                    'field' => $field,
                    'section' => $section,
                    'key' => $key,
                    'original_value' => $value,
                    'converted_value' => $checkboxData
                ]);
            } else {
                // Normal field (text, select, radio)
                $sectionData[$key] = $value;
            }

            // Profile section'ını kaydet
            $profile->$section = $sectionData;
            $profile->save();

            \Log::info('✅ jQuery Auto-Save - Field saved', [
                'tenant_id' => $tenantId,
                'field' => $field,
                'section' => $section,
                'key' => $key,
                'value' => is_array($value) ? json_encode($value) : $value,
                'step' => $step
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Field başarıyla kaydedildi',
                'field' => $field,
                'value' => $value
            ]);

        } catch (\Exception $e) {
            \Log::error('❌ jQuery Auto-Save - Field save error', [
                'error' => $e->getMessage(),
                'field' => $request->input('field'),
                'tenant_id' => tenant('id')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Field kaydedilirken hata: ' . $e->getMessage()
            ], 500);
        }
    }
}