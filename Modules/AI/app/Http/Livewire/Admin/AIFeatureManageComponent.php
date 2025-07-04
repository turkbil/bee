<?php
namespace Modules\AI\App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Modules\AI\App\Models\AIFeature;
use Modules\AI\App\Models\Prompt;
use Modules\AI\App\Models\AIFeaturePrompt;
use Illuminate\Support\Str;

#[Layout('admin.layout')]
class AIFeatureManageComponent extends Component
{
    public $featureId;
    
    // Temel inputs
    public $inputs = [
        'name' => '',
        'slug' => '',
        'description' => '',
        'emoji' => '🤖',
        'icon' => 'fas fa-robot',
        'category' => 'content',
        'response_length' => 'medium',
        'response_format' => 'markdown',
        'complexity_level' => 'intermediate',
        'status' => 'active',
        'badge_color' => 'success',
        'sort_order' => 1,
        'show_in_examples' => true,
        'is_featured' => false,
        'requires_pro' => false,
        'requires_input' => true,
        'input_placeholder' => '',
        'button_text' => 'Canlı Test Et',
        'meta_title' => '',
        'meta_description' => '',
        'example_inputs' => []
    ];

    // Prompt yönetimi
    public $existingPrompts = [];
    public $newPrompts = [];
    public $deletedPrompts = [];

    // Mevcut prompts ve statistics
    public $availablePrompts = [];
    public $featureStats = [];

    public function mount($id = null)
    {
        // Mevcut prompt'ları yükle
        $this->availablePrompts = Prompt::where('is_system', true)
            ->orderBy('name')
            ->get();

        if ($id) {
            $this->featureId = $id;
            $feature = AIFeature::with(['featurePrompts.prompt'])->find($id);
            
            if (!$feature) {
                abort(404, 'AI Özelliği bulunamadı');
            }
            
            $this->loadFeatureData($feature);
            $this->loadFeatureStats($feature);
        } else {
            $this->initializeEmptyData();
        }
    }

    protected function loadFeatureData($feature)
    {
        $this->inputs = [
            'name' => $feature->name,
            'slug' => $feature->slug,
            'description' => $feature->description,
            'emoji' => $feature->emoji,
            'icon' => $feature->icon,
            'category' => $feature->category,
            'response_length' => $feature->response_length,
            'response_format' => $feature->response_format,
            'complexity_level' => $feature->complexity_level,
            'status' => $feature->status,
            'badge_color' => $feature->badge_color,
            'sort_order' => $feature->sort_order,
            'show_in_examples' => $feature->show_in_examples,
            'is_featured' => $feature->is_featured,
            'requires_pro' => $feature->requires_pro,
            'requires_input' => $feature->requires_input,
            'input_placeholder' => $feature->input_placeholder,
            'button_text' => $feature->button_text ?? 'Canlı Test Et',
            'meta_title' => $feature->meta_title,
            'meta_description' => $feature->meta_description,
            'example_inputs' => $feature->example_inputs ?? []
        ];

        // Mevcut prompt bağlantılarını yükle
        foreach ($feature->featurePrompts as $featurePrompt) {
            $this->existingPrompts[$featurePrompt->id] = [
                'prompt_id' => $featurePrompt->ai_prompt_id,
                'role' => $featurePrompt->prompt_role,
                'priority' => $featurePrompt->priority,
                'is_required' => $featurePrompt->is_required,
                'is_active' => $featurePrompt->is_active
            ];
        }
    }

    protected function loadFeatureStats($feature)
    {
        $this->featureStats = [
            'usage_count' => $feature->usage_count,
            'avg_rating' => $feature->avg_rating,
            'rating_count' => $feature->rating_count,
            'total_tokens' => $feature->total_tokens,
            'last_used_at' => $feature->last_used_at,
            'is_system' => $feature->is_system,
            'created_at' => $feature->created_at,
            'updated_at' => $feature->updated_at,
            'prompts_count' => $feature->prompts->count()
        ];
    }

    protected function initializeEmptyData()
    {
        // Yeni özellik için varsayılan değerler zaten inputs'ta tanımlı
        $this->inputs['sort_order'] = AIFeature::max('sort_order') + 1;
    }

    public function updatedInputsName()
    {
        if (empty($this->inputs['slug'])) {
            $this->inputs['slug'] = Str::slug($this->inputs['name']);
        }
    }

    public function addPrompt()
    {
        $index = count($this->newPrompts);
        $this->newPrompts[$index] = [
            'prompt_id' => '',
            'role' => 'primary',
            'priority' => 1,
            'is_required' => true,
            'is_active' => true
        ];
    }

    public function removePrompt($index)
    {
        unset($this->newPrompts[$index]);
        $this->newPrompts = array_values($this->newPrompts);
    }

    public function removeExistingPrompt($id)
    {
        if (isset($this->existingPrompts[$id])) {
            $this->deletedPrompts[] = $id;
            unset($this->existingPrompts[$id]);
        }
    }

    public function addExample()
    {
        $this->inputs['example_inputs'][] = [
            'label' => '',
            'text' => ''
        ];
    }

    public function removeExample($index)
    {
        unset($this->inputs['example_inputs'][$index]);
        $this->inputs['example_inputs'] = array_values($this->inputs['example_inputs']);
    }

    protected function rules()
    {
        $slugRule = 'required|unique:ai_features,slug';
        if ($this->featureId) {
            $slugRule .= ',' . $this->featureId;
        }

        return [
            'inputs.name' => 'required|string|min:3|max:255',
            'inputs.slug' => $slugRule,
            'inputs.description' => 'nullable|string|max:500',
            'inputs.emoji' => 'nullable|string|max:10',
            'inputs.icon' => 'nullable|string|max:100',
            'inputs.category' => 'required|in:content,creative,business,technical,academic,legal,marketing,analysis,communication,other',
            'inputs.response_length' => 'required|in:short,medium,long,variable',
            'inputs.response_format' => 'required|in:text,markdown,structured,code,list',
            'inputs.complexity_level' => 'required|in:beginner,intermediate,advanced,expert',
            'inputs.status' => 'required|in:active,inactive,planned,beta',
            'inputs.badge_color' => 'required|in:success,primary,warning,info,danger,secondary',
            'inputs.sort_order' => 'required|integer|min:1',
            'inputs.show_in_examples' => 'boolean',
            'inputs.is_featured' => 'boolean',
            'inputs.requires_pro' => 'boolean',
            'inputs.requires_input' => 'boolean',
            'inputs.input_placeholder' => 'nullable|string|max:255',
            'inputs.button_text' => 'nullable|string|max:100',
            'inputs.meta_title' => 'nullable|string|max:255',
            'inputs.meta_description' => 'nullable|string|max:500',
            'inputs.example_inputs' => 'nullable|array',
            'inputs.example_inputs.*.label' => 'nullable|string|max:100',
            'inputs.example_inputs.*.text' => 'nullable|string|max:500',
            'existingPrompts.*.prompt_id' => 'required|exists:ai_prompts,id',
            'existingPrompts.*.role' => 'required|in:primary,secondary,hidden,conditional,formatting,validation',
            'existingPrompts.*.priority' => 'required|integer|min:0',
            'existingPrompts.*.is_required' => 'boolean',
            'newPrompts.*.prompt_id' => 'required|exists:ai_prompts,id',
            'newPrompts.*.role' => 'required|in:primary,secondary,hidden,conditional,formatting,validation',
            'newPrompts.*.priority' => 'required|integer|min:0',
            'newPrompts.*.is_required' => 'boolean'
        ];
    }

    protected $messages = [
        'inputs.name.required' => 'Özellik adı zorunludur',
        'inputs.name.min' => 'Özellik adı en az 3 karakter olmalıdır',
        'inputs.slug.required' => 'URL slug zorunludur',
        'inputs.slug.unique' => 'Bu slug zaten kullanılmaktadır',
        'inputs.category.required' => 'Kategori seçimi zorunludur',
        'inputs.status.required' => 'Durum seçimi zorunludur',
        'existingPrompts.*.prompt_id.required' => 'Prompt seçimi zorunludur',
        'existingPrompts.*.prompt_id.exists' => 'Seçilen prompt bulunamadı',
        'newPrompts.*.prompt_id.required' => 'Prompt seçimi zorunludur',
        'newPrompts.*.prompt_id.exists' => 'Seçilen prompt bulunamadı'
    ];

    public function save($redirect = false)
    {
        $this->validate();

        try {
            // Feature verilerini hazırla
            $featureData = $this->inputs;
            
            // Boolean değerleri kontrol et
            $featureData['show_in_examples'] = (bool) $featureData['show_in_examples'];
            $featureData['is_featured'] = (bool) $featureData['is_featured'];
            $featureData['requires_pro'] = (bool) $featureData['requires_pro'];
            $featureData['requires_input'] = (bool) $featureData['requires_input'];

            if ($this->featureId) {
                // Güncelleme işlemi
                $feature = AIFeature::findOrFail($this->featureId);
                $feature->update($featureData);
                
                $message = 'AI özelliği başarıyla güncellendi';
            } else {
                // Oluşturma işlemi
                $featureData['is_system'] = false; // Yeni oluşturulan özellikler sistem özelliği değil
                $feature = AIFeature::create($featureData);
                $this->featureId = $feature->id;
                
                $message = 'AI özelliği başarıyla oluşturuldu';
            }

            // Prompt bağlantılarını güncelle
            $this->updatePromptConnections($feature);

            $toast = [
                'title' => 'Başarılı',
                'message' => $message,
                'type' => 'success'
            ];

        } catch (\Exception $e) {
            \Log::error('AI Feature Save Error: ' . $e->getMessage(), [
                'inputs' => $this->inputs,
                'feature_id' => $this->featureId
            ]);

            $toast = [
                'title' => 'Hata',
                'message' => 'İşlem sırasında bir hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ];
        }

        if ($redirect) {
            session()->flash('toast', $toast);
            return redirect()->route('admin.ai.features.index');
        }

        $this->dispatch('toast', $toast);
        
        // İstatistikleri yeniden yükle
        if ($this->featureId) {
            $this->loadFeatureStats(AIFeature::find($this->featureId));
        }
    }

    protected function updatePromptConnections($feature)
    {
        // Silinecek prompt bağlantılarını kaldır
        foreach ($this->deletedPrompts as $promptId) {
            AIFeaturePrompt::where('id', $promptId)->delete();
        }

        // Mevcut prompt bağlantılarını güncelle
        foreach ($this->existingPrompts as $id => $promptData) {
            AIFeaturePrompt::where('id', $id)->update([
                'ai_prompt_id' => $promptData['prompt_id'],
                'prompt_role' => $promptData['role'],
                'priority' => $promptData['priority'],
                'is_required' => (bool) $promptData['is_required'],
                'is_active' => (bool) ($promptData['is_active'] ?? true)
            ]);
        }

        // Yeni prompt bağlantılarını oluştur
        foreach ($this->newPrompts as $promptData) {
            if (!empty($promptData['prompt_id'])) {
                AIFeaturePrompt::create([
                    'ai_feature_id' => $feature->id,
                    'ai_prompt_id' => $promptData['prompt_id'],
                    'prompt_role' => $promptData['role'],
                    'priority' => $promptData['priority'],
                    'is_required' => (bool) $promptData['is_required'],
                    'is_active' => true
                ]);
            }
        }

        // Component state'i temizle
        $this->deletedPrompts = [];
        $this->newPrompts = [];
    }

    public function delete()
    {
        if (!$this->featureId) {
            return;
        }

        $feature = AIFeature::find($this->featureId);
        
        if (!$feature) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Silinecek özellik bulunamadı',
                'type' => 'error'
            ]);
            return;
        }

        if ($feature->is_system) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Sistem özellikleri silinemez',
                'type' => 'error'
            ]);
            return;
        }

        try {
            // İlişkili verileri temizle
            $feature->featurePrompts()->delete();
            $feature->delete();

            session()->flash('toast', [
                'title' => 'Başarılı',
                'message' => 'AI özelliği başarıyla silindi',
                'type' => 'success'
            ]);

            return redirect()->route('admin.ai.features.index');

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'title' => 'Hata',
                'message' => 'Silme işlemi sırasında hata oluştu: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function render()
    {
        return view('ai::admin.livewire.ai-feature-manage-component', [
            'feature' => $this->featureId ? AIFeature::find($this->featureId) : null,
            'categories' => [
                'content' => 'İçerik',
                'creative' => 'Yaratıcı',
                'business' => 'İş Dünyası',
                'technical' => 'Teknik',
                'academic' => 'Akademik',
                'legal' => 'Hukuki',
                'marketing' => 'Pazarlama',
                'analysis' => 'Analiz',
                'communication' => 'İletişim',
                'other' => 'Diğer'
            ],
            'statuses' => [
                'active' => 'Aktif',
                'inactive' => 'Pasif',
                'planned' => 'Planlanan',
                'beta' => 'Beta'
            ]
        ]);
    }
}