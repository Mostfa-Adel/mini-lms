<?php

namespace App\Livewire;

use App\Services\CertificateListingService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MyCertificates extends Component
{
    public function render(CertificateListingService $certificateListing)
    {
        $certificates = $certificateListing->certificatesForUser(Auth::id());

        return view('my-certificates', [
            'certificates' => $certificates,
        ])->layout('layouts.app');
    }
}
