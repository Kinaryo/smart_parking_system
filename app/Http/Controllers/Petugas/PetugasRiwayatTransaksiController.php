<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\ParkirTransaksi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PetugasRiwayatTransaksiController extends Controller
{
    public function index()
    {
        return view('petugas.riwayat-transaksi.index');
    }

    public function data(Request $request)
    {
        Log::info('Fetch data transaksi', [
            'user_id' => auth()->id(),
            'status' => $request->status,
            'search' => $request->search,
            'page' => $request->page
        ]);

        $query = ParkirTransaksi::with(['kendaraan', 'petugas'])
            ->where('petugas_id', auth()->id())
            ->orderByDesc('waktu_keluar');

        if ($request->status && $request->status != 'all') {
            Log::info('Filter status applied', ['status' => $request->status]);
            $query->where('status', $request->status);
        }

        if ($request->search) {
            Log::info('Search filter applied', ['search' => $request->search]);

            $query->whereHas('kendaraan', function ($q) use ($request) {
                $q->where('plat_nomor', 'like', '%' . $request->search . '%');
            });
        }

        $paginator = $query->paginate(10);

        Log::info('Total data fetched', [
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage()
        ]);

        $dataTransform = $paginator->getCollection()->map(function ($trx) {
            return [
                'id' => $trx->id,
                'plat_nomor' => $trx->kendaraan->plat_nomor ?? '-',
                'jenis_kendaraan' => $trx->jenis_kendaraan,
                'waktu_masuk' => $trx->waktu_masuk,
                'waktu_keluar' => $trx->waktu_keluar,
                'total_waktu' => $trx->total_waktu,
                'total_bayar' => $trx->total_bayar ?? 0,
                'status' => $trx->status,
                'petugas' => $trx->petugas->name ?? '-'
            ];
        });

        $paginator->setCollection($dataTransform);

        return response()->json($paginator);
    }

    public function summary()
    {
        $petugasId = auth()->id();

        Log::info('Fetch summary transaksi', [
            'user_id' => $petugasId
        ]);

        $today = Carbon::today();

        $todayStart = $today->copy()->startOfDay();
        $todayEnd = $today->copy()->endOfDay();

        $startWeek = Carbon::now()->startOfWeek();
        $endWeek = Carbon::now()->endOfWeek();

        $startMonth = Carbon::now()->startOfMonth();
        $endMonth = Carbon::now()->endOfMonth();

        Log::info('Range waktu', [
            'today_start' => $todayStart,
            'today_end' => $todayEnd,
            'week_start' => $startWeek,
            'week_end' => $endWeek,
            'month_start' => $startMonth,
            'month_end' => $endMonth,
        ]);

        $hariIni = ParkirTransaksi::where('petugas_id', $petugasId)
            ->where('status', 'selesai')
            ->whereBetween('waktu_keluar', [$todayStart, $todayEnd])
            ->sum('total_bayar');

        $mingguIni = ParkirTransaksi::where('petugas_id', $petugasId)
            ->where('status', 'selesai')
            ->whereBetween('waktu_keluar', [$startWeek, $endWeek])
            ->sum('total_bayar');

        $bulanIni = ParkirTransaksi::where('petugas_id', $petugasId)
            ->where('status', 'selesai')
            ->whereBetween('waktu_keluar', [$startMonth, $endMonth])
            ->sum('total_bayar');

        $totalHari = ParkirTransaksi::where('petugas_id', $petugasId)
            ->where('status', 'selesai')
            ->whereBetween('waktu_keluar', [$todayStart, $todayEnd])
            ->count();

        Log::info('Hasil summary', [
            'hari_ini' => $hariIni,
            'minggu_ini' => $mingguIni,
            'bulan_ini' => $bulanIni,
            'total_hari' => $totalHari
        ]);

        return response()->json([
            'hari_ini' => $hariIni,
            'minggu_ini' => $mingguIni,
            'bulan_ini' => $bulanIni,
            'total_hari' => $totalHari
        ]);
    }
}