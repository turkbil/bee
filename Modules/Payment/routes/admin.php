<?php
// Modules/Payment/routes/admin.php
use Illuminate\Support\Facades\Route;
use Modules\Payment\App\Http\Livewire\Admin\PaymentMethodsComponent;
use Modules\Payment\App\Http\Livewire\Admin\PaymentMethodManageComponent;
use Modules\Payment\App\Http\Livewire\Admin\PaymentsComponent;
use Modules\Payment\App\Http\Livewire\Admin\PaymentDetailComponent;

// Admin rotaları - Payment Management
Route::middleware(['admin', 'tenant'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::prefix('payment')
            ->name('payment.')
            ->group(function () {
                // Payment Methods (PayTR, Stripe vb.)
                Route::prefix('methods')
                    ->name('methods.')
                    ->group(function () {
                        Route::get('/', PaymentMethodsComponent::class)
                            ->middleware('module.permission:payment,view')
                            ->name('index');

                        Route::get('/manage/{id?}', PaymentMethodManageComponent::class)
                            ->middleware('module.permission:payment,update')
                            ->name('manage');
                    });

                // Payments (Ödeme kayıtları)
                Route::get('/', PaymentsComponent::class)
                    ->middleware('module.permission:payment,view')
                    ->name('index');

                Route::get('/{id}', PaymentDetailComponent::class)
                    ->middleware('module.permission:payment,view')
                    ->name('detail');

                // Payment Detail AJAX (Modal için)
                Route::get('/{paymentId}/ajax-detail', function ($paymentId) {
                    try {
                        $payment = \Modules\Payment\App\Models\Payment::with(['payable'])->find($paymentId);

                        if (!$payment) {
                            return response()->json(['success' => false, 'message' => 'Ödeme bulunamadı']);
                        }

                        $html = view('payment::admin.partials.payment-detail', compact('payment'))->render();

                        return response()->json([
                            'success' => true,
                            'payment' => $payment,
                            'html' => $html
                        ]);
                    } catch (\Exception $e) {
                        return response()->json(['success' => false, 'message' => $e->getMessage()]);
                    }
                })->name('ajax-detail');

                // Fatura/Dekont Yükleme (AJAX)
                Route::post('/{paymentId}/upload-invoice', function (\Illuminate\Http\Request $request, $paymentId) {
                    try {
                        $payment = \Modules\Payment\App\Models\Payment::find($paymentId);

                        if (!$payment) {
                            return response()->json(['success' => false, 'message' => 'Ödeme bulunamadı'], 404);
                        }

                        $request->validate([
                            'invoice_file' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:10240', // 10MB
                        ], [
                            'invoice_file.required' => 'Dosya seçiniz',
                            'invoice_file.mimes' => 'Sadece PDF, JPG, PNG veya WebP dosyaları yüklenebilir',
                            'invoice_file.max' => 'Dosya boyutu en fazla 10MB olabilir',
                        ]);

                        // Eski fatura varsa sil
                        if ($payment->invoice_path) {
                            \Illuminate\Support\Facades\Storage::disk('public')->delete($payment->invoice_path);
                        }

                        // Yeni dosyayı kaydet
                        $path = $request->file('invoice_file')->store('invoices/' . date('Y/m'), 'public');

                        $payment->update([
                            'invoice_path' => $path,
                            'invoice_uploaded_at' => now(),
                            'notes' => ($payment->notes ? $payment->notes . "\n" : '') . '[Admin] Fatura yüklendi: ' . now()->format('d.m.Y H:i'),
                        ]);

                        return response()->json([
                            'success' => true,
                            'message' => 'Fatura başarıyla yüklendi',
                            'invoice_url' => asset('storage/' . $path),
                        ]);
                    } catch (\Illuminate\Validation\ValidationException $e) {
                        return response()->json(['success' => false, 'message' => $e->errors()['invoice_file'][0] ?? 'Doğrulama hatası'], 422);
                    } catch (\Exception $e) {
                        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
                    }
                })->middleware('module.permission:payment,update')->name('upload-invoice');

                // Fatura Silme (AJAX)
                Route::delete('/{paymentId}/delete-invoice', function ($paymentId) {
                    try {
                        $payment = \Modules\Payment\App\Models\Payment::find($paymentId);

                        if (!$payment) {
                            return response()->json(['success' => false, 'message' => 'Ödeme bulunamadı'], 404);
                        }

                        if (!$payment->invoice_path) {
                            return response()->json(['success' => false, 'message' => 'Silinecek fatura bulunamadı'], 404);
                        }

                        \Illuminate\Support\Facades\Storage::disk('public')->delete($payment->invoice_path);

                        $payment->update([
                            'invoice_path' => null,
                            'invoice_uploaded_at' => null,
                            'notes' => ($payment->notes ? $payment->notes . "\n" : '') . '[Admin] Fatura silindi: ' . now()->format('d.m.Y H:i'),
                        ]);

                        return response()->json([
                            'success' => true,
                            'message' => 'Fatura silindi'
                        ]);
                    } catch (\Exception $e) {
                        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
                    }
                })->middleware('module.permission:payment,update')->name('delete-invoice');
            });
    });
