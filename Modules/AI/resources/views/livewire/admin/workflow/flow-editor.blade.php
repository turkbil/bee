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
        let editor = null;

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
                const nodeHtml = `
                    <div class="node-title">${nodeName}</div>
                    <div class="node-type">${nodeType}</div>
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

                drawflowData.drawflow.Home.data[dfId] = {
                    id: dfId,
                    name: node.type,
                    data: {
                        label: node.name,
                        config: node.config || {}
                    },
                    class: node.type,
                    html: `
                        <div style="padding: 10px;">
                            <div class="node-title" style="font-weight: bold; margin-bottom: 5px;">${node.name}</div>
                            <div class="node-type" style="font-size: 11px; color: #666;">${node.type}</div>
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
</div>
