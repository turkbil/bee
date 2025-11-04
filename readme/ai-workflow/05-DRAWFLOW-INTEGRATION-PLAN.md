# 🎨 DRAWFLOW INTEGRATION PLAN - PHASE 5

**Tarih:** 2025-11-04 19:00
**Durum:** 📋 PLANLANDI (Henüz uygulanmadı)
**Kaynak:** https://jerosoler.github.io/Drawflow/

---

## 📊 MEVCUT DURUM (Phase 4 Tamamlandı)

✅ **Core System:**
- Database (3 tablo)
- Models (TenantConversationFlow, AITenantDirective, AIConversation)
- Node System (13 node tipi)
- Flow Engine (ConversationFlowEngine)
- Seeder (Demo data için)

✅ **Admin Panel (Basic CRUD):**
- FlowList component (flow listesi, activate/deactivate, duplicate, delete)
- DirectiveManager component (directive yönetimi, inline edit)
- Layout system entegre

❌ **Visual Flow Editor:** Henüz yok - Bu Phase 5'te eklenecek

---

## 🎯 DRAWFLOW NEDİR?

**Drawflow** - JavaScript görsel akış tasarlayıcı kütüphanesi
- Drag & drop node editor
- Connection management (edges)
- Node configuration
- Export/Import JSON
- Event system
- Zoom & Pan

**Örnek:** https://jerosoler.github.io/Drawflow/

---

## 🏗️ PHASE 5 - DRAWFLOW ENTEGRASYONU

### 1. Frontend Setup

**CDN veya NPM:**
```html
<!-- Option 1: CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/drawflow@0.0.60/dist/drawflow.min.css">
<script src="https://cdn.jsdelivr.net/npm/drawflow@0.0.60/dist/drawflow.min.js"></script>

<!-- Option 2: NPM -->
npm install drawflow
```

**Assets:**
- CSS: `public/vendor/drawflow/drawflow.css`
- JS: `public/vendor/drawflow/drawflow.js`

### 2. FlowEditor Component

**Livewire Component:**
```php
// Modules/AI/App/Http/Livewire/Admin/Workflow/FlowEditor.php

namespace Modules\AI\App\Http\Livewire\Admin\Workflow;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\TenantConversationFlow;
use App\Services\ConversationNodes\NodeExecutor;

#[Layout('admin.layout')]
class FlowEditor extends Component
{
    public $flowId = null;
    public $flowName = '';
    public $flowDescription = '';
    public $flowData = [];
    public $availableNodes = [];

    public function mount($flowId = null)
    {
        if ($flowId) {
            $flow = TenantConversationFlow::find($flowId);
            $this->flowId = $flow->id;
            $this->flowName = $flow->flow_name;
            $this->flowDescription = $flow->flow_description;
            $this->flowData = $flow->flow_data;
        }

        // Node kütüphanesini al
        $this->availableNodes = NodeExecutor::getAvailableNodes();
    }

    public function saveFlow($drawflowData)
    {
        // Drawflow JSON'u al, flow_data'ya dönüştür
        $flowData = $this->convertDrawflowToFlowData($drawflowData);

        TenantConversationFlow::updateOrCreate(
            ['id' => $this->flowId],
            [
                'tenant_id' => tenant('id'),
                'flow_name' => $this->flowName,
                'flow_description' => $this->flowDescription,
                'flow_data' => $flowData,
                'start_node_id' => $flowData['nodes'][0]['id'] ?? null,
            ]
        );

        session()->flash('success', 'Flow saved!');
    }

    private function convertDrawflowToFlowData($drawflowData)
    {
        // Drawflow formatını TenantConversationFlow formatına çevir
        // ...
    }

    public function render()
    {
        return view('ai::livewire.admin.workflow.flow-editor');
    }
}
```

### 3. FlowEditor Blade View

**Template:**
```blade
<div>
    <div class="page-header">
        <div class="container-xl">
            <h2 class="page-title">
                <i class="ti ti-git-branch me-2"></i>
                {{ $flowId ? 'Edit Flow' : 'Create New Flow' }}
            </h2>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row">
                <!-- Left Sidebar: Node Palette -->
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Node Library</h3>
                        </div>
                        <div class="card-body">
                            @foreach($availableNodes as $node)
                                <div class="node-item draggable mb-2 p-2 border rounded"
                                     data-node="{{ $node['type'] }}"
                                     data-class="{{ $node['class'] }}">
                                    <i class="ti ti-box me-2"></i>
                                    {{ $node['name'] }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Main Canvas: Drawflow Editor -->
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header">
                            <input type="text" wire:model.defer="flowName" class="form-control" placeholder="Flow Name">
                        </div>
                        <div class="card-body">
                            <div id="drawflow" style="height: 600px;"></div>
                        </div>
                        <div class="card-footer">
                            <button wire:click="saveFlow" class="btn btn-primary">
                                <i class="ti ti-save"></i> Save Flow
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="/vendor/drawflow/drawflow.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Drawflow init
            const editor = new Drawflow(document.getElementById('drawflow'));
            editor.start();

            // Load existing flow
            @if($flowId)
                const flowData = @json($flowData);
                editor.import(flowData);
            @endif

            // Node palette drag & drop
            document.querySelectorAll('.draggable').forEach(item => {
                item.addEventListener('dragstart', (e) => {
                    e.dataTransfer.setData('node', item.dataset.node);
                });
            });

            // Canvas drop handler
            document.getElementById('drawflow').addEventListener('drop', (e) => {
                e.preventDefault();
                const nodeType = e.dataTransfer.getData('node');
                // Add node to canvas
                editor.addNode(nodeType, 0, 1, e.clientX, e.clientY, nodeType, {}, '<div>Node Content</div>');
            });

            // Save handler
            Livewire.on('saveFlow', () => {
                const exportData = editor.export();
                @this.saveFlow(exportData);
            });
        });
    </script>
    @endpush
</div>
```

### 4. Routes (Zaten yorum olarak mevcut)

**Uncomment:**
```php
// Modules/AI/routes/admin.php (Line 1016-1023)

Route::get('/flows/create', \Modules\AI\App\Http\Livewire\Admin\Workflow\FlowEditor::class)
    ->middleware('module.permission:ai,create')
    ->name('flows.create');

Route::get('/flows/{flowId}/edit', \Modules\AI\App\Http\Livewire\Admin\Workflow\FlowEditor::class)
    ->middleware('module.permission:ai,update')
    ->name('flows.edit');
```

### 5. FlowList Butonlarını Aktifleştir

**Uncomment:**
```blade
<!-- flow-list.blade.php Line 13-17 (Create button) -->
<a href="{{ route('admin.ai.workflow.flows.create') }}" class="btn btn-primary">
    <i class="ti ti-plus me-1"></i>
    Create New Flow
</a>

<!-- flow-list.blade.php Line 82-85 (Edit button) -->
<a href="{{ route('admin.ai.workflow.flows.edit', $flow->id) }}" class="btn btn-sm">
    <i class="ti ti-edit"></i> Edit
</a>
```

---

## 🔄 DATA FLOW (Drawflow ↔ Laravel)

### Drawflow Export Format:
```json
{
  "drawflow": {
    "Home": {
      "data": {
        "1": {
          "id": 1,
          "name": "welcome",
          "data": {},
          "class": "welcome",
          "html": "<div>Welcome Node</div>",
          "typenode": false,
          "inputs": {},
          "outputs": {
            "output_1": {
              "connections": [
                { "node": "2", "output": "input_1" }
              ]
            }
          },
          "pos_x": 100,
          "pos_y": 100
        },
        "2": { /* ... */ }
      }
    }
  }
}
```

### TenantConversationFlow Format:
```json
{
  "nodes": [
    {
      "id": "node_welcome",
      "type": "ai_response",
      "name": "Welcome",
      "class": "App\\Services\\ConversationNodes\\Common\\AIResponseNode",
      "config": {
        "system_prompt": "Hello!"
      },
      "position": { "x": 100, "y": 100 }
    }
  ],
  "edges": [
    {
      "id": "edge_1",
      "source": "node_welcome",
      "target": "node_category"
    }
  ]
}
```

### Conversion Logic:
```php
private function convertDrawflowToFlowData($drawflowData)
{
    $nodes = [];
    $edges = [];

    foreach ($drawflowData['drawflow']['Home']['data'] as $nodeId => $nodeData) {
        $nodes[] = [
            'id' => 'node_' . $nodeId,
            'type' => $nodeData['name'],
            'name' => $nodeData['html'] ?? $nodeData['name'],
            'class' => $nodeData['class'],
            'config' => $nodeData['data'],
            'position' => [
                'x' => $nodeData['pos_x'],
                'y' => $nodeData['pos_y']
            ]
        ];

        // Extract connections as edges
        foreach ($nodeData['outputs'] as $output) {
            foreach ($output['connections'] as $conn) {
                $edges[] = [
                    'id' => 'edge_' . $nodeId . '_' . $conn['node'],
                    'source' => 'node_' . $nodeId,
                    'target' => 'node_' . $conn['node']
                ];
            }
        }
    }

    return compact('nodes', 'edges');
}
```

---

## 📋 IMPLEMENTATION CHECKLIST

- [ ] Drawflow kütüphanesini indir/yükle (CDN veya NPM)
- [ ] FlowEditor Livewire component oluştur
- [ ] flow-editor.blade.php view oluştur
- [ ] Node palette HTML/CSS tasarla
- [ ] Drawflow canvas entegrasyonu (init, import, export)
- [ ] Drag & drop node ekleme
- [ ] Node configuration modal (Alpine.js)
- [ ] Data conversion logic (Drawflow ↔ Laravel)
- [ ] Route'ları uncomment et
- [ ] FlowList butonlarını aktifleştir
- [ ] Test: Create flow
- [ ] Test: Edit flow
- [ ] Test: Save & load
- [ ] Cache clear + OPcache reset

---

## 🎨 UI/UX NOTLARI

**Node Palette (Sol Sidebar):**
- Common Nodes (yeşil)
- İxtif Nodes (mavi)
- Draggable icons
- Tooltip ile açıklama

**Canvas (Ortadaki Alan):**
- Grid background
- Zoom controls
- Pan support
- Context menu (sağ tık)

**Node Configuration:**
- Modal veya sidebar
- Form inputs (text, select, textarea)
- Validation
- Save/Cancel

**Toolbar:**
- Save Flow
- Test Flow (preview mode)
- Export JSON
- Import JSON
- Clear Canvas

---

## 🚨 ÖNEMLI NOTLAR

1. **Node Class Mapping:** Her Drawflow node'u bir PHP class'a map etmeli
2. **Validation:** Flow'da döngü (circular dependency) kontrolü
3. **Start Node:** İlk node otomatik start_node_id olarak ayarlanmalı
4. **Error Handling:** Eksik bağlantı, yanlış config kontrolü
5. **Auto-save:** Belirli aralıklarla otomatik kayıt (localStorage backup)

---

## 📖 KAYNAKLAR

- **Drawflow Docs:** https://github.com/jerosoler/Drawflow
- **Demo:** https://jerosoler.github.io/Drawflow/
- **Examples:** https://github.com/jerosoler/Drawflow/tree/master/examples

---

**Hazırlayan:** Claude AI
**Tarih:** 2025-11-04
**Durum:** PLANLANMIŞ - Uygulamaya hazır
