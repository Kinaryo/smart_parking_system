<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tarif;

class TarifSeeder extends Seeder
{
    public function run(): void
    {
        Tarif::create([
            'nama' => 'motor',
            'tarif_per_jam' => 2000,
            'tarif_maksimal' => 10000
        ]);

        Tarif::create([
            'nama' => 'mobil',
            'tarif_per_jam' => 5000,
            'tarif_maksimal' => 25000
        ]);
    }
}