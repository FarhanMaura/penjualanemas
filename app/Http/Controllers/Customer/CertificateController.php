<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\DigitalCertificate;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function index()
    {
        $certificates = DigitalCertificate::with(['transaction.items.product'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('customer.certificates.index', compact('certificates'));
    }

    public function show(DigitalCertificate $certificate)
    {
        abort_if($certificate->user_id !== auth()->id(), 403);

        $certificate->load(['transaction.items.product', 'user']);

        return view('customer.certificates.show', compact('certificate'));
    }
}
