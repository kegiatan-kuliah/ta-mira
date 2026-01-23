<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\LaporanAbsenQuery;

class AbsenController extends Controller
{
    public function print(Request $request)
    {
        $filter = $request->only([
            'from',
            'until',
            'kelas_id',
            'mata_pelajaran_id',
        ]);

        $guruId = auth()->user()?->role === 'guru'
            ? auth()->user()->guru?->id
            : null;

        $data = LaporanAbsenQuery::build($filter, $guruId)->get();

        $pdf = Pdf::loadView('pdf.laporan_absen', [
            'data' => $data,
            'from' => $filter['from'] ?? date('Y-m-d'),
            'until' => $filter['until'] ?? date('Y-m-d'),
        ])->setPaper('a4');

        return $pdf->stream("LAPORAN-ABSEN.pdf");
    }
}
