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
            
            // Profil varsa ve hikaye yoksa oluştur (25% completion yeterli)
            $brandStoryGenerating = false;
            $completionData = $profile->getCompletionPercentage();
            $completionPercentage = round($completionData['percentage']);
            
            if ($profile && !$profile->hasBrandStory() && $completionPercentage >= 25) {
                try {
                    // API anahtarı kontrolü ÖNCE yap
                    $aiSettings = \Modules\AI\App\Models\Setting::first();
                    if (!$aiSettings || empty($aiSettings->api_key)) {
                        \Log::error('Controller - API anahtarı bulunamadı - marka hikayesi oluşturulamadı');
                        session()->flash('brand_story_error', 'API anahtarı bulunamadı. Marka hikayesi oluşturulamadı.');
                    } else {
                        // Sync brand story generation
                        \Log::info('Controller - Brand story generation başlatılıyor - sync');
                        try {
                            $profile->generateBrandStory();
                            session()->flash('brand_story_generated', 'Marka hikayeniz başarıyla oluşturuldu!');
                            $brandStoryGenerating = false;
                        } catch (\Exception $e) {
                            \Log::error('Controller - Brand story generation failed', [
                                'profile_id' => $profile->id,
                                'error' => $e->getMessage()
                            ]);
                            session()->flash('brand_story_error', 'Marka hikayesi oluşturulurken hata: ' . $e->getMessage());
                            $brandStoryGenerating = false;
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Controller - Brand story generation failed', [
                        'profile_id' => $profile->id,
                        'error' => $e->getMessage()
                    ]);
                    session()->flash('brand_story_error', 'Marka hikayesi oluşturulurken bir hata oluştu. Lütfen daha sonra tekrar deneyin.');
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
     * jQuery-based basit profil düzenleme
     */
    public function jqueryEdit($step = 1)
    {
        // Debug: Controller method çalışıyor mu?
        \Log::info('🔥 AIProfileController::jqueryEdit() called', ['step' => $step]);
        
        // Step validation
        $step = max(1, min(5, (int) $step));
        
        // Profil verilerini yükle
        $profile = AITenantProfile::currentOrCreate();
        
        // Profil verilerini flatten et (dot notation)
        $profileData = [];
        
        if ($profile && $profile->exists) {
            // Company info
            if ($profile->company_info) {
                foreach ($profile->company_info as $key => $value) {
                    if (is_array($value)) {
                        // Array'leri preserve et (checkbox/select options için)
                        $profileData[$key] = $value;
                        foreach ($value as $subKey => $subValue) {
                            $profileData["company_info.{$key}.{$subKey}"] = $subValue;
                        }
                    } else {
                        $profileData["company_info.{$key}"] = $value;
                        $profileData[$key] = $value; // Direct access
                    }
                }
            }
            
            // Sector details
            if ($profile->sector_details) {
                foreach ($profile->sector_details as $key => $value) {
                    if (is_array($value)) {
                        // Array'leri preserve et (checkbox/select options için)
                        $profileData[$key] = $value;
                        foreach ($value as $subKey => $subValue) {
                            $profileData["sector_details.{$key}.{$subKey}"] = $subValue;
                        }
                    } else {
                        $profileData["sector_details.{$key}"] = $value;
                        $profileData[$key] = $value; // Direct access
                    }
                }
            }
            
            // Success stories
            if ($profile->success_stories) {
                foreach ($profile->success_stories as $key => $value) {
                    if (is_array($value)) {
                        // Array'leri preserve et (checkbox/select options için)
                        $profileData[$key] = $value;
                        foreach ($value as $subKey => $subValue) {
                            $profileData["success_stories.{$key}.{$subKey}"] = $subValue;
                        }
                    } else {
                        $profileData["success_stories.{$key}"] = $value;
                        $profileData[$key] = $value; // Direct access
                    }
                }
            }
            
            // AI behavior rules
            if ($profile->ai_behavior_rules) {
                foreach ($profile->ai_behavior_rules as $key => $value) {
                    if (is_array($value)) {
                        // Array'leri preserve et (checkbox/select options için)
                        $profileData[$key] = $value;
                        foreach ($value as $subKey => $subValue) {
                            $profileData["ai_behavior_rules.{$key}.{$subKey}"] = $subValue;
                        }
                    } else {
                        $profileData["ai_behavior_rules.{$key}"] = $value;
                        $profileData[$key] = $value; // Direct access
                    }
                }
            }
            
            // Founder info
            if ($profile->founder_info) {
                foreach ($profile->founder_info as $key => $value) {
                    if (is_array($value)) {
                        // Array'leri preserve et (checkbox/select options için)
                        $profileData[$key] = $value;
                        foreach ($value as $subKey => $subValue) {
                            $profileData["founder_info.{$key}.{$subKey}"] = $subValue;
                        }
                    } else {
                        $profileData["founder_info.{$key}"] = $value;
                        $profileData[$key] = $value; // Direct access
                    }
                }
            }
        }
        
        // Sektör bilgilerini al - hem sector hem sector_selection key'lerini kontrol et
        $sectorCode = $profileData['sector_selection'] ?? $profileData['sector'] ?? null;
        $selectedSector = null;
        if ($sectorCode) {
            $selectedSector = \Modules\AI\app\Models\AIProfileSector::where('code', $sectorCode)->first();
        }
        
        // Soruları step'e göre çek
        $questions = \Modules\AI\app\Models\AIProfileQuestion::getByStep($step, $sectorCode);
        
        // Sektörler listesi (step 1 için)
        $sectors = [];
        if ($step == 1) {
            $sectors = \Modules\AI\app\Models\AIProfileSector::getCategorizedSectors();
        }
        
        // Progress yüzdesini hesapla - sadece edit sayfalarındaki sorulara göre
        $completionData = $profile->getEditPageCompletionPercentage();
        $completionPercentage = $completionData['percentage'];
        
        \Log::info('🔍 AIProfileController - Progress calculated', [
            'step' => $step,
            'completion_percentage' => $completionPercentage,
            'completed_fields' => $completionData['completed'],
            'total_fields' => $completionData['total'],
            'steps' => array_map(fn($s) => $s['completed'] . '/' . $s['total'], $completionData['steps'])
        ]);
        
        return view('ai::admin.profile.jquery-edit', [
            'initialStep' => $step,
            'totalSteps' => 5,
            'questions' => $questions,
            'sectors' => $sectors,
            'profileData' => $profileData,
            'sectorCode' => $sectorCode,
            'selectedSector' => $selectedSector,
            'completionPercentage' => $completionPercentage,
            'showFounderQuestions' => in_array($profileData['founder_permission'] ?? '', ['Evet, bilgilerimi paylaşmak istiyorum', 'yes_full', 'yes_limited', 'evet'])
                || in_array($profileData['share_founder_info'] ?? '', ['Evet, bilgilerimi paylaşmak istiyorum', 'yes_full', 'yes_limited', 'evet'])
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
            
            // Hikaye oluşturma için minimum %25 completion gerekli
            $completionData = $profile->getCompletionPercentage();
            $completionPercentage = $completionData['percentage'] ?? 0;
            
            if ($completionPercentage < 25) {
                return response()->json([
                    'success' => false,
                    'message' => 'Profil henüz tamamlanmamış. Marka hikayesi oluşturmak için profilin en az %25 tamamlanması gerekiyor. Şu anki tamamlanma oranı: %' . number_format($completionPercentage, 1)
                ]);
            }
            
            // Yeniden oluşturma isteği kontrolü
            $regenerate = $request->input('regenerate', false);
            
            // Mevcut hikaye kontrol et
            if (!empty($profile->brand_story) && !$regenerate) {
                // Hikaye zaten var, tekrar oluşturma
                return response()->json([
                    'success' => true,
                    'message' => 'Marka hikayeniz zaten mevcut!',
                    'story' => $profile->brand_story,
                    'created_at' => $profile->brand_story_created_at ? $profile->brand_story_created_at->format('d.m.Y H:i') : 'Bilinmiyor'
                ]);
            }
            
            // Yeniden oluşturma istendiyse mevcut hikayeyi sil
            if ($regenerate) {
                $profile->brand_story = null;
                $profile->brand_story_created_at = null;
                \Log::info('Controller - Brand story regeneration başlatılıyor - mevcut hikaye silindi');
            }
            
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
                    
                    // Company info - Step 2 temel bilgiler
                    'brand_name' => 'company_info',
                    'city' => 'company_info',
                    'business_start_year' => 'company_info',  // Step 2 - YENİ
                    'main_service' => 'company_info',
                    'experience_years' => 'company_info',
                    'contact_info' => 'company_info',
                    'founder_permission' => 'company_info',
                    'share_founder_info' => 'company_info',  // Step 4 - kurucu izni - YENİ
                    
                    // Company info - Step 3 ana iş bilgileri
                    'main_business_activities' => 'company_info',  // Step 3 - YENİ
                    'main_business_activities_custom' => 'company_info',  // Step 3 - YENİ (ek hizmetler)
                    'target_customers' => 'company_info',  // Step 3 - YENİ
                    'target_customers_custom_custom' => 'company_info',  // Step 3 - custom input
                    'web_specific_services_diger_custom' => 'sector_details',  // Step 3 - web sektör custom
                    'web_specific_services' => 'sector_details',  // Step 3 - web sektör checkbox
                    
                    // Sector details (Step 3) - Ortak sektörel sorular
                    'service_areas' => 'sector_details',  // Section: hizmet_alanlari - YENİ
                    'working_hours' => 'sector_details',  // Section: calisma_saatleri - YENİ
                    'payment_options' => 'sector_details',  // Section: odeme_secenekleri - YENİ
                    'special_services' => 'sector_details',  // Section: ozel_hizmetler - YENİ
                    'customer_profile' => 'sector_details',  // Section: musteri_profili - YENİ
                    'expertise_areas' => 'sector_details',  // Section: deneyim_uzmanlik - YENİ
                    'business_capacity' => 'sector_details',  // Section: is_kapasitesi - YENİ
                    'communication_channels' => 'sector_details',  // Section: iletisim_kanallari - YENİ
                    
                    // Sector details (Step 3) - Genel
                    'business_size' => 'sector_details',
                    'target_audience' => 'sector_details',
                    'service_area' => 'sector_details',
                    'brand_voice' => 'sector_details',
                    
                    // Founder info (Step 4)
                    'founder_name' => 'founder_info',               // Step 4 - kurucu adı - YENİ
                    'founder_role' => 'founder_info',               // Step 4 - kurucu rolü - YENİ
                    'founder_additional_info' => 'founder_info',    // Step 4 - kurucu ek bilgi - YENİ
                    'founder_story' => 'founder_info',
                    'biggest_challenge' => 'founder_info',
                    
                    // Success stories
                    'success_story' => 'success_stories',
                    'customer_testimonial' => 'success_stories',
                    'competitive_advantage' => 'success_stories',
                    
                    // AI behavior rules (Step 5)
                    'brand_character' => 'ai_behavior_rules',   // Step 5 - marka karakteri - YENİ
                    'writing_style' => 'ai_behavior_rules',     // Step 5 - yazım tavrı - YENİ
                    'ai_response_style' => 'ai_behavior_rules',  // Step 5 - yanıt stili
                    'sales_approach' => 'ai_behavior_rules',     // Step 5 - satış yaklaşımı
                    'response_style' => 'ai_behavior_rules',   // Step 5 - checkbox
                    'forbidden_topics' => 'ai_behavior_rules',
                    'writing_tone' => 'ai_behavior_rules',
                    'brand_voice' => 'ai_behavior_rules',
                    'content_approach' => 'ai_behavior_rules',
                    'emphasis_points' => 'ai_behavior_rules',
                    'avoid_topics' => 'ai_behavior_rules',
                    
                    // Sektöre özel hizmet soruları (Step 3) - Ana kategoriler
                    'technology_specific_services' => 'sector_details',     // Teknoloji
                    'web_specific_services' => 'sector_details',            // Web Tasarım
                    'health_specific_services' => 'sector_details',         // Sağlık
                    'education_specific_services' => 'sector_details',      // Eğitim
                    'food_specific_services' => 'sector_details',           // Yiyecek-İçecek
                    'retail_specific_services' => 'sector_details',         // E-ticaret/Perakende
                    'construction_specific_services' => 'sector_details',   // İnşaat/Emlak
                    'finance_specific_services' => 'sector_details',        // Finans/Muhasebe
                    'art_design_specific_services' => 'sector_details',     // Sanat/Tasarım
                    'sports_specific_services' => 'sector_details',         // Spor/Fitness
                    'automotive_specific_services' => 'sector_details',     // Otomotiv
                    'legal_specific_services' => 'sector_details',          // Hukuk/Danışmanlık
                    
                    // Sektöre özel detaylı hizmet soruları (Step 3)
                    'tech_specific_services' => 'sector_details',           // Teknoloji detay
                    'web_project_types' => 'sector_details',                // Web proje türleri
                    'mobile_platforms_detailed' => 'sector_details',        // Mobil platform detay
                    'hospital_departments_detailed' => 'sector_details',    // Hastane bölümleri
                    'dental_treatments_detailed' => 'sector_details',       // Diş tedavileri
                    'education_programs' => 'sector_details',               // Eğitim programları
                    'education_levels_detailed' => 'sector_details',        // Eğitim seviyesi detay
                    'languages_offered_detailed' => 'sector_details',       // Dil kursları
                    'software_specific_services' => 'sector_details',       // Yazılım hizmetleri
                    'cybersecurity_specific_services' => 'sector_details',  // Siber güvenlik
                    'digital_marketing_specific_services' => 'sector_details', // Dijital pazarlama
                    'advertising_specific_services' => 'sector_details',    // Reklam hizmetleri
                    'consulting_specific_services' => 'sector_details',     // Danışmanlık
                    'accounting_specific_services' => 'sector_details',     // Muhasebe
                    'ecommerce_specific_services' => 'sector_details',      // E-ticaret
                    'fitness_specific_services' => 'sector_details',        // Fitness
                    'beauty_specific_services' => 'sector_details',         // Güzellik
                    'organic_food_certifications' => 'sector_details',      // Organik gıda
                    'street_food_specialties' => 'sector_details',          // Sokak yemeği
                    'restaurant_cuisine_types' => 'sector_details',         // Restoran mutfağı
                    'cafe_services' => 'sector_details',                    // Kafe hizmetleri
                    'beauty_product_categories' => 'sector_details',        // Güzellik ürünleri
                    'baby_kids_categories' => 'sector_details',             // Bebek/çocuk
                    'construction_project_types' => 'sector_details',       // İnşaat proje türleri
                    'finance_client_segments' => 'sector_details',          // Finans müşteri segmenti
                    'legal_service_types' => 'sector_details',              // Hukuk hizmet türleri
                    'art_design_specialization' => 'sector_details',        // Sanat/tasarım uzmanlık
                    'cleaning_service_types' => 'sector_details',           // Temizlik hizmetleri
                    'wedding_dress_services' => 'sector_details',           // Gelinlik hizmetleri
                    'technology_main_service_detailed' => 'sector_details', // Teknoloji ana hizmet
                    
                    // Legacy eski field'lar (geriye uyumluluk)
                    'tech_specialization' => 'sector_details',
                    'project_duration' => 'sector_details',
                    'support_model' => 'sector_details',
                    'medical_specialties' => 'sector_details',
                    'appointment_system' => 'sector_details',
                    'insurance_acceptance' => 'sector_details',
                    'education_levels' => 'sector_details',
                    'education_format' => 'sector_details',
                    'success_tracking' => 'sector_details',
                    'cuisine_type' => 'sector_details',
                    'service_style' => 'sector_details',
                    'special_features' => 'sector_details',
                    'product_categories' => 'sector_details',
                    'sales_channels' => 'sector_details',
                    'shipping_payment' => 'sector_details',
                    'construction_types' => 'sector_details',
                    'project_scale' => 'sector_details',
                    'services_included' => 'sector_details',
                    'finance_services' => 'sector_details',
                    'client_segments' => 'sector_details',
                    'digital_tools' => 'sector_details',
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

            // Sektör değişikliği kontrolü - ÖNCE mevcut sektörü kaydet
            $previousSector = null;
            if ($key === 'sector' && $section === 'sector_details') {
                $previousSector = $profile->sector_details['sector'] ?? null;
                \Log::info('Sektör değişikliği tespit edildi', [
                    'previous_sector' => $previousSector,
                    'new_sector' => $value,
                    'tenant_id' => $tenantId
                ]);
            }

            // Profile section'ını kaydet
            $profile->$section = $sectionData;
            
            // Sektör değişikliği işlemi - SONRA temizle
            if ($key === 'sector' && $section === 'sector_details' && $previousSector && $previousSector !== $value) {
                \Log::info('Sektör değişti - eski verileri temizleniyor', [
                    'from' => $previousSector,
                    'to' => $value,
                    'tenant_id' => $tenantId
                ]);
                
                // Eski sektör verilerini temizle
                $profile->clearSectorRelatedData();
                
                // Yeni sektörü tekrar set et (clearSectorRelatedData sadece sector'u korur)
                $sectorData = $profile->sector_details ?? [];
                $sectorData['sector'] = $value;
                $profile->sector_details = $sectorData;
                
                \Log::info('Sektör değişimi tamamlandı', [
                    'new_sector' => $value,
                    'tenant_id' => $tenantId
                ]);
            }
            
            // Founder permission özel işlemi - hayır seçilirse kurucu bilgilerini temizle
            if (($key === 'founder_permission' || $key === 'share_founder_info') && $section === 'company_info') {
                $shouldShowFounderQuestions = in_array($value, [
                    'Evet, bilgilerimi paylaşmak istiyorum', 
                    'yes_full', 
                    'yes_limited', 
                    'evet'
                ]);
                
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

            // Progress yüzdesini yeniden hesapla (PHP tarafında) - sadece edit sorularına göre
            $completionData = $profile->getEditPageCompletionPercentage();
            $completionPercentage = $completionData['percentage'];

            \Log::info('✅ jQuery Auto-Save - Field saved', [
                'tenant_id' => $tenantId,
                'field' => $field,
                'section' => $section,
                'key' => $key,
                'value' => is_array($value) ? json_encode($value) : $value,
                'step' => $step,
                'completion_percentage' => $completionPercentage
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Field başarıyla kaydedildi',
                'field' => $field,
                'value' => $value,
                'completion_percentage' => $completionPercentage
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

    /**
     * jQuery için adım sorularını getir
     */
    public function getQuestions($step, Request $request)
    {
        \Log::info('🔧 AIProfileController - getQuestions called', [
            'step' => $step,
            'sector_code' => $request->input('sector_code')
        ]);
        
        try {
            $sectorCode = $request->input('sector_code');
            
            // Soruları step'e göre çek
            $questions = \Modules\AI\app\Models\AIProfileQuestion::getByStep($step, $sectorCode);
            
            \Log::info('✅ Questions loaded', [
                'step' => $step,
                'questions_count' => $questions->count(),
                'sector_code' => $sectorCode
            ]);
            
            // Sektörler listesi (step 1 için)
            $sectors = [];
            if ($step == 1) {
                $sectors = \Modules\AI\app\Models\AIProfileSector::getCategorizedSectors();
                \Log::info('✅ Sectors loaded', [
                    'sectors_count' => $sectors->count()
                ]);
            }
            
            return response()->json([
                'success' => true,
                'questions' => $questions,
                'sectors' => $sectors,
                'step' => $step
            ]);
            
        } catch (\Exception $e) {
            \Log::error('❌ AIProfileController - getQuestions error', [
                'error' => $e->getMessage(),
                'step' => $step,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Sorular yüklenirken hata: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * jQuery için mevcut profil verilerini getir
     */
    public function getProfileData(Request $request)
    {
        try {
            $profile = AITenantProfile::currentOrCreate();
            
            // Profil verilerini flatten et (dot notation)
            $profileData = [];
            
            if ($profile && $profile->exists) {
                // Company info
                if ($profile->company_info) {
                    foreach ($profile->company_info as $key => $value) {
                        if (is_array($value)) {
                            // Array'leri preserve et (checkbox/select options için)
                            $profileData[$key] = $value;
                            foreach ($value as $subKey => $subValue) {
                                $profileData["company_info.{$key}.{$subKey}"] = $subValue;
                            }
                        } else {
                            $profileData["company_info.{$key}"] = $value;
                            $profileData[$key] = $value; // Direct access
                        }
                    }
                }
                
                // Sector details
                if ($profile->sector_details) {
                    foreach ($profile->sector_details as $key => $value) {
                        if (is_array($value)) {
                            // Array'leri preserve et (checkbox/select options için)
                            $profileData[$key] = $value;
                            foreach ($value as $subKey => $subValue) {
                                $profileData["sector_details.{$key}.{$subKey}"] = $subValue;
                            }
                        } else {
                            $profileData["sector_details.{$key}"] = $value;
                            $profileData[$key] = $value; // Direct access
                        }
                    }
                }
                
                // Success stories
                if ($profile->success_stories) {
                    foreach ($profile->success_stories as $key => $value) {
                        if (is_array($value)) {
                            // Array'leri preserve et (checkbox/select options için)
                            $profileData[$key] = $value;
                            foreach ($value as $subKey => $subValue) {
                                $profileData["success_stories.{$key}.{$subKey}"] = $subValue;
                            }
                        } else {
                            $profileData["success_stories.{$key}"] = $value;
                            $profileData[$key] = $value; // Direct access
                        }
                    }
                }
                
                // AI behavior rules
                if ($profile->ai_behavior_rules) {
                    foreach ($profile->ai_behavior_rules as $key => $value) {
                        if (is_array($value)) {
                            // Array'leri preserve et (checkbox/select options için)
                            $profileData[$key] = $value;
                            foreach ($value as $subKey => $subValue) {
                                $profileData["ai_behavior_rules.{$key}.{$subKey}"] = $subValue;
                            }
                        } else {
                            $profileData["ai_behavior_rules.{$key}"] = $value;
                            $profileData[$key] = $value; // Direct access
                        }
                    }
                }
                
                // Founder info
                if ($profile->founder_info) {
                    foreach ($profile->founder_info as $key => $value) {
                        if (is_array($value)) {
                            // Array'leri preserve et (checkbox/select options için)
                            $profileData[$key] = $value;
                            foreach ($value as $subKey => $subValue) {
                                $profileData["founder_info.{$key}.{$subKey}"] = $subValue;
                            }
                        } else {
                            $profileData["founder_info.{$key}"] = $value;
                            $profileData[$key] = $value; // Direct access
                        }
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'profile_data' => $profileData,
                'sector_code' => $profileData['sector_selection'] ?? $profileData['sector'] ?? null,
                'show_founder_questions' => in_array($profileData['founder_permission'] ?? '', ['Evet, bilgilerimi paylaşmak istiyorum', 'yes_full', 'yes_limited', 'evet']) 
                    || in_array($profileData['share_founder_info'] ?? '', ['Evet, bilgilerimi paylaşmak istiyorum', 'yes_full', 'yes_limited', 'evet'])
            ]);
            
        } catch (\Exception $e) {
            \Log::error('AIProfileController - getProfileData error', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Profil verileri yüklenirken hata: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Form POST işlemi için update metodu
     */
    public function update(Request $request, $step = null)
    {
        \Log::info('🔧 AIProfileController - update called', [
            'step' => $step,
            'request_data' => $request->all()
        ]);

        // Livewire bileşeni ile aynı sayfaya redirect
        return redirect()->route('admin.ai.profile.edit', ['step' => $step])
                        ->with('success', 'Form gönderildi');
    }

    /**
     * Dashboard mini AI chat endpoint
     */
    public function chat(Request $request)
    {
        try {
            $message = $request->input('message');
            
            if (empty($message)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mesaj boş olamaz'
                ]);
            }

            // AI Service'i kullanarak yanıt al
            $aiService = app(\Modules\AI\app\Services\AIService::class);
            
            // Marka bilgisini al
            $profile = \Modules\AI\app\Models\AITenantProfile::currentOrCreate();
            $brandName = $profile->company_info['business_name'] ?? 'Türk Bilişim';
            
            // Dashboard chat için özel context
            $systemPrompt = "Sen {$brandName} firmasının AI asistanısın. Kullanıcıların tüm sorularına yardımcı ol - teknik, iş, genel sorular dahil. Kısa ve net yanıtlar ver. Türkçe yanıt ver.";
            
            $response = $aiService->ask($message, [
                'system_prompt' => $systemPrompt,
                'type' => 'dashboard_chat',
                'max_tokens' => 150 // Kısa yanıtlar için
            ]);

            return response()->json([
                'success' => true,
                'response' => $response
            ]);

        } catch (\Exception $e) {
            \Log::error('Dashboard AI Chat Error', [
                'error' => $e->getMessage(),
                'message' => $request->input('message')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'AI servisinde bir hata oluştu: ' . $e->getMessage()
            ]);
        }
    }
}