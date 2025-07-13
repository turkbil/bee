<?php

namespace Modules\AI\App\Http\Controllers\Admin\Profile;

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
                    // API anahtarı kontrolü ÖNCE yap
                    $aiSettings = \Modules\AI\App\Models\Setting::first();
                    if (!$aiSettings || empty($aiSettings->api_key)) {
                        \Log::error('API anahtarı bulunamadı - marka hikayesi oluşturulamadı');
                        $brandStoryGenerating = true; // Loading state göster
                    } else {
                        // Async olarak hikaye oluştur (arka planda)
                        \Log::info('Brand story generation başlatılıyor - async');
                        $brandStoryGenerating = true; // Loading state göster
                        
                        // Hikaye oluşturma deneme sayısını kontrol et
                        $attemptKey = 'brand_story_attempt_' . $profile->id;
                        $attempts = session($attemptKey, 0);
                        
                        if ($attempts < 3) { // Maximum 3 deneme
                            session([$attemptKey => $attempts + 1]);
                            
                            try {
                                \Log::info('Brand story oluşturuluyor', [
                                    'profile_id' => $profile->id,
                                    'attempt' => $attempts + 1
                                ]);
                                
                                $profile->generateBrandStory();
                                session()->forget($attemptKey);
                                session()->flash('brand_story_generated', 'Marka hikayeniz başarıyla oluşturuldu!');
                                $brandStoryGenerating = false;
                                
                            } catch (\Exception $e) {
                                \Log::error('Brand story generation attempt failed', [
                                    'profile_id' => $profile->id,
                                    'attempt' => $attempts + 1,
                                    'error' => $e->getMessage()
                                ]);
                                throw $e; // Re-throw to be caught by outer try-catch
                            }
                        } else {
                            \Log::warning('Brand story generation max attempts reached', [
                                'profile_id' => $profile->id,
                                'attempts' => $attempts
                            ]);
                            session()->flash('brand_story_error', 'Hikaye oluşturma denemesi başarısız. Lütfen "Hikayeyi Yeniden Oluştur" butonunu kullanın.');
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Brand story generation failed in show', ['error' => $e->getMessage()]);
                    $brandStoryGenerating = true; // Loading state göster
                    session()->flash('brand_story_error', 'Marka hikayesi oluşturulurken hata: ' . $e->getMessage());
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
        // AI hikaye oluşturma uzun sürebilir - timeout arttır
        set_time_limit(300); // 5 dakika
        
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
            // Tenant ID'yi resolve_tenant_id helper ile al (daha güvenilir)
            $tenantId = resolve_tenant_id();
            
            \Log::info('🔧 AIProfileController - Reset başladı', [
                'tenant_id' => $tenantId,
                'tenant_function' => tenant('id'),
                'session_tenant' => session('admin_selected_tenant_id')
            ]);
            
            if (!$tenantId) {
                \Log::error('❌ Reset - Tenant ID bulunamadı');
                return redirect()->route('admin.ai.profile.show')
                                ->with('error', 'Tenant ID bulunamadı. Lütfen tenant seçimini kontrol edin.');
            }
            
            // Mevcut profili bul (varsa)
            $profile = AITenantProfile::where('tenant_id', $tenantId)->first();
            
            \Log::info('🔍 Reset - Profil arama sonucu', [
                'tenant_id' => $tenantId,
                'profile_found' => !is_null($profile),
                'profile_id' => $profile?->id,
                'total_profiles' => AITenantProfile::count()
            ]);
            
            if ($profile && $profile->exists) {
                // Cache'i temizle
                \Illuminate\Support\Facades\Cache::forget('ai_tenant_profile_' . $tenantId);
                
                // Profili tamamen sil (ID dahil) - veritabanından kalıcı olarak sil
                $profileId = $profile->id;
                $profile->forceDelete();
                
                \Log::info('✅ AIProfileController - Profile force deleted', [
                    'tenant_id' => $tenantId,
                    'profile_id' => $profileId,
                    'action' => 'complete_reset'
                ]);
                
                return redirect()->route('admin.ai.profile.show')
                                ->with('success', 'Yapay zeka profili tamamen sıfırlandı! Tüm veriler veritabanından silindi.');
            } else {
                \Log::info('ℹ️ Reset - Profil bulunamadı', [
                    'tenant_id' => $tenantId,
                    'total_profiles_in_db' => AITenantProfile::count()
                ]);
                
                return redirect()->route('admin.ai.profile.show')
                                ->with('info', 'Silinecek profil bulunamadı. Zaten temiz durumda.');
            }
            
        } catch (\Exception $e) {
            \Log::error('❌ AIProfileController - Reset error', [
                'error' => $e->getMessage(),
                'tenant_id' => tenant('id'),
                'trace' => $e->getTraceAsString()
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

            // Field'ı parse et (section.key format'ında VEYA tek alan)
            $fieldParts = explode('.', $field, 2);
            
            // Tek alan ise (örn: "sector"), hangi section'a ait olduğunu belirle
            if (count($fieldParts) === 1) {
                $fieldName = $fieldParts[0];
                
                // Field mapping - hangi alan hangi section'a ait
                $fieldToSectionMap = [
                    // Sector details
                    'sector' => 'sector_details',
                    'sector_selection' => 'sector_details',
                    
                    // Company info
                    'brand_name' => 'company_info',
                    'city' => 'company_info',
                    'main_service' => 'company_info',
                    'experience_years' => 'company_info',
                    'contact_info' => 'company_info',
                    'founder_permission' => 'company_info',
                    
                    // Sector details (Step 3)
                    'business_size' => 'sector_details',
                    'target_audience' => 'sector_details',
                    'service_area' => 'sector_details',
                    'brand_voice' => 'sector_details',
                    
                    // Founder info
                    'founder_story' => 'founder_info',
                    'biggest_challenge' => 'founder_info',
                    
                    // Success stories
                    'success_story' => 'success_stories',
                    'customer_testimonial' => 'success_stories',
                    'competitive_advantage' => 'success_stories',
                    
                    // AI behavior rules (Step 5)
                    'ai_response_style' => 'ai_behavior_rules',  // Step 5 - yanıt stili
                    'sales_approach' => 'ai_behavior_rules',     // Step 5 - satış yaklaşımı
                    'response_style' => 'ai_behavior_rules',   // Step 5 - checkbox
                    'forbidden_topics' => 'ai_behavior_rules',
                    'writing_tone' => 'ai_behavior_rules',
                    'brand_voice' => 'ai_behavior_rules',
                    'content_approach' => 'ai_behavior_rules',
                    'emphasis_points' => 'ai_behavior_rules',
                    'avoid_topics' => 'ai_behavior_rules',
                    
                    // Sektöre özel sorular (Step 7) - Teknoloji & Bilişim
                    'tech_specialization' => 'sector_details',
                    'project_duration' => 'sector_details',
                    'support_model' => 'sector_details',
                    
                    // Sektöre özel sorular - Sağlık & Tıp
                    'medical_specialties' => 'sector_details',
                    'appointment_system' => 'sector_details',
                    'insurance_acceptance' => 'sector_details',
                    
                    // Sektöre özel sorular - Eğitim & Öğretim
                    'education_levels' => 'sector_details',
                    'education_format' => 'sector_details',
                    'success_tracking' => 'sector_details',
                    
                    // Sektöre özel sorular - Yiyecek & İçecek
                    'cuisine_type' => 'sector_details',
                    'service_style' => 'sector_details',
                    'special_features' => 'sector_details',
                    
                    // Sektöre özel sorular - E-ticaret & Perakende
                    'product_categories' => 'sector_details',
                    'sales_channels' => 'sector_details',
                    'shipping_payment' => 'sector_details',
                    
                    // Sektöre özel sorular - İnşaat & Emlak
                    'construction_types' => 'sector_details',
                    'project_scale' => 'sector_details',
                    'services_included' => 'sector_details',
                    
                    // Sektöre özel sorular - Finans & Muhasebe
                    'finance_services' => 'sector_details',
                    'client_segments' => 'sector_details',
                    'digital_tools' => 'sector_details',
                    
                    // Sektöre özel sorular - Sanayi & Üretim
                    'production_type' => 'sector_details',
                    'production_capacity' => 'sector_details',
                    'quality_certifications' => 'sector_details',
                ];
                
                if (isset($fieldToSectionMap[$fieldName])) {
                    $section = $fieldToSectionMap[$fieldName];
                    $key = $fieldName;
                } else {
                    throw new \Exception("Unknown field: {$fieldName}. Field mapping gerekli.");
                }
            } else {
                $section = $fieldParts[0];
                $key = $fieldParts[1];
            }
            
            \Log::info('🔧 saveField - Field parsed', [
                'original_field' => $field,
                'section' => $section,
                'key' => $key,
                'value' => $value
            ]);

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
                
                // AI için anlamlı format - tüm alanlar için
                $this->enhanceFieldForAI($field, $key, $value, $sectionData);
            }

            // Profile section'ını kaydet
            $profile->$section = $sectionData;
            
            // Founder permission özel işlemi - hayır seçilirse kurucu bilgilerini temizle
            if (($key === 'founder_permission' || $key === 'share_founder_info') && $section === 'company_info') {
                $shouldShowFounderQuestions = in_array($value, ['Evet, bilgilerimi paylaşmak istiyorum', 'yes_full', 'yes_limited', 'evet']);
                
                if (!$shouldShowFounderQuestions) {
                    // Kurucu bilgilerini veritabanından temizle
                    $profile->founder_info = [];
                    
                    \Log::info('✅ jQuery Auto-Save - Founder info cleared due to permission change', [
                        'tenant_id' => $tenantId,
                        'field' => $key,
                        'permission_value' => $value,
                        'founder_info_cleared' => true
                    ]);
                }
            }
            
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
    
    /**
     * AI için anlamlı format - tüm alanlar için açıklama ekle
     */
    private function enhanceFieldForAI(string $field, string $key, $value, array &$sectionData): void
    {
        if (empty($value)) {
            return;
        }
        
        // Sektör seçimi
        if ($key === 'sector_selection') {
            $sector = \Modules\AI\app\Models\AIProfileSector::findByCode($value);
            if ($sector) {
                $sectionData['sector_selection'] = $value;
                $sectionData['sector_name'] = $sector->name;
                $sectionData['sector_description'] = $sector->description;
                
                \Log::info('✅ Field enhanced for AI: sector_selection', [
                    'code' => $value,
                    'name' => $sector->name,
                    'description' => $sector->description
                ]);
            }
            return;
        }
        
        // Soru bilgilerini al
        $question = \Modules\AI\app\Models\AIProfileQuestion::where('question_key', $key)->first();
        if (!$question) {
            return;
        }
        
        // Select ve radio alanları için seçenek açıklaması
        if (in_array($question->input_type, ['select', 'radio']) && $question->options) {
            $options = is_array($question->options) 
                ? $question->options 
                : json_decode($question->options, true);
                
            if (is_array($options)) {
                foreach ($options as $option) {
                    $optionValue = is_array($option) ? ($option['value'] ?? $option) : $option;
                    $optionLabel = is_array($option) ? ($option['label'] ?? $option) : $option;
                    
                    if ($optionValue === $value) {
                        $sectionData[$key . '_label'] = $optionLabel;
                        $sectionData[$key . '_question'] = $question->question_text;
                        
                        \Log::info('✅ Field enhanced for AI: ' . $key, [
                            'value' => $value,
                            'label' => $optionLabel,
                            'question' => $question->question_text
                        ]);
                        break;
                    }
                }
            }
        }
        
        // Checkbox alanları için seçilen değerlerin açıklaması
        if ($question->input_type === 'checkbox' && is_array($value)) {
            $options = is_array($question->options) 
                ? $question->options 
                : json_decode($question->options, true);
                
            if (is_array($options)) {
                $selectedLabels = [];
                foreach ($value as $selectedValue) {
                    foreach ($options as $option) {
                        $optionValue = is_array($option) ? ($option['value'] ?? $option) : $option;
                        $optionLabel = is_array($option) ? ($option['label'] ?? $option) : $option;
                        
                        if ($optionValue === $selectedValue) {
                            $selectedLabels[] = $optionLabel;
                            break;
                        }
                    }
                }
                
                if (!empty($selectedLabels)) {
                    $sectionData[$key . '_labels'] = $selectedLabels;
                    $sectionData[$key . '_question'] = $question->question_text;
                    
                    \Log::info('✅ Field enhanced for AI: ' . $key, [
                        'values' => $value,
                        'labels' => $selectedLabels,
                        'question' => $question->question_text
                    ]);
                }
            }
        }
        
        // Tüm alanlar için soru metnini ekle
        if (!isset($sectionData[$key . '_question'])) {
            $sectionData[$key . '_question'] = $question->question_text;
        }
    }
}