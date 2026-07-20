<?php

namespace App\Services;

use App\Models\DigitalCertificate;
use App\Models\Transaction;

class CertificateService
{
    /**
     * Generate a digital certificate for a completed transaction.
     */
    public function generateForTransaction(Transaction $transaction): void
    {
        // Only generate for completed purchase or installment transactions
        if (!in_array($transaction->type, ['purchase', 'installment']) || $transaction->status !== 'completed') {
            return;
        }

        // Check if certificate already exists
        $exists = DigitalCertificate::where('transaction_id', $transaction->id)->exists();
        if ($exists) {
            return;
        }

        DigitalCertificate::create([
            'certificate_number' => 'CERT-' . date('Ymd') . '-' . sprintf('%04d', rand(1, 9999)),
            'transaction_id'     => $transaction->id,
            'user_id'            => $transaction->user_id,
            'issued_at'          => now(),
            'is_valid'           => true,
        ]);
    }
}
