<div>
    <div class="page-header d-print-none">
        <div class="container-fluid">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <i class="fa fa-code-branch me-2"></i>
                        {{ $flowId ? __('ai::admin.workflow.edit_flow') : __('ai::admin.workflow.create_new_flow') }}
                    </h2>
                    <div class="text-muted mt-1">{{ __('ai::admin.workflow.design_subtitle') }}</div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('admin.ai.workflow.flows.index') }}" class="btn btn-ghost-secondary me-2">
                        <i class="fa fa-times me-1"></i>
                        {{ __('ai::admin.workflow.cancel') }}
                    </a>
                    @if($flowId)
                    <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#testFlowModal">
                        <i class="fa fa-play me-1"></i>
                        {{ __('ai::admin.workflow.test_flow') }}
                    </button>
                    @endif
                    <button wire:click="saveFlow" class="btn btn-primary" id="save-flow-btn">
                        <i class="fa fa-save me-1"></i>
                        {{ __('ai::admin.workflow.save_flow') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-fluid">
            <!-- Flow Info Card -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label required">{{ __('ai::admin.workflow.flow_name') }}</label>
                                    <input type="text" wire:model.defer="flowName" class="form-control" placeholder="{{ __('ai::admin.workflow.flow_name_placeholder') }}">
                                    @error('flowName') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('ai::admin.workflow.priority') }}</label>
                                    <input type="number" wire:model.defer="priority" class="form-control" min="0" max="999">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('ai::admin.status') }}</label>
                                    <label class="form-check form-switch mt-2">
                                        <input type="checkbox" wire:model.defer="isActive" class="form-check-input">
                                        <span class="form-check-label">{{ __('ai::admin.workflow.active') }}</span>
                                    </label>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">{{ __('ai::admin.workflow.flow_description') }}</label>
                                    <textarea wire:model.defer="flowDescription" class="form-control" rows="2" placeholder="{{ __('ai::admin.workflow.flow_description_placeholder') }}"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Left Sidebar: Node Palette -->
                <div class="col-md-3">
                    <div class="card" style="position: sticky; top: 20px; max-height: 80vh; overflow-y: auto;">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fa fa-cubes me-2"></i>
                                {{ __('ai::admin.workflow.node_library') }}
                            </h3>
                            <div class="card-subtitle text-muted small mt-1">
                                {{ __('ai::admin.workflow.tenant') }}: {{ $currentTenantId == 2 ? 'İxtif.com' : 'Tenant A' }}
                            </div>
                        </div>
                        <div class="card-body p-0">
                            @foreach($nodesByCategory as $category => $nodes)
                                <div class="list-group list-group-flush">
                                    <!-- Category Header -->
                                    <div class="list-group-item bg-light">
                                        <div class="fw-bold text-uppercase small">
                                            <i class="fa fa-folder me-1"></i>
                                            @if($category === 'common')
                                                <span class="badge bg-green-lt text-green">{{ __('ai::admin.workflow.global_functions') }}</span>
                                            @elseif($category === 'ecommerce')
                                                <span class="badge bg-blue-lt text-blue">{{ __('ai::admin.workflow.ecommerce') }}</span>
                                            @elseif($category === 'communication')
                                                <span class="badge bg-purple-lt text-purple">{{ __('ai::admin.workflow.communication') }}</span>
                                            @else
                                                <span class="badge bg-gray-lt">{{ ucfirst($category) }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Category Nodes -->
                                    @foreach($nodes as $node)
                                        <div class="list-group-item node-palette-item"
                                             draggable="true"
                                             data-node-type="{{ $node['type'] }}"
                                             data-node-name="{{ $node['name'] }}"
                                             data-node-class="{{ $node['class'] }}"
                                             data-node-category="{{ $category }}"
                                             style="cursor: move; padding-left: 1.5rem;">
                                            <div class="d-flex align-items-center">
                                                <span class="avatar avatar-sm me-2
                                                    @if(str_contains($node['class'], 'Common')) bg-green-lt
                                                    @elseif($category === 'ecommerce') bg-blue-lt
                                                    @else bg-purple-lt
                                                    @endif">
                                                    <i class="fa fa-circle"></i>
                                                </span>
                                                <div class="flex-fill">
                                                    <div class="fw-bold">{{ $node['name'] }}</div>
                                                    <div class="text-muted small">
                                                        {{ $node['description'] ?? $node['type'] }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach

                            @if(empty($nodesByCategory))
                                <div class="list-group-item text-center text-muted py-4">
                                    <i class="fa fa-inbox fs-2 mb-2 d-block"></i>
                                    {{ __('ai::admin.workflow.no_nodes') }}
                                </div>
                            @endif
                        </div>
                        <div class="card-footer text-muted small">
                            <i class="fa fa-info-circle me-1"></i>
                            {{ __('ai::admin.workflow.drag_nodes') }}
                        </div>
                    </div>
                </div>

                <!-- Main Canvas: Drawflow Editor -->
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <h3 class="card-title">{{ __('ai::admin.workflow.flow_canvas') }}</h3>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-ghost-secondary" onclick="editor.zoom_in()" title="{{ __('ai::admin.workflow.zoom_in') }}">
                                        <i class="fa fa-search-plus"></i>
                                    </button>
                                    <button class="btn btn-sm btn-ghost-secondary" onclick="editor.zoom_out()" title="{{ __('ai::admin.workflow.zoom_out') }}">
                                        <i class="fa fa-search-minus"></i>
                                    </button>
                                    <button class="btn btn-sm btn-ghost-secondary" onclick="editor.zoom_reset()" title="{{ __('ai::admin.workflow.reset_zoom') }}">
                                        <i class="fa fa-sync-alt"></i>
                                    </button>
                                    <button class="btn btn-sm btn-ghost-danger" onclick="clearCanvas()">
                                        <i class="fa fa-trash"></i> {{ __('ai::admin.workflow.clear') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div id="drawflow" style="height: 600px; position: relative;"></div>
                        </div>
                        <div class="card-footer text-muted small">
                            <i class="fa fa-hand-paper me-1"></i>
                            {{ __('ai::admin.workflow.canvas_help') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="/vendor/drawflow/drawflow.min.css">
    <style>
        /* Canvas Background - Light Mode */
        #drawflow {
            background: var(--tblr-bg-surface-secondary, #f6f8fb);
            background-image:
                linear-gradient(rgba(0,0,0,.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0,0,0,.05) 1px, transparent 1px);
            background-size: 20px 20px;
            /* Performance optimization */
            will-change: transform;
            transform: translateZ(0);
        }

        /* Canvas Background - Dark Mode */
        [data-bs-theme="dark"] #drawflow {
            background: var(--tblr-bg-surface-dark, #1e293b);
            background-image:
                linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px);
        }

        /* Drawflow Nodes - Light Mode */
        .drawflow .drawflow-node {
            background: var(--tblr-bg-surface, white);
            border: 2px solid var(--tblr-primary, #206bc4);
            border-radius: 8px;
            padding: 15px;
            min-width: 200px;
            box-shadow: 0 1px 3px rgba(0,0,0,.1);
            /* REMOVE transition for performance */
            /* transition: all 0.2s ease; */
            /* Performance optimization */
            will-change: transform, left, top;
            backface-visibility: hidden;
            -webkit-font-smoothing: subpixel-antialiased;
            transform: translateZ(0);
            pointer-events: all;
        }

        /* Disable Drawflow pan - we don't need it */
        #drawflow {
            cursor: default !important;
        }

        .drawflow .parent-drawflow {
            position: relative !important;
        }

        .drawflow .drawflow-node:hover {
            border-color: var(--tblr-primary-darken, #1a5a9d);
            box-shadow: 0 4px 6px rgba(32,107,196,.15);
            transform: translateY(-1px);
        }

        /* Drawflow Nodes - Dark Mode */
        [data-bs-theme="dark"] .drawflow .drawflow-node {
            background: var(--tblr-bg-surface, #1e293b);
            border-color: var(--tblr-primary, #4299e1);
            box-shadow: 0 1px 3px rgba(0,0,0,.3);
        }

        [data-bs-theme="dark"] .drawflow .drawflow-node:hover {
            border-color: var(--tblr-primary-lighten, #63b3ed);
            box-shadow: 0 4px 6px rgba(66,153,225,.25);
        }

        /* Selected Node */
        .drawflow .drawflow-node.selected {
            border-color: var(--tblr-primary, #206bc4);
            box-shadow: 0 0 0 3px rgba(32,107,196,.2);
        }

        [data-bs-theme="dark"] .drawflow .drawflow-node.selected {
            border-color: var(--tblr-primary, #4299e1);
            box-shadow: 0 0 0 3px rgba(66,153,225,.3);
        }

        /* Node Text - Light Mode */
        .drawflow .drawflow-node .node-title {
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--tblr-body-color, #1e293b);
        }

        .drawflow .drawflow-node .node-type {
            font-size: 12px;
            color: var(--tblr-muted, #64748b);
        }

        /* Node Text - Dark Mode */
        [data-bs-theme="dark"] .drawflow .drawflow-node .node-title {
            color: var(--tblr-body-color, #f1f5f9);
        }

        [data-bs-theme="dark"] .drawflow .drawflow-node .node-type {
            color: var(--tblr-muted, #94a3b8);
        }

        /* Node Container Styles - Advanced Nodes */
        .node-ai-response {
            background: #e3f2fd !important;
            border-left: 4px solid #2196f3 !important;
            border-radius: 4px;
            padding: 10px;
            position: relative;
        }

        .node-product-search {
            background: #e8f5e9 !important;
            border-left: 4px solid #4caf50 !important;
            border-radius: 4px;
            padding: 10px;
            position: relative;
        }

        .node-stock-sorter {
            background: #fff3e0 !important;
            border-left: 4px solid #ff9800 !important;
            border-radius: 4px;
            padding: 10px;
            position: relative;
        }

        .node-condition {
            background: #f3e5f5 !important;
            border-left: 4px solid #9c27b0 !important;
            border-radius: 4px;
            padding: 10px;
            position: relative;
        }

        /* Node Container Styles - Simple Nodes */
        .node-simple {
            background: #f5f5f5 !important;
            border-left: 4px solid #ccc !important;
            border-radius: 4px;
            padding: 10px;
            position: relative;
        }

        /* Dark Mode - Advanced Nodes */
        [data-bs-theme="dark"] .node-ai-response {
            background: #1e3a5f !important;
            border-left-color: #4299e1 !important;
        }

        [data-bs-theme="dark"] .node-product-search {
            background: #1e3d30 !important;
            border-left-color: #48bb78 !important;
        }

        [data-bs-theme="dark"] .node-stock-sorter {
            background: #3d2e1e !important;
            border-left-color: #ed8936 !important;
        }

        [data-bs-theme="dark"] .node-condition {
            background: #2d1e3d !important;
            border-left-color: #9f7aea !important;
        }

        /* Dark Mode - Simple Nodes */
        [data-bs-theme="dark"] .node-simple {
            background: #2d3748 !important;
            border-left-color: #4a5568 !important;
        }

        /* Connection Lines */
        .drawflow .connection .main-path {
            stroke: var(--tblr-primary, #206bc4);
            stroke-width: 3px;
        }

        [data-bs-theme="dark"] .drawflow .connection .main-path {
            stroke: var(--tblr-primary, #4299e1);
        }

        /* Node Palette Items - Light Mode */
        .node-palette-item {
            transition: all 0.2s ease;
            border-bottom: 1px solid var(--tblr-border-color, rgba(98,105,118,.16));
        }

        .node-palette-item:hover {
            background-color: var(--tblr-primary-lt, #e7f1fb) !important;
            border-left: 3px solid var(--tblr-primary, #206bc4);
            padding-left: calc(1.5rem - 3px);
        }

        /* Node Palette Items - Dark Mode */
        [data-bs-theme="dark"] .node-palette-item {
            border-bottom-color: var(--tblr-border-color-dark, rgba(255,255,255,.1));
        }

        [data-bs-theme="dark"] .node-palette-item:hover {
            background-color: rgba(66,153,225,.15) !important;
            border-left: 3px solid var(--tblr-primary, #4299e1);
        }

        /* Category Headers - Dark Mode */
        [data-bs-theme="dark"] .list-group-item.bg-light {
            background-color: var(--tblr-bg-surface-tertiary, #2d3748) !important;
        }

        /* Sticky Sidebar - Dark Mode */
        [data-bs-theme="dark"] .card {
            background-color: var(--tblr-bg-surface, #1e293b);
            border-color: var(--tblr-border-color-dark, rgba(255,255,255,.1));
        }

        /* Avatar badges */
        .avatar.bg-green-lt { background-color: var(--tblr-green-lt, #d3f5df) !important; }
        .avatar.bg-blue-lt { background-color: var(--tblr-blue-lt, #cfe2ff) !important; }
        .avatar.bg-purple-lt { background-color: var(--tblr-purple-lt, #e4d9f7) !important; }

        [data-bs-theme="dark"] .avatar.bg-green-lt { background-color: rgba(32,201,151,.2) !important; }
        [data-bs-theme="dark"] .avatar.bg-blue-lt { background-color: rgba(66,153,225,.2) !important; }
        [data-bs-theme="dark"] .avatar.bg-purple-lt { background-color: rgba(159,122,234,.2) !important; }
    </style>
    @endpush

    @push('scripts')
    <script src="/vendor/drawflow/drawflow.min.js"></script>
    <script>
        // Translation variables
        const trans = {
            editConfig: '{{ __('ai::admin.workflow.edit_config') }}',
            deleteNode: '{{ __('ai::admin.workflow.delete_node') }}',
            deleteNodeConfirm: '{{ __('ai::admin.workflow.delete_node_confirm') }}',
            nodeConfiguration: '{{ __('ai::admin.workflow.node_configuration') }}',
            nodeType: '{{ __('ai::admin.workflow.node_type') }}',
            nodeLabel: '{{ __('ai::admin.workflow.node_label') }}',
            systemPrompt: '{{ __('ai::admin.workflow.system_prompt') }}',
            systemPromptHelp: '{{ __('ai::admin.workflow.system_prompt_help') }}',
            maxTokens: '{{ __('ai::admin.workflow.max_tokens') }}',
            temperature: '{{ __('ai::admin.workflow.temperature') }}',
            configJson: '{{ __('ai::admin.workflow.configuration_json') }}',
            configJsonHelp: '{{ __('ai::admin.workflow.configuration_json_help') }}',
            saveChanges: '{{ __('ai::admin.workflow.save_changes') }}',
            cancel: '{{ __('ai::admin.workflow.cancel') }}',
            configSaved: '{{ __('ai::admin.workflow.config_saved') }}',
            configError: '{{ __('ai::admin.workflow.config_error') }}',
            selectNode: '{{ __('ai::admin.workflow.select_node_to_edit') }}',
            characters: '{{ __('ai::admin.workflow.characters') }}'
        };

        let editor = null;

        /**
         * Node tipine göre stil bilgisini döndür
         */
        function getNodeStyle(nodeType) {
            // Karmaşık config'e sahip node'lar (renkli + belirgin)
            const advancedNodes = {
                'ai_response': {
                    containerClass: 'node-ai-response',
                    btnClass: 'btn-primary',
                    icon: 'fa-robot'
                },
                'product_search': {
                    containerClass: 'node-product-search',
                    btnClass: 'btn-success',
                    icon: 'fa-search'
                },
                'stock_sorter': {
                    containerClass: 'node-stock-sorter',
                    btnClass: 'btn-warning',
                    icon: 'fa-sort'
                },
                'condition': {
                    containerClass: 'node-condition',
                    btnClass: 'btn-purple',
                    icon: 'fa-code-branch'
                }
            };

            // Basit node'lar (gri + soluk)
            const simpleStyle = {
                containerClass: 'node-simple',
                btnClass: 'btn-ghost-secondary',
                icon: 'fa-circle',
                isSimple: true
            };

            return advancedNodes[nodeType] || simpleStyle;
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Drawflow
            const container = document.getElementById('drawflow');
            editor = new Drawflow(container);
            editor.reroute = true;
            editor.reroute_fix_curvature = 0.5;

            // Allow zoom but start at 1
            editor.zoom_max = 2;
            editor.zoom_min = 0.5;
            editor.zoom_value = 0.05;
            editor.zoom_last_value = 1;

            editor.start();

            // Disable default context menu (right-click delete)
            container.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                return false;
            });

            // Set initial position and zoom
            editor.zoom = 1;
            editor.canvas_x = 0;
            editor.canvas_y = 0;

            // Load existing flow if editing
            @if($flowId && !empty($flowData))
                console.log('🔄 Loading existing flow...', @json($flowData));
                loadExistingFlow(@json($flowData));
            @else
                console.log('ℹ️ No existing flow data - flowId:', @json($flowId), 'flowData empty:', @json(empty($flowData)));
            @endif

            // Node palette drag start
            document.querySelectorAll('.node-palette-item').forEach(item => {
                item.addEventListener('dragstart', (e) => {
                    e.dataTransfer.setData('node-type', item.dataset.nodeType);
                    e.dataTransfer.setData('node-name', item.dataset.nodeName);
                    e.dataTransfer.setData('node-class', item.dataset.nodeClass);
                });
            });

            // Canvas drop handler
            container.addEventListener('dragover', (e) => {
                e.preventDefault();
            });

            container.addEventListener('drop', (e) => {
                e.preventDefault();

                const nodeType = e.dataTransfer.getData('node-type');
                const nodeName = e.dataTransfer.getData('node-name');
                const nodeClass = e.dataTransfer.getData('node-class');

                if (!nodeType) return;

                // Calculate position
                const rect = container.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                // Add node to canvas
                const nodeId = 'node_' + Date.now();
                const nodeStyle = getNodeStyle(nodeType);
                const nodeHtml = `
                    <div class="${nodeStyle.containerClass}">
                        <button type="button" class="btn btn-sm btn-ghost-danger delete-node-btn"
                                onclick="deleteNode(event)"
                                style="position: absolute; top: 2px; right: 2px; padding: 2px 6px; font-size: 10px; line-height: 1; z-index: 10;"
                                title="${trans.deleteNode}">
                            <i class="fa fa-times"></i>
                        </button>
                        <div class="node-id" style="font-size: 9px; color: #999; margin-bottom: 3px; font-family: monospace;">
                            <i class="fa ${nodeStyle.icon} me-1"></i>ID: ${nodeId}
                        </div>
                        <div class="node-title" style="font-weight: bold; margin-bottom: 5px; padding-right: 20px;">${nodeName}</div>
                        <div class="node-type" style="font-size: 11px; color: #666; margin-bottom: 8px;">${nodeType}</div>
                        ${!nodeStyle.isSimple ? `
                        <button type="button" class="btn btn-sm ${nodeStyle.btnClass} w-100 edit-node-config-btn"
                                onclick="openNodeConfig(event)"
                                style="font-size: 11px; padding: 4px 8px;">
                            <i class="fa fa-edit me-1"></i>${trans.editConfig}
                        </button>
                        ` : `
                        <div style="font-size: 10px; color: #999; text-align: center; padding: 4px;">
                            <i class="fa fa-info-circle me-1"></i>Basit config (sadece next_node)
                        </div>
                        `}
                    </div>
                `;

                editor.addNode(
                    nodeType,
                    1, // inputs
                    1, // outputs
                    x,
                    y,
                    nodeType,
                    {
                        label: nodeName,
                        class: nodeClass,
                        config: {}
                    },
                    nodeHtml
                );
            });

            // Save flow event listener
            window.addEventListener('save-flow-request', () => {
                const exportData = editor.export();
                @this.saveFlowData(exportData);
            });

            // Context menu for node deletion
            container.addEventListener('contextmenu', (e) => {
                e.preventDefault();
                const nodeElement = e.target.closest('.drawflow-node');
                if (nodeElement) {
                    const nodeId = nodeElement.id.replace('node-', '');
                    if (confirm('Delete this node?')) {
                        editor.removeNodeId('node-' + nodeId);
                    }
                }
            });
        });

        function loadExistingFlow(flowData) {
            console.log('🔄 Loading flow with IMPORT method...');

            // Clear everything
            editor.clear();

            // Build Drawflow import format
            const drawflowData = {
                drawflow: {
                    Home: {
                        data: {}
                    }
                }
            };

            // Track node mapping
            const nodeIdMap = new Map();
            let nodeCounter = 1;

            // Convert nodes to Drawflow format
            flowData.nodes.forEach((node) => {
                const dfId = nodeCounter++;
                nodeIdMap.set(node.id, dfId);

                const posX = node.position?.x || 150;
                const posY = node.position?.y || (100 + (dfId - 1) * 180);
                const nodeStyle = getNodeStyle(node.type);

                drawflowData.drawflow.Home.data[dfId] = {
                    id: dfId,
                    name: node.type,
                    data: {
                        label: node.name,
                        config: node.config || {}
                    },
                    class: node.type,
                    html: `
                        <div class="${nodeStyle.containerClass}">
                            <button type="button" class="btn btn-sm btn-ghost-danger delete-node-btn"
                                    onclick="deleteNode(event)"
                                    style="position: absolute; top: 2px; right: 2px; padding: 2px 6px; font-size: 10px; line-height: 1; z-index: 10;"
                                    title="${trans.deleteNode}">
                                <i class="fa fa-times"></i>
                            </button>
                            <div class="node-id" style="font-size: 9px; color: #999; margin-bottom: 3px; font-family: monospace;">
                                <i class="fa ${nodeStyle.icon} me-1"></i>ID: ${node.id}
                            </div>
                            <div class="node-title" style="font-weight: bold; margin-bottom: 5px; padding-right: 20px;">${node.name}</div>
                            <div class="node-type" style="font-size: 11px; color: #666; margin-bottom: 8px;">${node.type}</div>
                            ${!nodeStyle.isSimple ? `
                            <button type="button" class="btn btn-sm ${nodeStyle.btnClass} w-100 edit-node-config-btn"
                                    onclick="openNodeConfig(event)"
                                    style="font-size: 11px; padding: 4px 8px;">
                                <i class="fa fa-edit me-1"></i>${trans.editConfig}
                            </button>
                            ` : `
                            <div style="font-size: 10px; color: #999; text-align: center; padding: 4px;">
                                <i class="fa fa-info-circle me-1"></i>Basit config
                            </div>
                            `}
                        </div>
                    `,
                    typenode: false,
                    inputs: { input_1: { connections: [] } },
                    outputs: { output_1: { connections: [] } },
                    pos_x: posX,
                    pos_y: posY
                };
            });

            // Add connections to import data
            flowData.edges.forEach(edge => {
                const sourceId = nodeIdMap.get(edge.source);
                const targetId = nodeIdMap.get(edge.target);

                if (sourceId && targetId) {
                    // Add connection to source output
                    drawflowData.drawflow.Home.data[sourceId].outputs.output_1.connections.push({
                        node: targetId.toString(),
                        output: 'input_1'
                    });

                    // Add connection to target input
                    drawflowData.drawflow.Home.data[targetId].inputs.input_1.connections.push({
                        node: sourceId.toString(),
                        input: 'output_1'
                    });

                    console.log(`🔗 Connection: ${edge.source} (${sourceId}) -> ${edge.target} (${targetId})`);
                }
            });

            // Import the complete flow data
            console.log('📦 Importing flow data:', drawflowData);
            editor.import(drawflowData);

            // Center view and reset zoom after import
            setTimeout(() => {
                // Reset zoom to 1
                editor.zoom_reset();

                // Optional: Verify positions after import
                setTimeout(() => {
                    let allCorrect = true;
                    nodeIdMap.forEach((dfId, nodeId) => {
                        const node = flowData.nodes.find(n => n.id === nodeId);
                        if (node && node.position) {
                            const element = document.getElementById(`node-${dfId}`);
                            if (element) {
                                const currentLeft = parseInt(element.style.left);
                                const currentTop = parseInt(element.style.top);

                                if (currentLeft !== node.position.x || currentTop !== node.position.y) {
                                    console.warn(`⚠️ Position mismatch for node ${dfId}: Expected [${node.position.x}, ${node.position.y}], Got [${currentLeft}, ${currentTop}]`);
                                    allCorrect = false;

                                    // Force correct position if needed
                                    element.style.left = `${node.position.x}px`;
                                    element.style.top = `${node.position.y}px`;
                                }
                            }
                        }
                    });

                    if (allCorrect) {
                        console.log('✅ All node positions are correct!');
                    }

                    console.log('✅ Flow loaded successfully: ', flowData.nodes.length, 'nodes,', flowData.edges.length, 'connections');
                }, 200);
            }, 100);
        }

        function clearCanvas() {
            if (confirm('{{ __('ai::admin.workflow.clear_confirm') }}')) {
                editor.clear();
            }
        }

        // Node Config Editor Functions
        let currentEditingNodeId = null;
        let currentEditingNodeData = null;

        function openNodeConfig(event) {
            event.stopPropagation();

            // Find the node element
            const button = event.target.closest('button');
            const nodeElement = button.closest('.drawflow-node');

            if (!nodeElement) {
                console.error('Node element not found');
                return;
            }

            // Get node ID from drawflow
            const nodeId = nodeElement.id.replace('node-', '');
            const nodeData = editor.getNodeFromId(nodeId);

            if (!nodeData) {
                console.error('Node data not found for ID:', nodeId);
                return;
            }

            currentEditingNodeId = nodeId;
            currentEditingNodeData = nodeData;

            console.log('Opening config for node:', nodeId, nodeData);

            // Update offcanvas title
            document.getElementById('config-node-title').textContent =
                `${nodeData.data.label || nodeData.name} - ${trans.nodeConfiguration}`;

            // Generate config form
            generateConfigForm(nodeData);

            // Open offcanvas (native DOM - Bootstrap namespace not available)
            const offcanvasEl = document.getElementById('nodeConfigOffcanvas');
            offcanvasEl.classList.add('show');
            offcanvasEl.style.visibility = 'visible';

            // Add backdrop
            const backdrop = document.createElement('div');
            backdrop.className = 'offcanvas-backdrop fade show';
            backdrop.id = 'nodeConfigBackdrop';
            backdrop.onclick = closeNodeConfig;
            document.body.appendChild(backdrop);
            document.body.classList.add('offcanvas-open');
        }

        function closeNodeConfig() {
            // Close offcanvas
            const offcanvasEl = document.getElementById('nodeConfigOffcanvas');
            offcanvasEl.classList.remove('show');
            offcanvasEl.style.visibility = 'hidden';

            // Remove backdrop
            const backdrop = document.getElementById('nodeConfigBackdrop');
            if (backdrop) {
                backdrop.remove();
            }
            document.body.classList.remove('offcanvas-open');
        }

        function deleteNode(event) {
            event.stopPropagation();

            // Find the node element
            const button = event.target.closest('button');
            const nodeElement = button.closest('.drawflow-node');

            if (!nodeElement) {
                console.error('Node element not found');
                return;
            }

            // Get node ID from drawflow
            const nodeId = nodeElement.id.replace('node-', '');

            // Confirm deletion
            if (confirm(trans.deleteNodeConfirm)) {
                console.log('Deleting node:', nodeId);
                editor.removeNodeId('node-' + nodeId);
            }
        }

        function generateConfigForm(nodeData) {
            const container = document.getElementById('config-form-content');
            const config = nodeData.data.config || {};
            const nodeType = nodeData.name;

            let formHtml = `
                <div class="mb-3">
                    <label class="form-label fw-bold">${trans.nodeType}</label>
                    <input type="text" class="form-control" value="${nodeType}" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">${trans.nodeLabel}</label>
                    <input type="text" class="form-control" id="config-label" value="${nodeData.data.label || ''}">
                </div>
            `;

            // TYPE-SPECIFIC FORM BUILDERS (no JSON typing!)

            if (nodeType === 'ai_response') {
                // AI Response: system_prompt + max_tokens + temperature
                const promptValue = config.system_prompt || '';
                const charCount = promptValue.length;

                formHtml += `
                    <div class="mb-3">
                        <label class="form-label fw-bold required">
                            ${trans.systemPrompt}
                            <span class="badge bg-blue-lt text-blue ms-2" id="char-count">${charCount} ${trans.characters}</span>
                        </label>
                        <textarea class="form-control font-monospace" id="config-system_prompt" rows="20"
                                  style="font-size: 12px; line-height: 1.5;"
                                  oninput="updateCharCount(this)">${escapeHtml(promptValue)}</textarea>
                        <div class="form-hint mt-1">
                            <i class="fa fa-info-circle me-1"></i>
                            ${trans.systemPromptHelp}
                        </div>
                    </div>
                `;

                formHtml += `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">${trans.maxTokens}</label>
                            <input type="number" class="form-control" id="config-max_tokens" value="${config.max_tokens || 500}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">${trans.temperature}</label>
                            <input type="number" step="0.1" min="0" max="2" class="form-control" id="config-temperature" value="${config.temperature || 0.7}">
                        </div>
                    </div>
                `;
            }
            else if (nodeType === 'product_search') {
                // Product Search: search_limit + sort_by_stock + use_meilisearch + no_products_next_node
                formHtml += `
                    <div class="mb-3">
                        <label class="form-label">Arama Limiti (Kaç ürün?)</label>
                        <input type="number" class="form-control" id="config-search_limit" value="${config.search_limit || 5}" min="1" max="20">
                        <div class="form-hint">Kullanıcıya gösterilecek maksimum ürün sayısı</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-check">
                            <input type="checkbox" class="form-check-input" id="config-sort_by_stock" ${config.sort_by_stock ? 'checked' : ''}>
                            <span class="form-check-label">Stoğa Göre Sırala</span>
                        </label>
                        <div class="form-hint">Yüksek stoklu ürünler önce gösterilsin mi?</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-check">
                            <input type="checkbox" class="form-check-input" id="config-use_meilisearch" ${config.use_meilisearch !== false ? 'checked' : ''}>
                            <span class="form-check-label">Meilisearch Kullan (Hızlı Arama)</span>
                        </label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ürün Bulunamazsa İleri Node</label>
                        <input type="text" class="form-control font-monospace" id="config-no_products_next_node" value="${config.no_products_next_node || ''}" placeholder="node_11">
                        <div class="form-hint">Ürün bulunamazsa hangi node'a gitsin? (örn: node_11)</div>
                    </div>
                `;
            }
            else if (nodeType === 'stock_sorter') {
                // Stock Sorter: exclude_out_of_stock + high_stock_threshold
                formHtml += `
                    <div class="mb-3">
                        <label class="form-check">
                            <input type="checkbox" class="form-check-input" id="config-exclude_out_of_stock" ${config.exclude_out_of_stock ? 'checked' : ''}>
                            <span class="form-check-label">Stokta Olmayanları Hariç Tut</span>
                        </label>
                        <div class="form-hint">Stok = 0 olan ürünler gösterilmesin mi?</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Yüksek Stok Eşiği</label>
                        <input type="number" class="form-control" id="config-high_stock_threshold" value="${config.high_stock_threshold || 10}" min="0">
                        <div class="form-hint">Bu değerin üstündeki stoklar "yüksek stok" sayılır</div>
                    </div>
                `;
            }
            else if (nodeType === 'condition') {
                // Condition: condition_type + field + operator + value + true_node + false_node
                formHtml += `
                    <div class="mb-3">
                        <label class="form-label">Koşul Tipi</label>
                        <select class="form-select" id="config-condition_type">
                            <option value="intent" ${config.condition_type === 'intent' ? 'selected' : ''}>Niyet (Intent)</option>
                            <option value="sentiment" ${config.condition_type === 'sentiment' ? 'selected' : ''}>Duygu (Sentiment)</option>
                            <option value="product_count" ${config.condition_type === 'product_count' ? 'selected' : ''}>Ürün Sayısı</option>
                            <option value="custom" ${config.condition_type === 'custom' ? 'selected' : ''}>Özel</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Doğruysa İleri Node</label>
                            <input type="text" class="form-control font-monospace" id="config-true_node" value="${config.true_node || ''}" placeholder="node_8">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Yanlışsa İleri Node</label>
                            <input type="text" class="form-control font-monospace" id="config-false_node" value="${config.false_node || ''}" placeholder="node_9">
                        </div>
                    </div>
                `;
            }
            else if (nodeType === 'welcome' || nodeType === 'history_loader' || nodeType === 'sentiment_detection' ||
                     nodeType === 'category_detection' || nodeType === 'price_query' || nodeType === 'context_builder' ||
                     nodeType === 'contact_request' || nodeType === 'link_generator' || nodeType === 'message_saver' ||
                     nodeType === 'end') {
                // Simple nodes: next_node only
                formHtml += `
                    <div class="mb-3">
                        <label class="form-label">İleri Node</label>
                        <input type="text" class="form-control font-monospace" id="config-next_node" value="${config.next_node || ''}" placeholder="node_2">
                        <div class="form-hint">Bu node bittikten sonra hangi node'a gitsin? (örn: node_2, node_10)</div>
                    </div>
                `;
            }
            else {
                // Fallback: Generic JSON editor for unknown node types
                formHtml += `
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle me-2"></i>
                        Bu node tipi için özel form henüz hazırlanmamış. JSON düzenleyici kullanın.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">${trans.configJson}</label>
                        <textarea class="form-control font-monospace" id="config-json" rows="15"
                                  style="font-size: 12px;">${JSON.stringify(config, null, 2)}</textarea>
                        <div class="form-hint mt-1">
                            <i class="fa fa-info-circle me-1"></i>
                            ${trans.configJsonHelp}
                        </div>
                    </div>
                `;
            }

            container.innerHTML = formHtml;
        }

        function updateCharCount(textarea) {
            const charCount = textarea.value.length;
            document.getElementById('char-count').textContent = `${charCount} ${trans.characters}`;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Save node config
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('save-node-config-btn').addEventListener('click', function() {
                if (!currentEditingNodeId || !currentEditingNodeData) {
                    alert('No node selected');
                    return;
                }

                try {
                    // Get updated label
                    const newLabel = document.getElementById('config-label')?.value || currentEditingNodeData.data.label;

                    // Get updated config (FORM → JSON conversion)
                    let newConfig = {};
                    const nodeType = currentEditingNodeData.name;

                    if (nodeType === 'ai_response') {
                        // AI Response
                        newConfig = {
                            ...currentEditingNodeData.data.config,
                            system_prompt: document.getElementById('config-system_prompt')?.value || '',
                            max_tokens: parseInt(document.getElementById('config-max_tokens')?.value) || 500,
                            temperature: parseFloat(document.getElementById('config-temperature')?.value) || 0.7
                        };
                    }
                    else if (nodeType === 'product_search') {
                        // Product Search
                        newConfig = {
                            search_limit: parseInt(document.getElementById('config-search_limit')?.value) || 5,
                            sort_by_stock: document.getElementById('config-sort_by_stock')?.checked || false,
                            use_meilisearch: document.getElementById('config-use_meilisearch')?.checked || false,
                            no_products_next_node: document.getElementById('config-no_products_next_node')?.value || ''
                        };
                    }
                    else if (nodeType === 'stock_sorter') {
                        // Stock Sorter
                        newConfig = {
                            exclude_out_of_stock: document.getElementById('config-exclude_out_of_stock')?.checked || false,
                            high_stock_threshold: parseInt(document.getElementById('config-high_stock_threshold')?.value) || 10
                        };
                    }
                    else if (nodeType === 'condition') {
                        // Condition
                        newConfig = {
                            condition_type: document.getElementById('config-condition_type')?.value || 'intent',
                            true_node: document.getElementById('config-true_node')?.value || '',
                            false_node: document.getElementById('config-false_node')?.value || ''
                        };
                    }
                    else if (nodeType === 'welcome' || nodeType === 'history_loader' || nodeType === 'sentiment_detection' ||
                             nodeType === 'category_detection' || nodeType === 'price_query' || nodeType === 'context_builder' ||
                             nodeType === 'contact_request' || nodeType === 'link_generator' || nodeType === 'message_saver' ||
                             nodeType === 'end') {
                        // Simple nodes: next_node only
                        newConfig = {
                            next_node: document.getElementById('config-next_node')?.value || ''
                        };
                    }
                    else {
                        // Fallback: Generic JSON parsing
                        const jsonText = document.getElementById('config-json')?.value || '{}';
                        newConfig = JSON.parse(jsonText);
                    }

                    // Update node data in editor
                    currentEditingNodeData.data.label = newLabel;
                    currentEditingNodeData.data.config = newConfig;

                    // Update node HTML
                    updateNodeHtml(currentEditingNodeId, newLabel, currentEditingNodeData.name);

                    console.log('Config saved for node:', currentEditingNodeId, newConfig);

                    // Close offcanvas
                    closeNodeConfig();

                    // Show success message
                    alert(trans.configSaved);

                } catch (error) {
                    console.error('Error saving config:', error);
                    alert(trans.configError + ': ' + error.message);
                }
            });
        });

        function updateNodeHtml(nodeId, newLabel, nodeType) {
            const nodeElement = document.getElementById(`node-${nodeId}`);
            if (nodeElement) {
                const contentDiv = nodeElement.querySelector('.drawflow_content_node');
                if (contentDiv) {
                    const nodeStyle = getNodeStyle(nodeType);

                    contentDiv.innerHTML = `
                        <div class="${nodeStyle.containerClass}">
                            <button type="button" class="btn btn-sm btn-ghost-danger delete-node-btn"
                                    onclick="deleteNode(event)"
                                    style="position: absolute; top: 2px; right: 2px; padding: 2px 6px; font-size: 10px; line-height: 1; z-index: 10;"
                                    title="${trans.deleteNode}">
                                <i class="fa fa-times"></i>
                            </button>
                            <div class="node-id" style="font-size: 9px; color: #999; margin-bottom: 3px; font-family: monospace;">ID: node-${nodeId}</div>
                            <div class="node-title" style="font-weight: bold; margin-bottom: 5px; padding-right: 20px;">${escapeHtml(newLabel)}</div>
                            <div class="node-type" style="font-size: 11px; color: #666; margin-bottom: 8px;">
                                ${nodeStyle.icon ? `<i class="fa ${nodeStyle.icon} me-1"></i>` : ''}${nodeType}
                            </div>
                            ${!nodeStyle.isSimple ? `
                            <button type="button" class="btn btn-sm ${nodeStyle.btnClass} w-100 edit-node-config-btn"
                                    onclick="openNodeConfig(event)"
                                    style="font-size: 11px; padding: 4px 8px;">
                                <i class="fa fa-edit me-1"></i>${trans.editConfig}
                            </button>
                            ` : `
                            <div style="padding: 4px 8px; text-align: center; font-size: 11px; color: #999; background: #fafafa; border-radius: 4px;">
                                <i class="fa fa-info-circle me-1"></i>Basit config (sadece next_node)
                            </div>
                            `}
                        </div>
                    `;
                }
            }
        }
    </script>
    @endpush

    <!-- Test Flow Modal -->
    <div class="modal fade" id="testFlowModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-flask me-2"></i>
                        {{ __('ai::admin.workflow.test_flow_title') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Chat Interface -->
                    <div class="chat-container" style="height: 400px; overflow-y: auto; border: 1px solid var(--tblr-border-color); border-radius: 8px; padding: 15px;">
                        <div id="test-chat-messages">
                            @if(isset($testMessages))
                                @foreach($testMessages as $msg)
                                <div class="mb-3 {{ $msg['role'] == 'user' ? 'text-end' : '' }}">
                                    <div class="d-inline-block p-3 rounded-3 {{ $msg['role'] == 'user' ? 'bg-primary text-white' : 'bg-light' }}" style="max-width: 70%;">
                                        <small class="d-block mb-1 {{ $msg['role'] == 'user' ? 'text-white-50' : 'text-muted' }}">
                                            {{ $msg['role'] == 'user' ? 'You' : 'Assistant' }}
                                        </small>
                                        {{ $msg['content'] }}
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="text-center text-muted py-5">
                                    <i class="fa fa-comments fa-3x mb-3"></i>
                                    <p>{{ __('ai::admin.workflow.test_instructions') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Input Area -->
                    <div class="mt-3">
                        <div class="input-group">
                            <input type="text"
                                   class="form-control"
                                   placeholder="{{ __('ai::admin.workflow.test_message_placeholder') }}"
                                   wire:model="testMessage"
                                   wire:keydown.enter="sendTestMessage"
                                   id="test-message-input">
                            <button class="btn btn-primary" wire:click="sendTestMessage" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="sendTestMessage">
                                    <i class="fa fa-paper-plane"></i>
                                </span>
                                <span wire:loading wire:target="sendTestMessage">
                                    <i class="fa fa-spinner fa-spin"></i>
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- Debug Panel -->
                    <div class="mt-3">
                        <div class="accordion" id="debugAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#debugPanel">
                                        <i class="fa fa-bug me-2"></i> Debug Information
                                    </button>
                                </h2>
                                <div id="debugPanel" class="accordion-collapse collapse" data-bs-parent="#debugAccordion">
                                    <div class="accordion-body bg-dark text-white" style="font-family: monospace; font-size: 12px;">
                                        <pre id="debug-output">{{ json_encode($debugInfo ?? [], JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost-secondary" data-bs-dismiss="modal">
                        {{ __('ai::admin.workflow.close') }}
                    </button>
                    <button type="button" class="btn btn-warning" wire:click="resetTestSession">
                        <i class="fa fa-refresh me-1"></i>
                        {{ __('ai::admin.workflow.reset_session') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Node Config Editor Offcanvas -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="nodeConfigOffcanvas" style="width: 600px;">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">
                <i class="fa fa-cog me-2"></i>
                <span id="config-node-title">{{ __('ai::admin.workflow.node_configuration') }}</span>
            </h5>
            <button type="button" class="btn-close" onclick="closeNodeConfig()"></button>
        </div>
        <div class="offcanvas-body">
            <div id="config-form-content">
                <!-- Dynamic content will be loaded here -->
                <div class="text-center text-muted py-5">
                    <i class="fa fa-cog fa-3x mb-3"></i>
                    <p>{{ __('ai::admin.workflow.select_node_to_edit') }}</p>
                </div>
            </div>
        </div>
        <div class="offcanvas-footer border-top p-3">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-ghost-secondary flex-fill" onclick="closeNodeConfig()">
                    <i class="fa fa-times me-1"></i>
                    {{ __('ai::admin.workflow.cancel') }}
                </button>
                <button type="button" class="btn btn-primary flex-fill" id="save-node-config-btn">
                    <i class="fa fa-save me-1"></i>
                    {{ __('ai::admin.workflow.save_changes') }}
                </button>
            </div>
        </div>
    </div>
</div>

