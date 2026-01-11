<?php

declare(strict_types=1);

namespace Modules\AI\App\Http\Controllers\Admin\Bulk;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\AI\App\Services\V3\BulkOperationProcessor;
use Modules\AI\App\Models\AIBulkOperation;

/**
 * Bulk Operation Controller V3
 * 
 * Enterprise-level bulk processing with:
 * - UUID-based operation tracking
 * - Real-time progress monitoring
 * - Automatic failure recovery
 * - Queue-based background processing
 * - Comprehensive error logging
 */
class BulkOperationController extends Controller
{
    public function __construct(
        private readonly BulkOperationProcessor $bulkProcessor
    ) {}

    /**
     * Bulk Operations Ana Sayfa
     */
    public function index()
    {
        return view('ai::admin.universal.bulk-operations');
    }

    /**
     * Create new bulk operation
     */
    public function createBulkOperation(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'operation_type' => 'required|string|in:bulk_translate,bulk_seo,bulk_optimize,bulk_generate',
                'module_name' => 'required|string',
                'record_ids' => 'required|array|min:1|max:1000',
                'record_ids.*' => 'integer|min:1',
                'options' => 'sometimes|array',
                'options.target_language' => 'sometimes|string',
                'options.feature_id' => 'sometimes|integer',
                'options.template_id' => 'sometimes|integer',
                'options.priority' => 'sometimes|in:low,normal,high'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                    'message' => 'Validation failed'
                ], 422);
            }

            $operationType = $request->get('operation_type');
            $moduleName = $request->get('module_name');
            $recordIds = $request->get('record_ids', []);
            $options = $request->get('options', []);

            // Add user context to options
            $options['user_id'] = auth()->id();
            $options['ip_address'] = $request->ip();
            $options['created_at'] = now()->toISOString();

            // Create the bulk operation
            $operationId = $this->bulkProcessor->createOperation(
                $operationType,
                $recordIds,
                $options
            );

            // Get operation details for response
            $operation = AIBulkOperation::where('operation_uuid', $operationId)->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'operation_id' => $operationId,
                    'operation_type' => $operationType,
                    'module_name' => $moduleName,
                    'total_items' => count($recordIds),
                    'status' => 'pending',
                    'estimated_duration' => $this->estimateProcessingTime($operationType, count($recordIds)),
                    'created_at' => $operation->created_at->toISOString()
                ],
                'message' => 'Bulk operation created successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk operation creation failed', [
                'operation_type' => $request->get('operation_type'),
                'module_name' => $request->get('module_name'),
                'record_count' => count($request->get('record_ids', [])),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Could not create bulk operation',
                'message' => config('app.debug') ? $e->getMessage() : 'Operation creation failed'
            ], 500);
        }
    }

    /**
     * Get operation status and progress
     */
    public function getOperationStatus(string $operationId): JsonResponse
    {
        try {
            $operation = AIBulkOperation::where('operation_uuid', $operationId)
                ->first();

            if (!$operation) {
                return response()->json([
                    'success' => false,
                    'error' => 'Operation not found'
                ], 404);
            }

            // Check if user owns this operation
            if ($operation->created_by !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Access denied'
                ], 403);
            }

            // Get detailed status from processor
            $detailedStatus = $this->bulkProcessor->getOperationStatus($operationId);

            return response()->json([
                'success' => true,
                'data' => [
                    'operation_id' => $operationId,
                    'status' => $operation->status,
                    'progress' => $operation->progress,
                    'total_items' => $operation->total_items,
                    'processed_items' => $operation->processed_items,
                    'success_items' => $operation->success_items,
                    'failed_items' => $operation->failed_items,
                    'operation_type' => $operation->operation_type,
                    'module_name' => $operation->module_name,
                    'started_at' => $operation->started_at?->toISOString(),
                    'completed_at' => $operation->completed_at?->toISOString(),
                    'estimated_completion' => $this->estimateCompletion($operation),
                    'results' => $operation->results ?? [],
                    'errors' => $operation->error_log ?? [],
                    'detailed_status' => $detailedStatus
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Operation status retrieval failed', [
                'operation_id' => $operationId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Could not retrieve operation status'
            ], 500);
        }
    }

    /**
     * Cancel running operation
     */
    public function cancelOperation(string $operationId): JsonResponse
    {
        try {
            $operation = AIBulkOperation::where('operation_uuid', $operationId)
                ->first();

            if (!$operation) {
                return response()->json([
                    'success' => false,
                    'error' => 'Operation not found'
                ], 404);
            }

            // Check if user owns this operation
            if ($operation->created_by !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Access denied'
                ], 403);
            }

            // Check if operation can be cancelled
            if (in_array($operation->status, ['completed', 'failed'])) {
                return response()->json([
                    'success' => false,
                    'error' => 'Operation cannot be cancelled',
                    'message' => "Operation is already {$operation->status}"
                ], 400);
            }

            // Cancel the operation
            $cancelled = $this->bulkProcessor->cancelOperation($operationId);

            if ($cancelled) {
                return response()->json([
                    'success' => true,
                    'message' => 'Operation cancelled successfully',
                    'data' => [
                        'operation_id' => $operationId,
                        'cancelled_at' => now()->toISOString(),
                        'processed_before_cancel' => $operation->processed_items
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Could not cancel operation',
                    'message' => 'Operation may have already completed'
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Operation cancellation failed', [
                'operation_id' => $operationId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Could not cancel operation'
            ], 500);
        }
    }

    /**
     * Get operation history for user
     */
    public function getOperationHistory(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'limit' => 'sometimes|integer|min:1|max:100',
                'offset' => 'sometimes|integer|min:0',
                'status' => 'sometimes|string|in:pending,processing,completed,failed,partial',
                'operation_type' => 'sometimes|string',
                'module_name' => 'sometimes|string',
                'date_from' => 'sometimes|date',
                'date_to' => 'sometimes|date'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $limit = $request->get('limit', 20);
            $offset = $request->get('offset', 0);

            $query = AIBulkOperation::where('created_by', auth()->id())
                ->orderBy('created_at', 'desc');

            // Apply filters
            if ($request->has('status')) {
                $query->where('status', $request->get('status'));
            }

            if ($request->has('operation_type')) {
                $query->where('operation_type', $request->get('operation_type'));
            }

            if ($request->has('module_name')) {
                $query->where('module_name', $request->get('module_name'));
            }

            if ($request->has('date_from')) {
                $query->where('created_at', '>=', $request->get('date_from'));
            }

            if ($request->has('date_to')) {
                $query->where('created_at', '<=', $request->get('date_to'));
            }

            $total = $query->count();
            $operations = $query->skip($offset)->take($limit)->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'operations' => $operations->map(function ($operation) {
                        return [
                            'operation_id' => $operation->operation_uuid,
                            'operation_type' => $operation->operation_type,
                            'module_name' => $operation->module_name,
                            'status' => $operation->status,
                            'progress' => $operation->progress,
                            'total_items' => $operation->total_items,
                            'success_items' => $operation->success_items,
                            'failed_items' => $operation->failed_items,
                            'created_at' => $operation->created_at->toISOString(),
                            'completed_at' => $operation->completed_at?->toISOString(),
                            'duration' => $this->calculateDuration($operation)
                        ];
                    }),
                    'pagination' => [
                        'total' => $total,
                        'limit' => $limit,
                        'offset' => $offset,
                        'has_more' => ($offset + $limit) < $total
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Operation history retrieval failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Could not retrieve operation history'
            ], 500);
        }
    }

    /**
     * Retry failed items in operation
     */
    public function retryFailedItems(string $operationId): JsonResponse
    {
        try {
            $operation = AIBulkOperation::where('operation_uuid', $operationId)
                ->first();

            if (!$operation) {
                return response()->json([
                    'success' => false,
                    'error' => 'Operation not found'
                ], 404);
            }

            // Check if user owns this operation
            if ($operation->created_by !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Access denied'
                ], 403);
            }

            // Check if operation has failed items
            if ($operation->failed_items === 0) {
                return response()->json([
                    'success' => false,
                    'error' => 'No failed items to retry',
                    'message' => 'All items in this operation were processed successfully'
                ], 400);
            }

            // Extract failed record IDs from error log
            $failedRecordIds = $this->extractFailedRecordIds($operation);

            if (empty($failedRecordIds)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Could not identify failed items'
                ], 500);
            }

            // Create new operation for retry
            $retryOptions = $operation->options ?? [];
            $retryOptions['original_operation_id'] = $operationId;
            $retryOptions['retry_attempt'] = ($retryOptions['retry_attempt'] ?? 0) + 1;

            $retryOperationId = $this->bulkProcessor->createOperation(
                $operation->operation_type,
                $failedRecordIds,
                $retryOptions
            );

            return response()->json([
                'success' => true,
                'message' => 'Retry operation created successfully',
                'data' => [
                    'retry_operation_id' => $retryOperationId,
                    'original_operation_id' => $operationId,
                    'retry_items_count' => count($failedRecordIds),
                    'retry_attempt' => $retryOptions['retry_attempt']
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Operation retry failed', [
                'operation_id' => $operationId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Could not retry failed items'
            ], 500);
        }
    }

    /**
     * Estimate processing time based on operation type and count
     */
    private function estimateProcessingTime(string $operationType, int $itemCount): int
    {
        // Base time per item in seconds
        $timePerItem = match($operationType) {
            'bulk_translate' => 3,
            'bulk_seo' => 2,
            'bulk_optimize' => 4,
            'bulk_generate' => 5,
            default => 3
        };

        // Add overhead and scale factor
        $overhead = 10; // seconds
        $scaleFactor = max(1, $itemCount / 100); // Slower for large batches

        return (int) ceil(($itemCount * $timePerItem * $scaleFactor) + $overhead);
    }

    /**
     * Estimate completion time for ongoing operation
     */
    private function estimateCompletion(AIBulkOperation $operation): ?string
    {
        if (!$operation->started_at || $operation->status !== 'processing') {
            return null;
        }

        $elapsed = now()->diffInSeconds($operation->started_at);
        $progress = max($operation->progress, 1); // Avoid division by zero

        $estimatedTotal = ($elapsed * 100) / $progress;
        $remaining = max(0, $estimatedTotal - $elapsed);

        return now()->addSeconds($remaining)->toISOString();
    }

    /**
     * Calculate operation duration
     */
    private function calculateDuration(AIBulkOperation $operation): ?int
    {
        if (!$operation->started_at) {
            return null;
        }

        $endTime = $operation->completed_at ?? now();
        return $operation->started_at->diffInSeconds($endTime);
    }

    /**
     * Extract failed record IDs from operation error log
     */
    private function extractFailedRecordIds(AIBulkOperation $operation): array
    {
        $errorLog = $operation->error_log ?? [];
        $failedIds = [];

        foreach ($errorLog as $error) {
            if (isset($error['record_id'])) {
                $failedIds[] = $error['record_id'];
            }
        }

        return array_unique($failedIds);
    }
}