<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // DEFAULT DATA
        DB::table('settings')->insert([
            [
                'key' => 'app_name',
                'value' => 'Smart Parking',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'lokasi_parkir',
                'value' => 'Bandar Udara Mopah Merauke',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'alamat',
                'value' => 'Jl. RE. Martadinata, Merauke, Papua Selatan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'kontak',
                'value' => '08123456789',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'latitude',
                'value' => '-8.5203',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'longitude',
                'value' => '140.4185',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};