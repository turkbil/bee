<?php

namespace Modules\AI\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\AI\App\Models\AIFeature;
use Modules\AI\App\Models\Prompt;
use Modules\AI\App\Models\AIFeaturePrompt;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AIFeaturesController extends Controller
{
    /**
     * AI Features listesi
     */
    public function index(Request $request)
    {
        $query = AIFeature::with(['prompts']);
        
        // Filtreleme
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        
        // Sıralama
        $query->orderBy('sort_order')->orderBy('name');
        
        $features = $query->paginate(20);
        
        // Kategoriler ve durumlar dropdown için
        $categories = collect([
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
        ]);
        
        $statuses = collect([
            'active' => 'Aktif',
            'inactive' => 'Pasif',
            'planned' => 'Planlanan',
            'beta' => 'Beta'
        ]);
        
        return view('ai::admin.features.index', compact('features', 'categories', 'statuses'));
    }

    /**
     * Yeni AI Feature oluşturma formu
     */
    public function create()
    {
        $prompts = Prompt::where('is_active', true)->orderBy('name')->get();
        
        return view('ai::admin.features.create', compact('prompts'));
    }

    /**
     * AI Feature kaydetme
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'emoji' => 'nullable|string|max:10',
            'icon' => 'nullable|string|max:50',
            'category' => 'required|in:content,creative,business,technical,academic,legal,marketing,analysis,communication,other',
            'response_length' => 'required|in:short,medium,long,variable',
            'response_format' => 'required|in:text,markdown,structured,code,list',
            'complexity_level' => 'required|in:beginner,intermediate,advanced,expert',
            'status' => 'required|in:active,inactive,planned,beta',
            'badge_color' => 'required|in:primary,secondary,success,danger,warning,info,light,dark',
            'requires_input' => 'boolean',
            'input_placeholder' => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:50',
            'example_inputs' => 'nullable|array',
            'example_inputs.*.text' => 'required|string',
            'example_inputs.*.label' => 'required|string',
            'prompts' => 'array',
            'prompts.*.prompt_id' => 'required|exists:ai_prompts,id',
            'prompts.*.role' => 'required|in:primary,secondary,hidden,conditional,formatting,validation',
            'prompts.*.priority' => 'required|integer|min:0',
            'prompts.*.is_required' => 'boolean'
        ]);

        // Slug oluştur
        $validated['slug'] = Str::slug($validated['name']);
        $validated['button_text'] = $validated['button_text'] ?: 'Canlı Test Et';
        
        // AI Feature oluştur
        $feature = AIFeature::create($validated);
        
        // Prompt'ları bağla
        if ($request->has('prompts')) {
            foreach ($request->prompts as $promptData) {
                AIFeaturePrompt::create([
                    'ai_feature_id' => $feature->id,
                    'ai_prompt_id' => $promptData['prompt_id'],
                    'prompt_role' => $promptData['role'],
                    'priority' => $promptData['priority'],
                    'is_required' => $promptData['is_required'] ?? false,
                    'is_active' => true
                ]);
            }
        }
        
        return redirect()->route('admin.ai.features.index')
            ->with('success', 'AI özelliği başarıyla oluşturuldu.');
    }

    /**
     * AI Feature görüntüleme
     */
    public function show(AIFeature $feature)
    {
        $feature->load(['prompts', 'featurePrompts.aiPrompt']);
        
        return view('ai::admin.features.show', compact('feature'));
    }

    /**
     * AI Feature düzenleme formu
     */
    public function edit(AIFeature $feature)
    {
        $feature->load(['prompts', 'featurePrompts.aiPrompt']);
        $prompts = Prompt::where('is_active', true)->orderBy('name')->get();
        
        return view('ai::admin.features.edit', compact('feature', 'prompts'));
    }

    /**
     * AI Feature güncelleme
     */
    public function update(Request $request, AIFeature $feature)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'emoji' => 'nullable|string|max:10',
            'icon' => 'nullable|string|max:50',
            'category' => 'required|in:content,creative,business,technical,academic,legal,marketing,analysis,communication,other',
            'response_length' => 'required|in:short,medium,long,variable',
            'response_format' => 'required|in:text,markdown,structured,code,list',
            'complexity_level' => 'required|in:beginner,intermediate,advanced,expert',
            'status' => 'required|in:active,inactive,planned,beta',
            'badge_color' => 'required|in:primary,secondary,success,danger,warning,info,light,dark',
            'requires_input' => 'boolean',
            'input_placeholder' => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:50',
            'example_inputs' => 'nullable|array',
            'prompts' => 'array'
        ]);

        // Slug güncelle
        $validated['slug'] = Str::slug($validated['name']);
        $validated['button_text'] = $validated['button_text'] ?: 'Canlı Test Et';
        
        // Feature güncelle
        $feature->update($validated);
        
        // Mevcut prompt bağlantılarını sil
        $feature->featurePrompts()->delete();
        
        // Yeni prompt'ları bağla
        if ($request->has('prompts')) {
            foreach ($request->prompts as $promptData) {
                AIFeaturePrompt::create([
                    'ai_feature_id' => $feature->id,
                    'ai_prompt_id' => $promptData['prompt_id'],
                    'prompt_role' => $promptData['role'],
                    'priority' => $promptData['priority'],
                    'is_required' => $promptData['is_required'] ?? false,
                    'is_active' => true
                ]);
            }
        }
        
        return redirect()->route('admin.ai.features.index')
            ->with('success', 'AI özelliği başarıyla güncellendi.');
    }

    /**
     * AI Feature silme
     */
    public function destroy(AIFeature $feature)
    {
        // Sistem özelliği silinemez
        if ($feature->is_system) {
            return back()->with('error', 'Sistem özellikleri silinemez.');
        }
        
        // İlişkili kayıtları sil
        $feature->featurePrompts()->delete();
        $feature->delete();
        
        return redirect()->route('admin.ai.features.index')
            ->with('success', 'AI özelliği başarıyla silindi.');
    }

    /**
     * Toplu durum değiştirme
     */
    public function bulkStatusUpdate(Request $request)
    {
        $request->validate([
            'feature_ids' => 'required|array',
            'feature_ids.*' => 'exists:ai_features,id',
            'status' => 'required|in:active,inactive,planned,beta'
        ]);
        
        AIFeature::whereIn('id', $request->feature_ids)
            ->update(['status' => $request->status]);
        
        return back()->with('success', 'Seçili özellikler güncellendi.');
    }

    /**
     * Sıralama güncelleme
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|exists:ai_features,id',
            'orders.*.sort_order' => 'required|integer'
        ]);
        
        foreach ($request->orders as $order) {
            AIFeature::where('id', $order['id'])
                ->update(['sort_order' => $order['sort_order']]);
        }
        
        return response()->json(['success' => true]);
    }

    /**
     * Özellik kopyalama
     */
    public function duplicate(AIFeature $feature)
    {
        $newFeature = $feature->replicate();
        $newFeature->name = $feature->name . ' (Kopya)';
        $newFeature->slug = Str::slug($newFeature->name);
        $newFeature->is_system = false;
        $newFeature->save();
        
        // Prompt bağlantılarını kopyala
        foreach ($feature->featurePrompts as $featurePrompt) {
            AIFeaturePrompt::create([
                'ai_feature_id' => $newFeature->id,
                'ai_prompt_id' => $featurePrompt->ai_prompt_id,
                'prompt_role' => $featurePrompt->prompt_role,
                'priority' => $featurePrompt->priority,
                'is_required' => $featurePrompt->is_required,
                'is_active' => $featurePrompt->is_active,
                'conditions' => $featurePrompt->conditions,
                'parameters' => $featurePrompt->parameters,
                'notes' => $featurePrompt->notes
            ]);
        }
        
        return redirect()->route('admin.ai.features.edit', $newFeature)
            ->with('success', 'AI özelliği kopyalandı ve düzenleme için açıldı.');
    }
}