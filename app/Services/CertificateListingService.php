<?php

namespace App\Services;

use App\Models\Certificate;
use Illuminate\Database\Eloquent\Collection;

class CertificateListingService
{
    /**
     * Certificates issued to the user (newest first).
     * Index on user_id keeps this query fast.
     */
    public function certificatesForUser(int $userId): Collection
    {
        return Certificate::query()
            ->where('user_id', $userId)
            ->with('course')
            ->orderByDesc('issued_at')
            ->get();
    }
}
