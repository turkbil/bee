<div>
    <div class="page-header d-print-none">
        <div class="container-fluid">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <i class="ti ti-git-branch me-2"></i>
                        {{ $flowId ? 'Edit Flow' : 'Create New Flow' }}
                    </h2>
                    <div class="text-muted mt-1">Design your conversation flow with drag & drop</div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('admin.ai.workflow.flows.index') }}" class="btn btn-ghost-secondary me-2">
                        <i class="ti ti-x me-1"></i>
                        Cancel
                    </a>
                    <button wire:click="saveFlow" class="btn btn-primary" id="save-flow-btn">
                        <i class="ti ti-device-floppy me-1"></i>
                        Save Flow
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
                                    <label class="form-label required">Flow Name</label>
                                    <input type="text" wire:model.defer="flowName" class="form-control" placeholder="e.g. Customer Support Flow">
                                    @error('flowName') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Priority</label>
                                    <input type="number" wire:model.defer="priority" class="form-control" min="0" max="999">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Status</label>
                                    <label class="form-check form-switch mt-2">
                                        <input type="checkbox" wire:model.defer="isActive" class="form-check-input">
                                        <span class="form-check-label">Active</span>
                                    </label>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Description</label>
                                    <textarea wire:model.defer="flowDescription" class="form-control" rows="2" placeholder="Describe what this flow does..."></textarea>
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
                                <i class="ti ti-box me-2"></i>
                                Node Library
                            </h3>
                            <div class="card-subtitle text-muted small mt-1">
                                Tenant: {{ $currentTenantId == 2 ? 'İxtif.com' : 'Tenant A' }}
                            </div>
                        </div>
                        <div class="card-body p-0">
                            @foreach($nodesByCategory as $category => $nodes)
                                <div class="list-group list-group-flush">
                                    <!-- Category Header -->
                                    <div class="list-group-item bg-light">
                                        <div class="fw-bold text-uppercase small">
                                            <i class="ti ti-folder me-1"></i>
                                            @if($category === 'common')
                                                <span class="badge bg-green-lt text-green">Global Functions</span>
                                            @elseif($category === 'ecommerce')
                                                <span class="badge bg-blue-lt text-blue">E-Commerce</span>
                                            @elseif($category === 'communication')
                                                <span class="badge bg-purple-lt text-purple">Communication</span>
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
                                                    <i class="ti ti-circle-dot"></i>
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
                                    <i class="ti ti-inbox fs-2 mb-2 d-block"></i>
                                    No nodes available
                                </div>
                            @endif
                        </div>
                        <div class="card-footer text-muted small">
                            <i class="ti ti-info-circle me-1"></i>
                            Drag nodes to canvas
                        </div>
                    </div>
                </div>

                <!-- Main Canvas: Drawflow Editor -->
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <h3 class="card-title">Flow Canvas</h3>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-ghost-secondary" onclick="editor.zoom_in()">
                                        <i class="ti ti-zoom-in"></i>
                                    </button>
                                    <button class="btn btn-sm btn-ghost-secondary" onclick="editor.zoom_out()">
                                        <i class="ti ti-zoom-out"></i>
                                    </button>
                                    <button class="btn btn-sm btn-ghost-secondary" onclick="editor.zoom_reset()">
                                        <i class="ti ti-zoom-reset"></i>
                                    </button>
                                    <button class="btn btn-sm btn-ghost-danger" onclick="clearCanvas()">
                                        <i class="ti ti-trash"></i> Clear
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div id="drawflow" style="height: 600px; position: relative;"></div>
                        </div>
                        <div class="card-footer text-muted small">
                            <i class="ti ti-hand-move me-1"></i>
                            Drag canvas to pan • Mouse wheel to zoom • Right-click node to delete
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="/vendor/drawflow/drawflow.min.css">
    <style>
        #drawflow {
            background: #f6f8fb;
            background-image:
                linear-gradient(rgba(0,0,0,.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0,0,0,.05) 1px, transparent 1px);
            background-size: 20px 20px;
        }
        .drawflow .drawflow-node {
            background: white;
            border: 2px solid #206bc4;
            border-radius: 8px;
            padding: 15px;
            min-width: 200px;
        }
        .drawflow .drawflow-node.selected {
            border-color: #1e40af;
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.2);
        }
        .drawflow .drawflow-node .node-title {
            font-weight: 600;
            margin-bottom: 5px;
            color: #1e293b;
        }
        .drawflow .drawflow-node .node-type {
            font-size: 12px;
            color: #64748b;
        }
        .drawflow .connection .main-path {
            stroke: #206bc4;
            stroke-width: 3px;
        }
        .node-palette-item:hover {
            background-color: #f1f5f9;
        }
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
            editor.start();

            // Load existing flow if editing
            @if($flowId && !empty($flowData))
                loadExistingFlow(@json($flowData));
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
            // Convert Laravel format to Drawflow format
            const drawflowData = {
                drawflow: {
                    Home: {
                        data: {}
                    }
                }
            };

            flowData.nodes.forEach((node, index) => {
                const nodeId = index + 1;
                drawflowData.drawflow.Home.data[nodeId] = {
                    id: nodeId,
                    name: node.type,
                    data: {
                        label: node.name,
                        class: node.class,
                        config: node.config
                    },
                    class: node.type,
                    html: `
                        <div class="node-title">${node.name}</div>
                        <div class="node-type">${node.type}</div>
                    `,
                    typenode: false,
                    inputs: { input_1: { connections: [] } },
                    outputs: { output_1: { connections: [] } },
                    pos_x: node.position.x,
                    pos_y: node.position.y
                };
            });

            // Add edges/connections
            flowData.edges.forEach(edge => {
                const sourceIndex = flowData.nodes.findIndex(n => n.id === edge.source) + 1;
                const targetIndex = flowData.nodes.findIndex(n => n.id === edge.target) + 1;

                if (sourceIndex && targetIndex) {
                    drawflowData.drawflow.Home.data[sourceIndex].outputs.output_1.connections.push({
                        node: String(targetIndex),
                        output: 'input_1'
                    });
                }
            });

            editor.import(drawflowData);
        }

        function clearCanvas() {
            if (confirm('Clear entire canvas? This cannot be undone.')) {
                editor.clear();
            }
        }
    </script>
    @endpush
</div>
