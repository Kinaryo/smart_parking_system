<?php

namespace App\Exports;

use App\Models\ParkirTransaksi;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class TransaksiPetugasExport implements FromQuery, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithEvents
{
    protected $id, $start, $end, $name;
    private $rowNumber = 0;

    public function __construct($id, $start, $end, $name)
    {
        $this->id = $id;
        $this->start = $start;
        $this->end = $end;
        $this->name = $name;
    }

    public function query()
    {
        $query = ParkirTransaksi::with('kendaraan')->where('petugas_id', $this->id);
        if ($this->start && $this->end) {
            $query->whereBetween('waktu_masuk', [$this->start, $this->end]);
        }
        return $query->latest('waktu_masuk');
    }

    public function headings(): array
    {
        return ['No', 'Plat Nomor', 'Jenis', 'Waktu Masuk', 'Waktu Keluar', 'Status', 'Total Bayar'];
    }

    public function map($row): array
    {
        return [
            ++$this->rowNumber,
            $row->kendaraan->plat_nomor ?? '-',
            $row->jenis_kendaraan,
            $row->waktu_masuk,
            $row->waktu_keluar ?? '-',
            ucfirst($row->status),
            'Rp ' . number_format($row->total_bayar, 0, ',', '.'),
        ];
    }

    public function title(): string 
    { 
        return substr(str_replace(['*', ':', '?', '[', ']', '/', '\\'], '', $this->name), 0, 31); 
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $periode = ($this->start && $this->end) 
                    ? $this->start->format('d/m/Y') . ' s/d ' . $this->end->format('d/m/Y') 
                    : 'Semua Waktu';

                $event->sheet->insertNewRowBefore(1, 4);
                $event->sheet->setCellValue('A1', 'DETAIL TRANSAKSI PETUGAS: ' . strtoupper($this->name));
                $event->sheet->setCellValue('A2', 'Periode: ' . $periode);
                $event->sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);
            },
        ];
    }
}