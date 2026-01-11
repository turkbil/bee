<?php

namespace Modules\Muzibu\App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\Muzibu\App\Services\DeviceService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DeviceController extends Controller
{
    public function __construct(
        protected DeviceService $deviceService
    ) {}

    /**
     * Check device limit and return active devices if exceeded
     */
    public function check(Request $request): JsonResponse
    {
        // Tenant 1001 kontrolü
        if (!tenant() || tenant()->id != 1001) {
            return response()->json(['limit_exceeded' => false]);
        }

        $user = $request->user();

        if (!$user) {
            return response()->json(['limit_exceeded' => false]);
        }

        $limitExceeded = !$this->deviceService->checkDeviceLimit($user);

        if ($limitExceeded) {
            return response()->json([
                'limit_exceeded' => true,
                'active_devices' => $this->deviceService->getActiveDevices($user),
                'device_limit' => $this->deviceService->getDeviceLimit($user),
            ]);
        }

        return response()->json(['limit_exceeded' => false]);
    }

    /**
     * Get active devices for authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'devices' => $this->deviceService->getActiveDevices($user),
            'device_limit' => $this->deviceService->getDeviceLimit($user),
        ]);
    }

    /**
     * Terminate a specific device session
     */
    public function destroy(Request $request, string $sessionId): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $success = $this->deviceService->terminateSession($sessionId, $user);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Cihaz başarıyla kapatıldı',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Cihaz kapatılamadı',
        ], 400);
    }

    /**
     * Terminate all device sessions except current
     */
    public function destroyAll(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $count = $this->deviceService->terminateOtherSessions($user);

        return response()->json([
            'success' => true,
            'message' => "{$count} cihazdan çıkış yapıldı",
            'devices_logged_out' => $count,
        ]);
    }
}
