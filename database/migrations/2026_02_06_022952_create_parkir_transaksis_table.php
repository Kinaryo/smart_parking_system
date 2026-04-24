<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parkir_transaksis', function (Blueprint $table) {
            $table->id();


            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('kendaraan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('petugas_id')->nullable()->constrained('users');

            $table->foreignId('gate_masuk_id')->constrained('gates');
            $table->foreignId('gate_keluar_id')->nullable()->constrained('gates');
            $table->foreignId('qr_parkir_id')->nullable()->constrained('qr_parkirs');

            $table->timestamp('waktu_masuk')->useCurrent();
            $table->timestamp('waktu_keluar')->nullable();
            $table->integer('total_waktu')->nullable(); // Menit

            $table->integer('tarif_per_jam'); 
            $table->integer('total_bayar')->nullable();

            $table->string('jenis_kendaraan'); 
            $table->enum('status', ['aktif', 'selesai'])->default('aktif')->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parkir_transaksis');
    }
};