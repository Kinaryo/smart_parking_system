<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    protected $fillable = [
        'user_id',
        'jenis',
        'plat_nomor',
        'merk',
        'warna'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isMotor()
    {
        return $this->jenis === 'motor';
    }

    public function isMobil()
    {
        return $this->jenis === 'mobil';
    }
}