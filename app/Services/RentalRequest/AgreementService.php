<?php

namespace App\Services\RentalRequest;

use App\Models\RentalRequest\RentalRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class AgreementService
{
    public function ensureAgreementExists(RentalRequest $request): string
    {
        if ($request->agreement_path
            && Storage::disk('public')->exists($request->agreement_path)
        ) {
            return $request->agreement_path;
        }

        $request->loadMissing(['car', 'client']);

        $path = $this->generateAgreement($request);
        $request->agreement_path = $path;
        $request->save();

        return $path;
    }

    private function generateAgreement(RentalRequest $request): string
    {
        $pdf = Pdf::loadView('pdf.agreement', [
            'request' => $request,
            'car' => $request->car,
            'client' => $request->client,
        ]);

        $fileName = 'agreements/agreement_' . $request->id . '.pdf';

        Storage::disk('public')->put($fileName, $pdf->output());

        return $fileName;
    }
}
