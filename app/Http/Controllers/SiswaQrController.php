<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Siswa;

class SiswaQrController extends Controller
{
    public function cetak($id)
    {

        $siswa = Siswa::findOrFail($id);

        $pdf = Pdf::loadView('pdf.print_qr', [
            'siswa' => $siswa,
        ])->setPaper('a4');

        return $pdf->stream("QR-{$siswa->nis}.pdf");
    }
}
