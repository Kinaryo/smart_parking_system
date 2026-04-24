<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ParkirTransaksi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminRiwayatTransaksiController extends Controller
{
    public function index()
    {
        return view('admin.riwayat-transaksi.index');
    }

    public function data(Request $request)
    {
       $query = ParkirTransaksi::with(['kendaraan', 'petugas'])
    ->orderByDesc('waktu_keluar');

        if ($request->status && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        if ($request->petugas_id) {
            $query->where('petugas_id', $request->petugas_id);
        }

        if ($request->search) {
            $query->whereHas('kendaraan', function ($q) use ($request) {
                $q->where('plat_nomor', 'like', '%' . $request->search . '%');
            });
        }

        $paginator = $query->paginate(10);

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
                'petugas' => $trx->petugas?->name ?? '-',
                'role' => $trx->petugas?->role ?? 'petugas',
            ];
        });

        $paginator->setCollection($dataTransform);

        return response()->json($paginator);
    }

    public function summary(Request $request)
    {
        $today = Carbon::today();

        $todayStart = $today->copy()->startOfDay();
        $todayEnd = $today->copy()->endOfDay();

        $startWeek = Carbon::now()->startOfWeek();
        $endWeek = Carbon::now()->endOfWeek();

        $startMonth = Carbon::now()->startOfMonth();
        $endMonth = Carbon::now()->endOfMonth();

        $baseQuery = ParkirTransaksi::query()->where('status', 'selesai');

        if ($request->petugas_id) {
            $baseQuery->where('petugas_id', $request->petugas_id);
        }

        $hariIni = (clone $baseQuery)
            ->whereBetween('waktu_keluar', [$todayStart, $todayEnd])
            ->sum('total_bayar');

        $mingguIni = (clone $baseQuery)
            ->whereBetween('waktu_keluar', [$startWeek, $endWeek])
            ->sum('total_bayar');

        $bulanIni = (clone $baseQuery)
            ->whereBetween('waktu_keluar', [$startMonth, $endMonth])
            ->sum('total_bayar');

        $totalHari = (clone $baseQuery)
            ->whereBetween('waktu_keluar', [$todayStart, $todayEnd])
            ->count();

        return response()->json([
            'hari_ini' => $hariIni,
            'minggu_ini' => $mingguIni,
            'bulan_ini' => $bulanIni,
            'total_hari' => $totalHari,
        ]);
    }
}
