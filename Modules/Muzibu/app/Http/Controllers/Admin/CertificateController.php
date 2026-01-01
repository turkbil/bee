<?php

namespace Modules\Muzibu\App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;

class CertificateController extends Controller
{
    /**
     * Sertifika listesi
     */
    public function index()
    {
        return view('muzibu::admin.certificate-index');
    }

    /**
     * Sertifika duzenleme
     */
    public function manage($id = null)
    {
        return view('muzibu::admin.certificate-manage', [
            'certificateId' => $id,
        ]);
    }
}
