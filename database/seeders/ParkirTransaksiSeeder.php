<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ParkirTransaksi;
use App\Models\Kendaraan;
use App\Models\Tarif;
use App\Models\Gate;
use App\Models\User;
use Carbon\Carbon;

class ParkirTransaksiSeeder extends Seeder
{
    public function run(): void
    {
        $kendaraans = Kendaraan::all();
        $gateMasuk = Gate::where('tipe_gate', 'masuk')->first();
        $petugasList = User::where('role', 'petugas')->get();

        if ($kendaraans->isEmpty() || !$gateMasuk || $petugasList->isEmpty()) {
            $this->command->warn('Data kendaraan / gate / petugas belum lengkap!');
            return;
        }

        for ($i = 0; $i < 40; $i++) {
            $this->createTransaksi($kendaraans->random(), $petugasList->random(), $gateMasuk, 'selesai', true);
        }

        for ($i = 0; $i < 10; $i++) {
            $this->createTransaksi($kendaraans->random(), $petugasList->random(), $gateMasuk, 'selesai', false);
        }

        for ($i = 0; $i < 5; $i++) {
            $this->createTransaksi($kendaraans->random(), $petugasList->random(), $gateMasuk, 'aktif', false);
        }

        $this->command->info('Seeder transaksi berhasil dijalankan.');
    }

    private function createTransaksi($kendaraan, $petugas, $gate, $status, $isOldData)
    {
        $tarif = Tarif::where('nama', strtolower($kendaraan->jenis))->first();
        $tarifPerJam = $tarif->tarif_per_jam ?? ($kendaraan->jenis == 'mobil' ? 5000 : 2000);

        if ($isOldData) {
            $waktuMasuk = Carbon::now()->subDays(rand(1, 60))->subMinutes(rand(0, 1440));
        } else {
            $waktuMasuk = Carbon::now()->subMinutes(rand(10, 300));
        }

        if ($status === 'selesai') {
            $durasiMenit = rand(10, 300);
            $waktuKeluar = (clone $waktuMasuk)->addMinutes($durasiMenit);
            $jam = max(1, ceil($durasiMenit / 60));
            $totalBayar = $jam * $tarifPerJam;
        } else {
            $waktuKeluar = null;
            $durasiMenit = null;
            $totalBayar = 0;
        }

        ParkirTransaksi::create([
            'user_id'         => $kendaraan->user_id,
            'petugas_id'      => $petugas->id,
            'kendaraan_id'    => $kendaraan->id,
            'gate_masuk_id'   => $gate->id,
            'gate_keluar_id'  => $status === 'selesai' ? $gate->id : null,
            'waktu_masuk'     => $waktuMasuk,
            'waktu_keluar'    => $waktuKeluar,
            'total_waktu'     => $durasiMenit,
            'jenis_kendaraan' => $kendaraan->jenis,
            'tarif_per_jam'   => $tarifPerJam,
            'total_bayar'     => $totalBayar,
            'status'          => $status,
        ]);
    }
}