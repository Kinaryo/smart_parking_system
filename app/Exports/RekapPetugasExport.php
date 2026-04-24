<?php

namespace App\Exports;

use App\Models\User;
use App\Models\ParkirTransaksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class RekapPetugasExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    protected $start, $end;

    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function collection()
    {
        return User::whereIn('role', ['admin', 'petugas'])->get()->map(function ($user) {

            $query = ParkirTransaksi::where('petugas_id', $user->id)
                ->where('status', 'selesai');

            if ($this->start && $this->end) {
                $query->whereBetween('waktu_keluar', [$this->start, $this->end]);
            }

            $user->total_trx = $query->count();
            $user->total_pendapatan = $query->sum('total_bayar');

            return $user;
        });
    }

    public function headings(): array
    {
        return ['Nama', 'Role', 'Email', 'Jumlah Transaksi', 'Total Pendapatan'];
    }

    public function map($user): array
    {
        return [
            $user->name,
            ucfirst($user->role),
            $user->email,
            $user->total_trx . ' Kali',
            'Rp ' . number_format($user->total_pendapatan, 0, ',', '.')
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $periode = ($this->start && $this->end)
                    ? $this->start->format('d/m/Y') . ' s/d ' . $this->end->format('d/m/Y')
                    : 'Semua Waktu';

                $event->sheet->insertNewRowBefore(1, 4);
                $event->sheet->setCellValue('A1', 'LAPORAN REKAP PENDAPATAN PETUGAS');
                $event->sheet->setCellValue('A2', 'Periode: ' . $periode);

                $event->sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $event->sheet->getStyle('A2')->getFont()->setItalic(true);
            },
        ];
    }
}
