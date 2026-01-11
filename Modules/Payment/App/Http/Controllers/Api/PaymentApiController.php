<?php

namespace Modules\Payment\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Payment\App\Models\Payment;
use App\Traits\HasModuleAccessControl;
use Illuminate\Http\JsonResponse;

class PaymentApiController extends Controller
{
    use HasModuleAccessControl;

    public function __construct()
    {
        // 🔒 MODÜL ERİŞİM KONTROLÜ
        $this->checkModuleAccess('Payment');
    }

    /**
     * Tüm portfolyoları listele
     */
    public function index(Request $request): JsonResponse
    {
        $locale = $request->get('locale', app()->getLocale());

        $payments = Payment::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($payment) use ($locale) {
                return [
                    'id' => $payment->payment_id,
                    'title' => $payment->getTranslated('title', $locale),
                    'slug' => $payment->getTranslated('slug', $locale),
                    'body' => $payment->getTranslated('body', $locale),
                    'is_active' => $payment->is_active,
                    'created_at' => $payment->created_at,
                    'updated_at' => $payment->updated_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $payments,
            'meta' => [
                'total' => $payments->count(),
                'locale' => $locale
            ]
        ]);
    }

    /**
     * Belirli bir portfolyoyu slug ile getir
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $locale = $request->get('locale', app()->getLocale());

        $payment = Payment::where('is_active', true)
            ->whereJsonContains("slug->{$locale}", $slug)
            ->first();

        if (!$payment) {
            // Fallback: diğer dillerde ara
            $payment = Payment::where('is_active', true)
                ->where(function ($query) use ($slug) {
                    $query->whereJsonContains('slug->en', $slug)
                        ->orWhereJsonContains('slug->tr', $slug);
                })
                ->first();
        }

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => "Payment not found for slug: {$slug}",
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $payment->payment_id,
                'title' => $payment->getTranslated('title', $locale),
                'slug' => $payment->getTranslated('slug', $locale),
                'body' => $payment->getTranslated('body', $locale),
                'is_active' => $payment->is_active,
                'created_at' => $payment->created_at,
                'updated_at' => $payment->updated_at,
            ],
            'meta' => [
                'locale' => $locale
            ]
        ]);
    }
}
