<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    public function makePngBase64(string $payload, int $size = 300): string
    {

        $png = QrCode::format('png')
            ->size($size)
            ->margin(1)
            ->errorCorrection('M')
            ->generate($payload);

        return 'data:image/png;base64,' . base64_encode($png);
    }
}
