<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gate extends Model
{
    protected $fillable = [
        'nama_gate',
        'tipe_gate',
    ];

    /**
     * =========================
     * RELASI
     * =========================
     */

    // Gate masuk → banyak transaksi parkir
    public function parkirMasuk()
    {
        return $this->hasMany(ParkirTransaksi::class, 'gate_masuk_id');
    }

    // Gate keluar → banyak transaksi parkir
    public function parkirKeluar()
    {
        return $this->hasMany(ParkirTransaksi::class, 'gate_keluar_id');
    }


    public function isMasuk()
    {
        return $this->tipe_gate === 'masuk';
    }

    public function isKeluar()
    {
        return $this->tipe_gate === 'keluar';
    }
}
