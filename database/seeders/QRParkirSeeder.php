<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QRParkir;
use Illuminate\Support\Str;

class QRParkirSeeder extends Seeder
{
    public function run(): void
    {
        if(QRParkir::count() == 0){

            QRParkir::create([
                'kode' => 'PKR-123456',
                'status' => 'tersedia',
                'aktif' => true
            ]);

        }
    }
}