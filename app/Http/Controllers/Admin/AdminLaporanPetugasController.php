<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Exports\TransaksiPetugasExport;
use App\Exports\RekapPetugasExport;
use App\Exports\AllPetugasDetailExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminLaporanPetugasController extends Controller
{
    public function exportRekap(Request $request)
    {
        $start = $request->start ? Carbon::parse($request->start)->startOfDay() : null;
        $end = $request->end ? Carbon::parse($request->end)->endOfDay() : null;

        $fileName = 'Rekap_Pendapatan_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new RekapPetugasExport($start, $end), $fileName);
    }

    public function exportAllDetail(Request $request)
    {
        $start = $request->start ? Carbon::parse($request->start)->startOfDay() : null;
        $end = $request->end ? Carbon::parse($request->end)->endOfDay() : null;

        $fileName = 'Detail_Semua_Petugas_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new AllPetugasDetailExport($start, $end), $fileName);
    }
}