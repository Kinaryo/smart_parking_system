<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QRParkir extends Model
{
    protected $table = 'qr_parkirs';

    protected $fillable = [
        'kode',
        'status',
        'aktif'
    ];


    public function transaksi()
    {
        return $this->hasMany(ParkirTransaksi::class, 'qr_parkir_id');
    }

public function qrParkir() {
    return $this->belongsTo(QRParkir::class, 'qr_parkir_id'); // Pastikan foreign key-nya benar
}
}
