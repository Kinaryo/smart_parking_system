<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gate;

class GateSeeder extends Seeder
{
    public function run(): void
    {
        Gate::create([
            'nama_gate' => 'Gate Masuk 1',
            'tipe_gate' => 'masuk',
        ]);

        Gate::create([
            'nama_gate' => 'Gate Keluar 1',
            'tipe_gate' => 'keluar',
        ]);
    }
}