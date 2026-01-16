<?php

namespace Modules\Muzibu\App\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Muzibu\App\Services\CertificateService;
use Modules\Muzibu\App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    protected CertificateService $certificateService;

    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

    /**
     * Show certificate form or existing certificate
     */
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Check if user already has a certificate
        $certificate = $this->certificateService->getUserCertificate($user);

        if ($certificate) {
            // Show existing certificate with download option
            return view('themes.muzibu.certificate.show', [
                'certificate' => $certificate,
            ]);
        }

        // Check eligibility
        $eligibility = $this->certificateService->canCreateCertificate($user);

        if (!$eligibility['can_create']) {
            return view('themes.muzibu.certificate.not-eligible', [
                'reason' => $eligibility['reason'],
            ]);
        }

        // Show form
        return view('themes.muzibu.certificate.form', [
            'firstPaidDate' => $eligibility['first_paid_date'],
        ]);
    }

    /**
     * Preview certificate before creating
     */
    public function preview(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'member_name' => 'required|string|max:255',
            'tax_office' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'confirmed' => 'required|accepted',
            'skip_correction' => 'nullable',
        ]);

        $user = auth()->user();
        $eligibility = $this->certificateService->canCreateCertificate($user);

        if (!$eligibility['can_create']) {
            return back()->withErrors(['error' => 'Belge oluşturmaya yetkiniz yok.']);
        }

        // Check if skip correction is enabled (for member_name only)
        $skipCorrection = $request->boolean('skip_correction');

        // Apply spelling correction for preview (skip for member_name if checked)
        $previewData = [
            'member_name' => $skipCorrection ? $validated['member_name'] : Certificate::correctSpelling($validated['member_name']),
            'tax_office' => !empty($validated['tax_office']) ? Certificate::correctSpelling($validated['tax_office']) : null,
            'tax_number' => $validated['tax_number'],
            'address' => !empty($validated['address']) ? Certificate::correctSpelling($validated['address']) : null,
            'membership_start' => $eligibility['first_paid_date'],
        ];

        // Store form data in session for "go back" functionality
        $formData = $validated;
        $formData['skip_correction'] = $skipCorrection;
        session(['certificate_form' => $formData]);

        return view('themes.muzibu.certificate.preview', [
            'previewData' => $previewData,
            'formData' => $formData,
        ]);
    }

    /**
     * Create certificate
     */
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'member_name' => 'required|string|max:255',
            'tax_office' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'skip_correction' => 'nullable',
        ]);

        $user = auth()->user();

        // Check if skip correction is enabled
        $skipCorrection = $request->boolean('skip_correction');
        $validated['skip_correction'] = $skipCorrection;

        try {
            $certificate = $this->certificateService->createCertificate($user, $validated);

            // Clear form session
            session()->forget('certificate_form');

            return redirect()
                ->route('muzibu.certificate.index')
                ->with('success', 'Belgeniz başarıyla oluşturuldu!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Download certificate as PDF
     */
    public function download()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $certificate = $this->certificateService->getUserCertificate($user);

        if (!$certificate) {
            return redirect()->route('muzibu.certificate.index')
                ->withErrors(['error' => 'Belge bulunamadı.']);
        }

        // Generate QR code
        $qrBase64 = qr($certificate->getVerificationUrl(), 150);

        // Generate PDF
        $pdf = Pdf::loadView('muzibu::certificate.pdf-template', [
            'certificate' => $certificate,
            'qrBase64' => $qrBase64,
        ]);

        // A4 Landscape
        $pdf->setPaper('a4', 'landscape');

        $filename = 'muzibu-belge-' . $certificate->certificate_code . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Verify certificate (public page)
     */
    public function verify(string $hash)
    {
        $result = $this->certificateService->verifyCertificate($hash);

        if (!$result['found']) {
            return view('themes.muzibu.certificate.not-found');
        }

        return view('themes.muzibu.certificate.verify', [
            'certificate' => $result['certificate'],
            'user' => $result['user'],
            'subscriptionPeriods' => $result['subscription_periods'],
            'isCurrentlyActive' => $result['is_currently_active'],
        ]);
    }
}
