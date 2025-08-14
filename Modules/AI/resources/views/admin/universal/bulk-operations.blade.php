@extends('admin.layout')

@section('title', 'AI Bulk Operations - Universal Input System V3')

@section('content')
<div class="container-fluid py-4" x-data="bulkOperationsManager()">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">AI Bulk Operations Manager</h1>
                    <p class="text-muted">Enterprise-level bulk processing with queue management and real-time monitoring</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" @click="refreshOperations()">
                        <i class="fas fa-sync-alt" :class="{'fa-spin': refreshing}"></i>
                        Refresh
                    </button>
                    <button class="btn btn-primary" @click="showNewOperationModal = true">
                        <i class="fas fa-plus"></i>
                        New Bulk Operation
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Active Operations</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" x-text="stats.active">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cogs fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Completed Today</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" x-text="stats.completed">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Queue Size</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" x-text="stats.queued">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Failed Operations</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" x-text="stats.failed">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Operation Filters</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Operation Type</label>
                        <select class="form-control" x-model="filters.type" @change="applyFilters()">
                            <option value="">All Types</option>
                            <option value="content_generation">Content Generation</option>
                            <option value="translation">Translation</option>
                            <option value="seo_optimization">SEO Optimization</option>
                            <option value="data_analysis">Data Analysis</option>
                            <option value="social_media">Social Media</option>
                            <option value="email_marketing">Email Marketing</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select class="form-control" x-model="filters.status" @change="applyFilters()">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="completed">Completed</option>
                            <option value="failed">Failed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Date Range</label>
                        <select class="form-control" x-model="filters.dateRange" @change="applyFilters()">
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="all">All Time</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Search Operations</label>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search by name or ID..." 
                                   x-model="filters.search" @input.debounce.500ms="applyFilters()">
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Operations Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Bulk Operations</h6>
            <div class="d-flex gap-2">
                <select class="form-control form-control-sm" style="width: auto;" x-model="selectedAction" :disabled="selectedOperations.length === 0">
                    <option value="">Bulk Actions</option>
                    <option value="cancel">Cancel Selected</option>
                    <option value="retry">Retry Selected</option>
                    <option value="delete">Delete Selected</option>
                    <option value="archive">Archive Selected</option>
                </select>
                <button class="btn btn-sm btn-outline-primary" @click="executeBulkAction()" 
                        :disabled="!selectedAction || selectedOperations.length === 0">
                    Execute
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="bulkOperationsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="40">
                                <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()">
                            </th>
                            <th>Operation ID</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Progress</th>
                            <th>Items</th>
                            <th>Started</th>
                            <th>Duration</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="operation in filteredOperations" :key="operation.id">
                            <tr>
                                <td>
                                    <input type="checkbox" :value="operation.id" x-model="selectedOperations">
                                </td>
                                <td class="font-monospace" x-text="operation.uuid.substring(0, 8)"></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3">
                                            <div class="icon-circle" :class="getOperationIcon(operation.type)">
                                                <i :class="getOperationIconClass(operation.type)"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-weight-bold" x-text="operation.name"></div>
                                            <div class="text-muted small" x-text="operation.description"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge" :class="getTypeColor(operation.type)" x-text="operation.type_label"></span>
                                </td>
                                <td>
                                    <span class="badge" :class="getStatusColor(operation.status)" x-text="operation.status_label"></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="w-100 mr-2">
                                            <div class="progress progress-sm">
                                                <div class="progress-bar" :class="getProgressColor(operation.status)" 
                                                     :style="`width: ${operation.progress_percentage}%`"></div>
                                            </div>
                                        </div>
                                        <div class="text-xs font-weight-bold" x-text="`${operation.progress_percentage}%`"></div>
                                    </div>
                                    <div class="text-xs text-muted mt-1" x-text="`${operation.processed_items}/${operation.total_items} items`"></div>
                                </td>
                                <td class="text-center">
                                    <div class="font-weight-bold" x-text="operation.total_items.toLocaleString()"></div>
                                    <div class="text-xs text-muted">items</div>
                                </td>
                                <td>
                                    <div x-text="formatDateTime(operation.created_at)"></div>
                                    <div class="text-xs text-muted" x-text="getTimeAgo(operation.created_at)"></div>
                                </td>
                                <td>
                                    <div x-text="formatDuration(operation.duration)"></div>
                                    <div class="text-xs text-muted" x-show="operation.status === 'processing'">
                                        <span x-text="getEstimatedCompletion(operation)"></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-info" @click="viewOperation(operation)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" @click="downloadResults(operation)" 
                                                :disabled="operation.status !== 'completed'">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-toggle="dropdown"></button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="#" @click="retryOperation(operation)" 
                                                   x-show="operation.status === 'failed'">
                                                    <i class="fas fa-redo mr-2"></i>Retry
                                                </a>
                                                <a class="dropdown-item" href="#" @click="cancelOperation(operation)" 
                                                   x-show="operation.status === 'processing' || operation.status === 'pending'">
                                                    <i class="fas fa-stop mr-2"></i>Cancel
                                                </a>
                                                <a class="dropdown-item" href="#" @click="cloneOperation(operation)">
                                                    <i class="fas fa-copy mr-2"></i>Clone
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item text-danger" href="#" @click="deleteOperation(operation)">
                                                    <i class="fas fa-trash mr-2"></i>Delete
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <!-- Empty State -->
                <div x-show="filteredOperations.length === 0" class="text-center py-5">
                    <i class="fas fa-tasks fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">No bulk operations found</h5>
                    <p class="text-gray-500">Start by creating a new bulk operation to process multiple items at once.</p>
                    <button class="btn btn-primary" @click="showNewOperationModal = true">
                        <i class="fas fa-plus"></i>
                        Create New Operation
                    </button>
                </div>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3" x-show="pagination.total_pages > 1">
                <div class="text-muted">
                    Showing <span x-text="pagination.from"></span> to <span x-text="pagination.to"></span> 
                    of <span x-text="pagination.total"></span> operations
                </div>
                <nav>
                    <ul class="pagination mb-0">
                        <li class="page-item" :class="{'disabled': pagination.current_page === 1}">
                            <a class="page-link" href="#" @click="changePage(pagination.current_page - 1)">Previous</a>
                        </li>
                        <template x-for="page in getPaginationPages()" :key="page">
                            <li class="page-item" :class="{'active': page === pagination.current_page}">
                                <a class="page-link" href="#" @click="changePage(page)" x-text="page"></a>
                            </li>
                        </template>
                        <li class="page-item" :class="{'disabled': pagination.current_page === pagination.total_pages}">
                            <a class="page-link" href="#" @click="changePage(pagination.current_page + 1)">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Real-time Queue Monitor -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Queue Performance</h6>
                </div>
                <div class="card-body">
                    <canvas id="queueChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Operation Types Distribution</h6>
                </div>
                <div class="card-body">
                    <canvas id="typeChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- System Health Monitor -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">System Health & Resources</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="mb-2">
                            <div class="progress progress-lg">
                                <div class="progress-bar bg-info" :style="`width: ${health.cpu_usage}%`"></div>
                            </div>
                        </div>
                        <div class="font-weight-bold">CPU Usage</div>
                        <div class="text-muted" x-text="`${health.cpu_usage}%`"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="mb-2">
                            <div class="progress progress-lg">
                                <div class="progress-bar bg-warning" :style="`width: ${health.memory_usage}%`"></div>
                            </div>
                        </div>
                        <div class="font-weight-bold">Memory Usage</div>
                        <div class="text-muted" x-text="`${health.memory_usage}%`"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="mb-2">
                            <div class="progress progress-lg">
                                <div class="progress-bar bg-success" :style="`width: ${health.queue_health}%`"></div>
                            </div>
                        </div>
                        <div class="font-weight-bold">Queue Health</div>
                        <div class="text-muted" x-text="`${health.queue_health}%`"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="mb-2">
                            <div class="progress progress-lg">
                                <div class="progress-bar bg-primary" :style="`width: ${health.worker_efficiency}%`"></div>
                            </div>
                        </div>
                        <div class="font-weight-bold">Worker Efficiency</div>
                        <div class="text-muted" x-text="`${health.worker_efficiency}%`"></div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="border-left-primary pl-3">
                            <div class="font-weight-bold">Active Workers</div>
                            <div class="h5 text-primary" x-text="health.active_workers"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border-left-success pl-3">
                            <div class="font-weight-bold">Jobs/Minute</div>
                            <div class="h5 text-success" x-text="health.jobs_per_minute"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border-left-warning pl-3">
                            <div class="font-weight-bold">Avg Processing Time</div>
                            <div class="h5 text-warning" x-text="health.avg_processing_time + 's'"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Operation Modal -->
    <div class="modal fade" :class="{'show d-block': showNewOperationModal}" tabindex="-1" x-show="showNewOperationModal" 
         style="background-color: rgba(0,0,0,0.5);" @click.self="showNewOperationModal = false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Bulk Operation</h5>
                    <button type="button" class="close" @click="showNewOperationModal = false">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form @submit.prevent="createNewOperation()">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Operation Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" x-model="newOperation.name" 
                                           placeholder="e.g., SEO Content Generation" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Operation Type <span class="text-danger">*</span></label>
                                    <select class="form-control" x-model="newOperation.type" required>
                                        <option value="">Select Type</option>
                                        <option value="content_generation">Content Generation</option>
                                        <option value="translation">Translation</option>
                                        <option value="seo_optimization">SEO Optimization</option>
                                        <option value="data_analysis">Data Analysis</option>
                                        <option value="social_media">Social Media</option>
                                        <option value="email_marketing">Email Marketing</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" x-model="newOperation.description" rows="3" 
                                      placeholder="Describe what this operation will do..."></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">AI Feature <span class="text-danger">*</span></label>
                                    <select class="form-control" x-model="newOperation.ai_feature_id" required>
                                        <option value="">Select Feature</option>
                                        <template x-for="feature in availableFeatures" :key="feature.id">
                                            <option :value="feature.id" x-text="feature.name"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Priority</label>
                                    <select class="form-control" x-model="newOperation.priority">
                                        <option value="low">Low</option>
                                        <option value="normal" selected>Normal</option>
                                        <option value="high">High</option>
                                        <option value="urgent">Urgent</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Data Input Section -->
                        <div class="form-group">
                            <label class="form-label">Input Data <span class="text-danger">*</span></label>
                            <div class="nav nav-tabs" role="tablist">
                                <a class="nav-item nav-link" :class="{'active': newOperation.inputMethod === 'text'}" 
                                   @click="newOperation.inputMethod = 'text'">Manual Input</a>
                                <a class="nav-item nav-link" :class="{'active': newOperation.inputMethod === 'file'}" 
                                   @click="newOperation.inputMethod = 'file'">File Upload</a>
                                <a class="nav-item nav-link" :class="{'active': newOperation.inputMethod === 'database'}" 
                                   @click="newOperation.inputMethod = 'database'">Database Query</a>
                            </div>
                            
                            <div class="tab-content mt-3">
                                <!-- Manual Input -->
                                <div x-show="newOperation.inputMethod === 'text'" class="tab-pane">
                                    <textarea class="form-control" x-model="newOperation.inputData" rows="6" 
                                              placeholder="Enter items separated by new lines..."></textarea>
                                    <small class="form-text text-muted">Each line will be processed as a separate item.</small>
                                </div>

                                <!-- File Upload -->
                                <div x-show="newOperation.inputMethod === 'file'" class="tab-pane">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" accept=".csv,.xlsx,.txt" 
                                               @change="handleFileUpload($event)">
                                        <label class="custom-file-label">Choose file (CSV, Excel, or TXT)</label>
                                    </div>
                                    <small class="form-text text-muted">Supported formats: CSV, Excel (.xlsx), Text files</small>
                                </div>

                                <!-- Database Query -->
                                <div x-show="newOperation.inputMethod === 'database'" class="tab-pane">
                                    <div class="form-group">
                                        <label>Source Module</label>
                                        <select class="form-control" x-model="newOperation.sourceModule">
                                            <option value="">Select Module</option>
                                            <option value="pages">Pages</option>
                                            <option value="portfolios">Portfolios</option>
                                            <option value="announcements">Announcements</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Filter Conditions</label>
                                        <textarea class="form-control" x-model="newOperation.queryConditions" rows="3" 
                                                  placeholder="e.g., status = 'active' AND created_at > '2024-01-01'"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Advanced Settings -->
                        <div class="card">
                            <div class="card-header py-2">
                                <h6 class="m-0 font-weight-bold text-secondary">Advanced Settings</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Batch Size</label>
                                            <input type="number" class="form-control" x-model="newOperation.batchSize" 
                                                   min="1" max="1000" value="50">
                                            <small class="form-text text-muted">Items to process per batch</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Delay Between Batches (seconds)</label>
                                            <input type="number" class="form-control" x-model="newOperation.delay" 
                                                   min="0" max="3600" value="0">
                                            <small class="form-text text-muted">Delay to prevent rate limiting</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Max Retries</label>
                                            <input type="number" class="form-control" x-model="newOperation.maxRetries" 
                                                   min="0" max="10" value="3">
                                            <small class="form-text text-muted">Retry failed items</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" x-model="newOperation.emailNotification">
                                        <label class="form-check-label">Email notification when completed</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" x-model="newOperation.saveResults">
                                        <label class="form-check-label">Save results to file</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" x-model="newOperation.pauseOnError">
                                        <label class="form-check-label">Pause operation on error</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="showNewOperationModal = false">Cancel</button>
                        <button type="submit" class="btn btn-primary" :disabled="loading">
                            <span x-show="loading">
                                <i class="fas fa-spinner fa-spin"></i>
                                Creating...
                            </span>
                            <span x-show="!loading">
                                <i class="fas fa-play"></i>
                                Start Operation
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Operation Details Modal -->
    <div class="modal fade" :class="{'show d-block': showDetailsModal}" tabindex="-1" x-show="showDetailsModal" 
         style="background-color: rgba(0,0,0,0.5);" @click.self="showDetailsModal = false">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Operation Details</h5>
                    <button type="button" class="close" @click="showDetailsModal = false">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" x-show="selectedOperation">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Operation Info -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold text-primary">Operation Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <dl class="row">
                                                <dt class="col-sm-4">Name:</dt>
                                                <dd class="col-sm-8" x-text="selectedOperation?.name"></dd>
                                                <dt class="col-sm-4">Type:</dt>
                                                <dd class="col-sm-8" x-text="selectedOperation?.type_label"></dd>
                                                <dt class="col-sm-4">Status:</dt>
                                                <dd class="col-sm-8">
                                                    <span class="badge" :class="getStatusColor(selectedOperation?.status)" 
                                                          x-text="selectedOperation?.status_label"></span>
                                                </dd>
                                                <dt class="col-sm-4">Priority:</dt>
                                                <dd class="col-sm-8" x-text="selectedOperation?.priority"></dd>
                                            </dl>
                                        </div>
                                        <div class="col-md-6">
                                            <dl class="row">
                                                <dt class="col-sm-4">Created:</dt>
                                                <dd class="col-sm-8" x-text="formatDateTime(selectedOperation?.created_at)"></dd>
                                                <dt class="col-sm-4">Started:</dt>
                                                <dd class="col-sm-8" x-text="formatDateTime(selectedOperation?.started_at)"></dd>
                                                <dt class="col-sm-4">Duration:</dt>
                                                <dd class="col-sm-8" x-text="formatDuration(selectedOperation?.duration)"></dd>
                                                <dt class="col-sm-4">Total Items:</dt>
                                                <dd class="col-sm-8" x-text="selectedOperation?.total_items.toLocaleString()"></dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Details -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold text-primary">Progress Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="progress mb-3" style="height: 20px;">
                                        <div class="progress-bar" :class="getProgressColor(selectedOperation?.status)" 
                                             :style="`width: ${selectedOperation?.progress_percentage}%`">
                                            <span x-text="`${selectedOperation?.progress_percentage}%`"></span>
                                        </div>
                                    </div>
                                    <div class="row text-center">
                                        <div class="col-md-3">
                                            <div class="border-left-success pl-3">
                                                <div class="font-weight-bold text-success" 
                                                     x-text="selectedOperation?.processed_items.toLocaleString()"></div>
                                                <div class="text-muted">Processed</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="border-left-warning pl-3">
                                                <div class="font-weight-bold text-warning" 
                                                     x-text="selectedOperation?.pending_items.toLocaleString()"></div>
                                                <div class="text-muted">Pending</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="border-left-danger pl-3">
                                                <div class="font-weight-bold text-danger" 
                                                     x-text="selectedOperation?.failed_items.toLocaleString()"></div>
                                                <div class="text-muted">Failed</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="border-left-info pl-3">
                                                <div class="font-weight-bold text-info" 
                                                     x-text="formatDuration(selectedOperation?.avg_processing_time)"></div>
                                                <div class="text-muted">Avg Time</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Processing Log -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold text-primary">Processing Log</h6>
                                </div>
                                <div class="card-body">
                                    <div class="log-container" style="height: 300px; overflow-y: auto; background: #f8f9fc; padding: 15px; border-radius: 5px; font-family: monospace; font-size: 12px;">
                                        <template x-for="log in selectedOperation?.logs" :key="log.id">
                                            <div class="log-entry" :class="`log-${log.level}`">
                                                <span class="text-muted" x-text="formatTime(log.created_at)"></span>
                                                <span class="badge badge-sm" :class="getLogLevelColor(log.level)" x-text="log.level.toUpperCase()"></span>
                                                <span x-text="log.message"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Quick Actions -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-success btn-block" 
                                                x-show="selectedOperation?.status === 'processing'"
                                                @click="pauseOperation(selectedOperation)">
                                            <i class="fas fa-pause"></i> Pause Operation
                                        </button>
                                        <button class="btn btn-primary btn-block" 
                                                x-show="selectedOperation?.status === 'paused'"
                                                @click="resumeOperation(selectedOperation)">
                                            <i class="fas fa-play"></i> Resume Operation
                                        </button>
                                        <button class="btn btn-warning btn-block" 
                                                x-show="selectedOperation?.status === 'failed'"
                                                @click="retryOperation(selectedOperation)">
                                            <i class="fas fa-redo"></i> Retry Failed Items
                                        </button>
                                        <button class="btn btn-info btn-block" 
                                                x-show="selectedOperation?.status === 'completed'"
                                                @click="downloadResults(selectedOperation)">
                                            <i class="fas fa-download"></i> Download Results
                                        </button>
                                        <button class="btn btn-secondary btn-block" @click="cloneOperation(selectedOperation)">
                                            <i class="fas fa-copy"></i> Clone Operation
                                        </button>
                                        <button class="btn btn-outline-danger btn-block" @click="deleteOperation(selectedOperation)">
                                            <i class="fas fa-trash"></i> Delete Operation
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Error Summary -->
                            <div class="card" x-show="selectedOperation?.failed_items > 0">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold text-danger">Error Summary</h6>
                                </div>
                                <div class="card-body">
                                    <template x-for="error in selectedOperation?.error_summary" :key="error.type">
                                        <div class="mb-2">
                                            <div class="d-flex justify-content-between">
                                                <span x-text="error.message"></span>
                                                <span class="badge badge-danger" x-text="error.count"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="showDetailsModal = false">Close</button>
                </div>
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
}

.icon-circle.bg-content { background-color: #6f42c1; }
.icon-circle.bg-translation { background-color: #20c997; }
.icon-circle.bg-seo { background-color: #fd7e14; }
.icon-circle.bg-analysis { background-color: #17a2b8; }
.icon-circle.bg-social { background-color: #e83e8c; }
.icon-circle.bg-email { background-color: #6c757d; }

.progress-lg {
    height: 20px;
}

.log-container .log-entry {
    margin-bottom: 5px;
    padding: 2px 0;
}

.log-info { color: #17a2b8; }
.log-warning { color: #ffc107; }
.log-error { color: #dc3545; }
.log-success { color: #28a745; }

.border-left-primary { border-left: 4px solid #4e73df !important; }
.border-left-success { border-left: 4px solid #1cc88a !important; }
.border-left-warning { border-left: 4px solid #f6c23e !important; }
.border-left-info { border-left: 4px solid #36b9cc !important; }
.border-left-danger { border-left: 4px solid #e74a3b !important; }

.card {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
}

.progress-sm {
    height: 0.5rem;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.processing {
    animation: pulse 2s infinite;
}
</style>

<script>
function bulkOperationsManager() {
    return {
        // Data properties
        operations: [],
        filteredOperations: [],
        selectedOperations: [],
        selectedOperation: null,
        availableFeatures: [],
        
        // UI state
        loading: false,
        refreshing: false,
        showNewOperationModal: false,
        showDetailsModal: false,
        selectAll: false,
        selectedAction: '',

        // Filters
        filters: {
            type: '',
            status: '',
            dateRange: 'today',
            search: ''
        },

        // New operation form
        newOperation: {
            name: '',
            type: '',
            description: '',
            ai_feature_id: '',
            priority: 'normal',
            inputMethod: 'text',
            inputData: '',
            sourceModule: '',
            queryConditions: '',
            batchSize: 50,
            delay: 0,
            maxRetries: 3,
            emailNotification: false,
            saveResults: true,
            pauseOnError: false
        },

        // Stats and health
        stats: {
            active: 0,
            completed: 0,
            queued: 0,
            failed: 0
        },

        health: {
            cpu_usage: 45,
            memory_usage: 62,
            queue_health: 89,
            worker_efficiency: 78,
            active_workers: 8,
            jobs_per_minute: 42,
            avg_processing_time: 2.3
        },

        // Pagination
        pagination: {
            current_page: 1,
            total_pages: 1,
            per_page: 15,
            total: 0,
            from: 0,
            to: 0
        },

        // Charts
        queueChart: null,
        typeChart: null,

        // Initialize component
        init() {
            this.loadOperations();
            this.loadAvailableFeatures();
            this.loadStats();
            this.initializeCharts();
            this.startRealTimeUpdates();
        },

        // Load operations data
        async loadOperations() {
            this.loading = true;
            try {
                const response = await fetch('/admin/ai/bulk-operations/list', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        filters: this.filters,
                        page: this.pagination.current_page,
                        per_page: this.pagination.per_page
                    })
                });

                const data = await response.json();
                this.operations = data.operations;
                this.filteredOperations = data.operations;
                this.pagination = data.pagination;
                this.updateStats();
            } catch (error) {
                console.error('Error loading operations:', error);
                this.showNotification('Error loading operations', 'error');
            } finally {
                this.loading = false;
            }
        },

        // Load available AI features
        async loadAvailableFeatures() {
            try {
                const response = await fetch('/admin/ai/features/list');
                const data = await response.json();
                this.availableFeatures = data.features;
            } catch (error) {
                console.error('Error loading features:', error);
            }
        },

        // Load statistics
        async loadStats() {
            try {
                const response = await fetch('/admin/ai/bulk-operations/stats');
                const data = await response.json();
                this.stats = data.stats;
                this.health = data.health;
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        },

        // Apply filters
        applyFilters() {
            this.pagination.current_page = 1;
            this.loadOperations();
        },

        // Refresh operations
        async refreshOperations() {
            this.refreshing = true;
            await this.loadOperations();
            await this.loadStats();
            this.updateCharts();
            this.refreshing = false;
        },

        // Toggle select all
        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedOperations = this.filteredOperations.map(op => op.id);
            } else {
                this.selectedOperations = [];
            }
        },

        // Execute bulk action
        async executeBulkAction() {
            if (!this.selectedAction || this.selectedOperations.length === 0) return;

            this.loading = true;
            try {
                const response = await fetch('/admin/ai/bulk-operations/bulk-action', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        action: this.selectedAction,
                        operation_ids: this.selectedOperations
                    })
                });

                const data = await response.json();
                if (data.success) {
                    this.showNotification(data.message, 'success');
                    this.selectedOperations = [];
                    this.selectedAction = '';
                    this.selectAll = false;
                    this.loadOperations();
                } else {
                    this.showNotification(data.message, 'error');
                }
            } catch (error) {
                console.error('Error executing bulk action:', error);
                this.showNotification('Error executing bulk action', 'error');
            } finally {
                this.loading = false;
            }
        },

        // Create new operation
        async createNewOperation() {
            this.loading = true;
            try {
                const response = await fetch('/admin/ai/bulk-operations/create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(this.newOperation)
                });

                const data = await response.json();
                if (data.success) {
                    this.showNotification('Bulk operation created successfully', 'success');
                    this.showNewOperationModal = false;
                    this.resetNewOperationForm();
                    this.loadOperations();
                } else {
                    this.showNotification(data.message, 'error');
                }
            } catch (error) {
                console.error('Error creating operation:', error);
                this.showNotification('Error creating operation', 'error');
            } finally {
                this.loading = false;
            }
        },

        // Reset new operation form
        resetNewOperationForm() {
            this.newOperation = {
                name: '',
                type: '',
                description: '',
                ai_feature_id: '',
                priority: 'normal',
                inputMethod: 'text',
                inputData: '',
                sourceModule: '',
                queryConditions: '',
                batchSize: 50,
                delay: 0,
                maxRetries: 3,
                emailNotification: false,
                saveResults: true,
                pauseOnError: false
            };
        },

        // View operation details
        async viewOperation(operation) {
            this.selectedOperation = operation;
            
            // Load detailed data
            try {
                const response = await fetch(`/admin/ai/bulk-operations/${operation.id}/details`);
                const data = await response.json();
                this.selectedOperation = { ...operation, ...data };
                this.showDetailsModal = true;
            } catch (error) {
                console.error('Error loading operation details:', error);
                this.showNotification('Error loading operation details', 'error');
            }
        },

        // Operation actions
        async retryOperation(operation) {
            await this.performOperationAction(operation.id, 'retry', 'Operation retry initiated');
        },

        async cancelOperation(operation) {
            if (confirm('Are you sure you want to cancel this operation?')) {
                await this.performOperationAction(operation.id, 'cancel', 'Operation cancelled');
            }
        },

        async pauseOperation(operation) {
            await this.performOperationAction(operation.id, 'pause', 'Operation paused');
        },

        async resumeOperation(operation) {
            await this.performOperationAction(operation.id, 'resume', 'Operation resumed');
        },

        async deleteOperation(operation) {
            if (confirm('Are you sure you want to delete this operation? This action cannot be undone.')) {
                await this.performOperationAction(operation.id, 'delete', 'Operation deleted');
                this.showDetailsModal = false;
            }
        },

        async cloneOperation(operation) {
            try {
                const response = await fetch(`/admin/ai/bulk-operations/${operation.id}/clone`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();
                if (data.success) {
                    this.showNotification('Operation cloned successfully', 'success');
                    this.loadOperations();
                } else {
                    this.showNotification(data.message, 'error');
                }
            } catch (error) {
                console.error('Error cloning operation:', error);
                this.showNotification('Error cloning operation', 'error');
            }
        },

        // Perform generic operation action
        async performOperationAction(operationId, action, successMessage) {
            this.loading = true;
            try {
                const response = await fetch(`/admin/ai/bulk-operations/${operationId}/${action}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();
                if (data.success) {
                    this.showNotification(successMessage, 'success');
                    this.loadOperations();
                } else {
                    this.showNotification(data.message, 'error');
                }
            } catch (error) {
                console.error(`Error ${action} operation:`, error);
                this.showNotification(`Error ${action} operation`, 'error');
            } finally {
                this.loading = false;
            }
        },

        // Download results
        async downloadResults(operation) {
            try {
                const response = await fetch(`/admin/ai/bulk-operations/${operation.id}/download`);
                if (response.ok) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `${operation.name}-results.xlsx`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                } else {
                    this.showNotification('Error downloading results', 'error');
                }
            } catch (error) {
                console.error('Error downloading results:', error);
                this.showNotification('Error downloading results', 'error');
            }
        },

        // File upload handler
        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                const formData = new FormData();
                formData.append('file', file);
                
                fetch('/admin/ai/bulk-operations/upload', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.newOperation.inputData = data.content;
                        this.showNotification('File uploaded successfully', 'success');
                    } else {
                        this.showNotification(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error uploading file:', error);
                    this.showNotification('Error uploading file', 'error');
                });
            }
        },

        // Pagination methods
        changePage(page) {
            if (page >= 1 && page <= this.pagination.total_pages) {
                this.pagination.current_page = page;
                this.loadOperations();
            }
        },

        getPaginationPages() {
            const pages = [];
            const current = this.pagination.current_page;
            const total = this.pagination.total_pages;
            
            let start = Math.max(1, current - 2);
            let end = Math.min(total, current + 2);
            
            for (let i = start; i <= end; i++) {
                pages.push(i);
            }
            
            return pages;
        },

        // Update statistics from operations
        updateStats() {
            this.stats = {
                active: this.operations.filter(op => op.status === 'processing').length,
                completed: this.operations.filter(op => op.status === 'completed').length,
                queued: this.operations.filter(op => op.status === 'pending').length,
                failed: this.operations.filter(op => op.status === 'failed').length
            };
        },

        // Initialize charts
        initializeCharts() {
            this.$nextTick(() => {
                this.initQueueChart();
                this.initTypeChart();
            });
        },

        // Initialize queue performance chart
        initQueueChart() {
            const ctx = document.getElementById('queueChart');
            if (!ctx) return;

            this.queueChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['1h ago', '45m ago', '30m ago', '15m ago', 'Now'],
                    datasets: [{
                        label: 'Queue Size',
                        data: [120, 85, 67, 34, 15],
                        borderColor: '#4e73df',
                        backgroundColor: 'rgba(78, 115, 223, 0.1)',
                        tension: 0.4
                    }, {
                        label: 'Processing Rate',
                        data: [25, 42, 38, 55, 48],
                        borderColor: '#1cc88a',
                        backgroundColor: 'rgba(28, 200, 138, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        },

        // Initialize type distribution chart
        initTypeChart() {
            const ctx = document.getElementById('typeChart');
            if (!ctx) return;

            this.typeChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Content Generation', 'Translation', 'SEO Optimization', 'Data Analysis', 'Social Media'],
                    datasets: [{
                        data: [35, 25, 20, 12, 8],
                        backgroundColor: [
                            '#6f42c1',
                            '#20c997',
                            '#fd7e14',
                            '#17a2b8',
                            '#e83e8c'
                        ]
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        },

        // Update charts with new data
        updateCharts() {
            if (this.queueChart) {
                // Update queue chart with real data
                this.queueChart.update();
            }
            
            if (this.typeChart) {
                // Update type chart with real data
                this.typeChart.update();
            }
        },

        // Start real-time updates
        startRealTimeUpdates() {
            setInterval(() => {
                this.refreshOperations();
            }, 30000); // Refresh every 30 seconds
        },

        // Helper methods for styling
        getOperationIcon(type) {
            const icons = {
                'content_generation': 'bg-content',
                'translation': 'bg-translation',
                'seo_optimization': 'bg-seo',
                'data_analysis': 'bg-analysis',
                'social_media': 'bg-social',
                'email_marketing': 'bg-email'
            };
            return icons[type] || 'bg-secondary';
        },

        getOperationIconClass(type) {
            const classes = {
                'content_generation': 'fas fa-pen-nib',
                'translation': 'fas fa-language',
                'seo_optimization': 'fas fa-search',
                'data_analysis': 'fas fa-chart-bar',
                'social_media': 'fas fa-share-alt',
                'email_marketing': 'fas fa-envelope'
            };
            return classes[type] || 'fas fa-cog';
        },

        getTypeColor(type) {
            const colors = {
                'content_generation': 'badge-primary',
                'translation': 'badge-info',
                'seo_optimization': 'badge-warning',
                'data_analysis': 'badge-success',
                'social_media': 'badge-danger',
                'email_marketing': 'badge-secondary'
            };
            return colors[type] || 'badge-light';
        },

        getStatusColor(status) {
            const colors = {
                'pending': 'badge-warning',
                'processing': 'badge-primary',
                'completed': 'badge-success',
                'failed': 'badge-danger',
                'cancelled': 'badge-secondary',
                'paused': 'badge-info'
            };
            return colors[status] || 'badge-light';
        },

        getProgressColor(status) {
            const colors = {
                'pending': 'bg-warning',
                'processing': 'bg-primary',
                'completed': 'bg-success',
                'failed': 'bg-danger',
                'cancelled': 'bg-secondary',
                'paused': 'bg-info'
            };
            return colors[status] || 'bg-light';
        },

        getLogLevelColor(level) {
            const colors = {
                'info': 'badge-info',
                'warning': 'badge-warning',
                'error': 'badge-danger',
                'success': 'badge-success'
            };
            return colors[level] || 'badge-secondary';
        },

        // Utility methods
        formatDateTime(datetime) {
            if (!datetime) return 'N/A';
            return new Date(datetime).toLocaleString();
        },

        formatTime(datetime) {
            if (!datetime) return '';
            return new Date(datetime).toLocaleTimeString();
        },

        formatDuration(seconds) {
            if (!seconds) return '0s';
            
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;
            
            if (hours > 0) {
                return `${hours}h ${minutes}m ${secs}s`;
            } else if (minutes > 0) {
                return `${minutes}m ${secs}s`;
            } else {
                return `${secs}s`;
            }
        },

        getTimeAgo(datetime) {
            if (!datetime) return '';
            
            const now = new Date();
            const past = new Date(datetime);
            const diffInSeconds = Math.floor((now - past) / 1000);
            
            if (diffInSeconds < 60) return 'just now';
            if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
            if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
            return `${Math.floor(diffInSeconds / 86400)}d ago`;
        },

        getEstimatedCompletion(operation) {
            if (!operation || operation.progress_percentage === 0) return 'Calculating...';
            
            const remainingItems = operation.total_items - operation.processed_items;
            const avgTime = operation.avg_processing_time || 0;
            const estimatedSeconds = remainingItems * avgTime;
            
            return `ETA: ${this.formatDuration(estimatedSeconds)}`;
        },

        // Show notification
        showNotification(message, type = 'info') {
            // This would integrate with your notification system
            console.log(`${type.toUpperCase()}: ${message}`);
        }
    };
}
</script>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>