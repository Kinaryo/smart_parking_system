<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AllPetugasDetailExport implements WithMultipleSheets
{
    protected $start, $end;

    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function sheets(): array
    {
        $sheets = [];

        $users = User::whereIn('role', ['admin', 'petugas'])->get();

        foreach ($users as $u) {
            $sheets[] = new TransaksiPetugasExport(
                $u->id,
                $this->start,
                $this->end,
                $u->name . ' (' . ucfirst($u->role) . ')'
            );
        }

        return $sheets;
    }
}