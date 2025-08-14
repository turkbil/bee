@extends('admin.layout')

@section('title', 'AI Integration Settings - Universal Input System V3')

@section('content')
<div class="container-fluid py-4" x-data="integrationManager()">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">AI Integration Settings</h1>
                    <p class="text-muted">Module integration configuration panel with real-time monitoring and health checks</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary" @click="testAllConnections()" :disabled="testing">
                        <i class="fas fa-flask" :class="{'fa-spin': testing}"></i>
                        Test All Connections
                    </button>
                    <button class="btn btn-outline-primary" @click="refreshSettings()" :disabled="loading">
                        <i class="fas fa-sync-alt" :class="{'fa-spin': loading}"></i>
                        Refresh
                    </button>
                    <button class="btn btn-primary" @click="saveAllSettings()" :disabled="saving">
                        <i class="fas fa-save"></i>
                        Save Settings
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- System Status Overview -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Active Integrations</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" x-text="systemStatus.active_integrations"></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-plug fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Healthy Modules</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" x-text="systemStatus.healthy_modules"></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-heartbeat fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Updates</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" x-text="systemStatus.pending_updates"></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Failed Connections</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" x-text="systemStatus.failed_connections"></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Integration Categories Tabs -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link" :class="{'active': activeTab === 'ai-providers'}" 
                       @click="activeTab = 'ai-providers'" href="#ai-providers" role="tab">
                        <i class="fas fa-brain mr-2"></i>AI Providers
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" :class="{'active': activeTab === 'modules'}" 
                       @click="activeTab = 'modules'" href="#modules" role="tab">
                        <i class="fas fa-cube mr-2"></i>Module Integrations
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" :class="{'active': activeTab === 'external'}" 
                       @click="activeTab = 'external'" href="#external" role="tab">
                        <i class="fas fa-link mr-2"></i>External Services
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" :class="{'active': activeTab === 'webhooks'}" 
                       @click="activeTab = 'webhooks'" href="#webhooks" role="tab">
                        <i class="fas fa-satellite-dish mr-2"></i>Webhooks
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" :class="{'active': activeTab === 'monitoring'}" 
                       @click="activeTab = 'monitoring'" href="#monitoring" role="tab">
                        <i class="fas fa-chart-line mr-2"></i>Monitoring
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <!-- AI Providers Tab -->
            <div x-show="activeTab === 'ai-providers'" class="tab-pane">
                <div class="row">
                    <template x-for="provider in aiProviders" :key="provider.id">
                        <div class="col-xl-4 col-lg-6 mb-4">
                            <div class="card border-left-primary h-100">
                                <div class="card-header py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="m-0 font-weight-bold text-primary" x-text="provider.name"></h6>
                                        <div class="d-flex align-items-center">
                                            <div class="custom-control custom-switch mr-3">
                                                <input type="checkbox" class="custom-control-input" 
                                                       x-model="provider.enabled" :id="`provider-${provider.id}`"
                                                       @change="toggleProvider(provider)">
                                                <label class="custom-control-label" :for="`provider-${provider.id}`"></label>
                                            </div>
                                            <span class="badge" :class="getStatusBadge(provider.status)" x-text="provider.status"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="form-label">API Key</label>
                                        <div class="input-group">
                                            <input :type="provider.showKey ? 'text' : 'password'" 
                                                   class="form-control" x-model="provider.api_key" 
                                                   :placeholder="`Enter ${provider.name} API Key`">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" 
                                                        @click="provider.showKey = !provider.showKey">
                                                    <i :class="provider.showKey ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group" x-show="provider.supports_custom_endpoint">
                                        <label class="form-label">Custom Endpoint</label>
                                        <input type="url" class="form-control" x-model="provider.custom_endpoint" 
                                               placeholder="https://api.example.com/v1">
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Model Selection</label>
                                        <select class="form-control" x-model="provider.default_model">
                                            <template x-for="model in provider.available_models" :key="model.id">
                                                <option :value="model.id" x-text="model.name"></option>
                                            </template>
                                        </select>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Rate Limit</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" x-model="provider.rate_limit" 
                                                           min="1" max="10000">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">req/min</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Timeout</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" x-model="provider.timeout" 
                                                           min="5" max="300">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">sec</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" 
                                                   x-model="provider.fallback_enabled" :id="`fallback-${provider.id}`">
                                            <label class="custom-control-label" :for="`fallback-${provider.id}`">
                                                Enable as fallback provider
                                            </label>
                                        </div>
                                    </div>

                                    <div class="mt-3 pt-3 border-top">
                                        <div class="row text-center">
                                            <div class="col-4">
                                                <div class="font-weight-bold text-primary" x-text="provider.usage_today"></div>
                                                <div class="text-xs text-muted">Today</div>
                                            </div>
                                            <div class="col-4">
                                                <div class="font-weight-bold text-success" x-text="provider.success_rate + '%'"></div>
                                                <div class="text-xs text-muted">Success</div>
                                            </div>
                                            <div class="col-4">
                                                <div class="font-weight-bold text-info" x-text="provider.avg_response + 'ms'"></div>
                                                <div class="text-xs text-muted">Avg Time</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-between">
                                        <button class="btn btn-sm btn-outline-info" @click="testProvider(provider)" 
                                                :disabled="provider.testing">
                                            <i class="fas fa-vial" :class="{'fa-spin': provider.testing}"></i>
                                            Test Connection
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary" @click="viewProviderLogs(provider)">
                                            <i class="fas fa-list"></i>
                                            View Logs
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Module Integrations Tab -->
            <div x-show="activeTab === 'modules'" class="tab-pane">
                <div class="row">
                    <template x-for="module in moduleIntegrations" :key="module.id">
                        <div class="col-xl-6 mb-4">
                            <div class="card border-left-success h-100">
                                <div class="card-header py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle bg-success mr-3">
                                                <i :class="getModuleIcon(module.type)"></i>
                                            </div>
                                            <div>
                                                <h6 class="m-0 font-weight-bold text-gray-900" x-text="module.name"></h6>
                                                <small class="text-muted" x-text="module.description"></small>
                                            </div>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" 
                                                   x-model="module.ai_enabled" :id="`module-${module.id}`"
                                                   @change="toggleModuleIntegration(module)">
                                            <label class="custom-control-label" :for="`module-${module.id}`"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body" x-show="module.ai_enabled">
                                    <div class="form-group">
                                        <label class="form-label">AI Features</label>
                                        <div class="row">
                                            <template x-for="feature in module.available_features" :key="feature.id">
                                                <div class="col-md-6 mb-2">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" 
                                                               x-model="feature.enabled" :id="`feature-${feature.id}`"
                                                               @change="updateModuleFeature(module, feature)">
                                                        <label class="custom-control-label" :for="`feature-${feature.id}`">
                                                            <span x-text="feature.name"></span>
                                                            <small class="text-muted d-block" x-text="feature.description"></small>
                                                        </label>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Auto-processing Rules</label>
                                        <select class="form-control" x-model="module.auto_processing">
                                            <option value="disabled">Disabled</option>
                                            <option value="manual">Manual Trigger Only</option>
                                            <option value="on_create">Auto-process on Create</option>
                                            <option value="on_update">Auto-process on Update</option>
                                            <option value="on_publish">Auto-process on Publish</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Processing Queue</label>
                                        <select class="form-control" x-model="module.queue_name">
                                            <option value="default">Default Queue</option>
                                            <option value="high">High Priority</option>
                                            <option value="low">Low Priority</option>
                                            <option value="ai-processing">AI Processing Queue</option>
                                        </select>
                                    </div>

                                    <div class="mt-3 pt-3 border-top">
                                        <div class="row text-center">
                                            <div class="col-3">
                                                <div class="font-weight-bold text-primary" x-text="module.processed_today"></div>
                                                <div class="text-xs text-muted">Processed</div>
                                            </div>
                                            <div class="col-3">
                                                <div class="font-weight-bold text-success" x-text="module.success_rate + '%'"></div>
                                                <div class="text-xs text-muted">Success</div>
                                            </div>
                                            <div class="col-3">
                                                <div class="font-weight-bold text-warning" x-text="module.queue_size"></div>
                                                <div class="text-xs text-muted">In Queue</div>
                                            </div>
                                            <div class="col-3">
                                                <div class="font-weight-bold text-info" x-text="module.avg_time + 's'"></div>
                                                <div class="text-xs text-muted">Avg Time</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer" x-show="module.ai_enabled">
                                    <div class="d-flex justify-content-between">
                                        <button class="btn btn-sm btn-outline-success" @click="testModuleIntegration(module)">
                                            <i class="fas fa-play"></i>
                                            Test Integration
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary" @click="viewModuleSettings(module)">
                                            <i class="fas fa-cog"></i>
                                            Advanced Settings
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- External Services Tab -->
            <div x-show="activeTab === 'external'" class="tab-pane">
                <div class="row">
                    <template x-for="service in externalServices" :key="service.id">
                        <div class="col-xl-4 col-lg-6 mb-4">
                            <div class="card border-left-info h-100">
                                <div class="card-header py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle bg-info mr-3">
                                                <i :class="getServiceIcon(service.type)"></i>
                                            </div>
                                            <h6 class="m-0 font-weight-bold text-gray-900" x-text="service.name"></h6>
                                        </div>
                                        <span class="badge" :class="getStatusBadge(service.status)" x-text="service.status"></span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="form-label">Service URL</label>
                                        <input type="url" class="form-control" x-model="service.url" 
                                               :placeholder="`Enter ${service.name} URL`">
                                    </div>

                                    <div class="form-group" x-show="service.requires_auth">
                                        <label class="form-label">Authentication</label>
                                        <select class="form-control" x-model="service.auth_type">
                                            <option value="none">No Authentication</option>
                                            <option value="api_key">API Key</option>
                                            <option value="bearer">Bearer Token</option>
                                            <option value="basic">Basic Auth</option>
                                            <option value="oauth2">OAuth 2.0</option>
                                        </select>
                                    </div>

                                    <div class="form-group" x-show="service.auth_type === 'api_key'">
                                        <label class="form-label">API Key</label>
                                        <div class="input-group">
                                            <input :type="service.showAuth ? 'text' : 'password'" 
                                                   class="form-control" x-model="service.api_key">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" 
                                                        @click="service.showAuth = !service.showAuth">
                                                    <i :class="service.showAuth ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group" x-show="service.auth_type === 'bearer'">
                                        <label class="form-label">Bearer Token</label>
                                        <input type="password" class="form-control" x-model="service.bearer_token">
                                    </div>

                                    <div class="row" x-show="service.auth_type === 'basic'">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Username</label>
                                                <input type="text" class="form-control" x-model="service.username">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Password</label>
                                                <input type="password" class="form-control" x-model="service.password">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" 
                                                   x-model="service.enabled" :id="`service-${service.id}`">
                                            <label class="custom-control-label" :for="`service-${service.id}`">
                                                Enable this service
                                            </label>
                                        </div>
                                    </div>

                                    <div class="mt-3 pt-3 border-top">
                                        <div class="row text-center">
                                            <div class="col-4">
                                                <div class="font-weight-bold text-primary" x-text="service.requests_today"></div>
                                                <div class="text-xs text-muted">Requests</div>
                                            </div>
                                            <div class="col-4">
                                                <div class="font-weight-bold text-success" x-text="service.uptime + '%'"></div>
                                                <div class="text-xs text-muted">Uptime</div>
                                            </div>
                                            <div class="col-4">
                                                <div class="font-weight-bold text-info" x-text="service.response_time + 'ms'"></div>
                                                <div class="text-xs text-muted">Response</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-between">
                                        <button class="btn btn-sm btn-outline-info" @click="testService(service)" 
                                                :disabled="service.testing">
                                            <i class="fas fa-satellite-dish" :class="{'fa-spin': service.testing}"></i>
                                            Test Connection
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary" @click="configureService(service)">
                                            <i class="fas fa-cogs"></i>
                                            Configure
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Webhooks Tab -->
            <div x-show="activeTab === 'webhooks'" class="tab-pane">
                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="font-weight-bold text-gray-900">Webhook Configurations</h5>
                            <button class="btn btn-primary" @click="createWebhook()">
                                <i class="fas fa-plus"></i>
                                Add New Webhook
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <template x-for="webhook in webhooks" :key="webhook.id">
                        <div class="col-xl-6 mb-4">
                            <div class="card border-left-warning h-100">
                                <div class="card-header py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="m-0 font-weight-bold text-gray-900" x-text="webhook.name"></h6>
                                        <div class="d-flex align-items-center">
                                            <div class="custom-control custom-switch mr-3">
                                                <input type="checkbox" class="custom-control-input" 
                                                       x-model="webhook.enabled" :id="`webhook-${webhook.id}`">
                                                <label class="custom-control-label" :for="`webhook-${webhook.id}`"></label>
                                            </div>
                                            <span class="badge" :class="getStatusBadge(webhook.status)" x-text="webhook.status"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="form-label">Endpoint URL</label>
                                        <input type="url" class="form-control" x-model="webhook.url" 
                                               placeholder="https://example.com/webhook">
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Events</label>
                                        <div class="row">
                                            <template x-for="event in webhook.available_events" :key="event.id">
                                                <div class="col-md-6 mb-2">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" 
                                                               x-model="event.enabled" :id="`event-${event.id}`">
                                                        <label class="custom-control-label" :for="`event-${event.id}`">
                                                            <span x-text="event.name"></span>
                                                            <small class="text-muted d-block" x-text="event.description"></small>
                                                        </label>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">HTTP Method</label>
                                        <select class="form-control" x-model="webhook.method">
                                            <option value="POST">POST</option>
                                            <option value="PUT">PUT</option>
                                            <option value="PATCH">PATCH</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Secret Token (Optional)</label>
                                        <input type="password" class="form-control" x-model="webhook.secret" 
                                               placeholder="Used for signature verification">
                                    </div>

                                    <div class="mt-3 pt-3 border-top">
                                        <div class="row text-center">
                                            <div class="col-3">
                                                <div class="font-weight-bold text-primary" x-text="webhook.deliveries_today"></div>
                                                <div class="text-xs text-muted">Deliveries</div>
                                            </div>
                                            <div class="col-3">
                                                <div class="font-weight-bold text-success" x-text="webhook.success_rate + '%'"></div>
                                                <div class="text-xs text-muted">Success</div>
                                            </div>
                                            <div class="col-3">
                                                <div class="font-weight-bold text-warning" x-text="webhook.failed_deliveries"></div>
                                                <div class="text-xs text-muted">Failed</div>
                                            </div>
                                            <div class="col-3">
                                                <div class="font-weight-bold text-info" x-text="webhook.last_delivery"></div>
                                                <div class="text-xs text-muted">Last Sent</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-between">
                                        <button class="btn btn-sm btn-outline-warning" @click="testWebhook(webhook)" 
                                                :disabled="webhook.testing">
                                            <i class="fas fa-paper-plane" :class="{'fa-spin': webhook.testing}"></i>
                                            Test Webhook
                                        </button>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary" @click="viewWebhookLogs(webhook)">
                                                <i class="fas fa-history"></i>
                                                Logs
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" @click="deleteWebhook(webhook)">
                                                <i class="fas fa-trash"></i>
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Monitoring Tab -->
            <div x-show="activeTab === 'monitoring'" class="tab-pane">
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Real-time Integration Health Monitor</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="healthChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-4 mb-4">
                        <div class="card shadow h-100">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">System Alerts</h6>
                            </div>
                            <div class="card-body">
                                <div class="alerts-container" style="max-height: 300px; overflow-y: auto;">
                                    <template x-for="alert in systemAlerts" :key="alert.id">
                                        <div class="alert" :class="getAlertClass(alert.severity)" role="alert">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <strong x-text="alert.title"></strong>
                                                    <p class="mb-1 text-sm" x-text="alert.message"></p>
                                                    <small class="text-muted" x-text="formatTime(alert.timestamp)"></small>
                                                </div>
                                                <button type="button" class="close" @click="dismissAlert(alert.id)">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                    
                                    <div x-show="systemAlerts.length === 0" class="text-center py-4">
                                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                        <h6 class="text-gray-600">All Systems Operational</h6>
                                        <p class="text-gray-500 text-sm">No alerts or issues detected</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-8 mb-4">
                        <div class="card shadow h-100">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Integration Performance Metrics</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Integration</th>
                                                <th>Status</th>
                                                <th>Response Time</th>
                                                <th>Success Rate</th>
                                                <th>Last Check</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="metric in performanceMetrics" :key="metric.id">
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="icon-circle mr-3" :class="getIntegrationIconBg(metric.type)">
                                                                <i :class="getIntegrationIcon(metric.type)"></i>
                                                            </div>
                                                            <div>
                                                                <div class="font-weight-bold" x-text="metric.name"></div>
                                                                <div class="text-xs text-muted" x-text="metric.type"></div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge" :class="getStatusBadge(metric.status)" x-text="metric.status"></span>
                                                    </td>
                                                    <td>
                                                        <div class="font-weight-bold" x-text="metric.response_time + 'ms'"></div>
                                                        <div class="progress progress-sm mt-1">
                                                            <div class="progress-bar" :class="getResponseTimeColor(metric.response_time)" 
                                                                 :style="`width: ${getResponseTimePercentage(metric.response_time)}%`"></div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="font-weight-bold" x-text="metric.success_rate + '%'"></div>
                                                        <div class="progress progress-sm mt-1">
                                                            <div class="progress-bar bg-success" :style="`width: ${metric.success_rate}%`"></div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <small x-text="formatTime(metric.last_check)"></small>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button class="btn btn-sm btn-outline-info" @click="checkHealth(metric)">
                                                                <i class="fas fa-heartbeat"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-primary" @click="viewLogs(metric)">
                                                                <i class="fas fa-list"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Save Button -->
    <div class="fixed-bottom p-3" x-show="hasUnsavedChanges">
        <div class="d-flex justify-content-end">
            <div class="bg-white shadow-lg rounded p-3">
                <span class="text-muted mr-3">You have unsaved changes</span>
                <button class="btn btn-outline-secondary mr-2" @click="discardChanges()">Discard</button>
                <button class="btn btn-primary" @click="saveAllSettings()" :disabled="saving">
                    <i class="fas fa-save" :class="{'fa-spin': saving}"></i>
                    Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.icon-circle {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
}

.progress-sm {
    height: 0.375rem;
}

.border-left-primary { border-left: 4px solid #4e73df !important; }
.border-left-success { border-left: 4px solid #1cc88a !important; }
.border-left-info { border-left: 4px solid #36b9cc !important; }
.border-left-warning { border-left: 4px solid #f6c23e !important; }
.border-left-danger { border-left: 4px solid #e74a3b !important; }

.card {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
}

.nav-tabs .nav-link {
    border: 1px solid transparent;
    border-top-left-radius: 0.35rem;
    border-top-right-radius: 0.35rem;
}

.nav-tabs .nav-link.active {
    color: #4e73df;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
}

.fixed-bottom {
    z-index: 1030;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.alert {
    animation: fadeIn 0.3s ease-out;
}
</style>

<script>
function integrationManager() {
    return {
        // UI State
        activeTab: 'ai-providers',
        loading: false,
        saving: false,
        testing: false,
        hasUnsavedChanges: false,

        // System Status
        systemStatus: {
            active_integrations: 12,
            healthy_modules: 8,
            pending_updates: 2,
            failed_connections: 1
        },

        // Data Collections
        aiProviders: [],
        moduleIntegrations: [],
        externalServices: [],
        webhooks: [],
        systemAlerts: [],
        performanceMetrics: [],

        // Charts
        healthChart: null,

        // Initialize component
        init() {
            this.loadSettings();
            this.initializeHealthChart();
            this.startRealTimeUpdates();
            this.watchForChanges();
        },

        // Load all settings
        async loadSettings() {
            this.loading = true;
            try {
                const response = await fetch('/admin/ai/integration/settings');
                const data = await response.json();
                
                this.aiProviders = data.ai_providers;
                this.moduleIntegrations = data.module_integrations;
                this.externalServices = data.external_services;
                this.webhooks = data.webhooks;
                this.systemStatus = data.system_status;
                this.performanceMetrics = data.performance_metrics;
                this.systemAlerts = data.system_alerts;
                
                this.hasUnsavedChanges = false;
            } catch (error) {
                console.error('Error loading settings:', error);
                this.showNotification('Error loading integration settings', 'error');
            } finally {
                this.loading = false;
            }
        },

        // Save all settings
        async saveAllSettings() {
            this.saving = true;
            try {
                const response = await fetch('/admin/ai/integration/settings', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        ai_providers: this.aiProviders,
                        module_integrations: this.moduleIntegrations,
                        external_services: this.externalServices,
                        webhooks: this.webhooks
                    })
                });

                const data = await response.json();
                if (data.success) {
                    this.showNotification('Integration settings saved successfully', 'success');
                    this.hasUnsavedChanges = false;
                } else {
                    this.showNotification(data.message, 'error');
                }
            } catch (error) {
                console.error('Error saving settings:', error);
                this.showNotification('Error saving integration settings', 'error');
            } finally {
                this.saving = false;
            }
        },

        // Refresh settings
        async refreshSettings() {
            await this.loadSettings();
            this.showNotification('Settings refreshed', 'success');
        },

        // Provider management
        async toggleProvider(provider) {
            this.hasUnsavedChanges = true;
            if (provider.enabled) {
                await this.testProvider(provider);
            }
        },

        async testProvider(provider) {
            provider.testing = true;
            try {
                const response = await fetch(`/admin/ai/integration/test-provider/${provider.id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();
                provider.status = data.success ? 'connected' : 'failed';
                
                this.showNotification(
                    data.success ? `${provider.name} connection successful` : `${provider.name} connection failed: ${data.message}`,
                    data.success ? 'success' : 'error'
                );
            } catch (error) {
                console.error('Error testing provider:', error);
                provider.status = 'failed';
                this.showNotification(`Error testing ${provider.name}`, 'error');
            } finally {
                provider.testing = false;
            }
        },

        async testAllConnections() {
            this.testing = true;
            const promises = this.aiProviders
                .filter(provider => provider.enabled)
                .map(provider => this.testProvider(provider));
                
            await Promise.all(promises);
            this.testing = false;
        },

        // Module integration management
        async toggleModuleIntegration(module) {
            this.hasUnsavedChanges = true;
            if (module.ai_enabled) {
                await this.testModuleIntegration(module);
            }
        },

        async updateModuleFeature(module, feature) {
            this.hasUnsavedChanges = true;
        },

        async testModuleIntegration(module) {
            try {
                const response = await fetch(`/admin/ai/integration/test-module/${module.id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();
                this.showNotification(
                    data.success ? `${module.name} integration test successful` : `${module.name} integration test failed`,
                    data.success ? 'success' : 'error'
                );
            } catch (error) {
                console.error('Error testing module integration:', error);
                this.showNotification(`Error testing ${module.name} integration`, 'error');
            }
        },

        // External service management
        async testService(service) {
            service.testing = true;
            try {
                const response = await fetch(`/admin/ai/integration/test-service/${service.id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();
                service.status = data.success ? 'connected' : 'failed';
                
                this.showNotification(
                    data.success ? `${service.name} connection successful` : `${service.name} connection failed`,
                    data.success ? 'success' : 'error'
                );
            } catch (error) {
                console.error('Error testing service:', error);
                service.status = 'failed';
                this.showNotification(`Error testing ${service.name}`, 'error');
            } finally {
                service.testing = false;
            }
        },

        // Webhook management
        createWebhook() {
            const newWebhook = {
                id: `webhook_${Date.now()}`,
                name: 'New Webhook',
                url: '',
                method: 'POST',
                secret: '',
                enabled: false,
                status: 'pending',
                available_events: [
                    { id: 'ai.request.completed', name: 'AI Request Completed', description: 'Fired when AI processing completes', enabled: false },
                    { id: 'ai.request.failed', name: 'AI Request Failed', description: 'Fired when AI processing fails', enabled: false },
                    { id: 'module.updated', name: 'Module Content Updated', description: 'Fired when module content is updated', enabled: false }
                ],
                deliveries_today: 0,
                success_rate: 100,
                failed_deliveries: 0,
                last_delivery: 'Never',
                testing: false
            };
            
            this.webhooks.push(newWebhook);
            this.hasUnsavedChanges = true;
        },

        async testWebhook(webhook) {
            webhook.testing = true;
            try {
                const response = await fetch(`/admin/ai/integration/test-webhook/${webhook.id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();
                webhook.status = data.success ? 'active' : 'failed';
                
                this.showNotification(
                    data.success ? 'Webhook test successful' : 'Webhook test failed',
                    data.success ? 'success' : 'error'
                );
            } catch (error) {
                console.error('Error testing webhook:', error);
                webhook.status = 'failed';
                this.showNotification('Error testing webhook', 'error');
            } finally {
                webhook.testing = false;
            }
        },

        deleteWebhook(webhook) {
            if (confirm('Are you sure you want to delete this webhook?')) {
                this.webhooks = this.webhooks.filter(w => w.id !== webhook.id);
                this.hasUnsavedChanges = true;
            }
        },

        // Health monitoring
        initializeHealthChart() {
            this.$nextTick(() => {
                const ctx = document.getElementById('healthChart');
                if (!ctx) return;

                this.healthChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['1h', '50m', '40m', '30m', '20m', '10m', 'Now'],
                        datasets: [{
                            label: 'AI Providers Health',
                            data: [95, 97, 92, 98, 96, 94, 99],
                            borderColor: '#4e73df',
                            backgroundColor: 'rgba(78, 115, 223, 0.1)',
                            tension: 0.4
                        }, {
                            label: 'Module Integrations Health',
                            data: [88, 91, 87, 93, 90, 89, 92],
                            borderColor: '#1cc88a',
                            backgroundColor: 'rgba(28, 200, 138, 0.1)',
                            tension: 0.4
                        }, {
                            label: 'External Services Health',
                            data: [78, 82, 79, 85, 83, 81, 84],
                            borderColor: '#36b9cc',
                            backgroundColor: 'rgba(54, 185, 204, 0.1)',
                            tension: 0.4
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                ticks: {
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                }
                            }
                        }
                    }
                });
            });
        },

        // Real-time updates
        startRealTimeUpdates() {
            setInterval(async () => {
                await this.updateSystemStatus();
                await this.updatePerformanceMetrics();
                await this.loadSystemAlerts();
            }, 30000); // Every 30 seconds
        },

        async updateSystemStatus() {
            try {
                const response = await fetch('/admin/ai/integration/system-status');
                const data = await response.json();
                this.systemStatus = data;
            } catch (error) {
                console.error('Error updating system status:', error);
            }
        },

        async updatePerformanceMetrics() {
            try {
                const response = await fetch('/admin/ai/integration/performance-metrics');
                const data = await response.json();
                this.performanceMetrics = data.metrics;
            } catch (error) {
                console.error('Error updating performance metrics:', error);
            }
        },

        async loadSystemAlerts() {
            try {
                const response = await fetch('/admin/ai/integration/system-alerts');
                const data = await response.json();
                this.systemAlerts = data.alerts;
            } catch (error) {
                console.error('Error loading system alerts:', error);
            }
        },

        // Change tracking
        watchForChanges() {
            // Watch for changes in data and mark as unsaved
            this.$watch('aiProviders', () => { this.hasUnsavedChanges = true; }, { deep: true });
            this.$watch('moduleIntegrations', () => { this.hasUnsavedChanges = true; }, { deep: true });
            this.$watch('externalServices', () => { this.hasUnsavedChanges = true; }, { deep: true });
            this.$watch('webhooks', () => { this.hasUnsavedChanges = true; }, { deep: true });
        },

        discardChanges() {
            if (confirm('Are you sure you want to discard all unsaved changes?')) {
                this.loadSettings();
            }
        },

        // Helper methods for styling
        getStatusBadge(status) {
            const badges = {
                'connected': 'badge-success',
                'active': 'badge-success',
                'disconnected': 'badge-warning',
                'failed': 'badge-danger',
                'pending': 'badge-secondary',
                'testing': 'badge-info'
            };
            return badges[status] || 'badge-secondary';
        },

        getModuleIcon(type) {
            const icons = {
                'content': 'fas fa-file-alt',
                'ecommerce': 'fas fa-shopping-cart',
                'blog': 'fas fa-blog',
                'portfolio': 'fas fa-briefcase',
                'contact': 'fas fa-envelope',
                'user': 'fas fa-user'
            };
            return icons[type] || 'fas fa-cube';
        },

        getServiceIcon(type) {
            const icons = {
                'storage': 'fas fa-cloud',
                'analytics': 'fas fa-chart-bar',
                'email': 'fas fa-envelope',
                'sms': 'fas fa-sms',
                'payment': 'fas fa-credit-card',
                'social': 'fas fa-share-alt'
            };
            return icons[type] || 'fas fa-cog';
        },

        getIntegrationIcon(type) {
            const icons = {
                'ai_provider': 'fas fa-brain',
                'module': 'fas fa-cube',
                'external_service': 'fas fa-link',
                'webhook': 'fas fa-satellite-dish'
            };
            return icons[type] || 'fas fa-plug';
        },

        getIntegrationIconBg(type) {
            const backgrounds = {
                'ai_provider': 'bg-primary',
                'module': 'bg-success',
                'external_service': 'bg-info',
                'webhook': 'bg-warning'
            };
            return backgrounds[type] || 'bg-secondary';
        },

        getAlertClass(severity) {
            const classes = {
                'critical': 'alert-danger',
                'warning': 'alert-warning',
                'info': 'alert-info',
                'success': 'alert-success'
            };
            return classes[severity] || 'alert-info';
        },

        getResponseTimeColor(time) {
            if (time <= 500) return 'bg-success';
            if (time <= 1000) return 'bg-warning';
            return 'bg-danger';
        },

        getResponseTimePercentage(time) {
            return Math.min((time / 2000) * 100, 100);
        },

        // Action handlers
        viewProviderLogs(provider) {
            // Implementation for viewing provider logs
            console.log('View logs for provider:', provider.name);
        },

        viewModuleSettings(module) {
            // Implementation for advanced module settings
            console.log('View advanced settings for module:', module.name);
        },

        configureService(service) {
            // Implementation for service configuration
            console.log('Configure service:', service.name);
        },

        viewWebhookLogs(webhook) {
            // Implementation for viewing webhook logs
            console.log('View logs for webhook:', webhook.name);
        },

        checkHealth(metric) {
            // Implementation for health check
            console.log('Check health for:', metric.name);
        },

        viewLogs(metric) {
            // Implementation for viewing logs
            console.log('View logs for:', metric.name);
        },

        dismissAlert(alertId) {
            this.systemAlerts = this.systemAlerts.filter(alert => alert.id !== alertId);
        },

        // Utility functions
        formatTime(timestamp) {
            if (!timestamp || timestamp === 'Never') return timestamp;
            return new Date(timestamp).toLocaleTimeString();
        },

        showNotification(message, type = 'info') {
            // Integration with notification system
            console.log(`${type.toUpperCase()}: ${message}`);
        }
    };
}
</script>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>