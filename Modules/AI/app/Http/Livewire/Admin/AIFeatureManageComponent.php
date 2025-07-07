<?php

namespace Modules\AI\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Modules\AI\App\Models\AIFeature;
use Modules\AI\App\Models\AIFeaturePrompt;
use Modules\AI\App\Models\Prompt;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

#[Layout('admin.layout')]
class AIFeatureManageComponent extends Component
{
    public $featureId;
    
    // Temel Bilgiler
    public $inputs = [
        'name' => '',
        'slug' => '',
        'description' => '',
        'emoji' => '🤖',
        'icon' => 'fas fa-robot',
        'category' => '',
        'complexity_level' => 'intermediate',
        'status' => 'active',
        'sort_order' => 999,
        'badge_color' => 'primary',
        'input_placeholder' => '',
        'helper_function' => '',
        'response_length' => 'medium',
        'response_format' => 'text',
        'button_text' => 'Generate',
        'is_featured' => false,
        'show_in_examples' => true,
        'requires_input' => true,
        'is_system' => false,
        'hybrid_system_type' => 'simple',
        'has_custom_prompt' => false,
        'has_related_prompts' => false
    ];
    
    // JSON Alanları - Sortable ile yönetilebilir, seeder'dan gelen gerçek verilerle
    public $jsonFields = [
        'additional_config' => [
            'max_tokens' => 1000,
            'temperature' => 0.7,
            'preprocessing' => true
        ],
        'usage_examples' => [
            [
                'input' => 'Basit metin girişi örneği',
                'output' => 'Beklenen çıktı formatı',
                'description' => 'Yeni başlayanlar için örnek'
            ]
        ],
        'input_validation' => [
            'required' => true,
            'min_length' => 10,
            'max_length' => 5000
        ],
        'settings' => [
            'auto_save' => true,
            'enable_history' => true,
            'performance_mode' => 'balanced'
        ],
        'error_messages' => [
            'insufficient_tokens' => 'Yeterli token bulunmamaktadır',
            'invalid_input' => 'Geçersiz giriş formatı'
        ],
        'success_messages' => [
            'content_generated' => 'İçerik başarıyla oluşturuldu',
            'analysis_completed' => 'Analiz tamamlandı'
        ],
        'token_cost' => [
            'base_cost' => 100,
            'per_word_cost' => 2,
            'estimated_range' => ['min' => 50, 'max' => 500]
        ],
        'example_inputs' => [
            [
                'text' => 'Ankara\'da faaliyet gösteren inşaat firmamız villa, apartman projeleri gerçekleştiriyor.',
                'label' => 'İnşaat Firması'
            ]
        ],
        'helper_examples' => [
            'basic' => [
                'code' => 'ai_feature_function("örnek parametre")',
                'description' => 'Temel kullanım örneği',
                'estimated_tokens' => 300
            ]
        ],
        'helper_parameters' => [
            'text' => 'Ana metin parametresi',
            'options' => [
                'tone' => 'Yazım tonu (professional, friendly)',
                'length' => 'İçerik uzunluğu (short, medium, long)'
            ]
        ],
        'helper_returns' => [
            'success' => 'Başarılı işlem sonucu',
            'content' => 'Üretilen içerik',
            'metadata' => ['token_used' => 'Kullanılan token sayısı']
        ],
        'response_template' => [
            'sections' => [
                'BAŞLIK: Ana başlık formatı',
                'İÇERİK: Ana içerik bölümü',
                'SONUÇ: Özet ve sonuç'
            ],
            'format' => 'structured_text',
            'scoring' => true
        ]
    ];
    
    // Prompt Alanları
    public $customPrompt = '';
    public $quickPrompt = '';
    
    // Helper Bilgileri
    public $helperDescription = '';
    
    // Feature Prompt İlişkileri
    public $featurePrompts = [];
    public $availablePrompts = [];
    
    // UI State
    public $activeTab = 'basic';
    public $expandedSections = [];

    public function mount($id = null)
    {
        $this->loadAvailablePrompts();
        
        // Varsayılan expanded sections
        $this->expandedSections = [
            'basic_info' => true,
            'prompts' => false,
            'json_fields' => false,
            'helper_system' => false
        ];
        
        if ($id) {
            $this->featureId = $id;
            $this->loadFeature($id);
        } else {
            $this->initializeEmpty();
        }
    }


    protected function loadAvailablePrompts()
    {
        $this->availablePrompts = Prompt::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function ($prompt) {
                return [
                    'id' => $prompt->id,
                    'name' => $prompt->name,
                    'category' => $prompt->prompt_type ?? 'standard',
                    'description' => Str::limit($prompt->content ?? '', 100),
                    'prompt_type' => $prompt->prompt_type ?? 'standard'
                ];
            })->toArray();
    }

    protected function loadFeature($id)
    {
        $feature = AIFeature::with(['prompts', 'featurePrompts.aiPrompt'])->findOrFail($id);
        
        // Temel bilgileri doldur
        $this->inputs = $feature->only([
            'name', 'slug', 'description', 'emoji', 'icon', 'category',
            'complexity_level', 'status', 'sort_order', 'badge_color',
            'input_placeholder', 'helper_function', 'response_length',
            'response_format', 'button_text', 'is_featured', 'show_in_examples',
            'requires_input', 'is_system', 'hybrid_system_type',
            'has_custom_prompt', 'has_related_prompts'
        ]);
        
        // JSON alanları - RAW veritabanı değerlerini al (cast'ler olmadan)
        $rawFeature = \DB::table('ai_features')->where('id', $feature->id)->first();
        
        $jsonFieldNames = [
            'additional_config', 'usage_examples', 'input_validation', 'settings',
            'error_messages', 'success_messages', 'token_cost', 'example_inputs',
            'helper_examples', 'helper_parameters', 'helper_returns', 'response_template'
        ];
        
        foreach ($jsonFieldNames as $field) {
            $value = $rawFeature->$field ?? null;
            \Log::info("Loading JSON field '{$field}'", [
                'raw_value' => substr($value ?? 'null', 0, 100),
                'type' => gettype($value),
                'is_string' => is_string($value),
                'is_array' => is_array($value),
                'length' => is_string($value) ? strlen($value) : 'n/a'
            ]);
            
            if (is_string($value) && !empty($value)) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $this->jsonFields[$field] = $decoded;
                    \Log::info("Successfully decoded JSON for '{$field}'", [
                        'decoded_type' => gettype($decoded),
                        'decoded_count' => is_array($decoded) ? count($decoded) : 'n/a',
                        'keys' => is_array($decoded) ? array_keys($decoded) : 'n/a'
                    ]);
                } else {
                    \Log::warning("JSON decode failed for '{$field}'", [
                        'error' => json_last_error_msg(),
                        'sample' => substr($value, 0, 200)
                    ]);
                    // JSON parse hatası varsa raw string'i store et, blade'de tekrar deneriz
                    $this->jsonFields[$field] = $value;
                }
            } else {
                // Null/empty fields için empty array
                $this->jsonFields[$field] = [];
                \Log::info("Setting empty array for '{$field}' (null/empty value)");
            }
        }
        
        // Prompt alanları
        $this->customPrompt = $feature->custom_prompt ?? '';
        $this->quickPrompt = $feature->quick_prompt ?? '';
        $this->helperDescription = $feature->helper_description ?? '';
        
        // Feature Prompt ilişkilerini yükle
        $this->featurePrompts = $feature->featurePrompts->map(function ($fp) {
            return [
                'id' => $fp->id,
                'prompt_id' => $fp->prompt_id,
                'prompt_name' => $fp->aiPrompt->name ?? 'Unknown',
                'role' => $fp->role,
                'priority' => $fp->priority,
                'is_active' => $fp->is_active,
                'conditions' => is_array($fp->conditions) ? $fp->conditions : [],
                'notes' => $fp->notes ?? ''
            ];
        })->sortBy('priority')->values()->toArray();
    }

    protected function initializeEmpty()
    {
        // Varsayılan JSON değerlerini temizle - yeni feature için boş başla
        foreach ($this->jsonFields as $field => $defaultValue) {
            $this->jsonFields[$field] = [];
        }
        
        $this->featurePrompts = [];
        
        // Yeni feature için sort_order'ı hesapla
        $this->inputs['sort_order'] = (AIFeature::max('sort_order') ?? 0) + 1;
    }


    public function toggleSection($section)
    {
        $this->expandedSections[$section] = !($this->expandedSections[$section] ?? false);
    }

    // JSON Field Management - Sortable destekli
    public function addJsonItem($field, $key = null, $value = null)
    {
        if (!isset($this->jsonFields[$field])) {
            $this->jsonFields[$field] = [];
        }
        
        if ($key !== null) {
            $this->jsonFields[$field][$key] = $value ?? '';
        } else {
            $this->jsonFields[$field][] = $value ?? '';
        }
    }

    public function removeJsonItem($field, $key)
    {
        if (isset($this->jsonFields[$field][$key])) {
            unset($this->jsonFields[$field][$key]);
            $this->jsonFields[$field] = array_values($this->jsonFields[$field]);
        }
    }

    public function updateJsonItem($field, $key, $value)
    {
        if (isset($this->jsonFields[$field])) {
            $this->jsonFields[$field][$key] = $value;
        }
    }

    public function sortJsonItems($field, $orderedIds)
    {
        if (!isset($this->jsonFields[$field])) return;
        
        $sortedItems = [];
        foreach ($orderedIds as $index) {
            if (isset($this->jsonFields[$field][$index])) {
                $sortedItems[] = $this->jsonFields[$field][$index];
            }
        }
        $this->jsonFields[$field] = $sortedItems;
    }

    // Feature Prompt Management - Priority ile sortable
    public function addFeaturePrompt()
    {
        $this->featurePrompts[] = [
            'id' => null,
            'prompt_id' => '',
            'prompt_name' => '',
            'role' => 'primary',
            'priority' => count($this->featurePrompts) + 1,
            'is_active' => true,
            'conditions' => [],
            'notes' => ''
        ];
    }

    public function removeFeaturePrompt($index)
    {
        if (isset($this->featurePrompts[$index])) {
            // Eğer ID varsa, veritabanından da sil
            if ($this->featurePrompts[$index]['id']) {
                AIFeaturePrompt::find($this->featurePrompts[$index]['id'])?->delete();
            }
            
            unset($this->featurePrompts[$index]);
            $this->featurePrompts = array_values($this->featurePrompts);
            
            // Priority'leri yeniden düzenle
            foreach ($this->featurePrompts as $idx => $prompt) {
                $this->featurePrompts[$idx]['priority'] = $idx + 1;
            }
        }
    }

    public function updatePromptName($index)
    {
        if (isset($this->featurePrompts[$index])) {
            $promptId = $this->featurePrompts[$index]['prompt_id'];
            $prompt = collect($this->availablePrompts)->firstWhere('id', $promptId);
            
            if ($prompt) {
                $this->featurePrompts[$index]['prompt_name'] = $prompt['name'];
            }
        }
    }

    public function sortFeaturePrompts($orderedIds)
    {
        $sortedPrompts = [];
        foreach ($orderedIds as $priority => $index) {
            if (isset($this->featurePrompts[$index])) {
                $this->featurePrompts[$index]['priority'] = $priority + 1;
                $sortedPrompts[] = $this->featurePrompts[$index];
            }
        }
        $this->featurePrompts = $sortedPrompts;
    }

    // Auto-generate slug from name
    public function updatedInputsName($value)
    {
        if (!$this->inputs['is_system'] && empty($this->inputs['slug'])) {
            $this->inputs['slug'] = Str::slug($value);
        }
    }

    protected function rules()
    {
        $rules = [
            'inputs.name' => 'required|string|min:3|max:255',
            'inputs.slug' => 'required|string|max:255|unique:ai_features,slug,' . ($this->featureId ?? 'NULL'),
            'inputs.description' => 'required|string|min:10',
            'inputs.category' => 'required|string',
            'inputs.complexity_level' => 'required|string',
            'inputs.status' => 'required|in:active,inactive,beta,planned',
            'inputs.sort_order' => 'nullable|integer|min:1',
            'inputs.response_length' => 'required|in:short,medium,long,variable',
            'inputs.response_format' => 'required|in:text,markdown,structured,code,list',
            'customPrompt' => 'nullable|string',
            'quickPrompt' => 'nullable|string',
            'helperDescription' => 'nullable|string'
        ];

        // JSON field validation
        foreach ($this->jsonFields as $field => $value) {
            $rules["jsonFields.{$field}"] = 'nullable|array';
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'inputs.name.required' => __('ai::admin.feature_name_required'),
            'inputs.name.min' => 'Feature adı en az 3 karakter olmalıdır',
            'inputs.slug.required' => __('ai::admin.slug_required'),
            'inputs.slug.unique' => 'Bu slug zaten kullanılmaktadır',
            'inputs.description.required' => __('ai::admin.feature_description_required'),
            'inputs.description.min' => 'Açıklama en az 10 karakter olmalıdır',
            'inputs.category.required' => __('ai::admin.feature_category_required'),
        ];
    }

    public function save($formData = [], $redirect = false)
    {
        try {
            // Form data'dan basic fields al
            $basicFields = collect($formData)->except([
                'example_inputs', 'helper_examples', 'helper_parameters', 'helper_returns',
                'response_template', 'settings', 'usage_examples', 'additional_config',
                'input_validation', 'error_messages', 'success_messages', 'token_cost'
            ])->toArray();
            
            // JSON fields'ları al
            $jsonData = [];
            $jsonFieldNames = [
                'example_inputs', 'helper_examples', 'helper_parameters', 'helper_returns',
                'response_template', 'settings', 'usage_examples', 'additional_config',
                'input_validation', 'error_messages', 'success_messages', 'token_cost'
            ];
            
            foreach ($jsonFieldNames as $field) {
                if (isset($formData[$field])) {
                    $jsonData[$field] = $formData[$field];
                }
            }
            
            $data = array_merge($basicFields, $jsonData);
            
            if ($this->featureId) {
                $feature = AIFeature::findOrFail($this->featureId);
                $feature->update($data);
                $message = 'AI Feature başarıyla güncellendi!';
            } else {
                $feature = AIFeature::create($data);
                $this->featureId = $feature->id;
                $message = 'AI Feature başarıyla oluşturuldu!';
            }
            
            session()->flash('success', $message);
            
            if ($redirect) {
                return redirect()->route('admin.ai.features.index');
            } else {
                return redirect()->route('admin.ai.features.manage', $feature->id);
            }
            
        } catch (\Exception $e) {
            Log::error('AI Feature save error: ' . $e->getMessage(), [
                'form_data' => $formData,
                'feature_id' => $this->featureId
            ]);
            
            session()->flash('error', 'Kaydetme sırasında hata oluştu: ' . $e->getMessage());
            return back();
        }
    }

    protected function saveFeaturePrompts($feature)
    {
        // Mevcut ilişkileri temizle
        $feature->featurePrompts()->delete();
        
        // Yeni ilişkileri kaydet
        foreach ($this->featurePrompts as $fp) {
            if (!empty($fp['prompt_id'])) {
                AIFeaturePrompt::create([
                    'feature_id' => $feature->id,
                    'prompt_id' => $fp['prompt_id'],
                    'role' => $fp['role'],
                    'priority' => $fp['priority'],
                    'is_active' => $fp['is_active'],
                    'conditions' => $fp['conditions'] ?? [],
                    'notes' => $fp['notes']
                ]);
            }
        }
    }

    public function delete()
    {
        if (!$this->featureId) return;
        
        $feature = AIFeature::find($this->featureId);
        
        if (!$feature) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Feature bulunamadı',
                'type' => 'error'
            ]);
            return;
        }
        
        if ($feature->is_system) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Sistem feature\'ları silinemez',
                'type' => 'error'
            ]);
            return;
        }
        
        try {
            $feature->featurePrompts()->delete();
            $feature->delete();
            
            session()->flash('toast', [
                'title' => 'Başarılı',
                'message' => 'Feature başarıyla silindi',
                'type' => 'success'
            ]);
            
            return redirect()->route('admin.ai.features.index');
            
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Silme işlemi başarısız: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function getCategories()
    {
        return [
            'content-creation' => 'İçerik Oluşturma',
            'web-editor' => 'Web Editör',
            'productivity' => 'Prodüktivite',
            'communication' => 'İletişim',
            'education' => 'Eğitim',
            'analysis' => 'Analiz',
            'translation' => 'Çeviri',
            'seo-tools' => 'SEO Araçları',
            'marketing' => 'Pazarlama',
            'creative' => 'Yaratıcı',
            'business' => 'İş Dünyası',
            'technical' => 'Teknik',
            'other' => 'Diğer'
        ];
    }

    public function getComplexityLevels()
    {
        return [
            'beginner' => 'Başlangıç',
            'intermediate' => 'Orta',
            'advanced' => 'İleri',
            'expert' => 'Uzman'
        ];
    }

    public function getPromptRoles()
    {
        return [
            'primary' => 'Ana Prompt',
            'secondary' => 'İkincil Prompt',
            'hidden' => 'Gizli Sistem',
            'conditional' => 'Şartlı Prompt',
            'formatting' => 'Format Düzenleme',
            'validation' => 'Doğrulama'
        ];
    }

    public function getJsonFieldNames()
    {
        return [
            'additional_config' => 'Ek Konfigürasyon',
            'usage_examples' => 'Kullanım Örnekleri',
            'input_validation' => 'Input Doğrulama',
            'settings' => 'Ayarlar',
            'error_messages' => 'Hata Mesajları',
            'success_messages' => 'Başarı Mesajları',
            'token_cost' => 'Token Maliyeti',
            'example_inputs' => 'Örnek Girişler',
            'helper_examples' => 'Helper Örnekleri',
            'helper_parameters' => 'Helper Parametreleri',
            'helper_returns' => 'Helper Dönüş Değerleri',
            'response_template' => 'Yanıt Şablonu'
        ];
    }

    public function render()
    {
        return view('ai::admin.livewire.ai-feature-manage-component');
    }
}